<template>
  <div class="flex page">
    <div class="bg-grey flex-1 pt-6 px-24">
      <p
        class="mb-2 text-xl text-teal rubik-medium"
      >{{ $t('components.communities.my_communities') }}</p>

      <div class="flex text-grey-darkest my-4 rubik-light text-sm">
        <div>
          <b-radio :class="(language === 'ar') ? 'pl-2' : 'pr-2'" v-model="sortType" native-value>
            <span :class="(language === 'ar') ? 'pr-1' : 'pl-0'">{{ $t('shared.all') }}</span>
          </b-radio>
        </div>
        <div>
          <b-radio class="px-2" v-model="sortType" native-value="public">
            <span :class="(language === 'ar') ? 'pr-1' : 'pl-0'">{{ $t('shared.public') }}</span>
          </b-radio>
        </div>

        <div>
          <b-radio class="px-2" v-model="sortType" native-value="private">
            <span :class="(language === 'ar') ? 'pr-1' : 'pl-0'">{{ $t('shared.private') }}</span>
          </b-radio>
        </div>

        <div class>
          <b-radio class="px-2" v-model="sortType" native-value="secret">
            <span :class="(language === 'ar') ? 'pr-1' : 'pl-0'">{{ $t('shared.secret') }}</span>
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
          v-for="(myCommunity, index) in mySortedCommunities"
          :community="myCommunity"
          :key="index"
        >
          <div class="w-1/4 h-full">
            <img
              class="regular-left-border-radius object-cover"
              :src="'/storage/images/' + myCommunity.image_path"
              alt
            />
          </div>

          <div class="py-6 px-6 leading-normal w-full">
            <a
              :href="'/communities/' + myCommunity.slug"
              class="text-grey-darker rubik-medium"
            >{{ myCommunity.name }}</a>

            <p class="mt-4 text-sm max-h-24 max overflow-hidden" style="max-height:4.0rem;">
              <span class="font-bold">{{ $t('shared.about') }}:</span>
              {{ myCommunity.body }}
            </p>

            <div>
              <hr />

              <div class="w-full block flex items-center justify-between">
                <p class="text-xs raleway-semibold">
                  <span class="text-teal">{{ myCommunity.participants.length }}</span>

                  <span
                    v-if="myCommunity.participants.length === 1"
                  >{{ $t('components.communities.person_is_member') }}</span>
                  <span v-else>
                    {{ $t('components.communities.people_are_members') }}
                    <span
                      class="text-teal"
                      v-if="Object.keys(myCommunity.mutuals).length != 0"
                    >including {{ Object.keys(myCommunity.mutuals).length}}</span>

                    <span v-if="Object.keys(myCommunity.mutuals).length == 1">friend</span>

                    <span v-if="Object.keys(myCommunity.mutuals).length > 1">friends</span>
                  </span>
                </p>

                <div class="flex">
                  <b-tooltip
                    class="float-right cursor-pointer mx-1"
                    position="is-top"
                    animated
                    v-for="(item, index) in myCommunity.mutuals"
                    :label="item.name"
                    :key="index"
                  >
                    <img class="w-6 rounded-full" :src="'/storage/images/' + item.image_path" alt />
                  </b-tooltip>

                  <font-awesome-icon
                    class="icon text-teal cursor-pointer hover:text-teal-light"
                    :class="(language === 'ar') ? 'mr-1' : 'ml-1'"
                    :icon="['fa', 'plus-circle']"
                    size="lg"
                    @click="openModal('guest-list', myCommunity)"
                  />
                </div>
              </div>

              <div class="flex items-center">
                <div
                  class="flex items-center text-grey-darker hover:text-grey-darkest cursor-pointer"
                  @click="like(myCommunity)"
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
                    v-if="liked(myCommunity.love_reactant.reactions)"
                  >{{ $t('shared.liked') }} ({{ myCommunity.reactions_count }})</p>

                  <p
                    v-else
                    class="text-xs"
                    :class="(language === 'ar') ? 'ml-6' : 'mr-6'"
                  >{{ $t('shared.like') }} ({{ myCommunity.reactions_count }})</p> -->

                  <p
                    class="text-xs text-teal"
                    :class="(language === 'ar') ? 'ml-6' : 'mr-6'"
                    v-if="liked(myCommunity.love_reactant.reactions)"
                  >{{ $t('shared.liked') }} ({{ Object.keys(myCommunity.love_reactant.reactions).length }})</p>

                  <p
                    v-else
                    class="text-xs"
                    :class="(language === 'ar') ? 'ml-6' : 'mr-6'"
                  >{{ $t('shared.like') }} ({{ Object.keys(myCommunity.love_reactant.reactions).length }})</p>
                </div>

                <div
                  class="flex items-center text-grey-darker hover:text-grey-darkest cursor-pointer"
                  @click="openShareModal(myCommunity)"
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
        <p class="text-xl rubik-medium">{{ $t('components.communities.suggested_communities') }}</p>

        <slider
          :flickityOptions="flickityOptions"
          :cards="communities"
          :resource="'communities'"
          :loading="isLoading"
        ></slider>

        <div class="card-item-small text-base flex-col w-full mt-8 mb-8">
          <img class="regular-top-border-radius" src="images/people_skiing.jpg" alt />

          <div class="p-6">
            <p
              class="text-lg text-teal rubik-medium"
            >{{ $t('components.communities.create_your_own_community') }}</p>

            <p class="mt-4 text-sm">{{ $t('components.communities.create_community_description') }}</p>

            <hr class="bg-teal" />

            <button
              class="bg-teal flex text-white text-sm px-6 py-2 mx-auto mt-4 border border-teal hover:bg-white hover:text-teal"
              @click="openCreateCommunityModal"
            >{{ $t('components.communities.create_community') }}</button>
          </div>
        </div>

        <p class="text-xl rubik-medium">{{ $t('components.communities.most_popular_communities') }}</p>

        <elastic-slider
          :flickityOptions="flickityOptions"
          :cards="popularCommunities"
          :loading="isLoading"
          :resource="'communities'"
        ></elastic-slider>

        <a href="/search-page#communities-controls" class="ml-auto my-4 rubik-medium text-sm">
          {{ $t('components.communities.display_all_communities') }} &nbsp;
          >
        </a>
      </div>

      <footer-component></footer-component>
    </div>

    <transition name="fade">
      <proped-modal
        v-if="PropedModalOpen"
        :announcement="$t('components.communities.you_have_created_the_community')"
        standalone="standalone"
        :image="'/storage/images/' + newCommunity.image_path"
        :title="newCommunity.name"
        v-on:modal:close="PropedModalOpen = false"
        v-on:keydown.esc="PropedModalOpen = false"
      ></proped-modal>
    </transition>

    <transition name="fade">
      <component
        v-if="modalOpen"
        v-bind:is="modalName"
        v-on:modal:close="modalOpen = false"
        :for="`communities`"
      ></component>
    </transition>
  </div>
