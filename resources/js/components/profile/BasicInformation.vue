<template>
  <div class="w-3/4 h-full p-8 text-black pt-24 text-base">
    <div class="w-full px-4 py-4 border-t flex justify-between items-center">
      <p class="w-2/5 text-grey-dark">{{ $t('components.register.email')}}</p>

      <live-edit
        class
        :circleName="this.user.email"
        placeholder="email"
        :profileUpdate="false"
        :multiline="false"
        v-on:input:update-value="updateField($event)"
      ></live-edit>
    </div>

    <div class="w-full px-4 py-4 border-t flex justify-between items-center">
      <p class="w-2/5 text-grey-dark">{{ $t('components.settings.phone_number') }}</p>

      <div class="w-full flex justify-between items-center">
        <p class="w-3/5 px-4 py-3 h-12">{{ user.phone_number }}</p>

        <div class="block cursor-pointer" @click="openModal('phone-registration')">
          <font-awesome-icon
            class="icon text-grey-darker hover:text-teal cursor-pointer"
            :icon="['fa', 'pen']"
            size="lg"
          />
        </div>
      </div>
    </div>

    <div class="w-full px-4 py-4 border-t flex justify-between items-center">
      <p class="w-2/5 text-grey-dark">{{ $t('components.register.change_password') }}</p>

      <div class="w-full flex justify-between items-center">
        <p class="w-3/5 px-4 py-3 h-12">***********</p>

        <div class="block cursor-pointer" @click="openModal('change-password')">
          <font-awesome-icon
            class="icon text-grey-darker hover:text-teal cursor-pointer"
            :icon="['fa', 'pen']"
            size="lg"
          />
        </div>
      </div>
    </div>

    <div class="w-full px-4 py-4 border-t flex justify-between items-center">
      <p class="w-2/5 text-grey-dark">{{ $t('components.settings.birthday')}}</p>

      <div class="w-full flex justify-between items-center">
        <p class="w-3/5 px-4 py-3 h-12" v-if="dob">{{ month + ' ' + day + ', ' + year }}</p>
        <p class="w-3/5 px-4 py-3 h-12" v-else>{{ $t('components.settings.enter_date_of_birth') }}</p>

        <div class="block cursor-pointer" @click="openModal('date-of-birth')">
          <font-awesome-icon
            class="icon text-grey-darker hover:text-teal cursor-pointer"
            :icon="['fa', 'pen']"
            size="lg"
          />
        </div>
      </div>
    </div>

        <div class="w-full px-4 py-4 border-t flex justify-between items-center">
      <p class="w-2/5 text-grey-dark">{{ $t('components.settings.about_you')}}</p>

      <div class="w-full flex justify-between items-center">
        <p class="w-3/5 px-4 py-3 h-12">{{ $t('components.settings.enter_description') }}</p>

        <div class="block cursor-pointer" @click="openModal('description')">
          <font-awesome-icon
            class="icon text-grey-darker hover:text-teal cursor-pointer"
            :icon="['fa', 'pen']"
            size="lg"
          />
        </div>
      </div>
    </div>

    <div class="w-full px-4 py-4 border-t flex justify-between items-center">
      <p class="w-2/5 text-grey-dark">{{ $t('components.settings.deactivate')}}</p>

      <div class="w-full flex justify-between items-center">
        <div class="flex w-4/5 px-4 py-3 h-12" v-if="deactivateAccountClicked">
          <p class :class="(language === 'ar') ? 'ml-4' : 'mr-4'">
            <span v-if="active">{{ $t('components.settings.deactivate')}}</span>
            <span v-else>{{ $t('components.settings.activate')}}</span>
            {{ $t('shared.account')}}
          </p>
          <div
            class="cursor-pointer"
            :class="(language === 'ar') ? 'ml-8' : 'mr-8'"
            @click="openModal('deactivate-account')"
          >
            <font-awesome-icon
              class="icon text-grey-darker hover:text-teal cursor-pointer"
              :icon="['fa', 'pen']"
              size="lg"
            />
          </div>

          <p class :class="(language === 'ar') ? 'ml-4' : 'mr-4'">
            {{ $t('shared.delete')}}
            {{ $t('shared.account')}}
          </p>

          <div class="block cursor-pointer" @click="openModal('delete-account')">
            <font-awesome-icon
              class="icon text-grey-darker hover:text-teal cursor-pointer"
              :icon="['fa', 'pen']"
              size="lg"
            />
          </div>
        </div>

        <p class="w-3/5 px-4 py-3 h-12" v-else>{{ $t('components.settings.deactivate_or_delete') }}</p>

        <div class="block">
          <div
            class="cursor-pointer"
            v-if="!deactivateAccountClicked"
            @click="deactivateAccountClicked = true"
          >
            <font-awesome-icon
              class="icon text-grey-darker hover:text-teal cursor-pointer"
              :icon="['fa', 'pen']"
              size="lg"
            />
          </div>

          <div class="cursor-pointer" v-else @click="deactivateAccountClicked = false">
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
    
    <!-- 
    <transition name="fade">
      <modal
        v-if="modalOpen"
        :current-component="modalName"
        :standalone="true"
        :profile="this.profile"
        :dob="this.dob"
        :active="this.active"
        v-on:modal:close="closeModal($event)"
        v-on:modal:password-close="closeModal()"
        v-on:modal:deactivate="deactivate()"
        v-on:modal:update-user="updateUser"
      ></modal>
    </transition>-->
  </div>
