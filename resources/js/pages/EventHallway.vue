<template>
  <div class="container-fluid chat_container fill-current bg-teal text-black" id="app">
    <div class="row flex flex-col w-full h-full bg-transparent" v-if="signedInProfile">
      <div class="flex h-full">
        <div
          class="flex flex-col w-1/10 justify-between border border-r-0 border-t-0"
          style="background-color:rgba(0, 0, 0, 0.5);"
        >
          <div class="py-3 flex w-full justify-center h-24 border border-l-0 px-5">
            <img class="invert-icon" src="/svgs/logo.svg" alt />
          </div>

          <div
            class="w-full bg-transparent flex flex-col items-center flex-1 border border-t-0 border-l-0"
          >
            <button v-if="conferenceEntered">
              <p
                class="text-white w-full text-center py-3 cursor-pointer border-b px-2"
                style="font-size:1.5em;"
                @click="exitConference"
              >{{ $t('components.conferencing.hallway') }}</p>
            </button>

            <button v-else>
              <p
                class="text-white w-full text-center py-3 px-2 cursor-pointer border-b"
                @click="enterConference"
              >{{ $t('components.conferencing.enter_conference') }}</p>
            </button>
            <div
              class="flex flex-col bg-grey-darkest w-full h-full overflow-auto"
              style="min-height:400px;"
            ></div>
          </div>

          <a :href="'/events/' + event_slug">
            <div
              class="w-full hover:bg-white hover:text-black text-white py-3 flex justify-center cursor-pointer border border-t-0 border-b-0 border-l-0"
            >
              <p class style="font-size:1.5em;">{{ $t('components.conferencing.exit') }}</p>
            </div>
          </a>
        </div>

        <div class="flex flex-col flex-1 bg-transparent relative parent">
          <div class="flex items-center justify-end h-24 border border-l-0">
            <div
              class="flex justify-center items-center bg-grey-dark rounded-full cursor-pointer"
              style="width:50px;height:50px;"
              :class="(language === 'ar') ? 'ml-3' : 'mr-3'"
              @click="openHallwayModal('event-info')"
            >
              <font-awesome-icon
                class="icon text-teal-darker cursor-pointer hover:text-teal fa-2x"
                :icon="['fa', 'info']"
              />
            </div>
            <div
              class="flex justify-center items-center bg-grey-dark rounded-full cursor-pointer"
              style="width:50px;height:50px;"
              :class="(language === 'ar') ? 'ml-3' : 'mr-3'"
              @click="openHallwayModal('participants')"
            >
              <font-awesome-icon
                class="icon text-teal-darker cursor-pointer hover:text-teal fa-2x"
                :icon="['fa', 'users']"
              />
            </div>

            <div
              class="flex justify-center items-center bg-grey-dark rounded-full cursor-pointer"
              style="width:50px;height:50px;"
              :class="(language === 'ar') ? 'ml-3' : 'mr-3'"
            >
              <font-awesome-icon
                class="icon text-teal-darker cursor-pointer hover:text-teal fa-2x"
                :icon="['fa', 'question']"
              />
            </div>
          </div>

          <div class="absolute z-50" style="position: absolute;left: 35%;top: 30%;">
            <transition name="fade">
              <audio-recorder
                v-if="openRecorder"
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

          <div
            class="flex flex-1 justify-start items-start wrapper px-6 py-3 border border-l-0 border-t-0 h-full"
            id="hall-area"
            ref="hall-area"
            v-if="bootstrapedActiveCircle && !conferenceEntered"
          >
            <div
              class="flex justify-center items-center flex-col mx-5 cursor-pointer rounded mt-4 relative"
              v-for="(item, index) in presentMembers"
              :key="index"
            >
              <div
                v-if="!item.is_friend && !item.friend_request_sent && item.username !== profile.username"
                class="flex z-30 justify-center items-center bg-teal-dark rounded-full cursor-pointer absolute pin-t pin-r mr-1"
                style="width:30px;height:30px;"
                @click="openModal('send-friend-request', item)"
              >
                <font-awesome-icon
                  class="icon text-white cursor-pointer hover:text-teal-darker fa-md"
                  :icon="['fa', 'user-plus']"
                />
              </div>

              <div
                v-else-if="!item.is_friend && (item.friend_request_sent || item.has_friend_request_from ) && item.username !== profile.username"
                class="flex z-30 justify-center items-center bg-teal-dark rounded-full cursor-pointer absolute pin-t pin-r mr-1"
                style="width:30px;height:30px;"
                @click="openModal('cancel-friend-request', item)"
              >
                <font-awesome-icon
                  class="icon text-white cursor-pointer hover:text-teal-darker fa-md"
                  :icon="['fa', 'user-clock']"
                />
              </div>

              <popper
                trigger="clickToToggle"
                :options="{
                        placement: 'top',
                        modifiers: { offset: { offset: '350px,15px' } }
                      }"
              >
                <!-- <div class="popper"> -->
                <div
                  class="flex flex-col justify-start bg-grey py-2 px-4 rounded mr-32"
                  v-if="item.username !== profile.username"
                >
                  <div
                    class="flex mb-1 justify-start items-center cursor-pointer hover:text-teal"
                    @click="openReport(item, 'profiles')"
                  >
                    <font-awesome-icon
                      class="icon text-red cursor-pointer hover:text-teal-darker"
                      :class="(language === 'ar') ? 'ml-3' : 'mr-3'"
                      :icon="['fa', 'flag']"
                    />
                    <p class="text-sm tooltip-content">{{ $t('shared.report')}}</p>
                  </div>

                  <div
                    class="flex justify-start items-center cursor-pointer hover:text-teal"
                    @click="block"
                  >
                    <font-awesome-icon
                      class="icon text-red cursor-pointer hover:text-teal-darker fa-md"
                      :class="(language === 'ar') ? 'ml-3' : 'mr-3'"
                      :icon="['fa', 'ban']"
                    />
                    <p class="text-sm tooltip-content">{{ $t('components.profiles.block')}}</p>
                  </div>
                </div>

                <div v-else class></div>
                <!-- </div> -->

                <div slot="reference" class="relative">
                  <div
                    v-if="item.username !== profile.username"
                    class="flex justify-center items-center bg-grey rounded-full cursor-pointer absolute pin-r -mt-3 mr-8"
                    style="width:20px;height:20px;"
                    tooltip-target
                  >
                    <font-awesome-icon
                      class="icon text-grey-darkest cursor-pointer hover:text-teal fa-sm"
                      :icon="['fa', 'question']"
                    />
                  </div>

                  <div class="flex justify-center items-center flex-col" @click="profileClicked">
                    <div
                      class="flex items-center justify-center rounded-full h-20 w-20 bg-white overflow-hidden border border-white"
                    >
                      <img class="w-20" :src="'/storage/images/' + item.image_path" alt />
                    </div>

                    <p
                      class="profile-name flex justify-center flex-wrap w-32 text-center p-2 text-white text-sm -mt-2 bg-teal-lighter hover:text-black rounded-full"
                    >{{ item.name }}</p>
                  </div>
                </div>
              </popper>
            </div>
          </div>

          <twilio-video
            class="flex-1"
            :username="signedInProfile"
            v-on:conference:exit="exitConference()"
            v-else-if="bootstrapedActiveCircle && conferenceEntered"
          />

          <chat
            class="absolute pin-b mb-4 mx-4"
            :class="(language === 'ar') ? 'pin-l' : 'pin-r'"
            v-if="bootstrapedActiveCircle && activeConversation"
            :recording="file"
            v-on:open-recorder="openRecorderView()"
          ></chat>
        </div>
      </div>
    </div>

    <transition name="fade">
      <EventInfo
        v-if="activeHallwayModal === 'event-info'"
        v-on:modal:close="activeHallwayModal = null"
      ></EventInfo>
      <participants
        v-if="activeHallwayModal === 'participants'"
        v-on:modal:close="activeHallwayModal = null"
      ></participants>
    </transition>
  </div>
