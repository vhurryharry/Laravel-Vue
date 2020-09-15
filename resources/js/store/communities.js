const state = {
    communities: {},
    myCommunities: {},
    newCommunity: null,
    activeCommunity: {},
    profilesThatCanBeInvited: {},
    upcomingEvents: {},
    popularCommunities: {}
};

const mutations = {
    SET_UPCOMING_EVENTS(state, payload) {
        state.upcomingEvents = {
            ...state.upcomingEvents,
            [payload.id]: payload
        };
    },
    SET_COMMUNITIES(state, payload) {
        state.communities = {
            ...state.communities,
            [payload.id]: payload
        };
    },

    SET_MY_COMMUNITIES(state, payload) {
        state.myCommunities = {
            ...state.myCommunities,
            [payload.id]: payload
        };
    },
    SET_POPULAR_COMMUNITIES(state, payload) {
        state.popularCommunities = {
            ...state.popularCommunities,
            [payload.id]: payload
        };
    },
    ADD_MY_COMMUNITY(state, payload) {
        state.myCommunities = {
            ...state.myCommunities,
            [payload.id]: payload
        };
    },

    SET_NEW_COMMUNITY(state, payload) {
        state.newCommunity = payload;
    },

    SET_ACTIVE_COMMUNITY(state, payload) {
        state.activeCommunity = payload;
    },

    SET_PROFILES_THAT_CAN_BE_INVITED(state, payload) {
        state.profilesThatCanBeInvited = {
            ...state.profilesThatCanBeInvited,
            [payload.id]: payload
        };
    },

    SET_FRIEND_REQUEST_SENT(state, payload) {
        console.log(payload);

        let community = _.find(state.myCommunities, {
            id: state.activeCommunity.id
        });

        let recipient = _.find(community.participants, {
            id: payload
        });

        let recipientOnActive = _.find(state.activeCommunity.participants, {
            id: payload
        });

        recipient.friend_request_sent = true;
        recipientOnActive.friend_request_sent = true;
    },
    SET_FRIENDSHIP(state, payload) {
        let community = _.find(state.myCommunities, {
            id: state.activeCommunity.id
        });

        let recipient = _.find(community.participants, {
            id: payload
        });

        community.mutuals = {
            ...community.mutuals,
            [recipient.id]: recipient
        };

        recipient.friend_request_sent = false;
        recipient.has_friend_request_from = false;

        recipient.is_friend = true;
    },
    CLEAR_FRIEND_REQUEST(state, payload) {
        let community = _.find(state.myCommunities, {
            id: state.activeCommunity.id
        });

        let recipient = _.find(community.participants, {
            id: payload
        });

        recipient.friend_request_sent = false;
        recipient.has_friend_request_from = false;
    },
    UPDATE_COMMUNITY_LIKE(state, payload) {
        let community = _.find(state.myCommunities, {
            id: payload.community.id
        });

        let reactant = _.find(community.love_reactant.reactions, {
            reacter_id: payload.profile.id
        });

        if (reactant) {
            community.love_reactant.reactions.pop({
                reacter_id: payload.profile.id
            });
            community.reactions_count--;
        } else {
            community.love_reactant.reactions.push({
                reacter_id: payload.profile.id
            });
            community.reactions_count++;
        }

        state.myCommunities = {
            ...state.myCommunities,
            [community.id]: community
        };
    },
    UPDATE_COMMUNITY_REQUEST(state, payload) {
        state.activeCommunity.membership_requested = true;
    }
};

const actions = {
    async getCommunities({ commit, state }) {
        await axios
            .get("/communities/all")
            .then(response => {
                _.forEach(response.data.communities, (value, key) => {
                    commit("SET_COMMUNITIES", value);
                });
            })
            .catch(error => {
                this.loading = false;
            });
    },
    async getPopularCommunities({ commit, rootState }) {
        await axios
            .post("/communities/popular")
            .then(response => {
                response.data.communities.forEach(element => {
                    commit("SET_POPULAR_COMMUNITIES", element);
                });
            })
            .catch(error => {
                this.loading = false;
            });
    },
    async getMyCommunities({ commit, rootState }) {
        await axios
            .get(
                "/profile/" +
                    rootState.profiles.signedInProfile.username +
                    "/myCommunities"
            )
            .then(response => {
                response.data.communities.forEach(element => {
                    commit("SET_MY_COMMUNITIES", element);
                });
            })
            .catch(error => {
                this.loading = false;
            });
    },
    async getUpcomingEvents({ commit, state }) {
        await axios
            .get("/community/" + state.activeCommunity.slug + "/upcomingEvents")
            .then(response => {
                console.log(response);

                response.data.events.forEach(element => {
                    console.log("Fdsfdsfds");

                    console.log(element);
                    commit("SET_UPCOMING_EVENTS", element);
                });
            })
            .catch(error => {
                this.loading = false;
            });
    },

    async addMyCommunity({ commit, state }, payload) {
        commit("ADD_MY_COMMUNITY", payload);
    },

    addFriendRequestSent({ commit, state }, payload) {
        commit("SET_FRIEND_REQUEST_SENT", payload);
    },

    addIsFriend({ commit, state }, payload) {
        commit("SET_FRIENDSHIP", payload);
    },

    clearFriendRequest({ commit, state }, payload) {
        commit("CLEAR_FRIEND_REQUEST", payload);
    },

    setNewCommunity({ commit, state }, payload) {
        commit("SET_NEW_COMMUNITY", payload);
        commit("SET_ACTIVE_COMMUNITY", payload);
    },

    setActiveCommunity({ commit, state }, payload) {

        
        commit("SET_ACTIVE_COMMUNITY", payload);
    },

    async setProfilesThatCanBeInvited({ commit, state }, payload) {
        payload.forEach(element => {
            commit("SET_PROFILES_THAT_CAN_BE_INVITED", element);
        });
    },

    addMyCommunity({ commit, state }, payload) {
        commit("ADD_MY_COMMUNITY", payload);
    },
    updateCommunityLike({ commit, state }, payload) {
        commit("UPDATE_COMMUNITY_LIKE", payload);
    },
    updateCommunityRequest({ commit, state }) {
        commit("UPDATE_COMMUNITY_REQUEST");
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
