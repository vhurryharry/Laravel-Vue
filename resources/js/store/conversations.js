import uuidv4 from "uuid/v4";

const state = {
    conversations: {},
    activeConversation: null,
    eventsConversations: {},
    talkingTo: null,
    messages: null,
    latestMessage: null
};

const mutations = {
    CLEAR_CONVERSATIONS(state, payload) {
        state.conversations = null;
    },
    SET_ACTIVE_CONVERSATION(state, payload) {
        state.activeConversation = payload;
    },
    SET_CONVERSATIONS(state, payload) {
        payload.conversation.members.forEach(element => {
            if (element.id !== payload.rootState.profiles.signedInProfile.id) {
                payload.conversation.talking_to = element;
            }
        });

        state.conversations = {
            ...state.conversations,
            [payload.conversation.id]: payload.conversation
        };
    },
    SET_CONVERSATION(state, payload) {
        payload.conversation.members.forEach(element => {
            if (element.id !== payload.rootState.profiles.signedInProfile.id) {
                payload.conversation.talking_to = element;
            }
        });

        state.conversations = {
            ...state.conversations,
            [payload.conversation.id]: payload.conversation
        };
    },
    UPDATE_ACTIVE_CONVERSATION_UPDATED_AT(state, payload){
        state.activeConversation.updated_at = payload.updated_at;
    },
    SET_SIGNED_IN_USER(state, payload) {
        state.signedInProfile = payload;
    },
    SET_TALKING_TO(state, payload) {
        state.talkingTo = payload;
    },
    SET_MESSAGES(state, payload) {
        state.messages = {
            ...state.messages,
            [payload.uid]: payload
        };
    },
    SET_MESSAGE(state, payload) {
        state.messages = {
            ...state.messages,
            [payload.uid]: payload
        };
    },
    CLEAR_MESSAGES(state, payload) {
        state.messages = null;
    },
    SET_LATEST_MESSAGE(state, payload) {
        state.latestMessage = payload;
    },
    CLEAR_UNREAD_FOR_ACTIVE_CONVERSATION(state, payload) {
        state.activeConversation.unread = null;
    }
};

const actions = {
    async getConversation({ commit, state, dispatch }, payload) {
        await axios
            .post("/event/" + payload.event_slug + "/conversation/get", payload)
            .then(response => {
                commit("SET_ACTIVE_CONVERSATION", response.data.conversation);
                dispatch(
                    "conversations/getMessages",
                    {
                        conversation_id: payload.event_id,
                        event_slug: payload.event_slug,
                        conversation: state.activeConversation,
                        count: 5,
                        type: "event"
                    },
                    { root: true }
                );
            });
    },
    async getConversations({ commit, rootState }, payload) {
        await axios
            .post(
                "/profile/" +
                    rootState.profiles.signedInProfile.username +
                    "/chat/get",
                payload
            )
            .then(response => {
                commit("CLEAR_CONVERSATIONS");
                commit("CLEAR_MESSAGES");

                response.data.conversations.forEach(element => {
                    commit("SET_CONVERSATIONS", {
                        conversation: element,
                        rootState: rootState
                    });
                });
            });
    },
    async startConversation({ commit, state }, payload) {
        await axios
            .post(
                "/profile/" + payload.username + "/chat/createConversation",
                payload
            )
            .then(response => {
                return true;
            });
    },
    clearUnreadForActiveConversation({ commit, rootState, dispatch }, payload) {
        commit("CLEAR_UNREAD_FOR_ACTIVE_CONVERSATION");
    },
    setActiveConversation({ commit, rootState, dispatch }, payload) {
        commit("SET_ACTIVE_CONVERSATION", payload.conversation);
        if (payload.type === "profiles") {
            dispatch("conversations/getMessages", payload, { root: true });

            payload.conversation.members.forEach(element => {
                if (element.id !== rootState.profiles.signedInProfile.id) {
                    commit("SET_TALKING_TO", element);
                }
            });
        } else {
            commit("CLEAR_MESSAGES");

            commit("SET_TALKING_TO", payload.conversation);
        }
    },
    async getMessages({ commit, rootState, dispatch }, payload) {
        let messages = null;
        await axios
            .post(
                "/profile/" +
                    rootState.profiles.signedInProfile.username +
                    "/chat/" +
                    payload.conversation.id,
                payload
            )
            .then(response => {
                messages = Object.keys(response.data.messages).length;
                if (Object.keys(response.data.messages).length === 0) {
                    commit("CLEAR_MESSAGES");
                } else {
                    commit("CLEAR_MESSAGES");

                    _.forEach(response.data.messages, function(value, key) {
                        commit("SET_MESSAGES", value);
                    });
                }
            });
        return messages;
    },
    async sendMessage({ commit, rootState, dispatch }, payload) {
        let message = {
            uid: uuidv4(),
            body: payload.message,
            file: payload.file,
            type: payload.type,
            sender: rootState.profiles.signedInProfile,
            delivered: false,
            created_at: Date.now()
        };

        dispatch("conversations/addMessage", message, { root: true });

        if (payload.to) {
            await axios
                .patch(
                    "/event/" +
                        payload.event_slug +
                        "/chat/" +
                        payload.to.id +
                        "/send",
                    {
                        conversation:
                            rootState.conversations.activeConversation,
                        message: message,
                        type: payload.type
                    }
                )
                .then(response => {
                    // console.log(response.data.message);
                    dispatch(
                        "conversations/addMessage",
                        response.data.message,
                        { root: true }
                    );
                });
        } else {
            await axios
                .patch(
                    "/profile/" +
                        rootState.profiles.signedInProfile.username +
                        "/chat/" +
                        rootState.conversations.activeConversation.id +
                        "/send",
                    {
                        conversation:
                            rootState.conversations.activeConversation,
                        message: message,
                        recipient: rootState.conversations.talkingTo,
                        type: payload.type
                    }
                )
                .then(response => {
                    dispatch(
                        "conversations/addMessage",
                        response.data.message,
                        { root: true }
                    );
                });
        }
    },
    addConversation({ commit, rootState }, payload) {
        commit("SET_CONVERSATION", {
            conversation: payload,
            rootState: rootState
        });
    },
    addMessage({ commit, rootState }, payload) {
        commit("SET_MESSAGE", payload);
    },

    markConversationAsRead({ commit, rootState, dispatch }, payload) {
        axios
            .post(
                "/profile/" +
                    rootState.profiles.signedInProfile.username +
                    "/chat/" +
                    rootState.conversations.activeConversation.id +
                    "/markAsRead",
                {
                    profile: rootState.profiles.signedInProfile,
                    conversation: rootState.conversations.activeConversation
                }
            )
            .then(response => {
                console.log(response.data.conversation);

                commit("UPDATE_ACTIVE_CONVERSATION_UPDATED_AT",response.data.conversation);

            // dispatch("conversations/getMessages", response.data, { root: true });


                // dispatch(
                //     "conversations/getMessages",
                //     {
                //         conversation_id: response.data.conversation.event_id,
                //         event_slug: response.data.conversation.event_slug,
                //         conversation: state.activeConversation,
                //         count: 5,
                //         type: "event"
                //     },
                //     { root: true }
                // );

                dispatch(
                    "conversations/clearUnreadForActiveConversation",
                    response,
                    { root: true }
                );
            });
    },
    setLatestMessage({ commit, state }, payload) {
        commit("SET_LATEST_MESSAGE", payload);
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
