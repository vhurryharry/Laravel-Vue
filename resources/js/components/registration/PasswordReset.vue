<template>
  <div class="flex flex-col pt-6 pb-2 my-2 items-center">
    <p class="rubik-regular text-2xl text-center mb-6">Password Reset</p>

    <form class="flex flex-col pb-2 w-1/2" @submit.prevent="ResetPassword()">
      <div class="mb-4">
        <input
          v-model="email"
          class="raleway-regular text-black text-sm w-full p-3 bg-white"
          type="email"
          placeholder="Email"
          name="email"
          v-validate="'required|email'"
          :class="{'is-danger': errors.has('email') }"
        />

        <span
          v-show="errors.has('email')"
          class="help is-danger inline h-1 w-1 whitespace-no-wrap"
        >{{ errors.first('email') }}</span>
      </div>

      <div class="mb-4">
        <input
          class="raleway-regular text-black text-sm w-full p-3 bg-white"
          id="password"
          type="password"
          placeholder="Password"
          v-model="password"
          name="password"
          v-validate="'required|min:8'"
          :class="{'is-danger': errors.has('password') }"
        />

        <span
          v-show="errors.has('password')"
          class="help is-danger inline h-1 w-1 whitespace-no-wrap"
        >{{ errors.first('password') }}</span>
      </div>

      <div class="mb-4">
        <input
          class="raleway-regular text-black text-sm w-full p-3 bg-white"
          id="confirmPassword"
          type="password"
          placeholder="Confirm password"
          v-model="password_confirmation"
          name="password_confirmation"
          v-validate="'required|min:8'"
          :class="{'is-danger': errors.has('password_confirmation') }"
        />

        <span
          v-show="errors.has('password_confirmation')"
          class="help is-danger inline h-1 w-1 whitespace-no-wrap"
        >{{ errors.first('password_confirmation') }}</span>
      </div>

      <div class="flex">
        <button
          class="block mx-auto flex raleway-semibold hover:bg-white hover:text-teal text-white border-white border py-2 px-6 rounded"
          type="button"
          @click="ResetPassword"
        >
          {{ $t('shared.send')}}
          <div class="block flex ml-5 spinner" v-if="loading"></div>
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
  mounted() {
    let url = new URL(location.href);
    this.email = url.searchParams.get("email");
    this.token = url.searchParams.get("token");
  },
  data() {
    return {
      token: null,
      password: null,
      password_confirmation: null,
      loading: false,
      email: null
    };
  },
  methods: {
    ResetPassword() {
      this.loading = true;

      this.$validator.validateAll().then(async result => {
        if (result) {
          await axios
            .post("/password/reset", {
              email: this.email,
              password: this.password,
              password_confirmation: this.password_confirmation,
              token: this.token
            })
            .then(response => {
              this.$toast.open({
                duration: 5000,
                message: "Password has been reset. Please log in.",
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
      });
      this.loading = false;
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
