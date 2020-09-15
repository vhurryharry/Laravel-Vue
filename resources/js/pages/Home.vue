<template>
  <div class="flex flex-col flex-wrap page px-24 pt-24 w-full h-full bg-teal">
    <div class="flex flex-1 px-24">
      <div class="w-1/2">
        <div class>
          <h1
            class="raleway-bold text-6xl leading-none mb-12"
            style
          >{{ $t('components.home.welcome') }}</h1>

          <h2 class="raleway-bold text-xl mb-6">{{ $t('components.home.welcome_2') }}</h2>

          <ul class="raleway-bold pl-4">
            <li class="mb-3">
              <h4 class>{{ $t('components.home.welcome_3') }}</h4>
            </li>

            <li class="mb-3">
              <h4 class>{{ $t('components.home.welcome_4') }}</h4>
            </li>

            <li class="mb-3">
              <h4 class>{{ $t('components.home.welcome_5') }}</h4>
            </li>
          </ul>
        </div>
      </div>

      <div class="w-1/2 flex justify-end">
        <div class="w-1/2 float-left">
          <p class="raleway-regular text-xl mb-6">{{ $t('components.home.welcome_6') }}</p>

          <p class="raleway-regular text-sm mb-4">
            {{ $t('components.home.welcome_7') }}
            <span
              class="raleway-medium underline cursor-pointer"
              @click="registrationModalOpen = true"
            >{{ $t('components.home.welcome_8') }}</span>
            {{ $t('components.home.welcome_9') }}
          </p>

          <form @submit.prevent="login()">
            <div class="mb-4">
              <input
                class="raleway-regular text-black text-sm w-full p-3 bg-white border border-grey"
                name="email"
                type="text"
                v-model="email"
                v-validate="'required|email'"
                :class="{'is-danger': errors.has('email') }"
                :placeholder="$t('components.home.welcome_22')"
              />

              <span v-show="errors.has('email')" class="help is-danger">{{ errors.first('email') }}</span>
            </div>

            <div class="mb-4">
              <input
                class="raleway-regular text-black text-sm w-full p-3 bg-white border border-grey"
                name="password"
                type="password"
                ref="password"
                :placeholder="$t('components.home.welcome_23')"
                v-model="password"
                v-validate="'required|min:8'"
                :class="{'is-danger': errors.has('password') }"
              />

              <span
                v-show="errors.has('password')"
                class="help is-danger"
              >{{ errors.first('password') }}</span>
            </div>

            <b-checkbox
              class="raleway-regular text-sm mb-4"
              v-model="rememberMe"
            >{{ $t('components.home.welcome_21') }}</b-checkbox>

            <div class="flex items-center mb-4">
              <a class href="#">
                <button
                  class="flex block raleway-semibold hover:bg-transparent hover:text-white border border-white block bg-white text-teal px-6 py-3"
                  type="submit"
                  @click="login()"
                >
                  {{ $t('components.home.welcome_10') }}
                  <div class="block flex ml-5 spinner" v-if="loading"></div>
                </button>
              </a>

              <p
                class="raleway-medium text-sm underline mx-8 cursor-pointer"
                @click="openModal('RecoverPassword')"
              >{{ $t('components.home.welcome_11') }}</p>
            </div>
          </form>

          <p class="raleway-regular text-sm mb-4">{{ $t('components.home.welcome_12') }}</p>

          <a href="auth/facebook" class="btn btn-lg btn-primary btn-block">
            <button
              class="raleway-semibold hover:bg-transparent hover:text-white border border-white bg-white text-teal px-6 py-3"
            >{{ $t('components.home.welcome_13') }}</button>
          </a>
        </div>
      </div>
    </div>

    <home-footer></home-footer>

    <transition name="fade">
      <registration-component
        v-if="registrationModalOpen"
        v-on:modal:close="registrationModalOpen = false"
      ></registration-component>

      <modal
        v-if="activeModal"
        :current-component="activeModal"
        :standalone="true"
        v-on:modal:close="closeModal($event)"
        v-on:modal:leave-event="event.member = false"
      ></modal>
    </transition>
  </div>
</template>

<script>
import { mapState, mapMutations, mapActions } from "vuex";

export default {
  props: ["banned_user"],
  computed: {
    ...mapState({
      language: state => state.i18n.locale,
      activeModal: state => state.ui.activeModal
    })
  },
  data() {
    return {
      isDisabled: true,
      modalOpen: false,
      modalName: "",
      loading: false,
      registrationModalOpen: false,
      rememberMe: false,
      email: "",
      password: "",
      formError: {
        email: "",
        password: ""
      }
    };
  },
  watch: {},
  created() {},
  mounted() {
    let url = new URL(location.href);
    let token = url.searchParams.get("token");
    let email = url.searchParams.get("email");

    if (token && email) {
      this.$store.dispatch("ui/setActiveModal", "PasswordReset");
    }

    if (this.banned_user) {
      this.$store.dispatch("profiles/setBannedUser", data.user);
      this.$store.dispatch("ui/setActiveModal", "BannedNotification");
    }
  },
  methods: {
    openModal(name) {
      this.$store.dispatch("ui/setActiveModal", name);
    },
    closeModal() {
      this.$store.dispatch("ui/setActiveModal", null);
    },
    login() {
      this.loading = true;

      this.$validator.validateAll().then(async result => {
        if (result) {
          let user = await axios
            .post("/user/login", {
              email: this.email,
              password: this.password,
              remember_me: this.rememberMe
            })
            .then(response => {
              this.loading = false;
              window.location.href = "/events";
            })
            .catch(error => {
              this.loading = false;

              let data = error.response.data;
              if (data.user && data.user.banned) {
                this.$store.dispatch("profiles/setBannedUser", data.user);
                this.$store.dispatch("ui/setActiveModal", "BannedNotification");
              } else {
                this.$toast.open({
                  duration: 5000,
                  message: "Login error.",
                  position: "is-bottom",
                  type: "is-warning"
                });
              }
            });
        }
        this.loading = false;
      });
    }
  },
  components: {}
};
</script>

<style scoped lang="scss">
.page {
  color: white;
  min-height: 90vh;
  background: url(/images/login_background.png);
  background-repeat: no-repeat;
  background-size: cover;
}
</style>
