<template>
  <div class="flex page bg-grey">
    <div class="flex-1 flex-col justify-center items-center m-6 text-black h-full" style>
      <div
        class="flex-col bg-white w-full regular-border-radius px-10 py-6 messages-container"
        style="height:78vh;overflow:auto;"
        v-if="talkingTo"
        id="messages-container"
      >
        <div
          class="bg-white w-full flex border-b border-teal-light rounded-sm px-6 py-4 text-teal items-center justify-start mb-4"
        >
          <img
            class="float-left rounded-full border border-grey w-16"
            :class="(language === 'ar') ? 'ml-1' : 'mr-1'"
            :src="'/storage/images/' + talkingTo.image_path"
            alt
          />

          <div class="flex-1" :class="(language === 'ar') ? 'mr-3' : 'ml-3'">
            <p
              class="text-teal rubik-medium"
              v-if="conversationView === 'profiles'"
            >{{ talkingTo.name }}</p>

            <p
              class="text-teal rubik-medium"
              v-if="conversationView === 'events' && talkingTo.event"
            >{{ talkingTo.event.title }}</p>

            <p class="rubik-regular text-xs text-black" v-if="conversationView === 'profiles'">
              <span
                v-if="talkingTo.friendship_record"
              >{{ $t('components.profiles.friends_since') + ' ' + moment(talkingTo.friendship_record.created_at).format('MMMM-DD-YYYY') }}</span>
            </p>

            <p
              class="rubik-regular text-xs text-black capitalize"
              v-if="conversationView === 'events' && talkingTo.event"
            >{{ talkingTo.event.event_privacy }} event</p>
          </div>
        </div>

        <infinite-loading
          direction="top"
          @infinite="infiniteHandler"
          ref="infiniteLoading"
          v-if="conversationView === 'profiles'"
        >
          <span
            slot="no-more"
            class="raleway-bold text-grey-darker"
          >{{ $t('components.conferencing.end_of_conversation') }}</span>
        </infinite-loading>

        <div class="flex flex-col mb-4">
          <div class="flex mt-2 mx-auto text-grey-darker" v-if="conversationView === 'events'">
            <p class="raleway-italic">You joined a circle with</p>

            <div class="flex">
              <b-tooltip
                class="float-right cursor-pointer mx-1"
                position="is-top"
                animated
                v-for="(item, index) in talkingTo.members"
                :label="item.name"
                :key="index"
              >
                <a :href="'/profiles/' + item.username">
                  <img class="w-6" :src="'/storage/images/' + item.image_path" alt />
                </a>
              </b-tooltip>
            </div>
          </div>

          <div
            class="flex flex-col text-sm max-w-md m-1 justify-end"
            style="min-height:80px;"
            v-for="(item, index) in messages"
            :key="item.uid"
            v-bind:class="{'ml-auto': item.sender.id === profile.id, 'mr-auto': item.sender.id !== profile.id}"
          >
            <div class="flex">
              <div class="w-6 mt-5" :class="(language === 'ar') ? 'ml-2' : 'mr-2'">
                <img
                  class="rounded-full border border-grey w-16"
                  :src="'/storage/images/' + item.sender.image_path"
                  alt
                  v-if="item.sender.id !== profile.id"
                />
              </div>

              <div style="min-width:95px;">
                <p
                  class="mb-1 text-xs"
                  v-if="typeof item.created_at === 'string'"
                >{{ moment.tz(item.created_at, 'UTC').fromNow() }}</p>

                <div
                  class="text-white small-border-radius p-2"
                  v-bind:class="{'bg-teal-dark': item.sender.id === profile.id, 'bg-grey': item.sender.id !== profile.id,  'pr-4': language === 'ar', 'pl-4': language !== 'ar'}"
                >
                  <p
                    class="text-teal-dark"
                    v-if="item.sender.id !== profile.id"
                  >{{ item.sender.name }}</p>

                  <img
                    v-if="item.content_path && item.type !== 'audio'"
                    :src="'/storage/images/' + item.content_path"
                    alt
                  />

                  <audio-player
                    v-if="item.content_path && item.type === 'audio'"
                    :src="'/storage/audio/' + item.content_path"
                  />
                  <p
                    class="my-1"
                    v-bind:class="{'text-black': item.sender.id !== profile.id, 'pl-8': language === 'ar', 'pr-8': language !== 'ar'}"
                  >{{ item.body }}</p>

                  <p
                    class="mt-1 text-xs"
                    v-bind:class="{'text-black': item.sender.id !== profile.id, 'float-left': language === 'ar', 'float-right': language !== 'ar'}"
                    v-if="item.delivered && item.sender.id === profile.id"
                  >{{ $t('components.conferencing.delivered') }}</p>
                  <div class="clearfix"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="absolute pin-t mt-32">
        <transition name="fade">
          <audio-recorder
            v-if="openRecorder"
            class="absolute pin-t"
            upload-url="/profile/voice"
            :attempts="3"
            :time="2"
            :headers="headers"
            :before-recording="callback"
            :pause-recording="callback"
            :after-recording="callback"
            :select-record="selectRecord"
            :before-upload="uploadVoiceRecording"
            :successful-upload="uploadRecordingDone"
            :failed-upload="callback"
            v-on-clickaway="closeRecorder"
          />
        </transition>
      </div>

      <send-message
        class
        style="height:6vh;"
        v-if="activeConversation"
        v-on:chat:message-sent="scrollBottom"
        v-on:chat:open-recorder="openRecorderWidget"
        v-on:chat:close-recorder="closeRecorderWidget"
      ></send-message>

      <!-- <div
        class="relative mt-4 mb-6 outline-none flex justify-center items-center"
        style="height:7vh;"
        v-if="activeConversation"
      >
        <div
          class="flex w-12 mt-5 absolute pin-t cursor-pointer"
          :class="(language === 'ar') ? 'pin-r mr-5' : 'pin-l ml-5'"
        >
          <div class="flex justify-center items-center overflow-hidden">
            <input
              class="relative opacity-0 w-full relative z-20 cursor-pointer"
              type="file"
              id="uploadInput"
              @change="onFileChange"
              ref="fileInput"
            />
            <img class="absolute w-6 icon-grey cursor-pointer z-0" src="/svgs/plus.svg" alt />
          </div>
        </div>

        <div
          class="flex w-16 absolute pin-t mt-5 icon-grey"
          :class="(language === 'ar') ? 'pin-l pl-10' : 'pin-r pr-10'"
        >
          <img
            class="w-5 cursor-pointer"
            src="/svgs/microphone.svg"
            alt
            @click="openRecorder = true"
          />
        </div>

        <div
          id="imagePreview"
          class="absolute pin-t mt-2 w-12 h-12"
          :class="(language === 'ar') ? 'pin-l ml-24' : 'pin-r mr-24'"
        ></div>

        <input
          type="search"
          class="text-lg w-full bg-white rounded-full p-3 mt-1 py-4 text-black h-full"
          :class="(language === 'ar') ? 'pr-20' : 'pl-20'"
          :placeholder="$t('components.conferencing.send_message')"
          id="send-message"
          v-model="message"
          @keyup.enter="sendMessage"
        />
      </div>-->
    </div>

    <div class="main-sidebar flex flex-col bg-teal w-1/3 -mt-1 relative z-0 h-full">
      <div class="flex-col px-10 pt-6" style="height:80.5vh;">
        <div class="absolute pin flex items-center mt-6" style="height:75px;">
          <div
            class="flex bg-teal-light w-full px-1 py-3"
            :class="(language === 'ar') ? 'justify-end ml-12' : 'justify-start mr-12'"
          >
            <span
              class="flex items-center leading-normal whitespace-no-wrap text-grey-dark text-sm cursor-pointer"
              @click="toggleConversationView"
            >
              <img
                class="w-8"
                :class="(language === 'ar') ? 'ml-1' : 'mr-1'"
                src="/svgs/wine.svg"
                alt
              />
            </span>

            <div class="flex-1" :class="(language === 'ar') ? 'pl-3' : 'pr-3'">
              <input
                v-model="searchValue"
                class="appearance-none rounded py-3 px-4 text-white bg-teal text-sm w-full"
                type="text"
                :placeholder="$t('shared.search_by_friend')"
              />
            </div>

            <span
              class="flex items-center leading-normal whitespace-no-wrap text-grey-dark text-sm"
              @click="getFriendsOpen = true"
            >
              <img class="cursor-pointer" src="/svgs/notepad.svg" alt />
            </span>
          </div>
        </div>

        <div
          class="flex flex-col relative mt-20 z-0"
          :class="(language === 'ar') ? 'ml-2 pl-2' : 'mr-2 pr-2'"
          style="height:88%;overflow:auto;"
          v-if="conversationView == 'profiles'"
        >
          <div
            class="private-messages-container border flex bg-white opacity-75 w-full cursor-pointer flex rounded-sm p-4 text-black items-center mb-4"
            :class="[(language === 'ar') ? 'justify-end' : 'justify-start', (activeConversation && activeConversation.id === item.id) ? 'opacity-100' : 'opacity-50']"
            v-for="(item, index) in searchFilter"
            :key="index"
            @click="setActiveConversation(item, $event)"
          >
            <div class="flex w-full relative pointer-events-none">
              <div
                class="w-16 -mt-2"
                :class="(language === 'ar') ? 'float-right ml-1' : 'float-left mr-1'"
              >
                <img
                  class="rounded-full border border-grey"
                  :src="'/storage/images/' + item.talking_to.image_path"
                  alt
                />
              </div>

              <div
                class="flex flex-col flex-1 w-full"
                :class="(language === 'ar') ? 'mr-3' : 'ml-3'"
              >
                <div class="flex">
                  <div class="text-teal rubik-medium flex justify-between w-full">
                    <div class="w-full flex justify-between">
                      <p class>{{item.talking_to.name}}</p>

                      <p class="rubik-regular text-xs text-grey-darker">
                        <span
                          v-if="item.talking_to.friendship_record"
                        >{{ $t('components.profiles.friends_since') + ' ' + moment(item.talking_to.friendship_record.created_at).format('MMMM-DD-YYYY') }}</span>
                      </p>

                      <!-- <p>
                        updated at
                        {{ item.updated_at }}
                      </p>-->
                    </div>
                  </div>
                </div>

                <p
                  class="text-sm w-full mt-2"
                  style="min-width:370px;"
                  v-if="item.latest"
                >{{ item.latest.body }}</p>

                <div class="flex justify-between">
                  <p
                    class="float-right mt-1 text-xs text-grey-darker"
                    v-if="item.latest"
                  >{{ item.latest.delivered ? $t('components.conferencing.delivered') : null }}</p>
                  <div
                    class="rounded-full flex items-center justify-center text-sm text-white bg-teal-light w-6 h-6"
                    :class="(language === 'ar') ? 'mr-auto' : 'ml-auto'"
                    v-if="item.unread && item.unread.length !== 0"
                  >
                    <p>{{ item.unread.length }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div
          class="relative mt-20 z-0"
          :class="(language === 'ar') ? 'ml-2 pl-2' : 'mr-2 pr-2'"
          style="height:100%;overflow:auto;"
          v-else
        >
          <div
            class="private-messages-container border flex bg-white opacity-75 w-full cursor-pointer flex rounded-sm p-4 text-black items-center mb-4"
            :class="(language === 'ar') ? 'justify-end' : 'justify-start'"
            v-for="(item, index) in conversations"
            :key="index"
            @click="setActiveConversation(item, $event)"
          >
            <div class="flex w-full">
              <div
                class="w-16 -mt-2"
                :class="(language === 'ar') ? 'float-right ml-1' : 'float-left mr-1'"
              >
                <img
                  class="rounded-full border border-grey"
                  :src="'/storage/images/' + item.event.image_path"
                  alt
                />
              </div>

              <div
                class="flex flex-col flex-1 w-full"
                :class="(language === 'ar') ? 'mr-3' : 'ml-3'"
              >
                <div class="flex justify-between w-full">
                  <p class="text-teal rubik-medium">{{ item.event.title }}</p>
                </div>

                <p
                  class="text-sm w-full mt-2"
                  style="min-width:370px;"
                  v-if="item.latest"
                >{{ item.latest.body }}</p>

                <p
                  class="mt-1 text-xs text-grey-darker"
                  :class="(language === 'ar') ? 'float-left' : 'float-right'"
                >{{ $t('components.conferencing.delivered') }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <footer-component></footer-component>
    </div>

    <get-friends v-if="getFriendsOpen" v-on:modal:close="getFriendsOpen = false"></get-friends>
  </div>
</template>

<script>
import { mapState, mapMutations, mapActions } from "vuex";
import getFriends from "./../components/chats/getFriends";
import SendMessage from "./../components/chats/SendMessage";
import InfiniteLoading from "vue-infinite-loading";
import FileUpload from "vue-upload-component";
import { directive as onClickaway } from "vue-clickaway";

export default {
  props: ["recipient"],
  directives: {
    onClickaway: onClickaway
  },
  computed: {
    ...mapState({
      profile: state => state.profiles.signedInProfile,
      friends: state => state.profiles.friends,
      conversations: state => state.conversations.conversations,
      activeConversation: state => state.conversations.activeConversation,
      talkingTo: state => state.conversations.talkingTo,
      messages: state => state.conversations.messages,
      latestMessage: state => state.conversations.latestMessage,
      eventsConversations: state => state.conversations.eventsConversations,
      language: state => state.i18n.locale
    }),
    sortedConversations() {
      return _.orderBy(this.conversations, "updated_at", "desc");
    },
    searchFilter() {
      if (this.searchValue !== "") {
        return _.filter(this.conversations, element => {
          return element.talking_to["name"]
            .toLowerCase()
            .includes(this.searchValue.toLowerCase());
        });
      } else {
        return this.sortedConversations;
      }
    }
  },
  created() {
    this.$store.dispatch("conversations/getConversations", {
      view: this.conversationView
    });

    this.$store
      .dispatch("profiles/getFriends", this.profile)
      .then(response => {});
  },
  async mounted() {
    let that = this;
    if (this.recipient) {
      await axios
        .post(
          "/profile/" + this.recipient.username + "/chat/createConversation",
          this.recipient
        )
        .then(response => {
          that.setActiveConversation(response.data.conversation);
        });
    }
    // that.setActiveConversation(response.data.conversation);

    // if (this.profile) {
    // Echo.private(`conversations.${this.activeConversation.id}`).listen(
    //   "MessageSent",
    //   ({ event }) => {
    //     console.log("FDSgreGREGERGER");
    //     if (event.message.conversation_id === this.activeConversation.id) {
    //       this.$store
    //         .dispatch("conversations/addMessage", event.message)
    //         .then(response => {
    //           setTimeout(that.scrollBottom, 1);
    //         });
    //     }
    //   }
    // );
    // }

    if (this.profile) {
      Echo.private(`conversation.${this.profile.id}`).listen(
        "ConversationCreated",
        ({ event }) => {
          this.$store
            .dispatch("conversations/addConversation", event.conversation)
            .then(response => {
              this.getFriendsOpen = false;
            });
        }
      );

      Echo.private(`conversation.${this.profile.id}`).listen(
        "ConversationUpdated",
        ({ event }) => {
          if (event.conversation.id !== this.activeConversation.id) {
            this.$store
              .dispatch("conversations/addConversation", event.conversation)
              .then(response => {});
          }
        }
      );
    }
  },
  watch: {
    conversations(value) {
      let arry = [];

      _.forEach(this.conversations, e => {
        arry.push(e);
      });

      var mostRecentDate = new Date(
        Math.max.apply(
          null,
          arry.map(e => {
            return new Date(e.updated_at);
          })
        )
      );

      const mostRecentObject = arry.find(e => {
        const d = new Date(e.updated_at);
        return d.getTime() == mostRecentDate.getTime();
      });

      this.setActiveConversation(mostRecentObject);
    },
    messages(value) {
      if (this.$el.querySelector("#messages-container")) {
      }
    },
    activeConversation(value) {
      if (value) {
        this.dropzoneOptions.url =
          "/profile/" +
          this.profile.username +
          "/chat/" +
          this.activeConversation.id +
          "/send";
      }
    }
  },
  data() {
    return {
      searchValue: "",
      dropzoneOptions: {
        url: null,
        thumbnailWidth: 150,
        maxFilesize: 0.5,
        headers: {
          _token: document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content"),
          "content-type": "multipart/form-data"
        },
        data: {
          _token: document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content")
        }
      },
      recording: null,
      openRecorder: false,
      getFriendsOpen: false,
      message: null,
      page: 1,
      list: [],
      messageCount: 0,
      conversationView: "profiles",
      file: null,
      data: {
        _token: document
          .querySelector('meta[name="csrf-token"]')
          .getAttribute("content"),
        text: ""
      },
      headers: {
        "Content-Type": "multipart/form-data",
        _token: document
          .querySelector('meta[name="csrf-token"]')
          .getAttribute("content")
      }
    };
  },
  methods: {
    openRecorderWidget() {
      this.openRecorder = true;
    },
    closeRecorderWidget() {
      this.openRecorder = false;
    },
    uploadRecordingDone() {
      this.openRecorder = false;
    },
    uploadVoiceRecording(recording) {
      this.createImage(this.file, "audio");
    },
    selectRecord(msg) {
      var blob = new Blob([JSON.stringify([0, 1, 2])], {
        type: "application/json"
      });
      var fileOfBlob = new File([msg.blob], "recording");
      this.file = fileOfBlob;
      this.message = "recording";
    },
    closeRecorder() {
      this.openRecorder = false;
    },
    callback(msg) {
      // console.debug("Event: ", msg);
    },
    onFileChange(e) {
      let files = e.target.files || e.dataTransfer.files;
      if (!files.length) return;
      this.createImage(files[0]);
    },
    createImage(file, type = null) {
      let reader = new FileReader();
      let vm = this;
      reader.onload = e => {
        vm.file = e.target.result;
        this.message = file.name;

        this.sendMessage(type);
      };

      reader.readAsDataURL(file);
    },

    getMember(conversation) {
      conversation.members.forEach(element => {
        if (element.id != this.profile.id) {
          return element.name;
        }
      });
    },
    infiniteHandler($state) {
      this.messageCount = this.messageCount + 5;
      this.$store
        .dispatch("conversations/getMessages", {
          conversation: this.activeConversation,
          count: this.messageCount
        })
        .then(response => {
          if (this.messageCount > response) {
            $state.complete();
          }
          $state.loaded();
        });
    },
    scrollBottom() {
      setTimeout(() => {
        var container = this.$el.querySelector("#messages-container");
        container.scrollTop = container.scrollHeight;
        var input = this.$el.querySelector("#send-message");
        input.focus();
      }, 1);
    },
    setActiveConversation(conversation, event = null) {
      let that = this;
      // if (event) {
      //   $(".private-messages-container").removeClass("opacity-100");
      //   event.target.classList.toggle("opacity-100");
      // }

      this.$store
        .dispatch("conversations/setActiveConversation", {
          conversation: conversation,
          count: 5,
          type: this.conversationView
        })
        .then(response => {
          let that = this;
          this.message = null;
          this.messageCount = 0;

          this.$store.dispatch("conversations/markConversationAsRead");

          Echo.private(`conversations.${this.activeConversation.id}`).listen(
            "MessageSent",
            ({ event }) => {
              if (
                event.message.conversation_id === this.activeConversation.id
              ) {
                this.$store
                  .dispatch("conversations/addMessage", event.message)
                  .then(response => {
                    setTimeout(that.scrollBottom, 1);
                  });
              }
            }
          );
          this.$refs.infiniteLoading.$emit("$InfiniteLoading:reset");
        });
    },
    sendMessage(type = null) {
      let that = this;
      let tempMessage = this.message;

      setTimeout(this.scrollBottom, 1);

      if (this.message !== null) {
        this.message = null;

        this.$store
          .dispatch("conversations/sendMessage", {
            message: tempMessage,
            file: this.file,
            type: type,
            headers: {
              "Content-Type": "multipart/form-data",
              _token: document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content")
            }
          })
          .then(response => {
            this.openRecorder = false;
            (that.file = null),
              this.$store.dispatch("conversations/markConversationAsRead");
          });
      }
    },
    toggleConversationView() {
      if (this.conversationView === "profiles") {
        this.conversationView = "events";
      } else {
        this.conversationView = "profiles";
      }

      this.$store.dispatch("conversations/getConversations", {
        view: this.conversationView
      });
    }
  },
  components: {
    getFriends,
    InfiniteLoading,
    FileUpload,
    SendMessage
  }
};
</script>

<style lang="scss" scoped>
.messages-container {
}
.post-input {
  border-radius: 0 9999px 9999px 0;
}

.post-input-left-icon {
  border-radius: 9999px 0 0 9999px;
}

.post-horizontal-line {
  width: 97.5%;
}
</style>
