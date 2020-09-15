<template>
  <div class="flex flex-col" style="height:90vh;">

    <div class="w-full h-full remote_video_container" style="height:70vh;">
      <div id="remoteTrack" class="w-1/4 flex"></div>
      <div class></div>
    </div>

    <div class="flex flex-col items-center absolute pin-b w-full" style="height:25vh;">
      <div class="flex justify-center" id="localTrack" style="width:300px;height:150px;"></div>

      <div class="flex px-10 py-3 my-4" style="background:rgba(0,0,0,0.2);">
        <div
          class="flex justify-center items-center bg-grey rounded-full cursor-pointer"
          :class="(language === 'ar') ? 'ml-4' : 'mr-4'"
          style="width:50px;height:50px;"
        >
          <font-awesome-icon
            class="icon text-teal-darker cursor-pointer hover:text-teal fa-2x"
            :icon="['fa', 'exchange-alt']"
          />
        </div>

        <div
          class="flex justify-center items-center bg-grey rounded-full cursor-pointer"
          :class="(language === 'ar') ? 'ml-4' : 'mr-4'"

          style="width:50px;height:50px;"
          @click="toggleLocalVolume()"
        >
          <font-awesome-icon
            class="icon text-teal-darker cursor-pointer hover:text-teal fa-2x"
            :icon="['fa', 'volume-up']"
            v-if="localVolume == true"
          />

          <font-awesome-icon
            class="icon text-teal-darker cursor-pointer hover:text-teal fa-2x"
            v-else
            :icon="['fa', 'volume-off']"
          />
        </div>

        <div
          class="flex justify-center items-center bg-grey rounded-full cursor-pointer"
          :class="(language === 'ar') ? 'ml-4' : 'mr-4'"

          style="width:50px;height:50px;"
        >
          <font-awesome-icon
            class="icon text-teal-darker cursor-pointer hover:text-teal fa-2x"
            :icon="['fa', 'info']"
          />
        </div>

        <div
          class="flex justify-center items-center bg-grey rounded-full cursor-pointer"
          :class="(language === 'ar') ? 'ml-4' : 'mr-4'"

          style="width:50px;height:50px;"
          @click="toggleLocalVideo()"
        >
          <font-awesome-icon
            class="icon text-teal-darker cursor-pointer hover:text-teal fa-2x"
            :icon="['fa', 'video']"
            v-if="localVideo == true"
          />

          <font-awesome-icon
            class="icon text-teal-darker cursor-pointer hover:text-teal fa-2x"
            v-else
            :icon="['fa', 'video-slash']"
          />
        </div>

        <div
          class="flex justify-center items-center bg-grey rounded-full cursor-pointer"
          :class="(language === 'ar') ? 'ml-4' : 'mr-4'"

          style="width:50px;height:50px;"
          @click="exitConference()"
        >
          <font-awesome-icon
            class="icon text-orange cursor-pointer hover:text-red fa-2x"
            :icon="['fa', 'stop']"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Twilio, {
  Video,
  connect,
  createLocalTracks,
  createLocalVideoTrack
} from "twilio-video";

import { mapState, mapMutations, mapActions } from "vuex";

