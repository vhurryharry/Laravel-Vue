require("./bootstrap");

import Vue from "vue";
window.Vue = Vue;

import store from "./store";

import Buefy from "buefy";

Vue.use(Buefy);

import moment from "moment-timezone";
import "moment/locale/ar";

Vue.prototype.moment = moment;

import Toasted from "vue-toasted";
Vue.use(Toasted);

import Croppa from "vue-croppa";
Vue.use(Croppa);

import vuexI18n from "vuex-i18n";
Vue.use(vuexI18n.plugin, store);

import Locales from "./vue-i18n-locales.generated.js";
Vue.i18n.add("en", Locales.en);
Vue.i18n.add("ar", Locales.ar);

import AudioRecorder from "vue-audio-recorder";
Vue.use(AudioRecorder);

// import Loading from './components/utils/Loading';

import VeeValidate from "vee-validate";

Vue.use(VeeValidate);

import VTooltip from "v-tooltip";
Vue.use(VTooltip);

import Popper from "vue-popperjs";
Vue.component("popper", Popper);

// import { VueReCaptcha } from "vue-recaptcha-v3";
// Vue.use(VueReCaptcha, {
//     siteKey: '6LeiPOEUAAAAAAck5_U8O5pF--D3NKWTI7tKk9E8',
//     loaderOptions: {
//         useRecaptchaNet: true
//     }
// });

var SocialSharing = require("vue-social-sharing");

Vue.use(SocialSharing);

import { library } from "@fortawesome/fontawesome-svg-core";

import {
    faJs,
    faVuejs,
    faGooglePlus,
    faFacebook,
    faLine,
    faLinkedin,
    faReddit,
    faSkype,
    faTelegram,
    faTwitter,
    faWhatsapp,
    faPinterest
} from "@fortawesome/free-brands-svg-icons";

import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import {
    faEnvelope,
    faUserSecret,
    faAngleDown,
    faPlusCircle,
    faUserPlus,
    faStar,
    faBell,
    faBan,
    faCalendar,
    faComment,
    faCheck,
    faCircle,
    faTimes,
    faMugHot,
    faEllipsisV,
    faChevronDown,
    faChevronUp,
    faChevronLeft,
    faChevronRight,
    faChevronCircleDown,
    faChevronCircleUp,
    faChevronCircleLeft,
    faChevronCircleRight,
    faFlag,
    faHeart,
    faMicrophone,
    faTasks,
    faClipboard,
    faPen,
    faPlus,
    faQuestion,
    faQuestionCircle,
    faSearch,
    faCog,
    faCogs,
    faShare,
    faSmileWink,
    faSmileBeam,
    faSmile,
    faGrinHearts,
    faGrinAlt,
    faTrashAlt,
    faCameraRetro,
    faUsers,
    faCampground,
    faCalendarDay,
    faGhost,
    faExclamation,
    faExclamationCircle,
    faExclamationTriangle,
    faInfo,
    faInfoCircle,
    faUserClock,
    faExchangeAlt,
    faVolumeOff,
    faVolumeUp,
    faVolumeDown,
    faVideo,
    faStop,
    faVideoSlash
} from "@fortawesome/free-solid-svg-icons";

library.add(
    faJs,
    faVuejs,
    faUserSecret,
    faAngleDown,
    faPlusCircle,
    faUserPlus,
    faStar,
    faBell,
    faBan,
    faCalendar,
    faComment,
    faCheck,
    faCircle,
    faTimes,
    faMugHot,
    faEllipsisV,
    faChevronDown,
    faChevronUp,
    faChevronLeft,
    faChevronRight,
    faChevronCircleDown,
    faChevronCircleUp,
    faChevronCircleLeft,
    faChevronCircleRight,
    faFlag,
    faHeart,
    faMicrophone,
    faTasks,
    faClipboard,
    faPen,
    faPlus,
    faQuestion,
    faQuestionCircle,
    faSearch,
    faCog,
    faCogs,
    faShare,
    faSmileWink,
    faSmileBeam,
    faSmile,
    faGrinHearts,
    faGrinAlt,
    faTrashAlt,
    faCameraRetro,
    faUsers,
    faCampground,
    faCalendarDay,
    faGhost,
    faExclamation,
    faExclamationCircle,
    faExclamationTriangle,
    faInfo,
    faInfoCircle,
    faUserClock,
    faExchangeAlt,
    faVolumeOff,
    faVolumeUp,
    faVolumeDown,
    faVideo,
    faStop,
    faVideoSlash,
    faGooglePlus,
    faFacebook,
    faLine,
    faLinkedin,
    faReddit,
    faSkype,
    faTelegram,
    faTwitter,
    faWhatsapp,
    faPinterest,
    faEnvelope
);

Vue.component("font-awesome-icon", FontAwesomeIcon);

Vue.component(
    "header-component",
    require("./components/HeaderComponent.vue").default
);
Vue.component("footer-component", require("./components/Footer.vue").default);
Vue.component("home-footer", require("./components/HomeFooter.vue").default);

Vue.component("home", require("./pages/Home.vue").default);
Vue.component("news", require("./pages/News.vue").default);

Vue.component("events", require("./pages/Events.vue").default);
Vue.component("event", require("./pages/Event.vue").default);

Vue.component("communities", require("./pages/Communities.vue").default);
Vue.component("community", require("./pages/Community.vue").default);

Vue.component("friends", require("./pages/Friends.vue").default);
Vue.component(
    "individual-profile",
    require("./pages/IndividualProfile.vue").default
);

Vue.component("contact-us", require("./pages/ContactUs.vue").default);
Vue.component("privacy-policy", require("./pages/PrivacyPolicy.vue").default);
Vue.component(
    "terms-conditions",
    require("./pages/TermsConditions.vue").default
);
Vue.component("settings", require("./pages/Settings.vue").default);

Vue.component("search", require("./pages/Search.vue").default);

Vue.component("chat", require("./pages/Chat.vue").default);

Vue.component("hallway", require("./pages/Hallway.vue").default);
Vue.component("event-hallway", require("./pages/EventHallway.vue").default);

Vue.component(
    "registration-component",
    require("./components/RegistrationComponent.vue").default
);
Vue.component("modal", require("./components/utils/Modal.vue").default);

new Vue({
    el: "#app",
    store,
    mounted: function() {}
});
