<template>
  <div class="relative"  :class="{ calling: isLoading }">
    <button  @click="toggleEventForm" :disabled="eventModalForm" class="bg-indigo-600 text-white font-medium py-2 px-4 rounded mb-8">Agregar Evento</button>
    <Spinner v-if="isLoading" size="w-8 h-8  absolute right-0 mt-2 mr-4" />
    <div v-if="message" class="notification is-success">{{ message }}</div>
    <div class="event-calendar-wrapper bg-white shadow-md rounded mb-16">
      <calendar-view
        :events="items"
        :show-date="showDate"
        :time-format-options="{ hour: 'numeric', minute: '2-digit' }"
        :enable-drag-drop="true"
        :disable-past="disablePast"
        :disable-future="disableFuture"
        :show-event-times="showEventTimes"
        :display-period-uom="displayPeriodUom"
        :display-period-count="displayPeriodCount"
        :starting-day-of-week="startingDayOfWeek"
        :class="themeClasses"
        :period-changed-callback="periodChanged"
        :current-period-label="useTodayIcons ? 'icons' : ''"
        locale="ES-es"
        @drop-on-date="onDrop"
        @click-date="onClickDay"
        @click-event="onClickItem"
      >
        <calendar-view-header
          slot="header"
          slot-scope="t"
          :header-props="t.headerProps"
          @input="setShowDate"
        />
      </calendar-view>
    </div>
    <EventForm :show="eventModalForm" :event="event" @delete="deleteEvent" @closeForm="toggleEventForm" @saveEvent="clickTestAddItem"/>
  </div>
</template>

<script>
import { mapState, mapActions } from "vuex";
import { CalendarView, CalendarViewHeader, CalendarMathMixin } from "vue-simple-calendar";
// The next two lines are processed by webpack. If you're using the component without webpack compilation,
// you should just create <link> elements for these. Both are optional, you can create your own theme if you prefer.
require("vue-simple-calendar/static/css/default.css");
require("vue-simple-calendar/static/css/holidays-us.css");
import EventForm from './eventForm.vue'
import Spinner from "./../spinner.vue";

const eventTemplate = {
          startDate: null,
          endDate: null,
          startTime: null,
          endTime: null,
          title: '',
          classes: '',
          url: '',
      }

