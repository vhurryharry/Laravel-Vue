<template>
  <div class="footer flex justify-center flex-no-shrink z-20">
    <div class="flex w-7/8 h-full">
      <div class="flex flex-wrap items-center w-3/4 h-full">
        <div>
          <a class="text-sm" href="/contact-us">{{ $t('components.home.welcome_14') }}</a>

          <a class="text-sm mx-4" href="/privacy-policy">{{ $t('components.home.welcome_15') }}</a>

          <a class="text-sm" href="/terms-conditions">{{ $t('components.home.welcome_16') }}</a>

          <p class="text-xs">{{ $t('components.home.welcome_17') }}</p>
        </div>
      </div>

      <div class="flex w-1/2 items-center justify-end h-full">
        <a class="mx-3" href="#">
          <img src="/images/logo.png" alt style="width:30px" />
        </a>

        <a class="mx-3" href="#">
          <img src="/images/facebook_square.png" alt style="width:20px" />
        </a>

        <a class="mx-3" href="#">
          <img src="/images/twitter.png" alt style="width:20px" />
        </a>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  mounted() {},

  data() {
    return {};
  }
};
</script>

<style lang="scss" scoped>
.footer {
  width: 100%;
  height: 10vh;
  background: url(/images/footer_background.png);
  background-repeat: no-repeat;
  background-size: cover;

  .flex {
    div {
      img {
        max-width: 100%;
      }
    }
  }
}
</style>
