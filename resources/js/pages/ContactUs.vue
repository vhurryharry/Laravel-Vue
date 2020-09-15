<template>
  <div class="flex flex-col page w-full px-24 justify-center items-center">
    <div class="flex flex-col flex-1 w-1/3 items-start justify-center">
      <p class="text-2xl mb-6">{{ $t('components.contact_us.contact_us') }}</p>

      <p class="mb-4">{{ $t('components.contact_us.contact_us_description') }}</p>

      <form class="w-full" @submit.prevent="submit()">
        <input
          class
          style="height:0;width:0;z-index:-1;display:none;opacity:0;"
          v-model="tempField"
          type="text"
        />
        <div class="mb-4">
          <input
            class="raleway-regular text-black text-sm w-full p-3 bg-white"
            type="text"
            :placeholder="$t('shared.name')"
            name="name"
            v-model="name"
            v-validate="'required'"
            :class="{'is-danger': errors.has('name') }"
          />

          <span
            v-show="errors.has('name')"
            class="help is-danger inline h-1 w-1 whitespace-no-wrap"
          >{{ errors.first('name') }}</span>
        </div>

        <div class="mb-4">
          <input
            class="raleway-regular text-black text-sm w-full p-3 bg-white"
            type="email"
            :placeholder="$t('components.register.email')"
            name="email"
            v-model="email"
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
            class="bg-white w-full h-24 p-3"
            type="text"
            name="description"
            :placeholder="$t('components.contact_us.how_can_we_help')"
            v-model="description"
            v-validate="'required'"
            :class="{'is-danger': errors.has('description') }"
          />

          <span
            v-show="errors.has('description')"
            class="help is-danger inline h-1 w-1 whitespace-no-wrap"
          >{{ errors.first('description') }}</span>
        </div>

        <b-checkbox
          class="raleway-regular text-sm mb-6 bg-teal-light p-3 rounded"
          v-model="rememberMe"
        >{{ $t('components.contact_us.how_can_we_help') }}</b-checkbox>

        <button
          class="flex block raleway-semibold hover:bg-white hover:text-teal text-white border-white border py-2 px-6 rounded"
          type="button"
          @click="submit()"
        >
          {{ $t('shared.submit')}}
          <div class="block flex ml-5 spinner" v-if="loading"></div>
        </button>
      </form>
    </div>

    <home-footer></home-footer>
  </div>
</template>

<script>
import { VueReCaptcha } from "vue-recaptcha-v3";

export default {
  mounted() {},

  data() {
    return {
      loading: false,
      name: "",
      email: "",
      description: "",
      rememberMe: false,
      tempField: null
    };
  },
  methods: {
    submit() {
      this.loading = true;

      this.$validator.validateAll().then(async result => {
        if (result && this.tempField === null) {
          await axios
            .post("/contact-us", {
              name: this.name,
              email: this.email,
              description: this.description
            })
            .then(response => {
              this.loading = false;
            })
            .catch(error => {
              this.loading = false;
            });
        } else {
          this.loading = false;
        }
      });
    },
    async recaptcha() {
      // (optional) Wait until recaptcha has been loaded.
      await this.$recaptchaLoaded();

      // Execute reCAPTCHA with action "login".
      const token = await this.$recaptcha("login");

      // Do stuff with the received token.
      // console.log(token);

      this.submit();
    }
  },
  components: {
    VueReCaptcha: {
      siteKey: process.env.MIX_RECAPTCHA_SITE_KEY,
      loaderOptions: {
        useRecaptchaNet: true
      }
    }
  }
};
</script>

<style lang="scss" scoped>
.page {
  color: white;
  min-height: 90vh;
  background: url(/images/login_background.png);
  background-repeat: no-repeat;
  background-size: cover;
}
</style>
