<template>
  <div
    class="flex flex-col w-1/4 border border-teal chat-component-inactive overflow-hidden rounded"
    style="height:75vh;"
    id="chat-component"
  >
    <div
      class="bg-teal-light flex justify-center cursor-pointer rounded py-5"
      style
      @click="toggleChatComponent"
    >
      <font-awesome-icon
        class="icon text-white cursor-pointer w-full hover:text-teal-darker"
        :icon="['fa', 'comment']"
        size="2x"
      />
    </div>

    <div
      class="flex-1 flex flex-col messages-container bg-white"
      style="height:73vh;overflow:auto;"
      id="messages-container"
    >
      <infinite-loading
        class="mt-4"
        direction="top"
        @infinite="infiniteHandler"
        ref="infiniteLoading"
        v-if="activeConversation"
      >
        <span
          slot="no-more"
          class="raleway-bold text-grey-darker"
        >{{ $t('components.search.end_of_conversation') }}</span>
      </infinite-loading>

      <div
        class="flex flex-col text-sm max-w-md m-1 justify-end"
        v-for="(item, index) in messages"
        :key="item.uid"
        v-bind:class="{'ml-auto': item.sender.id === profile.id, 'mr-auto': item.sender.id !== profile.id}"
      >
        <div class="flex">
          <div class="w-6 mt-5 mr-2">
            <img
              class="rounded-full border border-grey w-16"
              :src="'/storage/images/' + item.sender.image_path"
              alt
              v-if="item.sender.id !== profile.id"
            />
          </div>

          <div style="min-width:95px;">
            <p class="mb-1 text-xs" v-if="typeof item.created_at === 'string'">
              <span>{{ moment.tz(item.created_at, 'UTC').fromNow() }}</span>
            </p>

            <div
              class="text-white small-border-radius p-2 pl-4"
              v-bind:class="{'bg-teal-dark': item.sender.id === profile.id, 'bg-grey': item.sender.id !== profile.id}"
            >
              <p class="text-teal-dark" v-if="item.sender.id !== profile.id">{{ item.sender.name }}</p>

              <img
                v-if="item.content_path && item.type !== 'audio'"
                :src="'/storage/images/' + item.content_path"
                alt
              />

              <audio-player
                class
                style
                v-if="item.content_path && item.type === 'audio'"
                :src="'/storage/audio/' + item.content_path"
              />

              <p
                class="pr-8 my-1"
                v-bind:class="{'text-black': item.sender.id !== profile.id}"
              >{{ item.body }}</p>

              <p
                class="float-right mt-1 text-xs"
                v-bind:class="{'text-black': item.sender.id !== profile.id}"
                v-if="item.delivered && item.sender.id === profile.id"
              >Delivered</p>
              <div class="clearfix"></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="w-full bg-grey">
      <div class="flex items-center relative my-4 mx-2 outline-none bg-white rounded-full">
        <div
          class="flex w-12 pin-t cursor-pointer items-center"
          :class="(language === 'ar') ? 'pin-r mr-5' : 'pin-l ml-5'"
        >
          <div class="flex justify-center items-center overflow-hidden">
            <input
              class="relative opacity-0 w-full z-20 cursor-pointer"
              type="file"
              id="uploadInput"
              @change="onFileChange"
              ref="fileInput"
            />
            <font-awesome-icon
              class="z-0 absolute icon text-grey-darkest cursor-pointer w-full hover:text-grey-darkest"
              :icon="['fa', 'plus']"
              size="2x"
            />
          </div>
        </div>

        <div class="flex w-16 icon-grey absolute" :class="(language === 'ar') ? 'pin-l' : 'pin-r'">
          <font-awesome-icon
            class="icon text-grey-darkest cursor-pointer hover:text-grey-dark"
            :icon="['fa', 'microphone']"
            size="2x"
            @click="openRecorderView"
          />
        </div>

        <input
          type="search"
          class="text-lg w-full bg-transparent rounded-full p-3 py-4 text-grey-darkest h-full"
          :class="(language === 'ar') ? 'pr-0' : 'pl-0'"
          :placeholder="$t('shared.name_placeholder')"
          id="send-message"
          v-model="message"
          @keyup.enter="sendMessage"
        />
      </div>
    </div>
  </div>
</template>

<script>
import { mapState, mapMutations, mapActions } from "vuex";
import InfiniteLoading from "vue-infinite-loading";
import FileUpload from "vue-upload-component";
import { directive as onClickaway } from "vue-clickaway";
import SendMessage from "./../chats/SendMessage";

