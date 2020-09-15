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
          <div class="flex items-center">
            <img class="invert-icon w-6" src="/svgs/trash.svg" alt />
            <p
              class="text-white text-lg"
              :class="(language === 'ar') ? 'mr-2' : 'ml-2'"
            >{{ $t('components.profiles.blocked_profiles') }}</p>
          </div>

          <button class :class="(language === 'ar') ? 'ml-4' : 'mr-4'" @click="close()">
            <img class="invert-icon w-4" src="/svgs/close.svg" alt />
          </button>
        </div>

        <div class="w-full mb-4 py-4 pb-6 px-10">
          <div class="relative mb-2 mt-4">
            <div
              class="absolute pin-t mt-3 w-5 icon-grey"
              :class="(language === 'ar') ? 'pin-r mr-6' : 'pin-l ml-6'"
            >
              <img src="/svgs/search.svg" alt />
            </div>

            <input
              type="search"
              class="w-full text-sm bg-white rounded border border-grey-dark p-3 text-black"
              :class="(language === 'ar') ? 'pr-16' : 'pl-16'"
              :placeholder="$t('components.profiles.blocked_profiles')"
            />
          </div>

          <div
            class="overflow-auto flex flex-col"
            :class="(language === 'ar') ? 'pl-4' : 'pr-4'"
            style="max-height:500px;"
          >
            <div
              class="relative bg-white w-full flex border border-white rounded-sm px-6 py-4 text-teal items-center justify-start mb-4"
              v-for="(item, index) in blockedProfiles"
              :key="index"
            >
              <img
                class="rounded-full h-16 w-16 flex items-center justify-center border border-grey-dark"
                :class="(language === 'ar') ? 'ml-2' : 'mr-2'"
                :src="'/storage/images/' + item.recipient.image_path"
                alt
              />

              <div class="flex-1" :class="(language === 'ar') ? 'mr-3' : 'ml-3'">
                <a :href="'/profiles/' + item.recipient.username">
                  <p class="text-teal rubik-medium text-sm">{{ item.recipient.name }}</p>
                </a>

                <p
                  class="text-xs text-grey-darkest rubik-light"
                  v-if="item.recipient.location"
                >{{ item.recipient.location.name }}</p>

                <hr />
              </div>

              <button
                class="absolute text-sm bg-teal-light flex text-white border px-4 py-2 border-teal-light hover:bg-white hover:text-teal"
                style="top:10px;right:20px;"
                @click="unblock(item.recipient)"
              >{{ $t('components.profiles.unblock')}}</button>

              <!-- <FriendShipInteractionComponent
                class="absolute"
                style="top:10px;right:20px;"
                :tempprofile="item.recipient"
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
      language: state => state.i18n.locale,
      blockedProfiles: state => state.profiles.blockedProfiles
    })
  },
  created() {

  },
  mounted() {
    $(".invite-target").click(function() {
      $(".invite-target").removeClass(["text-teal"]);
      $(this).addClass(["text-teal"]);
    });
  },
  data() {
    return {};
  },
  methods: {
    async unblock(profile) {
      this.loading = true;

      await axios
        .post("/profile/" + profile.username + "/unblock", {
          username: profile.username
        })
        .then(response => {
          this.$toast.open({
            duration: 5000,
            message: profile.name + ` unblocked.`,
            position: "is-bottom",
            type: "is-success"
          });

          Vue.delete(this.blockedProfiles, profile.id);
        })
        .catch(error => {
          this.loading = false;
        });
    },
    addFriendRequestSent(profileId) {
      this.$store.dispatch("communities/addFriendRequestSent", profileId);
    },
    addIsFriend(profileId) {
      this.$store.dispatch("communities/addIsFriend", profileId);
    },
    clearFriendRequest(profileId) {
      this.$store.dispatch("communities/clearFriendRequest", profileId);
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
