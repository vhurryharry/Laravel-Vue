<template>
  <div class="fixed pin flex items-center">
    <div class="fixed pin bg-black opacity-50 z-10"></div>

    <div class="relative mx-6 md:mx-auto w-full md:w-1/2 lg:w-1/3 z-20 m-8">
      <div class="shadow-lg bg-grey rounded text-grey-darkest">
        <div class="flex items-center bg-teal justify-between pl-10 py-2">
          <p class="text-white">Share Event</p>

          <button class="mr-4" @click="close()">
            <img class="invert-icon w-4" src="/svgs/close.svg" alt />
          </button>
        </div>

        <div class="w-full mb-4 pl-10 py-4 pb-6 pr-10">
          <label class="block text-sm mb-1 rubik-regular" for="eventDescription">To</label>

          <input
            type="text"
            class="w-full mx-auto bg-white text-black leading-normal px-4 py-2 border border-grey-dark mb-4"
          />

          <label class="block text-sm mb-1 rubik-regular" for="eventDescription">Message</label>

          <textarea
            class="appearance-none text-black border border-grey-darker rounded-lg w-full py-2 px-3 bg-white mb-8"
            name="eventDescription"
            id="eventDescription"
            cols="30"
            rows="4"
            placeholder="Write a description about the event"
          ></textarea>

          <div class="flex justify-end">
            <button
              class="raleway-semibold text-sm bg-teal-light text-white hover:bg-white hover:text-teal text-white border-teal-light border py-2 px-4 mr-2"
              type="button"
            >Confirm</button>

            <button
              class="raleway-semibold text-sm bg-grey-darker text-white hover:bg-white hover:text-teal text-white border-grey-darker border py-2 px-4 ml-2"
              type="button"
            >Cancel</button>
          </div>
        </div>
      </div>
    </div>
  </div>
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
