<template>
  <div
    class="header-container flex justify-between items-center relative z-50 flex-no-shrink"
    :class="(language === 'ar') ? 'pr-24 pl-10' : 'pl-24 pr-12'"
  >
    <div class="flex items-center w-1/2">
      <div class="w-1/3">
        <a class href="/">
          <img class="w-1/4 inline align-middle" src="/images/logo.png" alt />

          <p
            class="text-xl inline align-middle rubik-regular mx-2"
          >{{ $t('components.header.logo') }}</p>
        </a>
      </div>

      <div class="inline-flex links text-sm text-grey rubik-light ml-4">
        <a
          id="nav-news"
          class="whitespace-no-wrap align-middle ml-6 pt-2 -mt-2"
          href="/news"
        >{{ $t('components.header.news_feed') }}</a>

        <a
          id="nav-events"
          class="whitespace-no-wrap align-middle ml-6 pt-2 -mt-2"
          href="/events"
        >{{ $t('components.header.events') }}</a>

        <a
          id="nav-communities"
          class="whitespace-no-wrap align-middle ml-6 pt-2 -mt-2"
          href="/communities"
        >{{ $t('components.header.communities') }}</a>

        <a
          id="nav-friends"
          class="whitespace-no-wrap align-middle ml-6 pt-2 -mt-2"
          href="/friends"
        >{{ $t('components.header.friends') }}</a>
      </div>
    </div>

    <div v-if="signedInProfile" class="inline-flex items-center">
      <b-dropdown :class="(language === 'ar') ? 'ml-6' : ''" hoverable aria-role="list">
        <button
          class="button bg-transparent text-white"
          :class="(language === 'ar') ? 'pl-1' : ''"
          slot="trigger"
        >
          <span class="text-sm" :class="(language === 'ar') ? 'pl-4' : ''">{{ language }}</span>

          <b-icon icon="menu-down"></b-icon>
        </button>

        <a href="/change_locale/en" class="w-8">
          <b-dropdown-item aria-role="listitem">English</b-dropdown-item>
        </a>
        <a href="/change_locale/ar" class="w-8">
          <b-dropdown-item aria-role="listitem">العربية</b-dropdown-item>
        </a>
      </b-dropdown>

      <a
        id="nav-search"
        class="whitespace-no-wrap w-5 block cursor-pointer"
        :class="(language === 'ar') ? 'mr-3' : 'ml-6'"
        href="/search-page"
      >
        <!-- <img src="/svgs/search.svg" alt /> -->
        <font-awesome-icon
          class="icon text-white hover:text-teal-darkest cursor-pointer"
          :icon="['fa', 'search']"
          size="lg"
        />
      </a>

      <a
        id="nav-chat"
        class="whitespace-no-wrap w-5 block cursor-pointer"
        :class="(language === 'ar') ? 'mr-6' : 'ml-6'"
        href="/chat"
      >
        <font-awesome-icon
          class="icon text-white hover:text-teal-darkest cursor-pointer"
          :icon="['fa', 'comment']"
          size="lg"
        />
        <!-- <img src="/svgs/chat.svg" alt /> -->
      </a>

      <b-dropdown
        class
        :class="(language === 'ar') ? 'pr-6 is-bottom-right' : 'pl-6 is-bottom-left'"
        hoverable
        open
      >
        <a class="relative block" href="#" slot="trigger">
          <!-- <img
            class="w-5 block cursor-pointer"
            v-bind:class="notificationClass"
            src="/svgs/bell.svg"
            alt
          />-->

          <font-awesome-icon
            class="icon text-white hover:text-teal-darkest cursor-pointer"
            v-bind:class="notificationClass"
            :icon="['fa', 'bell']"
            size="lg"
          />
          <span
            class="text-xs text-red-dark absolute pin-b -mb-3"
            :class="(language === 'ar') ? 'pin-l -ml-3' : 'pin-r -mr-3'"
            v-if="Object.keys(notifications).length !== 0"
          >{{ Object.keys(notifications).length }}</span>
        </a>

        <a
          class="cursor-pointer"
          :href="'/user/' + signedInProfile.username + '/settings#invitations'"
          v-for="(notification, index) in notifications"
          :key="index"
        >
          <b-dropdown-item class>{{ notification.message }}</b-dropdown-item>
        </a>
      </b-dropdown>

      <b-dropdown
        :class="(language === 'ar') ? 'pr-6 text-right' : 'pl-6'"
        :position="(language === 'ar') ? 'is-bottom-right' : 'is-bottom-left'"
        hoverable
        open
      >
        <img src="/svgs/navbar_profile.svg" alt slot="trigger" v-if="!profileImage" />

        <div
          class="overflow-hidden"
          style="border-radius:50%;width:50px;height:50px;"
          slot="trigger"
          v-else
        >
          <img :src="'/storage/images/' + signedInProfile.image_path" alt />
        </div>

        <div class>
          <a
            class="cursor-pointer"
            :class="(language === 'ar') ? '' : ''"
            :href="'/profiles/' + signedInProfile.username"
          >
            <b-dropdown-item
              :class="(language === 'ar') ? '' : ''"
            >{{ $t('components.header.profile')}}</b-dropdown-item>
          </a>

          <a class="cursor-pointer" :href="'/user/' + signedInProfile.username + '/settings'">
            <b-dropdown-item class>{{ $t('components.header.settings')}}</b-dropdown-item>
          </a>

          <a class="cursor-pointer" href="#">
            <b-dropdown-item class @click="logOut()">{{ $t('components.header.sign_out')}}</b-dropdown-item>
          </a>
        </div>
      </b-dropdown>
    </div>

    <div v-else class="inline-flex items-center text-sm">
      <p
        class
        :class="(language === 'ar') ? 'ml-4' : 'mr-4'"
      >{{ $t('components.header.not_a_member') }}</p>

      <button
        class="raleway-semibold hover:bg-transparent hover:text-white border border-white rounded block bg-white text-teal px-4 py-2 mx-4"
        @click="registrationModalOpen = true"
      >{{ $t('components.header.join_meet_mingle') }}</button>

      <p class>&#45;</p>
      <p class="mx-4">{{ $t('components.header.sign_up_with') }}</p>

      <a href="auth/facebook">
        <img class="invert-icon w-6 cursor-pointer" src="/svgs/facebook.svg" alt />
      </a>

      <b-dropdown
        class
        :class="(language === 'ar') ? 'mr-6' : 'ml-6'"
        :position="(language === 'ar') ? 'is-bottom-right' : 'is-bottom-left'"
        hoverable
        aria-role="list"
      >
        <button
          class="button text-white bg-transparent"
          :class="(language === 'ar') ? 'pl-1' : ''"
          slot="trigger"
        >
          <span class="text-sm" :class="(language === 'ar') ? 'pl-4' : ''">{{ language }}</span>
          <b-icon icon="menu-down"></b-icon>
        </button>

        <a href="/change_locale/en" class="w-8">
          <b-dropdown-item aria-role="listitem">English</b-dropdown-item>
        </a>
        <a href="/change_locale/ar" class="w-8">
          <b-dropdown-item aria-role="listitem">العربية</b-dropdown-item>
        </a>
      </b-dropdown>
    </div>

    <transition name="fade">
      <registration-component
        v-if="registrationModalOpen"
        v-on:modal:close="registrationModalOpen = false"
      ></registration-component>
    </transition>

    <transition name="fade">
      <report
        v-if="reportOpen"
        v-on:modal:close="reportOpen = false"
        :reported_resource="reportedResource"
      ></report>
    </transition>

    <transition name="fade">
      <modal
        v-if="activeModal"
        :current-component="activeModal"
        :standalone="true"
        v-on:modal:close="closeModal($event)"
        v-on:modal:unfriend="is_friend = false"
        v-on-clickaway="close"
      ></modal>
    </transition>

    <transition name="fade">
      <share
        v-if="shareModalOpen"
        v-on:modal:close="closeModal($event)"
        v-on-clickaway="closeShareModal"
      ></share>
    </transition>
  </div>
