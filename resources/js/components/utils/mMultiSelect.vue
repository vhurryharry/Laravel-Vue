<template>
  <div class="w-full text-black">
    <div v-if="innerEditing" class="flex justify-between items-center">
      <div class="w-3/5">
        <multiselect
          class="rounded bg-white text-black"
          style="box-sizing:content-box;display:block;position:relative;width:100%;"
          v-model="value"
          :track-by="trackBy"
          :label="'name'"
          placeholder="Select languages"
          :options="options"
          :searchable="true"
          :allow-empty="true"
          :close-on-select="closeOnSelect"
          :multiple="multiple"
          :limit="3"
          :taggable="true"
        >
          <template slot="singleLabel" slot-scope="{ option }">
            <p class>{{ option.name || option.value }}</p>
          </template>
        </multiselect>
      </div>

      <div class="block flex justify-center items-center h-4">
        <div class="icon-teal cursor-pointer w-6" :class="(language === 'ar') ? 'ml-4' : 'mr-4'">
          <img src="/svgs/check.svg" alt @click.stop="save" />
        </div>

        <div class="icon-red colorize-teal cursor-pointer w-4">
          <img src="/svgs/close.svg" alt @click.stop="deactivate($event)" />
        </div>
      </div>
    </div>

    <div v-else class="flex justify-between items-center">
      <div class="flex" :class="(language === 'ar') ? 'pr-4' : 'pl-4'">
        <div class="flex" v-if="value">
          <p v-for="(item, index) in value" :value="item.id" :key="index" class="py-3 h-12">
            <span v-if="index < value.length - 1">{{item.name + ',&nbsp' || "Select one" }}</span>
            <span v-else>{{ item.name || "Select one" }}</span>
          </p>
        </div>
        <p v-else>{{ $t('components.settings.select') }}</p>
      </div>

      <div class="block cursor-pointer icon-grey colorize-teal w-5" @click="activateField($event)">
        <img class src="/svgs/pen.svg" alt />
      </div>
    </div>
  </div>
</template>

<script>
import Multiselect from "vue-multiselect";
import { mapState, mapMutations, mapActions } from "vuex";

export default {
  props: {
    valueToEdit: {
      type: [String, Object, Array],
      required: false
    },
    editing: {
      type: Boolean,
      default: false
    },
    resource: {
      type: String
    },
    options: {
      type: [Array, Object]
    },
    multiple: {
      type: Boolean,
      default: false
    },
    closeOnSelect: {
      type: Boolean,
      default: true
    },
    trackBy: {
      type: String
    },
    limit: {},
    label: {
      type: String
    }
  },
  computed: {
    ...mapState({
      language: state => state.i18n.locale
    })
  },
  data() {
    return {
      innerEditing: this.editing,
      value: [],
      inputConfig: {
        height: "50px"
      }
    };
  },
  created() {},
  mounted() {
    if (this.valueToEdit.length != 0) {
      this.valueToEdit.forEach(element => {
        this.value.push(element);
      });
    } else {
      this.value = null;
    }
  },
  watch: {
    valueToEdit(value) {
      if (value) {
        this.value = value;
      }
    }
  },
  methods: {
    activateField: function(event) {
      $(event.target)
        .parent()
        .parent()
        .parent()
        .parent()
        .addClass("bg-grey");

      this.inputConfig.height = $(".live-edit-container").outerHeight();
      this.innerEditing = true;

      $(".live-edit").focus();
    },
    save(event) {
      this.$emit("input:update-value", {
        value: this.value,
        resource: this.resource
      });

      this.innerEditing = false;

      if (this.value.length === 0) {
        this.value = null;
      }

      $(event.target)
        .parent()
        .parent()
        .parent()
        .parent()
        .parent()
        .removeClass("bg-grey");
    },
    valueChanged($event) {},
    deactivate: function(event) {
      var that = this;
      this.innerEditing = false;

      $(event.target)
        .parent()
        .parent()
        .parent()
        .parent()
        .parent()
        .removeClass("bg-grey");
    },
    addTag(newTag) {},
    appendObjTo(thatArray, newObj) {
      const frozenObj = Object.freeze(newObj);
      return Object.freeze(thatArray.concat(frozenObj));
    }
  },
  components: {
    Multiselect
  }
};
</script>
<style lang="sass" scoped>
</style>
