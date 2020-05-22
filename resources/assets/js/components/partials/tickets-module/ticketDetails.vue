<template>
<div v-if="ticketDetailsShown">
  <div class="absolute top-0 left-0 right-0 mt-24 container mx-auto px-4 w-full md:max-w-3xl lg:max-w-4xl xl:max-w-6xl z-30 mb-16">
    <div class="bg-gray-100 rounded shadow-lg py-8">
      <div class="flex flex-row justify-between relative px-8">
        <div @click="closeTicketDetails(false)" class="cursor-pointer">
          <font-awesome-icon :icon="faArrowLeft"
            class="text-base text-gray-600">
          </font-awesome-icon>
        </div>
        <div @click="toggleMenu" v-click-outside="hideMenu" class="cursor-pointer">
          <font-awesome-icon :icon="faEllipsisH"
            class="text-base text-gray-600">
          </font-awesome-icon>
        </div>
        <div v-if="dropdownMenuShown" class="absolute rounded shadow-md right-0 top-0 mt-6 mr-4 py-1 text-indigo-800 bg-white">
          <div @click="editTicket" class="cursor-pointer hover:bg-indigo-200 px-4 py-2">
            Edit
          </div>
          <div @click="deleteTicket" class="cursor-pointer hover:bg-indigo-200 px-4 py-2">
            Delete
          </div>
        </div>
      </div>

      <div :data-ticket-id="ticket.id" class="text-gray-700 text-left text-3xl font-medium py-4 px-16">
        {{ ticket.name }}
      </div>
       <div class="flex flex-shrink-0 text-sm items-center pr-2 py-4 px-16">
          <div class="bg-gray-300 text-gray-600 px-2 py-1 rounded-l">Estado</div>
          <div v-if="ticket.draft" class="bg-gray-500 text-green-100 px-2 py-1 rounded-r-md">Borrador</div>
          <div v-else-if="!ticket.draft && !ticket.archived" class="bg-green-200 text-green-700 px-2 py-1 rounded-r">Activo</div>
          <div v-else class="bg-red-200 text-red-900 px-2 py-1 rounded-r">Closed</div>
      </div>
      <div class="flex py-4 px-16">
        <span class="text-sm border border-2 rounded-l px-4 py-2 bg-gray-300 whitespace-no-wrap">{{ 'Acoso por razón de género' | localize }}:</span>
        <input name="field_name" v-model="ticket.subject.name" class="border border-2 rounded-r px-4 py-2 w-full" type="text" disabled />
    </div>
      <div class="flex flex-row justify-start items-center pb-4 px-16">
        <img :src="generateUrl(ticket.creator.avatar)" class="rounded-full w-10 h-10">
        <div class="text-gray-800 text-sm ml-4">
          <div>
            {{ ticket.creator.name }}
          </div>
          <div>
            {{ ticket.date }}
          </div>
        </div>
      </div>
      <div class="border-t w-full h-16 flex flex-row justify-start items-center py-4 px-16">
        <div class="flex flex-row flex-row-reverse">
          <a v-for="(member, index) in ticket.assignees" v-if="index < 6" :href="'/users/' + member.username" :key="member.id">
            <profile-card :user="member"></profile-card>
          </a>
        </div>
        <span v-if="ticket.assignees.length > 6" class="w-10 h-10 bg-indigo-100 text-indigo-700 border-white border font-semibold p-2 -ml-2 rounded-full">{{ ticket.assignees.length - 6 }}+</span>
      </div>
      <div class="ql-snow">
        <div class="ql-editor -mx-3">
          <div v-html="ticket.content" class="py-8 px-16 bg-white text-gray-900"></div>
        </div>
      </div>

      <comment-ticket-box resourceType="discussion" :resourceId="ticket.id" parentGroupType="office" :parentGroupId="1" :show="ticketDetailsShown" class="px-16"></comment-ticket-box>
    </div>
    <div class="h-16"></div>
  </div>

  <div @click="closeTicketDetails(false)" class="h-screen w-screen fixed inset-0 bg-gray-900 opacity-25 z-20"></div>
</div>
</template>

<script>
import { mapActions } from 'vuex'
import commentTicketBox from './../commentTicketBox.vue'
import {
  faArrowLeft,
  faEllipsisH
} from '@fortawesome/free-solid-svg-icons'
import profileCard from './../profileCard.vue'

export default {
  components: {
    commentTicketBox,
    profileCard
  },
  props: {
    ticketDetailsShown: {
      required: true,
      type: Boolean
    },
    ticket: {
      required: true,
      type: Object
    },
    index: {
      required: true
    }
  },

  data () {
    return {
      dropdownMenuShown: false,
      faArrowLeft,
      faEllipsisH
    }
  },

  mounted () {
    let topElement = document.getElementById('app')
    topElement.scrollIntoView({behavior: "smooth", block: "start"})
  },

  methods: {
    ...mapActions([
      'showNotification',
    ]),
    closeTicketDetails (editTicket = false) {
      this.dropdownMenuShown = false
      this.$emit('close', editTicket)
    },
    toggleMenu () {
      this.dropdownMenuShown = !this.dropdownMenuShown
    },
    hideMenu () {
      this.dropdownMenuShown = false
    },
    deleteTicket () {
      axios.delete(`/tickets/${this.ticket.id}`)
        .then((response) => {
          this.$emit('deleted', this.index)
          this.closeTicketDetails()
          this.showNotification({type: response.data.status, message: response.data.message})
        })
        .catch((error) => {
          this.closeTicketDetails()
          this.showNotification({type: error.response.data.status, message: error.response.data.message})
        })
    },
    editTicket () {
      this.closeTicketDetails(true);
    }
  }
}
</script>

<style src="./../../../../css/editor.css">
</style>