</template>

<script>
import { mapState, mapMutations, mapActions } from "vuex";
import moment from "moment";
import { directive as onClickaway } from "vue-clickaway";
import Report from "./../components/utils/Report.vue";
import Share from "./../components/utils/Share.vue";

export default {
  props: ["profile", "page", "hallway", "locale"],
  directives: {
    onClickaway: onClickaway
  },
  computed: {
    ...mapState({
      signedInProfile: state => state.profiles.signedInProfile,
      notifications: state => state.ui.notifications,
      language: state => state.i18n.locale,
      accessedProfile: state => state.ui.accessedProfile,
      reportOpen: state => state.ui.reportOpen,
      activeModal: state => state.ui.activeModal,
      reportedResource: state => state.reports.reportedResource,
      shareModalOpen: state => state.ui.shareModalOpen
    }),
    profileImage() {
      if (this.signedInProfile.image_path !== "default.jpeg") {
        return true;
      } else {
        return false;
      }
    },
    notificationClass() {
      if (Object.keys(this.notifications).length !== 0) {
        return "text-teal-darkest";
      } else {
        return "text-white";
      }
    }
  },
  created() {
    Vue.i18n.set(this.locale);
    moment.locale(this.locale);

    this.$store.dispatch("profiles/setSignedInProfile", this.profile);
  },
  async mounted() {
    if (this.signedInProfile) {
      Echo.private(`invitation.${this.signedInProfile.id}`).listen(
        "ProfileInvited",
        ({ message }) => {
          this.$store.dispatch("ui/setNotifications", message);
        }
      );

      Echo.private(`request.${this.signedInProfile.id}`).listen(
        "FriendshipAccepted",
        ({ message }) => {
          // console.log("friend ship accepted");
          this.$store.dispatch(
            "conferencing/updateFriendshipStatusToFriends",
            message.profile
          );

          this.$toast.open({
            duration: 5000,
            message: message.profile.name + " accepted your friend request.",
            position: "is-bottom",
            type: "is-success"
          });
        }
      );

      Echo.private(`request.${this.signedInProfile.id}`).listen(
        "FriendshipRequested",
        ({ message }) => {
          // let src =
          //   "http://soundbible.com/grab.php?id=1682&";
          // let audio = new Audio(src);
          // audio.play();

          this.$toasted.show(
            message.profile.name +
              " " +
              this.$t("components.profiles.toast.sent_you_a_friend_request"),
            {
              theme: "outline",
              position: this.language === "ar" ? "top-left" : "top-right",
              singleton: false,
              type: "success",
              duration: null,
              iconPack: "fontawesome",
              // icon: {
              //   name: "fas-check"
              // },
              action: [
                {
                  text: this.$t("shared.accept"),
                  class: "acceptClass",
                  onClick: async (e, toastObject) => {
                    await axios
                      .post("/requests/accept", {
                        username: message.profile.username
                      })
                      .then(response => {
                        // this.$emit("friend-request-accepted", profileId);
                        toastObject.goAway(0);

                        this.$store.dispatch(
                          "conferencing/updateFriendshipStatusToFriends",
                          message.profile
                        );

                        this.$toast.open({
                          duration: 5000,
                          message: this.$t(
                            "components.profiles.toast.friend_request_accepted"
                          ),
                          position: "is-bottom",
                          type: "is-success"
                        });
                      })
                      .catch(error => {});
                  }
                },
                {
                  text: this.$t("shared.refuse"),
                  class: "refuseClass",
                  onClick: (e, toastObject) => {
                    toastObject.goAway(0);
                  }
                }
              ]
            }
          );

          this.$store.dispatch("ui/setNotifications", message);
        }
      );

      Echo.private(`requests.${this.signedInProfile.id}`).listen(
        "ProfileRequested",
        ({ message }) => {
          this.$store.dispatch("ui/setNotifications", message);
        }
      );
    }

    await axios
      .get("/invitations")
      .then(response => {
        response.data.eventInvitations.forEach(element => {
          this.$store.dispatch("ui/setNotifications", element);
        });

        response.data.communityInvitations.forEach(element => {
          this.$store.dispatch("ui/setNotifications", element);
        });
      })
      .catch(error => {
        this.loading = false;
      });

    await axios
      .get("/requests")
      .then(response => {
        response.data.requests.forEach(element => {
          this.$store.dispatch("ui/setNotifications", element);
        });
      })
      .catch(error => {
        this.loading = false;
      });

    await axios
      .get("/getRequests")
      .then(response => {
        response.data.requests.forEach(element => {
          this.$store.dispatch("ui/setNotifications", element);
        });
      })
      .catch(error => {
        this.loading = false;
      });

    if (window.location.href.indexOf("/news") > -1) {
      $("#nav-news").css("color", "white");
      $("#nav-news").css("borderTop", "1px solid white");
    } else if (window.location.href.indexOf("/events") > -1) {
      $("#nav-events").css("color", "white");
      $("#nav-events").css("borderTop", "1px solid white");
    } else if (window.location.href.indexOf("/communities") > -1) {
      $("#nav-communities").css("color", "white");
      $("#nav-communities").css("borderTop", "1px solid white");
    } else if (window.location.href.indexOf("/friends") > -1) {
      $("#nav-friends").css("color", "white");
      $("#nav-friends").css("borderTop", "1px solid white");
    }

    if (this.hallway) {
      $(".header-container").addClass("hide-header");
    }
  },
  data() {
    return {
      registrationModalOpen: false
    };
  },
  methods: {
    ShareModal() {
      this.$store.dispatch("ui/setActiveModal", null);
    },
    closeShareModal() {
      this.$store.dispatch("ui/closeShareModal");
    },
    closeRegistrationModal() {
      this.registrationModalOpen = false;
    },
    setLanguage(value) {
      Vue.i18n.set(value);
    },
    close() {
      this.$emit("modal:close");
    },
    logOut() {
      axios
        .get("/user/logout")
        .then(response => {
          window.location.href = "/";
        })
        .catch(error => {});
    }
  },
  components: {
    Report,
    Share
  }
};
</script>

<style scoped lang="scss">
.header-container {
  color: white;
  background: url(/images/header_background.png);
  background-repeat: no-repeat;
  background-size: cover;
  background-color: #347f79;
  min-height: 10vh;
}

.hide-header {
  // display: none;
  // height:0;
  // overflow: hidden;
  position: absolute;
  top: -9999px;
}

img {
  align-self: center;
}

.active-nav-item {
  color: white;
}

.links {
  a {
    color: lighten(#bebebe, 5%);
    transition: 125ms all ease-in-out;

    &:hover {
      color: lighten(#e9ebee, 100%);
    }
  }
}
</style>
