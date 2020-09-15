<template>
  <div class="fixed pin flex items-center">
    <div
      class="relative mx-6 md:mx-auto w-full md:w-1/2 lg:w-2/4 z-20"
      v-if="createEventInitialization"
    >
      <div class="shadow-lg bg-grey rounded text-grey-darkest">
        <form class="flex flex-col" @submit.prevent>
          <div class="flex items-center bg-teal justify-between pl-10 py-4">
            <p class="text-white text-lg">{{ $t('shared.edit') }} {{ event.title }}</p>
            <button @click="close()">
              <img
                class="invert-icon w-4"
                :class="(language === 'ar') ? 'ml-4' : 'mr-4'"
                src="/svgs/close.svg"
                alt
              />
            </button>
          </div>

          <div class="flex px-10 pt-4">
            <div class="flex flex-col w-1/2">
              <div class="flex">
                <div class="flex flex-col w-2/3">
                  <label
                    class="text-sm rubik-regular"
                    for="firstName"
                  >{{ $t('components.events.event_title') }}</label>
                  <input
                    class="appearance-none border border-grey-darker rounded w-5/6 py-2 px-3 text-grey-darker bg-white"
                    id="firstName"
                    type="text"
                    :placeholder="$t('components.events.event_name')"
                    v-model="editedEvent.title"
                    maxlength="65"
                    name="title"
                    v-validate="'required'"
                    :class="{'is-danger': errors.has('title') }"
                  />

                  <span
                    v-show="errors.has('title')"
                    class="help is-danger inline h-1 w-1 whitespace-no-wrap"
                  >{{ errors.first('title') }}</span>
                </div>

                <div class="w-1/3">
                  <label
                    class="text-sm mb-1 rubik-regular"
                    for="firstName"
                  >{{ $t('components.events.event_type') }}</label>

                  <div class="flex text-grey-darkest mt-2 rubik-light text-sm">
                    <div :class="(language === 'ar') ? 'ml-4' : 'mr-4'">
                      <b-radio
                        class="whitespace-no-wrap"
                        v-model="editedEvent.event_type"
                        native-value="one-time"
                      >
                        <span
                          :class="(language === 'ar') ? 'pr-1' : 'pl-0'"
                        >{{ $t('components.events.one_time') }}</span>
                      </b-radio>
                    </div>
                    <div :class="(language === 'ar') ? 'ml-4' : 'mr-4'">
                      <b-radio
                        class="whitespace-no-wrap"
                        v-model="editedEvent.event_type"
                        native-value="endless"
                      >
                        <span
                          :class="(language === 'ar') ? 'pr-1' : 'pl-0'"
                        >{{ $t('components.events.endless') }}</span>
                      </b-radio>
                    </div>
                  </div>
                </div>
              </div>

              <div class="w-full mt-4">
                <label
                  class="text-sm rubik-regular"
                  for="firstName"
                >{{ $t('components.events.date_range') }}</label>
                <div class="flex flex-col">
                  <b-datepicker
                    v-model="editedEvent.start_date"
                    :placeholder="$t('shared.click_to_select')"
                    icon="calendar-today"
                    :class="(language === 'ar') ? 'ml-2' : 'mr-2'"
                    v-validate="'required'"
                    data-vv-as="start date"
                    name="start_date"
                    ref="start_date"
                  ></b-datepicker>

                  <span
                    v-show="errors.has('start_date')"
                    class="help is-danger inline h-1 w-1 whitespace-no-wrap"
                  >{{ errors.first('start_date') }}</span>
                  <b-datepicker
                    v-if="editedEvent.event_type === 'one-time'"
                    v-model="editedEvent.end_date"
                    :placeholder="$t('shared.click_to_select')"
                    icon="calendar-today"
                    v-validate="'required'"
                    data-vv-as="end date"
                    name="end_date"
                    ref="end_date"
                  ></b-datepicker>

                  <span
                    v-show="errors.has('end_date')"
                    class="help is-danger inline h-1 w-1 whitespace-no-wrap"
                  >{{ errors.first('end_date') }}</span>
                </div>
              </div>

              <div class="flex w-full mt-4">
                <div :class="(language === 'ar') ? 'ml-5' : 'mr-5'">
                  <label class="text-sm rubik-regular" for="firstName">{{ $t('shared.from') }}</label>

                  <b-timepicker
                    :placeholder="$t('shared.select_time')"
                    icon="clock"
                    v-model="editedEvent.starts_at"
                    :hour-format="'12'"
                    v-validate="'required'"
                    data-vv-as="start time"
                    name="starts_at"
                    ref="starts_at"
                  >
                    <button class="button is-danger" @click="editedEvent.starts_at = new Date()">
                      <b-icon icon="close"></b-icon>
                      <span>{{ $t('shared.clear') }}</span>
                    </button>
                  </b-timepicker>

                  <span
                    v-show="errors.has('starts_at')"
                    class="help is-danger inline h-1 w-1 whitespace-no-wrap"
                  >{{ errors.first('starts_at') }}</span>
                </div>

                <div
                  v-if="editedEvent.event_type === 'one-time'"
                  :class="(language === 'ar') ? 'ml-5' : 'mr-5'"
                  style="height:20px;"
                >
                  <label class="text-sm rubik-regular" for="firstName">{{ $t('shared.to') }}</label>

                  <b-timepicker
                    :placeholder="$t('shared.select_time')"
                    icon="clock"
                    v-model="editedEvent.ends_at"
                    :hour-format="'12'"
                    v-validate="'required'"
                    data-vv-as="end time"
                    name="ends_at"
                    ref="ends_at"
                  >
                    <button class="button is-danger" @click="editedEvent.ends_at = null">
                      <b-icon icon="close"></b-icon>
                      <span>{{ $t('shared.clear') }}</span>
                    </button>
                  </b-timepicker>

                  <span
                    v-show="errors.has('ends_at')"
                    class="help is-danger inline h-1 w-1 whitespace-no-wrap"
                  >{{ errors.first('ends_at') }}</span>
                </div>
              </div>

              <div class="w-1/2 mt-4">
                <label class="text-sm rubik-regular" for="firstName">{{ $t('shared.timezone') }}</label>

                <multiselect
                  class="rounded bg-white text-black"
                  style="box-sizing:content-box;display:block;position:relative;width:100%;"
                  v-model="timezone"
                  track-by="key"
                  :label="'name'"
                  :placeholder="$t('shared.select_one')"
                  :options="timezones"
                  :searchable="true"
                  :allow-empty="false"
                  :close-on-select="true"
                  :multiple="false"
                  v-validate="'required'"
                  data-vv-as="timezone"
                  name="timezone"
                  ref="timezone"
                >
                  <template slot="singleLabel" slot-scope="{ option }">
                    <p class>{{ option.name || option.value }}</p>
                    <!-- <strong>is written in {{ option.language }}</strong> -->
                  </template>
                </multiselect>

                <span
                  v-show="errors.has('timezone')"
                  class="help is-danger inline h-1 w-1 whitespace-no-wrap"
                >{{ errors.first('timezone') }}</span>
              </div>

              <div class="flex-col py-4" :class="(language === 'ar') ? 'pl-10' : 'pr-10'">
                <label
                  class="text-sm rubik-regular"
                  for="firstName"
                >{{ $t('components.events.event_privacy') }}</label>

                <div class="mt-2">
                  <b-radio
                    :class="(language === 'ar') ? 'ml-3' : 'mr-3'"
                    v-model="editedEvent.event_privacy"
                    native-value="public"
                    v-validate="'required|included:public,private,secret'"
                    name="event_privacy"
                    data-vv-as="event privacy"
                  >
                    <span :class="(language === 'ar') ? 'pr-1' : 'pl-0'">{{ $t('shared.public') }}</span>
                  </b-radio>
                  <b-radio
                    :class="(language === 'ar') ? 'ml-3' : 'mr-3'"
                    v-model="editedEvent.event_privacy"
                    native-value="private"
                  >
                    <span :class="(language === 'ar') ? 'pr-1' : 'pl-0'">{{ $t('shared.private') }}</span>
                  </b-radio>
                  <b-radio
                    :class="(language === 'ar') ? 'ml-3' : 'mr-3'"
                    v-model="editedEvent.event_privacy"
                    native-value="secret"
                  >
                    <span :class="(language === 'ar') ? 'pr-1' : 'pl-0'">{{ $t('shared.secret') }}</span>
                  </b-radio>

                  <br />
                  <span
                    v-show="errors.has('event_privacy')"
                    class="help is-danger inline h-1 w-1 whitespace-no-wrap"
                  >{{ errors.first('event_privacy') }}</span>
                </div>
              </div>
            </div>

            <div class="w-1/3 flex flex-col" :class="(language === 'ar') ? 'mr-auto' : 'ml-auto'">
              <resource-image-picker
                class="relative z-10"
                :profile="profile"
                :existingImage="editedEvent.image_path"
                v-on:image-chosen="imageChosen"
              ></resource-image-picker>
            </div>
          </div>

          <div class="w-full mb-4 px-10 py-4">
            <label
              class="block text-sm mb-1 rubik-regular"
              for="eventDescription"
            >{{ $t('shared.description') }}</label>

            <textarea
              class="appearance-none border border-grey-darker rounded-lg w-full py-2 px-3 text-grey-darker bg-white"
              name="eventDescription"
              id="eventDescription"
              cols="30"
              rows="4"
              :placeholder="$t('components.events.write_description')"
              v-model="editedEvent.body"
              v-validate="'required'"
              data-vv-as="event description"
              ref="eventDescription"
            ></textarea>

            <span
              v-show="errors.has('eventDescription')"
              class="help is-danger inline h-1 w-1 whitespace-no-wrap"
            >{{ errors.first('eventDescription') }}</span>
          </div>

          <div class="flex">
            <button
              class="block mx-auto flex bg-teal-light border border-teal-light hover:bg-white hover:text-teal-light text-white text-sm px-6 py-2 mt-4 mb-6"
              :class="(language === 'ar') ? 'ml-10 float-left' : 'mr-10 float-right'"
              @click="updateEvent()"
            >
              {{ $t('shared.update') }}
              <div class="block flex ml-5 spinner" v-if="loading"></div>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- >{{ moment(profile.date_of_birth).format('DD-MMMM-YYYY') }}</p> -->