export default {
  name: "twilio-video",
  computed: {
    ...mapState({
      profile: state => state.profiles.signedInProfile,
      logs: state => state.conferencing.logs,
      activeCircle: state => state.conferencing.activeCircle,
      bootstrapedActiveCircle: state =>
        state.conferencing.bootstrapedActiveCircle
    })
  },
  created() {
    this.createChat(this.bootstrapedActiveCircle.title);

    window.addEventListener("beforeunload", this.leaveCircleIfJoined);
  },
  mounted() {},
  data() {
    return {
      loading: false,
      data: {},
      localTrack: false,
      remoteTrack: "",
      previewTracks: "",
      identity: "",
      circleName: null,
      localVolume: true,
      localVideo: true,
      localVideoContainer: null
    };
  },
  props: ["username"],
  watch: {
    bootstrapedActiveCircle(value) {
      if (value) {
        this.createChat(this.bootstrapedActiveCircle.title);
      }
    }
  },
  methods: {
    async getAccessToken() {
      return await axios
        .post("/hallway/" + this.profile.username + "/access", {
          circle_name: this.bootstrapedActiveCircle.title
        })
        .then(response => {
          return response.data.token;
        });
    },
    dispatchLog(message) {
      this.$store.dispatch("conferencing/setLogs", message);
    },
    toggleLocalVolume() {
      let that = this;
      this.localVolume = !this.localVolume;

      var localParticipant = this.activeCircle.localParticipant;

      if (!this.localVolume) {
        localParticipant.audioTracks.forEach(audioTrack => {
          audioTrack.track.disable();
        });
      } else {
        localParticipant.audioTracks.forEach(audioTrack => {
          audioTrack.track.enable();
        });
      }
    },
    toggleLocalVideo() {
      let that = this;
      this.localVideo = !this.localVideo;

      var localParticipant = this.activeCircle.localParticipant;

      if (!this.localVideo) {
        localParticipant.videoTracks.forEach(videoTrack => {
          videoTrack.track.disable();
        });
      } else {
        localParticipant.videoTracks.forEach(videoTrack => {
          videoTrack.track.enable();
        });
      }
    },
    exitConference() {
      this.$emit("conference:exit");
    },
    attachTrack(track, container) {
      container.appendChild(track.attach());
    },
    attachTracks(tracks, container) {
      let that = this;
      tracks.forEach(function(track) {
        that.attachTrack(track, container);
      });
    },
    attachParticipantTracks(participant, container, isLocal) {
      participant.tracks.forEach(publication => {
        publication.on("subscribed", track => {
          document.getElementById("remoteTrack").appendChild(track.attach());
        });
      });
    },
    detachTrack(track) {
      track.detach().forEach(function(element) {
        element.remove();
      });
    },
    detachParticipantTracks(participant) {
      var tracks = this.getTracks(participant);
      tracks.forEach(this.detachTrack);
    },
    trackPublished(publication, container) {
      let that = this;
      if (publication.isSubscribed) {
        this.attachTrack(publication.track, container);
      }
      publication.on("subscribed", function(track) {
        that.attachTrack(track, container);
      });
      publication.on("unsubscribed", this.detachTrack);
    },

    trackUnpublished(publication) {},
    getTracks(participant) {
      return Array.from(participant.tracks.values())
        .filter(function(publication) {
          return publication.track;
        })
        .map(function(publication) {
          return publication.track;
        });
    },
    leaveCircleIfJoined() {
      if (this.activeCircle) {
        this.activeCircle.disconnect();
      }
    },
    participantConnected(participant, container) {
      let that = this;

      participant.tracks.forEach(function(publication) {
        that.trackPublished(publication, container);
      });
      participant.on("trackPublished", function(publication) {
        that.trackPublished(publication, container);
      });
      participant.on("trackUnpublished", this.trackUnpublished);
    },
    createChat(circle_name) {
      this.loading = true;
      const VueThis = this;

      this.getAccessToken().then(data => {
        VueThis.circleName = null;
        const token = data;

        let connectOptions = {
          name: this.bootstrapedActiveCircle.title,
          video: { width: 300, height: 150 },
          audio: true
        };

        console.log(connectOptions);

        this.leaveCircleIfJoined();

        document.getElementById("remoteTrack").innerHTML = "";

        Twilio.connect(token, connectOptions).then(function(circle) {
          console.log("Successfully joined a Circle: ", circle);
          VueThis.dispatchLog("Successfully joined a Circle: " + circle.name);

          VueThis.$store
            .dispatch("conferencing/setActiveCircle", circle)
            .then(response => {
              VueThis.loading = false;

              let remoteMediaContainer = document.getElementById("remoteTrack");

              VueThis.activeCircle.participants.forEach(function(participant) {
                console.log("attaching participants");

                VueThis.participantConnected(participant, remoteMediaContainer);
              });

              VueThis.activeCircle.on("participantConnected", function(
                participant
              ) {
                console.log("participant connected");
                VueThis.participantConnected(participant, remoteMediaContainer);
              });

              VueThis.activeCircle.on("participantDisconnected", function(
                participant
              ) {
                console.log("Participant disconnected");
                VueThis.detachParticipantTracks(participant);
              });

              VueThis.activeCircle.on("disconnected", function() {
                VueThis.detachParticipantTracks(
                  VueThis.activeCircle.localParticipant
                );

                VueThis.activeCircle.participants.forEach(
                  VueThis.detachParticipantTracks
                );
              });

              if (!VueThis.localTrack) {
                createLocalVideoTrack().then(track => {
                  let localMediaContainer = document.getElementById(
                    "localTrack"
                  );

                  localMediaContainer.appendChild(track.attach());
                  VueThis.localTrack = true;

                  VueThis.localVideoContainer = localMediaContainer;
                });
              }
            });
        });
      });
    }
  }
};
</script>

<style scoped>
.remote_video_container {
  left: 0;
  margin: 0;
}

#localTrack video {
  border: 3px solid rgb(124, 129, 124);
  margin: 0px;
  max-width: 50% !important;
  background-repeat: no-repeat;
}
</style>
