<template>
  <div class="flex page">
    <div class="bg-grey flex-1 pt-6 px-24">
      <p class="mb-2 text-xl text-teal rubik-medium">{{ $t('components.events.my_events') }}</p>

      <div class="flex text-grey-darkest my-4 rubik-light text-sm">
        <div>
          <b-radio
            class
            :class="(language === 'ar') ? 'pl-2' : 'pr-2'"
            v-model="sortType"
            native-value
          >
            <span :class="(language === 'ar') ? 'pr-1' : 'pl-0'">{{ $t('components.events.all') }}</span>
          </b-radio>
        </div>

        <div>
          <b-radio class="px-2" v-model="sortType" native-value="one-time">
            <span
              :class="(language === 'ar') ? 'pr-1' : 'pl-0'"
            >{{ $t('components.events.one_time') }}</span>
          </b-radio>
        </div>

        <div>
          <b-radio class="px-2" v-model="sortType" native-value="endless">
            <span
              :class="(language === 'ar') ? 'pr-1' : 'pl-0'"
            >{{ $t('components.events.endless') }}</span>
          </b-radio>
        </div>

        <div>
          <b-radio class="px-2" v-model="sortType" native-value="past">
            <span :class="(language === 'ar') ? 'pr-1' : 'pl-0'">{{ $t('components.events.past') }}</span>
          </b-radio>
        </div>
      </div>

      <div
        class="page-content-container flex flex-col pb-16"
        :class="(language === 'ar') ? 'pl-4' : 'pr-4'"
      >
        <b-loading :is-full-page="false" :active.sync="isLoading" :can-cancel="false"></b-loading>

        <div
          class="card-item text-base flex bg-white text-black mb-6"
          v-for="(myEvent, index) in mySortedEvents"
          :event="myEvent"
          :key="index"
        >
          <div class="w-1/4 h-full">
            <img
              class="regular-left-border-radius object-cover"
              :src="'/storage/images/' + myEvent.image_path"
              alt
            />
          </div>

          <div class="py-6 px-6 leading-normal w-full">
            <a
              :href="'/events/' + myEvent.slug"
              class="text-grey-darker rubik-medium"
            >{{ myEvent.title }}</a>

            <div class="flex">
              <p
                class="text-xs rubik-light"
              >{{ moment(myEvent.start_date).format("MMMM DD, YYYY") }}&nbsp;</p>

              <p class="text-xs rubik-light" v-if="myEvent.starts_at">
                at
                <span v-if="myEvent.ends_at">{{ moment(myEvent.starts_at).format("HH:mm") }}</span>

                <span v-else>
                  {{ moment(myEvent.starts_at).format("HH:mm A") }}
                  <span
                    v-if="myEvent.timezone"
                  >({{ myEvent.timezone.timezone_value.name[language] }})</span>
                </span>
              </p>

              <p class="text-xs rubik-light" v-if="myEvent.ends_at">
                &nbsp;- {{ moment(myEvent.ends_at).format("HH:mm A") }}
                <span
                  v-if="myEvent.timezone"
                >({{ myEvent.timezone.timezone_value.name[language] }})</span>
              </p>
            </div>

            <p class="mt-4 text-sm">
              <span class="font-bold">{{ $t('shared.about') }}:</span>
              {{ myEvent.body }}
            </p>

            <div>
              <hr />

              <div class="w-full block flex items-center justify-between">
                <p class="text-xs raleway-semibold">
                  <span class="text-teal">{{ myEvent.participants.length }}</span>

                  <span
                    v-if="myEvent.participants.length === 1"
                  >{{ $t('components.events.person_is_attending') }}</span>
                  <span v-else>
                    {{ $t('components.events.people_are_attending') }}
                    <span
                      class="text-teal"
                      v-if="Object.keys(myEvent.mutuals).length != 0"
                    >including {{ Object.keys(myEvent.mutuals).length }}</span>

                    <span v-if="Object.keys(myEvent.mutuals).length == 1">friend</span>

                    <span v-if="Object.keys(myEvent.mutuals).length > 1">friends</span>
                  </span>
                </p>

                <div class="flex">
                  <b-tooltip
                    class="float-right cursor-pointer mx-1"
                    position="is-top"
                    animated
                    v-for="(item, index) in myEvent.participants"
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

                  <font-awesome-icon
                    class="icon text-teal cursor-pointer hover:text-teal-light"
                    :class="(language === 'ar') ? 'mr-1' : 'ml-1'"
                    :icon="['fa', 'plus-circle']"
                    size="lg"
                    @click="openModal('guest-list', myEvent)"
                  />
                </div>
              </div>

              <div class="flex items-center">
                <div
                  class="flex items-center text-grey-darker hover:text-grey-darkest cursor-pointer"
                  @click="like(myEvent)"
                >
                  <font-awesome-icon
                    class="icon text-teal cursor-pointer hover:text-teal-light"
                    :class="(language === 'ar') ? 'ml-2' : 'mr-2'"
                    :icon="['fa', 'heart']"
                    size="sm"
                  />

                  <!-- <p
                    class="text-xs text-teal"
                    :class="(language === 'ar') ? 'ml-6' : 'mr-6'"
                    v-if="liked(myEvent.love_reactant.reactions)"
                  >{{ $t('shared.liked') }} ({{ myEvent.reactions_count }})</p>

                  <p
                    v-else
                    class="text-xs"
                    :class="(language === 'ar') ? 'ml-6' : 'mr-6'"
                  >{{ $t('shared.like') }} ({{ myEvent.reactions_count }})</p> -->

                                <p
                    class="text-xs text-teal"
                    :class="(language === 'ar') ? 'ml-6' : 'mr-6'"
                    v-if="liked(myEvent.love_reactant.reactions)"
                  >{{ $t('shared.liked') }} ({{ Object.keys(myEvent.love_reactant.reactions).length }})</p>

                  <p
                    v-else
                    class="text-xs"
                    :class="(language === 'ar') ? 'ml-6' : 'mr-6'"
                  >{{ $t('shared.like') }} ({{ Object.keys(myEvent.love_reactant.reactions).length }})</p>
                </div>

                <div
                  class="flex items-center text-grey-darker hover:text-grey-darkest cursor-pointer"
                  @click="openShareModal(myEvent)"
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

    <div class="main-sidebar flex flex-col bg-teal w-1/3 -mt-1 relative z-0 h-full">
      <div class="flex flex-col px-10 pt-6" style="height:80.5vh;overflow:auto;">
        <p class="text-xl rubik-medium">{{ $t('components.events.suggested_events') }}</p>

        <slider
          :flickityOptions="flickityOptions"
          :cards="events"
          :loading="isLoading"
          :resource="'events'"
        ></slider>

        <div class="card-item-small text-base flex-col w-full mt-8">
          <img class="regular-top-border-radius" src="images/people_skiing.jpg" alt />

          <div class="p-6">
            <p
              class="text-lg text-teal rubik-medium"
            >{{ $t('components.events.create_your_own_event') }}</p>

            <p class="mt-4 text-sm">{{ $t('components.events.have_idea') }}</p>

            <hr class="bg-teal" />

            <button
              class="bg-teal flex text-white text-sm px-6 py-2 mx-auto mt-4 border border-teal hover:bg-white hover:text-teal"
              @click="openCreateEvent"
            >{{ $t('components.events.create_event') }}</button>
          </div>
        </div>

        <p class="text-xl rubik-medium mt-8">{{ $t('components.events.event_happening_now') }}</p>

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
      <component
        v-if="modalOpen"
        v-bind:is="modalName"
        v-on:modal:close="modalOpen = false"
        :for="`events`"
      ></component>
    </transition>
  </div>
