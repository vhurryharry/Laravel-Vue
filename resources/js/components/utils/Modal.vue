<template>
  <div class="fixed pin flex items-center text-white">
    <div class="fixed pin bg-black opacity-50 z-10"></div>

    <div class="relative mx-6 md:mx-auto w-full md:w-1/2 lg:w-1/3 z-20 m-8">
      <div class="shadow-lg bg-teal-lighter rounded-lg p-8">
        <div class="flex justify-end">
          <button class @click="close()">
            <img class="invert-icon w-4" src="/svgs/close.svg" alt />
          </button>
        </div>

        <img class="block mx-auto invert-icon" src="/svgs/logo_flat.svg" alt />

        <component
          v-bind:is="currentComponent"
          :standalone="standalone"
          :profile="signedInProfile"
          :dob="dob"
          :to-block="toBlock"
          :from-community="0"
          :my-communities="[]"
          v-on:phone-registration:open-verification="openPhoneVerification()"
          v-on:phone-registration:open-registration="openPhoneRegistration()"
          v-on:modal:edit-birthday:done="close($event)"
          v-on:modal:edit-password:done="closePassword()"
          v-on:modal:deactivate="deactivate()"
          v-on:modal:activate="activate()"
          v-on:modal:close="close"
          v-on:modal:unfriend="unfriend($event)"
          v-on:modal:block="block()"
          v-on:modal:unblock="unblock()"
          v-on:modal:leave="leave($event)"
          v-on:modal:update-user="updateUser"
          v-on:update:event="eventUpdated"
          v-on:update:community="communityUpdated"
          v-on:invite:finish="finishInvite"
        ></component>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState, mapMutations, mapActions } from "vuex";

import PhoneRegistration from "./../../components/registration/PhoneRegistration.vue";
import PhoneVerification from "./../../components/registration/PhoneVerification.vue";
import DateOfBirth from "./../../components/profile/EditDateOfBirth.vue";
import Description from "./../../components/profile/Description.vue";

import DeactivateAccount from "./../../components/profile/DeactivateAccount.vue";
import DeleteAccount from "./../../components/profile/DeleteAccount.vue";
import ChangePassword from "./../../components/profile/ChangePassword.vue";
import BlockUsers from "./../../components/profile/BlockUsers.vue";
import BlockedProfiles from "./../../components/utils/BlockedProfiles.vue";
import Unfriend from "./../../components/utils/Unfriend.vue";
import Block from "./../../components/utils/Block.vue";
import Unblock from "./../../components/utils/Unblock.vue";
import LeaveEvent from "./../../components/events/LeaveEvent.vue";
import LeaveCommunity from "./../../components/communities/LeaveCommunity.vue";
import RecoverPassword from "./../../components/registration/RecoverPassword.vue";
import PasswordReset from "./../../components/registration/PasswordReset.vue";
import BannedNotification from "./../../components/profile/BannedNotification.vue";
import AppealBan from "./../../components/profile/AppealBan.vue";
import CreateEvent from "./../events/CreateEvent.vue";
import CreateCommunity from "./../communities/CreateCommunity.vue";
import InviteToResource from "./../utils/InviteToResource.vue";
import PropedModal from "./../utils/PropedModal.vue";
// import Event from "./../pages/Event.vue";
import EditEvent from "./../events/EditEvent.vue";
import EditCommunity from "./../communities/EditCommunity.vue";
import SendFriendRequest from "./../friends/SendFriendRequest.vue";
import HandleFriendRequest from "./../friends/HandleFriendRequest.vue";
import CancelFriendRequest from "./../friends/CancelFriendRequest.vue";
import Share from "./../../components/utils/Share.vue";

import { directive as onClickaway } from "vue-clickaway";

export default {
  created() {},
  props: ["current-component", "standalone", "profile", "dob", "to-block"],
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
      event: state => state.events.activeEvent,
      community: state => state.communities.activeCommunity,
      inviteFrom: state => state.ui.inviteFrom
    })
  },
  data() {
    return {};
  },

  methods: {
    updateUser(value) {
      this.$emit("modal:update-user", value);
    },
    closePassword() {
      this.$emit("modal:password-close");
    },
    close() {
      this.$store.dispatch("ui/setActiveModal", null);
    },
    openPhoneVerification() {
      this.currentComponent = "phone-verification";
    },
    openPhoneRegistration() {
      this.currentComponent = "phone-registration";
    },
    activate() {
      this.$emit("modal:activate");
    },
    deactivate() {
      this.$emit("modal:deactivate");
    },
    unfriend() {
      this.$emit("modal:unfriend");
    },
    block() {
      this.$emit("modal:block");
    },
    unblock() {
      this.$emit("modal:unblock");
    },
    leave() {
      this.$emit("modal:leave-event");
    },
    finishInvite() {
      if (this.inviteFrom === "create") {
        this.$store.dispatch("ui/setActiveModal", "proped-modal");
      } else {
        this.$store.dispatch("ui/setActiveModal", null);

        this.$toast.open({
          duration: 5000,
          message: "Invitations have been sent.",
          position: "is-bottom",
          type: "is-success"
        });
      }
    },
    eventUpdated() {
      this.$toast.open({
        duration: 5000,
        message: this.event.title + this.$t("shared.toast.has_been_updated"),
        position: "is-bottom",
        type: "is-success"
      });
    },
    communityUpdated() {
      this.$toast.open({
        duration: 5000,
        message: this.community.name + this.$t("shared.toast.has_been_updated"),
        position: "is-bottom",
        type: "is-success"
      });
    }
  },
  components: {
    PhoneRegistration,
    PhoneVerification,
    DateOfBirth,
    DeactivateAccount,
    DeleteAccount,
    ChangePassword,
    BlockUsers,
    Unfriend,
    Block,
    Unblock,
    LeaveEvent,
    BlockedProfiles,
    RecoverPassword,
    PasswordReset,
    BannedNotification,
    AppealBan,
    LeaveCommunity,
    CreateEvent,
    InviteToResource,
    PropedModal,
    CreateCommunity,
    EditEvent,
    EditCommunity,
    SendFriendRequest,
    HandleFriendRequest,
    CancelFriendRequest,
    Share,
    Description
  }
};
</script>

<style lang="scss" scoped>
</style>
