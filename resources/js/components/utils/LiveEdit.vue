<template>
  <div class="w-full text-black">
    <div v-if="dataEditable" class="flex justify-between items-center">
      <input
        class="w-3/5 bg-white text-black px-4 py-3 h-12"
        type="text"
        @input="valueChanged($event)"
        @keyup.enter="save($event)"
        :placeholder="placeholder"
        v-if="!advselect"
        v-model="fieldValue"
      />
      <div class="w-3/5" v-else>
        <multiselect
          class="rounded bg-white text-black"
          style="box-sizing:content-box;display:block;position:relative;width:100%;"
          v-model="value"
          track-by="name"
          label="name"
          placeholder="Select one"
          :options="options"
          :searchable="true"
          :allow-empty="true"
        >
          <template slot="singleLabel" slot-scope="{ option }">
            <p class>{{ option.name }}</p>
          </template>
        </multiselect>
      </div>

      <div class="block flex justify-center items-center h-4">
        <div
          class="icon-teal cursor-pointer"
          :class="(language === 'ar') ? 'ml-4' : 'mr-4'"
          @click.stop="save($event)"
        >
          <font-awesome-icon
            class="icon text-teal-light cursor-pointer hover:text-teal"
            :icon="['fa', 'check']"
            size="lg"
          />
        </div>

        <div class="cursor-pointer" @click.stop="deactivate($event)">
          <font-awesome-icon
            class="icon text-red cursor-pointer hover:text-black"
            :icon="['fa', 'times']"
            size="lg"
            @click="refuseRequest(profile.username, profile.id)"
          />
        </div>
      </div>
    </div>

    <div v-else class="flex justify-between items-center">
      <p class="w-3/5 px-4 py-3 h-12">{{circleName || placeholder}}</p>

      <div class="block cursor-pointer w-5" @click="activateField($event)">
        <!-- <img class src="/svgs/pen.svg" alt /> -->

        <font-awesome-icon
          class="icon text-grey-darker hover:text-teal cursor-pointer"
          :icon="['fa', 'pen']"
          size="lg"
        />
      </div>
    </div>
  </div>
</template>
<script>
import vSelect from "vue-select";
import Multiselect from "vue-multiselect";
import { mapState, mapMutations, mapActions } from "vuex";

export default {
  computed: {
    ...mapState({
      language: state => state.i18n.locale
    })
  },
  props: {
    circleName: {
      type: [String, Object],
      required: true
    },
    editable: {
      type: Boolean,
      default: false
    },
    multiline: {
      type: Boolean,
      default: false
    },
    advselect: {
      type: Boolean,
      default: false
    },
    simpleselect: {
      type: Boolean,
      default: false
    },
    profileUpdate: {
      type: Boolean
    },
    placeholder: {
      type: String
    },
    options: {
      type: Array
    }
  },
  data() {
    return {
      value: null,
      dataEditable: this.editable,
      inputConfig: {
        height: "50px"
      },
      countryCode: "+1",
      data: "",
      fieldValue: ""
    };
  },

  mounted() {
    this.fieldValue = this.circleName;
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
      this.dataEditable = true;

      $(".live-edit").focus();
    },
    save(event) {
      this.circleName = this.fieldValue;

      this.$emit("input:update-value", {
        value: this.fieldValue,
        resource: "email"
      });

      this.dataEditable = false;

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
      console.log(event);
      var that = this;
      this.dataEditable = false;

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
    vSelect,
    Multiselect
  }
};
</script>

<style lang="scss" scoped>
</style>