</template>

<script>
import Circles from "./../components/conferencing/Circles";
import TwilioVideo from "./../components/conferencing/TwilioVideo";
import Logs from "./../components/conferencing/Logs";
import EventInfo from "./../components/conferencing/EventInfo.vue";
import Participants from "./../components/conferencing/Participants.vue";
import Chat from "./../components/chats/ChatComponent";
import { mapState, mapMutations, mapActions } from "vuex";
import { directive as onClickaway } from "vue-clickaway";

export default {
  props: ["event_slug"],
  name: "event-hallway",
  directives: {
    onClickaway: onClickaway
  },
  computed: {
    ...mapState({
      activeModal: state => state.ui.activeModal,
      signedInProfile: state => state.profiles.signedInProfile,
      activeCircle: state => state.conferencing.activeCircle,
      bootstrapedActiveCircle: state =>
        state.conferencing.bootstrapedActiveCircle,
      profile: state => state.profiles.signedInProfile,
      presentMembers: state => state.conferencing.presentMembers,
      activeConversation: state => state.conversations.activeConversation,
      language: state => state.i18n.locale,
      inHallway: state => state.conferencing.inHallway
    }),
    channel() {
      return Echo.join("conferences." + this.bootstrapedActiveCircle.id);
    }
  },
  async created() {
    let that = this;

    this.$store
      .dispatch("conferencing/setInHallwayArray", this.signedInProfile)
      .then(response => {});

    await this.$store
      .dispatch("conferencing/getCircle", {
        slug: this.event_slug
      })
      .then(response => {});

    this.$store.dispatch("conversations/getConversation", {
      event_id: this.bootstrapedActiveCircle.id,
      event_slug: this.bootstrapedActiveCircle.slug
    });

    this.channel
      .here(users => {
        this.$store.dispatch("conferencing/SetPresentMembers", users);
      })
      .joining(user => {
        this.$store.dispatch("conferencing/AddMember", user);
      })
      .leaving(user => {
        this.$store.dispatch("conferencing/RemoveDisconnectedMember", user);
      });
  },
  mounted() {
    let that = this;

    window.onbeforeunload = async function() {
      await axios.post("/hallway/" + that.event_slug + "/leave");
    };
  },
  watch: {
    hallway(value) {
      if (value) {
        axios.post("/hallway/" + this.event_slug + "/updateHallway", {
          hallway: this.hallway,
          slug: this.event_slug
        });
      }
    },
    bootstrapedActiveCircle(value) {
      let that = this;

      if (value) {
        this.$store.dispatch("conferencing/enterConference", {
          slug: this.event_slug
        });

        value.participants.forEach(function(participant) {
          if (participant.username !== that.signedInProfile.username) {
            that.$store
              .dispatch("conferencing/setInHallwayArray", participant)
              .then(response => {});
          }
        });

        // Echo.private(`conferences.${this.bootstrapedActiveCircle.id}`).listen(
        //   "ParticipantConnected",
        //   ({ event }) => {
        //     console.log(event.member);
        //     this.$store
        //       .dispatch("conferencing/setInHallwayArray", event.member)
        //       .then(response => {});

        //     this.$store.dispatch("conferencing/SetPresentMembers", {
        //       username: event.member.username
        //     });

        //     this.$toast.open({
        //       duration: 5000,
        //       message: event.member.name + " connected.",
        //       position: "is-bottom",
        //       type: "is-success"
        //     });
        //   }
        // );

        // Echo.private(`conferences.${this.bootstrapedActiveCircle.id}`).listen(
        //   "ParticipantDisconnected",
        //   ({ event }) => {
        //     that.$store.dispatch("conferencing/RemoveDisconnectedMember", {
        //       username: event.member.username
        //     });

        //     this.$toast.open({
        //       duration: 5000,
        //       message: event.member.name + " disconnected.",
        //       position: "is-bottom",
        //       type: "is-danger"
        //     });
        //   }
        // );
      }
    }
  },
  beforeDestroy() {
    this.$store.dispatch("conferencing/leaveConference", {
      slug: this.event_slug
    });
  },
  destroyed() {
    this.$store.dispatch("conferencing/leaveConference", {
      slug: this.event_slug
    });
  },
  data() {
    return {
      inHall: [],
      modalOpen: false,
      modalName: "",
      activeHallwayModal: null,
      drag: false,
      recording: null,
      openRecorder: false,
      username: "",
      authenticated: false,
      conferenceEntered: false,
      loading: false,
      file: null,
      message: null,
      hallway: [],
      headers: {
        "Content-Type": "multipart/form-data",
        _token: document
          .querySelector('meta[name="csrf-token"]')
          .getAttribute("content")
      }
    };
  },
  methods: {
    profileClicked() {},
    openReport(resource, resourceType) {
      this.$store.dispatch("reports/setReportedResourceType", resourceType);

      this.$store.dispatch("reports/setReportedResource", resource);

      this.$store.dispatch("ui/setReportView", true);
    },
    block() {
      this.$store.dispatch("profiles/setTargetProfile", this.profile);

      this.$store.dispatch("ui/setActiveModal", "Block");
    },
    unblock() {
      this.$store.dispatch("profiles/setTargetProfile", this.profile);

      this.$store.dispatch("ui/setActiveModal", "Unblock");
    },
    openRecorderView() {
      this.openRecorder = true;
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
      console.debug("Event: ", msg);
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
    openHallwayModal(componentName) {
      this.activeHallwayModal = componentName;
    },
    openModal(componentName, profile) {
      this.$store.dispatch("profiles/setTargetProfile", profile);

      this.$store.dispatch("ui/setActiveModal", componentName);
    },
    leaveComponent() {
      this.$store.dispatch("conferencing/leaveConference", {
        slug: this.event_slug
      });

      axios.post("/hallway/" + this.event_slug + "/leave");
    },
    exitConference() {
      this.conferenceEntered = false;
      this.activeCircle.disconnect();
    },
    enterConference() {
      this.conferenceEntered = true;
      let that = this;
    },
    submitUsername(username) {
      if (!username) {
        return alert("please provide a username");
      }

      this.authenticated = true;
    },
    getTracks(participant) {
      return Array.from(participant.tracks.values())
        .filter(function(publication) {
          return publication.track;
        })
        .map(function(publication) {
          return publication.track;
        });
    },
    sendMessage(type = null) {
      let that = this;

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
    onMove() {}
  },
  components: {
    Circles,
    Logs,
    TwilioVideo,
    Chat,
    EventInfo,
    Participants
  }
};
</script>

<style scoped lang="scss">
.chat_container {
  height: 100vh;
}
@keyframes fadeIn {
  to {
    opacity: 1;
  }
}
.ex-moved {
  animation: fadeIn 2s ease-in 1 forwards;
  border: 2px solid yellow;
  padding: 2px;
}
.ex-over {
  animation: fadeIn 0.5s ease-in 1 forwards;
  border: 4px solid green;
  padding: 2px;
}

.tooltip {
  display: block !important;
  z-index: 10000;

  .tooltip-inner {
    background: black;
    color: white;
    border-radius: 16px;
    padding: 5px 10px 4px;
  }

  .tooltip-arrow {
    width: 0;
    height: 0;
    border-style: solid;
    position: absolute;
    margin: 5px;
    border-color: black;
    z-index: 1;
  }

  &[x-placement^="top"] {
    margin-bottom: 5px;

    .tooltip-arrow {
      border-width: 5px 5px 0 5px;
      border-left-color: transparent !important;
      border-right-color: transparent !important;
      border-bottom-color: transparent !important;
      bottom: -5px;
      left: calc(50% - 5px);
      margin-top: 0;
      margin-bottom: 0;
    }
  }

  &[x-placement^="bottom"] {
    margin-top: 5px;

    .tooltip-arrow {
      border-width: 0 5px 5px 5px;
      border-left-color: transparent !important;
      border-right-color: transparent !important;
      border-top-color: transparent !important;
      top: -5px;
      left: calc(50% - 5px);
      margin-top: 0;
      margin-bottom: 0;
    }
  }

  &[x-placement^="right"] {
    margin-left: 5px;

    .tooltip-arrow {
      border-width: 5px 5px 5px 0;
      border-left-color: transparent !important;
      border-top-color: transparent !important;
      border-bottom-color: transparent !important;
      left: -5px;
      top: calc(50% - 5px);
      margin-left: 0;
      margin-right: 0;
    }
  }

  &[x-placement^="left"] {
    margin-right: 5px;

    .tooltip-arrow {
      border-width: 5px 0 5px 5px;
      border-top-color: transparent !important;
      border-right-color: transparent !important;
      border-bottom-color: transparent !important;
      right: -5px;
      top: calc(50% - 5px);
      margin-left: 0;
      margin-right: 0;
    }
  }

  &.popover {
    $color: #f9f9f9;

    .popover-inner {
      background: $color;
      color: black;
      padding: 24px;
      border-radius: 5px;
      box-shadow: 0 5px 30px rgba(black, 0.1);
    }

    .popover-arrow {
      border-color: $color;
    }
  }

  &[aria-hidden="true"] {
    visibility: hidden;
    opacity: 0;
    transition: opacity 0.15s, visibility 0.15s;
  }

  &[aria-hidden="false"] {
    visibility: visible;
    opacity: 1;
    transition: opacity 0.15s;
  }
}
</style>
