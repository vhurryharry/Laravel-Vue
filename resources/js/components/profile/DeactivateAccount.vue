<template>
  <form class="flex-col pt-6 pb-2 my-2">
    <p
      class="rubik-medium text-xl text-center mb-4"
      v-if="profile.active"
    >{{ $t('components.settings.deactivate_account_title')}}</p>

    <div class="flex justify-center items-center text-center mb-4">
      <p
        class="text-sm leading-loose"
        v-if="profile.active"
      >{{ $t('components.settings.deactivate_account_message')}}</p>
    </div>

    <div class="flex justify-center mr-12">
      <button
        class="block flex items-center raleway-semibold hover:bg-white hover:text-teal text-white border-white border py-2 px-6 rounded"
        :class="(language === 'ar') ? 'ml-8' : 'mr-8'"
        type="button"
        @click="deactivate"
        v-if="profile.active"
      >
        {{ $t('components.settings.deactivate')}}
        <div
          class="block flex spinner"
          :class="(language === 'ar') ? 'mr-5' : 'ml-5'"
          v-if="loading"
        ></div>
      </button>

      <button
        class="block flex items-center raleway-semibold hover:bg-white hover:text-teal text-white border-white border py-2 px-6 rounded"
        :class="(language === 'ar') ? 'ml-8' : 'mr-8'"
        type="button"
        @click="activate"
        v-else
      >
        {{ $t('components.settings.activate')}}
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
  props: ["standalone"],
  computed: {
    ...mapState({
      profile: state => state.profiles.signedInProfile,
      language: state => state.i18n.locale
    })
  },
  data() {
    return {
      loading: false
    };
  },
  methods: {
    activate() {
      this.loading = true;

      this.$store.dispatch("profiles/activateAccount").then(response => {
        this.loading = false;

        this.$emit("modal:activate", false);

        this.$toast.open({
          duration: 5000,
          message: this.$t("components.settings.toast.account_activated"),
          position: "is-bottom",
          type: "is-success"
        });

        this.$emit("modal:close");
      });
    },
    deactivate() {
      this.loading = true;

      this.$store.dispatch("profiles/deactivateAccount").then(response => {
        this.loading = false;

        this.$emit("modal:deactivate", false);

        this.$toast.open({
          duration: 5000,
          message: this.$t("components.settings.toast.account_deactivated"),
          position: "is-bottom",
          type: "is-success"
        });

        this.$emit("modal:close");
      });
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
