<template>
  <div class="w-3/4 h-full p-8 text-black pt-16 text-base">
    <div class="w-full px-4 py-4 border-t flex justify-between items-center">
      <p class="w-2/5 text-grey-dark">{{ $t('components.settings.location') }}</p>

      <m-select
        :valueToEdit.sync="location"
        placeholder="Location"
        :options="countries"
        :multiple="false"
        :trackBy="'iso_3166_2'"
        :label="'name'"
        :resource="'location'"
        v-on:input:update-value="updateField($event, 'location')"
      ></m-select>
    </div>

    <div class="w-full px-4 py-4 border-t flex justify-between items-center">
      <p class="w-2/5 text-grey-dark">{{ $t('components.settings.education') }}</p>

      <m-multi-select
        :valueToEdit="education"
        placeholder="Education"
        :options="educations"
        :multiple="true"
        :trackBy="'key'"
        :label="'name'"
        :close-on-select="false"
        :limit="3"
        v-on:input:update-value="updateField($event, 'education')"
      ></m-multi-select>
    </div>

    <div class="w-full px-4 py-4 border-t flex justify-between items-center">
      <p class="w-2/5 text-grey-dark">{{ $t('components.settings.language') }}</p>

      <m-multi-select
        :valueToEdit="language"
        placeholder="Language"
        :options="languages"
        :multiple="true"
        :trackBy="'key'"
        :label="'name'"
        :close-on-select="false"
        :limit="3"
        v-on:input:update-value="updateField($event, 'language')"
      ></m-multi-select>
    </div>

    <div class="w-full px-4 py-4 border-t flex justify-between items-center">
      <p class="w-2/5 text-grey-dark">{{ $t('components.settings.gender') }}</p>

      <m-select
        :valueToEdit.sync="gender"
        placeholder="Gender"
        :options="genders"
        :multiple="false"
        :trackBy="'key'"
        :label="'name'"
        :resource="'gender'"
        v-on:input:update-value="updateField($event, 'gender')"
      ></m-select>
    </div>

    <div class="w-full px-4 py-4 border-t flex justify-between items-center">
      <p class="w-2/5 text-grey-dark">{{ $t('components.events.time_zone') }}</p>

      <m-select
        :valueToEdit.sync="timezone"
        placeholder="Location"
        :options="timezones"
        :multiple="false"
        :trackBy="'key'"
        :label="'name'"
        :resource="'timezone'"
        v-on:input:update-value="updateField($event, 'timezone')"
      ></m-select>
    </div>

    <div class="w-full px-4 py-4 border-t flex justify-between items-center">
      <p class="w-2/5 text-grey-dark">{{ $t('components.settings.hide_my') }}</p>

      <div class="w-full flex justify-between items-center">
        <div class="w-3/5 px-4 h-12 flex">
          <b-checkbox
            class="text-sm h-12"
            :class="(languageLocale === 'ar') ? 'ml-4' : 'mr-4'"
            :disabled="!hideMyClicked"
            v-model="locationPrivacy"
          >{{ $t('components.settings.location') }}</b-checkbox>

          <b-checkbox
            class="text-sm h-12"
            :class="(languageLocale === 'ar') ? 'ml-4' : 'mr-4'"
            :disabled="!hideMyClicked"
            v-model="birthdayPrivacy"
          >{{ $t('components.settings.birthday') }}</b-checkbox>

          <b-checkbox
            class="text-sm h-12"
            :disabled="!hideMyClicked"
            v-model="educationPrivacy"
          >{{ $t('components.settings.education') }}</b-checkbox>
        </div>

        <div
          class="block cursor-pointer icon-grey colorize-teal w-5"
          @click="hideMyClicked = true"
          v-if="!hideMyClicked"
        >
          <img src="/svgs/pen.svg" alt />
        </div>

        <div class="block flex justify-center items-center h-4" v-else>
          <div
            class="icon-teal cursor-pointer w-6"
            :class="(languageLocale === 'ar') ? 'ml-4' : 'mr-4'"
          >
            <img src="/svgs/check.svg" alt @click.stop="updatePrivacies" />
          </div>

          <div class="icon-red colorize-teal cursor-pointer w-4">
            <img src="/svgs/close.svg" alt @click.stop="hideMyClicked = false" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import LiveEdit from "./../utils/LiveEdit.vue";
import Modal from "./../utils/Modal";
import mSelect from "./../utils/mSelect";
import mMultiSelect from "./../utils/mMultiSelect";
import { mapState, mapMutations, mapActions } from "vuex";

