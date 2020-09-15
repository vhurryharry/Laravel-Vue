import uuidv4 from "uuid/v4";
import Axios from "axios";
import * as types from "./mutation-types";

const state = {
    bootstrapedActiveCircle: null,
    activeCircle: null,
    newCircle: {},
    logs: {},
    members: {},
    presentMembers: {},
    inHallway: [],
    inHall: []
};

const mutations = {
    SET_MEMBERS(state, payload) {
        let member = _.find(state.bootstrapedActiveCircle.participants, {
            id: payload.profile_id
        });
    },
    ADD_MEMBER(state, payload) {
        let participant = _.find(state.bootstrapedActiveCircle.participants, {
            username: payload.username
        });

        state.presentMembers = {
            ...state.presentMembers,
            [participant.id]: participant
        };
    },
    SET_PRESENT_MEMBERS(state, payload) {
        _.forEach(payload, user => {
            let participant = _.find(
                state.bootstrapedActiveCircle.participants,
                {
                    username: user.username
                }
            );

            state.presentMembers = {
                ...state.presentMembers,
                [participant.id]: participant
            };
        });
    },
    SET_IN_HALLWAY_ARRAY(state, payload) {
        state.inHallway.push(payload);
    },
    SET_IN_HALL_ARRAY(state, payload) {
        state.inHall.push(payload);
    },
    REMOVE_DISCONNECTED_MEMBER(state, payload) {
        let member = _.find(state.presentMembers, {
            username: payload.username
        });

        Vue.delete(state.presentMembers, member.id);
    },
    SET_CIRCLES(state, payload) {
        state.circles = {
            ...state.circles,
            [payload.id]: payload
        };
    },
    SET_ACTIVE_CIRCLE(state, payload) {
        state.activeCircle = payload;
    },
    SET_NEW_CIRCLE(state, payload) {
        state.newCircle = payload;
    },
    SET_LOGS(state, payload) {
        state.logs = {
            ...state.logs,
            [payload.id]: payload
        };
    },
    SET_BOOTSTRAPED_ACTIVE_CIRCLE(state, payload) {
        state.bootstrapedActiveCircle = payload;
    },
    UPDATE_FRIENDSHIP_STATUS_TO_SENT(state, payload) {
        let profile = _.find(state.presentMembers, {
            username: payload.username
        });

        profile.friend_request_sent = true;
    },
    UPDATE_FRIENDSHIP_STATUS_TO_FRIENDS(state, payload){
        let profile = _.find(state.presentMembers, {
            username: payload.username
        });

        profile.is_friend = true;
    }
};

const actions = {
    setCircles({ commit, state }, payload) {
        commit("SET_CIRCLES", payload);
    },
    clearUnreadForActiveConversation({ commit, rootState, dispatch }, payload) {
        commit("CLEAR_UNREAD_FOR_ACTIVE_CONVERSATION");
    },
    setNewcircle({ commit, state }, payload) {
        commit("SET_NEW_CIRCLE", payload);
    },
    setLogs({ commit, state }, payload) {
        commit("SET_LOGS", payload);
    },
    setActiveCircle({ commit, state }, payload) {
        commit("SET_ACTIVE_CIRCLE", payload);
    },
    async getCircles({ commit, rootState }) {
        await axios
            .get(
                "/profile/" +
                    rootState.profiles.signedInProfile.username +
                    "/events"
            )
            .then(response => {
                response.data.events.forEach(element => {
                    commit("SET_CIRCLES", element);
                });
            });
    },
    async getCircle({ commit, state }, payload) {
        await axios.post("/hallway/" + payload.slug).then(response => {
            commit("SET_BOOTSTRAPED_ACTIVE_CIRCLE", response.data.event);
        });
    },
    async enterConference({ commit, rootState }, payload) {
        await axios
            .post("/hallway/" + payload.slug + "/enter")
            .then(response => {
                response.data.conferenceMembers.forEach(element => {
                    commit("SET_MEMBERS", element);
                });
            });
    },
    async leaveConference({ commit, rootState }, payload) {
        await axios
            .post("/hallway/" + payload.slug + "/leave")
            .then(response => {});
    },
    SetPresentMembers({ commit, state }, payload) {
        commit("SET_PRESENT_MEMBERS", payload);
    },
    AddMember({ commit, state }, payload) {
        commit("ADD_MEMBER", payload);
    },
    setInHallwayArray({ commit, state }, payload) {
        commit("SET_IN_HALLWAY_ARRAY", payload);
    },
    setInHallArray({ commit, state }, payload) {
        commit("SET_IN_HALL_ARRAY", payload);
    },
    RemoveDisconnectedMember({ commit, state }, payload) {
        commit("REMOVE_DISCONNECTED_MEMBER", payload);
    },
    bootstrapActiveCircle({ commit, state }, payload) {
        commit("SET_BOOTSTRAPED_ACTIVE_CIRCLE", payload);
    },
    updateFriendshipStatusToSent({ commit, state }, payload) {
        commit("UPDATE_FRIENDSHIP_STATUS_TO_SENT", payload);
    },
    updateFriendshipStatusToSent({ commit, state }, payload) {
        commit("UPDATE_FRIENDSHIP_STATUS_TO_SENT", payload);
    },
    updateFriendshipStatusToFriends({ commit, state }, payload) {
        console.log(payload);
        commit("UPDATE_FRIENDSHIP_STATUS_TO_FRIENDS", payload);
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
