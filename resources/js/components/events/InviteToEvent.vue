<template>
  <div class="relative mx-6 md:mx-auto w-full lg:w-4/5 z-20">
    <div class="flex shadow-lg bg-grey rounded text-grey-darkest" style="height:70vh">
      <div class="flex-col w-1/4 border-r border-grey-darker">
        <div class="bg-teal flex w-full items-center pl-10 py-4 h-10">
          <p class="text-white">Categories?</p>
        </div>

        <div class="flex-col pl-10 py-4">
          <div class="flex-col items-start justify-start text-grey-darkest text-sm">
            <label class="block rubik-regular mb-1" for="firstName">General</label>

            <div class="mb-3">
              <b-radio class="block" native-value="All Friends (452)">All Friends (452)</b-radio>
            </div>
            <div>
              <b-radio class="block" native-value="UC Berkeley (52)">UC Berkeley (52)</b-radio>
            </div>
          </div>

          <div class="flex-col items-start justify-start text-grey-darkest text-sm mt-8">
            <label class="block rubik-regular mb-1" for="firstName">My Communities</label>

            <div class="mb-3">
              <b-radio class native-value="French Speakers (115)">French Speakers (115)</b-radio>
            </div>
            <div>
              <b-radio
                class
                native-value="Gabonians Worldwide of... (92)"
              >Gabonians Worldwide of... (92)</b-radio>
            </div>
          </div>
        </div>
      </div>

      <div class="flex-col justify-center items-center flex-1 border-r border-grey-darker">
        <div class="bg-teal flex w-full items-center py-4 px-4 h-10">
          <p class="text-white mx-auto">Who would you like to invite?</p>
          <div class="flex">
            <button
              class="block mx-auto flex small-border-radius rubik-light text-sm text-white float-right bg-teal-light px-4 py-1"
              @click="inviteToEvent()"
            >
              Invite
              <div class="block flex ml-5 spinner" v-if="finishLoading"></div>
            </button>
          </div>
        </div>

        <div class="mx-auto flex flex-col px-6 py-4 w-5/6 justify-center items-center">
          <div class="flex-col items-center justify-center mb-2">
            <p class="text-center">
              You have chosen to invite
              <span class="rubik-bold">{{ this.guestSize }} people</span> to your event
              <span class="text-teal rubik-bold">{{ event.title }}</span>
            </p>
          </div>

          <div class="w-5/6">
            <div class="relative mb-2 mt-4">
              <div class="absolute pin-l pin-t mt-3 ml-3 w-5 icon-grey">
                <img src="/svgs/search.svg" alt />
              </div>

              <input
                type="search"
                class="w-full text-sm bg-white rounded border border-grey-dark p-3 pl-10 text-black"
                placeholder="Search by friend"
              />
            </div>

            <div class="clearfix block text-center">
              <b-checkbox class="mx-auto mt-2 mb-3 text-sm float-left">Invite All</b-checkbox>
            </div>
          </div>

          <div
            class="flex flex-wrap justify-between w-full content-start"
            style="height:48vh;overflow-y:auto;"
          >
            <div
              class="flex bg-white rounded text-black p-2 m-1 cursor-pointer border hover:border-teal"
              v-for="(profile, index) in notInvited"
              :key="profile.id"
              @click="moveToGuestList(index)"
            >
              <div class>
                <img class :src="'/storage/images/' + profile.image_path" alt style="height:100px;" />
              </div>

              <div class="ml-3 leading-normal relative">
                <a
                  class="text-teal rubik-medium"
                  :href="'/profiles/' + profile.username"
                  target="_blank"
                >{{ profile.name }}</a>
                <p class="text-xs -mt-1">Engineer at Baker Hughes</p>

                <hr />

                <p class="text-xs">
                  <span class="text-teal">3</span> mutual friends
                </p>

                <p class="text-grey-dark text-xs">Los Angeles USA</p>

                <FriendShipInteractionComponent
                  class="absolute"
                  style="bottom:0;right:0px;"
                  :tempprofile="profile"
                ></FriendShipInteractionComponent>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="flex flex-col w-1/4">
        <div class="bg-teal flex w-full items-center py-4 h-10">
          <p class="text-white mx-auto">On guest list ({{ guestSize }})</p>
          <button class="mr-4" @click="close()">
            <img class="invert-icon w-4" src="/svgs/close.svg" alt />
          </button>
        </div>

        <div class="px-6 py-4 overflow-y-auto">
          <div
            class="flex bg-white rounded text-black p-2 m-1 mb-2 cursor-pointer border hover:border-red"
            v-for="(guest, index) in guestList"
            :key="guest.id"
            @click="removeFromGuestList(index)"
          >
            <div class>
              <img
                class="rounded-full"
                :src="'/storage/images/' + profile.image_path"
                alt
                style="height:50px;"
              />
            </div>

            <div class="ml-3 leading-normal">
              <a
                class="text-teal rubik-medium"
                :href="'/profiles/' + guest.username"
                target="_blank"
              >{{ guest.name }}</a>
              <p class="text-grey-dark text-xs">Los Angeles USA</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import moment from "moment";
import { mapState, mapMutations, mapActions } from "vuex";
import Multiselect from "vue-multiselect";
import FriendShipInteractionComponent from "./../../components/friends/FriendShipInteractionComponent.vue";

export default {
  props: [],
  computed: {
    ...mapState({
      profile: state => state.profiles.signedInProfile,
      event: state => state.events.activeEvent,
      profiles: state => state.events.profilesThatCanBeInvited
    })
  },
  created() {
    this.notInvited = this.profiles;

    axios.get("/event/test").then(response => {
      this.$store.dispatch(
        "events/setProfilesThatCanBeInvited",
        response.data.profiles_that_can_be_invited.data
      );
    });
  },
  watch: {
    guestList(value) {
      this.guestSize = Object.keys(this.guestList).length;
    }
  },
  data() {
    return {
      notInvited: {},
      guestSize: 0,
      guestList: {},
      loading: false,
      finishLoading: false
    };
  },

  methods: {
    moveToGuestList(id) {
      this.guestList = {
        ...this.guestList,
        [id]: this.notInvited[id]
      };

      Vue.delete(this.notInvited, id);
    },
    removeFromGuestList(id) {
      this.notInvited = {
        ...this.notInvited,
        [id]: this.guestList[id]
      };

      Vue.delete(this.guestList, id);
    },
    async inviteToEvent() {
      this.finishLoading = true;
      await axios
        .post("/events/invite", {
          event_slug: this.event.slug,
          guest_list: this.guestList
        })
        .then(response => {
          this.finishLoading = false;

          this.$emit("event:invite");
        })
        .catch(error => {
          this.finishLoading = false;
        });
    },
    close() {
      this.$emit("modal:close");
    }
  },
  components: { moment, Multiselect, FriendShipInteractionComponent }
};
</script>

<style lang="scss" scoped>
// @import ~'buefy/lib/buefy.css';
</style>
