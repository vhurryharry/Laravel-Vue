import Vue from "vue";
import Vuex from "vuex";

import ui from "./ui";
import profiles from "./profiles";
import events from "./events";
import communities from "./communities";
import search from "./search";
import conversations from "./conversations";
import conferencing from "./conferencing";
import reports from "./reports";

Vue.use(Vuex);

export default new Vuex.Store({
    modules: {
        ui,
        profiles,
        events,
        communities,
        search,
        conversations,
        conferencing,
        reports
    },
    strict: process.env.NODE_ENV !== "production"
});
