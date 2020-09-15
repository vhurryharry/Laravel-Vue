<template>
  <div class="fixed pin flex items-center">
    <div class="fixed pin bg-black opacity-50 z-10"></div>
    <div class="relative mx-6 md:mx-auto w-full md:w-1/2 lg:w-2/4 z-20">
      <div class="shadow-lg bg-grey rounded text-grey-darkest">
        <form class="flex flex-col" @submit.prevent>
          <div class="flex items-center bg-teal justify-between pl-10 py-4">
            <p class="text-white text-lg">Report {{ reported_resource.name }}'s behavior</p>
            <button class="mr-4" @click="close()">
              <img class="invert-icon w-4" src="/svgs/close.svg" alt />
            </button>
          </div>

          <div class="flex pl-10 pt-4 pr-10">
            <div class="flex-col w-1/2">
              <div class="flex flex-col mb-4">
                <label
                  class="text-sm rubik-regular"
                  for="category"
                >How was {{ reported_resource.name}}'s behavior inappropriate?</label>
                <b-select
                  placeholder="Select an offense"
                  v-model="report.category"
                  v-validate="'required'"
                  data-vv-as="category"
                  name="category"
                  ref="category"
                >
                  <option
                    v-for="option in offsenseCategories"
                    :value="option.name"
                    :key="option.id"
                  >{{ option.name }}</option>
                </b-select>

                <span
                  v-show="errors.has('category')"
                  class="help is-danger inline h-1 w-1 whitespace-no-wrap"
                >{{ errors.first('category') }}</span>
              </div>

              <div class="flex flex-col mb-4">
                <label class="text-sm rubik-regular" for="firstName">When did the offense occur?</label>

                <div class="flex flex-col w-1/2">
                  <b-datepicker
                    v-model="report.date"
                    placeholder="Click to select..."
                    icon="calendar-today"
                    class="mr-2"
                    v-validate="'required'"
                    data-vv-as="date"
                    name="date"
                    ref="date"
                  ></b-datepicker>

                  <span
                    v-show="errors.has('date')"
                    class="help is-danger inline h-1 w-1 whitespace-no-wrap"
                  >{{ errors.first('date') }}</span>
                </div>
              </div>

              <div class="flex mb-4">
                <div class="w-2/3">
                  <label
                    v-if="reportedResourceType !== 'profiles'"
                    class="text-sm rubik-regular"
                    for="firstName"
                  >Where did the offense occur?</label>
                  <label v-else class="text-sm rubik-regular" for>Who committed the offense?</label>

                  <p class="text-teal">{{ reported_resource.name || reported_resource.slug }}</p>
                </div>
              </div>

              <div class="flex flex-col">
                <label class="block text-sm mb-1 rubik-regular" for="description">Description</label>

                <textarea
                  class="appearance-none border border-grey-darker rounded-lg w-full py-2 px-3 text-grey-darker bg-white"
                  name="description"
                  id="description"
                  cols="30"
                  rows="4"
                  placeholder="Write a description about the community"
                  v-model="report.description"
                  v-validate="'required'"
                  data-vv-as="description"
                  ref="description"
                ></textarea>

                <span
                  v-show="errors.has('description')"
                  class="help is-danger inline h-1 w-1 whitespace-no-wrap"
                >{{ errors.first('description') }}</span>
              </div>
            </div>

            <div class="w-1/3 ml-auto flex flex-col">
              <resource-image-picker class :profile="profile" v-on:image-chosen="imageChosen"></resource-image-picker>
            </div>
          </div>

          <div class="flex">
            <button
              class="block mx-auto flex bg-teal-light border border-teal-light hover:bg-white hover:text-teal-light text-white text-sm px-6 py-2 mt-4 mr-10 mb-6 float-right"
              @click="createReport"
            >
              Submit
              <div class="block flex ml-5 spinner" v-if="loading"></div>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import moment from "moment";
import { mapState, mapMutations, mapActions } from "vuex";
import Multiselect from "vue-multiselect";
import ResourceImagePicker from "./../utils/ResourceImagePicker.vue";
import closeMixin from "./../../mixins/close.js";

export default {
  mixins: [closeMixin],
  props: ["reported_resource"],
  created() {},
  computed: {
    ...mapState({
      profile: state => state.profiles.signedInProfile,
      reportedResourceType: state => state.reports.reportedResourceType
    })
  },
  watch: {},
  data() {
    return {
      offsenseCategories: {
        1: {
          id: 1,
          name: "offense 1"
        },
        2: {
          id: 2,
          name: "offense 2"
        },
        3: {
          id: 3,
          name: "offense 3"
        },
        4: {
          id: 4,
          name: "offense 4"
        },
        5: {
          id: 5,
          name: "offense 5"
        },
        6: {
          id: 6,
          name: "offense 6"
        }
      },
      report: {
        category: null,
        description: null,
        image: null,
        date: null
      },
      finishLoading: false,
      loading: false
    };
  },

  methods: {
    imageChosen(value) {
      this.community.image = value;
    },
    async createReport() {
      this.loading = true;
      let that = this;

      this.$validator.validateAll().then(async result => {
        if (result) {
          await axios
            .post("/reports/create", {
              reportedResourceType: this.reportedResourceType,
              category: this.report.category,
              description: this.report.description,
              reportedResource: this.reported_resource,
              date: this.report.date
            })
            .then(response => {
              that.loading = false;
              that.$store.dispatch("ui/setReportView", false);

              this.$toast.open({
                duration: 5000,
                message: "Report sent.",
                position: "is-bottom",
                type: "is-success"
              });
            });
        }
      });

      this.loading = false;
    },
    close() {
      this.$store.dispatch("ui/setReportView", false);
    }
  },
  components: { moment, Multiselect, ResourceImagePicker }
};
</script>

<style lang="scss" scoped>
</style>
