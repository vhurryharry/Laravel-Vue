<template>
  <form class="flex flex-col pt-6 pb-2 my-2" @submit.prevent="sendVerificationCode()">
    <p class="rubik-medium text-2xl text-center mb-4">{{ $t('components.register.verify_phone') }}</p>

    <div class="flex justify-center items-center items-end mb-6">
      <div class="w-2/4">
        <label
          class="block text-sm rubik-regular mb-1"
          for="day"
        >{{ $t('components.register.mobile_number') }}</label>

        <multiselect
          class="appearance-none border rounded w-full bg-white"
          v-model="location"
          track-by="iso_3166_2"
          label="name"
          name="location"
          :placeholder="$t('shared.select_one')"
          :options="countries"
          :searchable="true"
          :allow-empty="false"
          :close-on-select="true"
          :multiple="false"
          type="text"
          v-validate="'required'"
          :class="{'is-danger': errors.has('location') }"
        >
          <span
            v-show="errors.has('location')"
            class="help is-danger"
          >{{ errors.first('location') }}</span>

          <template slot="singleLabel" slot-scope="{ option }">
            <p class="text-grey-dark">+{{ option.calling_code }}</p>
          </template>

          <template slot="option" slot-scope="props">
            <div class="option__desc">
              <span class="option__title mr-3">+{{ props.option.calling_code }}</span>
              <span class="option__small">{{ props.option.name }}</span>
            </div>
          </template>
        </multiselect>
      </div>

      <div class="mx-2"></div>

      <div class="flex flex-col w-2/4 mt-5 relative">
        <input
          class="appearance-none border rounded w-full h-10 px-3 text-grey-darker bg-white"
          id="phoneNumber"
          type="text"
          name="phoneNumber"
          placeholder="7687 3566 34"
          v-model="phoneNumber"
          v-validate="'required|numeric'"
          :class="{'is-danger': errors.has('phoneNumber') }"
        />

        <span
          v-show="errors.has('phoneNumber')"
          class="help is-danger inline h-1 w-1 whitespace-no-wrap"
        >{{ errors.first('phoneNumber') }}</span>
      </div>
    </div>

    <button
      class="block mx-auto flex items-center raleway-semibold bg-white hover:bg-white hover:text-teal text-teal-light border py-2 px-6 rounded"
      type="button"
      @click="sendVerificationCode()"
    >
      {{ $t('components.register.send_verification') }}
      <div class="block flex spinner" :class="(language === 'ar') ? 'mr-5' : 'ml-5'" v-if="loading"></div>
    </button>

    <p class="text-xs text-center mt-4">{{ $t('components.register.message_text') }}</p>

    <div class="flex justify-center mt-8">
      <button
        class="raleway-semibold hover:bg-white hover:text-teal text-white border-white border py-2 px-6 rounded"
        :class="(language === 'ar') ? 'mr-2' : 'ml-2'"
        type="button"
        @click="goBackToInitialization()"
      >{{ $t('components.register.back') }}</button>

      <button
        id="codeSentButton"
        class="raleway-semibold text-white border py-2 px-6 rounded bg-grey-dark border-grey-dark cursor-not-allowed"
        :class="(language === 'ar') ? 'mr-2' : 'ml-2'"
        type="button"
        :disabled="!codeSent"
        @click="openPhoneVerification()"
      >{{ $t('components.register.continue') }}</button>
    </div>
  </form>
</template>

<script>
import countriesList from "./../../json/countries.json";
import Multiselect from "vue-multiselect";
import { mapState, mapMutations, mapActions } from "vuex";

export default {
  props: ["username", "profile"],
  computed: {
    ...mapState({
      language: state => state.i18n.locale
    })
  },
  activated() {
    this.codeSent = false;
  },
  async created() {
    let that = this;

    await axios.get("/countries").then(response => {
      _.forEach(response.data.countries, (value, key) => {
        value.name = value.name[that.language];
        that.countries.push(value);
      });
    });
  },
  watch: {
    codeSent(value) {
      if (value) {
        $("#codeSentButton").removeClass(
          "bg-grey-dark border-grey-dark cursor-not-allowed"
        );
        $("#codeSentButton").addClass("hover:bg-white hover:text-teal");
      } else {
        $("#codeSentButton").addClass(
          "bg-grey-dark border-grey-dark cursor-not-allowed"
        );
        $("#codeSentButton").removeClass("hover:bg-white hover:text-teal");
      }
    }
  },
  data() {
    return {
      loading: false,

      location: "Choose",
      phoneNumber: null,
      countries: [],
      codeSent: false
    };
  },
  methods: {
    sendVerificationCode() {
      this.loading = true;

      this.$validator.validateAll().then(async result => {
        if (result) {
          await axios
            .post("/user/sendVerificationCode", {
              phone_number: "+" + this.location.calling_code + this.phoneNumber,
              username: this.profile ? this.profile.username : this.username
            })
            .then(response => {
              this.loading = false;
              this.codeSent = true;

              this.$toast.open({
                duration: 5000,
                message: this.$t(
                  "components.settings.toast.verification_code_sent"
                ),
                position: "is-bottom",
                type: "is-success"
              });
            })
            .catch(error => {
              this.loading = false;

              this.$toast.open({
                duration: 5000,
                message: error.response.data.message,
                position: "is-bottom",
                type: "is-warning"
              });
            });
        }

        this.loading = false;
      });
    },
    goBackToInitialization() {
      this.$emit("initialization:open");
    },
    openPhoneVerification() {
      this.$emit("phone-registration:open-verification");
    }
  },
  components: {
    Multiselect
  }
};
</script>

<style lang="scss" scoped>
</style>
