<template>
  <div class></div>
</template>

<script>
export default {
  created() {},
  props: [],
  data() {
    return {};
  },

  methods: {
    close() {
      this.$emit("modal:close");
    }
  },
  components: {}
};
</script>

<style lang="scss" scoped>
</style>
