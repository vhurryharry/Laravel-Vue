import moment from "moment";

const state = {
    events: {},
    myEvents: {},
    newEvent: null,
    activeEvent: {},
    eventsHappeningNow: {},
    profilesThatCanBeInvited: {},
    creatingFrom: "events"
};

const mutations = {
    SET_EVENTS(state, payload) {
        let tempDate = new Date(payload.start_date);
        // console.log(tempDate);

        // console.log(moment(tempDate).format("YYYY-MM-DD"));

        payload.start = moment(tempDate).format("YYYY-MM-DD");
        // payload.start = tempDate.toISOString();

        console.log(payload);
        state.events = {
            ...state.events,
            [payload.id]: payload
        };
    },
    SET_MY_EVENTS(state, payload) {
        state.myEvents = {
            ...state.myEvents,
            [payload.id]: payload
        };
    },
    SET_EVENTS_HAPPENING_NOW(state, payload) {
        state.eventsHappeningNow = {
            ...state.eventsHappeningNow,
            [payload.id]: payload
        };
    },
    ADD_MY_EVENT(state, payload) {
        state.myEvents = {
            ...state.myEvents,
            [payload.id]: payload
        };
    },
    SET_NEW_EVENT(state, payload) {
        state.newEvent = payload;
    },
    SET_CREATING_FROM(state, payload) {
        state.creatingFrom = payload;
    },

    SET_ACTIVE_EVENT(state, payload) {
        state.activeEvent = payload;
    },

    SET_PROFILES_THAT_CAN_BE_INVITED(state, payload) {
        state.profilesThatCanBeInvited = {
            ...state.profilesThatCanBeInvited,
            [payload.id]: payload
        };
    },

    SET_FRIEND_REQUEST_SENT(state, payload) {
        let event = _.find(state.myEvents, {
            id: state.activeEvent.id
        });

        let recipient = _.find(event.participants, {
            id: payload
        });

        let recipientOnActive = _.find(state.activeEvent.participants, {
            id: payload
        });

        recipient.friend_request_sent = true;
        recipientOnActive.friend_request_sent = true;
    },
    SET_FRIENDSHIP(state, payload) {
        let event = _.find(state.myEvents, {
            id: state.activeEvent.id
        });

        let recipient = _.find(event.participants, {
            id: payload
        });

        event.mutuals = {
            ...event.mutuals,
            [recipient.id]: recipient
        };

        recipient.friend_request_sent = false;
        recipient.has_friend_request_from = false;
        recipient.is_friend = true;
    },
    CLEAR_FRIEND_REQUEST(state, payload) {
        let event = _.find(state.myEvents, {
            id: state.activeEvent.id
        });

        let recipient = _.find(event.participants, {
            id: payload
        });

        recipient.friend_request_sent = false;
        recipient.has_friend_request_from = false;
    },
    UPDATE_EVENT_LIKE(state, payload) {
        let event = _.find(state.myEvents, { id: payload.event.id });

        let reactant = _.find(event.love_reactant.reactions, {
            reacter_id: payload.profile.id
        });

        if (reactant) {
            event.love_reactant.reactions.pop({
                reacter_id: payload.profile.id
            });
            event.reactions_count--;
        } else {
            event.love_reactant.reactions.push({
                reacter_id: payload.profile.id
            });
            event.reactions_count++;
        }

        state.myEvents = {
            ...state.myEvents,
            [event.id]: event
        };
    },
    UPDATE_EVENT_REQUEST(state, payload) {
        state.activeEvent.membership_requested = true;
    }
};

const actions = {
    async getEvents({ commit, state }) {
        await axios
            .get("/events/all")
            .then(response => {
                _.forEach(response.data.events, (value, key) => {
                    commit("SET_EVENTS", value);
                });
            })
            .catch(error => {
                this.loading = false;
            });
    },
    async getMyEvents({ commit, state }, payload) {
        await axios
            .get("/profile/" + payload.username + "/events")
            .then(response => {
                response.data.events.forEach(element => {
                    commit("SET_MY_EVENTS", element);
                });
            })
            .catch(error => {
                this.loading = false;
            });
    },
    async getEventsHappeningNow({ commit, state }, payload) {
        await axios
            .post("/search/events", {
                match: "match_all",
                fields: "name",
                query: "",
                filters: {
                    ranges: ["now"]
                }
            })
            .then(response => {
                response.data.forEach(element => {
                    commit("SET_EVENTS_HAPPENING_NOW", element);
                });
            });
    },
    addMyEvent({ commit, state }, payload) {
        commit("ADD_MY_EVENT", payload);
    },
    updateEventLike({ commit, state }, payload) {
        commit("UPDATE_EVENT_LIKE", payload);
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

    setCreatingFrom({ commit, state }, payload) {
        commit("SET_CREATING_FROM", payload);
    },
    setNewEvent({ commit, state }, payload) {
        commit("SET_NEW_EVENT", payload);
        commit("SET_ACTIVE_EVENT", payload);
    },

    setActiveEvent({ commit, state }, payload) {
        commit("SET_ACTIVE_EVENT", payload);
    },
    async setProfilesThatCanBeInvited({ commit, state }, payload) {
        payload.forEach(element => {
            commit("SET_PROFILES_THAT_CAN_BE_INVITED", element);
        });
    },

    updateEventRequest({ commit, state }) {
        commit("UPDATE_EVENT_REQUEST");
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
