<template>
  <b-tooltip :label="$t('shared.upload_zoom')" size="is-large" multilined>
    <div class="flex flex-col mx-auto items-center">
      <div
        class="overflow-hidden"
        style="border-radius:50%;width:150px;height:150px;"
        slot="trigger"
      >
        <img :src="'/storage/images/' + profile.image_path" alt />
      </div>

      <vue-core-image-upload
        class="raleway-semibold mt-6"
        crop="local"
        @imageuploaded="imageuploaded"
        @imageuploading="imageuploading"
        extensions="png,gif,jpeg,jpg"
        :data="data"
        :text="$t('shared.upload_image')"
        resize="local"
        :max-file-size="5242880"
        url="/user/settings/updateImage"
      ></vue-core-image-upload>

      <div class="flex mt-3" v-show="imagePresent">
        <button
          class="raleway-semibold hover:bg-white hover:text-teal text-white border-white border py-2 px-6 rounded ml-2"
          @click="stopSelection"
        >Remove</button>

        <button
          class="raleway-semibold hover:bg-white hover:text-teal text-white border-white border py-2 px-6 rounded ml-2"
          @click="uploadCroppedImage"
        >Accept</button>
      </div>
    </div>
  </b-tooltip>
</template>

<script>
import VueCoreImageUpload from "vue-core-image-upload";

export default {
  props: ["profile"],
  created() {
    if (this.profile.image_path !== "default.jpeg") {
    }
  },
  mounted() {
    var image = new Image();
    image.src = "/storage/images/" + this.profile.image_path;

    this.initialImage = image;
  },
  data() {
    return {
      data: {
        _token: document
          .querySelector('meta[name="csrf-token"]')
          .getAttribute("content")
      },
      image: "default.jpeg",
      croppedImage: {},
      initialImage: null,
      imagePresent: false,
      fileData: null,
      src: "/app/public/images/profiles/default.jpeg"
    };
  },
  watch: {
    image(value) {}
  },
  methods: {
    imageuploading() {
      console.log("111111111111");
    },
    imageuploaded(res) {
      console.log("FDSFSDBSDB");
      console.log(res.profile);

      this.src = res.profile.image_path;
      this.$store.dispatch("profiles/updateProfile", res.profile);
    },
    onChange(e) {
      if (!e.target.files.length) return;

      let file = e.target.files[0];

      let reader = new FileReader();

      reader.readAsDataURL(file);

      reader.onload = e => {
        this.image = e.target.result;
      };
    },
    set(file) {
      let generatedDataUrl = this.croppedImage.generateDataUrl();
    },
    stopSelection() {
      this.croppedImage.remove();
      this.imagePresent = false;
    },
    uploadCroppedImage() {
      let dataUrl = this.croppedImage.generateDataUrl();

      axios
        .post("/user/settings/updateImage", {
          file: dataUrl,
          name: "fefefe"
        })
        .then(response => {
          this.imagePresent = false;
          this.$toast.open({
            duration: 5000,
            message: "Image updated.",
            position: "is-bottom",
            type: "is-success"
          });
        })
        .catch(error => {
          this.$toast.open({
            duration: 5000,
            message: "Error.",
            position: "is-bottom",
            type: "is-failure"
          });
        });
    }
  },
  components: {
    VueCoreImageUpload
  }
};
</script>

<style lang="scss" scoped>
.croppa-container {
  width: 140px;
  height: 140px;
  border: 2px solid grey;
  border-radius: 50%;
  overflow: hidden;
  margin: 0 auto;
}

.croppa-container:hover {
  opacity: 1;
  background-color: #8ac9ef;
}
</style>
