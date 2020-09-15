<template>
  <div class="w-3/4 p-8 text-black pt-2 text-base" style>
    <div class="w-full px-4 py-4 flex justify-between items-center">
      <p class="w-2/5 text-grey-dark">{{ $t('components.settings.searchability') }}</p>

      <m-select
        :valueToEdit.sync="searchabilityPrivacy"
        placeholder="Privacy"
        :options="privacyValuesForSearchability"
        :multiple="false"
        :trackBy="'key'"
        :label="'name'"
        :resource="'searchability'"
        v-on:input:update-value="updateField($event, 'searchability')"
      ></m-select>
    </div>

    <div class="w-full px-4 py-4 border-t flex justify-between items-center">
      <p class="w-2/5 text-grey-dark">{{ $t('components.settings.name_seen_by_non_friends') }}</p>
    </div>

    <div class="w-full px-4 py-4 border-t flex justify-between items-center">
      <p class="w-2/5 text-grey-dark">{{ $t('components.events.my_events') }}</p>

      <m-select
        :valueToEdit.sync="myEventsPrivacy"
        placeholder="Privacy"
        :options="relationshipPrivacies"
        :multiple="false"
        :trackBy="'key'"
        :label="'name'"
        :resource="'my-events'"
        v-on:input:update-value="updateField($event, 'my-events')"
      ></m-select>
    </div>

    <div class="w-full px-4 py-4 border-t flex justify-between items-center">
      <p class="w-2/5 text-grey-dark">{{ $t('components.communities.my_communities') }}</p>
      <m-select
        :valueToEdit.sync="myCommunitiesPrivacy"
        placeholder="Privacy"
        :options="relationshipPrivacies"
        :multiple="false"
        :trackBy="'key'"
        :label="'name'"
        :resource="'my-communities'"
        v-on:input:update-value="updateField($event, 'my-communities')"
      ></m-select>
    </div>

    <div class="w-full px-4 py-4 border-t flex justify-between items-center">
      <p class="w-2/5 text-grey-dark">{{ $t('components.settings.my_friends') }}</p>

      <m-select
        :valueToEdit.sync="myFriendsPrivacy"
        placeholder="Privacy"
        :options="relationshipPrivacies"
        :multiple="false"
        :trackBy="'key'"
        :label="'name'"
        :resource="'my-friends'"
        v-on:input:update-value="updateField($event, 'my-friends')"
      ></m-select>
    </div>

    <div class="w-full px-4 py-4 border-t flex justify-between items-center">
      <p class="w-2/5 text-grey-dark">{{ $t('components.settings.my_likes') }}</p>
    </div>

    <div class="w-full px-4 py-4 border-t flex justify-between items-center">
      <p class="w-2/5 text-grey-dark">{{ $t('components.settings.blocked_users') }}</p>

      <div class="w-full flex justify-between items-center">
        <div class="w-3/5 px-4 py-3 h-12 flex">
          <p class="text whitespace-no-wrap" v-for="(item, index) in blockedProfiles" :key="index">
            <span v-if="index">{{ item.recipient.name }},&nbsp;</span>
          </p>
        </div>

        <div class="block cursor-pointer icon-grey colorize-teal w-5" @click="openBlockedProfiles">
          <img src="/svgs/pen.svg" alt />
        </div>
      </div>
    </div>

    <div class="w-full px-4 py-4 border-t flex justify-between items-center">
      <p class="w-2/5 text-grey-dark">{{ $t('components.settings.block_flagged_users') }}</p>
    </div>

    <transition name="fade">
      <modal
        v-if="activeModal"
        :current-component="activeModal"
        :standalone="true"
        v-on:modal:close="modalOpen = false"
      ></modal>
    </transition>
  </div>
</template>

<script>
import LiveEdit from "./../utils/LiveEdit.vue";
import Modal from "./../utils/Modal";
import mSelect from "./../utils/mSelect";
import { mapState, mapMutations, mapActions } from "vuex";

