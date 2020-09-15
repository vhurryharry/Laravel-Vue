<template>
  <form class="flex-col pt-6 pb-2 my-2">
    <p
      class="rubik-medium text-xl text-center mb-4"
    >{{ $t('components.profiles.are_you_sure_you_want_to_unblock')}} {{ targetProfile.name }}</p>

    <div class="flex justify-center mt-12">
      <button
        class="block flex items-center raleway-semibold bg-red text-white border-red border py-2 px-6 rounded"
        :class="(language === 'ar') ? 'ml-8' : 'mr-8'"
        type="button"
        @click="unblock"
      >
        {{ $t('components.profiles.unblock')}}
        <div
          class="block flex spinner"
          :class="(language === 'ar') ? 'mr-5' : 'ml-5'"
          v-if="loading"
        ></div>
      </button>

      <button
        class="raleway-semibold hover:bg-white hover:text-teal text-white border-white border py-2 px-6 rounded"
        :class="(language === 'ar') ? 'mr-8' : 'ml-8'"
        type="button"
        @click="close"
      >{{ $t('shared.cancel')}}</button>
    </div>
  </form>
</template>

<script>
import { mapState, mapMutations, mapActions } from "vuex";

export default {
  props: ["standalone", "to-block"],
  computed: {
    ...mapState({
      language: state => state.i18n.locale,
      profile: state => state.profiles.signedInProfile,
      targetProfile: state => state.profiles.targetProfile

    })
  },
  data() {
    return {
      loading: false
    };
  },

  methods: {
    async unblock() {
      this.loading = true;

      await axios
        .post("/profile/" + this.targetProfile.username + "/unblock", {
          username: this.targetProfile.username
        })
        .then(response => {
          this.$store.dispatch("ui/setActiveModal", null);
          this.$emit("modal:unblock");

          this.$toast.open({
            duration: 5000,
            message: this.targetProfile.name + ` unblocked.`,
            position: "is-bottom",
            type: "is-success"
          });
        })
        .catch(error => {
          this.loading = false;
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
