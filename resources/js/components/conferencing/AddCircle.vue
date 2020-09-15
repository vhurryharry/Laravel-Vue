<template>
  <div class="row circleForm text-black">
    <form
      class="form-inline"
      @submit.prevent="createNeCirclem(circle_name)"
    >
      <div class="form-group mb-2">
        <input
          type="text"
          class="form-control"
          v-model="circle_name"
        >
      </div>
      <button
        type="submit"
        class="btn btn-primary mb-2 createCircleBotton"
      >Create Circle</button>
    </form>
  </div>
</template>

<script>
import { EventBus } from '../../event'
import { mapState, mapMutations, mapActions } from 'vuex';

export default {
  name: 'AddCircle', // Component name
  data () {
    return {
      circle_name: '',
    }
  },
  methods: {
    createNewCircle (name) {
      if (!name) {
        alert('please provide a circle name');
        return
      }

      this.circle_name = '';

        this.$store.dispatch('conferencing/setCircles', {
            name: name
        });
    //   EventBus.$emit('new_circle', name);
    }
  }
}
</script>

<style lang="scss" scoped>
.circleForm {
  margin-left: auto;
  margin-right: auto;
  margin-top: 30px;
  width: 100%;
}
.createCircleBotton {
  background-color: #4d555f;
  border-color: #303840;
  padding: 8px;
  font-weight: bolder;
}
</style>

