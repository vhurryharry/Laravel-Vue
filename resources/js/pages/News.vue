<template>
  <div class="flex page">
    <div class="bg-grey pt-6 pl-24 flex-1">
      <p class="mb-2 text-xl text-teal rubik-medium">{{ $t('components.news.news_feed') }}</p>

      <div
        class="page-content-container flex flex-col pb-16"
        :class="(language === 'ar') ? 'pl-4' : 'pr-4'"
      >
        <b-loading :is-full-page="false" :active.sync="isLoading" :can-cancel="false"></b-loading>

        <div
          class="card-item text-base flex bg-white text-black mb-6"
          v-for="(event, index) in events"
          :event="event"
          :key="index"
        >
          <div class="w-1/4 h-full">
            <img
              class="regular-left-border-radius object-cover"
              :src="'/storage/images/' + event.image_path"
              alt
            />
          </div>

          <div class="py-6 px-6 leading-normal w-full">
            <a
              :href="'/events/' + event.slug"
              class="text-grey-darker rubik-medium"
            >{{ event.title }}</a>

            <div class="flex">
              <p
                class="text-xs rubik-light"
              >{{ moment(event.start_date).format("MMMM DD, YYYY") }}&nbsp;</p>

              <p class="text-xs rubik-light" v-if="event.starts_at">
                at
                <span v-if="event.ends_at">{{ moment(event.starts_at).format("HH:mm") }}</span>

                <span v-else>
                  {{ moment(event.starts_at).format("HH:mm A") }}
                  <span
                    v-if="event.timezone"
                  >({{ event.timezone.timezone_value.name[language] }})</span>
                </span>
              </p>

              <p class="text-xs rubik-light" v-if="event.ends_at">
                &nbsp;- {{ moment(event.ends_at).format("HH:mm A") }}
                <span
                  v-if="event.timezone"
                >({{ event.timezone.timezone_value.name[language] }})</span>
              </p>
            </div>

            <p class="mt-4 text-sm">
              <span class="font-bold">{{ $t('shared.about') }}:</span>
              {{ event.body }}
            </p>

            <div>
              <hr />

              <div class="w-full block flex items-center justify-between">
                <p class="text-xs raleway-semibold">
                  <span class="text-teal">{{ event.participants.length }}</span>

                  <span
                    v-if="event.participants.length === 1"
                  >{{ $t('components.events.person_is_attending') }}</span>
                  <span v-else>
                    {{ $t('components.events.people_are_attending') }}
                    <!-- <span
                      class="text-teal"
                      v-if="Object.keys(event.mutuals).length != 0"
                    >including {{ Object.keys(event.mutuals).length }}</span>-->

                    <!-- <span v-if="Object.keys(event.mutuals).length == 1">friend</span> -->

                    <!-- <span v-if="Object.keys(event.mutuals).length > 1">friends</span> -->
                  </span>
                </p>

                <div class="flex">
                  <b-tooltip
                    class="float-right cursor-pointer mx-1"
                    position="is-top"
                    animated
                    v-for="(item, index) in event.participants"
                    :label="item.name"
                    :key="index"
                  >
                    <img
                      v-if="index <= 5"
                      class="rounded-full w-6"
                      :src="'/storage/images/' + item.image_path"
                      alt
                    />
                  </b-tooltip>

                  <img
                    class="w-6 cursor-pointer"
                    :class="(language === 'ar') ? 'mr-1' : 'ml-1'"
                    src="/svgs/add.svg"
                    alt
                    @click="openModal('guest-list', event)"
                  />
                </div>
              </div>

              <div class="flex items-center">
                <div
                  class="flex items-center text-grey-darker hover:text-grey-darkest cursor-pointer"
                  @click="like(event)"
                >
                  <font-awesome-icon
                    class="icon text-teal cursor-pointer hover:text-teal-light"
                    :class="(language === 'ar') ? 'ml-2' : 'mr-2'"
                    :icon="['fa', 'heart']"
                    size="sm"
                  />

                  <p
                    class="text-xs text-teal"
                    :class="(language === 'ar') ? 'ml-6' : 'mr-6'"
                    v-if="liked(event.love_reactant.reactions)"
                  >{{ $t('shared.liked') }} ({{ event.reactions_count }})</p>

                  <p
                    v-else
                    class="text-xs"
                    :class="(language === 'ar') ? 'ml-6' : 'mr-6'"
                  >{{ $t('shared.like') }} ({{ event.reactions_count }})</p>
                </div>

                <div
                  class="flex items-center text-grey-darker hover:text-grey-darkest cursor-pointer"
                  @click="openShareModal(event)"
                >
                  <font-awesome-icon
                    class="icon text-teal cursor-pointer hover:text-teal-light"
                    :class="(language === 'ar') ? 'ml-2' : 'mr-2'"
                    :icon="['fa', 'share']"
                    size="sm"
                  />
                  <p class="text-xs">{{ $t('shared.share') }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="main-sidebar bg-teal w-1/3 -mt-1 relative z-0 h-full">
      <div class="flex-1 px-10 pt-6" style="height:80.5vh;overflow:auto;">
        <p class="text-xl rubik-medium mb-2">Events Calendar</p>

        <calendar></calendar>
        <!-- <div class="card-item-small flex flex-col w-full p-8 mb-8">
          <div class>
            <p
              class="text-xs text-grey-darker raleway-italic ml-1 mb-2"
            >Answer some questions to see events more suited to your interests.</p>
            <p class="text-sm">Out of these people who would you like to have dinner with?</p>

            <div class="flex flex-col mt-2 mb-4">
              <div class="border-b-2 border-teal-dark pb-2 rubik-light text-sm ml-4">
                <div class="w-5/6 mb-2">
                  <b-radio v-model="preferenceRadio" native-value="Madonna">Madonna</b-radio>
                </div>

                <div class="w-5/6 mb-2">
                  <b-radio v-model="preferenceRadio" native-value="Britney Spears">Britney Spears</b-radio>
                </div>

                <div class="w-5/6 mb-2">
                  <b-radio v-model="preferenceRadio" native-value="Beyonce">Beyonce</b-radio>
                </div>

                <div class="w-5/6 mb-2">
                  <b-radio v-model="preferenceRadio" native-value="Cardi B.">Cardi B.</b-radio>
                </div>
              </div>
            </div>

            <button
              class="raleway-semibold text-sm float-right bg-grey-darker text-white hover:bg-white hover:text-teal text-white border-grey-darker border py-2 px-4 ml-2"
              type="button"
            >Next</button>

            <button
              class="raleway-semibold text-sm float-right bg-teal text-white hover:bg-white hover:text-teal text-white border-teal border py-2 px-4 mr-2"
              type="button"
            >Submit</button>
          </div>
        </div>-->

        <!-- <p class="text-xl rubik-medium mb-2">Events happening now</p> -->
        <p class="text-xl rubik-medium mt-8 mb-2">{{ $t('components.events.event_happening_now') }}</p>

        <elastic-slider
          :flickityOptions="flickityOptions"
          :cards="eventsHappeningNow"
          :loading="isLoading"
          :resource="'events'"
        ></elastic-slider>

        <a
          href="/search-page#events-controls"
          class="ml-auto my-4 rubik-medium text-sm"
        >{{ $t('components.events.display_all_events') }} &nbsp;></a>
      </div>
      <footer-component></footer-component>
    </div>

    <transition name="fade">
      <component v-if="modalOpen" v-bind:is="modalName" v-on:modal:close="modalOpen = false"></component>
    </transition>
  </div>
</template>

<script>
import Flickity from "vue-flickity";
import PropedModal from "./../components/utils/PropedModal.vue";
import Calendar from "./../components/calendar/Calendar.vue";
import ShareEvent from "./../components/events/ShareEvent.vue";
import InviteToEvent from "./../components/events/InviteToEvent.vue";
import { mapState, mapMutations, mapActions } from "vuex";
import ElasticSlider from "./../components/utils/ElasticSlider.vue";
import GuestList from "./../components/communities/GuestList.vue";

export default {
  mounted() {},
  computed: {
    ...mapState({
      language: state => state.i18n.locale,
      events: state => state.events.events,
      profile: state => state.profiles.signedInProfile,
      eventsHappeningNow: state => state.events.eventsHappeningNow
    }),
    mySortedEvents() {
      let that = this;
      if (this.sortType === "") {
        return _.orderBy(this.myEvents, "created_at", "desc");
      } else {
        return _.filter(this.myEvents, { event_type: this.sortType });
      }
    }
  },
  created() {
    this.isLoading = true;

    this.$store.dispatch("events/getEvents").then(response => {
      this.isLoading = false;
    });

    this.$store.dispatch("events/getMyEvents", this.profile).then(response => {
      this.isLoading = false;
    });

    this.$store.dispatch("events/getEventsHappeningNow");
  },
  data() {
    return {
      isLoading: true,
      modalOpen: false,
      modalName: "create-event",
      currentComponent: "",
      preferenceRadio: "",
      flickityOptions: {
        prevNextButtons: true,
        pageDots: false,
        wrapAround: true,
        groupCells: true
      }
    };
  },
  methods: {
    openShareModal(value) {
      this.$store.dispatch("ui/openShareModal", value);
    },
    like(event) {
      axios
        .post("/profile/" + this.profile.username + "/like", {
          slug: event.slug,
          type: "event"
        })
        .then(response => {
          this.$toast.open({
            duration: 5000,
            message: response.data.message,
            position: "is-bottom",
            type: "is-success",
            queue: false
          });

          this.$store.dispatch("events/updateEventLike", {
            event: response.data.resource,
            profile: this.profile
          });
        })
        .catch(error => {});
    },
    liked(reactions) {
      let liked = reactions.find(
        reaction => reaction.reacter_id === this.profile.id
      );
      return liked;
    },
    openModal(name) {
      this.modalOpen = true;
      this.modalName = name;
    }
  },
  components: {
    Flickity,
    ShareEvent,
    PropedModal,
    InviteToEvent,
    Calendar,
    ElasticSlider,
    GuestList
  }
};
</script>

<style lang="scss" scoped>
</style>
