<template>
  <div class="flex cursor-pointer">
    <picture-input
      class="flex rubik-medium"
      ref="pictureInput"
      width="250"
      height="250"
      accept="image/jpeg, image/png"
      size="10"
      button-class="btn"
      :custom-strings="{
        drag: $t('shared.click_to_upload'),
        upload: $t('shared.click_to_upload'),
        select: $t('shared.click_to_upload'),
        tap: $t('shared.click_to_upload'),
      }"
      @change="onChange"
    ></picture-input>
  </div>
</template>

<script>
import VueCoreImageUpload from "vue-core-image-upload";
import PictureInput from "vue-picture-input";

export default {
  props: ["profile", "existingImage"],
  created() {},
  mounted() {},
  data() {
    return {
      data: {
        _token: document
          .querySelector('meta[name="csrf-token"]')
          .getAttribute("content"),
        text: ""
      },
      image: "default.jpeg",
      croppedImage: {},
      initialImage: "/svgs/upload_picture.svg",
      imagePresent: false,
      fileData: null,
      src: "/app/public/images/profiles/default.jpeg"
    };
  },
  watch: {
    image(value) {}
  },
  methods: {
    imagechanged(res) {},
    imageuploading() {},
    imageuploaded(res) {},
    uploadCroppedImage() {},
    onChange(image) {
      if (image) {
        this.initialImage = image;
        this.$emit("image-chosen", image);
      } else {
        console.log("FileReader API not supported: use the <form>, Luke!");
      }
    }
  },
  components: {
    VueCoreImageUpload,
    PictureInput
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
