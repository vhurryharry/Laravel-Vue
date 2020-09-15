<template>
  <div class="flex-col">
    <div class="flex justify-center items-center bg-white w-full small-border-radius px-16 pr-6 py-6 mb-4">
      <div class="w-full">
        <p class="text-teal raleway-bold mb-4">{{ $t('components.communities.interest_area') }}</p>

        <div class="flex text-grey-darkest">
          <div
            class="flex flex-wrap raleway-regular overflow-y-auto"
            style="height:150px;"
          >
            <div
              class="field pb-4 w-1/2"
              v-for="(item, index) in sortedInterests"
              :key="index"
            >
              <b-checkbox
                type="is-teal"
                @click.native="updateFilters(item.key)"
                :native-value="item.key"
                v-model="interestsGroup"
              >{{ item.name[language] }} ({{ item.communities_count }})</b-checkbox>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="flex justify-center items-center bg-white w-full small-border-radius px-16 py-6 mb-4">
      <div class="w-full">
        <p class="text-teal raleway-bold mb-4">{{ $t('components.communities.community_type') }}</p>

        <div class="flex text-grey-darkest">
          <div class="flex flex-col w-1/2">
            <div class="field pb-4 w-1/2">
              <b-checkbox
                type="is-teal"
                native-value="public"
                v-model="typesGroup"
                @click.native="updateFilters('public')"
              >
                {{ $t('shared.public') }}
              </b-checkbox>
            </div>

            <div class="field pb-4 w-1/2">
              <b-checkbox
                type="is-teal"
                native-value="private"
                v-model="typesGroup"
                @click.native="updateFilters('private')"
              >
                {{ $t('shared.private') }}
              </b-checkbox>
            </div>
          </div>
          <!-- <div class="field pb-4 w-1/2">
            <b-checkbox
              type="is-teal"
              native-value="secret"
              v-model="typesGroup"
              @click.native="updateFilters('secret')"
            >Secret</b-checkbox>
          </div> -->
        </div>
      </div>
    </div>

    <div class="flex justify-center items-center bg-white w-full small-border-radius px-16 py-6">
      <div class="w-full">
        <p class="text-teal raleway-bold mb-4">{{ $t('components.search.search_criteria') }}</p>

        <div class="mx-2">
          <multiselect
            class="rounded bg-white text-black"
            :placeholder="$t('shared.select_option')"
            v-model="sortBy"
            :options="options"
          ></multiselect>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState, mapMutations, mapActions } from 'vuex';
import Multiselect from 'vue-multiselect';

export default {
  computed: {
    ...mapState({
      language: state => state.i18n.locale,
      // searchResults: state => state.search.searchResults,
    }),

    sortedInterests () {
      return _.orderBy(
        this.communityInterests,
        'communities_count',
        'desc'
      );
    }
  },
  watch: {
    interestsGroup (value) {
      this.buildInterestsString(value);
    },
    typesGroup (value) {
      this.buildTypesString(value);
    }
  },
  async created () {
    this.$store.dispatch('search/setActiveIndex', 'communities');

    await axios.post('/search/interests').then(response => {
      // console.log(response);
      response.data.forEach(element => {
        this.communityInterests = {
          ...this.communityInterests,
          [element.id]: element
        };
      });
    });

    this.$store.dispatch('search/searchForCommunities').then(() => {
      // this.isLoading = false;
    });

    // axios.get('/search/communities').then(response => {
    //     response.data.forEach(element => {
    //         this.searchResults.communities = {
    //             ...this.searchResults,
    //             [element.id]: element
    //         };
    //     });
    // });
  },
  mounted () { },

  data () {
    return {
      options: ['Name'],
      sortBy: null,
      communityInterests: null,
      searchResults: null,
      chosenFilters: [],
      interestsGroup: [],
      typesGroup: [],

    };
  },

  methods: {
    updateFilters (key) {
      // alert(key);
      // console.log(this.chosenFilters);
      //   this.chosenFilters = key;
      // this.$store.dispatch('search/updateFilters', key);
    },
    buildInterestsString (filters) {
      let string;

      filters.forEach(element => {
        if (string === undefined) {
          string = element;
        } else {
          string += ' ' + element;
        }
      });

      //   console.log(string);

      this.setFilters(string, 'interests');
    },
    buildTypesString (filters) {
      let string;

      filters.forEach(element => {
        if (string === undefined) {
          string = element;
        } else {
          string += ' ' + element;
        }
      });

      this.setFilters(string, 'type');
    },
    setFilters (string, type) {
      this.$store.dispatch('search/setFilters', {
        string: string,
        filter_name: type
      });

      this.search();
    },
    search () {
      this.$store.dispatch('search/search');

      // this.$store.dispatch('search/queryCommunities', {
      //     match: 'match',
      //     fields: 'name',
      //     value: value
      // });
    }
  },

  components: {
    Multiselect
  }
};
</script>

<style lang="scss" scoped>
</style>
