<template>
  <div class="flex-col">
    <!-- <div
      class="flex justify-center items-center bg-white w-full small-border-radius px-16 py-6 mb-4"
    >
      <div class="w-full">
        <p class="text-teal raleway-bold mb-4">Date</p>

        <div class="flex text-grey-darkest">
          <div class="flex-col">
            <label class="raleway-regular text-sm flex items-center mb-4 cursor-pointer" for>
              <input class="mr-2" type="checkbox" />
              All
            </label>
            <label class="raleway-regular text-sm flex items-center mb-4 cursor-pointer" for>
              <input class="mr-2" type="checkbox" />
              Today
            </label>
            <label class="raleway-regular text-sm flex items-center mb-4 cursor-pointer" for>
              <input class="mr-2" type="checkbox" />
              This month
            </label>
          </div>
        </div>

        <div class="mx-2">
          <v-select class="block bg-teal w-full text-black" placeholder="Custom Date"></v-select>
        </div>
      </div>
    </div> -->

    <div class="flex justify-center items-center bg-white w-full small-border-radius px-16 py-6">
      <div class="w-full">
        <p class="text-teal raleway-bold mb-4">{{ $t('components.search.search_criteria') }}</p>

        <div class>
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
import { mapState, mapMutations, mapActions } from "vuex";
import Multiselect from "vue-multiselect";

export default {
  computed: {
    ...mapState({
      language: state => state.i18n.locale
      // searchResults: state => state.search.searchResults,
    })
  },
  async created() {
    let that = this;
    this.$store.dispatch("search/setActiveIndex", "posts");

    this.$store.dispatch("search/searchForPosts").then(() => {});
  },
  mounted() {},

  data() {
    return {
      sortBy: null,
      options: ["Name"]
    };
  },

  methods: {},

  components: {
    Multiselect
  }
};
</script>

<style lang="scss" scoped>
</style>
