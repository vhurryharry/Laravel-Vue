<template>
  <div class="fixed pin flex items-center">
    <div
      class="relative mx-6 md:mx-auto w-full md:w-1/2 lg:w-2/4 z-20"
      v-if="createEventInitialization"
    >
      <div class="shadow-lg bg-grey rounded text-grey-darkest">
        <form class="flex flex-col" @submit.prevent>
          <div class="flex items-center bg-teal justify-between px-10 py-4">
            <p class="text-white text-lg">{{ $t('components.events.create_an_event') }}</p>
            <button class @click="close()">
              <img class="invert-icon w-4" src="/svgs/close.svg" alt />
            </button>
          </div>

          <div class="flex pt-4 px-10">
            <div class="flex flex-col w-1/2">
              <div class="flex">
                <div class="flex flex-col w-2/3">
                  <label
                    class="text-sm rubik-regular"
                    for="firstName"
                  >{{ $t('components.events.event_title') }}</label>

                  <input
                    class="w-5/6 py-2 px-3 text-grey-darker bg-white raleway-regular text-sm bg-white border border-grey"
                    id="firstName"
                    type="text"
                    :placeholder="$t('components.events.event_name')"
                    v-model="event.title"
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
                        v-model="event.event_type"
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
                        v-model="event.event_type"
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
                <div class="flex">
                  <div class="flex flex-col">
                    <b-datepicker
                      :date-formatter="(date) => formatDate(date)"
                      v-model="event.start_date"
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
                  </div>

                  <div class="flex flex-col">
                    <b-datepicker
                      v-if="event.event_type === 'one-time'"
                      v-model="event.end_date"
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
              </div>

              <div class="flex w-full mt-4">
                <div :class="(language === 'ar') ? 'ml-5' : 'mr-5'">
                  <label class="text-sm rubik-regular" for="firstName">{{ $t('shared.from') }}</label>

                  <b-timepicker
                    :placeholder="$t('shared.select_time')"
                    icon="clock"
                    v-model="event.starts_at"
                    :hour-format="'12'"
                    v-validate="'required'"
                    data-vv-as="start time"
                    name="starts_at"
                    ref="starts_at"
                  >
                    <button class="button is-danger text-sm" @click="event.starts_at = new Date()">
                      <b-icon icon="close"></b-icon>
                      <span>{{ $t('shared.clear') }}</span>
                    </button>
                    <!-- </div> -->
                  </b-timepicker>

                  <span
                    v-show="errors.has('starts_at')"
                    class="help is-danger inline h-1 w-1 whitespace-no-wrap"
                  >{{ errors.first('starts_at') }}</span>
                </div>

                <div
                  v-if="event.event_type === 'one-time'"
                  :class="(language === 'ar') ? 'ml-5' : 'mr-5'"
                  style="height:20px;"
                >
                  <label class="text-sm rubik-regular" for="firstName">{{ $t('shared.to') }}</label>

                  <b-timepicker
                    :placeholder="$t('shared.select_time')"
                    icon="clock"
                    v-model="event.ends_at"
                    :hour-format="'12'"
                    v-validate="'required'"
                    data-vv-as="end time"
                    name="ends_at"
                    ref="ends_at"
                  >
                    <button class="button is-danger text-sm" @click="event.ends_at = null">
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
                    v-model="event.event_privacy"
                    native-value="public"
                    v-validate="'required|included:public,private,secret'"
                    name="event_privacy"
                    data-vv-as="event privacy"
                  >
                    <span :class="(language === 'ar') ? 'pr-1' : 'pl-0'">{{ $t('shared.public') }}</span>
                  </b-radio>
                  <b-radio
                    :class="(language === 'ar') ? 'ml-3' : 'mr-3'"
                    v-model="event.event_privacy"
                    native-value="private"
                  >
                    <span :class="(language === 'ar') ? 'pr-1' : 'pl-0'">{{ $t('shared.private') }}</span>
                  </b-radio>
                  <b-radio
                    :class="(language === 'ar') ? 'ml-3' : 'mr-3'"
                    v-model="event.event_privacy"
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
              <div class="mb-4">
                <label
                  class="text-sm rubik-regular"
                  for="firstName"
                >{{ $t('components.events.create_event_for') }}</label>
                <p class="text-teal" v-if="creatingFrom === 'community'">{{ community.name }}</p>

                <div class="flex text-grey-darkest mt-2 rubik-light text-sm" v-else>
                  <div class :class="(language === 'ar') ? 'ml-4' : 'mr-4'">
                    <b-radio v-model="event.created_by" native-value="profile">
                      <span :class="(language === 'ar') ? 'pr-1' : ''">{{ $t('shared.myself') }}</span>
                    </b-radio>
                  </div>

                  <div class :class="(language === 'ar') ? 'ml-4' : 'mr-4'">
                    <b-radio v-model="event.created_by" native-value="community">
                      <span
                        :class="(language === 'ar') ? 'pr-1' : ''"
                      >{{ $t('components.communities.community') }}</span>
                    </b-radio>
                  </div>
                </div>
              </div>

              <multiselect
                class="rounded bg-white text-black mb-4 relative z-20"
                style="box-sizing:content-box;display:block;position:relative;width:100%;"
                v-model="chosenCommunity"
                label="name"
                track-by="slug"
                placeholder="Select community"
                :options="localMyCommunities"
                :searchable="true"
                :allow-empty="false"
                :close-on-select="true"
                :multiple="false"
                v-if="event.created_by != 'profile'"
              >
                <template slot="singleLabel" slot-scope="{ option }">
                  <p class>{{ option.name }}</p>
                  <!-- <strong>is written in {{ option.language }}</strong> -->
                </template>
              </multiselect>

              <resource-image-picker
                class="relative z-10"
                :profile="profile"
                v-on:image-chosen="imageChosen"
              ></resource-image-picker>
            </div>
          </div>

          <div class="w-full mb-4 py-4 px-10">
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
              v-model="event.body"
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
              class="block mx-auto flex items-center bg-teal-light border border-teal-light hover:bg-white hover:text-teal-light text-white text-sm px-6 py-2 mt-4 mb-6"
              :class="(language === 'ar') ? 'ml-10 float-left' : 'mr-10 float-right'"
              @click="createEvent()"
              type="submit"
            >
              {{ $t('shared.next') }}
              <div
                class="block flex spinner"
                :class="(language === 'ar') ? 'mr-5' : 'ml-5'"
                v-if="loading"
              ></div>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import DateOfBirth from "./../../components/profile/EditDateOfBirth.vue";
