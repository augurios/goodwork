<template>
<div v-if="formShown">
  <div class="absolute top-0 left-0 right-0 mt-24 container mx-auto px-4 sm:max-w-xl md:max-w-3xl lg:max-w-5xl xl:max-w-6xl z-30">
    <div v-if="!linkForm" class="bg-gray-100 rounded shadow-lg">
      <div class="text-2xl text-gray-700 text-center font-semibold px-8 py-6">
          <h1 class="w-full bg-gray-100 text-gray-800 text-center rounded py-3 px-4" > {{ 'Providers' | localize }} </h1>
      </div>
      <div class="px-8 py-4 bg-white">  
        <linkSearch :handleSearchQuery="handleSearchQuery"/>
      </div>
      <div class="px-8 py-4 bg-white">
        <div v-if="links.length > 0" class="flex flex-row flex-wrap justify-center lg:mx-4 xxl:mx-0">
          <div v-for="link in links" :key="link.id" class="max-w-sm w-full lg:flex-grow">
            <linkCard :link="link" :editLink="editLink" :deleteLink="deleteLink" />
          </div>
        </div>
        <Spinner v-else size="w-16 h-16" />
      </div>
      
      <div class="flex flex-row justify-end py-6 px-8 bg-gray-100 rounded">
        <button @click="closeCreateTaskForm" class="border border-gray-400 bg-white text-gray-700 font-medium py-2 px-4 mr-4 rounded">{{ 'Close' | localize }}</button>
       
        <button @click="toggleLinkForm" class="bg-indigo-600 text-white font-medium py-2 px-4 rounded">{{ 'Create New' | localize }}</button>
      </div>
    </div>
    <div v-else class="bg-gray-100 rounded shadow-lg">
      <linkForm :enable="linkForm" :newLink="newLink" :closeLinkForm="toggleLinkForm" :isEdit="isEdit"/>
    </div>
    <div class="h-16"></div>
  </div>
  <div @click="closeCreateTaskForm" :class="{'hidden': !formShown}" class="h-screen w-screen fixed inset-0 bg-gray-900 opacity-25 z-20">
  </div>
</div>
</template>

<script>
import Datepicker from 'vuejs-datepicker'
import { mapState, mapActions } from 'vuex'
import { faChevronDown, faTimesCircle, faPlus } from '@fortawesome/free-solid-svg-icons'
import linkCard from './links-module/linkCard.vue';
import linkSearch from './links-module/linkSearch.vue';
import linkForm from './links-module/linkForm.vue';
import Spinner from './spinner.vue';

const linkDefaults = {
      title: '',       
      url: 'http://',         
      phone: '',       
      description: '',
      user_id: user.id,
      labels: []
    }

export default {
  components: {
    Datepicker,
    linkCard,
    linkSearch,
    linkForm,
    Spinner
    },
  props: {
    formShown: {
      required: true,
      type: Boolean
    },
    tasks: {
      required: false,
      type: Array
    },
  },

  data: () => ({
    name: '',
    notes: '',
    assignedTo: null,
    relatedTo: '',
    dueOnDate: null,
    editedFirstTask: '', 
    cycleId: null,
    disabledDates: {
      to: new Date(),
      from: ''
    },
    tags: [],
    labels: [],
    tagSuggestionShown: false,
    faChevronDown,
    faTimesCircle,
    faPlus,
    updatedDueOnDate: '',

    links: [],
    newLink: {
      ...linkDefaults
    },
    isEdit: false,
    linkForm: false,
  }),
  mounted: function() {
    this.loadLinkList();
  },
  methods: {
    ...mapActions([
      'showNotification',
    ]),
    loadLinkList() {
      axios.get('/links')
        .then((response) => {
          console.log('response', response.data.links);
          this.links = response.data.links;
        })
        .catch((error) => {
          console.log(error)
        })
    },
    toggleLinkForm() {
      this.linkForm = !this.linkForm;
      if (this.isEdit) {
        this.isEdit = false;
        this.resetForm();
      }
    },
    editLink (link) {
      this.newLink = link;
      this. toggleLinkForm();
      this.isEdit = true;
    },
    deleteLink(id) {
      axios.delete(`/links/${id}`)
        .then((response) => {
        })
        .catch((error, resp) => {
          console.log(error, resp)
        })
    },
    closeLinkForm () {
      this.resetForm();
    },
    resetForm () {
      this.newLink = linkDefaults;
      this.isEdit = false;
    },
    handleSearchQuery(searchQuery) {
      this.links = [];
      if(searchQuery.name || searchQuery.tag) {
        axios.post('/links/search',searchQuery)
        .then((response) => {
          this.links = response.data.links;
        })
        .catch((error) => {
          console.log(error)
        })
      } else {
        this.loadLinkList();
      }
    },
    closeCreateTaskForm () {

    },
    closeNotification () {
      this.showNotification = false
    },
    showTags () {
      this.tagSuggestionShown = true
    },
    dontShowTags () {
      this.tagSuggestionShown = false
    },
    addTag (tag) {
      if(tag.id)  {
        this.tags.push(this.availableTags.find(x => x.tag_id === tag.id))
        this.labels.push(tag.id)
        this.availableTags = this.availableTags.filter(x => x.tag_id !== tag.id)
      } else {
        this.tags.push(tag)
        this.labels.push(tag.tag_id)
        this.availableTags = this.availableTags.filter(x => x.tag_id !== tag.tag_id)
      }
    },
    deleteTag (tag) {
      let index = this.tags.indexOf(tag)
      if (index !== -1) { 
        this.tags.splice(index, 1)
        index = this.labels.indexOf(tag.id)
        this.labels.splice(index, 1)
        this.availableTags.push(tag)
      }
    },
    hydrateForm () {
      this.name = this.task.name
      this.notes = this.task.notes
      this.assignedTo = this.task.assigned_to
      this.relatedTo = this.task.related_to
      this.cycleId = this.selectedCycleId
      this.dueOnDate = this.task.due_on
      this.editedFirstTask = true
      this.task.tags.forEach(tagData => this.addTag(tagData));
    },
    dehydrateForm () {
      this.name = ''
      this.notes = ''
      this.assignedTo = null
      this.dueOnDate = null
      this.relatedTo = ''
      this.tags = []
      this.labels = []
    },
    changeDueDate(value) {
      let cycleSelected = this.cycles.find(cycle => cycle.id === value)
      let todayDate = new Date()
      todayDate.setDate(todayDate.getDate() - 1)
      let startDate = new Date(cycleSelected.start_date)
      startDate.setDate(startDate.getDate())
      let endDate = new Date(cycleSelected.end_date)
      endDate.setDate(endDate.getDate() + 1)

      if(todayDate.getTime() < startDate.getTime()){
        this.disabledDates.to = new Date(startDate)
      }else {
        this.disabledDates.to = new Date(todayDate)
      }

      this.disabledDates.from = new Date(endDate)
      
      if (this.editedFirstTask !== true) {
        this.dueOnDate = null
      }
      
      this.editedFirstTask =  false

    },
    updateDueOnDate(edited_dueDate){
      let tempDate = window.luxon.DateTime.fromISO(edited_dueDate).toISODate()
      if(tempDate !== null && tempDate !== '') {
        this.updatedDate = edited_dueDate
      } else {
        this.updatedDate = window.luxon.DateTime.fromISO(edited_dueDate.toISOString()).toISODate()
      }
    }
  },
}
</script>
