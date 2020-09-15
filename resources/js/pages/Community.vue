<template>
  <div class="flex page">
    <div class="bg-grey pt-6 flex-1" :class="(language === 'ar') ? 'pr-24' : 'pl-24'">
      <div
        class="page-content-container h-full flex flex-col flex-no-shrink text-black overflow-y-auto"
        :class="(language === 'ar') ? 'pl-6' : 'pr-6'"
        style="height:87.5vh;"
      >
        <div class="flex items-center text-black my-3 text-sm rubik-bold" v-if="community.profile">
          <img
            class="float-left rounded-full border border-grey w-8"
            :class="(language === 'ar') ? 'ml-1' : 'mr-1'"
            :src="'/storage/images/' + community.profile.image_path"
            alt
          />
          <p class="text-grey-darkest">{{ $t('shared.host') }}</p>

          <p class>&nbsp;{{ community.profile.name }}</p>
        </div>

        <div class="placeholder-image text-center mb-5" alt v-if="community">
          <img
            class="border border-grey w-full object-cover"
            :src="'/storage/images/' + community.image_path"
            alt
          />
        </div>

        <div class="flex border-b border-teal-light mb-4 pb-3">
          <div class="flex justify-between w-full">
            <div class>
              <p class="text-base text-teal rubik-bold mb-1">{{ community.name }}</p>

              <p
                class="text-xs rubik-regular text-grey-darkest mb-3"
                v-if="community.privacy && language === 'ar'"
              >{{ $t('components.communities.community') + ' ' + community.privacy.privacy_value.name[language]}}</p>

              <p
                class="text-xs rubik-regular text-grey-darkest mb-3"
                v-else-if="community.privacy"
              >{{ community.privacy.privacy_value.name[language] + ' ' + $t('components.communities.community') }}</p>
            </div>

            <div class="flex flex-wrap">
              <div class="flex flex-wrap justify-end items-center w-full relative">
                <button
                  class="bg-teal-light flex text-white border px-4 py-2 border border-teal-light hover:bg-white hover:text-teal"
                  :class="(language === 'ar') ? 'ml-2' : 'mr-2'"
                  @click="openModal('create-event', 'community')"
                  v-if="community.admin"
                >{{ $t('components.events.create_event') }}</button>
                <button
                  class="bg-teal-light flex text-white border px-4 py-2 border-teal-light hover:bg-white hover:text-teal"
                  :class="(language === 'ar') ? 'ml-2' : 'mr-2'"
                  v-if="community.admin"
                  @click="openModal('invite-to-resource')"
                >{{ $t('shared.invite') }}</button>

                <div
                  v-if="community.participants && community.privacy.privacy_value.key !== 'public'"
                  class
                  :class="(language === 'ar') ? 'mr-auto' : 'ml-auto'"
                >
                  <button
                    class="bg-teal-light flex text-white border px-4 py-2 border-teal-light hover:bg-white hover:text-teal"
                    :class="(language === 'ar') ? 'ml-2' : 'mr-2'"
                    v-if="!community.member && Object.keys(community.participants).length >= 1 && !community.invitation_sent"
                    @click="requestInvitation"
                  >
                    <span v-if="!community.membership_requested">Request To Join</span>
                    <span v-else>{{ $t('shared.toast.request_sent') }}</span>
                  </button>

                  <button
                    class="bg-teal-light flex text-white border px-4 py-2 border-teal-light hover:bg-white hover:text-teal"
                    :class="(language === 'ar') ? 'ml-2' : 'mr-2'"
                    v-if="community.invitation_sent"
                    @click="acceptInvitation(community.invitation_sent.invitations_id, community.invitation_sent.invitations_type)"
                  >
                    <span>{{ $t('shared.accept_invitation') }}</span>
                  </button>
                </div>

                <div
                  v-else-if="!community.member"
                  :class="(community.member) ? 'hidden mr-auto' : 'block ml-auto'"
                  v-cloak
                >
                  <button
                    class="bg-teal-light flex text-white border px-4 py-2 border-teal-light hover:bg-white hover:text-teal"
                    :class="(language === 'ar') ? 'ml-2' : 'mr-2'"
                    @click="joinCommunity"
                  >
                    <span>{{ $t('components.communities.join_community') }}</span>
                  </button>
                </div>

                <!-- <b-dropdown
                  class="block bg-teal-dark regular-border-radius"
                  :class="(language === 'ar') ? 'mr-auto ml-2' : 'ml-auto mr-2'"
                  hoverable
                  :position="(language === 'ar') ? 'is-bottom-right' : 'is-bottom-left'"
                  v-if="community.member"
                >
                  <button class="bg-teal-light float-right px-4 border-teal-light" slot="trigger">
                    <img class="invert-icon w-5 mt-1" src="/svgs/check.svg" alt />
                  </button>

                  <b-dropdown-item
                    class="cursor-pointer"
                    @click="leaveCommunity"
                  >{{ $t('components.communities.leave_community') }}</b-dropdown-item>
                </b-dropdown>-->

                <b-dropdown
                  hoverable
                  :position="(language === 'ar') ? 'is-bottom-right' : 'is-bottom-left'"
                  v-if="community.member"
                >
                  <button
                    class="bg-teal-light px-4 border-teal-light p-2"
                    :class="(language === 'ar') ? 'ml-2' : 'mr-2'"
                    slot="trigger"
                  >
                    <font-awesome-icon
                      class="icon text-white cursor-pointer"
                      :icon="['fa', 'check']"
                      size="lg"
                    />
                  </button>

                  <b-dropdown-item
                    class="cursor-pointer"
                    @click="leaveCommunity"
                  >{{ $t('components.communities.leave_community') }}</b-dropdown-item>
                </b-dropdown>

                <!-- <img
                  @click="openModal('edit-community')"
                  v-if="community.admin"
                  class="w-10 cursor-pointer"
                  :class="(language === 'ar') ? 'ml-2' : 'mr-2'"
                  src="/svgs/settings.svg"
                  alt
                />-->

                <div
                  class="bg-orange p-2 rounded cursor-pointer"
                  :class="(language === 'ar') ? 'ml-2' : 'mr-2'"
                  @click="openModal('edit-community')"
                  v-if="community.admin"
                >
                  <font-awesome-icon
                    class="icon text-white cursor-pointer hover:text-orange-darker"
                    :icon="['fa', 'cog']"
                    size="lg"
                  />
                </div>

                <!-- <img
                  class="w-10 cursor-pointer"
                  :class="(language === 'ar') ? 'ml-2' : 'mr-1'"
                  src="/svgs/admin.svg"
                  alt
                />-->

                <div
                  class="bg-orange p-2 rounded cursor-pointer"
                  :class="(language === 'ar') ? 'ml-2' : ''"
                  style="width:40px;height:40px;"
                  v-if="community.admin"
                >
                  <font-awesome-icon
                    class="text-white cursor-pointer hover:text-orange-darker"
                    :icon="['fa', 'star']"
                    size="lg"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>

        <p class="rubik-regular text-sm">{{ community.body }}</p>

        <b-tooltip
          class="cursor-pointer"
          :class="(language === 'ar') ? 'mr-auto' : 'ml-auto'"
          :label="$t('shared.report')"
          position="is-top"
          animated
          @click.native="openReport(community, 'communities')"
        >
          <!-- <img class src="/svgs/question_mark.svg" alt /> -->
          <font-awesome-icon
            class="icon text-teal cursor-pointer w-full hover:text-teal-darker"
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
          v-if="community.member"
        >
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

          <div
            class="flex w-16 icon-grey absolute"
            :class="(language === 'ar') ? 'pin-l' : 'pin-r'"
          >
            <font-awesome-icon
              class="icon text-grey-darkest cursor-pointer hover:text-grey-dark"
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

        <!-- Post item -->
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
                <span class="text-teal rubik-medium text-sm">{{ item.profile.name }}</span>
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
          />
          <p class="mt-2 mb-4 text-lg rubik-medium" v-if="community.participants">
            {{ $t('shared.members') }}
            ({{ Object.keys(community.participants).length}})
          </p>-->

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

            <p class="mt-2 mb-4 text-lg rubik-medium" v-if="community.participants">
              {{ $t('shared.members') }}
              ({{ Object.keys(community.participants).length}})
            </p>
          </div>

          <div
            class="w-full flex flex-col mb-1"
            v-for="(item, index) in community.participants"
            :key="index"
          >
            <div
              class="bg-white w-full flex border border-white rounded-sm text-teal justify-start mb-4 relative"
              :class="(language === 'ar') ? 'px-6 py-4' : 'pr-2 pl-6'"
            >
              <div class="flex items-center flex-1 py-4">
                <img
                  class="float-left rounded-full border border-grey w-16 mr-1"
                  :class="(language === 'ar') ? 'ml-1' : 'mr-1'"
                  :src="'/storage/images/' + item.image_path"
                  alt
                />

                <div class :class="(language === 'ar') ? 'mr-3' : 'ml-3'">
                  <a :href="'/profiles/' + item.username">
                    <p class="text-teal rubik-medium text-sm">{{ item.name }}</p>
                  </a>

                  <p
                    class="rubik-regular text-xs text-black"
                    v-if="item.location"
                  >{{ item.location.country.name[language] }}</p>
                </div>
              </div>

              <div
                class="flex justify-end items-center py-2 h-12"
                :class="(language === 'ar') ? 'pin-l' : 'pin-r'"
              >
                <FriendShipInteractionComponent
                  class="mx-3"
                  :tempprofile="item"
                  v-on:friend-request-sent="addFriendRequestSent"
                  v-on:friend-request-accepted="addIsFriend"
                  v-on:friend-request-refused="clearFriendRequest"
                ></FriendShipInteractionComponent>

                <b-tooltip
                  v-if="community.owner && !item.admin"
                  class="cursor-pointer"
                  :class="(language === 'ar') ? 'mr-auto' : 'ml-auto'"
                  :label="$t('Make administrator')"
                  position="is-left"
                  animated
                >
                  <!-- <img class="cursor-pointer" src="/svgs/admin.svg" alt @click="makeAdmin(item.id)" /> -->

                  <div
                    class="flex justify-center items-center bg-orange rounded"
                    style="width:30px;height:30px;"
                  >
                    <font-awesome-icon
                      class="text-white cursor-pointer hover:text-orange-darker"
                      :icon="['fa', 'star']"
                      size="sm"
                      @click="makeAdmin(item.id)"
                    />
                  </div>
                </b-tooltip>

                <b-tooltip
                  v-if="community.owner && item.admin"
                  class="cursor-pointer"
                  :class="(language === 'ar') ? 'mr-auto' : 'ml-auto'"
                  :label="$t('Remove as administrator')"
                  position="is-left"
                  animated
                >
                  <!-- <img
                    class="cursor-pointer"
                    src="/svgs/admin.svg"
                    alt
                    @click="removeAdmin(item.id)"
                  />-->
                  <div
                    class="flex justify-center items-center bg-orange rounded"
                    style="width:30px;height:30px;"
                  >
                    <font-awesome-icon
                      class="text-white cursor-pointer hover:text-orange-darker"
                      :icon="['fa', 'star']"
                      size="sm"
                      @click="removeAdmin(item.id)"
                    />
                  </div>
                </b-tooltip>

                <b-tooltip
                  v-if="!community.owner && item.admin"
                  class="cursor-pointer"
                  :class="(language === 'ar') ? 'mr-auto' : 'ml-auto'"
                  :label="$t('Administrator')"
                  position="is-left"
                  animated
                >
                  <div
                    class="flex justify-center items-center bg-orange rounded"
                    style="width:30px;height:30px;"
                  >
                    <font-awesome-icon
                      class="text-white cursor-pointer hover:text-orange-darker"
                      :icon="['fa', 'star']"
                      size="sm"
                    />
                  </div>
                </b-tooltip>
              </div>
            </div>
          </div>
        </div>

        <div class="px-10 pt-6 overflow-y-auto">
          <!-- <img
            :class="(language === 'ar') ? 'pr-2 pl-1 float-right' : 'pl-2 pr-1 float-left'"
            src="/svgs/wine.svg"
            alt
          />

          <p class="pt-2 text-lg rubik-medium" :class="(language === 'ar') ? 'mr-10' : 'ml-10'">
            {{ $t('components.events.upcoming_events') }}
            ({{ Object.keys(upcomingEvents).length}})
          </p>-->

          <div class="flex mb-5">
            <div
              class="flex justify-center items-center bg-orange rounded-full"
              style="width:40px;height:40px;"
              :class="(language === 'ar') ? 'ml-3' : 'mr-3'"
            >
              <font-awesome-icon
                class="icon text-white cursor-pointer w-full"
                :icon="['fa', 'calendar-day']"
                size="lg"
              />
            </div>

            <p class="pt-2 text-lg rubik-medium">
              {{ $t('components.events.upcoming_events') }}
              ({{ Object.keys(upcomingEvents).length}})
            </p>
          </div>

          <slider
            class="px-10"
            :flickityOptions="flickityOptions"
            :cards="upcomingEvents"
            :loading="isLoading"
            :resource="'events'"
          ></slider>
        </div>
      </div>
      <footer-component></footer-component>
    </div>
  </div>