import DeactivateAccount from "./../../components/profile/DeactivateAccount.vue";
import DeleteAccount from "./../../components/profile/DeleteAccount.vue";
import FriendShipInteractionComponent from "./../../components/friends/FriendShipInteractionComponent.vue";
import InfiniteLoading from "vue-infinite-loading";

import moment from "moment";
import Multiselect from "vue-multiselect";
import ResourceImagePicker from "./../utils/ResourceImagePicker.vue";
import closeMixin from "./../../mixins/close.js";
import mSelect from "./../utils/mSelect";

import { mapState, mapMutations, mapActions } from "vuex";

export default {
  mixins: [closeMixin],

  props: ["fromCommunity"],
  computed: {
    ...mapState({
      profile: state => state.profiles.signedInProfile,
      profiles: state => state.profiles.profiles,
      community: state => state.communities.activeCommunity,
      myCommunities: state => state.communities.myCommunities,
      queryResults: state => state.search.searchResults.profiles,
      language: state => state.i18n.locale,
      creatingFrom: state => state.events.creatingFrom
    })
  },
  async created() {
    let that = this;

    if (this.creatingFrom === "community") {
      this.chosenCommunity = this.community;
    }

    await axios.get("/timezones").then(response => {
      _.forEach(response.data.timezones, (value, key) => {
        value.name = value.name[that.language];
        that.timezones.push(value);
      });
    });

    this.$store.dispatch("profiles/getProfiles");
    this.$store.dispatch("communities/getMyCommunities");
  },
  watch: {
    myCommunities(value) {
      if (value) {
        _.forEach(this.myCommunities, element => {
          this.localMyCommunities.push(element);
        });
      }
    },
    profiles(value) {
      this.searchResults = value;
    },
    queryResults(value) {
      this.searchResults = value;
    },
    guestList(value) {
      this.guestSize = Object.keys(this.guestList).length;
    },
    "event.event_type"(value) {
      if (value === "endless") {
        this.event.start_date = new Date();
      } else {
        this.event.start_date = moment(this.event.start_date)
          .locale("en")
          .toDate();
      }
    },
    "event.start_date"(value) {
      let startDateString = moment(value)
        .locale("en")
        .tz("UTC")
        .format("YYYY-MM-DD");
      let startsAtString = moment(this.event.starts_at)
        .locale("en")
        .tz("UTC")
        .format("HH:mm:ss");

      let finalDate = moment(startDateString + " " + startsAtString)
        .locale("en")
        .format();

      this.start_date_to_send = finalDate;
      this.starts_at_to_send = finalDate;
    },
    "event.end_date"(value) {
      let endDateString = moment(value)
        .locale("en")
        .tz("UTC")
        .format("YYYY-MM-DD");
      let endsAtString = moment(this.event.ends_at)
        .tz("UTC")
        .locale("en")
        .format("HH:mm:ss");

      let finalDate = moment(endDateString + " " + endsAtString)
        .locale("en")
        .format();

      this.end_date_to_send = finalDate;
      this.ends_at_to_send = finalDate;
    },
    "event.starts_at"(value) {
      if (this.event.start_date) {
        var startDateString = moment(this.event.start_date)
          .locale("en")
          .tz("UTC")
          .format("YYYY-MM-DD");
      } else {
        var startDateString = moment(Date.now())
          .locale("en")
          .tz("UTC")
          .format("YYYY-MM-DD");
      }

      let startsAtString = moment(this.event.starts_at)
        .locale("en")
        .tz("UTC")
        .format("HH:mm:ss");

      let finalDate = moment(startDateString + " " + startsAtString)
        .locale("en")
        .format("YYYY-MM-DD HH:mm:ss");

      this.start_date_to_send = finalDate;
      this.starts_at_to_send = finalDate;
    },
    "event.ends_at"(value) {
      if (this.event.end_date) {
        var endDateString = moment(this.event.end_date)
          .locale("en")
          .tz("UTC")
          .format("YYYY-MM-DD");
      } else if (this.event.start_date) {
        var endDateString = moment(this.event.start_date)
          .locale("en")
          .tz("UTC")
          .format("YYYY-MM-DD");
      } else {
        var endDateString = moment(Date.now())
          .locale("en")
          .tz("UTC")
          .format("YYYY-MM-DD");
      }

      let endsAtString = moment(this.event.ends_at)
        .locale("en")
        .tz("UTC")
        .format("HH:mm:ss");

      let finalDate = moment(endDateString + " " + endsAtString)
        .locale("en")
        .format("YYYY-MM-DD HH:mm:ss");

      this.end_date_to_send = finalDate;
      this.ends_at_to_send = finalDate;
    },
    timezone(value) {
      if (value) {
        this.event.timezone = value.key;
      }
    }
  },
  data() {
    return {
      localMyCommunities: [],
      guestSize: 0,
      searchResults: {},
      guestList: {},
      profilesCount: 0,
      searchFieldValue: "",
      communitiesPanel: null,
      event: {
        title: null,
        event_type: "one-time",
        start_date: null,
        end_date: null,
        starts_at: null,
        ends_at: null,
        event_privacy: null,
        body: null,
        timezone: null,
        created_by: "profile",
        image: null
      },
      start_date_to_send: null,
      end_date_to_send: null,
      starts_at_to_send: null,
      ends_at_to_send: null,
      chosenCommunity: "",
      createdEvent: null,
      startDateTimestamp: null,
      endDateTimestamp: null,
      endDateShow: false,
      date: new Date().toISOString().substr(0, 10),
      menu: false,
      modal: false,
      menu2: false,
      dropFiles: [],
      createEventInitialization: true,
      privacyRadio: "",
      privacyRadio2: "",
      loading: false,
      finishLoading: false,
      timezones: [],
      timezone: ""
    };
  },
  methods: {
    formatDate(date) {
      return moment(date)
        .locale(this.language)
        .format("YYYY-MM-DD");
    },
    imageChosen(value) {
      this.event.image = value;
    },
    search(value) {
      if (value === "") {
        this.searchResults = this.profiles;
      } else {
        this.$store
          .dispatch("search/queryProfiles", {
            match: "match",
            fields: "name",
            query: value
          })
          .then(response => {});
      }
    },
    getFriends() {
      this.searchResults = this.profile.friends;
    },
    infiniteHandler($state) {
      this.profilesCount = this.profilesCount + 5;
      this.$store
        .dispatch("profiles/getProfiles", {
          count: this.profilesCount
        })
        .then(response => {
          if (this.profilesCount > Object.keys(this.profiles).length) {
            $state.complete();
          }
          $state.loaded();
        });
    },
    buildCommunitiesArray() {},
    moveToGuestList(id) {
      this.guestList = {
        ...this.guestList,
        [id]: this.profiles[id]
      };

      Vue.delete(this.profiles, id);
    },
    removeFromGuestList(id) {
      this.profiles = {
        ...this.profiles,
        [id]: this.guestList[id]
      };

      Vue.delete(this.guestList, id);
    },
    async inviteToEvent() {
      this.finishLoading = true;
      await axios
        .post("/events/invite", {
          event_slug: this.createdEvent,
          guest_list: this.guestList
        })
        .then(response => {
          this.$store.dispatch("events/addMyEvent", response.data.event);
          this.finishLoading = false;

          this.$emit("create:event");
        })
        .catch(error => {
          this.finishLoading = false;
        });
    },
    createEvent() {
      this.loading = true;

      this.$validator.validateAll().then(async result => {
        if (result) {
          await axios
            .post("/events/create", {
              title: this.event.title,
              event_type: this.event.event_type.toLowerCase(),
              start_date: this.start_date_to_send,
              end_date: this.end_date_to_send,
              starts_at: this.starts_at_to_send,
              ends_at: this.ends_at_to_send,
              event_privacy: this.event.event_privacy,
              body: this.event.body,
              timezone: this.event.timezone,
              image: this.event.image,
              created_by:
                this.event.created_by === "community" || this.creatingFrom
                  ? this.chosenCommunity
                  : null
            })
            .then(response => {
              this.createdEvent = response.data.event_object.slug;
              this.$store.dispatch(
                "events/setNewEvent",
                response.data.event_object
              );
              if (this.creatingFrom !== "community") {
                this.loading = false;
                this.createEventInitialization = false;
                this.$emit("create:done");
                this.$store.dispatch("ui/setInviteToResource", "events");
                this.$store.dispatch("ui/setActiveModal", "invite-to-resource");
              } else {
                this.close();

                this.$toast.open({
                  duration: 5000,
                  message:
                    response.data.event_object.name +
                    this.$t("shared.toast.has_been_created"),
                  position: "is-bottom",
                  type: "is-success"
                });
              }
            })
            .catch(error => {
              this.loading = false;
            });
        } else {
          this.loading = false;
        }
      });
    },
    close() {
      this.$emit("modal:close");
    },
    addFriendRequestSent(profileId) {
      this.$store.dispatch("communities/addFriendRequestSent", profileId);
    },
    addIsFriend(profileId) {
      this.$store.dispatch("communities/addIsFriend", profileId);
    },
    clearFriendRequest(profileId) {
      this.$store.dispatch("communities/clearFriendRequest", profileId);
    }
  },
  components: {
    DateOfBirth,
    DeactivateAccount,
    DeleteAccount,
    moment,
    Multiselect,
    FriendShipInteractionComponent,
    InfiniteLoading,
    ResourceImagePicker,
    mSelect
  }
};
</script>

<style lang="scss" scoped>
// @import ~'buefy/lib/buefy.css';
</style>
