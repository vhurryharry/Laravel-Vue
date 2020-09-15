<template>
  <div class="flex flex-col pt-6 pb-2 my-2 items-center">
    <p
      class="rubik-regular text-2xl text-center mb-6"
    >{{ $t('components.settings.recover_password') }}</p>

    <form class="flex flex-col pb-2 w-1/2" @submit.prevent="sendEmail()">
      <div class="mb-4">
        <input
          class="raleway-regular appearance-none border rounded w-full py-2 px-3 text-grey-darker bg-white"
          name="email"
          type="text"
          v-model="email"
          v-validate="'required|email'"
          :class="{'is-danger': errors.has('email') }"
          :placeholder="$t('components.register.email')"
        />

        <span v-show="errors.has('email')" class="help is-danger">{{ errors.first('email') }}</span>
      </div>

      <div class="flex items-center">
        <a class="mx-auto" href="#">
          <button
            class="flex block raleway-semibold hover:bg-white hover:text-teal text-white border-white border py-2 px-6 rounded"
            type="button"
            @click="sendEmail"
          >
            {{ $t('shared.send')}}
            <div class="block flex ml-5 spinner" v-if="loading"></div>
          </button>
        </a>
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
      loading: false,
      email: null
    };
  },
  methods: {
    sendEmail() {
      this.loading = true;

      this.$validator.validateAll().then(async result => {
        if (result) {
          await axios
            .post("/password/email", {
              email: this.email
            })
            .then(response => {
              this.$toast.open({
                duration: 5000,
                message: this.$t(
                  "components.settings.toast.password_reset_email_sent"
                ),
                position: "is-bottom",
                type: "is-success"
              });

              this.$store.dispatch("ui/setActiveModal", null);
            })
            .catch(error => {
              this.$toast.open({
                duration: 5000,
                message: error.response.data.message,
                position: "is-bottom",
                type: "is-danger"
              });
            });
        }
        this.loading = false;
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