</template>

<script>
import EditCommunity from "./../components/communities/EditCommunity.vue";
import InviteToCommunity from "./../components/communities/InviteToCommunity.vue";
import CreateEvent from "./../components/events/CreateEvent.vue";
import { mapState, mapMutations, mapActions } from "vuex";
import FriendShipInteractionComponent from "./../components/friends/FriendShipInteractionComponent.vue";
import InviteToResource from "./../components/utils/InviteToResource.vue";
import Slider from "./../components/utils/Slider.vue";
import { directive as onClickaway } from "vue-clickaway";

export default {
  props: ["slug"],
  directives: {
    onClickaway: onClickaway
  },
  computed: {
    ...mapState({
      profile: state => state.profiles.signedInProfile,
      community: state => state.communities.activeCommunity,
      profilesThatCanBeInvited: state =>
        state.communities.profilesThatCanBeInvited,
      upcomingEvents: state => state.communities.upcomingEvents,
      language: state => state.i18n.locale,
      activeModal: state => state.ui.activeModal
    }),
    sortedPosts() {
      return _.orderBy(this.posts, "created_at", "desc");
    }
  },
  created() {
    this.isLoading = true;

    axios.get("/community/" + this.slug).then(response => {
      this.$store.dispatch(
        "communities/setActiveCommunity",
        response.data.community
      );
      this.$store.dispatch(
        "communities/setProfilesThatCanBeInvited",
        response.data.profiles_that_can_be_invited.data
      );
      this.$store.dispatch("communities/getMyCommunities");

      this.$store.dispatch("communities/getUpcomingEvents").then(() => {
        this.isLoading = false;
      });

      response.data.posts.forEach(element => {
        this.posts = {
          ...this.posts,
          [element.id]: element
        };
      });
    });
  },
  mounted() {},

  data() {
    return {
      modalOpen: false,
      modalName: "",
      editCommunityModalOpen: false,
      inviteToResourceModalOpen: false,
      createEventModalOpen: false,
      postText: null,
      posts: {},
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
      },
      isLoading: true,
      flickityOptions: {
        prevNextButtons: true,
        pageDots: false,
        wrapAround: true,
        groupCells: true
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
    leaveCommunity() {
      this.$store.dispatch("ui/setActiveModal", "LeaveCommunity");
    },
    async joinCommunity() {
      await axios
        .post("/community/" + this.community.slug + "/join", {
          slug: this.community.slug
        })
        .then(response => {
          this.$store.dispatch(
            "communities/setActiveCommunity",
            response.data.community
          );

          this.$toast.open({
            duration: 5000,
            message: this.$t("components.communities.toast.community_joined"),
            position: "is-bottom",
            type: "is-success"
          });
        });
    },
    async sendPostMessage(type = null) {
      let that = this;
      if (this.postText) {
        await axios
          .post("/community/" + this.community.slug + "/post", {
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
    openModal(name, community = null) {
      this.$store.dispatch("ui/setInviteToResource", "communities");
      this.$store.dispatch("ui/setInviteFrom", "update");

      if (community) {
        this.$store.dispatch("events/setCreatingFrom", community);
      }

      this.$store.dispatch("ui/setActiveModal", name);
    },
    addFriendRequestSent(profileId) {
      this.$store.dispatch("communities/addFriendRequestSent", profileId);
    },
    addIsFriend(profileId) {
      this.$store.dispatch("communities/addIsFriend", profileId);
    },
    openReport(resource, resourceType) {
      this.$store.dispatch("reports/setReportedResourceType", resourceType);

      this.$store.dispatch("reports/setReportedResource", resource);

      this.$store.dispatch("ui/setReportView", true);
    },
    clearFriendRequest(profileId) {
      this.$store.dispatch("communities/clearFriendRequest", profileId);
    },
    communityUpdated() {
      this.editCommunityModalOpen = false;

      this.$toast.open({
        duration: 5000,
        message: this.community.name + this.$t("shared.toast.has_been_updated"),
        position: "is-bottom",
        type: "is-success"
      });
    },
    finishInvite() {
      this.inviteToResourceModalOpen = false;

      this.$toast.open({
        duration: 5000,
        message: this.$t("shared.toast.profiles_invited"),
        position: "is-bottom",
        type: "is-success"
      });
    },
    async makeAdmin(profileId) {
      await axios
        .post("/community/" + this.community.slug + "/makeAdmin", {
          profile_id: profileId,
          community_id: this.community.id
        })
        .then(response => {
          this.$store.dispatch(
            "communities/setActiveCommunity",
            response.data.community
          );

          this.$toast.open({
            duration: 5000,
            message: this.$t("shared.toast.profile_made_administrator"),
            position: "is-bottom",
            type: "is-success"
          });
        });
    },
    async removeAdmin(profileId) {
      await axios
        .post("/community/" + this.community.slug + "/removeAdmin", {
          profile_id: profileId,
          community_id: this.community.id
        })
        .then(response => {
          this.$store.dispatch(
            "communities/setActiveCommunity",
            response.data.community
          );

          this.$toast.open({
            duration: 5000,
            message: this.$t("shared.toast.profile_removed_as_administrator"),
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
        .post("/communities/requestInvitation", {
          community: this.community,
          type: "community"
        })
        .then(response => {
          this.$toast.open({
            duration: 5000,
            message: this.$t("shared.toast.request_sent"),
            position: "is-bottom",
            type: "is-success"
          });

          this.$store.dispatch("communities/updateCommunityRequest");
        })
        .catch(error => {
          this.finishLoading = false;
        });
    }
  },
  components: {
    EditCommunity,
    FriendShipInteractionComponent,
    InviteToCommunity,
    CreateEvent,
    InviteToResource,
    Slider
  }
};
</script>

<style lang="scss" scoped>
.placeholder-image {
  width: 100%;
  height: 55vh;
  min-height: 55vh;
  //   overflow: hidden;

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
