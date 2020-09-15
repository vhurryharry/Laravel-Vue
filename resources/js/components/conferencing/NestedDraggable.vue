<template>
  <!-- <draggable
    v-bind="dragOptions"
    tag="div"
    class="item-container ssssssdwww"
    :list="list"
    :value="value"
    @input="emitter"
  >
    <div
      class="item-group ssssssdwww"
      :key="el.id"
      v-for="el in realValue"
    >
      <div class="item">{{ el.name }}</div>
      <nested-draggable
        class="item-sub ssssssdwww"
        :list="el.circles"
      />
    </div>
  </draggable> -->

  <draggable
    class="item-container"
    element="div"
    v-model="list"
    v-bind="dragOptions"
    :move="onMove"
  >
   <li class="list-group-item item-group my-2" v-for="element in realValue" :key="element.id">
        <i
          @click=" element.fixed=! element.fixed"
          aria-hidden="true"
        ></i>

        <!-- <span class="badge">{{element.order}}</span> -->

        {{ element.name }}
      </li>

    <!-- <transition-group name="no" class="list-group p-2" tag="ul"> -->
    <!-- </transition-group> -->
  </draggable>
</template>

<script>
import draggable from "vuedraggable";
import { mapState, mapMutations, mapActions } from "vuex";

export default {
  name: "nested-draggable",
  props: {
    value: {
      required: false,
      type: Array,
      default: null
    },
    list: {
      required: false,
      type: Array,
      default: null
    }
  },
  computed: {
    ...mapState({
      signedInProfile: state => state.profiles.signedInProfile,
      language: state => state.i18n.locale
    }),
    dragOptions() {
      return {
        animation: 100,
        group: "description",
        disabled: false,
        fallbackOnBody: true,
        ghostClass: "ghost"
      };
    },
    // this.value when input = v-model
    // this.list  when input != v-model
    realValue() {
      return this.value ? this.value : this.list;
    }
  },
  created() {
    //   console.log(this.signedInProfile);
  },
  mounted() {
    // if (this.value.length !== 0) {
    //   if (this.value[0].username !== this.signedInProfile.username) {
    //     this.dragOptions.disabled = true;
    //   }
    // }
  },
  methods: {
    emitter(value) {
      this.$emit("draggable-updated");
    },
    onMove({ relatedContext, draggedContext }) {
      const relatedElement = relatedContext.element;
      const draggedElement = draggedContext.element;

      this.$emit("draggable-updated");

      return (
        (!relatedElement || !relatedElement.fixed) && !draggedElement.fixed
      );
    }
  },
  components: {
    draggable
  }
};
</script>

<style scoped>
.flip-list-move {
  transition: transform 0.5s;
}
.no-move {
  transition: transform 0s;
}
.ghost {
  opacity: 0.5;
  background: #c8ebfb;
}
.list-group {
  min-height: 20px;
  list-style-type: none;
}
.list-group-item {
  cursor: move;
}
.list-group-item i {
  cursor: pointer;
}

.item-container {
  min-width: 5rem;
  max-width: 20rem;
  margin: 0;
}

.item {
  /* padding: 1rem; */
  border: solid black 1px;
  background-color: #fefefe;
}

.item-sub {
  /* margin: 0 0 0 1rem; */
}
</style>
