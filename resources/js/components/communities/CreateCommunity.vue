<template>
  <div class="fixed pin flex items-center">
    <div class="fixed pin bg-black opacity-50 z-10"></div>

    <div
      class="relative mx-6 md:mx-auto w-full md:w-1/2 lg:w-2/4 z-20"
      v-if="createCommunityInitialization"
    >
      <div class="shadow-lg bg-grey rounded text-grey-darkest">
        <form class="flex flex-col" @submit.prevent>
          <div
            class="flex items-center bg-teal justify-between py-4"
            :class="(language === 'ar') ? 'pr-10' : 'pl-10'"
          >
            <p class="text-white text-lg">{{ $t('components.communities.create_a_community') }}</p>
            <button :class="(language === 'ar') ? 'ml-4' : 'mr-4'" @click="close()">
              <img class="invert-icon w-4" src="/svgs/close.svg" alt />
            </button>
          </div>

          <div class="flex px-10 pt-4">
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
                    v-model="community.name"
                    maxlength="65"
                    name="name"
                    v-validate="'required'"
                    :class="{'is-danger': errors.has('name') }"
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
                    v-model="community.communityPrivacy"
                    native-value="public"
                    v-validate="'required|included:public,private,secret'"
                    name="community_privacy"
                    data-vv-as="community privacy"
                  >
                    <span :class="(language === 'ar') ? 'pr-1' : 'pl-0'">{{ $t('shared.public') }}</span>
                  </b-radio>
                  <b-radio
                    :class="(language === 'ar') ? 'ml-3' : 'mr-3'"
                    v-model="community.communityPrivacy"
                    native-value="private"
                  >
                    <span
                      class
                      :class="(language === 'ar') ? 'pr-1' : 'pl-0'"
                    >{{ $t('shared.private') }}</span>
                  </b-radio>
                  <b-radio
                    :class="(language === 'ar') ? 'ml-3' : 'mr-3'"
                    v-model="community.communityPrivacy"
                    native-value="secret"
                  >
                    <span
                      class
                      :class="(language === 'ar') ? 'pr-1' : 'pl-0'"
                    >{{ $t('shared.secret') }}</span>
                  </b-radio>

                  <br />
                  <span
                    v-show="errors.has('community_privacy')"
                    class="help is-danger inline h-1 w-1 whitespace-no-wrap"
                  >{{ errors.first('community_privacy') }}</span>
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
                    v-if="interests"
                    v-model="interestValues"
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
                    v-validate="'required'"
                    data-vv-as="interest values"
                    name="interest_values"
                    ref="interest_values"
                  >
                    <!-- <template slot="singleLabel" slot-scope="{ option }">
                    <p class>{{ option.name }}</p>
                    </template>-->

                    <template slot="maxElements" slot-scope="{ option }">
                      <p
                        class="text-teal text-base"
                      >{{ $t('components.communities.max_4_interests') }}</p>
                    </template>
                  </multiselect>

                  <span
                    v-show="errors.has('interest_values')"
                    class="help is-danger inline h-1 w-1 whitespace-no-wrap"
                  >{{ errors.first('interest_values') }}</span>
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
                v-model="community.body"
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
              <resource-image-picker class :profile="profile" v-on:image-chosen="imageChosen"></resource-image-picker>
            </div>
          </div>

          <div class="flex">
            <button
              class="block mx-auto flex items-center bg-teal-light border border-teal-light hover:bg-white hover:text-teal-light text-white text-sm px-6 py-2 mt-4 mr-10 mb-6 float-right"
              @click="createCommunity()"
            >
              {{ $t('shared.next') }}
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
  computed: {
    ...mapState({
      profile: state => state.profiles.signedInProfile,
      language: state => state.i18n.locale
    })
  },
  created() {
    axios.get("/interests").then(response => {
      let that = this;
      response.data.interests.forEach(element => {
        let interestElement = element;
        interestElement.name = interestElement.name[that.language];
        that.interests.push(interestElement);
      });
    });

    this.$store.dispatch("profiles/getProfiles");
  },

  watch: {
    guestList(value) {
      this.guestSize = Object.keys(this.guestList).length;
    }
  },
  data() {
    return {
      interests: [],
      guestSize: 0,
      profiles: {},
      guestList: {},
      interestValues: [],
      community: {
        name: null,
        communityPrivacyName: null,
        communityPrivacyName: null,
        body: null,
        image: null
      },
      createdCommunity: null,
      loading: false,
      finishLoading: false,

      date: new Date().toISOString().substr(0, 10),
      menu: false,
      modal: false,
      menu2: false,
      dropFiles: [],
      createCommunityInitialization: true,
      privacyRadio: null,
      privacyRadio2: null,
      selectedInterest: null
    };
  },

  methods: {
    imageChosen(value) {
      this.community.image = value;
    },
    moveToGuestList(id) {
      this.guestList = {
        ...this.guestList,
        [id]: this.profiles[id]
      };

      Vue.delete(this.profiles, id);
    },
    removeFromGuestList(id) {
      this.profiles = {
        ...this.profiles,
        [id]: this.guestList[id]
      };

      Vue.delete(this.guestList, id);
    },
    async inviteToCommunity() {
      this.finishLoading = true;
      await axios
        .post("/communities/invite", {
          community_slug: this.createdCommunity.slug,
          guest_list: this.guestList
        })
        .then(response => {
          this.finishLoading = false;

          this.$store.dispatch(
            "communities/addMyCommunity",
            response.data.community
          );

          this.$emit("create:community");
        })
        .catch(error => {
          this.finishLoading = false;
        });
    },
    createCommunity() {
      this.loading = true;

      this.$validator.validateAll().then(async result => {
        if (result) {
          await axios
            .post("/communities/create", {
              name: this.community.name,
              community_privacy: this.community.communityPrivacy,
              body: this.community.body,
              interests: this.interestValues,
              image: this.community.image
            })
            .then(response => {
              this.createdCommunity = response.data.community_object;
              this.$store.dispatch(
                "communities/setNewCommunity",
                response.data.community_object
              );
              this.loading = false;
              this.createCommunityInitialization = false;
              this.$emit("create:done");

              this.$store.dispatch("ui/setInviteToResource", "communities");
              this.$store.dispatch("ui/setActiveModal", "invite-to-resource");
            })
            .catch(error => {
              this.loading = false;
            });
        } else {
          this.loading = false;
        }
      });
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
