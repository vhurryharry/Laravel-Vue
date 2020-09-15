<template>
  <div class="fixed pin flex items-center">
    <div
      class="relative mx-6 md:mx-auto w-full md:w-1/2 lg:w-2/4 z-20"
      v-if="createCommunityInitialization"
    >
      <div class="shadow-lg bg-grey rounded text-grey-darkest">
        <form class="flex flex-col" @submit.prevent>
          <div
            class="flex items-center justify-between py-4 bg-teal"
            :class="(language === 'ar') ? 'pr-10' : 'pl-10'"
          >
            <p class="text-white text-lg">{{ $t('shared.edit') }} {{ community.name }}</p>

            <button @click="close()">
              <img
                class="invert-icon w-4"
                :class="(language === 'ar') ? 'ml-4' : 'mr-4'"
                src="/svgs/close.svg"
                alt
              />
            </button>
          </div>

          <div class="flex pt-4 px-10">
            <div class="flex-col w-1/2">
              <div class="flex">
                <div class="flex flex-col w-2/3">
                  <label
                    class="text-sm rubik-regular"
                    for="firstName"
                  >{{ $t('components.communities.community_name') }}</label>
                  <input
                    class="w-5/6 py-2 px-3 text-grey-darker bg-white raleway-regular text-sm bg-white border border-grey"
                    id="firstName"
                    type="text"
                    :placeholder="$t('components.communities.write_community_name')"
                    v-model="editedCommunity.name"
                    maxlength="65"
                    name="name"
                    v-validate="'required'"
                  />

                  <span
                    v-show="errors.has('name')"
                    class="help is-danger inline h-1 w-1 whitespace-no-wrap"
                  >{{ errors.first('name') }}</span>
                </div>
              </div>

              <div class="flex-col py-4" :class="(language === 'ar') ? 'pl-10' : 'pr-10'">
                <label
                  class="text-sm rubik-regular"
                  for="firstName"
                >{{ $t('components.communities.community_privacy') }}</label>
                <div class="mt-2">
                  <b-radio
                    :class="(language === 'ar') ? 'ml-3' : 'mr-3'"
                    v-model="editedCommunity.community_privacy"
                    native-value="public"
                    v-validate="'required|included:public,private,secret'"
                    name="event_privacy"
                    data-vv-as="event privacy"
                  >
                    <span :class="(language === 'ar') ? 'pr-1' : 'pl-0'">{{ $t('shared.public') }}</span>
                  </b-radio>

                  <b-radio
                    class
                    :class="(language === 'ar') ? 'ml-3' : 'mr-3'"
                    v-model="editedCommunity.community_privacy"
                    native-value="private"
                  >
                    <span
                      class
                      :class="(language === 'ar') ? 'pr-1' : 'pl-0'"
                    >{{ $t('shared.private') }}</span>
                  </b-radio>

                  <b-radio
                    v-model="editedCommunity.community_privacy"
                    :class="(language === 'ar') ? 'ml-3' : 'mr-3'"
                    native-value="secret"
                  >
                    <span
                      class
                      :class="(language === 'ar') ? 'pr-1' : 'pl-0'"
                    >{{ $t('shared.secret') }}</span>
                  </b-radio>

                  <br />
                  <span
                    v-show="errors.has('event_privacy')"
                    class="help is-danger inline h-1 w-1 whitespace-no-wrap"
                  >{{ errors.first('event_privacy') }}</span>
                </div>
              </div>

              <div class="flex w-full mb-4">
                <div class="w-full">
                  <label
                    class="text-sm rubik-regular"
                    for="firstName"
                  >{{ $t('components.communities.interest_area') }}</label>
                  <multiselect
                    class="rounded bg-white text-black"
                    style
                    v-model="editedCommunity.interests"
                    track-by="name"
                    label="name"
                    :placeholder="$t('components.communities.select_interests')"
                    :options="interests"
                    :searchable="true"
                    :allow-empty="true"
                    :close-on-select="false"
                    :multiple="true"
                    :max-height="200"
                    :max="4"
                  >
                    <template slot="maxElements" slot-scope="{ option }">
                      <p
                        class="text-teal text-base"
                      >{{ $t('components.communities.max_4_interests') }}</p>
                    </template>
                  </multiselect>
                </div>
              </div>

              <label
                class="block text-sm mb-1 rubik-regular"
                for="communityDescription"
              >{{ $t('shared.description') }}</label>

              <textarea
                class="appearance-none border border-grey-darker rounded-lg w-full py-2 px-3 text-grey-darker bg-white"
                name="communityDescription"
                id="communityDescription"
                cols="30"
                rows="4"
                :placeholder="$t('components.communities.write_description')"
                v-model="editedCommunity.body"
                v-validate="'required'"
                data-vv-as="community description"
                ref="communityDescription"
              ></textarea>
              <span
                v-show="errors.has('communityDescription')"
                class="help is-danger inline h-1 w-1 whitespace-no-wrap"
              >{{ errors.first('communityDescription') }}</span>
            </div>

            <div class="w-1/3 flex flex-col" :class="(language === 'ar') ? 'mr-auto' : 'ml-auto'">
              <resource-image-picker
                class
                :profile="profile"
                :existingImage="editedCommunity.image_path"
                v-on:image-chosen="imageChosen"
              ></resource-image-picker>
            </div>
          </div>

          <div class="flex">
            <button
              class="block mx-auto flex items-center bg-teal-light border border-teal-light hover:bg-white hover:text-teal-light text-white text-sm px-6 py-2 mt-4 mr-10 mb-6 float-right"
              @click="updateCommunity()"
            >
              {{ $t('shared.update') }}
              <div
                class="block flex spinner"
                :class="(language === 'ar') ? 'mr-5' : 'ml-5'"
                v-if="loading"
              ></div>
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
  props: [],
  computed: {
    ...mapState({
      profile: state => state.profiles.signedInProfile,
      community: state => state.communities.activeCommunity,
      language: state => state.i18n.locale
    })
  },
  created() {
    let that = this;

    this.editedCommunity.slug = this.community.slug;
    this.editedCommunity.community_privacy = this.community.privacy.key;
    this.editedCommunity.interests = [];
    this.editedCommunity.name = this.community.name;
    this.editedCommunity.body = this.community.body;
    this.editedCommunity.image = this.community.image_path;

    if (this.community.interests !== null) {
      this.community.interests.forEach(element => {
        element.name = element.name[that.language];
        that.editedCommunity.interests.push(element);
      });
    }

    axios.get("/interests").then(response => {
      response.data.interests.forEach(element => {
        element.name = element.name[that.language];
        this.interests.push(element);
      });
    });
  },
  watch: {
    guestList(value) {
      this.guestSize = Object.keys(this.guestList).length;
    }
  },
  data() {
    return {
      interests: [],
      interestValues: [],
      createCommunityInitialization: {},
      editedCommunity: {
        slug: null,
        name: null,
        community_privacy: null,
        body: null,
        interests: null,
        image: null
      },
      createdCommunity: null,
      loading: false,
      dropFiles: [],
      createCommunityInitialization: true,
      selectedInterest: null
    };
  },

  methods: {
    imageChosen(value) {
      this.editedCommunity.image = value;
    },
    updateCommunity() {
      this.loading = true;

      this.$validator.validateAll().then(async result => {
        if (result) {
          await axios
            .patch("/community/" + this.community.slug + "/update", {
              slug: this.editedCommunity.slug,
              name: this.editedCommunity.name,
              community_privacy: this.editedCommunity.community_privacy,
              body: this.editedCommunity.body,
              interests: this.editedCommunity.interests,
              image: this.editedCommunity.image
            })
            .then(response => {
              this.loading = false;
              this.$emit("update:community");

              this.$store.dispatch(
                "communities/setActiveCommunity",
                response.data.community
              );
              this.$emit("modal:close");
            })
            .catch(error => {
              this.loading = false;
            });
        }
      });

      this.loading = false;
    },
    close() {
      this.$emit("modal:close");
    }
  },
  components: { moment, Multiselect, ResourceImagePicker }
};
</script>

<style lang="scss" scoped>
// @import ~'buefy/lib/buefy.css';
</style>
