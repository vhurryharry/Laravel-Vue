<template>
  <div class="fixed pin flex items-center">
    <div class="fixed pin bg-black opacity-50 z-10"></div>

    <div class="relative mx-6 md:mx-auto w-full md:w-1/2 lg:w-1/3 z-20 m-8">
      <div class="shadow-lg bg-teal rounded-lg p-8">
        <div class="flex justify-end">
          <button class @click="close()">
            <img class="invert-icon w-4" src="/svgs/close.svg" alt />
          </button>
        </div>

        <div v-if="event.name" class="text-center">
          <p class="text-lg rubik-bold mb-4">
            {{
            $t('components.events.you_have_created_the_event')
            }}
          </p>

          <img
            class="mx-auto rounded-full h-24 w-24 border border-grey-dark my-2"
            :src="'/storage/images/' + event.image_path"
            alt
          />
          <!-- </div> -->
          <p class="text-lg rubik-bold mt-3">{{ event.name }}</p>

          <button
            class="small-border-radius border border-teal-light hover:bg-white hover:text-teal-light rubik-light mt-5 text-sm text-white bg-teal-light px-4 py-1"
            @click="close()"
          >{{ $t('shared.done') }}</button>
        </div>

        <div v-if="community.name" class="text-center">
          <p class="text-lg rubik-bold mb-4">
            {{
            $t('components.communities.you_have_created_the_community')
            }}
          </p>

          <img
            class="mx-auto rounded-full h-24 w-24 border border-grey-dark my-2"
            :src="'/storage/images/' + community.image_path"
            alt
          />
          <!-- </div> -->
          <p class="text-lg rubik-bold mt-3">{{ community.name }}</p>

          <button
            class="small-border-radius border border-teal-light hover:bg-white hover:text-teal-light rubik-light mt-5 text-sm text-white bg-teal-light px-4 py-1"
            @click="close()"
          >{{ $t('shared.done') }}</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState, mapMutations, mapActions } from "vuex";

export default {
  created() {},
  props: ["standalone"],
  computed: {
    ...mapState({
      language: state => state.i18n.locale,
      profile: state => state.profiles.signedInProfile,
      event: state => state.events.activeEvent,
      community: state => state.communities.activeCommunity
    })
  },
  data() {
    return {};
  },

  methods: {
    close() {
      this.$store.dispatch("ui/setActiveModal", null);
    },
    openPhoneVerification() {},
    openPhoneRegistration() {}
  },
  components: {}
};
</script>

<style lang="scss" scoped>
</style>
