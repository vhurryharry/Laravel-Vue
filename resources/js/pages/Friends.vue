<template>
  <div class="flex page">
    <div class="bg-grey pt-6 px-24 flex-1">
      <p class="mb-2 text-xl text-teal rubik-medium">{{ $t('components.profiles.friends') }}</p>

      <div class="page-content-container flex content-start flex-wrap">
        <b-loading :is-full-page="false" :active.sync="isLoading" :can-cancel="false"></b-loading>

        <div
          class="flex bg-white text-black small-border-radius p-2 mb-8"
          :class="(language === 'ar') ? 'ml-8' : 'mr-8'"
          style="width:30%;"
          v-for="(friend, index) in friends"
          :key="index"
        >
          <div class="rounded overflow-hidden text-center flex justify-center items-center">
            <img class style="max-width:115px;" :src="'/storage/images/' + friend.image_path" alt />
          </div>

          <div class="leading-normal flex-1" :class="(language === 'ar') ? 'mr-3' : 'ml-3'">
            <div class="mt-3">
              <a
                class="text-teal rubik-medium"
                :href="'/profiles/' + friend.username"
              >{{ friend.name }}</a>

              <hr />

              <p class="text-xs">
                <span class="text-teal">{{ friend.mutual_count }}</span>
                {{ $t('components.profiles.mutual_friends') }}
                <span
                  v-if="(friend.mutual_count > 1) || (friend.mutual_count == 0) && language !== 'ar'"
                >s</span>
              </p>

              <p
                class="text-grey-dark text-xs"
                v-if="friend.location"
              >{{ friend.location.country.name[language] }}</p>

              <a :href="'/chat/' + friend.username">
                <button
                  class="block bg-teal mt-4 px-4"
                  :class="(language === 'ar') ? 'float-left' : 'float-right'"
                >
                  <!-- <img class="invert-icon hover:icon-red w-5 mt-1" src="/svgs/chat.svg" alt /> -->

                  <font-awesome-icon
                    class="icon text-white cursor-pointer hover:text-teal-light"
                    :icon="['fa', 'comment']"
                    size="lg"
                  />
                </button>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="main-sidebar bg-teal w-1/3 -mt-1 relative z-0 h-full">
      <div class="flex flex-col px-10 pt-6" style="height:80.5vh;overflow:auto;">
        <p class="text-xl rubik-medium mb-2">{{ $t('components.profiles.invite_friends') }}</p>

        <p class="text-xl rubik-medium mb-2">{{ $t('components.profiles.make_new_friends') }}</p>

        <flickity
          v-if="Object.keys(profiles).length > 0"
          class="carousel"
          ref="flickity"
          :options="flickityOptions"
        >
          <div
            class="carousel-cell flex flex-col bg-white text-black w-1/2 p-4 px-6 small-border-radius"
            :class="(language === 'ar') ? 'ml-4' : 'mr-4'"
            v-for="(profile, index) in profiles"
            :key="index"
            style="min-height:305px;"
          >
            <div style="width:auto;max-height:130px;overflow:hidden;">
              <img class="rounded" :src="'/storage/images/' + profile.image_path" alt />
            </div>

            <div class="leading-normal mt-2 relative">
              <a
                class="text-teal rubik-medium"
                :href="'/profiles/' + profile.username"
              >{{ profile.name }}</a>
              <hr />

              <p class="text-xs">
                <span class="text-black">
                  <span class="text-teal">{{ profile.mutual_count }}</span>
                  {{ $t('components.profiles.mutual_friends') }}
                  <span
                    v-if="(profile.mutual_count > 1) || (profile.mutual_count == 0) && language !== 'ar'"
                  >s</span>
                </span>
              </p>

              <p
                class="text-grey-dark text-xs"
                v-if="profile.location"
              >{{ profile.location.country.name[language] }}</p>
            </div>

            <div
              class="flex bg-white justify-end items-center mt-4 mb-4 cursor-default absolute pin-b"
              :class="(language === 'ar') ? 'ml-4  pin-l' : 'mr-4  pin-r'"
            >
              <!-- <img
                class="icon-teal w-8 cursor-pointer"
                src="/svgs/add_profile.svg"
                alt
                @click="sendFriendRequest(profile.username, profile.id)"
                v-if="!profile.friend_request_sent && !profile.has_friend_request_from"
              />-->

              <font-awesome-icon
                class="icon text-teal-light cursor-pointer hover:text-teal"
                :icon="['fa', 'user-plus']"
                size="lg"
                v-if="!profile.friend_request_sent && !profile.has_friend_request_from && !profile.is_friend"
                @click="sendFriendRequest(profile.username, profile.id)"
              />

              <span class="flex" v-else-if="profile.has_friend_request_from">
                <!-- <img
                  class="icon-teal w-6 cursor-pointer"
                  :class="(language === 'ar') ? 'ml-2' : 'mr-2'"
                  src="/svgs/check.svg"
                  alt
                  @click="acceptRequest(profile.username, profile.id)"
                />-->

                <font-awesome-icon
                  class="icon text-teal-light cursor-pointer hover:text-teal"
                  :class="(language === 'ar') ? 'ml-2' : 'mr-2'"
                  :icon="['fa', 'check']"
                  size="lg"
                  @click="acceptRequest(profile.username, profile.id)"
                />

                <font-awesome-icon
                  class="icon text-red cursor-pointer hover:text-black"
                  :icon="['fa', 'times']"
                  size="lg"
                  @click="refuseRequest(profile.username, profile.id)"
                />
              </span>

              <span class="flex" v-if="profile.friend_request_sent">
                <button
                  class="text-sm bg-teal-light flex text-white border px-2 py-2 border-teal-light hover:bg-white hover:text-teal"
                  :class="(language === 'ar') ? 'ml-2' : 'mr-2'"
                >{{ $t('shared.pending') }}</button>
              </span>
            </div>
          </div>
        </flickity>

        <a href="/search-page#friends-controls" class="ml-auto my-4 rubik-medium text-sm">
          {{ $t('components.profiles.display_all_profiles') }} &nbsp;
          >
        </a>
      </div>

      <footer-component></footer-component>
    </div>
  </div>
