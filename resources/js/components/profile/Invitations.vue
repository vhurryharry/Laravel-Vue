<template>
  <div class="w-3/4 h-full p-8 text-black pt-2 text-base">
    <p class="text-lg mt-4 border-b-2">{{ $t('components.settings.invitations') }}</p>

    <div
      class="text-black flex w-full justify-between mt-2"
      v-for="(invitation, index) in invitations"
      :key="index"
    >
      <p>
        <a
          class="text-teal"
          :href="'/profiles/' + invitation.profile.username"
        >{{ invitation.profile.name }}</a>

        <span
          v-if="invitation.event"
        >{{ $t('components.profiles.invited_you_to_enter') }} {{ invitation.event.name }}</span>
        <span
          v-if="invitation.community"
        >{{ $t('components.profiles.invited_you_to_enter') }} {{ invitation.community.name }}</span>
      </p>

      <div class="block flex justify-center items-center">
        <div class="icon-teal cursor-pointer w-6" :class="(language === 'ar') ? 'ml-4' : 'mr-4'">
          <img
            src="/svgs/check.svg"
            alt
            @click="acceptInvitation(invitation.invitations_id, invitation.invitations_type, invitation.uuid)"
          />
        </div>

        <div class="icon-red colorize-teal cursor-pointer w-4">
          <img
            src="/svgs/close.svg"
            alt
            @click="refuseInvitation(invitation.invitations_id, invitation.invitations_type, invitation.uuid)"
          />
        </div>
      </div>
    </div>

    <p class="text-lg mt-4 border-b-2">{{ $t('components.settings.requests') }}</p>

    <div
      class="text-black flex w-full justify-between mt-2"
      v-for="(request, index) in requests"
      :key="index"
    >
      <p>
        <a class="text-teal" :href="'/profiles/' + request.sender.username">{{ request.sender.name}}</a>
        {{ $t('components.profiles.toast.sent_you_a_friend_request')}}
      </p>

      <div class="block flex justify-center items-center">
        <div class="icon-teal cursor-pointer w-6" :class="(language === 'ar') ? 'ml-4' : 'mr-4'">
          <img
            src="/svgs/check.svg"
            alt
            @click="acceptRequest(request.sender.username, request.id, request.uuid)"
          />
        </div>

        <div class="icon-red colorize-teal cursor-pointer w-4">
          <img
            src="/svgs/close.svg"
            alt
            @click="refuseRequest(request.sender.username, request.id, request.uuid)"
          />
        </div>
      </div>
    </div>

    <div
      class="text-black flex w-full justify-between mt-2"
      v-for="(request, index) in resourceRequests"
      :key="index"
    >
      <p>
        <a
          class="text-teal"
          :href="'/profiles/' + request.profile.username"
        >{{ request.profile.name }}</a>

        <span
          v-if="request.event"
        >{{ $t('components.profiles.requested_to_enter') }} {{ request.event.name }}</span>
        <span
          v-if="request.community"
        >{{ $t('components.profiles.requested_to_enter') }} {{ request.community.name }}</span>
      </p>

      <div class="block flex justify-center items-center">
        <div class="icon-teal cursor-pointer w-6" :class="(language === 'ar') ? 'ml-4' : 'mr-4'">
          <img
            src="/svgs/check.svg"
            alt
            @click="acceptResourceRequest(request.requests_id, request.requests_type, request.profile.id, index, request.uuid)"
          />
        </div>

        <div class="icon-red colorize-teal cursor-pointer w-4">
          <img
            src="/svgs/close.svg"
            alt
            @click="refuseResourceRequest(request.requests_id, request.requests_type, request.profile.id, index, request.uuid)"
          />
        </div>
      </div>
    </div>

    <transition name="fade">
      <modal
        v-if="modalOpen"
        :current-component="modalName"
        :standalone="true"
        v-on:modal:close="modalOpen = false"
      ></modal>
    </transition>
  </div>
</template>

<script>
import LiveEdit from "./../utils/LiveEdit.vue";
import Modal from "./../utils/Modal";
import uuidv4 from "uuid/v4";
import { mapState, mapMutations, mapActions } from "vuex";