</template>

<script>
import LiveEdit from "./../utils/LiveEdit.vue";
import Modal from "./../utils/Modal";
import moment from "moment";
import { mapState, mapMutations, mapActions } from "vuex";

export default {
  props: ["user", "dob", "active"],

  computed: {
    ...mapState({
      language: state => state.i18n.locale,
      profile: state => state.profiles.signedInProfile
    })
  },
  data() {
    return {
      email: "zarkoperovic@gmail.com",
      phoneNumber: "+8(378)987-3876",
      phoneRegistrationModalOpen: false,
      dateOfBirthModalOpen: false,
      modalOpen: false,
      modalName: "",
      deactivateAccountClicked: false,
      profileName: "Zarko Perovic",
      newProfileName: "Zarko Perovic",
      editProfileName: false,
      day: null,
      month: null,
      year: null
    };
  },
  watch: {
    dob(value) {
      var check = moment(this.dob, "YYYY/MM/DD");

      if (this.dob !== null) {
        this.day = check.format("D");
        this.month = check.format("MMMM");
        this.year = check.format("YYYY");
      }
    }
  },
  created() {
    var check = moment(this.dob, "YYYY/MM/DD");

    if (this.dob !== null) {
      this.day = check.format("D");
      this.month = check.format("MMMM");
      this.year = check.format("YYYY");
    }
  },
  methods: {
    updateUser(value) {
      this.user = value;
      this.closeModal();

      this.$emit("modal:update-user", value);
    },
    deactivate() {
      this.active = 0;
    },
    activate() {
      this.activate = true;
    },
    // openModal(componentName) {
    //   this.modalOpen = true;
    //   this.modalName = componentName;
    // },
    openModal(name) {
      this.$store.dispatch("ui/setActiveModal", name);
    },
    setProfileName() {
      this.profileName = this.newProfileName;
      this.editProfileName = false;

      this.$toast.open({
        duration: 5000,
        message: this.$t("components.settings.profile_name_changed"),
        position: "is-bottom",
        type: "is-success"
      });
    },
    async updateField(value) {
      if (value.resource === "email") {
        await axios
          .put("/user/settings/updateEmail", {
            email: value.value
          })
          .then(response => {
            this.user.email = value.value;

            this.$toast.open({
              duration: 5000,
              message: this.$t("components.settings.email_changed"),
              position: "is-bottom",
              type: "is-success"
            });
          });
      }
    },
    closeModal(value) {
      if (value !== undefined) {
        this.modalOpen = false;

        this.dob = value;

        var check = moment(value, "YYYY/MM/DD");

        this.day = check.format("D");
        this.month = check.format("MMMM");
        this.year = check.format("YYYY");
      } else {
        this.modalOpen = false;
      }
    },
    resetProfileName() {
      this.newProfileName = this.profileName;
      this.editProfileName = false;
    }
  },
  components: {
    LiveEdit,
    Modal,
    moment
  }
};
</script>

<style lang="sass" scoped>

</style>
