<template>
  <div
    class="fixed pin flex items-center"
    style
  >
    <div class="fixed pin bg-black opacity-50 z-10"></div>

    <div
      class="relative mx-6 md:mx-auto w-full md:w-1/2 lg:w-1/3 z-20 m-8"
      style="max-height:700px;"
    >
      <div
        class="shadow-lg bg-grey text-grey-darkest"
        style
      >
        <div
          class="flex items-center bg-teal justify-between py-2"
          :class="(language === 'ar') ? 'pr-10' : 'pl-10'"
        >
          <p class="text-white">
            {{ $t('components.profiles.friends') }}
          </p>

          <button
            :class="(language === 'ar') ? 'ml-4' : 'mr-4'"
            @click="close()"
          >
            <img
              class="invert-icon w-4"
              src="/svgs/close.svg"
              alt
            >
          </button>
        </div>

        <div class="w-full mb-4 py-4 pb-6 px-10">
          <div class="relative mb-2 mt-4">
            <div
              class="absolute pin-t mt-3 w-5 icon-grey"
              :class="(language === 'ar') ? 'pin-r mr-6' : 'pin-l ml-6'"
            >
              <img
                src="/svgs/search.svg"
                alt
              >
            </div>

            <input
              v-model="searchValue"
              type="search"
              class="w-full text-sm bg-white rounded border border-grey-dark p-3 text-black"
              :class="(language === 'ar') ? 'pr-16' : 'pl-16'"
              :placeholder="$t('components.profiles.search_friends_list')"
            >
          </div>

          <div
            class="overflow-auto flex flex-col"
            :class="(language === 'ar') ? 'pl-4' : 'pr-4'"
            style="max-height:500px;"
          >
            <div
              class="relative bg-white w-full flex border border-white rounded-sm px-6 py-4 text-teal items-center mb-4 cursor-pointer hover:border-teal"
              :class="(language === 'ar') ? 'justify-end' : 'justify-start'"
              v-for="(item, index) in searchFilter"
              :key="index"
              @click="startConversation(item)"
            >
              <img
                class="rounded-full h-16 w-16 flex items-center justify-center border border-grey-dark mr-2"
                :class="(language === 'ar') ? 'ml-2' : 'mr-2'"
                :src="'/storage/images/' + item.image_path"
                alt
              >

              <div
                class="flex-1 ml-3"
                :class="(language === 'ar') ? 'mr-3' : 'ml-3'"
              >
                <p class="text-teal rubik-medium text-sm">{{ item.name }}</p>
                <p
                  class="text-xs text-grey-darkest rubik-light"
                  v-if="item.location"
                >{{ item.location.country.name[language] }}</p>

                <hr>

                <p class="text-xs raleway-semibold text-black">
                  {{ $t('components.profiles.friends') }}:
                  <span class="text-teal">{{ item.friends_count }}
                  </span>
                </p>
              </div>

              <div
                class="absolute -mt-16 bg-red flex spinner"
                :class="(language === 'ar') ? 'mr-5' : 'ml-5'"
                v-if="item.loading"
              ></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState, mapMutations, mapActions } from 'vuex';
import FriendShipInteractionComponent from './../../components/friends/FriendShipInteractionComponent.vue';
import closeMixin from './../../mixins/close.js';

export default {
  mixins: [closeMixin],
  props: ['for'],
  computed: {
    ...mapState({
      profile: state => state.profiles.signedInProfile,
      community: state => state.communities.activeCommunity,
      event: state => state.events.activeEvent,
      friends: state => state.profiles.friends,
      language: state => state.i18n.locale,
    }),
    searchFilter () {
      if (this.searchValue !== '') {
        return _.filter(this.friends, (element) => {
          return element['name'].toLowerCase().includes(this.searchValue.toLowerCase());
        });
      } else {
        return this.friends;
      }
    },
  },
  created () {
    // value.loading = false;
  },
  activated () {
    this.active = false;
  },
  data () {
    return {
      searchValue: '',
      active: false,
    };
  },

  methods: {
    startConversation (value) {
      if (!this.active) {
        this.active = true;
        value.loading = true;

        this.$store.dispatch('conversations/startConversation', value).then(response => {
          value.loading = false;
        this.active = false;

        }).catch(error => {
          value.loading = false;
        this.active = false;

        });
      }
    },
    close () {
      this.$emit('modal:close');
    }
  },
  components: { FriendShipInteractionComponent }
};
</script>

<style lang="scss" scoped>
</style>