export default {
  props: ["recording"],
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
      bootstrapedActiveCircle: state =>
        state.conferencing.bootstrapedActiveCircle,
      language: state => state.i18n.locale
    })
  },
  created() {
    // this.$store.dispatch("conversations/getConversation", {
    //   event_id: this.bootstrapedActiveCircle.id,
    //   event_slug: this.bootstrapedActiveCircle.slug
    // });
  },
  mounted() {
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

      Echo.private(`conversations.${this.activeConversation.id}`).listen(
        "MessageSent",
        ({ event }) => {
          console.log(event);
          if (event.message.conversation_id === this.activeConversation.id) {
            this.$store
              .dispatch("conversations/addMessage", event.message)
              .then(response => {
                setTimeout(that.scrollBottom, 1);
              });
          }
        }
      );

      window.onload = function() {
        $(".private-messages-container").click(function() {
          $(".private-messages-container").removeClass("opacity-100");
          $(this).addClass("opacity-100");
        });
      };
    }

    if (this.activeConversation) {
    }
  },
  watch: {
    recording(value) {
      console.log(value);
    },
    messages(value) {
      if (this.$el.querySelector("#messages-container")) {
      }
    }
    // activeConversation(value) {
    //   let that = this;
    //   if (value) {
    //     Echo.private(`conversations.${this.activeConversation.id}`).listen(
    //       "MessageSent",
    //       ({ event }) => {
    //         console.log(event);
    //         if (event.message.conversation_id === this.activeConversation.id) {
    //           this.$store
    //             .dispatch("conversations/addMessage", event.message)
    //             .then(response => {
    //               setTimeout(that.scrollBottom, 1);
    //             });
    //         }
    //       }
    //     );
    //   }
    // }
  },
  data() {
    return {
      chatComponentOpen: false,
      openRecorder: false,
      getFriendsOpen: false,
      message: null,
      page: 1,
      list: [],
      messageCount: 0,
      conversationView: "events",
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
    toggleChatComponent() {
      if (this.chatComponentOpen) {
        $("#chat-component").addClass("chat-component-inactive");
        $("#chat-component").removeClass("chat-component-active");

        this.chatComponentOpen = false;
      } else {
        $("#chat-component").removeClass("chat-component-inactive");
        $("#chat-component").addClass("chat-component-active");

        this.chatComponentOpen = true;
      }
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
        console.log("reader");
        this.sendMessage(type);
      };

      reader.readAsDataURL(file);
    },
    openRecorderView() {
      this.$emit("open-recorder");
    },
    inputFilter(newFile, oldFile, prevent) {
      if (newFile && !oldFile) {
        // Before adding a file

        // Filter system files or hide files
        if (/(\/|^)(Thumbs\.db|desktop\.ini|\..+)$/.test(newFile.name)) {
          return prevent();
        }

        // Filter php html js file
        if (/\.(php5?|html?|jsx?)$/i.test(newFile.name)) {
          return prevent();
        }

        newFile.blob = "";
        let URL = window.URL || window.webkitURL;
        // console.log(newFile);
        if (URL && URL.createObjectURL) {
          newFile.blob = URL.createObjectURL(newFile.file);

          this.message = newFile.name;
          // add
          this.$store.dispatch("conversations/sendMessage", {
            file: newFile,
            message: null,
            type: "content"
          });
        }
        // console.log('add', newFile);
      }
    },
    inputFile(newFile, oldFile) {
      if (newFile && !oldFile) {
      }
      if (newFile && oldFile) {
        // update
        // console.log('update', newFile)
      }
      if (!newFile && oldFile) {
        // remove
        // console.log('remove', oldFile)
      }
    },
    getMember(conversation) {
      conversation.members.forEach(element => {
        if (element.id != this.profile.id) {
          //   console.log(element.name);
          return element.name;
        }
      });
    },
    infiniteHandler($state) {
      this.messageCount = this.messageCount + 5;

      //   console.log(this.activeConversation);
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
    setActiveConversation(event, conversation) {
      let that = this;

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

          this.$refs.infiniteLoading.$emit("$InfiniteLoading:reset");
        });
    },
    sendMessage(type = null) {
      let that = this;
      setTimeout(this.scrollBottom, 1);
      // console.log("FDSFSDFDS");

      if (this.message !== null) {
        this.$store
          .dispatch("conversations/sendMessage", {
            message: this.message,
            to: this.activeConversation,
            event_slug: this.bootstrapedActiveCircle.slug,
            type: type,
            file: this.file
          })
          .then(response => {
            that.message = null;
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
    InfiniteLoading,
    FileUpload,
    SendMessage
  }
};
</script>

<style lang="scss" scoped>
.chat-component-inactive {
  height: 65px !important;
  width: 120px;
  margin-bottom: 30px;
  cursor: pointer;
}
.chat-component-active {
  width: 460px;
}

.audio-player {
  .flex {
    width: 250px;
  }
}

.messages-container {
  //   scroll-behavior: smooth;
  // transition: all 1s ease-in-out;
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
