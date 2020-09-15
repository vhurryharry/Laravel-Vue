<template>
  <form class="flex-col pt-6 pb-2 my-2">
    <p class="rubik-medium text-xl text-center">{{ targetProfile.name }} sent you a friend request.</p>

    <div class="flex flex-col justify-center items-center mt-4">
      <img
        class="mx-auto rounded-full h-24 w-24 border border-grey-dark mb-6"
        :src="'/storage/images/' + targetProfile.image_path"
        alt
      />

      <!-- <p class="text-center text-sm my-4 mb-6">
        {{ targetProfile.name }} will be able to see your full
        name, see your Meet and Mingle profile,
        Person
        and send you direct image
        messages outside of the event.
      </p>-->

      <div class="flex">
        <button
          class="block mx-auto flex items-center raleway-semibold hover:bg-green hover:border-green text-white border-white border py-2 px-6 rounded"
          :class="(language === 'ar') ? 'ml-8' : 'mr-8'"
          type="button"
          @click="sendFriendRequest"
        >
          Accept
          <div
            class="block flex spinner"
            :class="(language === 'ar') ? 'mr-5' : 'ml-5'"
            v-if="loading"
          ></div>
        </button>

        <button
          class="raleway-semibold hover:bg-red hover:border-red text-white border-white border py-2 px-6 rounded"
          :class="(language === 'ar') ? 'mr-8' : 'ml-8'"
          type="button"
          @click="close"
        >Refuse</button>
      </div>
    </div>
  </form>
</template>

<script>
import { mapState, mapMutations, mapActions } from "vuex";

export default {
  computed: {
    ...mapState({
      signedInProfile: state => state.profiles.signedInProfile,
      event: state => state.events.activeEvent,
      language: state => state.i18n.locale,
      targetProfile: state => state.profiles.targetProfile
    })
  },
  data() {
    return {
      loading: false
    };
  },

  methods: {
    async sendFriendRequest() {
      this.loading = true;

      await axios
        .post("/profile/" + this.targetProfile.username, {
          username: this.targetProfile.username
        })
        .then(response => {
          // this.$emit("friend-request-sent", profileId);
          this.$store.dispatch("ui/setActiveModal", null);

          this.$toast.open({
            duration: 5000,
            message: "Friend request sent.",
            position: "is-bottom",
            type: "is-success"
          });
        })
        .catch(error => {
          this.finishLoading = false;
        });
    },
    close() {
      this.$store.dispatch("ui/setActiveModal", null);
    }
  },

  components: {}
};
</script>

<style lang="scss" scoped>
</style>
