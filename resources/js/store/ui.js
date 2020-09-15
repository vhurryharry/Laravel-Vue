import uuidv4 from "uuid/v4";

const state = {
    activeModal: null,
    contentOverlay: "home",
    contentOpen: true,
    shareModalOpen: false,
    shareModalResource: null,
    shareResourceType: null,
    toast: "",
    isLoginModalActive: false,
    homeImages: {},
    notifications: {},
    notification: {},
    language: "en",
    accessedProfile: null,
    reportOpen: false,
    inviteToResource: null,
    inviteFrom: "create",
    createEventFromCommunity: false
};

const mutations = {
    SET_ACTIVE_MODAL(state, payload) {
        state.activeModal = payload;
    },
    SET_INVITE_TO_RESOURCE(state, payload) {
        state.inviteToResource = payload;
    },
    SET_CREATE_EVENT_FROM_COMMUNITY(state, payload) {
        state.createEventFromCommunity = payload;
    },
    SET_ACCESSED_PROFILE(state, payload) {
        state.accessedProfile = payload;
    },
    SET_CONTENT_VIEW(state, payload) {
        state.contentOpen = true;
        state.contentOverlay = payload.view;
    },
    SET_INVITE_FROM(state, payload) {
        state.inviteFrom = payload;
    },

    SET_REPORT_VIEW(state, payload) {
        state.reportOpen = payload;
    },

    SET_TOAST(state, payload) {
        state.toast = payload;
    },
    SET_NOTIFICATION(state, payload) {
        state.toast = payload;
    },
    SET_NOTIFICATIONS(state, payload) {
        state.notifications = {
            ...state.notifications,
            [uuidv4()]: payload
        };
    },
    SET_LOGIN_MODAL(state, payload) {
        state.isLoginModalActive = payload;
    },
    SET_HOME_IMAGES(state, payload) {
        state.homeImages = {
            ...state.homeImages,
            [payload.id]: payload
        };
    },
    DELETE_HOME_IMAGE(state, payload) {
        Vue.delete(state.homeImages, payload.id);
    },
    SET_LANGUAGE(state, payload) {
        state.language = payload;
    },
    OPEN_SHARE_MODAL(state, payload) {
        state.shareModalOpen = true;
        state.shareModalResource = payload;

        if (payload.event_privacy) {
            state.shareResourceType = "events";
        } else {
            state.shareResourceType = "communities";
        }
    },
    CLOSE_SHARE_MODAL(state) {
        state.shareModalOpen = false;
        state.shareModalResource = null;
    }
};

const actions = {
    setActiveModal({ commit, state }, payload) {
        commit("SET_ACTIVE_MODAL", payload);
    },
    setAccessedProfile({ commit, state }, payload) {
        commit("SET_ACCESSED_PROFILE", payload);
    },
    setReportView({ commit, state }, payload) {
        commit("SET_REPORT_VIEW", payload);
    },
    setLanguage({ commit, state }, payload) {
        commit("SET_LANGUAGE", payload);
    },

    setInviteFrom({ commit, state }, payload) {
        commit("SET_INVITE_FROM", payload);
    },
    setCreateEventFromCommunity({ commit, state }, payload) {
        commit("SET_CREATE_EVENT_FROM_COMMUNITY", payload);
    },
    setInviteToResource({ commit, state }, payload) {
        commit("SET_INVITE_TO_RESOURCE", payload);
    },
    setNotifications({ commit, state }, payload) {
        commit("SET_NOTIFICATIONS", payload);
    },
    setToast({ commit, state }, payload) {
        commit("SET_TOAST", payload);
    },

    setContentView({ commit, state }, payload) {
        commit("SET_CONTENT_VIEW", payload);
    },

    setLoginModal({ commit, state }, payload) {
        commit("SET_LOGIN_MODAL", payload);
    },
    async getHomeImages({ commit, rootState }, payload) {
        let ref = rootState.db.collection("home");

        await ref.onSnapshot(images => {
            images.forEach(function(image) {
                if (image && image.data()) {
                    commit("SET_HOME_IMAGES", image.data());
                }
            });
        });
    },

    async addHomePhoto({ commit, rootState }, payload) {
        let imageName = uuidv4();
        let ImagesRef = rootState.storage
            .ref()
            .child("home/" + imageName + ".jpg");

        await ImagesRef.putString(payload.url, "data_url").then(function(
            snapshot
        ) {
            ImagesRef.getDownloadURL().then(function(url) {
                let ref = rootState.db.collection("home");
                let scopedId = uuidv4();

                let image = {
                    id: scopedId,
                    dataUrl: url
                };

                ref.doc(scopedId)
                    .set(image)
                    .then(function() {
                        commit("SET_HOME_IMAGES", image);
                    });
            });
        });
    },

    async deleteHomeImage({ commit, rootState }, payload) {
        let user = rootState.auth.currentUser;

        await rootState.db
            .collection("home")
            .doc(payload.id)
            .delete()
            .then(function() {
                commit("DELETE_HOME_IMAGE", payload);
            });
    },

    openShareModal({ commit, state }, payload) {
        commit("OPEN_SHARE_MODAL", payload);
    },
    closeShareModal({ commit, state }, payload) {
        commit("CLOSE_SHARE_MODAL");
    }
};

const getters = {
    toast(state) {
        return state.toast;
    }
};

export default {
    namespaced: true,
    state,
    mutations,
    actions,
    getters
};