export default {
  props: ["user"],

  computed: {
    ...mapState({
      profile: state => state.profiles.signedInProfile,
      languageLocale: state => state.i18n.locale
    })
  },
  async created() {
    let that = this;
    await axios.get("/countries").then(response => {
      _.forEach(response.data.countries, (value, key) => {
        // console.log(value);
        value.name = value.name[that.languageLocale];
        that.countries.push(value);
      });
    });

    await axios
      .get("/educations")
      .then(response => {
        _.forEach(response.data.educations, (value, key) => {
          value.name = value.name[that.languageLocale];
          that.educations.push(value);
        });
      })
      .catch(error => {
        this.loading = false;
      });

    await axios
      .get("/languages")
      .then(response => {
        _.forEach(response.data.languages, (value, key) => {
          value.name = value.name[that.languageLocale];
          that.languages.push(value);
        });
      })
      .catch(error => {
        this.loading = false;
      });

    await axios.get("/genders").then(response => {
      _.forEach(response.data.genders, (value, key) => {
        value.name = value.name[that.languageLocale];
        that.genders.push(value);
      });
    });

    await axios.get("/timezones").then(response => {
      _.forEach(response.data.timezones, (value, key) => {
        //   console.log(value);
        value.name = value.name[that.languageLocale];
        that.timezones.push(value);
      });
    });

    if (this.profile.location !== null) {
      this.location = {
        iso_3166_2: this.profile.location.country.iso_3166_2,
        name: this.profile.location.country.name[this.languageLocale]
      };
      if (this.profile.location.hidden) {
        this.locationPrivacy = true;
      }
    } else {
      this.location = "Choose location";
    }

    if (this.profile.educations !== null) {
      this.profile.educations.forEach(element => {
        let educationElement = element;
        educationElement.name = educationElement.name[that.languageLocale];
        this.education.push(educationElement);

        if (element.pivot.hidden) {
          this.educationPrivacy = true;
        }
      });
    } else {
      this.education = "Choose educations";
    }

    if (this.profile.languages !== null) {
      this.profile.languages.forEach(element => {
        let languageElement = element;
        languageElement.name = languageElement.name[that.languageLocale];
        this.language.push(languageElement);
      });
    } else {
      this.language = "Choose languages";
    }

    if (this.profile.gender !== null) {
      this.gender = {
        key: this.profile.gender.key,
        name: this.profile.gender.name[this.languageLocale]
      };
    } else {
      this.gender = "Choose gender";
    }

    if (this.profile.timezone !== null) {
      this.timezone = {
        name: this.profile.timezone.timezone_value.name[this.languageLocale],
        key: this.profile.timezone.timezone_value.key
      };
    } else {
      this.timezone = "Choose timezone";
    }
  },
  async mounted() {},
  data() {
    return {
      birthdayPrivacy: false,
      educationPrivacy: false,
      locationPrivacy: false,
      location: null,
      countries: [],
      language: [],
      languages: [],
      education: [],
      educations: [],
      gender: null,
      genders: [],
      gender: "",
      timezone: "",
      timezones: [],
      hideMy: "",
      hideMyClicked: false,
      modalOpen: false,
      modalName: ""
    };
  },
  methods: {
    async updatePrivacies() {
      let that = this;

      await axios
        .post("/user/settings/updateLocationBirthdayEducationPrivacy", {
          birthday: this.birthdayPrivacy,
          location: this.locationPrivacy,
          education: this.educationPrivacy
        })
        .then(response => {
          this.hideMyClicked = false;

          this.$toast.open({
            duration: 5000,
            message: this.$t("components.settings.toast.privacies_updated"),
            position: "is-bottom",
            type: "is-success"
          });
        })
        .catch(error => {
          this.$toast.open({
            duration: 5000,
            message: error.response.data.message,
            position: "is-bottom",
            type: "is-warning"
          });
        });
    },
    async updateField(value, resource) {
      if (resource === "location") {
        await axios
          .put("/user/settings/updateLocation", {
            name: value.value !== null ? value.value.name : null,
            key: value.value !== null ? value.value.iso_3166_2 : null,
            resource: resource
          })
          .then(response => {
            this.$toast.open({
              duration: 5000,
              message: this.$t("components.settings.toast.location_updated"),

              position: "is-bottom",
              type: "is-success"
            });
          });
      } else if (resource === "timezone") {
        await axios
          .put("/user/settings/updateTimezone", {
            timezone: value.value
          })
          .then(response => {
            let that = this;

            this.$toast.open({
              duration: 5000,
              message: this.$t("components.settings.toast.timezone_updated"),
              position: "is-bottom",
              type: "is-success"
            });
          });
      } else if (resource === "language") {
        await axios
          .put("/user/settings/updateLanguage", {
            languages: value.value
          })
          .then(response => {
            let that = this;
            that.language = [];

            _.forEach(response.data.profile.languages, function(value) {
              value.name = value.name[that.languageLocale];

              that.language.push(value);
            });

            this.$toast.open({
              duration: 5000,
              message: this.$t("components.settings.toast.languages_updated"),
              position: "is-bottom",
              type: "is-success"
            });
          });
      } else if (resource === "education") {
        await axios
          .put("/user/settings/updateEducation", {
            educations: value.value
          })
          .then(response => {
            let that = this;
            that.education = [];

            _.forEach(response.data.profile.educations, function(value) {
              value.name = value.name[that.languageLocale];
              that.education.push(value);
            });

            this.$toast.open({
              duration: 5000,
              message: this.$t("components.settings.toast.educations_updated"),
              position: "is-bottom",
              type: "is-success"
            });
          });
      } else if (resource === "gender") {
        await axios
          .put("/user/settings/updateGender", {
            gender: value.value !== null ? value.value.key : null
          })
          .then(response => {
            this.$toast.open({
              duration: 5000,
              message: this.$t("components.settings.toast.gender_updated"),
              position: "is-bottom",
              type: "is-success"
            });
          });
      }
    },
    openModal(componentName) {
      this.modalOpen = true;
      this.modalName = componentName;
    }
  },
  components: {
    LiveEdit,
    Modal,
    mSelect,
    mMultiSelect
  }
};
</script>

<style lang="sass" scoped>

</style>