</template>

<script>
import Flickity from "vue-flickity";
import { mapState, mapMutations, mapActions } from "vuex";

export default {
  computed: {
    ...mapState({
      language: state => state.i18n.locale
    })
  },
  async created() {
    this.isLoading = true;

    await axios.get("/friends/all").then(response => {
      this.friends = response.data.friends;
      this.profiles = response.data.profiles;

      this.isLoading = false;
    });
  },
  mounted() {},

  data() {
    return {
      isLoading: true,
      friends: {},
      profiles: {},
      flickityOptions: {
        initialIndex: 3,
        prevNextButtons: true,
        pageDots: false,
        wrapAround: true,
        resize: true
      }
    };
  },

  methods: {
    sendFriendRequest(to, profileId) {
      axios
        .post("/profile/" + to, {
          username: to
        })
        .then(response => {
          this.friend_request_sent = true;
          this.$toast.open({
            duration: 5000,
            message: `Friend request sent.`,
            position: "is-bottom",
            type: "is-success"
          });

          _.forEach(this.profiles, function(value, key) {
            if (value.id === profileId) {
              value.friend_request_sent = true;
            }
          });
        })
        .catch(error => {});
    },
    refuseRequest(senderUsername, profileId) {
      axios
        .post("/requests/refuse", {
          username: senderUsername
        })
        .then(response => {
          this.$toast.open({
            duration: 5000,
            message: `Friend request refused.`,
            position: "is-bottom",
            type: "is-success"
          });

          _.forEach(this.profiles, function(value, key) {
            if (value.id === profileId) {
              value.has_friend_request_from = false;
            }
          });
        })
        .catch(error => {
          this.loading = false;
        });
    },
    acceptRequest(senderUsername, profileId) {
      let that = this;
      axios
        .post("/requests/accept", {
          username: senderUsername
        })
        .then(response => {
          this.$toast.open({
            duration: 5000,
            message: `Friend request accepted.`,
            position: "is-bottom",
            type: "is-success"
          });

          _.forEach(this.profiles, function(value, key) {
            if (value.id === profileId) {
              that.friends = {
                ...that.friends,
                [value.id]: value
              };
            }
          });

          Vue.delete(this.profiles, profileId);
        })
        .catch(error => {
          this.loading = false;
        });
    },
    flickityResize(profileId) {
      this.$refs.flickity.resize();
      this.$refs.flickity.rerender();
      this.$refs.flickity.reloadCells();
    }
  },
  components: {
    Flickity
  }
};
</script>

<style lang="scss" scoped>
.friend-item {
  display: flex;
  max-height: 200px;
  background: white;
  border-radius: 3px;
  color: black;
  margin: 0 1.5% 1.5% 0;
  padding: 20px 15px;
}
</style>
