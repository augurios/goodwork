<template>
<div id="ticket-container" v-if="activeTab === 'tickets'" class="w-full">
  <create-ticket-form ref="ticketForm" :resourceId="resource.id" :resourceType="resourceType" :ticket="ticket" @close="closeCreateTicketForm" :form-shown="createTicketFormShown"></create-ticket-form>

  <ticket-details v-if="ticket" :ticketDetailsShown="ticketDetailsShown" :ticket="ticket" :index="index" @close="closeTicketDetails" @deleted="deleteTicket"></ticket-details>

  <div v-if="authenticated" class="">
    <baseButton @click="showCreateTicketForm"> {{ 'Abrir Caso' | localize }} </baseButton>
    <baseButton @click="toogleTickets" class="ml-2"> 
      <span v-if="showDraftPost">{{ 'Mostrar Propios' | localize }} ({{draftTickets.length}})</span>
      <span v-else>{{ 'Mostrar Asignados' | localize }} ({{publishedTickets.length}})</span>
    </baseButton>
  </div>
  <div class="flex flex-row flex-wrap items-start md:-mx-4">
    <div @click="showTicketDetails(index, ticket.id)" v-for="(ticket, index) in tickets" :key="ticket.id" class="w-full md:w-88 my-6 md:mx-4 bg-white shadow-md relative ticket-card hover:shadow-xl active:shadow-sm focus:shadow-sm flex flex-col rounded cursor-pointer transition-all duration-300 ease-linear" aria-pressed="true">
      <div class="bg-indigo-500 flex flex-col items-start w-full text-white rounded-t p-6 pb-4">
        <div class="text-white text-xl font-semibold ticket-title">{{ ticket.name }}</div>
        <div class="flex flex-row pt-2">
          <div class="w-10 h-10 mr-2">
            <a :href="'/users/' + ticket.creator.username">
              <img :src="generateUrl(ticket.creator.avatar)" class="rounded-full w-10 h-10">
            </a>
          </div>
          <div class="text-xs font-bold flex flex-col justify-center">
            <div>
              {{ ticket.subject.name }}
            </div>
            <div>
              {{ ticket.date }}
            </div>
          </div>
        </div>
      </div>
      <div class="text-sm my-4 mx-6 text-gray-800 leading-normal overflow-hidden flex flex-shrink-0 justify-between" style="max-height: 12rem;">
          <div class="flex">
            <div class="bg-gray-300 text-gray-600 px-2 py-1 rounded-l">Estado</div>
            <div v-if="ticket.draft" class="bg-gray-500 text-green-100 px-2 py-1 rounded-r-md">Borrador</div>
            <div v-else-if="!ticket.draft && !ticket.archived" class="bg-green-200 text-green-700 px-2 py-1 rounded-r">Activo</div>
            <div v-else class="bg-red-200 text-red-900 px-2 py-1 rounded-r">Closed</div>
          </div>

          <div class="py-1">
            <font-awesome-icon :icon="faComments"
            class="text-base text-gray-600">
              </font-awesome-icon>
              <span>{{ticket.comments}}</span>
          </div>
      </div>
    </div>
  </div>
  <Spinner v-if="isLoading" size="w-64 h-64 m-auto pt-8" />
  <div v-else-if="tickets.length === 0" class="flex flex-col items-center pt-8">
    <div class="pb-4 text-lg">{{ 'Have an idea or question? Create a Ticket' | localize }}</div>
    <img src="/image/discussions.svg" alt="ticket post" class="w-80">
  </div>
</div>
</template>

<script>
import { mapState } from 'vuex'
import createTicketForm from './../../forms/createTicketForm.vue'
import ticketDetails from './ticketDetails'
import Spinner from "./../spinner.vue";
import {
  faComments
} from '@fortawesome/free-solid-svg-icons';