</template>

<script>
import ShareEvent from "./../components/events/ShareEvent.vue";
import GuestList from "./../components/communities/GuestList.vue";
import CreateCommunity from "./../components/communities/CreateCommunity.vue";
import PropedModal from "./../components/utils/PropedModal.vue";
import Slider from "./../components/utils/Slider.vue";
import InviteToResource from "./../components/utils/InviteToResource.vue";
import ElasticSlider from "./../components/utils/ElasticSlider.vue";

import Flickity from "vue-flickity";
import { mapState, mapMutations, mapActions } from "vuex";

export default {
  props: ["interests"],

  computed: {
    ...mapState({
      profile: state => state.profiles.signedInProfile,
      communities: state => state.communities.communities,
      myCommunities: state => state.communities.myCommunities,
      newCommunity: state => state.communities.newCommunity,
      language: state => state.i18n.locale,
      popularCommunities: state => state.communities.popularCommunities
    }),
    mySortedCommunities() {
      let that = this;
      if (this.sortType === "") {
        return _.orderBy(this.myCommunities, "created_at", "desc");
      } else {
        return _.filter(this.myCommunities, function(community) {
          return community.privacy.key === that.sortType;
        });
      }
    }
  },

  created() {
    this.isLoading = true;
    this.$store.dispatch("communities/getCommunities").then(() => {});

    this.$store
      .dispatch("communities/getMyCommunities", this.profile)
      .then(response => {
        this.isLoading = false;
      });

    this.$store.dispatch("communities/getPopularCommunities").then(response => {
      this.isLoading = false;
    });
  },
  mounted() {},

  data() {
    return {
      createCommunityModalOpen: false,
      inviteToResourceModalOpen: false,
      PropedModalOpen: false,
      modalName: "create-community",
      sortType: "",
      modalOpen: false,
      modalName: "",
      isLoading: true,
      flickityOptions: {
        prevNextButtons: true,
        pageDots: false,
        wrapAround: true,
        groupCells: true,
        imagesLoaded: true,
        adaptiveHeight: true
      }
    };
  },
  methods: {
    openShareModal(value) {
      this.$store.dispatch("ui/openShareModal", value);
    },
    openInvitationModal() {
      this.inviteToResourceModalOpen = true;
    },
    openCreateCommunityModal() {
      this.$store.dispatch("ui/setActiveModal", "create-community");
    },
    async like(event) {
      await axios
        .post("/profile/" + this.profile.username + "/like", {
          slug: event.slug,
          type: "community"
        })
        .then(response => {
          this.$toast.open({
            duration: 5000,
            message: response.data.message,
            position: "is-bottom",
            type: "is-success",
            queue: false
          });

          this.$store.dispatch("communities/updateCommunityLike", {
            community: response.data.resource,
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
      // return false;
    },
    open() {},
    openModal(name, community = null) {
      if (community) {
        this.$store.dispatch("communities/setActiveCommunity", community);
      }

      this.modalOpen = true;
      this.modalName = name;
    },
    openCongratsModal() {
      this.PropedModalOpen = true;
      this.createCommunityModalOpen = false;
      this.inviteToResourceModalOpen = false;
    }
  },
  components: {
    ShareEvent,
    GuestList,
    CreateCommunity,
    PropedModal,
    Flickity,
    Slider,
    InviteToResource,
    ElasticSlider
  }
};
</script>

<style lang="scss" scoped>
</style>