export default {
  computed: {
    ...mapState({
      profile: state => state.profiles.signedInProfile,
      language: state => state.i18n.locale,
      activeModal: state => state.ui.activeModal,
      blockedProfiles: state => state.profiles.blockedProfiles
    }),
    privacyValuesForSearchability() {
      return _.filter(this.relationshipPrivacies, function(value) {
        return (
          value.key === "visible_to_all" ||
          value.key === "visible_to_extended_friends" ||
          value.key === "hidden_from_search"
        );
      });
    }
  },
  async created() {
    let that = this;

    this.$store.dispatch("profiles/getBlockedProfiles");

    await axios.get("/relationshipPrivacies").then(response => {
      _.forEach(response.data.privacies, (value, key) => {
        value.name = value.name[that.language];
        that.relationshipPrivacies.push(value);
      });
    });

    if (this.profile.searchability_privacy !== null) {
      this.searchabilityPrivacy = {
        key: this.profile.searchability_privacy.key,
        name: this.profile.searchability_privacy.name[this.language]
      };
    }

    if (this.profile.my_friends_privacy !== null) {
      this.myFriendsPrivacy = {
        key: this.profile.my_friends_privacy.key,
        name: this.profile.my_friends_privacy.name[this.language]
      };
    }

    if (this.profile.my_events_privacy !== null) {
      this.myEventsPrivacy = {
        key: this.profile.my_events_privacy.key,
        name: this.profile.my_events_privacy.name[this.language]
      };
    }

    if (this.profile.my_communities_privacy !== null) {
      this.myCommunitiesPrivacy = {
        key: this.profile.my_communities_privacy.key,
        name: this.profile.my_communities_privacy.name[this.language]
      };
    }
  },
  data() {
    return {
      searchabilityPrivacy: null,
      myFriendsPrivacy: null,
      myEventsPrivacy: null,
      myCommunitiesPrivacy: null,
      privacies: [],
      relationshipPrivacies: [],
      searchability: "Searchable by all users on Meet and Mingle",
      nameSeen: "Entire First Name, Entire Last Name",
      myEvents: "Visible only to friends",
      myCommunities: "Visible to Public",
      myFriends: "Visible only to Me",
      modalName: "",
      myLikes: "Visible only to Friends",
      blockedUsers: "Matlab Code, Lex Luthor and 10 others",
      blockUsers: "Yes",
      modalOpen: false
    };
  },
  methods: {
    openBlockedProfiles() {
      this.$store.dispatch("ui/setActiveModal", "BlockedProfiles");
    },
    async updateField(value, resource) {
      if (resource === "searchability") {
        await axios
          .put("/user/settings/updateSearchabilityPrivacy", {
            privacy: value.value !== null ? value.value.key : null,
            resource: resource
          })
          .then(response => {
            this.$toast.open({
              duration: 5000,
              message: this.$t(
                "components.settings.toast.searchability_updated"
              ),
              position: "is-bottom",
              type: "is-success"
            });
          });
      } else if (resource === "my-friends") {
        await axios
          .put("/user/settings/updateMyFriendsPrivacy", {
            privacy: value.value !== null ? value.value.key : null,
            resource: resource
          })
          .then(response => {
            this.$toast.open({
              duration: 5000,
              message: this.$t(
                "components.settings.toast.friendships_privacy_updated"
              ),
              position: "is-bottom",
              type: "is-success"
            });
          });
      } else if (resource === "my-events") {
        await axios
          .put("/user/settings/updateMyEventsPrivacy", {
            privacy: value.value !== null ? value.value.key : null,
            resource: resource
          })
          .then(response => {
            this.$toast.open({
              duration: 5000,
              message: this.$t(
                "components.settings.toast.events_privacy_updated"
              ),
              position: "is-bottom",
              type: "is-success"
            });
          });
      } else if (resource === "my-communities") {
        await axios
          .put("/user/settings/updateMyCommunitiesPrivacy", {
            privacy: value.value !== null ? value.value.key : null,
            resource: resource
          })
          .then(response => {
            this.$toast.open({
              duration: 5000,
              message: this.$t(
                "components.settings.toast.communities_privacy_updated"
              ),
              position: "is-bottom",
              type: "is-success"
            });
          });
      }
    },
    openModal(componentName) {
      this.modalOpen = true;
      this.modalName = componentName;
    }
  },
  components: {
    LiveEdit,
    Modal,
    mSelect
  }
};
</script>

<style lang="sass" scoped>

</style>
