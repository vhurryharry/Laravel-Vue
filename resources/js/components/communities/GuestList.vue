<template>
  <div class="fixed pin flex items-center" style>
    <div class="fixed pin bg-black opacity-50 z-10"></div>

    <div
      class="relative mx-6 md:mx-auto w-full md:w-1/2 lg:w-1/3 z-20 m-8"
      style="max-height:600px;"
    >
      <div class="shadow-lg bg-grey text-grey-darkest" style>
        <div
          class="flex items-center bg-teal justify-between py-2"
          :class="(language === 'ar') ? 'pr-10' : 'pl-10'"
        >
          <p class="text-white">{{ $t('shared.guest_list') }}</p>

          <button class :class="(language === 'ar') ? 'ml-4' : 'mr-4'" @click="close()">
            <img class="invert-icon w-4" src="/svgs/close.svg" alt />
          </button>
        </div>

        <div class="w-full mb-4 py-4 pb-6 px-10">
          <div class="flex justify-end">
            <p
              class="text-xs cursor-pointer text-teal rubik-light invite-target"
              :class="(language === 'ar') ? 'ml-4' : 'mr-4'"
              @click="showAttending"
            >{{ $t('shared.attending') }}</p>
            <p
              class="text-xs cursor-pointer text-grey-darker rubik-light invite-target"
              @click="showInvited"
            >{{ $t('shared.invited') }}</p>
          </div>

          <div class="relative mb-2 mt-4">
            <div class="absolute pin-l pin-t mt-3 ml-6 w-5 icon-grey">
              <img src="/svgs/search.svg" alt />
            </div>

            <input
              v-model="searchValue"
              type="search"
              class="w-full text-sm bg-white rounded border border-grey-dark p-3 pl-16 text-black"
              :placeholder="$t('shared.search_guest_list')"
            />
          </div>

          <div
            class="overflow-auto flex flex-col"
            :class="(language === 'ar') ? 'pl-4' : 'pr-4'"
            style="max-height:500px;"
            v-if="active == 'attending'"
          >
            <div
              class="relative bg-white w-full flex border border-white rounded-sm px-6 py-4 text-teal items-center justify-start mb-4"
              v-for="(item, index) in searchFilter"
              :key="index"
            >
              <img
                class="rounded-full h-16 w-16 flex items-center justify-center border border-grey-dark"
                :class="(language === 'ar') ? 'ml-2' : 'mr-2'"
                :src="'/storage/images/' + item.image_path"
                alt
              />

              <div class="flex-1" :class="(language === 'ar') ? 'mr-3' : 'ml-3'">
                <a :href="'/profiles/' + item.username">
                  <p class="text-teal rubik-medium text-sm">{{ item.name }}</p>
                </a>

                <p
                  class="text-xs text-grey-darkest rubik-light"
                  v-if="item.location"
                >{{ item.location.country.name[language] }}</p>

                <hr />

                <p class="text-xs raleway-semibold text-black">
                  {{ $t('components.profiles.friends') }}:
                  <span
                    class="text-teal"
                  >{{ item.friend_count }}</span>
                </p>
              </div>

              <FriendShipInteractionComponent
                class="absolute"
                style="top:10px;right:20px;"
                :tempprofile="item"
                v-on:friend-request-sent="addFriendRequestSent"
                v-on:friend-request-accepted="addIsFriend"
                v-on:friend-request-refused="clearFriendRequest"
              ></FriendShipInteractionComponent>
            </div>
          </div>

          <div
            v-else
            class="overflow-auto flex flex-col"
            :class="(language === 'ar') ? 'pl-4' : 'pr-4'"
            style="max-height:500px;"
          >
            <div
              class="relative bg-white w-full flex border border-white rounded-sm px-6 py-4 text-teal items-center justify-start mb-4"
              v-for="(item, index) in searchFilter"
              :key="index"
            >
              <img
                class="rounded-full h-16 w-16 flex items-center justify-center border border-grey-dark"
                :class="(language === 'ar') ? 'ml-2' : 'mr-2'"
                :src="'/storage/images/' + item.image_path"
                alt
              />

              <div class="flex-1" :class="(language === 'ar') ? 'mr-3' : 'ml-3'">
                <a :href="'/profiles/' + item.username">
                  <p class="text-teal rubik-medium text-sm">{{ item.name }}</p>
                </a>
                <p
                  class="text-xs text-grey-darkest rubik-light"
                  v-if="item.location"
                >{{ item.location.country.name[language] }}</p>

                <hr />

                <p class="text-xs raleway-semibold text-black">
                  {{ $t('components.profiles.friends') }}:
                  <span
                    class="text-teal"
                  >{{ item.friend_count }}</span>
                </p>
              </div>

              <!-- <img
                class="absolute pin-t pin-r mt-4 mr-2 text-teal w-6 icon-teal"
                src="/svgs/check.svg"
                alt
              >-->
              <!-- <FriendShipInteractionComponent
                class="absolute"
                style="top:0;right:20px;"
                :tempprofile="item"
                v-on:friend-request-sent="addFriendRequestSent"
                v-on:friend-request-accepted="addIsFriend"
                v-on:friend-request-refused="clearFriendRequest"
              ></FriendShipInteractionComponent>-->
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState, mapMutations, mapActions } from "vuex";
import FriendShipInteractionComponent from "./../../components/friends/FriendShipInteractionComponent.vue";
import closeMixin from "./../../mixins/close.js";

export default {
  mixins: [closeMixin],
  props: ["for"],
  computed: {
    ...mapState({
      profile: state => state.profiles.signedInProfile,
      community: state => state.communities.activeCommunity,
      event: state => state.events.activeEvent,
      language: state => state.i18n.locale
    }),
    searchFilter() {
      if (this.active === "attending") {
        if (this.searchValue !== "") {
          return _.filter(
            this.community.participants || this.event.participants,
            element => {
              return element["name"]
                .toLowerCase()
                .includes(this.searchValue.toLowerCase());
            }
          );
        } else {
          return this.community.participants || this.event.participants;
        }
      } else {
        if (this.searchValue !== "") {
          return _.filter(this.invitedProfiles, element => {
            return element["name"]
              .toLowerCase()
              .includes(this.searchValue.toLowerCase());
          });
        } else {
          return this.invitedProfiles;
        }
      }
    }
  },
  async created() {
    await axios
      .post("invitations/getInvitations", {
        resource: this.for == "events" ? this.event : this.community
      })
      .then(response => {
        _.forEach(response.data.profiles, profile => {
          this.invitedProfiles = {
            ...this.invitedProfiles,
            [profile.id]: profile
          };
        });
      });
  },
  mounted() {
    $(".invite-target").click(function() {
      $(".invite-target").removeClass(["text-teal"]);
      $(this).addClass(["text-teal"]);
    });
  },
  data() {
    return {
      searchValue: "",
      invitedProfiles: {},
      active: "attending"
    };
  },
  methods: {
    addFriendRequestSent(profileId) {
      this.$store.dispatch("communities/addFriendRequestSent", profileId);
    },
    addIsFriend(profileId) {
      this.$store.dispatch("communities/addIsFriend", profileId);
    },
    clearFriendRequest(profileId) {
      this.$store.dispatch("communities/clearFriendRequest", profileId);
    },
    showAttending() {
      this.searchValue = "";
      this.active = "attending";
    },
    showInvited() {
      this.active = "invited";
    },
    close() {
      this.$emit("modal:close");
    }
  },
  components: { FriendShipInteractionComponent }
};
</script>

<style lang="scss" scoped>
</style>
