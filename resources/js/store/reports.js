const state = {
    reportedResource: null,
    reportedResourceType: null
};

const mutations = {
    SET_REPORTED_RESOURCE(state, payload) {
        state.reportedResource = payload;
    },
    SET_REPORTED_RESOURCE_TYPE(state, payload) {
        state.reportedResourceType = payload;
    }
};

const actions = {
    setReportedResource({ commit, state }, payload) {
        commit("SET_REPORTED_RESOURCE", payload);
    },
    setReportedResourceType({ commit, state }, payload) {
        commit("SET_REPORTED_RESOURCE_TYPE", payload);
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
