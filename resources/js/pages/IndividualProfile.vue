<template>
  <div class="flex page">
    <div class="bg-grey pt-12 flex-1" :class="(language === 'ar') ? 'pr-24' : 'pl-24'">
      <div
        class="page-content-container flex-col text-black"
        :class="(language === 'ar') ? 'pl-6' : 'pr-6'"
      >
        <div class="bg-white w-full flex h-80 profile-card m-auto mb-6">
          <div class="placeholder-image w-1/4 bg-grey-darker" alt v-if="!profileImage"></div>

          <div
            class="h-full max-w-xs overflow-hidden flex justify-center px-4"
            style="max-width:250px;"
            slot="trigger"
            v-else
          >
            <img class="object-contain" :src="'/storage/images/' + profile.image_path" alt />
          </div>

          <div class="flex flex-1 justify-center items-center">
            <div class="flex-col w-full">
              <div class="flex px-4 py-3">
                <p class="text-teal rubik-regular">{{ $t('components.settings.name')}} :</p>
                <p class="rubik-light mx-4">{{ profile.name }}</p>
              </div>

              <div class="flex px-4 py-3">
                <p class="text-teal rubik-regular">{{ $t('components.settings.the_location')}}:</p>
                <p
                  class="mx-4 rubik-light"
                  v-if="profile.location && !profile.location.hidden"
                >{{ profile.location.country.name[language] }}</p>
              </div>

              <div class="flex px-4 py-3">
                <p class="text-teal rubik-regular">{{ $t('components.settings.birthday')}}:</p>

                <p
                  class="mx-4 rubik-light"
                  v-if="profile.date_of_birth"
                >{{ moment(profile.date_of_birth).format('MMMM DD, YYYY') }}</p>
              </div>

              <div class="flex px-4 py-3">
                <p class="text-teal rubik-regular">{{ $t('components.settings.education')}}:</p>

                <div class="flex mx-4">
                  <p
                    class="rubik-light"
                    v-for="(education, index) in profile.educations"
                    :key="index"
                  >
                    <span v-if="!education.pivot.hidden">
                      {{ education.name[language] }}
                      <span
                        v-if="index != (Object.keys(profile.educations).length - 1)"
                      >,&nbsp;</span>
                    </span>
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="flex justify-between border-b border-teal-light pb-3 mb-2 items-center">
          <p class="text-teal text-lg rubik-bold w-1/2">{{ profile.name }}</p>

          <div class="flex items-center">
            <button
              class="bg-teal-light flex text-white border px-4 py-2 border-teal-light hover:bg-white hover:text-teal"
              :class="(language === 'ar') ? 'ml-2' : 'mr-2'"
              v-if="is_friend"
            >{{ $t('components.profiles.are_friends')}}</button>

            <b-tooltip
              class="cursor-pointer"
              :label="$t('Send friend request')"
              position="is-top"
              animated
            >
              <button
                class="bg-teal-light border-teal-light p-2"
                :class="(language === 'ar') ? 'float-left ml-2' : 'float-right mr-2'"
                @click="sendFriendRequest"
                v-if="!is_friend & !friend_request_sent & !has_friend_request_from & !owner"
              >
                <!-- <img class="invert-icon w-8 mt-1" src="/svgs/add_profile.svg" alt /> -->

                <font-awesome-icon
                  class="icon text-white cursor-pointer hover:text-teal-darker"
                  :icon="['fa', 'user-plus']"
                  size="lg"
                />
              </button>
            </b-tooltip>

            <b-tooltip
              class="cursor-pointer"
              :label="$t('Cancel friend request')"
              position="is-top"
              animated
            >
              <button
                class="bg-teal-light flex text-white border px-4 py-2 border-teal-light hover:bg-white hover:text-teal"
                :class="(language === 'ar') ? 'ml-2' : 'mr-2'"
                v-if="friend_request_sent"
                @click="cancelFriendRequest"
              >{{ $t('shared.sent')}}</button>
            </b-tooltip>

            <button
              class="bg-teal-light flex text-white border px-4 py-2 border-teal-light hover:bg-white hover:text-teal"
              :class="(language === 'ar') ? 'ml-2' : 'mr-2'"
              v-if="has_friend_request_from"
            >{{ $t('shared.pending')}}</button>

            <b-dropdown
              class="block bg-teal-dark regular-border-radius"
              :position="(language === 'ar') ? 'is-bottom-right' : 'is-bottom-left'"
              hoverable
              v-if="is_friend"
            >
              <button
                class="bg-teal-light px-4 border-teal-light"
                :class="(language === 'ar') ? 'float-left' : 'float-right'"
                slot="trigger"
              >
                <font-awesome-icon
                  class="icon text-white cursor-pointer hover:text-teal-darker"
                  :icon="['fa', 'check']"
                  size="lg"
                />

                <!-- <img class="invert-icon w-5 mt-1" src="/svgs/check.svg" alt /> -->
              </button>

              <b-dropdown-item
                class="cursor-pointer"
                @click="openReport(profile, 'profiles')"
              >{{ $t('shared.report')}}</b-dropdown-item>

              <b-dropdown-item
                v-if="!is_blocked"
                class="cursor-pointer"
                @click="block"
              >{{ $t('components.profiles.block')}}</b-dropdown-item>

              <b-dropdown-item
                v-else
                class="cursor-pointer"
                @click="unblock"
              >{{ $t('components.profiles.unblock')}}</b-dropdown-item>

              <b-dropdown-item
                class="cursor-pointer"
                @click="unfriend"
              >{{ $t('components.profiles.unfriend')}}</b-dropdown-item>
            </b-dropdown>

            <b-dropdown
              v-else-if="!owner"
              class="block bg-teal-dark regular-border-radius"
              :position="(language === 'ar') ? 'is-bottom-right' : 'is-bottom-left'"
              hoverable
            >
              <button
                class="bg-grey-dark px-4 border-teal-light"
                :class="(language === 'ar') ? 'float-left' : 'float-right'"
                slot="trigger"
              >
                <font-awesome-icon
                  class="icon text-white cursor-pointer hover:text-teal-darker"
                  :icon="['fa', 'check']"
                  size="lg"
                />

                <!-- <img class="invert-icon w-5 mt-1" src="/svgs/check.svg" alt /> -->
              </button>

              <b-dropdown-item
                class="cursor-pointer"
                @click="openReport(profile, 'profiles')"
              >{{ $t('shared.report') }}</b-dropdown-item>
              <b-dropdown-item
                v-if="!is_blocked"
                class="cursor-pointer"
                @click="block"
              >{{ $t('components.profiles.block') }}</b-dropdown-item>

              <b-dropdown-item
                v-else
                class="cursor-pointer"
                @click="unblock"
              >{{ $t('components.profiles.unblock')}}</b-dropdown-item>
            </b-dropdown>

            <a :href="'/chat/' + profile.username" v-if="is_friend">
              <button
                class="bg-teal-light px-4 border-teal-light"
                :class="(language === 'ar') ? 'float-left mr-2' : 'float-right ml-2'"
              >
                <font-awesome-icon
                  class="icon text-white cursor-pointer hover:text-teal-darker"
                  :icon="['fa', 'comment']"
                  size="lg"
                />
                <!-- <img class="invert-icon w-5 mt-1" src="/svgs/chat.svg" alt /> -->
              </button>
            </a>
          </div>
        </div>

        <p class="rubik-regular text-sm">About: {{ profile.bio }}</p>

        <div class="w-full flex my-2">
          <b-tooltip
            class="cursor-pointer"
            :class="(language === 'ar') ? 'mr-auto' : 'ml-auto'"
            :label="$t('shared.report')"
            position="is-top"
            animated
            @click.native="openReport(profile, 'profiles')"
          >
            <!-- <img class src="/svgs/question_mark.svg" alt /> -->

            <font-awesome-icon
              class="icon text-teal cursor-pointer w-full"
              :icon="['fa', 'question-circle']"
              size="lg"
            />
          </b-tooltip>
        </div>

        <div class="flex bg-teal text-white">
          <div class="w-auto items-center py-5 px-8">
            <span class="text-lg rubik-regular">{{ $t('shared.activity')}}</span>
          </div>
        </div>

        <!-- Post item -->
        <div
          class="bg-white py-3 px-8 mb-4 pb-4 flex flex-col items-center relative text-black"
          v-for="(item, index) in friendshipActivity"
          :key="index"
        >
          <div class="flex items-center justify-between w-full">
            <div class="flex items-center justify-between">
              <img
                class="rounded-full border border-grey w-12"
                :src="'/storage/images/' + profile.image_path"
                alt
              />
              <span class="ml-2 mt-3" :class="(language === 'ar') ? 'mr-2' : 'ml-2'">
                <span class="text-teal rubik-medium text-sm">{{ profile.name }}</span>
                <!-- <span class="rubik-regular text-xs">({{ $t('components.profiles.new_friendship')}})</span> -->
                <span class="rubik-regular text-xs">({{ item.activity_value.name[language]}} )</span>
              </span>
            </div>

            <p
              class="mt-3 rubik-regular text-xs text-grey-darker"
              :class="(language === 'ar') ? 'ml-3' : 'mr-3'"
            >{{ moment(item.created_at).format('MMMM DD') }}</p>
          </div>

          <hr class="post-horizontal-line bg-teal-light" />
          <div class="w-full flex-col flex-wrap text-center">
            <div class="py-1">
              <img
                class="rounded-full border border-grey w-24 mx-6"
                :src="'/storage/images/' + profile.image_path"
                alt
              />
              <img
                class="rounded-full border border-grey w-24 mx-6"
                :src="'/storage/images/' + item.target.image_path"
                alt
              />
            </div>
            <p class="text-base">
              <span class="rubik-bold">{{ profile.name }}</span>
              {{ $t('shared.and') }}
              <span class="rubik-bold">{{ item.target.name }}</span>
              {{ $t('components.profiles.became_friends') }}
            </p>
          </div>

          <!-- <b-tooltip
            class="ml-auto cursor-pointer"
            :label="$t('shared.report')"
            position="is-left"
            animated
          >
            <img class src="/svgs/question_mark.svg" alt />
          </b-tooltip>-->
        </div>

        <!-- Post item -->
        <div
          class="bg-white py-3 px-8 mb-4 pb-4 flex flex-col items-center relative text-black"
          v-for="(item, index) in resourceActivity"
          :key="index"
        >
          <div class="flex items-center justify-between w-full">
            <div class="flex items-center justify-between">
              <img
                class="rounded-full border border-grey w-12"
                :src="'/storage/images/' + profile.image_path"
                alt
              />
              <span class="mt-3" :class="(language === 'ar') ? 'mr-2' : 'ml-2'">
                <span class="text-teal rubik-medium text-sm">{{ profile.name }}</span>
                <span class="rubik-regular text-xs">({{ item.activity_value.name[language]}} )</span>
              </span>
            </div>
            <p
              class="mt-3 rubik-regular text-xs text-grey-darker"
              :class="(language === 'ar') ? 'ml-3' : 'mr-3'"
            >{{ moment(item.created_at).format('MMMM DD') }}</p>
          </div>

          <hr class="post-horizontal-line bg-teal-light" />

          <div class="flex flex-col justify-start w-full px-4">
            <a
              :href="(item.key === 'created_community') ? '/communities/' + item.target.slug : '/events/' + item.target.slug"
            >
              <p
                class="mb-4 rubik-regular text-md text-teal"
                :class="(language === 'ar') ? 'pr-2' : 'pl-2'"
                v-if="item.target"
              >{{ item.target.name }}</p>
            </a>

            <p
              class="mb-4 rubik-regular text-sm"
              :class="(language === 'ar') ? 'pr-2' : 'pl-2'"
              v-if="item.target"
            >{{ item.target.body }}</p>
          </div>
          <!-- <b-tooltip
            class="ml-auto cursor-pointer"
            :label="$t('shared.report')"
            position="is-left"
            animated
          >
            <img class src="/svgs/question_mark.svg" alt />
          </b-tooltip>-->
        </div>
      </div>
    </div>

    <div class="main-sidebar flex flex-col bg-teal w-1/3 -mt-1 relative z-0">
      <div class="overflow-y-scroll" style="height:80.5vh;">
        <div class="px-10 pt-6 overflow-y-auto">
          <div class="flex items-center">
            <div
              class="flex justify-center items-center bg-orange rounded-full"
              style="width:40px;height:40px;"
              :class="(language === 'ar') ? 'ml-3' : 'mr-3'"
            >
              <font-awesome-icon
                class="icon text-white cursor-pointer w-full"
                :icon="['fa', 'clipboard']"
                size="lg"
              />
            </div>

            <p
              class="text-lg rubik-medium"
            >{{ $t('components.profiles.friends') }} ({{ friend_count }})</p>
          </div>

          <div
            class="w-full flex-col mb-10 overflow-auto mt-5"
            :class="(language === 'ar') ? 'pl-2' : 'pr-2'"
            style="height:40vh;"
          >
            <div
              class="bg-white w-full flex border border-white rounded-sm px-6 py-4 text-teal items-center justify-start mb-4"
              v-for="(friend, index) in friends"
              :key="index"
            >
              <div class="overflow-hidden" style="border-radius:50%;width:50px;height:50px;">
                <img :src="'/storage/images/' + friend.image_path" alt />
              </div>
              <div class="flex-1" :class="(language === 'ar') ? 'mr-3' : 'ml-3'">
                <a :href="'/profiles/' + friend.username">
                  <p class="text-teal rubik-medium text-sm">{{ friend.name }}</p>
                </a>
                <p
                  class="rubik-regular text-xs text-black"
                  v-if="friend.location"
                >{{ friend.location.country.name[language] }}</p>
              </div>
            </div>
          </div>
        </div>

        <div class="px-10 pt-6 overflow-y-auto">
          <div class="flex items-center">
            <div
              class="flex justify-center items-center bg-orange rounded-full"
              style="width:40px;height:40px;"
              :class="(language === 'ar') ? 'ml-3' : 'mr-3'"
            >
              <font-awesome-icon
                class="icon text-white cursor-pointer w-full"
                :icon="['fa', 'campground']"
                size="lg"
              />
            </div>

            <p
              class="pt-2 text-lg rubik-medium"
            >{{ $t('components.communities.communities') }} ({{ Object.keys(myCommunities).length}})</p>
          </div>
        </div>

        <slider
          v-if="Object.keys(myCommunities).length"
          class="px-10"
          :flickityOptions="flickityOptions"
          :cards="myCommunities"
          :loading="isLoading"
          :resource="'communities'"
        ></slider>

        <div
          v-else
          class="flex bg-white p-10 text-center border border-white rounded-sm mt-5 mx-10"
        >
          <p class="rubik-medium text-xs text-black">
            <span class="rubik-bold">{{ profile.name }}</span>
            {{ $t('components.profiles.not_currently_member') }}.
            <br />
            <br />
            {{ $t('components.profiles.communities_are_a_great_way') }}
          </p>
        </div>
      </div>
      <footer-component></footer-component>
    </div>

    <transition name="fade">
      <modal
        v-if="activeModal"
        :current-component="activeModal"
        :profile="profile"
        :to-block="profile"
        :standalone="true"
        v-on:modal:close="closeModal($event)"
        v-on:modal:unfriend="is_friend = false"
        v-on:modal:block="is_blocked = true"
        v-on:modal:unblock="is_blocked = false"
      ></modal>
    </transition>
  </div>
