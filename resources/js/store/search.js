const state = {
    searchResults: {
        profiles: {},
        communities: {},
        events: {},
        posts: {},
        all: {}
    },
    filters: {},
    searchQuery: "",
    activeIndex: "all"
};
const mutations = {
    SET_ALL(state, payload) {
        state.searchResults.all = {
            ...state.searchResults.all,
            [payload.id]: payload
        };
    },
    CLEAR_ALL(state) {
        state.searchResults.all = {};
    },
    SET_SEARCH_QUERY(state, payload) {
        state.searchQuery = payload;
    },
    CLEAR_COMMUNITIES(state) {
        state.searchResults.communities = {};
    },
    SET_COMMUNITIES(state, payload) {
        state.searchResults.communities = {
            ...state.searchResults.communities,
            [payload.id]: payload
        };
    },
    SET_POSTS(state, payload) {
        state.searchResults.posts = {
            ...state.searchResults.posts,
            [payload.id]: payload
        };
    },
    CLEAR_POSTS(state) {
        state.searchResults.posts = {};
    },
    SET_PROFILES(state, payload) {
        state.searchResults.profiles = {
            ...state.searchResults.profiles,
            [payload.id]: payload
        };
    },
    CLEAR_PROFILES(state) {
        state.searchResults.profiles = {};
    },
    SET_EVENTS(state, payload) {
        state.searchResults.events = {
            ...state.searchResults.events,
            [payload.id]: payload
        };
    },
    CLEAR_EVENTS(state) {
        state.searchResults.events = {};
    },

    SET_FILTERS(state, payload) {
        state.filters = {
            ...state.filters,
            [payload.filter_name]: payload.string
        };
    },
    SET_ACTIVE_INDEX(state, payload) {
        state.activeIndex = payload;
    }
};

const actions = {
    async search({ commit, state }) {
        await axios
            .post("/search/" + state.activeIndex, {
                match: state.searchQuery === "" ? "match_all" : "match",
                fields: "name",
                query: state.searchQuery,
                filters: state.filters
            })
            .then(response => {
                if (state.activeIndex === "all") {
                    commit("CLEAR_ALL");

                    response.data.forEach(element => {
                        commit("SET_ALL", element);
                    });
                } else if (state.activeIndex === "profiles") {
                    commit("CLEAR_PROFILES");

                    _.forEach(response.data, element => {
                        commit("SET_PROFILES", element);
                    });
                } else if (state.activeIndex === "events") {
                    commit("CLEAR_EVENTS");

                    response.data.forEach(element => {
                        commit("SET_EVENTS", element);
                    });
                } else if (state.activeIndex === "communities") {
                    commit("CLEAR_COMMUNITIES");

                    response.data.forEach(element => {
                        commit("SET_COMMUNITIES", element);
                    });
                }
            })
            .catch(error => {});
    },
    async searchForAll({ commit, state }) {
        await axios
            .get("/search/all")
            .then(response => {
                response.data.forEach(element => {
                    commit("SET_ALL", element);
                });
            })
            .catch(error => {});
    },
    async searchForCommunities({ commit, state }) {
        await axios
            .get("/search/communities")
            .then(response => {
                response.data.forEach(element => {
                    commit("SET_COMMUNITIES", element);
                });
            })
            .catch(error => {});
    },
    async queryCommunities({ commit, state }, payload) {
        await axios
            .post("/search/communities", payload)
            .then(response => {
                commit("CLEAR_COMMUNITIES");
                response.data.forEach(element => {
                    commit("SET_COMMUNITIES", element);
                });
            })
            .catch(error => {});
    },
    async searchForEvents({ commit, state }) {
        await axios
            .get("/search/events")
            .then(response => {
                response.data.forEach(element => {
                    commit("SET_EVENTS", element);
                });
            })
            .catch(error => {});
    },
    async queryEvents({ commit, state }, payload) {
        await axios
            .post("/search/events", payload)
            .then(response => {
                commit("CLEAR_EVENTS");
                response.data.forEach(element => {
                    commit("SET_EVENTS", element);
                });
            })
            .catch(error => {});
    },
    async searchForProfiles({ commit, state }) {
        await axios
            .get("/search/profiles")
            .then(response => {
                _.forEach(response.data, element => {
                    commit("SET_PROFILES", element);
                });
            })
            .catch(error => {});
    },
    async queryProfiles({ commit, state }, payload) {
        await axios
            .post("/search/profiles", payload)
            .then(response => {
                commit("CLEAR_PROFILES");
                response.data.forEach(element => {
                    commit("SET_PROFILES", element);
                });
            })
            .catch(error => {});
    },

    async searchForPosts({ commit, state }) {
        await axios
            .get("/search/posts")
            .then(response => {
                _.forEach(response.data, element => {
                    commit("SET_POSTS", element);
                });
            })
            .catch(error => {});
    },
    async queryPosts({ commit, state }, payload) {
        await axios
            .post("/search/posts", payload)
            .then(response => {
                commit("CLEAR_POSTS");
                response.data.forEach(element => {
                    commit("SET_POSTS", element);
                });
            })
            .catch(error => {});
    },
    setFilters({ commit, state }, payload) {
        commit("SET_FILTERS", payload);
    },
    async addMyCommunity({ commit, state }, payload) {
        commit("ADD_MY_COMMUNITY", payload);
    },
    setNewCommunity({ commit, state }, payload) {
        commit("SET_NEW_COMMUNITY", payload);
    },
    setActiveIndex({ commit, state }, payload) {
        commit("SET_ACTIVE_INDEX", payload);
    },
    setSearchQuery({ commit, state }, payload) {
        commit("SET_SEARCH_QUERY", payload);
    }
};

const getters = {};

export default {
    namespaced: true,
    state,
    mutations,
    actions,
    getters
};
