<template>
  <div class="flex page">
    <div class="bg-grey pt-6 flex-1" :class="(language === 'ar') ? 'pr-24' : 'pl-24'">
      <div
        class="page-content-container h-full flex flex-col pb-6 text-black overflow-y-auto"
        :class="(language === 'ar') ? 'pl-6' : 'pr-6'"
        style="height:87.5vh;"
      >
        <div class="flex items-center text-black my-3 text-sm rubik-bold" v-if="event.profile">
          <img
            class="float-left rounded-full border border-grey w-8"
            :class="(language === 'ar') ? 'ml-1' : 'mr-1'"
            :src="'/storage/images/' + event.profile.image_path"
            alt
          />
          <p class="text-grey-darkest">{{ $t('shared.host') }}</p>
          <p class>&nbsp;{{ event.profile.name }}</p>
        </div>

        <div class="placeholder-image text-center mb-5" alt v-if="event">
          <img
            class="border border-grey w-full object-cover"
            :src="'/storage/images/' + event.image_path"
            alt
          />
        </div>

        <div class="flex border-b border-teal-light mb-4 pb-3">
          <div class="flex justify-between w-full">
            <div>
              <p class="text-base text-teal rubik-bold mb-1">{{ event.title }}</p>

              <div class="flex text-sm my-1">
                <p
                  class
                  v-if="!event.end_date"
                >{{ moment(event.start_date).format('MMMM DD, YYYY') }}</p>
                <p class v-else>{{ moment(event.start_date).format('MMMM DD') }}</p>

                <span
                  v-if="event.end_date"
                  class
                >&nbsp;until&nbsp;{{ moment(event.end_date).format('MMMM DD YYYY') }}</span>

                <p v-if="event.starts_at">
                  &nbsp;at&nbsp;
                  <span
                    v-if="event.ends_at"
                  >{{ moment(event.starts_at).format("HH:mm") }}</span>

                  <span v-else>{{ moment(event.starts_at).format("HH:mm A") }}</span>
                </p>

                <span v-if="event.ends_at">
                  <p class>&nbsp;-&nbsp;{{ moment(event.ends_at).format("HH:mm A") }}&nbsp;</p>
                </span>

                <p
                  class
                  v-if="event.timezone"
                >&nbsp;({{ event.timezone.timezone_value.name[language] }})</p>
              </div>

              <p
                class="text-xs rubik-regular text-grey-darkest mb-3"
                v-if="event.privacy && language === 'ar'"
              >{{ $t('components.events.event') + ' ' + event.privacy.privacy_value.name[language]}}</p>

              <p
                class="text-xs rubik-regular text-grey-darkest mb-3"
                v-else-if="event.privacy"
              >{{ event.privacy.privacy_value.name[language] + ' ' + $t('components.events.event') }}</p>
            </div>

            <div class="flex justify-between">
              <div class="flex justify-center items-center">
                <button
                  class="bg-teal-light flex text-white border px-4 py-2 border-teal-light hover:bg-white hover:text-teal"
                  :class="(language === 'ar') ? 'ml-2' : 'mr-2'"
                  v-if="event.member && event.begun"
                  @click="enterEvent"
                >{{ $t('components.events.enter_event') }}</button>

                <div v-if="event.participants && event.privacy.privacy_value.key !== 'public'">
                  <button
                    class="bg-teal-light flex text-white border px-4 py-2 border-teal-light hover:bg-white hover:text-teal"
                    :class="(language === 'ar') ? 'ml-2' : 'mr-2'"
                    v-if="!event.member && Object.keys(event.participants).length >= 1 && !event.invitation_sent"
                    @click="requestInvitation"
                  >
                    <span v-if="!event.membership_requested">{{ $t('shared.request_invitation') }}</span>

                    <span v-else>{{ $t('shared.toast.request_sent') }}</span>
                  </button>

                  <button
                    class="bg-teal-light flex text-white border px-4 py-2 border-teal-light hover:bg-white hover:text-teal"
                    :class="(language === 'ar') ? 'ml-2' : 'mr-2'"
                    v-if="event.invitation_sent"
                    @click="acceptInvitation(event.invitation_sent.invitations_id, event.invitation_sent.invitations_type)"
                  >
                    <span>{{ $t('shared.accept_invitation') }}</span>
                  </button>
                </div>

                <div v-else-if="!event.member" :class="(event.member) ? 'hidden' : 'block'" v-cloak>
                  <button
                    class="bg-teal-light flex text-white border px-4 py-2 border-teal-light hover:bg-white hover:text-teal"
                    :class="(language === 'ar') ? 'ml-2' : 'mr-2'"
                    @click="joinEvent"
                  >
                    <span>{{ $t('components.events.join_event') }}</span>
                  </button>
                </div>

                <button
                  class="bg-teal-light flex text-white border px-4 py-2 border-teal-light hover:bg-white hover:text-teal"
                  :class="(language === 'ar') ? 'ml-2' : 'mr-2'"
                  v-if="event.admin"
                  @click="openModal('invite-to-resource')"
                >{{ $t('shared.invite') }}</button>

                <b-dropdown
                  class="block bg-teal-dark regular-border-radius"
                  hoverable
                  :position="(language === 'ar') ? 'is-bottom-right' : 'is-bottom-left'"
                  v-if="event.member"
                >
                  <button
                    class="bg-teal-light float-right px-4 border-teal-light p-2"
                    slot="trigger"
                  >
                    <!-- <img class="invert-icon w-5 mt-1" src="/svgs/check.svg" alt /> -->

                    <font-awesome-icon
                      class="icon text-white cursor-pointer"
                      :icon="['fa', 'check']"
                      size="lg"
                    />
                  </button>

                  <b-dropdown-item
                    class="cursor-pointer"
                    @click="leaveEvent"
                  >{{ $t('components.events.leave_event') }}</b-dropdown-item>
                </b-dropdown>

                <button
                  class="bg-grey-darker float-right px-4 border-teal-light"
                  v-if="!event.member"
                  disabled="true"
                >
                  <!-- <img class="invert-icon w-5 mt-1" src="/svgs/check.svg" alt /> -->
                  <font-awesome-icon
                    class="icon text-white cursor-pointer"
                    :icon="['fa', 'check']"
                    size="lg"
                  />
                </button>

                <div
                  class="bg-orange p-2 rounded mx-2 cursor-pointer"
                  @click="openModal('edit-event')"
                  v-if="event.admin"
                >
                  <font-awesome-icon
                    class="icon text-white cursor-pointer"
                    :icon="['fa', 'cog']"
                    size="lg"
                  />
                </div>
                <!-- <img
                  @click="openModal('edit-event')"
                  v-if="event.admin"
                  class="mx-2 w-10 cursor-pointer"
                  src="/svgs/settings.svg"
                  alt
                />-->
              </div>
            </div>
          </div>
        </div>

        <p class="rubik-regular text-sm">{{ event.body }}</p>

        <b-tooltip
          class="cursor-pointer"
          :class="(language === 'ar') ? 'mr-auto' : 'ml-auto'"
          :label="$t('shared.report')"
          position="is-top"
          animated
          @click.native="openReport(event, 'events')"
        >
          <font-awesome-icon
            class="icon text-teal cursor-pointer w-full"
            :icon="['fa', 'question-circle']"
            size="lg"
          />
        </b-tooltip>

        <div class="clearfix"></div>

        <div class="absolute pin-t mt-32">
          <transition name="fade">
            <audio-recorder
              v-if="openRecorder"
              class="absolute pin-t z-50"
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
          class="flex items-center relative my-4 outline-none bg-white rounded-full"
          v-if="event.member"
        >
          <div
            class="flex w-12 pin-t cursor-pointer items-center"
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

              <font-awesome-icon
                class="z-0 absolute icon text-grey-darkest cursor-pointer w-full hover:text-grey-darkest"
                :icon="['fa', 'plus']"
                size="2x"
              />

              <!-- <img class="absolute w-6 icon-grey cursor-pointer z-0" src="/svgs/plus.svg" alt /> -->
            </div>
          </div>

          <div
            class="flex w-16 icon-grey absolute"
            :class="(language === 'ar') ? 'pin-l' : 'pin-r'"
          >
            <font-awesome-icon
              class="icon text-grey-darker cursor-pointer text-grey-darkest"
              :icon="['fa', 'microphone']"
              size="2x"
              @click="openRecorder = true"
            />
          </div>

          <input
            type="search"
            class="text-lg w-full bg-transparent rounded-full p-3 py-4 text-grey-darkest h-full"
            :class="(language === 'ar') ? 'pr-0' : 'pl-0'"
            :placeholder="$t('shared.name_placeholder')"
            id="send-message"
            v-model="postText"
            @keyup.enter="sendPostMessage"
          />
        </div>

        <div class="flex bg-teal text-white">
          <div class="w-auto items-center py-5 px-8">
            <span class="text-lg rubik-regular">{{ $t('shared.posts') }}</span>
            <span
              class="text-xs rubik-regular leading-tight"
              :class="(language === 'ar') ? 'mr-1' : 'ml-1'"
            >
              <span v-if="sortedPosts">{{ sortedPosts.length }}</span>
              {{ $t('shared.comments_posted') }}
            </span>
          </div>
        </div>

        <div
          class="bg-white py-3 px-8 mb-4 flex flex-col relative text-black"
          v-for="(item, index) in sortedPosts"
          :key="index"
        >
          <div class="flex items-center justify-between w-full">
            <div class="flex items-center justify-between">
              <img
                class="rounded-full border border-grey w-12"
                :src="'/storage/images/' + item.profile.image_path"
                alt
              />
              <span class="mt-3" :class="(language === 'ar') ? 'mr-2' : 'ml-2'">
                <span class="text-teal rubik-medium text-sm">{{ item.profile.name}}</span>
                <span
                  class="rubik-regular text-xs"
                  v-if="item.profile.location"
                >({{ item.profile.location.country.name[language] }})</span>
              </span>
            </div>

            <p
              class="mt-3 rubik-regular text-xs text-grey-darker"
              :class="(language === 'ar') ? 'ml-3' : 'mr-3'"
            >{{ moment(item.created_at).format('MMMM DD') }}</p>
          </div>

          <hr class="post-horizontal-line bg-teal-light" />

          <p class="mb-4 rubik-regular text-sm" :class="(language === 'ar') ? 'pr-2' : 'pl-2'">
            <img
              v-if="item.content_path && item.type === 'image'"
              :src="'/storage/images/' + item.content_path"
              alt
            />

            <audio-player
              v-if="item.content_path && item.type === 'audio'"
              :src="'/storage/audio/' + item.content_path"
            />
            {{ item.body }}
          </p>

          <b-tooltip
            class="ml-auto cursor-pointer"
            :label="$t('shared.report')"
            position="is-left"
            animated
            @click.native="openReport(item, 'posts')"
          >
            <img class src="/svgs/question_mark.svg" alt />
          </b-tooltip>
        </div>
      </div>
    </div>

    <div class="main-sidebar flex flex-col bg-teal w-1/3 -mt-1 relative z-0">
      <div class="overflow-y-scroll" style="height:80.5vh;">
        <div class="px-10 pt-6 overflow-y-auto">
          <!-- <img
            :class="(language === 'ar') ? 'pr-2 pl-1 float-right' : 'pl-2 pr-1 float-left'"
            src="/svgs/notes.svg"
            alt
          />-->
          <div class="flex mb-5">
            <div
              class="flex justify-center items-center bg-orange rounded-full"
              style="width:40px;height:40px;"
              :class="(language === 'ar') ? 'ml-3' : 'mr-3'"
            >
              <font-awesome-icon
                class="icon text-white cursor-pointer w-full"
                :icon="['fa', 'clipboard']"
                size="lg"
              />
            </div>

            <p
              class="mt-2 mb-4 text-lg rubik-medium"
              v-if="event.participants"
            >{{ $t('shared.members') }} ({{ Object.keys(event.participants).length}})</p>
          </div>

          <div
            class="w-full flex-col mb-1"
            v-for="(item, index) in event.participants"
            :key="index"
          >
            <div
              class="bg-white w-full flex border border-white rounded-sm px-6 py-4 text-teal items-center justify-start mb-4 relative"
            >
              <img
                class="float-left rounded-full border border-grey w-16"
                :class="(language === 'ar') ? 'ml-1' : 'mr-1'"
                :src="'/storage/images/' + item.image_path"
                alt
              />

              <div class="flex-1" :class="(language === 'ar') ? 'mr-3' : 'ml-3'">
                <a :href="'/profiles/' + item.username">
                  <p class="text-teal rubik-medium text-sm">{{ item.name }}</p>
                </a>
                <p
                  class="rubik-regular text-xs text-black"
                  v-if="item.location"
                >{{ item.location.country.name[language] }}</p>
              </div>

              <div
                class="absolute pin-t flex justify-end items-center mt-3 mx-2"
                :class="(language === 'ar') ? 'pin-l' : 'pin-r'"
              >
                <FriendShipInteractionComponent
                  class="mx-2"
                  :tempprofile="item"
                  v-on:friend-request-sent="addFriendRequestSent"
                  v-on:friend-request-accepted="addIsFriend"
                  v-on:friend-request-refused="clearFriendRequest"
                ></FriendShipInteractionComponent>
              </div>
            </div>
          </div>
        </div>

        <div class="px-10 pt-6 overflow-y-auto">
          <div class="flex mb-5">
            <div
              class="flex justify-center items-center bg-orange rounded-full"
              style="width:40px;height:40px;"
              :class="(language === 'ar') ? 'ml-3' : 'mr-3'"
            >
              <font-awesome-icon
                class="icon text-white cursor-pointer w-full"
                :icon="['fa', 'ghost']"
                size="lg"
              />
            </div>

            <p
              class="pt-2 mb-4 text-lg rubik-medium"
            >{{ $t('components.events.currently_in_hall') }}</p>
          </div>

          <div class="flex flex-col bg-white p-10 border border-white rounded-sm">
            <p class="rubik-medium text-xs text-black" v-if="!event.begun && !event.ended">
              {{ $t('components.events.the_event') }}
              <span class="rubik-bold">{{ event.name }}</span>
              {{ $t('components.events.has_not_yet_begun') }}
              <br />
              <br />
              {{ $t('components.events.please_come_back') }}
              {{ moment(event.start_date).format('MMMM D') }}
              {{ $t('shared.at') }}
              {{ moment(event.starts_at).format('HH:mm a') }}.
              {{ $t('components.events.you_will_be_able_to_enter') }}
            </p>

            <p v-else-if="event.begun" class="rubik-medium text-xs text-black">
              {{ $t('components.events.the_event') }}
              <span class="rubik-bold">{{ event.name }}</span>
              {{ $t('components.events.has_begun') }}.
            </p>

            <p v-else-if="event.ended" class="rubik-medium text-xs text-black">
              {{ $t('components.events.the_event') }}
              <span class="rubik-bold">{{ event.name }}</span>
              {{ $t('components.events.has_ended') }}.
            </p>

            <div class="mt-2 text-teal rubik-medium text-sm">
              <div v-for="(item, index) in membersInHall" :key="index">
                <p>{{ item.member.name }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <footer-component></footer-component>
    </div>
  </div>
</template>

<script>
import EditEvent from "./../components/events/EditEvent.vue";
import InviteToResource from "./../components/utils/InviteToResource.vue";
import { mapState, mapMutations, mapActions } from "vuex";
import FriendShipInteractionComponent from "./../components/friends/FriendShipInteractionComponent.vue";
import Modal from "./../components/utils/Modal";
import { directive as onClickaway } from "vue-clickaway";

export default {
  props: ["slug"],
  directives: {
    onClickaway: onClickaway
  },
  computed: {
    ...mapState({
      profile: state => state.profiles.signedInProfile,
      event: state => state.events.activeEvent,
      language: state => state.i18n.locale,
      profilesThatCanBeInvited: state => state.events.profilesThatCanBeInvited,
      activeModal: state => state.ui.activeModal
    }),
    sortedPosts() {
      return _.orderBy(this.posts, "created_at", "desc");
    }
  },
  async created() {
    this.isLoading = true;

    await axios.get("/event/" + this.slug).then(response => {
      this.$store.dispatch("events/setActiveEvent", response.data.event);

      this.$store.dispatch(
        "events/setProfilesThatCanBeInvited",
        response.data.profiles_that_can_be_invited.data
      );

      this.$store.dispatch("communities/getMyCommunities").then(() => {
        this.isLoading = false;
      });

      response.data.posts.forEach(element => {
        this.posts = {
          ...this.posts,
          [element.id]: element
        };
      });

      response.data.members_in_hall.forEach(element => {
        this.membersInHall = {
          ...this.membersInHall,
          [element.id]: element
        };
      });
    });
  },

  mounted() {
    // this.$store.dispatch("ui/setInviteToResource", "events");
    // this.$store.dispatch("ui/setInviteFrom", "update");

    // this.$store.dispatch("ui/setActiveModal", "invite-to-resource");
  },

  data() {
    return {
      modalOpen: false,
      modalName: "",
      editEventModalOpen: false,
      inviteToResourceModalOpen: false,
      postText: null,
      posts: {},
      membersInHall: null,
      isLoading: true,
      recording: null,
      openRecorder: false,
      message: null,
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
      this.postText = "recording";
    },
    closeRecorder() {
      this.openRecorder = false;
    },
    callback(msg) {
      console.debug("Event: ", msg);
    },
    onFileChange(e) {
      let files = e.target.files || e.dataTransfer.files;
      if (!files.length) return;
      this.createImage(files[0]);
    },
    openReport(resource, resourceType) {
      this.$store.dispatch("reports/setReportedResourceType", resourceType);

      this.$store.dispatch("reports/setReportedResource", resource);

      this.$store.dispatch("ui/setReportView", true);
    },
    createImage(file, type = null) {
      let reader = new FileReader();
      let vm = this;

      reader.onload = e => {
        this.file = e.target.result;
        this.postText = file.name;

        this.sendPostMessage(type);
      };

      reader.readAsDataURL(file);
    },
    leaveEvent() {
      this.$store.dispatch("ui/setActiveModal", "LeaveEvent");
    },
    enterEvent() {
      window.location.href = "/hallway/" + this.event.slug;
    },
    async joinEvent() {
      await axios
        .post("/event/" + this.event.slug + "/join", {
          slug: this.event.slug
        })
        .then(response => {
          this.$store.dispatch("events/setActiveEvent", response.data.event);

          this.$toast.open({
            duration: 5000,
            message: this.$t("components.events.toast.event_joined"),
            position: "is-bottom",
            type: "is-success"
          });
        });
    },
    async acceptInvitation(invitationId, invitationsType) {
      await axios
        .post("/invitations/accept", {
          invitation_id: invitationId,
          invitations_type: invitationsType
        })
        .then(response => {
          this.$toast.open({
            duration: 5000,
            message: this.$t("shared.toast.invitation_accepted"),
            position: "is-bottom",
            type: "is-success"
          });

          // Vue.delete(this.invitations, invitationId);
        })
        .catch(error => {
          this.loading = false;
        });
    },
    async requestInvitation() {
      await axios
        .post("/events/requestInvitation", {
          event: this.event,
          type: "event"
        })
        .then(response => {
          this.$toast.open({
            duration: 5000,
            message: this.$t("shared.toast.request_sent"),
            position: "is-bottom",
            type: "is-success"
          });

          this.$store.dispatch("events/updateEventRequest");
        })
        .catch(error => {
          this.finishLoading = false;
        });
    },
    async sendPostMessage(type = null) {
      let that = this;

      if (this.postText) {
        await axios
          .post("/event/" + this.event.slug + "/post", {
            message: this.postText,
            file: this.file,
            type: type
          })
          .then(response => {
            this.openRecorder = false;
            (this.file = null),
              (this.posts = {
                ...this.posts,
                [response.data.post.id]: response.data.post
              });

            this.postText = null;
          });
      }
    },
    openModal(name) {
      this.$store.dispatch("ui/setInviteToResource", "events");
      this.$store.dispatch("ui/setInviteFrom", "update");

      this.$store.dispatch("ui/setActiveModal", name);

      // this.modalOpen = true;
      // this.modalName = name;
    },
    addFriendRequestSent(profileId) {
      this.$store.dispatch("events/addFriendRequestSent", profileId);
    },
    addIsFriend(profileId) {
      this.$store.dispatch("events/addIsFriend", profileId);
    },
    clearFriendRequest(profileId) {
      this.$store.dispatch("events/clearFriendRequest", profileId);
    },
    eventUpdated() {
      this.editEventModalOpen = false;

      this.$toast.open({
        duration: 5000,
        message: this.event.title + this.$t("shared.toast.has_been_updated"),
        position: "is-bottom",
        type: "is-success"
      });
    }
  },
  components: {
    EditEvent,
    FriendShipInteractionComponent,
    InviteToResource
  }
};
</script>

<style lang="scss" scoped>
.placeholder-image {
  width: 100%;
  height: 55vh;
  min-height: 55vh;

  img {
    height: inherit;
  }
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
