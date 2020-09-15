<template>
  <div class="fixed pin flex items-center">
    <div class="fixed pin bg-black opacity-50 z-10"></div>

    <div class="relative mx-6 md:mx-auto w-full md:w-1/2 lg:w-1/3 z-20 m-8">
      <div class="shadow-lg bg-teal-lighter rounded-lg p-8">
        <div class="flex justify-end">
          <button @click="close()">
            <img class="invert-icon w-4" src="/svgs/close.svg" alt />
          </button>
        </div>

        <img class="block mx-auto invert-icon" src="/svgs/logo_flat.svg" alt />
        <keep-alive>
          <registration-initialization
            v-if="initialization"
            v-on:initialization:close="openPhoneRegistration"
          ></registration-initialization>

          <phone-registration
            v-if="phoneRegistration"
            :username="username"
            v-on:initialization:open="openInitialization()"
            v-on:phone-registration:open-verification="openPhoneVerification()"
          ></phone-registration>

          <phone-verification
            v-if="PhoneVerification"
            :username="username"
            v-on:phone-registration:open-registration="openPhoneRegistration"
          ></phone-verification>
        </keep-alive>
      </div>
    </div>
  </div>
</template>

<script>
import RegistrationInitialization from "./registration/RegistrationInitialization.vue";
import PhoneRegistration from "./registration/PhoneRegistration.vue";
import PhoneVerification from "./registration/PhoneVerification.vue";

import { mapState, mapMutations, mapActions } from "vuex";

export default {
  computed: {
    ...mapState({
      language: state => state.i18n.locale
    })
  },
  data() {
    return {
      username: null,
      initialization: true,
      phoneRegistration: false,
      PhoneVerification: false
    };
  },
  methods: {
    close() {
      this.$emit("modal:close");
    },
    openPhoneRegistration(value) {
      this.username = value;
      this.initialization = false;
      this.phoneRegistration = true;
      this.PhoneVerification = false;
    },
    openPhoneVerification() {
      this.initialization = false;
      this.phoneRegistration = false;
      this.PhoneVerification = true;
    },
    openInitialization() {
      this.initialization = true;
      this.phoneRegistration = false;
      this.PhoneVerification = false;
    }
  },
  components: {
    RegistrationInitialization,
    PhoneRegistration,
    PhoneVerification
  }
};
</script>

<style lang="scss" scoped>
</style>
