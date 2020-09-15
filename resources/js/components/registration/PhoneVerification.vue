<template>
  <form class="flex flex-col pt-6 pb-2 my-1" @submit.prevent="verifyCode()">
    <p class="rubik-medium text-2xl text-center mb-4">{{ $t('components.register.verify_phone')}}</p>

    <p class="text-sm text-center">{{ $t('components.register.receive_text')}}</p>

    <p class="text-sm text-center mb-4">{{ $t('components.register.enter_authentication')}}</p>
    <div class="mx-auto mb-8">
      <input
        class="appearance-none border rounded py-2 px-3 text-grey-darker bg-white"
        type="text"
        name="verificationCode"
        v-model="verificationCode"
        v-validate="'required|numeric'"
        :class="{'is-danger': errors.has('verificationCode') }"
      />
      <br />
      <span
        v-show="errors.has('verificationCode')"
        class="help is-danger inline h-1 w-1 whitespace-no-wrap"
      >{{ errors.first('verificationCode') }}</span>
    </div>
    <div class="flex justify-center">
      <button
        class="raleway-semibold text-sm hover:bg-white hover:text-teal text-white border-white border py-2 px-6 rounded"
        :class="(language === 'ar') ? 'mr-2' : 'ml-2'"
        type="button"
        @click="goBackToPhoneRegistration()"
      >{{ $t('components.register.back')}}</button>

      <button
        class="raleway-semibold hover:bg-white text-sm hover:text-teal text-white border-white border py-2 px-6 rounded"
        :class="(language === 'ar') ? 'mr-2' : 'ml-2'"
        type="button"
        @click="goBackToPhoneRegistration()"
      >{{ $t('components.register.resend')}}</button>

      <button
        class="block flex raleway-semibold bg-white text-sm text-teal-light hover:bg-white hover:text-teal text-white border-white border py-2 px-6 rounded"
        :class="(language === 'ar') ? 'mr-2' : 'ml-2'"
        type="button"
        @click="verifyCode()"
      >
        {{ $t('components.register.verify')}}
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
import vSelect from "vue-select";
import { mapState, mapMutations, mapActions } from "vuex";

export default {
  props: ["username"],
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
      verificationCode: null,
      countryCode: "+1",
      options: ["Lebanon", "Syria", "Jordan"]
    };
  },

  methods: {
    verifyCode() {
      this.loading = true;

      this.$validator.validateAll().then(async result => {
        if (result) {
          await axios
            .post("/user/verifyCode", {
              verification_code: this.verificationCode,
              username: this.username
            })
            .then(response => {
              this.loading = false;
              this.codeSent = true;

              if (!this.profile) {
                window.location.href = "/events";
              } else {
                this.$emit("modal:update-user", response.data.user);

                this.$toast.open({
                  duration: 5000,
                  message: `Phone number and account verified.`,
                  position: "is-bottom",
                  type: "is-success"
                });
              }
            })
            .catch(error => {
              this.loading = false;

              this.$toast.open({
                duration: 5000,
                message: this.$t("components.settings.toast.code_error"),
                position: "is-bottom",
                type: "is-warning"
              });
            });
        }
      });
      this.loading = false;
    },
    goBackToPhoneRegistration() {
      this.$emit("phone-registration:open-registration", this.username);
    }
  },
  components: {
    vSelect
  }
};
</script>

<style lang="scss" scoped>
</style>
