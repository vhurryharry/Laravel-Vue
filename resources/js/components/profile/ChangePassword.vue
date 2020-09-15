<template>
  <form class="flex flex-col items-center pt-6 pb-2 my-2" @submit.prevent>
    <p
      class="rubik-medium text-2xl text-center mb-4"
    >{{ $t('components.settings.change_password') }}</p>

    <div class="mb-4 w-3/4">
      <label
        class="block text-sm rubik-regular mb-1"
        for="day"
      >{{ $t('components.settings.current_password') }}</label>

      <input
        class="w-full bg-white text-black px-4 py-3 h-12"
        type="password"
        v-model="currentPassword"
        name="currentpassword"
        v-validate="'required|min:8'"
        :class="{'is-danger': errors.has('currentpassword') }"
        data-vv-as="current password"
      />

      <span
        v-show="errors.has('currentpassword')"
        class="help is-danger inline h-1 w-1 whitespace-no-wrap"
      >{{ errors.first('currentpassword') }}</span>
    </div>

    <div class="mb-4 w-3/4">
      <label
        class="block text-sm rubik-regular mb-1"
        for="day"
      >{{ $t('components.settings.new_password') }}</label>

      <input
        class="w-full bg-white text-black px-4 py-3 h-12"
        type="password"
        v-model="password"
        name="password"
        v-validate="'required|min:8'"
        :class="{'is-danger': errors.has('password') }"
        ref="password"
      />
      <span
        v-show="errors.has('password')"
        class="help is-danger inline h-1 w-1 whitespace-no-wrap"
      >{{ errors.first('password') }}</span>
    </div>

    <div class="w-3/4">
      <label
        class="block text-sm rubik-regular mb-1"
        for="day"
      >{{ $t('components.settings.confirm_new_password') }}</label>

      <input
        class="w-full bg-white text-black px-4 py-3 h-12"
        type="password"
        v-model="passwordConfirm"
        name="passwordconfirm"
        v-validate="'required|confirmed:password|min:8'"
        :class="{'is-danger': errors.has('passwordconfirm') }"
        data-vv-as="password"
      />

      <span
        v-show="errors.has('passwordconfirm')"
        class="help is-danger inline h-1 w-1 whitespace-no-wrap"
      >{{ errors.first('passwordconfirm') }}</span>
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
import { mapState, mapMutations, mapActions } from "vuex";

export default {
  props: ["standalone"],
  computed: {
    ...mapState({
      language: state => state.i18n.locale,
      profile: state => state.profiles.signedInProfile
    })
  },
  created() {},
  data() {
    return {
      loading: false,
      currentPassword: null,
      password: null,
      passwordConfirm: null
    };
  },

  methods: {
    update() {
      this.loading = true;

      this.$validator.validateAll().then(async result => {
        if (result) {
          await axios
            .put("/user/settings/updatePassword", {
              current_password: this.currentPassword,
              password: this.password,
              password_confirm: this.passwordConfirm
            })
            .then(response => {
              this.loading = false;

              this.$emit("modal:edit-password:done");
              this.$store.dispatch("ui/setActiveModal", null);

              this.$toast.open({
                duration: 5000,
                message: this.$t("components.settings.toast.password_changed"),
                position: "is-bottom",
                type: "is-success"
              });
            })
            .catch(error => {
              this.loading = false;

              this.$toast.open({
                duration: 5000,
                message: `Error.`,
                position: "is-bottom",
                type: "is-failure"
              });
            });
        } else {
          this.loading = false;
        }
      });
    }
  },

  components: {}
};
</script>


<style lang="scss" scoped>
</style>
