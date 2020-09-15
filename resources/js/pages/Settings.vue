<template>
  <div class="flex flex-col page w-full bg-grey">
    <div class="flex flex-col px-24 page-container pt-6">
      <a
        class="mb-6"
        :href="'/profiles/' + user.username"
        :class="(language === 'ar') ? 'mr-auto' : 'ml-auto'"
      >
        <button
          class="block bg-teal-dark border border-teal-dark hover:bg-white hover:text-teal-dark text-white text-base px-4 py-2"
        >{{ $t('components.settings.back_to_profile') }}</button>
      </a>

      <div class="flex w-full items-start">
        <div class="w-1/3 bg-white text-black settings-nav">
          <p
            id="basic-information"
            class="w-full p-4 border-b"
            @click="setComponent('basic-information')"
          >{{ $t('components.settings.account_information') }}</p>

          <p
            id="about"
            class="w-full p-4 border-b"
            @click="setComponent('about')"
          >{{ $t('components.settings.about_you') }}</p>

          <p
            id="privacy"
            class="w-full p-4 border-b"
            @click="setComponent('privacy')"
          >{{ $t('components.settings.privacy') }}</p>

          <p
            id="invitations"
            class="w-full p-4 border-b"
            @click="setComponent('invitations')"
          >{{ $t('components.settings.invitations') }}</p>
        </div>

        <div
          class="w-2/3 settings-container bg-white flex h-full"
          :class="(language === 'ar') ? 'mr-8' : 'ml-8'"
        >
          <div class="w-1/4 h-full bg-teal text-center">
            <p class="py-8">{{ $t('components.settings.basic_information') }}</p>

            <image-picker class :profile="profile"></image-picker>

            <div class="h-16" v-if="!editProfileName">
              <p
                class="text-xl mb-4 mt-8 rubik-medium"
                :class="(language === 'ar') ? 'mr-1' : 'ml-1'"
              >{{ newProfileName }}</p>

              <div class @click="editProfileName = true">
                <font-awesome-icon
                  class="icon text-grey hover:text-black cursor-pointer"
                  :icon="['fa', 'pen']"
                  size="lg"
                />
              </div>
            </div>

            <div class="h-16 mb-4" v-else>
              <input
                class="text-lg mt-6 mb-4 text-center rubik-medium bg-white text-black w-3/4 px-4 py-3"
                type="text"
                v-model="newProfileName"
                @keyup.enter="setProfileName($event)"
              />

              <div class="block flex justify-center items-center h-4 mt-2">
                <div
                  class="cursor-pointer"
                  :class="(language === 'ar') ? 'ml-4' : 'mr-4'"
                  @click="setProfileName()"
                >
                  <font-awesome-icon
                    class="icon text-grey cursor-pointer hover:black"
                    :icon="['fa', 'check']"
                    size="lg"
                  />
                </div>

                <div class="cursor-pointer" @click.stop="deactivateNameEditField()">
                  <font-awesome-icon
                    class="icon text-red cursor-pointer hover:text-black"
                    :icon="['fa', 'times']"
                    size="lg"
                    @click="refuseRequest(profile.username, profile.id)"
                  />
                </div>
              </div>
            </div>
          </div>

          <keep-alive>
            <component
              v-bind:is="currentComponent"
              :user="this.user"
              :dob="this.profile.date_of_birth"
              :active="this.profile.active"
              v-on:modal:update-user="updateUser"
            ></component>
          </keep-alive>
        </div>
      </div>
    </div>
    <b-notification
      class="absolute w-2/5 mx-auto pin-x"
      type="is-danger"
      :active.sync="verifiedAlert"
      aria-close-label="Close notification"
    >Account not verified. Please verify by registering a phone number.</b-notification>

    <footer-component class style="margin-top:auto;"></footer-component>
  </div>
</template>

<script>
import LiveEdit from "./../components/utils/LiveEdit.vue";
import Modal from "./../components/utils/Modal.vue";
import BasicInformation from "./../components/profile/BasicInformation.vue";
import About from "./../components/profile/About.vue";
import Privacy from "./../components/profile/Privacy.vue";
import Invitations from "./../components/profile/Invitations.vue";
import ImagePicker from "./../components/utils/ImagePicker.vue";
import { mapState, mapMutations, mapActions } from "vuex";
import { NotificationProgrammatic as Notification } from "buefy";

export default {
  props: ["user"],

  computed: {
    ...mapState({
      language: state => state.i18n.locale,
      profile: state => state.profiles.signedInProfile
    })
  },
  mounted() {
    if (!this.user.verified) {
      this.verifiedAlert = true;
    } else {
      this.verifiedAlert = false;
    }

    if (location.hash) {
      this.currentComponent = location.hash.substr(1);
      $("#" + location.hash.substr(1)).addClass("active");
    } else {
      window.location.hash = "#basic-information";
      $("#basic-information").addClass("active");
    }
  },
  data() {
    return {
      verifiedAlert: false,
      currentComponent: "basic-information",
      phoneRegistrationModalOpen: false,
      dateOfBirthModalOpen: false,
      modalOpen: false,
      modalName: "",
      deactivateAccountClicked: false,
      profileName: this.user.name,
      newProfileName: this.user.name,
      editProfileName: false,
      isActive: false
    };
  },
  watch: {},
  methods: {
    updateUser(value) {
      this.user = value;
      if (value.verified) {
        this.verifiedAlert = false;
      } else {
        this.verifiedAlert = true;
      }
    },

    setComponent(value) {
      window.location.hash = "#" + value;
      this.currentComponent = value;
      $("p").removeClass("active");

      $("#" + value).addClass("active");
    },
    openModal(componentName) {
      this.modalOpen = true;
      this.modalName = componentName;
    },
    deactivateNameEditField() {
      this.editProfileName = false;
    },
    async setProfileName() {
      await axios
        .put("/user/settings/updateName", {
          name: this.newProfileName
        })
        .then(response => {
          this.profileName = this.newProfileName;
          this.editProfileName = false;

          this.$toast.open({
            duration: 5000,
            message: this.$t("components.settings.toast.profile_name_changed"),
            position: "is-bottom",
            type: "is-success"
          });
        })
        .catch(error => {
          this.editProfileName = false;

          this.$toast.open({
            duration: 5000,
            message: `Error.`,
            position: "is-bottom",
            type: "is-failure"
          });
        });
    },
    resetProfileName() {
      this.newProfileName = this.profileName;
      this.editProfileName = false;
    },
    myFilter: function() {
      this.isActive = !this.isActive;
    }
  },
  components: {
    LiveEdit,
    Modal,
    BasicInformation,
    About,
    Privacy,
    Invitations,
    ImagePicker
  }
};
</script>

<style lang="scss" scoped>
.active {
  color: teal;
  border-left: 2px solid teal;
}

.page {
  color: white;
  min-height: 90vh;
}

.page-container {
  height: 80vh;
  overflow-y: auto;
}

.settings-nav {
  height: auto;
  p {
    cursor: pointer;
    border-left: 2px solid white;
    transition: 75ms all ease-in-out;

    &:hover {
      color: teal;
      border-left: 2px solid teal;
    }
  }
}

.settings-container {
  min-height: 60vh;
}

.settings-profile-image {
  //   border-radius: 9999px;
  border-radius: 50%;
  width: 50px;
  height: 50px;
}
</style>