</template>

<script>
import CreateEvent from "./../components/events/CreateEvent.vue";
import PropedModal from "./../components/utils/PropedModal.vue";
import InviteToResource from "./../components/utils/InviteToResource.vue";
import ShareEvent from "./../components/events/ShareEvent.vue";
import { mapState, mapMutations, mapActions } from "vuex";
import Slider from "./../components/utils/Slider.vue";
import ElasticSlider from "./../components/utils/ElasticSlider.vue";
import GuestList from "./../components/communities/GuestList.vue";

export default {
  props: [""],
  computed: {
    ...mapState({
      profile: state => state.profiles.signedInProfile,
      events: state => state.events.events,
      myEvents: state => state.events.myEvents,
      newEvent: state => state.events.newEvent,
      eventsHappeningNow: state => state.events.eventsHappeningNow,
      language: state => state.i18n.locale,
      myCommunities: state => state.communities.myCommunities
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

    this.$store.dispatch("events/getEvents").then(response => {});

    this.$store.dispatch("events/getMyEvents", this.profile).then(response => {
      this.isLoading = false;
    });

    this.$store.dispatch("events/getEventsHappeningNow");
  },
  data() {
    return {
      modalOpen: false,
      sortType: null,
      modalName: "create-event",
      createEventModalOpen: false,
      inviteToResourceModalOpen: false,
      PropedModalOpen: false,
      sortType: "",
      isLoading: true,
      flickityOptions: {
        prevNextButtons: true,
        pageDots: false,
        wrapAround: true,
        groupCells: true
      }
    };
  },
  methods: {
    finishInvite() {},
    openInvitationModal() {
      this.inviteToResourceModalOpen = true;
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
        .catch(error => {
          // this.$toast.open({
          //   duration: 5000,
          //   message: 'Error.',
          //   position: "is-bottom",
          //   type: "is-danger",
          //   queue: false
          // });
        });
    },
    openCreateEvent() {
      this.$store.dispatch("ui/setActiveModal", "create-event");
    },
    openShareModal(value) {
      this.$store.dispatch("ui/openShareModal", value);
    },
    liked(reactions) {
      let liked = reactions.find(
        reaction => reaction.reacter_id === this.profile.id
      );
      return liked;
    },
    setSortType(value) {
      this.sortType = value;
    },
    openModal(name, event = null) {
      if (event) {
        this.$store.dispatch("events/setActiveEvent", event);
      }

      this.modalOpen = true;
      this.modalName = name;
    },

    openCongratsModal() {
      this.PropedModalOpen = true;
      this.createEventModalOpen = false;
      this.inviteToResourceModalOpen = false;
    },

    next() {
      this.$refs.flickity.next();
    },

    previous() {
      this.$refs.flickity.previous();
    }
  },
  components: {
    CreateEvent,
    PropedModal,
    ShareEvent,
    Slider,
    GuestList,
    InviteToResource,
    ElasticSlider
  }
};
</script>

<style lang="scss" scoped>
</style>
