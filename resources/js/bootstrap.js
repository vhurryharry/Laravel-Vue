window._ = require("lodash");

try {
    window.$ = window.jQuery = require("jquery");
} catch (e) {}

window.axios = require("axios");

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common["X-CSRF-TOKEN"] = token.content;
} else {
    console.error(
        "CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token"
    );
}



import Echo from "laravel-echo";

window.Pusher = require("pusher-js");

window.Echo = new Echo({
    broadcaster: "pusher",
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: "eu",
    forceTLS: process.env.MIX_PUSHER_APP_TLS,
    encrypt: process.env.MIX_PUSHER_APP_ENCRYPT,
    authHost: process.env.MIX_PUSHER_APP_HOST,
    host: process.env.MIX_PUSHER_APP_HOST,
    port: "6001",
    auth: {
        headers: {
            Authorization:
                "Bearer " +
                document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content")
        }
    }
});

import "es6-promise/auto";