export default {
  components: {
    createTicketForm,
    ticketDetails,
    Spinner
  },
  data: () => ({
    resource: {
      id:2
    },
    isLoading: false,
    activeTab: 'tickets',
    resourceType: 'office',
    createTicketFormShown: false,
    faComments,
    tickets: [],
    ticket: null,
    draftTickets: [],
    publishedTickets: [],
    ticketDetailsShown: false,
    index: null,
    authenticated,
    showDraftPost: true
  }),
  async created () {
    this.isLoading = true;
    await this.getAllPublishedTickets(true)
    await this.getAllDraftTickets(true)
    const id = new URL(location.href).searchParams.get('id')
    if (id) {
      this.ticket = this.tickets.find(ticket => ticket.id === parseInt(id))
      this.ticketDetailsShown = true
    } else {
      this.ticket = null;
    }
  },
  watch: {
    activeTab: function () {
      this.getAllPublishedTickets(false)
      this.getAllDraftTickets(false)
    },
    selectedCycleId: function () {
      this.getAllPublishedTickets(true)
      this.getAllDraftTickets(true)
      this.showDraftPost = true
    }
  },
  computed: {
    ...mapState({
      selectedCycleId: state => state.cycle.selectedCycle ? state.cycle.selectedCycle.id : 0
    })
  },
  methods: {
    showCreateTicketForm () {
      this.createTicketFormShown = true
      this.$nextTick(() => {
        this.$refs.ticketForm.$refs.inputFocus.focus();
      })
    },
    closeCreateTicketForm (newTicket = null, updatedTicket = null) {
      if (newTicket && !newTicket.draft && (this.selectedCycleId === 0 || this.selectedCycleId === newTicket.cycle_id)) {
        this.showDraftPost = false
        this.publishedTickets.push(newTicket)
        this.toogleTickets()
      } else if (newTicket && newTicket.draft && (this.selectedCycleId === 0 || this.selectedCycleId === newTicket.cycle_id)) {
        this.showDraftPost = true
        this.draftTickets.push(newTicket)
        this.toogleTickets()
      } else if (updatedTicket && (this.selectedCycleId === 0 || this.selectedCycleId === updatedTicket.cycle_id)) {
        this.updateTicketsData(updatedTicket)
      }
      this.createTicketFormShown = false
      this.ticket = null
    },
    async getAllPublishedTickets (update = false) {
      try {
        if (this.activeTab === 'tickets' && (this.tickets.length < 1 || update)) {
          let { data } = await axios({
            url: '/tickets',
            params: {
              group_type: this.resourceType,
              group_id: this.resource.id,
              cycle_id: this.selectedCycleId !== 0 ? this.selectedCycleId : null
            }})
          this.publishedTickets = data.tickets
          this.tickets = data.tickets
          this.isLoading = false;
          return this.tickets
        }
      } catch (error) {
        console.error(error.response.data.message)
      }
    },
    async getAllDraftTickets (update = false) {
      try {
        if (this.activeTab === 'tickets'  && (this.tickets.length < 1 || update)) {
          let { data } = await axios({
            url: '/draft-tickets',
            params: {
              group_type: this.resourceType,
              group_id: this.resource.id,
              cycle_id: this.selectedCycleId !== 0 ? this.selectedCycleId : null
            }})
          this.draftTickets = data.draft_tickets
          return this.draftTickets
        }
      } catch (error) {
        console.error(error.response.data.message)
      }
    },
    showTicketDetails (index, id) {
      this.updateUrl({"id": id})
      this.index = index
      this.ticket = this.tickets[index]
      this.ticketDetailsShown = true
    },
    closeTicketDetails (editTicket = false) {
      this.updateUrl({"id": null})
      this.ticketDetailsShown = false;
      if (editTicket) {
        this.createTicketFormShown = true;
      } else {
        this.ticket = {}
        this.createTicketFormShown = false;
      }
    },
    deleteTicket (index) {
      this.tickets.splice(index, 1)
    },
    updateTicketsData(updatedTicket){
      const foundTicket = this.publishedTickets.find(disc => disc.id === updatedTicket.id);

      if(foundTicket) {
        this.showDraftPost = false
        this.publishedTickets.splice(this.index, 1, updatedTicket)
        this.toogleTickets()
      } else {
        this.showDraftPost  = true
        this.publishedTickets.push(updatedTicket)
        this.draftTickets = this.draftTickets.filter(draft => draft.id !== updatedTicket.id)
        this.toogleTickets()
      }
    },
    toogleTickets() {
      if(this.showDraftPost === true){
        this.showDraftPost = false
        this.tickets = this.draftTickets
      } else {
        this.showDraftPost = true
        this.tickets = this.publishedTickets
      }
    }
  },
}
</script>
<style scoped>
  .ticket-title {
    min-height: 4.5rem;
  }
  .ticket-card {
    transform:translateY(0rem); 
  }
  .ticket-card:hover {
    transform: translateY(-0.2rem); 
  }
</style>