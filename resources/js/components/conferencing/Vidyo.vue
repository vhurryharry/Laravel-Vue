<template>
  <div
    class="bg-white flex flex-col relative"
    style="height:100vh;"
  >
    <div class="circleTitle">
      <span v-if="loading"> Loading... {{ bootstrapedActiveCircle.title }}</span>
      <span v-else-if="!loading && bootstrapedActiveCircle.title"> Connected to {{bootstrapedActiveCircle.title}}</span>
      <span v-else>Select a circle to get started</span>
    </div>

    <div
      class="w-full h-full remote_video_container border border-black"
      style="height:72vh;"
    >

      <video
        id="remote-track"
        autoplay
        style="height:180px; width: 240px;"
      ></video>
      <!--
      <video
        id="remote-track"
        src=""
      ></video> -->
    </div>

    <!-- <div class="spacing"></div> -->
    <div id="local-track" class="" style="width:250px;height:250px;"></div>
    <!-- <div
      class="flex bg-grey justify-center py-6"
      style="height:25vh;"
    >
      <video
        id="local-track"
        autoplay
        style="height:180px; width: 240px;"
      ></video>
    </div> -->
  </div>
</template>

<script>
// import Twilio, { connect, createLocalTracks, createLocalVideoTrack } from 'twilio-video';
import { mapState, mapMutations, mapActions } from 'vuex';
// import peer from 'peerjs';
// import SimplePeer from 'simple-peer';
// import Peer from 'peerjs';

export default {
  name: "Video",
  computed: {
    ...mapState({
      profile: state => state.profiles.signedInProfile,
      logs: state => state.conferencing.logs,
      activeCircle: state => state.conferencing.activeCircle,
      bootstrapedActiveCircle: state => state.conferencing.bootstrapedActiveCircle,
      signalingOffer: state => state.conferencing.signalingOffer,
      signalingAnswer: state => state.conferencing.signalingAnswer,
      status: state => state.conferencing.status,
    }),
  },
  mounted () {
    // window.VC = vidyoScript;

    let vidyoScript = document.createElement('script');
    vidyoScript.setAttribute('src', 'https://static.vidyo.io/latest/javascript/VidyoClient/VidyoClient.js?onload=onVidyoClientLoaded');
    document.head.appendChild(vidyoScript);

    // this.onVidyoClientLoaded();
  },
  created () {
    // // When a user is about to transition away from this page,
    // // disconnect from the circle, if joined.
    // window.addEventListener('beforeunload', this.leaveCircleIfJoined);
  },
  data () {
    return {
      loading: false,
      data: {},
      localTrack: false,
      remoteTrack: '',
      previewTracks: '',
      identity: '',
      circleName: null,
      signalPayload: ''
    }
  },
  props: ['username'], // props that will be passed to this component
  watch: {

  },
  methods: {
    onVidyoClientLoaded () {
      //   console.log("FDSFDFSDFSDFSDFSD");
    },
    createVidyo () {
      //   console.log(VC);
      //   VC.CreateVidyoConnector({
      //     viewId: "renderer",                            // Div ID where the composited video will be rendered, see VidyoConnector.html
      //     viewStyle: "VIDYO_CONNECTORVIEWSTYLE_Default", // Visual style of the composited renderer
      //     remoteParticipants: 15,                        // Maximum number of participants
      //     logFileFilter: "warning all@VidyoConnector info@VidyoClient",
      //     logFileName: "",
      //     userData: ""
      //   }).then(function (vidyoConnector) {
      //     vidyoConnector.Connect({
      //       host: "prod.vidyo.io",
      //       token: generatedToken,
      //       displayName: "John Smith",
      //       resourceId: "JohnSmithRoom",
      //       // Define handlers for connection events.
      //       onSuccess: function () {/* Connected */ },
      //       onFailure: function (reason) {/* Failed */ },
      //       onDisconnected: function (reason) {/* Disconnected */ }
      //     }).then(function (status) {
      //       if (status) {
      //         console.log("ConnectCall Success");
      //       } else {
      //         console.error("ConnectCall Failed");
      //       }
      //     }).catch(function () {
      //       console.error("ConnectCall Failed");
      //     });
      //   }).catch(function () {
      //     console.error("CreateVidyoConnector Failed");
      //   });
    },

  },
  components: {
  }

}
</script>

<style scoped>
.remote_video_container {
  left: 0;
  margin: 0;
  border: 1px solid rgb(124, 129, 124);
}
#localTrack video {
  border: 3px solid rgb(124, 129, 124);
  margin: 0px;
  max-width: 50% !important;
  background-repeat: no-repeat;
}
.spacing {
  padding: 20px;
  width: 100%;
}
.circleTitle {
  border: 1px solid rgb(124, 129, 124);
  padding: 4px;
  color: dodgerblue;
}
</style>
