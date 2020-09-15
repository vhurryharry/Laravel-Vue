<template>
  <div class="col-md-3 box">
    <div
      class="log"
      v-for="log in logs"
      v-bind:key="log.id"
    >
      {{ log.message }}
    </div>
  </div>
</template>

<script>

// import { EventBus } from '../Event'
import { mapState, mapMutations, mapActions } from 'vuex';

export default {
  name: "Logs",
  computed: {
    ...mapState({
      logs: state => state.conferencing.logs,
    }),
  },
  data () {
    return {
      logCount: 0
    }
  },
  created () {
    // EventBus.$on('new_log', (message) => {
    //     this.logs.push( {id: this.logCount, message: message} );

    //     this.logCount += 1;
    // })
  },
}
</script>

<style scoped>
.log {
  border: 1px solid rgb(124, 129, 124);
  padding: 13px;
  margin: 3px 0px;
  color: ghostwhite;
}
</style>

