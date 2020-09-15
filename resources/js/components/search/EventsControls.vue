<template>
  <div class="flex-col">
    <div class="flex justify-center items-center bg-white w-full small-border-radius px-16 py-6 mb-4">
      <div class="w-full">
        <p class="text-teal raleway-bold mb-4">{{ $t('components.search.date') }}</p>

        <div class="flex text-grey-darkest whitespace-no-wrap">
          <div
            class="flex flex-wrap w-full raleway-regular overflow-y-auto"
            style="height:150px;"
          >
            <div
              class="flex-col"
              :class="(language === 'ar') ? 'ml-16' : 'mr-16'"
            >
              <div class="field pb-4 w-1/2">
                <b-checkbox
                  type="is-teal"
                  native-value="all"
                  v-model="dateFilter"
                >{{ $t('components.search.all') }}</b-checkbox>
              </div>

              <div class="field pb-4 w-1/2">
                <b-checkbox
                  type="is-teal"
                  native-value="today"
                  v-model="dateFilter"
                >{{ $t('components.search.today') }}</b-checkbox>
              </div>

              <div class="field pb-4 w-1/2">
                <b-checkbox
                  type="is-teal"
                  native-value="now"
                  v-model="dateFilter"
                >{{ $t('components.search.now') }}</b-checkbox>
              </div>
            </div>

            <div
              class="flex-col"
              :class="(language === 'ar') ? 'ml-16' : 'mr-16'"
            >
              <div class="field pb-4 w-1/2">
                <b-checkbox
                  type="is-teal"
                  native-value="this-week"
                  v-model="dateFilter"
                >{{ $t('components.search.this_week') }}</b-checkbox>
              </div>

              <div class="field pb-4 w-1/2">
                <b-checkbox
                  type="is-teal"
                  native-value="this-month"
                  v-model="dateFilter"
                >{{ $t('components.search.this_month') }}</b-checkbox>
              </div>

              <div class="field pb-4 w-1/2">
                <b-checkbox
                  type="is-teal"
                  native-value="endless"
                  v-model="dateFilter"
                >{{ $t('components.search.endless') }}</b-checkbox>
              </div>
            </div>
          </div>
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
import Multiselect from 'vue-multiselect';
import { mapState, mapMutations, mapActions } from 'vuex';

export default {
  computed: {
    ...mapState({
      language: state => state.i18n.locale,
    }),
  },
  watch: {
    sortBy (value) {
      if (value !== null) {
        this.$emit('sort-results', this.sortBy);
      }
    }
  },
  created () {
    this.$store.dispatch('search/setActiveIndex', 'events');

    this.$store.dispatch('search/searchForEvents').then(() => {
      // this.isLoading = false;
    });
  },
  watch: {
    dateFilter (value) {
      this.setFilters();
    }
  },
  mounted () { },

  data () {
    return {
      dateFilter: [],
      sortBy: null,
      options: ['Name']
    };
  },

  methods: {
    setFilters () {
      this.$store.dispatch('search/setFilters', {
        string: (this.dateFilter.length !== 0) ? this.dateFilter : null,
        filter_name: 'ranges'
      });

      this.search();
    },
    search () {
      this.$store.dispatch('search/search');
    }
  },

  components: {
    Multiselect
  }
};
</script>

<style lang="scss" scoped>
</style>
