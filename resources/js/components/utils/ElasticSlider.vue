<template>
  <flickity
    class="carousel py-5 w-full"
    ref="flickity"
    :options="flickityOptions"
    v-if="Object.keys(cardsResource).length > 0"
  >
    <div
      class="carousel-cell card-item-small text-base flex flex-col w-1/2 mx-2"
      v-for="(card, index) in cardsResource"
      :key="index"
    >
      <div
        class="w-full regular-top-border-radius"
        style="min-height:130px;max-height:130px;overflow:hidden;"
      >
        <img
          class="h-full w-full object-contain"
          style
          :src="'/storage/images/' + card.image_path"
          alt
        />
      </div>

      <div class="flex flex-col pb-2 px-6 leading-normal w-full">
        <a
          :href="'/' + resource +  '/' + card.slug"
          class="text-teal rubik-medium text-sm mt-2 mb-0"
        >{{ card.name || card.title }}</a>

        <div v-if="resource === 'events'" class="flex flex-no-wrap text-xs mb-2">
          <p class>{{ moment.unix(card.start_date).format('MMMM DD, YYYY') }}</p>

          <p
            v-if="card.starts_at"
          >&nbsp;at&nbsp;{{ moment.unix(card.starts_at).tz(profile.timezone.timezone_value.key).format("HH:mm") }}</p>

          <span v-if="card.ends_at">
            <p
              class
            >&nbsp;-&nbsp;{{ moment.unix(card.ends_at).tz(profile.timezone.timezone_value.key).format('hh:mm A') }}</p>
          </span>
        </div>

        <div class="w-full clearfix cursor-pointer">
          <p class="text-xs raleway-semibold">
            <span class="text-teal">{{ card.participants.length }}</span>
            <span
              v-if="card.participants.length === 1"
            >{{ $t('components.events.person_is_attending') }}</span>
            <span v-else>{{ $t('components.events.people_are_attending') }}</span>
          </p>
        </div>

        <div class="flex mt-3">
          <div
            class="flex items-center text-grey-darker hover:text-grey-darkest cursor-pointer"
            @click="like(card)"
          >
            <font-awesome-icon
              class="icon text-teal cursor-pointer hover:text-teal-light"
              :class="(language === 'ar') ? 'ml-2' : 'mr-2'"
              :icon="['fa', 'heart']"
              size="sm"
            />

            <!-- <p
              class="text-xs text-teal"
              :class="(language === 'ar') ? 'ml-6' : 'mr-6'"
              v-if="liked(card.love_reactant.reactions)"
            >{{ $t('shared.liked') }} ({{ card.reactions_count }})</p>

            <p
              v-else
              class="text-xs"
              :class="(language === 'ar') ? 'ml-6' : 'mr-6'"
            >{{ $t('shared.liked') }} ({{ card.reactions_count }})</p>-->

            <p
              class="text-xs text-teal"
              :class="(language === 'ar') ? 'ml-6' : 'mr-6'"
              v-if="liked(card.love_reactant.reactions)"
            >{{ $t('shared.liked') }} ({{ Object.keys(card.love_reactant.reactions).length }})</p>

            <p
              v-else
              class="text-xs"
              :class="(language === 'ar') ? 'ml-6' : 'mr-6'"
            >{{ $t('shared.like') }} ({{ Object.keys(card.love_reactant.reactions).length }})</p>
          </div>

          <div
            class="flex items-center text-grey-darker hover:text-grey-darkest cursor-pointer"
            @click="openShareModal(card)"
          >
            <font-awesome-icon
              class="icon text-teal cursor-pointer hover:text-teal-light"
              :class="(language === 'ar') ? 'ml-2' : 'mr-2'"
              :icon="['fa', 'share']"
              size="sm"
            />
            <p class="cursor-pointer text-xs">{{ $t('shared.share') }}</p>
          </div>
        </div>
      </div>
    </div>
  </flickity>
</template>

<script>
import Flickity from "vue-flickity";
import { mapState, mapMutations, mapActions } from "vuex";

export default {
  props: {
    flickityOptions: {},
    cards: {},
    loading: true,
    resource: null
  },
  computed: {
    ...mapState({
      profile: state => state.profiles.signedInProfile,
      language: state => state.i18n.locale
    })
  },
  created() {
    this.cardsResource = this.cards;
  },
  mounted() {},
  watch: {
    loading(value) {
      let that = this;
      if (!value) {
      }
    },
    cards(value) {
      this.cardsResource = value;
    }
  },
  data() {
    return {
      cardsResource: null
    };
  },
  methods: {
    openShareModal(value) {
      this.$store.dispatch("ui/openShareModal", value);
    },
    flickityResize(profileId) {
      this.$refs.flickity.resize();
      this.$refs.flickity.rerender();
    },
    like(card) {
      let type;
      if (this.resource === "events") {
        type = "event";
      } else if (this.resource === "communities") {
        type = "community";
      }

      axios
        .post("/profile/" + this.profile.username + "/like", {
          slug: card.slug,
          type: type
        })
        .then(response => {
          this.$toast.open({
            duration: 5000,
            message: response.data.message,
            position: "is-bottom",
            type: "is-success",
            queue: false
          });

          let existingCard = _.find(this.cardsResource, { id: card.id });

          let reactant = _.find(card.love_reactant.reactions, {
            reacter_id: this.profile.id
          });

          if (reactant) {
            existingCard.love_reactant.reactions.pop({
              reacter_id: this.profile.id
            });
            // existingCard.reactions_count--;
          } else {
            existingCard.love_reactant.reactions.push({
              reacter_id: this.profile.id
            });
            // existingCard.reactions_count++;
          }

          this.cardsResource = {
            ...this.cardsResource,
            [existingCard.id]: existingCard
          };
          // }
        })
        .catch(error => {});
    },
    liked(reactions) {
      // console.log(reactions);
      let liked = reactions.find(
        reaction => reaction.reacter_id === this.profile.id
      );

      // console.log
      return liked;
    }
  },
  components: {
    Flickity
  }
};
</script>
<style lang="sass" scoped>
</style>