export default {
  components: {
    CalendarView,
    CalendarViewHeader,
    EventForm,
    Spinner
  },
  mixins: [CalendarMathMixin],
  data() {
    return {
      eventModalForm: false,
      /* Show the current month, and give it some fake items to show */
      showDate: this.thisMonth(1),
      message: "",
      startingDayOfWeek: 0,
      disablePast: false,
      disableFuture: false,
      displayPeriodUom: "month",
      displayPeriodCount: 1,
      showEventTimes: true,
      useDefaultTheme: true,
      useHolidayTheme: true,
      useTodayIcons: false,
      items: [
      ],
      event: {
          ...eventTemplate
      },
      isLoading: true,
    };
  },
  computed: {
    userLocale() {
      return this.getDefaultBrowserLocale;
    },
    dayNames() {
      return this.getFormattedWeekdayNames(this.userLocale, "long", 0);
    },
    themeClasses() {
      return {
        "theme-default": this.useDefaultTheme,
      };
    },
  },
  mounted() {
    this.event.startDate = this.isoYearMonthDay(this.today());
    this.event.endDate = this.isoYearMonthDay(this.today());
    this.loadEvents();
  },
  methods: {
     ...mapActions([
      'showNotification',
    ]),
    periodChanged() {
      // range, eventSource) {
      // Demo does nothing with this information, just including the method to demonstrate how
      // you can listen for changes to the displayed range and react to them (by loading items, etc.)
      //console.log(eventSource)
      //console.log(range)
    },
    loadEvents() {
      this.isLoading = true;
      axios.get('/calevents')
        .then((response) => {
          this.items = response.data.events
          this.isLoading = false;
        })
        .catch((error) => {
          console.log(error)
        })
    },
    toggleEventForm() {
      if(this.eventModalForm) {
        this.resetEvent();
      }
      this.eventModalForm = !this.eventModalForm;
    },
    thisMonth(d, h, m) {
      const t = new Date();
      return new Date(t.getFullYear(), t.getMonth(), d, h || 0, m || 0);
    },
    formatForStorage(dateString, time) {
      return`${dateString} ${time ? time+':00' : '00:00:00'}`;
        
    },
    formatDate(date) {
      let dd = date.getDate(),
          mm = date.getMonth()+1,
          yyyy = date.getFullYear();
      if(dd<10) 
      {
          dd='0'+dd;
      } 

      if(mm<10) 
      {
          mm='0'+mm;
      } 
      return yyyy+'-'+mm+'-'+dd
    },
    getFormatHours(date) {
      let hh = date.getHours(),
          mm = date.getMinutes();
      if(hh<10) 
      {
          hh='0'+hh;
      } 

      if(mm<10) 
      {
          mm='0'+mm;
      } 
      return hh + ":" + mm
    },
    onClickDay(date) {
      this.event.startDate = this.formatDate(date);
      this.event.endDate = this.formatDate(date);
      this.eventModalForm = true;
    },
    onClickItem(clickedEvent) {
      const { originalEvent } = clickedEvent,
            newStart = new Date(originalEvent.startDate),
            newEnd = new Date(originalEvent.endDate);
      this.event = {
        ...originalEvent,
        startDate: this.formatDate(newStart),
        endDate: originalEvent.endDate ? this.formatDate(newEnd) : null,
        startTime: originalEvent.startDate ? this.getFormatHours(newStart) : null,
        endTime: originalEvent.endDate ? this.getFormatHours(newEnd)  : null,
      };
      this.eventModalForm = true;
    },
    setShowDate(d) {
      // this.message = `Changing calendar view to ${d.toLocaleDateString()}`;
      this.showDate = d;
    },
    onDrop(item, date) {
      // Determine the delta between the old start date and the date chosen,
      // and apply that delta to both the start and end date to move the item.
      const eLength = this.dayDiff(item.startDate, date);
      item.originalEvent.startDate = this.addDays(item.startDate, eLength);
      item.originalEvent.endDate = this.addDays(item.endDate, eLength);
      this.event = {
        ...item.originalEvent,
        startDate:this.formatForStorage(this.formatDate(item.originalEvent.startDate), this.getFormatHours(item.originalEvent.startDate)),
        endDate:this.formatForStorage(this.formatDate(item.originalEvent.endDate), this.getFormatHours(item.originalEvent.endDate))
      }
      this.EditEvent();
    },
    clickTestAddItem() {
      if(this.event.id) {
        this.EditEvent();
      } else {
        this.AddNewEvent();
      }
      this.toggleEventForm();
    },
    AddNewEvent() {
      const newStart = this.event.startTime ? this.formatForStorage(this.event.startDate,this.event.startTime) : this.event.startDate, 
            newEnd = this.event.endTime ? this.formatForStorage(this.event.endDate,this.event.endTime) : this.event.endDate,
            payload = {
              ...this.event,
              startDate: newStart,
              endDate: newEnd,
            };
      this.isLoading = true;
      axios.post('/calevents',payload)
        .then((response) => {
          this.showNotification({type: response.data.status, message: response.data.message})
          this.loadEvents();
        })
        .catch((error, resp) => {
          console.log(error, resp)
          this.showNotification({type: error.response.data.status, message: error.response.data.message});
           this.loadEvents();
        });
    },
    EditEvent() {
      const newStart = this.event.startTime ? this.formatForStorage(this.event.startDate,this.event.startTime) : this.event.startDate, 
            newEnd = this.event.endTime ? this.formatForStorage(this.event.endDate,this.event.endTime) : this.event.endDate,
            payload = {
              ...this.event,
              startDate: newStart,
              endDate: newEnd,
            };
      this.isLoading = true;
      axios.put('/calevents',payload)
        .then((response) => {
          this.showNotification({type: response.data.status, message: response.data.message})
          this.loadEvents();
        })
        .catch((error, resp) => {
          console.log(error, resp)
          this.showNotification({type: error.response.data.status, message: error.response.data.message});
           this.loadEvents();
        });
    },
    deleteEvent() {
      let confirm = window.confirm(this.$options.filters.localize('Are you sure you want to delete this Event?'));      
      if(confirm) {
        this.isLoading = true;
        axios.delete(`/calevents/${this.event.id}`)
          .then((response) => {
            this.showNotification({type: response.data.status, message: response.data.message})
            this.loadEvents();
          })
          .catch((error, resp) => {
            console.log(error.response, resp)
            this.showNotification({type: error.response.data.status, message: error.response.data.message})
            this.loadEvents();
          });
      } 

      this.toggleEventForm();
    },
    resetEvent() {
      this.event = {...eventTemplate}
    }
  },
};
</script>

<style>
.event-calendar-wrapper{
  height: 48rem;
}
.calling .cv-weeks{
  opacity: 0.5;
  pointer-events: none;
}
button:disabled {
  opacity: 0.5;
}
</style>