export default {
  computed: {
    ...mapState({
      language: state => state.i18n.locale
    })
  },
  async mounted() {
    await axios
      .get("/invitations")
      .then(response => {
        response.data.eventInvitations.forEach(element => {
          let uuid = uuidv4();

          element.uuid = uuid;

          this.invitations = {
            ...this.invitations,
            [uuid]: element
          };
        });

        response.data.communityInvitations.forEach(element => {
          let uuid = uuidv4();

          element.uuid = uuid;

          this.invitations = {
            ...this.invitations,
            [uuid]: element
          };
        });
      })
      .catch(error => {
        this.loading = false;
      });
    await axios
      .get("/requests")
      .then(response => {
        response.data.requests.forEach(element => {
          let uuid = uuidv4();
          element.uuid = uuid;

          this.requests = {
            ...this.requests,
            [uuid]: element
          };
        });
      })
      .catch(error => {
        this.loading = false;
      });

    await axios
      .get("/getRequests")
      .then(response => {
        response.data.requests.forEach(element => {
          let uuid = uuidv4();
          element.uuid = uuid;

          this.resourceRequests = {
            ...this.resourceRequests,
            [uuid]: element
          };
        });
      })
      .catch(error => {
        this.loading = false;
      });
  },
  data() {
    return {
      invitations: {},
      requests: {},
      resourceRequests: {},
      modalName: "",
      modalOpen: false
    };
  },
  methods: {
    async acceptInvitation(invitationId, invitationsType, uuid) {
      await axios
        .post("/invitations/accept", {
          invitation_id: invitationId,
          invitations_type: invitationsType
        })
        .then(response => {
          this.$toast.open({
            duration: 5000,
            message: this.$t("shared.toast.invitation_accepted"),
            position: "is-bottom",
            type: "is-success"
          });

          Vue.delete(this.invitations, uuid);
        })
        .catch(error => {
          this.loading = false;
        });
    },
    async refuseInvitation(invitationId, invitationsType, uuid) {
      await axios
        .post("/invitations/refuse", {
          invitation_id: invitationId,
          invitations_type: invitationsType
        })
        .then(response => {
          this.$toast.open({
            duration: 5000,
            message: this.$t("shared.toast.invitation_refused"),
            position: "is-bottom",
            type: "is-success"
          });

          Vue.delete(this.invitations, uuid);
        })
        .catch(error => {
          this.loading = false;
        });
    },

    async acceptRequest(senderUsername, requestId, uuid) {
      await axios
        .post("/requests/accept", {
          username: senderUsername
        })
        .then(response => {
          this.$toast.open({
            duration: 5000,
            message: this.$t(
              "components.profiles.toast.friend_request_accepted"
            ),
            position: "is-bottom",
            type: "is-success"
          });
          Vue.delete(this.requests, uuid);
        })
        .catch(error => {
          this.loading = false;
        });
    },
    async refuseRequest(senderUsername, requestId, uuid) {
      await axios
        .post("/requests/refuse", {
          username: senderUsername
        })
        .then(response => {
          this.$toast.open({
            duration: 5000,
            message: this.$t(
              "components.profiles.toast.friend_request_refused"
            ),
            position: "is-bottom",
            type: "is-success"
          });

          Vue.delete(this.requests, uuid);
        })
        .catch(error => {
          this.loading = false;
        });
    },

    async acceptResourceRequest(
      requestId,
      requestsType,
      requesterId,
      id,
      uuid
    ) {
      await axios
        .post("/resourceRequests/accept", {
          request_id: requestId,
          requests_type: requestsType,
          requester_id: requesterId
        })
        .then(response => {
          this.$toast.open({
            duration: 5000,
            message: this.$t("shared.toast.request_accepted"),
            position: "is-bottom",
            type: "is-success"
          });

          Vue.delete(this.resourceRequests, uuid);
        })
        .catch(error => {
          this.loading = false;
        });
    },
    async refuseResourceRequest(
      requestId,
      requestsType,
      requesterId,
      id,
      uuid
    ) {
      await axios
        .post("/resourceRequests/refuse", {
          request_id: requestId,
          requests_type: requestsType,
          requester_id: requesterId
        })
        .then(response => {
          this.$toast.open({
            duration: 5000,
            message: this.$t("shared.toast.request_accepted"),
            position: "is-bottom",
            type: "is-success"
          });

          Vue.delete(this.resourceRequests, uuid);
        })
        .catch(error => {
          this.loading = false;
        });
    },
    openModal(componentName) {
      this.modalOpen = true;
      this.modalName = componentName;
    }
  },
  components: {
    LiveEdit,
    Modal
  }
};
</script>

<style lang="sass" scoped>
</style>
