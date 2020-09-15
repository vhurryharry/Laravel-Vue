<template>
  <form class="flex-col pt-6 pb-2 my-2" @submit.prevent="register()">
    <div class="flex justify-around mb-4">
      <div class="flex flex-col">
        <label
          class="block text-sm mb-1 rubik-regular"
          for="firstName"
        >{{ $t('components.register.first_name')}}</label>

        <input
          class="appearance-none border rounded w-full py-2 px-3 text-grey-darker bg-white"
          name="firstname"
          type="text"
          v-model="user.firstname"
          v-validate="'required'"
          :class="{'is-danger': errors.has('firstname') }"
          minlength="1"
        />
        <span
          v-show="errors.has('firstname')"
          class="help is-danger inline h-1 w-1 whitespace-no-wrap"
        >{{ errors.first('firstname') }}</span>
      </div>
      <div class="flex flex-col">
        <label
          class="block text-sm mb-1 rubik-regular"
          for="lastName"
        >{{ $t('components.register.last_name')}}</label>

        <input
          class="appearance-none border rounded w-full py-2 px-3 text-grey-darker bg-white"
          v-validate="'required'"
          :class="{'is-danger': errors.has('lastname') }"
          id="lastName"
          name="lastname"
          type="text"
          v-model="user.lastname"
          minlength="1"
          required
        />
        <span
          v-show="errors.has('lastname')"
          class="help is-danger inline h-1 w-1 whitespace-no-wrap"
        >{{ errors.first('lastname') }}</span>
      </div>
    </div>

    <div class="flex justify-center items-end mb-4">
      <date-picker v-on:input="setDateOfBirth($event)"></date-picker>
    </div>

    <div class="flex flex-col mb-4 w-2/3 block mx-auto">
      <label
        class="block text-sm mb-1 rubik-regular"
        for="email"
      >{{ $t('components.register.email')}}</label>

      <input
        class="appearance-none border rounded w-full py-2 px-3 text-grey-darker bg-white"
        id="email"
        type="email"
        name="email"
        v-model="user.email"
        v-validate="'required|email'"
        :class="{'is-danger': errors.has('email') }"
        required
      />
      
      <span
        v-show="errors.has('email')"
        class="help is-danger inline h-1 w-1 whitespace-no-wrap"
      >{{ errors.first('email') }}</span>
    </div>

    <div class="flex justify-around mb-6">
      <div class="flex flex-col">
        <label
          class="block text-sm mb-1 rubik-regular"
          for="password"
        >{{ $t('components.register.password')}}</label>
        <input
          class="appearance-none border rounded w-full py-2 px-3 text-grey-darker bg-white"
          id="password"
          type="password"
          name="password"
          v-model="user.password"
          required
          v-validate="'required|min:8'"
          :class="{'is-danger': errors.has('password') }"
        />
        <span
          v-show="errors.has('password')"
          class="help is-danger inline h-1 w-1 whitespace-no-wrap"
        >{{ errors.first('password') }}</span>
      </div>
      <div class="flex flex-col">
        <label
          class="block text-sm mb-1 rubik-regular"
          for="confirmPassword"
        >{{ $t('components.register.confirm_password')}}</label>
        <input
          class="appearance-none border rounded w-full py-2 px-3 text-grey-darker bg-white"
          id="confirmPassword"
          type="password"
          name="password_confirmation"
          v-model="user.password_confirmation"
          required
          v-validate="'required|min:8'"
          :class="{'is-danger': errors.has('password_confirmation') }"
        />
        <span
          v-show="errors.has('password_confirmation')"
          class="help is-danger inline h-1 w-1 whitespace-no-wrap"
        >{{ errors.first('password_confirmation') }}</span>
      </div>
    </div>

    <div class="flex items-center">
      <a class="mx-auto" href="#">
        <button
          class="flex block raleway-semibold hover:bg-white hover:text-teal text-white border-white border py-2 px-6 rounded"
          type="button"
          @click="register()"
        >
          {{ $t('components.register.next')}}
          <div class="block flex ml-5 spinner" v-if="loading"></div>
        </button>
      </a>
    </div>
  </form>
</template>


<script>
import vSelect from "vue-select";
import DatePicker from "./../../components/utils/DatePicker.vue";
import { mapState, mapMutations, mapActions } from "vuex";

export default {
  computed: {
    ...mapState({
      language: state => state.i18n.locale
    })
  },
  data() {
    return {
      user: {
        firstname: null,
        lastname: null,
        email: null,
        password: null,
        password_confirmation: null,
        date_of_birth: null
      },
      // errors: {
      //   firstname: [],
      //   lastname: [],
      //   email: [],
      //   password: [],
      //   password_confirmation: [],
      //   date_of_birth: []
      // },
      day: null,
      month: null,
      year: null,
      loading: false
    };
  },
  created() {
    // console.log(this.$t('components.register.birthday'));
  },

  methods: {
    setDateOfBirth(value) {
      this.user.date_of_birth = value;
    },
    resetUser() {},
    register() {
      this.loading = true;

      this.$validator.validateAll().then(async result => {
        if (result) {
          let user = await axios
            .post("/user/register", this.user)
            .then(response => {
              this.loading = false;

              //   console.log(response.data.data.username);
              this.$emit("initialization:close", response.data.user.username);
            })
            .catch(error => {
              this.loading = false;

              this.$toast.open({
                duration: 5000,
                message: error.response.data.errors.password
                  ? error.response.data.errors.password[0]
                  : error.response.data.errors.email
                  ? error.response.data.errors.email[0]
                  : error.response.data.message,
                position: "is-bottom",
                type: "is-warning"
              });
            });
        }
      });
      this.loading = false;
    },
    close() {
      this.$emit("initialization:close");
    }
  },

  components: {
    vSelect,
    DatePicker
  }
};
</script>


<style lang="scss" scoped>
</style>
