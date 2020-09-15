const state = {
    signedInProfile: null,
    profiles: {},
    friends: {},
    guestList: {},
    blockedProfiles: {},
    bannedUser: null,
    targetProfile: null,
    ESprofiles: {}
};

const mutations = {
    SET_SIGNED_IN_USER(state, payload) {
        state.signedInProfile = payload;
    },
    SET_BANNED_USER(state, payload) {
        state.bannedUser = payload;
    },
    SET_TARGET_PROFILE(state, payload) {
        state.targetProfile = payload;
    },
    SET_FRIENDS(state, payload) {
        payload.loading = false;

        state.friends = {
            ...state.friends,
            [payload.id]: payload
        };
    },
    UPDATE_BIO(state, payload) {
        state.signedInProfile.bio = payload.bio;
    },
    SET_PROFILES(state, payload) {
        state.profiles = {
            ...state.profiles,
            [payload.id]: payload
        };
    },
    SET_ES_PROFILES(state, payload) {
        state.ESprofiles = {
            ...state.ESprofiles,
            [payload.id]: payload
        };
    },
    SET_BLOCKED_PROFILES(state, payload) {
        state.blockedProfiles = {
            ...state.blockedProfiles,
            [payload.id]: payload
        };
    },
    PREFILL_GUEST_LIST(state, payload) {
        state.guestList = {
            ...state.guestList,
            [payload.id]: payload
        };
    },
    MOVE_TO_GUEST_LIST(state, payload) {
        state.guestList = {
            ...state.guestList,
            [payload.id]: payload
        };
    },
    REMOVE_FROM_GUEST_LIST(state, payload) {
        state.profiles = {
            ...state.profiles,
            [payload.id]: state.guestList[payload.id]
        };

        Vue.delete(state.guestList, payload.id);
    },
    RESET_GUEST_LIST(state, payload) {
        state.guestList = {};
    }
};

const actions = {
    async updateDateOfBirth({ commit, rootState }, payload) {
        await axios
            .put(
                "/user/" +
                    rootState.profiles.signedInProfile.username +
                    "/settings",
                {
                    date_of_birth: payload.date_of_birth
                }
            )
            .then(response => {
                commit("SET_SIGNED_IN_USER", response.data.profile);
            });
    },
    async updateBio({ commit, rootState }, payload) {
        await axios
            .put("/user/settings/updateBio", {
                bio: payload.bio
            })
            .then(response => {
                commit("UPDATE_BIO", response.data.profile.bio);
            });
    },
    async activateAccount({ commit, rootState }, payload) {
        await axios.post("/user/settings/activate").then(response => {
            commit("SET_SIGNED_IN_USER", response.data.profile);
        });
    },
    async deactivateAccount({ commit, rootState }, payload) {
        await axios.post("/user/settings/deactivate").then(response => {
            commit("SET_SIGNED_IN_USER", response.data.profile);
        });
    },
    updateProfile({ commit, rootState }, payload) {
        commit("SET_SIGNED_IN_USER", payload);
    },
    setTargetProfile({ commit, rootState }, payload) {
        commit("SET_TARGET_PROFILE", payload);
    },
    setSignedInProfile({ commit, state }, payload) {
        commit("SET_SIGNED_IN_USER", payload);
    },
    setBannedUser({ commit, state }, payload) {
        commit("SET_BANNED_USER", payload);
    },
    async getFriends({ commit, state }, payload) {
        await axios.get("/profile/" + payload.username).then(response => {
            response.data.friends.forEach(element => {
                commit("SET_FRIENDS", element);
            });
        });
    },
    async getESProfiles({ commit, state }, payload) {
        await axios
            .post("/search/profiles", {
                match: "match_all",
                fields: "name",
                query: null,
                filters: null,
                page: payload ? payload.page : 0
            })
            .then(response => {
                _.forEach(response.data, element => {
                    commit("SET_ES_PROFILES", element);
                });
            })
            .catch(error => {});
    },
    async getProfiles({ commit, state }, payload) {
        await axios
            .post("/profiles", {
                count: payload ? payload.count : null
            })
            .then(response => {
                response.data.profiles.forEach(element => {
                    commit("SET_PROFILES", element);
                });
            });
    },
    resetGuestList({ commit, state }) {
        commit("RESET_GUEST_LIST");
    },
    prefillGuestList({ commit, state }, payload) {
        commit("PREFILL_GUEST_LIST", payload);
    },
    moveToGuestList({ commit, state }, payload) {
        commit("MOVE_TO_GUEST_LIST", payload);
    },
    removeFromGuestList({ commit, state }, payload) {
        commit("REMOVE_FROM_GUEST_LIST", payload);
    },
    async getBlockedProfiles({ commit, state }, payload) {
        await axios.get("/user/settings/blockedProfiles").then(response => {
            _.forEach(response.data.blocked_profiles, blockedProfile => {
                commit("SET_BLOCKED_PROFILES", blockedProfile);
            });
        });
    },
    updateFriendshipStatus({ commit, state }, payload) {
        commit("UPDATE_FRIENDSHIP_STATUS", payload);
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
