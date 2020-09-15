<template>
  <div
    class="bg-white flex flex-col"
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

    <div
      class="flex bg-grey justify-center py-6"
      style="height:25vh;"
    >
      <video
        id="local-track"
        autoplay
        style="height:180px; width: 240px;"
      ></video>

      <!-- <div
        class="flex"
        id="video"
      >
      </div> -->
    </div>
  </div>
</template>
<script src="https://unpkg.com/peerjs@1.0.0/dist/peerjs.min.js"></script>

<script>
import Twilio, { connect, createLocalTracks, createLocalVideoTrack } from 'twilio-video';
import { mapState, mapMutations, mapActions } from 'vuex';
// import peer from 'peerjs';
import SimplePeer from 'simple-peer';
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
  created () {
    this.initializePeers();

    // When a user is about to transition away from this page,
    // disconnect from the circle, if joined.
    window.addEventListener('beforeunload', this.leaveCircleIfJoined);
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
    bootstrapedActiveCircle (value) {
      if (value) {
        this.initializePeers();
      }
    }
  },
  methods: {

    initializePeers (canReset) {
      this.loading = true;
      const VueThis = this;
      let that = this;
      var client = io.connect("127.0.0.1:9002/hallway");
      //   var peer = new Peer();

      var peers = {};
      var useTrickle = true;

      let localStream = {};
      let localPeer = {}
      //   peer.on('open', function (id) {
      //     console.log('My peer ID is: ' + id);
      //   })
      //   client.on('connect', function () {
      //     console.log("FDFDFD");
      //     client.emit("test", "foo");
      //   });

      //   if ((!window.clientPeer && !window.hostPeer) || canReset) {
      //   this.$store.commit('UPDATE_IS_CONNECTING', true)
      //   const peer = new Peer('someid', { host: 'localhost', port: 9002, path: '/myapp' });


      client.on('connect', function () {
        console.log('Connected to signalling server, Peer ID: %s', client.id);

        navigator.mediaDevices.getUserMedia({ video: true, audio: true })
          .then(function (stream) {
            let peer = new SimplePeer({ initiator: location.hash === '#1', trickle: false, stream: stream, objectMode: true })
            localStream = stream;

            var video = document.getElementById('local-track')
            video.srcObject = stream

            peer.on('stream', function (stream) {
              console.log("streaming");
              let remoteVideo = document.getElementById('remote-track')
              remoteVideo.srcObject = stream
            })
          })
          .catch(function (err) {
            /* handle the error */
          });
      });

      client.on('stream', function (stream) {
        console.log("Stream")
        console.log(stream);
      });

      client.on('peer', function (data) {
        var peerId = data.peerId;
        var peer = new SimplePeer({ initiator: location.hash === '#1', trickle: useTrickle });
        // console.log(data.initiator);
        console.log('Peer available for connection discovered from signalling server, Peer ID: %s', peerId);

        client.on('signal', function (data) {
          if (data.peerId == peerId) {
            console.log('Received signalling data', data, 'from Peer ID:', peerId);
            peer.signal(data.signal);
          }
        });

        peer.on('signal', function (data) {
          console.log('Advertising signalling data', data, 'to Peer ID:', peerId);
          client.emit('signal', {
            signal: data,
            peerId: peerId
          });
        });

        peer.on('error', function (e) {
          console.log('Error sending connection to peer %s:', peerId, e);
        });

        peer.on('connect', function () {
          console.log('Peer connection established');
          peer.send("hey peer");
        });

        peer.on('data', function (data) {
          console.log('Recieved data from peer:', data);
        });

        peers[peerId] = peer;
      });

      //   client.on('joined', function (room) {
      //     console.log("someone joined the circle");
      //     console.log(room);

      //     var peer = new SimplePeer();
      //     console.log(peer);

      //     localPeer.on('signal', data => {
      //       console.log("DDDDDDDDDDDDDDDDDDDDDDDDDDD");

      //     })
      //     // hub.setPeer(peerid, peer);

      //     // peer.on('signal', function (data) {
      //     //   hub.send(peerid, 'signal', data);
      //     // });

      //     // peerCreated(peerid, peer);
      //     // hub.send(peerid, 'ack', '1'); // sending acknowledgement

      //     peer.on('stream', stream => {
      //       console.log("member streaming");

      //       // got remote video stream, now let's show it in a video tag
      //       //   var video = document.querySelector('video')
      //       var video = document.getElementById('remote-track')

      //       if ('srcObject' in video) {
      //         video.srcObject = stream
      //       } else {
      //         video.src = window.URL.createObjectURL(stream) // for older browsers
      //       }

      //       video.play()
      //     })
      //   });

      //   client.on('peer', function (data) {
      //     var peerId = data.peerId;
      //     var peer = new SimplePeer({ initiator: data.initiator, trickle: useTrickle });

      //     console.log('Peer available for connection discovered from signalling server, Peer ID: %s', peerId);

      //     client.on('signal', function (data) {
      //       if (data.peerId == peerId) {
      //         console.log('Received signalling data', data, 'from Peer ID:', peerId);
      //         peer.signal(data.signal);
      //       }
      //     });

      //     peer.on('signal', function (data) {
      //       console.log('Advertising signalling data', data, 'to Peer ID:', peerId);
      //       client.emit('signal', {
      //         signal: data,
      //         peerId: peerId
      //       });
      //     });

      //     peer.on('error', function (e) {
      //       console.log('Error sending connection to peer %s:', peerId, e);
      //     });

      //     peer.on('connect', function () {
      //       console.log('Peer connection established');
      //       peer.send("hey peer");
      //     });

      //     peer.on('data', function (data) {
      //       console.log('Recieved data from peer:', data);
      //     });

      //     peers[peerId] = peer;
      //   });

      //   client.on('signal', data => {
      //     console.log('SIGNAL', JSON.stringify(data))
      //     document.querySelector('#outgoing').textContent = JSON.stringify(data)
      //   })


      // window.clientPeer = new SimplePeer({ initiator: true, trickle: false, objectMode: true })
      // window.hostPeer = new SimplePeer({ trickle: false, objectMode: true })

      // // // Client peer listeners
      // window.clientPeer.on('signal', data => this.handleSignal(window.clientPeer, data))
      // window.clientPeer.on('connect', () => this.handleConnect(window.clientPeer))
      // window.clientPeer.on('data', data => this.handleData(window.clientPeer, data))
      // window.clientPeer.on('close', () => this.handleClose(window.clientPeer))
      // window.clientPeer.on('error', err => this.handleError(window.clientPeer, err))

      // // // Host peer listeners
      // window.hostPeer.on('signal', data => this.handleSignal(window.hostPeer, data))
      // window.hostPeer.on('connect', () => this.handleConnect(window.hostPeer))
      // window.hostPeer.on('data', data => this.handleData(window.hostPeer, data))
      // window.hostPeer.on('close', () => this.handleClose(window.hostPeer))
      // window.hostPeer.on('error', err => this.handleError(window.hostPeer, err))
      //   }

    },
    gotMedia (stream) {
      var peer1 = new SimplePeer({ initiator: true, stream: stream })
      var peer2 = new SimplePeer()

      peer1.on('signal', data => {
        peer2.signal(data)
      })

      peer2.on('signal', data => {
        peer1.signal(data)
      })

      peer2.on('stream', stream => {
        // got remote video stream, now let's show it in a video tag
        var video = document.querySelector('video')

        if ('srcObject' in video) {
          video.srcObject = stream
        } else {
          video.src = window.URL.createObjectURL(stream) // for older browsers
        }

        video.play()
      })
    },
    signal () {
      console.log('signal', this.signalPayload)
      let signalPayload = JSON.parse(this.signalPayload)

      if (signalPayload.type === 'offer') {
        window.hostPeer.signal(signalPayload)
      }

      if (signalPayload.type === 'answer') {
        window.clientPeer.signal(signalPayload)
      }
    },
    copySignal () {
      if (this.isElectron) {
        clipboard.writeText(this.signalingAnswer)
        // eslint-disable-next-line no-new
        // new Notification('Netsix', {
        //   body: 'Signal copied to clipboard!'
        // })
      }
    },
    handleSignal (peer, data) {
      console.log('PeerConnection: signal: peer, data', peer, data)
      this.$store.commit('conferencing/UPDATE_IS_CONNECTING', false)

      if (peer.initiator) {
        // when clientPeer has signaling data, give it to hostPeer somehow
        this.$store.commit('conferencing/UPDATE_SIGNALING_OFFER', JSON.stringify(data)) // window.hostPeer.signal(data)
      } else {
        // when hostPeer has signaling data, give it to clientPeer somehow
        this.$store.commit('conferencing/UPDATE_SIGNALING_ANSWER', JSON.stringify(data)) // window.clientPeer.signal(data)
      }
    },
    handleConnect (peer) {
      console.log('PeerConnection: connect: peer.initiator', peer.initiator)
      //   bus.$emit('connection:connect')
      this.$store.commit('conferencing/UPDATE_IS_CONNECTING', false)
      this.$store.commit('conferencing/UPDATE_IS_CONNECTED', true)
      this.$store.commit('conferencing/PUSH_NOTIFICATION', { type: 'success', message: 'You are connected to a peer!' })
      // wait for 'connect' event before using the data channel
      peer.send(JSON.stringify({
        type: 'COLLECTIONS',
        payload: this.localCollections
      }))
    },
    handleData (peer, data) {
      if (typeof data === 'string') {
        try {
          let message = JSON.parse(data)
          console.log('PeerConnection: data: peer.initiator, message', peer.initiator, message)
          switch (message.type) {
            case 'COLLECTIONS':
              // Handle the received collections
              this.$store.commit('conferencing/UPDATE_REMOTE_COLLECTIONS', Object.assign({}, message.payload))
              break
            case 'CHAT_MESSAGE':
              // Handle the received collections
              this.$store.commit('conferencing/PUSH_TO_CHATROOM_MESSAGES', {
                payload: message.payload,
                from: 'remote'
              })
              break
            case 'GET_FILE_REQUEST':
              // Handle the received file request
              this.$store.dispatch('conferencing/handleGetFileRequest', message.payload)
              break
            case 'SEND_FILE_INFORMATION':
              // Handle the received file information
              this.$store.commit('conferencing/UPDATE_SELECTED_FILE', Object.assign({}, message.payload))
              this.$store.commit('conferencing/UPDATE_REQUESTED_FILE', Object.assign({}, {}))
              break
            case 'ACK_FILE_INFORMATION':
              // Handle the received file acknowledgment
              this.$store.dispatch('conferencing/handleAckFileInformation', message.payload)
              break
            case 'TRANSCODING_PROGRESS':
              // Handle the received transcoding progress
              this.$store.commit('conferencing/UPDATE_TRANSCODING_PROGRESS', message.payload)
              // Emit a notification when the transcoding begins/ends
              switch (message.payload) {
                case 0:
                  this.$store.commit('conferencing/PUSH_NOTIFICATION', { type: 'info', message: 'Begin to transcode ' + this.$store.state.video.requestedFile.filename + '. It may take a while.' })
                  break
                case 100:
                  this.$store.commit('conferencing/PUSH_NOTIFICATION', { type: 'info', message: 'Done transcoding ' + this.$store.state.video.requestedFile.filename + '.' })
                  break
              }
              break
            case 'CANCEL_TRANSCODING_REQUEST':
              // Handle the received cancel transcoding request
              this.$store.dispatch('conferencing/handleCancelTranscodingInformation')
              break
            case 'NEED_TRANSCODING':
              // Handle the need for transcoding
              this.$store.commit('conferencing/PUSH_NOTIFICATION', {
                type: 'warning',
                message: `Transcoding needed! It may take a while.<br>Video: ${!message.payload.isVideoTypeSupported}, audio: ${!message.payload.isAudioTypeSupported}.`
              })
              break
            default:
              console.warn('PeerConnection: data: unknown type received', message.type)
          }
        } catch (e) {
          // It's a pure string, not a stringified JSON object
          console.error(e, data)
        }
      } else {
        // It's a video chunk
        // bus.$emit('video:chunk', data)
      }
    },
    handleClose (peer) {
      console.log('PeerConnection: close: peer.initiator', peer.initiator)
      //   bus.$emit('connection:close')
      this.$store.commit('conferencing/UPDATE_IS_CONNECTING', false)
      this.$store.commit('conferencing/UPDATE_IS_CONNECTED', false)
      this.$store.commit('conferencing/PUSH_NOTIFICATION', { type: 'danger', message: 'Connection closed!' })
      this.initializePeers(true)
    },
    handleError (peer, err) {
      console.error('PeerConnection: error: peer.initiator, err', peer.initiator, err)
      //   bus.$emit('connection:error')
      this.$store.commit('conferencing/UPDATE_IS_CONNECTING', false)
      this.$store.commit('conferencing/UPDATE_IS_CONNECTED', false)
      this.$store.commit('conferencing/PUSH_NOTIFICATION', { type: 'danger', message: err.toString() })
      this.initializePeers(true)
    },
    // Generate access token
    async getAccessToken () {
      return await axios.post('/hallway/' + this.profile.username + '/access', {
        circle_name: this.bootstrapedActiveCircle.title
      })
        .then(response => {
          return response.data.token;
        });
    },

    dispatchLog (message) {
      this.$store.dispatch('conferencing/setLogs', message);
      //   EventBus.$emit('new_log', message);
    },

    getTracks (participant) {
      return Array.from(participant.tracks.values()).filter(function (publication) {
        return publication.track;
      }).map(function (publication) {
        return publication.track;
      });
    },

    // Attach the Tracks to the DOM.
    attachTracks (tracks, container) {
      tracks.forEach(function (track) {
        container.appendChild(track);
      });
    },

    // Attach the Participant's Tracks to the DOM.
    attachParticipantTracks (participant, container, isLocal) {
      participant.tracks.forEach(publication => {
        publication.on('subscribed', track => {
          document.getElementById('remoteTrack').appendChild(track.attach());
        });
      });
    },

    // Detach the Tracks from the DOM.
    detachTracks (publication) {
      console.log("Tracks: ");
      console.log(publication);
      console.log('unsubscribed');

      document.getElementById('remoteTrack').removeChild(track);
    },

    // Detach the Participant's Tracks from the DOM.
    detachParticipantTracks (participant) {
      participant.tracks.forEach(publication => {
        document.getElementById('remoteTrack').removeChild(publication);
      });
    },

    // Leave Circle.
    leaveCircleIfJoined () {
      if (this.activeCircle) {
        this.activeCircle.disconnect();
      }
    },

  },
  components: {
    SimplePeer
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
