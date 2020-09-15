<template>
  <div class="flex justify-center items-center bg-white w-full small-border-radius px-16 py-6">
    <div class="w-full">
      <p class="text-teal raleway-bold mb-4">{{ $t('components.search.order_by') }}</p>

      <div class="mx-2">
        <!-- <v-select class="block bg-teal w-full text-black" placeholder="By Relevance"></v-select> -->
        <multiselect
          :placeholder="$t('shared.select_option')"
          class="rounded bg-white text-black"
          v-model="sortBy"
          :options="options"
        ></multiselect>
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
    this.$store.dispatch('search/searchForAll').then(() => { });
  },

  data () {
    return {
      sortBy: null,
      options: ['Name']
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
