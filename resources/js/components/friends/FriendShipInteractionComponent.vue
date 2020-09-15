<template>
  <div
    class="flex justify-end items-center cursor-default"
    v-if="signedInProfile.id !== profile.id"
  >
    <!-- <img
      class="icon-teal w-8 cursor-pointer"
      src="/svgs/add_profile.svg"
      alt
      @click="sendFriendRequest(profile.username, profile.id)"
      v-if="!profile.friend_request_sent && !profile.has_friend_request_from && !profile.is_friend"
    />-->

    <font-awesome-icon
      class="icon text-teal cursor-pointer hover:text-teal-light"
      :class="(language === 'ar') ? 'mr-1' : 'ml-1'"
      :icon="['fa', 'user-plus']"
      size="lg"
      v-if="!profile.friend_request_sent && !profile.has_friend_request_from && !profile.is_friend"
      @click="sendFriendRequest(profile.username, profile.id)"
    />

    <span class="flex" v-else-if="profile.has_friend_request_from">
      <b-tooltip
        class
        :class="(language === 'ar') ? 'mr-auto' : 'ml-auto'"
        :label="$t('shared.accept_friend_request')"
        position="is-top"
        animated
      >
        <font-awesome-icon
          class="icon text-teal-light cursor-pointer hover:text-teal"
          :class="(language === 'ar') ? 'ml-2' : 'mr-2'"
          :icon="['fa', 'check']"
          size="lg"
          @click="acceptRequest(profile.username, profile.id)"
        />

        <!-- <img
          class="icon-teal w-6 mr-2 cursor-pointer"
          src="/svgs/check.svg"
          alt
          @click="acceptFriendRequest(profile.username, profile.id)"
        />-->
      </b-tooltip>

      <b-tooltip
        class
        :class="(language === 'ar') ? 'mr-auto' : 'ml-auto'"
        :label="$t('shared.refuse_friend_request')"
        position="is-top"
        animated
      >
        <font-awesome-icon
          class="icon text-red cursor-pointer hover:text-black"
          :icon="['fa', 'times']"
          size="lg"
          @click="refuseRequest(profile.username, profile.id)"
        />
        <!-- <img
          class="icon-grey w-4 cursor-pointer"
          src="/svgs/close.svg"
          alt
          @click="refuseFriendRequest(profile.username, profile.id)"
        />-->
      </b-tooltip>
    </span>

    <span class="flex" v-if="profile.friend_request_sent">
      <button
        class="text-sm bg-teal-light flex text-white border px-2 py-2 border-teal-light hover:bg-white hover:text-teal"
      >{{ $t('shared.pending') }}</button>
    </span>

    <a :href="'/chat/' + profile.username">
      <font-awesome-icon
        class="icon text-teal-light cursor-pointer hover:text-teal"
        :icon="['fa', 'comment']"
        size="lg"
        v-if="profile.is_friend"
      />

      <!-- <img class="icon-teal w-6 cursor-pointer" src="/svgs/chat.svg" alt v-if="profile.is_friend" /> -->
    </a>
  </div>
</template>

<script>
import { mapState, mapMutations, mapActions } from "vuex";

export default {
  props: ["tempprofile"],
  computed: {
    ...mapState({
      signedInProfile: state => state.profiles.signedInProfile,
      language: state => state.i18n.locale
    })
  },
  mounted() {
    this.profile = this.tempprofile;
  },
  data() {
    return {
      profile: {}
    };
  },
  methods: {
    sendFriendRequest(to, profileId) {
      axios
        .post("/profile/" + to, {
          username: to
        })
        .then(response => {
          this.$emit("friend-request-sent", profileId);

          this.$toast.open({
            duration: 5000,
            message: "Friend request sent.",
            position: "is-bottom",
            type: "is-success"
          });
        })
        .catch(error => {});
    },
    acceptFriendRequest(to, profileId) {
      axios
        .post("/requests/accept", {
          username: to
        })
        .then(response => {
          this.$emit("friend-request-accepted", profileId);

          this.$toast.open({
            duration: 5000,
            message: `Friend request accepted.`,
            position: "is-bottom",
            type: "is-success"
          });
        })
        .catch(error => {});
    },
    refuseFriendRequest(to, profileId) {
      axios
        .post("/requests/refuse", {
          username: to
        })
        .then(response => {
          this.$emit("friend-request-refused", profileId);

          this.$toast.open({
            duration: 5000,
            message: `Friend request refused.`,
            position: "is-bottom",
            type: "is-warning"
          });
        })
        .catch(error => {});
    }
  }
};
</script>

<style lang="scss" scoped>
</style>
