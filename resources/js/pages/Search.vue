<template>
  <div class="flex page">
    <div class="bg-grey flex-1 flex" :class="(language === 'ar') ? 'pl-12' : 'pr-12'">
      <div class="w-1/6 pt-6 text-black bg-teal">
        <navigation @clicked="setCriteria" v-on:search-set-criteria="setCriteria($event)"></navigation>
      </div>

      <div class="w-5/6 flex flex-col pt-6 px-8">
        <p class="text-grey-darkest mb-2">
          {{ $t('components.search.search_yielded') }}
          <span
            class="text-teal"
          >{{ Object.keys(this.searchResults[activeIndex]).length }}</span>
          {{ $t('components.search.results') }}
        </p>

        <div class="relative mt-4 mb-6">
          <div
            class="absolute pin-t mt-4 w-5 icon-grey"
            :class="(language === 'ar') ? 'pin-r mr-4' : 'pin-l ml-4'"
          >
            <img src="/svgs/search.svg" alt />
          </div>

          <input
            type="search"
            class="w-full bg-white rounded border p-3 py-4 text-black"
            :class="(language === 'ar') ? 'pr-12' : 'pl-12'"
            :placeholder="$t('shared.name_placeholder')"
            v-model="searchFieldValue"
            v-on:keyup.enter="search(searchFieldValue)"
          />
        </div>

        <div
          class="flex flex-col overflow-x-hidden overflow-y-auto"
          :class="(language === 'ar') ? 'pl-6' : 'pr-6'"
          style="height:75vh;"
        >
          <div>
            <div
              class="flex items-center w-full bg-white justify-start text-black px-4 py-6 rounded mb-4"
              v-for="(item, index) in searchForIndex"
              :key="index"
            >
              <div
                v-if="activeIndex !== 'posts'"
                class="overflow-hidden"
                style="border-radius:50%;width:75px;height:75px;"
              >
                <img class="object-cover" :src="'/storage/images/' + item.image_path" alt />
              </div>

              <div v-else class="overflow-hidden" style="border-radius:50%;width:75px;height:75px;">
                <img class="object-cover" :src="'/storage/images/' + item.profile.image_path" alt />
              </div>

              <div
                v-if="activeIndex !== 'posts'"
                class="flex-1"
                :class="(language === 'ar') ? 'mr-8' : 'ml-8'"
              >
                <a :href="item.model_type + '/' + (item.slug || item.username)">
                  <p class="text-teal rubik-medium text-lg mb-1">{{ item.name }}</p>
                </a>
                <p
                  class="rubik-regular text-sm text-grey-darkest"
                  v-if="item.location"
                >{{ item.location.country.name[language] }}</p>

                <div v-if="item.interests">
                  <div
                    class="rubik-regular text-sm text-grey-darkest inline-block capitalize"
                    :class="(language === 'ar') ? 'ml-2' : 'mr-2'"
                    v-for="(interest, index) in item.interests"
                    :key="index"
                  >
                    {{ interest.name[language] }}
                    <p
                      class="rubick-regular inline-block text-grey-darkest text-sm"
                      v-if="index != item.interests.length - 1"
                    >,</p>
                  </div>
                </div>
                <hr />

                <div class="w-full flex text-sm items-center">
                  <p v-if="item.username" class="flex-1">
                    <span class v-if="item.friends">
                      {{ $t('components.search.friends') }}:
                      <span
                        class="text-teal"
                      >{{ item.friends.length }}</span>
                    </span>
                  </p>

                  <p v-if="item.model_type === 'communities'" class="flex-1">
                    {{ $t('components.communities.community_members') }}:
                    <span
                      class="text-teal"
                    >{{ item.participants.length }}</span>
                  </p>

                  <p v-if="item.model_type === 'events'" class="flex-1">
                    <span class="text-teal">{{ item.participants.length }}</span>
                    {{ $t('components.events.people_attending') }}.
                  </p>

                  <div class="flex" v-if="item.friends">
                    <b-tooltip
                      class="cursor-pointer"
                      position="is-top"
                      animated
                      v-for="(item, index) in item.friends"
                      :label="item.name"
                      :key="index"
                    >
                      <div v-if="index <= 5" class="mx-1">
                        <a :href="'/profiles/' + item.username">
                          <img
                            class="rounded-full border w-8"
                            :src="'/storage/images/' + item.image_path"
                            alt
                          />
                        </a>
                      </div>
                    </b-tooltip>
                  </div>

                  <div class="flex">
                    <b-tooltip
                      class="cursor-pointer"
                      position="is-top"
                      animated
                      v-for="(item, index) in item.participants"
                      :label="item.name"
                      :key="index"
                    >
                      <div v-if="index <= 5" class="mx-1">
                        <a :href="'/profiles/' + item.username">
                          <img
                            class="rounded-full border w-8"
                            :src="'/storage/images/' + item.image_path"
                            alt
                          />
                        </a>
                      </div>
                    </b-tooltip>
                  </div>
                </div>
              </div>

              <div v-else class="flex-1" :class="(language === 'ar') ? 'mr-8' : 'ml-8'">
                <div
                  v-for="(community, index) in item.communities"
                  :label="community.name"
                  :key="index"
                >
                  <a :href="'communities/' + community.slug">
                    <p class="text-teal rubik-medium text-lg mb-1">{{ community.name }}</p>
                  </a>
                </div>

                <div v-for="(event, index) in item.events" :label="event.name" :key="index">
                  <a :href="'events/' + event.slug">
                    <p class="text-teal rubik-medium text-lg mb-1">{{ event.name }}</p>
                  </a>
                </div>

                <a
                  :href="item.profile.model_type + '/' + (item.profile.slug || item.profile.username)"
                >
                  <p class="text-teal rubik-medium text-sm mb-1">{{ item.profile.name }}</p>
                </a>

                <p class="mt-2">{{ item.body }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="main-sidebar flex flex-col bg-teal w-1/3 -mt-1 relative z-0">
      <div class="flex-1 flex-col px-10 pt-12">
        <component v-bind:is="searchCriteria" v-on:sort-results="sortBy"></component>
      </div>

      <footer-component></footer-component>
    </div>
  </div>
</template>

<script>
import { mapState, mapMutations, mapActions } from "vuex";

import Navigation from "./../components/search/Navigation.vue";
import AllControls from "./../components/search/AllControls.vue";
import EventsControls from "./../components/search/EventsControls.vue";
import CommunitiesControls from "./../components/search/CommunitiesControls.vue";
import FriendsControls from "./../components/search/FriendsControls.vue";
import PostsControls from "./../components/search/PostsControls.vue";

export default {
  computed: {
    ...mapState({
      language: state => state.i18n.locale,
      searchResults: state => state.search.searchResults,
      activeIndex: state => state.search.activeIndex
    }),
    searchForIndex() {
      if (this.activeIndex === "profiles") {
        return this.searchResults.profiles;
      } else if (this.activeIndex === "communities") {
        return this.searchResults.communities;
      } else if (this.activeIndex === "events") {
        return this.searchResults.events;
      } else if (this.activeIndex === "posts") {
        return this.searchResults.posts;
      } else if (this.activeIndex === "all") {
        if (this.sortValue !== "") {
          return _.orderBy(this.searchResults.all, "name", "asc");
        } else {
          return this.searchResults.all;
        }
      }
    }
  },
  created() {},
  mounted() {
    if (location.hash) {
      this.searchCriteria = location.hash.substr(1);
      if (this.language === "ar") {
        $("#" + location.hash.substr(1)).addClass([
          "text-white border-white pr-4"
        ]);
      } else {
        $("#" + location.hash.substr(1)).addClass([
          "text-white border-white pl-4"
        ]);
      }
    } else {
      window.location.hash = "#all-controls";
      if (this.language === "ar") {
        $("#all-controls").addClass(["text-white border-white pr-4"]);
      } else {
        $("#all-controls").addClass(["text-white border-white pl-4"]);
      }
    }
  },
  data() {
    return {
      sortValue: "",
      searchFieldValue: "",
      searchCriteria: "all-controls",
      searchTypes: ["full", "events", "communities", "friends", "posts"]
    };
  },
  methods: {
    sortBy(value) {
      this.sortValue = value;
    },
    search(value) {
      this.$store.dispatch("search/setSearchQuery", value);

      if (this.activeIndex === "communities") {
        this.$store.dispatch("search/search", {
          match: "match",
          fields: "name",
          value: value
        });
      } else if (this.activeIndex === "all") {
        this.$store.dispatch("search/search", {
          match: "match",
          fields: "name",
          value: value
        });
      } else if (this.activeIndex === "profiles") {
        this.$store.dispatch("search/search", {
          match: "match",
          fields: "name",
          value: value
        });
      } else if (this.activeIndex === "events") {
        this.$store.dispatch("search/search", {
          match: "match",
          fields: "name",
          value: value
        });
      } else if (this.activeIndex === "posts") {
        this.$store.dispatch("search/search", {
          match: "match",
          fields: "name",
          value: value
        });
      }
    },
    setCriteria(criteria) {
      this.$store.dispatch("search/setActiveIndex", criteria.index);
      this.searchCriteria = criteria.searchType;

      window.location.hash = "#" + criteria.searchType;
    }
  },
  components: {
    Navigation,
    AllControls,
    EventsControls,
    CommunitiesControls,
    FriendsControls,
    PostsControls
  }
};
</script>

<style lang="scss" scoped>
</style>
