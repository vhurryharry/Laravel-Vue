<template>
  <div class="flex w-full justify-center items-end">
    <div class>
      <div
        v-if="showLabels !== 'false'"
        class="block text-sm mb-1 rubik-regular"
      >{{ $t('components.settings.birthday') }}</div>

      <b-select v-model="day" class @blur="onBlur" @focus="onFocus">
        <option
          v-if="placeholders[0]"
          value="null"
          disabled="disabled"
        >{{ $t('components.settings.day') }}</option>
        <option v-for="(item, index) in new Array(28)" :value="index + 1">{{ index + 1 }}</option>
        <option value="29" v-if="daysInMonth >= 29 || isLeapYear">29</option>
        <option value="30" v-if="daysInMonth >= 30">30</option>
        <option value="31" v-if="daysInMonth >= 31">31</option>
      </b-select>
    </div>

    <div class="mx-6">
      <b-select v-model="month" class @blur="onBlur" @focus="onFocus">
        <option
          v-if="placeholders[1]"
          value="null"
          disabled="disabled"
        >{{ $t('components.settings.month') }}</option>
        <option v-for="(item, index) in new Array(12)" :value="index">{{ getDisplayedMonth(index) }}</option>
      </b-select>
    </div>

    <div class>
      <b-select v-model="year" class @blur="onBlur" @focus="onFocus">
        <option
          v-if="placeholders[2]"
          value="null"
          disabled="disabled"
        >{{ $t('components.settings.year') }}</option>
        <option
          v-for="(item, index) in new Array(100)"
          :value="currentYear - index"
        >{{ currentYear - index }}</option>
      </b-select>
    </div>
  </div>
</template>

<script>
const datesInMonths = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

const monthNames = moment.months();

import moment from "moment";
import { mapState, mapMutations, mapActions } from "vuex";
import Multiselect from "vue-multiselect";

export default {
  name: "vue-dob-picker",
  props: {
    value: {
      type: Date
    },
    selectClass: String,
    selectPlaceholderClass: String,
    labelClass: String,
    showLabels: String,
    locale: {
      type: String,
      default: navigator.language
    },
    monthFormat: {
      type: String,
      default: "long"
    },
    proportions: {
      type: Array,
      default: () => [1, "January", 2000]
    },
    labels: {
      type: Array,
      default: () => ["Birthday", "Month", "Year"]
    },
    placeholders: {
      type: Array,
      default: () => ["01", "January", "2000"]
    }
  },
  data() {
    return {
      day: null,
      month: null,
      year: null,
      currentYear: new Date().getFullYear(),
      blurTimeout: null
    };
  },
  computed: {
    ...mapState({
      language: state => state.i18n.locale
    }),
    date: {
      get() {
        let daysInMonth = this.daysInMonth;
        if (this.isLeapYear && this.month === 1) {
          daysInMonth += 1;
        }
        if (this.day > daysInMonth) {
          this.day = null;
        }
        if (this.day !== null && this.month !== null && this.year !== null) {
          return new Date(
            this.year,
            this.month,
            this.day,
            false,
            false,
            false,
            false
          );
        }
        return null;
      },
      set(val) {
        if (val) {
          this.day = val.getDate();
          this.month = val.getMonth();
          this.year = val.getFullYear();
        }
      }
    },
    daysInMonth() {
      return datesInMonths[this.month] || 31;
    },
    isLeapYear() {
      return (
        (this.year % 4 === 0 && this.year % 100 !== 0) || this.year % 400 === 0
      );
    },
    dayClass() {
      if (this.selectPlaceholderClass && this.day === null) {
        return this.selectPlaceholderClass;
      }
      return this.selectClass;
    },
    monthClass() {
      if (this.selectPlaceholderClass && this.month === null) {
        return this.selectPlaceholderClass;
      }
      return this.selectClass;
    },
    yearClass() {
      if (this.selectPlaceholderClass && this.year === null) {
        return this.selectPlaceholderClass;
      }
      return this.selectClass;
    }
  },
  watch: {
    date() {
      if (this.date) {
        let date = moment(this.date).format();

        let timestamp = moment(date)
          .locale("en")
          .format("X");

        this.$emit("input", timestamp);
      }
    }
  },
  methods: {
    getDisplayedMonth(monthNum) {
      const monthDateObj = new Date(2000, monthNum, 1);
      const locale = this.language || navigator.language;
      return monthDateObj.toLocaleString(locale, { month: this.monthFormat });
    },
    onBlur() {
      this.blurTimeout = window.setTimeout(() => {
        this.$emit("blur");
      }, 50);
    },
    onFocus() {
      window.clearTimeout(this.blurTimeout);
    }
  },
  created() {
    this.date = this.value;
  },
  components: {
    moment,
    Multiselect
  }
};
</script>

<style scoped>
</style>
