<template>
  <div class="w-full text-black">
    <div v-if="innerEditing" class="flex justify-between items-center">
      <div class="w-3/5">
        <multiselect
          class="rounded bg-white text-black"
          style="box-sizing:content-box;display:block;position:relative;width:100%;"
          v-model="value"
          :track-by="trackBy"
          :label="label"
          placeholder="Select one"
          :options="options"
          :searchable="true"
          :allow-empty="true"
          :close-on-select="closeOnSelect"
          :multiple="multiple"
        >
          <template slot="singleLabel" slot-scope="{ option }">
            <p class>{{ option.name || option.value }}</p>
          </template>
        </multiselect>
      </div>

      <div class="block flex justify-center items-center h-4">
        <div class="icon-teal cursor-pointer w-6" :class="(language === 'ar') ? 'ml-4' : 'mr-4'">
          <img src="/svgs/check.svg" alt @click.stop="save($event)" />
        </div>

        <div class="icon-red colorize-teal cursor-pointer w-4">
          <img src="/svgs/close.svg" alt @click.stop="deactivate($event)" />
        </div>
      </div>
    </div>

    <div v-else class="flex justify-between items-center">
      <p class="w-3/5 px-4 py-3 h-12" v-if="value.name">{{ value.name }}</p>

      <p class="w-3/5 px-4 py-3 h-12" v-else>{{ $t('components.settings.select_one') }}</p>

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
      value: "",
      inputConfig: {
        height: "50px"
      }
    };
  },
  watch: {
    valueToEdit(value) {
      console.log(value);
      if (value) {
        this.value = value;
      }
    }
  },
  created() {},
  mounted() {},
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
        value: this.value !== null ? this.value : null,
        resource: this.resource
      });

      if (this.value === null) {
        this.value = "Choose";
      } else {
        this.value = {
          key: this.value.key,
          name: this.value.name
        };
      }

      this.innerEditing = false;

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
    }
  },
  components: {
    Multiselect
  }
};
</script>
<style lang="sass" scoped>

</style>
