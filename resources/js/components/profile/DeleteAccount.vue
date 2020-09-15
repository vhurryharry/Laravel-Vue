<template>
  <form class="flex-col pt-6 pb-2 my-2">
    <p
      class="rubik-medium text-xl text-center mb-4"
    >{{ $t('components.settings.delete_account_title')}}</p>

    <div class="flex justify-center items-center text-center mb-4">
      <p class="text-sm leading-loose">{{ $t('components.settings.delete_account_message')}}</p>
    </div>

    <div class="flex justify-center mt-12">
      <button
        class="block flex items-center raleway-semibold bg-red text-white border-red border py-2 px-6 rounded"
        :class="(language === 'ar') ? 'ml-8' : 'mr-8'"
        type="button"
        @click="deleteAccount"
      >
        {{ $t('shared.delete')}}
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
    async deleteAccount() {
      this.loading = true;

      await axios.post("/user/settings/deleteAccount").then(response => {
        this.loading = false;

        window.location.reload();
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