</template>

<script>
import moment from "moment";
import Multiselect from "vue-multiselect";

import { mapState, mapMutations, mapActions } from "vuex";
import closeMixin from "./../../mixins/close.js";
import ResourceImagePicker from "./../utils/ResourceImagePicker.vue";
import mSelect from "./../utils/mSelect";
import timezonesList2 from "./../../json/timezones2.json";

export default {
  mixins: [closeMixin],
  props: [],
  computed: {
    ...mapState({
      profile: state => state.profiles.signedInProfile,
      event: state => state.events.activeEvent,
      language: state => state.i18n.locale
    })
  },
  async created() {
    let that = this;

    await axios.get("/timezones").then(response => {
      _.forEach(response.data.timezones, (value, key) => {
        value.name = value.name[that.language];
        that.timezones.push(value);
      });
    });

    this.editedEvent.slug = this.event.slug;
    this.editedEvent.event_privacy = this.event.privacy.key;
    this.editedEvent.title = this.event.title;
    this.editedEvent.body = this.event.body;
    this.editedEvent.event_type = this.event.event_type;
    this.editedEvent.image = this.event.image_path;

    this.editedEvent.timezone = {
      key: this.event.timezone.timezone_value.key,
      name: this.event.timezone.timezone_value.name[this.language]
    };

    this.timezone = {
      key: this.event.timezone.timezone_value.key,
      name: this.event.timezone.timezone_value.name[this.language]
    };

    if (this.event.start_date) {
      this.editedEvent.start_date = moment(this.event.start_date)
        .locale("en")
        .toDate();
    }

    if (this.event.end_date) {
      this.editedEvent.end_date = moment(this.event.end_date)
        .locale("en")
        .toDate();
    }

    if (this.event.starts_at) {
      this.editedEvent.starts_at = moment(this.event.starts_at)
        .locale("en")
        .toDate();
    }

    if (this.event.ends_at) {
      this.editedEvent.ends_at = moment(this.event.ends_at)
        .locale("en")
        .toDate();
    }
  },

  watch: {
    "editedEvent.start_date"(value) {
      let startDateString = moment(value)
        .locale("en")
        .tz("UTC")
        .format("YYYY-MM-DD");
      let startsAtString = moment(this.editedEvent.starts_at)
        .locale("en")
        .tz("UTC")
        .format("HH:mm:ss");
      let endsAtString = moment(this.editedEvent.ends_at)
        .locale("en")
        .tz("UTC")
        .format("HH:mm:ss");

      let finalDateStart = moment(startDateString + " " + startsAtString)
        .locale("en")
        .format();
      let finalDateEnd = moment(startDateString + " " + endsAtString)
        .locale("en")
        .format();

      this.start_date_to_send = finalDateStart;
      this.starts_at_to_send = finalDateStart;
      if (this.editedEvent.event_type === "one-time") {
        this.ends_at_to_send = finalDateEnd;
      }
    },
    "editedEvent.end_date"(value) {
      let endDateString = moment(value)
        .locale("en")
        .tz("UTC")
        .format("YYYY-MM-DD");
      let endsAtString = moment(this.editedEvent.ends_at)
        .locale("en")
        .tz("UTC")
        .format("HH:mm:ss");

      let finalDate = moment(endDateString + " " + endsAtString)
        .locale("en")
        .format();

      this.end_date_to_send = finalDate;
      this.ends_at_to_send = finalDate;
    },
    "editedEvent.starts_at"(value) {
      if (this.editedEvent.start_date) {
        var startDateString = moment(this.editedEvent.start_date)
          .locale("en")
          .tz("UTC")
          .format("YYYY-MM-DD");
      } else {
        var startDateString = moment(Date.now())
          .locale("en")
          .tz("UTC")
          .format("YYYY-MM-DD");
      }

      let startsAtString = moment(this.editedEvent.starts_at)
        .locale("en")
        .tz("UTC")
        .format("HH:mm:ss");

      let finalDate = moment(startDateString + " " + startsAtString)
        .locale("en")
        .format("YYYY-MM-DD HH:mm:ss");

      this.start_date_to_send = finalDate;
      this.starts_at_to_send = finalDate;
    },
    "editedEvent.ends_at"(value) {
      if (this.editedEvent.event_type === "one-time") {
        if (this.editedEvent.end_date) {
          var endDateString = moment(this.editedEvent.end_date)
            .locale("en")
            .tz("UTC")
            .format("YYYY-MM-DD");
        } else if (this.editedEvent.start_date) {
          var endDateString = moment(this.editedEvent.start_date)
            .locale("en")
            .tz("UTC")
            .format("YYYY-MM-DD");
        } else {
          var endDateString = moment(Date.now())
            .locale("en")
            .tz("UTC")
            .format("YYYY-MM-DD");
        }

        let endsAtString = moment(this.editedEvent.ends_at)
          .locale("en")
          .tz("UTC")
          .format("HH:mm:ss");

        let finalDate = moment(endDateString + " " + endsAtString)
          .locale("en")
          .format("YYYY-MM-DD HH:mm:ss");

        this.end_date_to_send = finalDate;
        this.ends_at_to_send = finalDate;
      }
    },
    timezone(value) {
      if (value) {
        this.editedEvent.timezone = value.key;
      }
    }
  },
  data() {
    return {
      ends_at_to_send: null,
      startDateTimestamp: null,
      endDateTimestamp: null,
      start_date_to_send: null,
      end_date_to_send: null,
      starts_at_to_send: null,
      ends_at_to_send: null,
      editedEvent: {
        title: null,
        event_type: null,
        start_date: null,
        end_date: null,
        ends_at: null,
        starts_at: null,
        event_privacy: null,
        body: null,
        timezone: null,
        image: null
      },
      chosenCommunity: "",
      createdEvent: null,
      start_dateTimestamp: null,
      end_dateTimestamp: null,
      end_dateShow: false,
      date: new Date().toISOString().substr(0, 10),
      dropFiles: [],
      createEventInitialization: {},
      loading: false,
      timezones: [],
      timezone: ""
    };
  },
  methods: {
    imageChosen(value) {
      this.editedEvent.image = value;
    },
    async updateEvent() {
      this.loading = true;

      this.$validator.validateAll().then(async result => {
        if (result) {
          await axios
            .patch("/event/" + this.event.slug + "/update", {
              slug: this.editedEvent.slug,
              title: this.editedEvent.title,
              event_privacy: this.editedEvent.event_privacy,
              event_type: this.editedEvent.event_type,
              start_date: this.start_date_to_send,
              end_date: this.end_date_to_send,
              starts_at: this.starts_at_to_send,
              ends_at:
                this.editedEvent.event_type === "one-time"
                  ? this.ends_at_to_send
                  : null,
              body: this.editedEvent.body,
              timezone: this.editedEvent.timezone,
              image: this.editedEvent.image
            })
            .then(response => {
              this.loading = false;
              this.$emit("update:event");

              this.$store.dispatch(
                "events/setActiveEvent",
                response.data.event
              );
              this.$emit("modal:close");
            })
            .catch(error => {
              this.loading = false;
            });
        }
      });
      this.loading = false;
    },
    close() {
      this.$emit("modal:close");
    }
  },
  components: {
    moment,
    Multiselect,
    ResourceImagePicker,
    mSelect
  }
};
</script>

<style lang="scss" scoped>
// @import ~'buefy/lib/buefy.css';
</style>
