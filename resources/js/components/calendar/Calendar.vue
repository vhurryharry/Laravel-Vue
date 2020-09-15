<template>
  <FullCalendar
    defaultView="dayGridMonth"
    :plugins="calendarPlugins"
    :editable="true"
    @dateClick="handleDateClick"
    :weekends="true"
    :header="{
        left: 'title',
        right: 'prev today next',
      }"
    :events="localEvents"
    :eventLimit="true"
    :views="{
    timeGrid: {
      eventLimit: 2 // adjust to 6 only for timeGridWeek/timeGridDay
    }
  }"
  />
</template>

<script>
import FullCalendar from "@fullcalendar/vue";
import dayGridPlugin from "@fullcalendar/daygrid";
import interactionPlugin from "@fullcalendar/interaction";
import { mapState, mapMutations, mapActions } from "vuex";

export default {
  components: {
    FullCalendar // make the <FullCalendar> tag available
  },
  computed: {
    ...mapState({
      profile: state => state.profiles.signedInProfile,
      events: state => state.events.events,
      language: state => state.i18n.locale
    })
    // stubbedLocalEvents(value){
    //   console.log(value.data);
    //   value.forEach(element => {
    //       element.date = element.start_date;
    //   });
    // }
  },
  created() {
    // this.isLoading = true;

    this.$store.dispatch("events/getEvents").then(response => {
      _.forEach(this.events, element => {
        this.localEvents.push(element);
      });
    });

    // this.events.forEach(element => {
    //   this.localEvents = this.events;
    // });

    // this.$store.dispatch("events/getEventsHappeningNow");
  },
  watch: {
    // localEvents(value) {
    // value.date = value.start_date;
    // }
  },
  data() {
    return {
      calendarPlugins: [dayGridPlugin, interactionPlugin],
      localEvents: []
    };
  },
  methods: {
    handleDateClick(info) {
      // alert("Clicked on: " + info.dateStr);
      // alert("Coordinates: " + info.jsEvent.pageX + "," + info.jsEvent.pageY);
      // alert("Current view: " + info.view.type);
      // change the day's background color just for fun
      // info.dayEl.style.backgroundColor = "red";
    },
    getEvents() {}
  }
};
</script>

<style lang='scss'>
@import "./../../../sass/calendar.scss";
// @import "/daygrid/main.css";
</style>