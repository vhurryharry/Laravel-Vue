<template>
  <div class="fixed pin flex items-center">
    <div class="fixed pin bg-black opacity-50 z-10"></div>

    <div class="relative mx-6 md:mx-auto w-full md:w-1/2 lg:w-2/5 z-20 m-8">
      <div class="flex flex-col shadow-lg bg-grey-dark text-black rounded-lg p-8 pt-4">
        <div class="flex justify-end m-0 p-0 pb-2">
          <button class @click="close()">
            <img class="invert-icon w-4" src="/svgs/close.svg" alt />
          </button>
        </div>

        <div class="flex w-full items-center">
          <p>Get Shareable Link:</p>
          <a href="#" class="bg-white rounded p-2 mx-3">https://meetandminglealpha.com/{{ shareResourceType }}/{{ shareModalResource.slug }}</a>
        </div>

        <p class="mx-auto my-3">OR</p>

        <p>Share Through:</p>

        <social-sharing
          :url="'https://meetandminglealpha.com/' + shareResourceType + '/' + shareModalResource.slug"
          :title="shareModalResource.name"
          :description="shareModalResource.body"
          quote="Vue is a progressive framework for building user interfaces."
          hashtags="vuejs,javascript,framework"
          :twitter-user="profile.name"
          inline-template
          class="flex justify-center items-center px-4"
        >
          <div class="flex flex-wrap mt-4">
            <div class="mb-3 w-full flex">
              <network network="email" class="w-1/3 cursor-pointer">
                <font-awesome-icon
                  class="icon text-white hover:text-teal-darkest cursor-pointer"
                  :class="(language === 'ar') ? 'ml-2' : 'mr-2'"
                  :icon="['fa', 'envelope']"
                  size="lg"
                />Email
              </network>

              <network network="facebook" class="w-1/3 cursor-pointer">
                <font-awesome-icon
                  class="icon text-white hover:text-teal-darkest cursor-pointer"
                  :class="(language === 'ar') ? 'ml-2' : 'mr-2'"
                  :icon="['fab', 'facebook']"
                  size="lg"
                />Facebook
              </network>

              <network network="googleplus" class="w-1/3 cursor-pointer">
                <font-awesome-icon
                  class="icon text-white hover:text-teal-darkest cursor-pointer"
                  :class="(language === 'ar') ? 'ml-2' : 'mr-2'"
                  :icon="['fab', 'google-plus']"
                  size="lg"
                />Google +
              </network>
            </div>

            <div class="mb-3 w-full flex">
              <network network="line" class="w-1/3 cursor-pointer">
                <font-awesome-icon
                  class="icon text-white hover:text-teal-darkest cursor-pointer"
                  :class="(language === 'ar') ? 'ml-2' : 'mr-2'"
                  :icon="['fab', 'line']"
                  size="lg"
                />Line
              </network>
              <network network="linkedin" class="w-1/3 cursor-pointer">
                <font-awesome-icon
                  class="icon text-white hover:text-teal-darkest cursor-pointer"
                  :class="(language === 'ar') ? 'ml-2' : 'mr-2'"
                  :icon="['fab', 'linkedin']"
                  size="lg"
                />LinkedIn
              </network>
              <network network="pinterest" class="w-1/3 cursor-pointer">
                <font-awesome-icon
                  class="icon text-white hover:text-teal-darkest cursor-pointer"
                  :class="(language === 'ar') ? 'ml-2' : 'mr-2'"
                  :icon="['fab', 'pinterest']"
                  size="lg"
                />Pinterest
              </network>
            </div>

            <div class="mb-3 w-full flex">
              <network network="reddit" class="w-1/3 cursor-pointer">
                <font-awesome-icon
                  class="icon text-white hover:text-teal-darkest cursor-pointer"
                  :class="(language === 'ar') ? 'ml-2' : 'mr-2'"
                  :icon="['fab', 'reddit']"
                  size="lg"
                />Reddit
              </network>
              <network network="skype" class="w-1/3 cursor-pointer">
                <font-awesome-icon
                  class="icon text-white hover:text-teal-darkest cursor-pointer"
                  :class="(language === 'ar') ? 'ml-2' : 'mr-2'"
                  :icon="['fab', 'skype']"
                  size="lg"
                />Skype
              </network>
              <network network="telegram" class="w-1/3 cursor-pointer">
                <font-awesome-icon
                  class="icon text-white hover:text-teal-darkest cursor-pointer"
                  :class="(language === 'ar') ? 'ml-2' : 'mr-2'"
                  :icon="['fab', 'telegram']"
                  size="lg"
                />Telegram
              </network>
            </div>
            <div class="mb-3 w-full flex">
              <network network="twitter" class="w-1/3 cursor-pointer">
                <font-awesome-icon
                  class="icon text-white hover:text-teal-darkest cursor-pointer"
                  :class="(language === 'ar') ? 'ml-2' : 'mr-2'"
                  :icon="['fab', 'twitter']"
                  size="lg"
                />Twitter
              </network>

              <network network="whatsapp" class="w-1/3 cursor-pointer">
                <font-awesome-icon
                  class="icon text-white hover:text-teal-darkest cursor-pointer"
                  :class="(language === 'ar') ? 'ml-2' : 'mr-2'"
                  :icon="['fab', 'whatsapp']"
                  size="lg"
                />Whatsapp
              </network>
            </div>
          </div>
        </social-sharing>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState, mapMutations, mapActions } from "vuex";

export default {
  computed: {
    ...mapState({
      language: state => state.i18n.locale,
      profile: state => state.profiles.signedInProfile,
      targetProfile: state => state.profiles.targetProfile,
      shareModalResource: state => state.ui.shareModalResource,
      shareResourceType: state => state.ui.shareResourceType
    })
  },
  data() {
    return {
      loading: false
    };
  },
  methods: {
    close() {
      this.$store.dispatch("ui/closeShareModal");
      //   this.$emit('modal:close');
    }
  },

  components: {}
};
</script>


<style lang="scss" scoped>
</style>
