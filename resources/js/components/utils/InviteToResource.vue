<template>
  <div class="fixed pin flex items-center">
    <div class="relative mx-6 md:mx-auto w-full lg:w-4/5 z-20">
      <div class="flex shadow-lg bg-grey rounded text-grey-darkest" style="height:70vh">
        <div class="flex-col w-1/4 border-r border-grey-darker">
          <div
            class="bg-teal flex w-full items-center py-4 h-10"
            :class="(language === 'ar') ? 'pr-10' : 'pl-10'"
          >
            <p class="text-white">{{ $t('shared.categories') }}</p>
          </div>

          <div class="flex-col py-4" :class="(language === 'ar') ? 'pr-10' : 'pl-10'">
            <div class="flex-col items-start justify-start text-grey-darkest text-sm">
              <label class="block rubik-regular mb-1" for="firstName">{{ $t('shared.general') }}</label>

              <div class="mb-3">
                <b-radio
                  class="block"
                  v-model="privacyRadio"
                  native-value="All Friends"
                  @click.native="getFriends"
                >{{ $t('components.profiles.all_friends') }} ({{ profile.friends.length }})</b-radio>
              </div>

              <div class="mb-3" v-for="(item, index) in profile.educations" :key="index">
                <b-radio
                  class="block"
                  v-model="educationCheck"
                  :native-value="item.key"
                >{{ item.name[language] }}</b-radio>
              </div>
            </div>

            <div class="flex-col items-start justify-start text-grey-darkest text-sm mt-8">
              <label
                class="block rubik-regular mb-1"
                for="firstName"
              >{{ $t('components.communities.my_communities') }}</label>

              <div class="mb-3" v-for="(item, index) in myCommunities" :key="index">
                <b-radio
                  class
                  v-model="privacyRadio"
                  :native-value="item.slug"
                  @click.native="filterResults(item, item.slug)"
                >{{ item.name }} ({{ item.participants.length }})</b-radio>
              </div>
            </div>
          </div>
        </div>

        <div
          class="mb-3 flex-col justify-center items-center flex-1 border-r border-l border-grey-darker overflow-y-hidden pb-3"
        >
          <div class="bg-teal flex w-full items-center py-4 px-4 h-10">
            <p class="text-white mx-auto">{{ $t('shared.who_would_you_like_to_invite') }}</p>
            <div class="flex">
              <button
                class="block mx-auto flex items-center small-border-radius rubik-light text-sm text-white float-right bg-teal-light px-4 py-1"
                @click="inviteToEvent()"
              >
                {{ $t('shared.finish') }}
                <div
                  class="block flex spinner"
                  :class="(language === 'ar') ? 'mr-5' : 'ml-5'"
                  v-if="finishLoading"
                ></div>
              </button>
            </div>
          </div>

          <div
            class="h-full mx-auto flex flex-col px-2 py-4 w-5/6 justify-center items-center overflow-y-hidden"
            style="height:;"
          >
            <div class="flex-col items-center justify-center mb-2">
              <p class="text-center">
                {{ $t('shared.you_have_chosen') }}
                <span
                  class="rubik-bold"
                >{{ this.guestSize }} {{ $t('shared.people') }}</span>
                {{ $t('components.events.to_your_event') }}
                <span
                  class="text-teal rubik-bold"
                >{{ event.title }}</span>
              </p>
            </div>

            <div class="w-5/6">
              <div class="relative mb-2 mt-4">
                <div class="absolute pin-l pin-t mt-3 ml-3 w-5 icon-grey">
                  <img src="/svgs/search.svg" alt />
                </div>

                <input
                  type="search"
                  class="w-full text-sm bg-white rounded border border-grey-dark p-3 text-black"
                  :class="(language === 'ar') ? 'pr-10' : 'pl-10'"
                  :placeholder="$t('shared.search_by_friend')"
                  v-model="searchFieldValue"
                  v-on:keyup.enter="search(searchFieldValue)"
                />
              </div>

              <div class="clearfix block text-center">
                <b-checkbox
                  class="mx-auto mt-2 mb-3 text-sm float-left"
                >{{ $t('shared.invite_all') }}</b-checkbox>
              </div>
            </div>

            <div
              class="w-full px-4 relative"
              style="overflow-y:auto;overflow-x:hidden;display:grid;grid-gap:5px;grid-template-columns: 1fr 1fr;"
            >
              <div
                class="relative flex bg-white rounded text-black p-2 cursor-pointer border hover:border-teal"
                style="max-height:130px;"
                v-for="(profile, index1) in searchResults"
                :key="index1"
                @click="moveToGuestList(profile)"
              >
                <div class="overflow-hidden" style="border-radius:50%;width:50px;height:50px;">
                  <img :src="'/storage/images/' + profile.image_path" alt />
                </div>

                <div class="leading-normal" :class="(language === 'ar') ? 'mr-3' : 'ml-3'">
                  <a class="text-teal rubik-medium" href="#">{{ profile.name }}</a>
                  <p
                    class="text-xs -mt-1"
                    v-for="(item, index2) in profile.educations"
                    :key="index2"
                  >{{ item.name[language] }}</p>

                  <hr />

                  <p class="text-xs">
                    <span class="text-teal">{{profile.mutual_count}}</span>
                    {{ $t('components.profiles.mutual_friends') }}
                  </p>
                  <p
                    class="text-grey-dark text-xs"
                    v-if="profile.location"
                  >{{ profile.location.country.name[language] }}</p>
                </div>

                <!-- <FriendShipInteractionComponent
                  class="absolute"
                  style="bottom:5px;right:5px;"
                  :tempprofile="profile"
                  v-on:friend-request-sent="addFriendRequestSent"
                  v-on:friend-request-accepted="addIsFriend"
                  v-on:friend-request-refused="clearFriendRequest"
                ></FriendShipInteractionComponent>-->
              </div>

              <br />

              <infinite-loading
                class="flex"
                :class="(language === 'ar') ? '-ml-4' : '-mr-4'"
                style
                @infinite="infiniteHandler"
                ref="infiniteLoading"
              >
                <span slot="no-more" class="raleway-bold text-grey-darker"></span>
              </infinite-loading>
            </div>
          </div>
        </div>

        <div class="flex flex-col w-1/4">
          <div class="bg-teal flex w-full items-center py-4 h-10">
            <p class="text-white mx-auto">{{ $t('shared.on_guest_list') }} ({{ guestSize }})</p>
            <!-- <button class :class="(language === 'ar') ? 'ml-4' : 'mr-4'" @click="close()">
              <img class="invert-icon w-4" src="/svgs/close.svg" alt />
            </button>-->
          </div>

          <div class="px-6 py-4 overflow-y-auto">
            <div
              class="flex bg-white rounded text-black p-2 m-1 mb-2 cursor-pointer border hover:border-red"
              v-for="(guest, index) in guestList"
              :key="index"
              @click="removeFromGuestList(guest)"
            >
              <div class="overflow-hidden" style="border-radius:50%;width:50px;height:50px;">
                <img :src="'/storage/images/' + guest.image_path" alt />
              </div>

              <div class="leading-normal" :class="(language === 'ar') ? 'mr-3' : 'ml-3'">
                <a class="text-teal rubik-medium" href="#">{{ guest.name }}</a>
                <p
                  class="text-grey-dark text-xs"
                  v-if="guest.location"
                >{{ guest.location.country.name[language] }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import FriendShipInteractionComponent from "./../../components/friends/FriendShipInteractionComponent.vue";
import InfiniteLoading from "vue-infinite-loading";
import moment from "moment";
import Multiselect from "vue-multiselect";
import closeMixin from "./../../mixins/close.js";
import { mapState, mapMutations, mapActions } from "vuex";

export default {
  mixins: [closeMixin],
  async created() {
    this.$store.dispatch("search/setActiveIndex", "profiles");

    if (this.createEventFromCommunity) {
      this.chosenCommunity = this.community;
    }

    this.$store.dispatch("profiles/getESProfiles", this.page + 1);

    _.forEach(this.myCommunities, community => {
      _.remove(community.participants, {
        username: this.profile.username
      });
    });
  },
  async mounted() {},
  destroyed() {
    this.$store.dispatch("profiles/resetGuestList");
  },
  computed: {
    ...mapState({
      language: state => state.i18n.locale,
      profile: state => state.profiles.signedInProfile,
      profiles: state => state.profiles.profiles,
      ESprofiles: state => state.profiles.ESprofiles,
      guestList: state => state.profiles.guestList,
      event: state => state.events.activeEvent,
      community: state => state.communities.activeCommunity,
      myCommunities: state => state.communities.myCommunities,
      queryResults: state => state.search.searchResults.profiles,
      inviteToResource: state => state.ui.inviteToResource,
      inviteFrom: state => state.ui.inviteFrom,
      createEventFromCommunity: state => state.ui.createEventFromCommunity
    }),
    fitlerResults() {}
  },
  watch: {
    // profiles(value) {
    //   this.searchResults = value;
    // },
    async event(value) {
      if (value) {
        await axios
          .post("/invitations/getInvitations", {
            resource: this.event
          })
          .then(response => {
            _.forEach(response.data.profiles, profile => {
              console.log(profile);
              this.$store.dispatch("profiles/prefillGuestList", profile);

              this.invitedProfiles = {
                ...this.invitedProfiles,
                [profile.id]: profile
              };
            });
          });
      }
    },
    ESprofiles(value) {
      this.searchResults = value;
    },
    queryResults(value) {
      this.searchResults = value;
    },
    guestList(value) {
      this.guestSize = Object.keys(this.guestList).length;
    },
    educationCheck(value) {
      if (value) {
        this.setFilter(value, "education");
      }
    }
  },
  data() {
    return {
      guestSize: 0,
      searchResults: {},
      educationCheck: false,
      profilesCount: 0,
      searchFieldValue: "",
      communitiesPanel: null,
      chosenCommunity: "",
      createdEvent: null,
      loading: false,
      finishLoading: false,
      privacyRadio: null,
      privacyRadio2: null,
      selectedInterest: null,
      page: 0,
      invitedProfiles: {}
    };
  },
  methods: {
    setFilter(string, type) {
      this.privacyRadio = false;

      this.$store.dispatch("search/setFilters", {
        string: string,
        filter_name: type
      });

      this.$store.dispatch("search/search");
    },
    filterResults(item, slug) {
      let that = this;
      this.educationCheck = false;

      if (this.privacyRadio === slug) {
        this.privacyRadio = false;
      }

      let container = item.participants;

      _.remove(container, {
        username: that.profile.username
      });

      this.searchResults = container;
    },
    search(value) {
      this.privacyRadio = false;
      this.educationCheck = false;

      if (value === "") {
        this.searchResults = this.ESprofiles;
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
      this.educationCheck = false;

      this.searchResults = this.profile.friends;
    },
    infiniteHandler($state) {
      this.page = this.page + 1;
      this.$store
        .dispatch("profiles/getESProfiles", {
          page: this.page
        })
        .then(response => {
          if (this.page > Object.keys(this.ESprofiles).length) {
            $state.complete();
          }
          $state.loaded();
        });
    },
    buildCommunitiesArray() {},
    moveToGuestList(profile) {
      // console.log(profile);
      this.$store.dispatch("profiles/moveToGuestList", profile);
    },
    removeFromGuestList(profile) {
      this.$store.dispatch("profiles/removeFromGuestList", profile);
    },
    async inviteToEvent() {
      this.finishLoading = true;
      if (this.inviteToResource === "events") {
        await axios
          .post("/events/invite", {
            event_slug: this.event.slug,
            guest_list: this.guestList
          })
          .then(response => {
            this.$store.dispatch("events/addMyEvent", response.data.event);
            this.finishLoading = false;
            this.$emit("invite:finish");
          })
          .catch(error => {
            this.finishLoading = false;
          });
      } else if (this.inviteToResource === "communities") {
        await axios
          .post("/communities/invite", {
            community_slug: this.community.slug,
            guest_list: this.guestList
          })
          .then(response => {
            this.$store.dispatch(
              "communities/addMyCommunity",
              response.data.community
            );
            this.finishLoading = false;
            this.$emit("invite:finish");
          })
          .catch(error => {
            this.finishLoading = false;
          });
      }
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
    moment,
    Multiselect,
    FriendShipInteractionComponent,
    InfiniteLoading
  }
};
</script>

<style lang="scss" scoped>
</style>
