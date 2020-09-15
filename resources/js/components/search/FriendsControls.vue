<template>
  <div class="flex-col">
    <div
      class="flex justify-center items-center bg-white w-full small-border-radius px-16 py-6 mb-4"
    >
      <div class="w-full">
        <p class="text-teal raleway-bold mb-4">{{ $t('components.search.friend_location') }}</p>

        <div class="flex text-grey-darkest">
          <div class="flex flex-wrap raleway-regular overflow-y-auto" style="height:150px;">
            <div class="field pb-4 w-1/2" v-for="(item, index) in sortedLocations" :key="index">
              <b-checkbox
                type="is-teal"
                :native-value="item.key"
                v-model="locationsGroup"
              >{{ item.country.name[language] }} ({{ item.count }})</b-checkbox>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div
      class="flex justify-center items-center bg-white w-full small-border-radius px-16 py-6 mb-4"
    >
      <div class="w-full">
        <p class="text-teal raleway-bold mb-4">{{ $t('components.settings.education') }}</p>

        <div class="flex text-grey-darkest">
          <div class="flex flex-wrap raleway-regular overflow-y-auto" style="height:150px;">
            <div class="field pb-4 w-1/2" v-for="(item, index) in sortedEducations" :key="index">
              <b-checkbox
                type="is-teal"
                :native-value="item.key"
                v-model="educationsGroup"
              >{{ item.name[language] }} ({{ item.profiles_count }})</b-checkbox>
            </div>
          </div>
        </div>

        <div class="mx-2">
          <!-- <v-select class="block bg-teal w-full text-black" placeholder="Choose School"></v-select> -->
        </div>
      </div>
    </div>

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
import vSelect from "vue-select";
import Multiselect from "vue-multiselect";
import { mapState, mapMutations, mapActions } from "vuex";

export default {
  computed: {
    ...mapState({
      language: state => state.i18n.locale
      // searchResults: state => state.search.searchResults,
    }),
    sortedLocations() {
      return _.orderBy(this.locations, "name", "asc");
    },
    sortedEducations() {
      return _.orderBy(this.educations, "profiles_count", "desc");
    }
  },
  async created() {
    let that = this;
    this.$store.dispatch("search/setActiveIndex", "profiles");

    await axios.post("/search/locations").then(response => {
      _.forEach(response.data.locations, (element, index) => {
        _.forEach(element, (innerElement, index) => {
          innerElement.count = ++index;
          this.locations = {
            ...this.locations,
            [innerElement.key]: innerElement
          };
        });
      });
    });

    await axios.post("/search/educations").then(response => {
      response.data.forEach(element => {
        this.educations = {
          ...this.educations,
          [element.id]: element
        };
      });
    });

    this.$store.dispatch("search/searchForProfiles").then(() => {});
  },
  watch: {
    locationsGroup(value) {
      this.buildLocationsString(value);
    },
    educationsGroup(value) {
      this.EducationsString(value);
    }
  },
  mounted() {},

  data() {
    return {
      locations: null,
      educations: null,
      educationsGroup: [],
      locationsGroup: [],
      sortBy: null,
      options: ["Name"]
    };
  },

  methods: {
    buildLocationsString(filters) {
      let string;

      filters.forEach(element => {
        if (string === undefined) {
          string = element;
        } else {
          string += " " + element;
        }
      });

      this.setFilters(string, "location");
    },
    EducationsString(filters) {
      let string;

      filters.forEach(element => {
        if (string === undefined) {
          string = element;
        } else {
          string += " " + element;
        }
      });

      this.setFilters(string, "education");
    },
    setFilters(string, type) {
      this.$store.dispatch("search/setFilters", {
        string: string,
        filter_name: type
      });

      this.search();
    },
    search() {
      this.$store.dispatch("search/search");
    }
  },

  components: {
    Multiselect
  }
};
</script>

<style lang="scss" scoped>
</style>
