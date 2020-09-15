<template>
  <div class="flex w-full">
    <div class="raleway-regular text-xs py-6 w-1/2" style="margin-top: auto;">
      <a href="/contact-us" class="mr-6">{{ $t('components.home.welcome_14') }}</a>
      <a href="/privacy-policy" class="mr-6">{{ $t('components.home.welcome_15') }}</a>
      <a href="/terms-conditions" class="mr-6">{{ $t('components.home.welcome_16') }}</a>
    </div>

    <p
      class="raleway-regular text-xs py-6 w-1/2"
      :class="(language === 'ar') ? 'text-left' : 'text-right'"
      style="margin-top: auto;"
    >{{ $t('components.home.welcome_17') }}</p>
  </div>
</template>

<script>
import { mapState, mapMutations, mapActions } from "vuex";

export default {
  computed: {
    ...mapState({
      language: state => state.i18n.locale
    })
  },
  mounted() {},

  data() {
    return {};
  }
};
</script>

<style lang="scss" scoped>
</style>
