<template>
  <div class="flex flex-col pt-6 pb-2 my-2 items-center">
    <p class="rubik-regular text-2xl text-center mb-6">
      Several users have reported you for inappropriate behav-
      ior, so your account has been suspended for ___ days.
    </p>

    <p class="raleway-regular mb-4 text-center">
      Feel free to review the website’s Terms and Conditions to read about what
      behaviors are not permitted on the Meet and Mingle website. If you feel that
      your account has wrongfully been suspended, please click the Appeal
      button below to request to request that your account be reactivated and an
      administrator will review your request as soon as possible.
    </p>

    <form class="flex flex-col pb-2 w-1/2">
      <div class="flex">
        <button
          class="block mx-auto flex raleway-semibold hover:bg-white hover:text-teal text-white border-white border py-2 px-6 rounded"
          type="button"
          @click="close"
        >{{ $t('shared.close') }}</button>

        <button
          class="block mx-auto flex items-center raleway-semibold hover:bg-white hover:text-teal text-white border-white border py-2 px-6 rounded"
          type="button"
          @click="openAppealModal"
        >
          {{ $t('components.profiles.appeal') }}
          <div
            class="block flex spinner"
            :class="(language === 'ar') ? 'mr-5' : 'ml-5'"
            v-if="loading"
          ></div>
        </button>
      </div>
    </form>
  </div>
</template>

<script>
import { mapState, mapMutations, mapActions } from "vuex";

export default {
  computed: {
    ...mapState({
      language: state => state.i18n.locale
    })
  },
  data() {
    return {
      loading: false
    };
  },
  methods: {
    openAppealModal() {
      this.$store.dispatch("ui/setActiveModal", "AppealBan");
    },
    close() {
      this.$emit("modal:close");
    }
  },
  components: {}
};
</script>

<style lang="scss" scoped>
</style>
