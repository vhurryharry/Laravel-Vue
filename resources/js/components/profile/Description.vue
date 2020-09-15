<template>
  <form class="flex-col pt-6 pb-2 my-2">
    <p class="rubik-medium text-2xl text-center mb-4">{{ $t('components.settings.about_you') }}</p>

    <div class="flex justify-center items-end mb-4">
      <textarea
        v-model="bio"
        class="rounded p-2 w-3/4"
        name
        id
        cols="30"
        rows="10"
        :placeholder="$t('components.settings.enter_description')"
      ></textarea>
    </div>

    <div class="flex justify-center mt-12">
      <button
        class="block flex items-center raleway-semibold hover:bg-white hover:text-teal text-white border-white border py-2 px-6 rounded"
        :class="(language === 'ar') ? 'mr-2' : 'ml-2'"
        type="button"
        @click="update()"
      >
        {{ $t('shared.update') }}
        <div
          class="block flex spinner"
          :class="(language === 'ar') ? 'mr-5' : 'ml-5'"
          v-if="loading"
        ></div>
      </button>
    </div>
  </form>
</template>

<script>
import moment from "moment";
import { mapState, mapMutations, mapActions } from "vuex";

export default {
  props: ["standalone", "dob"],
  computed: {
    ...mapState({
      language: state => state.i18n.locale,
      profile: state => state.profiles.signedInProfile
    })
  },
  data() {
    return {
      loading: false,
      bio: null
    };
  },
  created() {
    this.bio = profile.bio;
  },
  methods: {
    async update() {
      this.loading = true;

      this.$store
        .dispatch("profiles/updateBio", {
          bio: this.bio
        })
        .then(response => {
          this.loading = false;

          this.$toast.open({
            duration: 5000,
            message: this.$t("components.settings.toast.date_of_birth_updated"),
            position: "is-bottom",
            type: "is-success"
          });

          this.$emit("modal:close");
        });
    }
  },

  components: {
    moment
  }
};
</script>


<style lang="scss" scoped>
</style>
