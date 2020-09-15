<template>
  <div class="fixed pin flex items-center" style>
    <div class="fixed pin bg-black opacity-50 z-10"></div>

    <div class="relative mx-6 ml-auto w-1/4 z-20 m-8" style="max-height:600px;">
      <div class="shadow-lg bg-grey text-grey-darkest" style>
        <div
          class="flex items-center bg-teal justify-between py-2"
          :class="(language === 'ar') ? 'pr-10' : 'pl-10'"
        >
          <p class="text-white">{{ $t('components.events.event_information') }}</p>

          <button class :class="(language === 'ar') ? 'ml-4' : 'mr-4'" @click="close()">
            <img class="invert-icon w-4" src="/svgs/close.svg" alt />
          </button>
        </div>

        <div class="w-full py-4 pb-6 px-12">
          <div class="flex items-center bg-white text-black py-3 flex-no-shrink">
            <img
              class="rounded-full mx-3 h-16 w-16 flex items-center justify-center border border-grey-dark"
              :src="'/storage/images/' + event.image_path"
              alt
            />

            <div class="leading-normal flex flex-col text-sm">
              <p class="text-teal text-lg">{{ event.name }}</p>
              <p class="text-grey-darkest">
                {{ $t('shared.duration') }}:
                <span class="text-teal">{{ event.event_type }}</span>
              </p>
              <p class="text-grey-darkest">
                {{ $t('components.settings.privacy') }}:
                <span
                  class="text-teal"
                >{{ event.privacy.privacy_value.name[language] }}</span>
              </p>
              <p class="text-grey-darkest">
                {{ $t('shared.participants') }}:
                <span
                  class="text-teal"
                >{{ event.participants.length }}</span>
              </p>
              <p class="text-grey-darkest">
                {{ $t('components.events.event_description') }}:
                <span
                  class="text-teal"
                >{{ event.description }}</span>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState, mapMutations, mapActions } from "vuex";
import closeMixin from "./../../mixins/close.js";

export default {
  mixins: [closeMixin],
  computed: {
    ...mapState({
      event: state => state.conferencing.bootstrapedActiveCircle,
      language: state => state.i18n.locale
    })
  },
  created() {},
  mounted() {},
  data() {
    return {};
  },
  methods: {
    close() {
      this.$store.dispatch("ui/setActiveModal", null);
      this.$emit("modal:close");
    }
  },
  components: {}
};
</script>

<style lang="scss" scoped>
</style>
