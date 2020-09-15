<template>
  <div class="flex flex-col pt-6 pb-2 my-2 items-center">
    <p class="rubik-regular text-2xl text-center mb-6">Appeal Ban</p>

    <form class="flex flex-col pb-2 w-3/4">
      <textarea
        class="text-sm appearance-none border border-grey-darker rounded-lg w-full py-2 px-3 mb-4 text-grey-darker bg-white"
        name="appealDescription"
        id="appealDescription"
        cols="30"
        rows="6"
        placeholder="Please describe why you are appealing the suspension of your account..."
        v-model="description"
      ></textarea>

      <div class="flex">
        <button
          class="block mx-auto flex raleway-semibold hover:bg-white hover:text-teal text-white border-white border py-2 px-6 rounded"
          type="button"
          @click="close"
        >{{ $t('shared.close') }}</button>

        <button
          class="block mx-auto flex items-center raleway-semibold hover:bg-white hover:text-teal text-white border-white border py-2 px-6 rounded"
          type="button"
          @click="sendAppeal"
        >
          {{ $t('shared.send') }}
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
      language: state => state.i18n.locale,
      user: state => state.profiles.bannedUser
    })
  },
  data() {
    return {
      loading: false,
      description: null
    };
  },
  methods: {
    async sendAppeal() {
      await axios
        .post("/user/appeal", {
          user: this.user,
          description: this.description
        })
        .then(response => {
          this.$store.dispatch("ui/setActiveModal", null);

          this.$toast.open({
            duration: 5000,
            message: "Appeal sent.",
            position: "is-bottom",
            type: "is-success"
          });
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
