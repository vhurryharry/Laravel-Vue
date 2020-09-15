<template>
  <div
    class="container-fluid chat_container fill-current bg-teal text-black"
    id="app"
  >
    <div
      class="row flex flex-col w-full  h-full"
      v-if="signedInProfile"
    >

      <div class="flex h-full">
        <Circles class="w-1/4" />
        <twilio-video
          class="flex-1"
          :username="signedInProfile"
          v-if="bootstrapedActiveCircle"
        />
      </div>

      <!-- <Logs /> -->
    </div>
    <div
      class="row"
      v-else
    >
      <div class="username">
        <form
          class="form-inline"
          @submit.prevent="submitUsername(signedInProfile)"
        >
          <div class="form-group mb-2">
            <input
              type="text"
              class="form-control"
              v-model="username"
            >
          </div>
          <button
            type="submit"
            class="btn btn-primary mb-2 Botton"
          >Submit</button>
        </form>
      </div>
    </div>
  </div>
</template>

<script>

import Circles from './../components/conferencing/Circles'
import TwilioVideo from './../components/conferencing/TwilioVideo'
import Logs from './../components/conferencing/Logs'
import { mapState, mapMutations, mapActions } from 'vuex';

export default {
  name: 'Hallway',

  computed: {
    ...mapState({
      signedInProfile: state => state.profiles.signedInProfile,
      activeCircle: state => state.conferencing.activeCircle,
      bootstrapedActiveCircle: state => state.conferencing.bootstrapedActiveCircle,

    }),
  },
  async created () {
    await this.$store.dispatch('conferencing/getCircles');
  },
  data () {
    return {
      username: "",
      authenticated: false
    }
  },
  components: {
    Circles,
    Logs,
    TwilioVideo,
  },
  methods: {
    submitUsername (username) {
      if (!username) {
        return alert('please provide a username');
      }

      this.authenticated = true;
    }
  }
}
</script>

<style scoped lang="scss">
.chat_container {
  // background:red;
  height: 100vh;
}
</style>
