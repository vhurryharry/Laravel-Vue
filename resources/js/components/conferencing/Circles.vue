<template>
  <div class="col-md-3 circles">
    <div
      class="circle"
      v-for="circle in circles"
      v-bind:key="circle.id"
      @click="showCircle(circle)"
    >
      {{ circle.title }}
    </div>
    <AddCircle /> <!-- Imported AddCircle component -->
  </div>
</template>

<script>
import { EventBus } from '../../event'
import AddCircle from './AddCircle'
import { mapState, mapMutations, mapActions } from 'vuex';

export default {
  name: "Circles",
  computed: {
    ...mapState({
      newCircle: state => state.conferencing.newCircle,
      circles: state => state.conferencing.circles,
    }),
  },
  data () {
    return {
      //   circles: [
      //     { id: 1, name: 'PHP circle' },
      //     { id: 2, name: 'Python circle' },
      //     { id: 3, name: 'Daily standup' }
      //   ],
      circleCount: 3, // used to keep track of the number of circles present
      loading: false, // indicate when tracks in a circle is being loaded
    }
  },
  components: {
    AddCircle // imported AddCircle component
  },
  created () {
    // EventBus.$on('new_room', (data) => {
    //   this.roomCount++;
    //   this.rooms.push({ id: this.roomCount, name: data });
    // });
  },
  methods: {
    showCircle (circle) {
      this.$store.dispatch('conferencing/bootstrapActiveCircle', null).then(response => {
        this.$store.dispatch('conferencing/bootstrapActiveCircle', {
          title: circle.title,
          circle: circle,
        });
      });

      //   EventBus.$emit('show_circle', circle);
    }
  }

}
</script>

<style scoped>
/* scoped attribute is used here so the styling applies to this component alone */
.circles > .circle {
  border: 1px solid rgb(124, 129, 124);
  padding: 13px;
  margin: 3px 0px;
  /* color: ghostwhite; */
}

.circles {
  border: 1px solid rgb(64, 68, 64);
  cursor: pointer;
}
</style>
