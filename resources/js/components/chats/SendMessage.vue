<template>
  <div class="flex items-center relative my-4 outline-none bg-white rounded-full">
    <div
      class="flex w-12 pin-t cursor-pointer items-center"
      :class="(language === 'ar') ? 'pin-r mr-5' : 'pin-l ml-5'"
    >
      <div class="flex justify-center items-center overflow-hidden">
        <input
          class="relative opacity-0 w-full z-20 cursor-pointer"
          type="file"
          id="uploadInput"
          @change="onFileChange"
          ref="fileInput"
        />
        <font-awesome-icon
          class="z-0 absolute icon text-grey-darkest cursor-pointer w-full hover:text-grey-darkest"
          :icon="['fa', 'plus']"
          size="2x"
        />
      </div>
    </div>

    <div class="flex w-16 icon-grey absolute" :class="(language === 'ar') ? 'pin-l' : 'pin-r'">
      <font-awesome-icon
        class="icon text-grey-darkest cursor-pointer hover:text-grey-dark"
        :icon="['fa', 'microphone']"
        size="2x"
        @click="openRecorder"
      />
    </div>

    <input
      type="search"
      class="text-lg w-full bg-transparent rounded-full p-3 py-4 text-grey-darkest h-full"
      :class="(language === 'ar') ? 'pr-0' : 'pl-0'"
      :placeholder="$t('shared.name_placeholder')"
      id="send-message"
      v-model="message"
      @keyup.enter="sendMessage"
    />
  </div>
</template>

<script>
import { mapState, mapMutations, mapActions } from "vuex";

export default {
  computed: {
    ...mapState({
      profile: state => state.profiles.signedInProfile,
      language: state => state.i18n.locale
    })
  },
  created() {
    // value.loading = false;
  },
  activated() {
    this.active = false;
  },
  data() {
    return {
      message: null,
      recording: null,
      page: 1,
      messageCount: 0,
      file: null,
      headers: {
        "Content-Type": "multipart/form-data",
        _token: document
          .querySelector('meta[name="csrf-token"]')
          .getAttribute("content")
      }
    };
  },

  methods: {
    openRecorder() {
      this.$emit("chat:open-recorder");
    },
    closeRecorder() {
      this.$emit("chat:close-recorder");
    },
    uploadRecordingDone() {
      this.$emit("chat:close-recorder");
    },
    callback(msg) {
      // console.debug("Event: ", msg);
    },
    onFileChange(e) {
      let files = e.target.files || e.dataTransfer.files;
      if (!files.length) return;
      this.createImage(files[0]);
    },
    createImage(file, type = null) {
      let reader = new FileReader();
      let vm = this;
      reader.onload = e => {
        vm.file = e.target.result;
        this.message = file.name;

        this.sendMessage(type);
      };

      reader.readAsDataURL(file);
    },
    scrollBottom() {
      this.$emit("chat:message-sent");
    },
    sendMessage(type = null) {
      let that = this;
      let tempMessage = this.message;

      setTimeout(this.scrollBottom, 1);

      if (this.message !== null) {
        this.message = null;

        this.$store
          .dispatch("conversations/sendMessage", {
            message: tempMessage,
            file: this.file,
            type: type,
            headers: {
              "Content-Type": "multipart/form-data",
              _token: document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content")
            }
          })
          .then(response => {
            // this.openRecorder = false;
            this.$emit("chat:close-recorder");

            (that.file = null),
              this.$store.dispatch("conversations/markConversationAsRead");
          });
      }
    }
  },
  components: {}
};
</script>

<style lang="scss" scoped>
</style>
