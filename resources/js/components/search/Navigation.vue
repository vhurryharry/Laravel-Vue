<template>
  <div class="flex flex-col text-black mx-8 text-grey-dark settings-nav">
    <span class="text-lg w-full cursor-text text-white">{{ $t('components.search.category')}}</span>
    <p
      id="all-controls"
      class="search-navigation w-full py-3 border-b border-grey-darker cursor-pointer hover:text-white"
      @click="setSearchType('all-controls', 'all')"
    >{{ $t('components.search.all')}}</p>
    <p
      id="events-controls"
      class="search-navigation w-full py-3 border-b border-grey-darker cursor-pointer hover:text-white"
      @click="setSearchType('events-controls', 'events')"
    >{{ $t('components.search.events')}}</p>
    <p
      id="communities-controls"
      class="search-navigation w-full py-3 border-b border-grey-darker cursor-pointer hover:text-white"
      @click="setSearchType('communities-controls', 'communities')"
    >{{ $t('components.search.communities')}}</p>
    <p
      id="friends-controls"
      class="search-navigation w-full py-3 border-b border-grey-darker cursor-pointer hover:text-white"
      @click="setSearchType('friends-controls', 'profiles')"
    >{{ $t('components.search.friends')}}</p>
    <p
      id="posts-controls"
      class="search-navigation w-full py-3 border-b border-grey-darker cursor-pointer hover:text-white"
      @click="setSearchType('posts-controls', 'posts')"
    >{{ $t('components.search.posts')}}</p>
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
  mounted() {
    $("p").click(function() {
      if (this.language === "ar") {
        $("p").removeClass(["text-white border-white pr-4"]);
        $(this).addClass(["text-white border-white pr-4"]);
      } else {
        $("p").removeClass(["text-white border-white pl-4"]);
        $(this).addClass(["text-white border-white pl-4"]);
      }
    });
  },

  data() {
    return {};
  },

  methods: {
    setSearchType(searchType, index) {
      this.$emit("search-set-criteria", {
        searchType: searchType,
        index: index
      });
    }
  },
  components: {}
};
</script>

<style lang="scss" scoped>
p {
  transition: 0.25s all ease;
}

.active {
}
</style>