</template>

<script>
import { mapState, mapMutations, mapActions } from "vuex";
import Slider from "./../components/utils/Slider.vue";
import uuidv4 from "uuid/v4";
import Modal from "./../components/utils/Modal";

export default {
  props: {
    profile: {
      type: [Object, Array]
    },
    owner: {
      type: Boolean
    },
    friends: {
      type: [Object, Array]
    },
    is_friend: {
      type: Boolean,
      default: true
    },
    friend_request_sent: {
      type: Boolean
    },
    has_friend_request_from: {
      type: Boolean
    },
    friend_count: {
      default: 0
    },
    my_communities: {
      type: [Object, Array]
    },
    is_blocked: {
      type: Boolean
    }
  },
  computed: {
    ...mapState({
      activeModal: state => state.ui.activeModal,
      language: state => state.i18n.locale
    }),
    profileImage() {
      if (this.profile.image_path !== "default.jpeg") {
        return true;
      } else {
        return false;
      }
    }
  },
  async created() {
    this.myCommunities = this.my_communities;

    this.$store.dispatch("ui/setAccessedProfile", this.profile);

    await axios
      .get("/profile/" + this.profile.username + "/activity")
      .then(response => {
        response.data.friendship_activity.forEach(element => {
          this.friendshipActivity = {
            ...this.friendshipActivity,
            [uuidv4()]: element
          };
        });

        response.data.resource_activity.forEach(element => {
          this.resourceActivity = {
            ...this.resourceActivity,
            [uuidv4()]: element
          };
        });
      });
  },
  mounted() {},

  data() {
    return {
      isLoading: true,
      myCommunities: {},
      friendshipActivity: {},
      resourceActivity: {},
      flickityOptions: {
        prevNextButtons: true,
        pageDots: false,
        wrapAround: true,
        groupCells: true
      }
    };
  },

  methods: {
    block() {
      this.$store.dispatch("profiles/setTargetProfile", this.profile);

      this.$store.dispatch("ui/setActiveModal", "Block");
    },
    unblock() {
      this.$store.dispatch("profiles/setTargetProfile", this.profile);

      this.$store.dispatch("ui/setActiveModal", "Unblock");
    },
    openReport(resource, resourceType) {
      this.$store.dispatch("reports/setReportedResourceType", resourceType);

      this.$store.dispatch("reports/setReportedResource", resource);

      this.$store.dispatch("ui/setReportView", true);
    },
    unfriend() {
      this.$store.dispatch("profiles/setTargetProfile", this.profile);

      this.$store.dispatch("ui/setActiveModal", "Unfriend");
    },
    cancelFriendRequest() {
      axios
        .post("/requests/cancelFriendRequest", {
          username: this.profile.username
        })
        .then(response => {
          this.friend_request_sent = false;
          this.$toast.open({
            duration: 5000,
            message: this.$t(
              "components.profiles.toast.friend_request_cancelled"
            ),
            position: "is-bottom",
            type: "is-success"
          });
        })
        .catch(error => {});
    },
    sendFriendRequest() {
      axios
        .post("/profile/" + this.profile.username, {
          username: this.profile.username
        })
        .then(response => {
          this.friend_request_sent = true;
          this.$toast.open({
            duration: 5000,
            message: this.$t("components.profiles.toast.friend_request_sent"),
            position: "is-bottom",
            type: "is-success"
          });
        })
        .catch(error => {});
    }
  },
  components: {
    Slider,
    Modal
    // Loading,
  }
};
</script>

<style lang="scss" scoped>
.profile-card {
  max-width: 900px;
}

.post-input {
  border-radius: 0 9999px 9999px 0;
}

.post-input-left-icon {
  border-radius: 9999px 0 0 9999px;
}

.post-horizontal-line {
  width: 97.5%;
}
</style>
