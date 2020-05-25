<template>
<div>
  <div :class="{'hidden': !formShown}" class="absolute top-0 left-0 right-0 mt-24 container mx-auto mb-8 w-5/6 md:w-md lg:w-lg xl:w-xl z-30">
    <div class="bg-white rounded shadow-lg">
      <div class="">
        <div class="px-8 pt-8 bg-gray-200 rounded-t">
          <label class="block uppercase tracking-wide text-gray-800 text-xs font-bold mb-2" for="grid-first-name">
            {{ 'Title' | localize }} <span class="text-gray-500 capitalize">({{ 'required' | localize }})</span>
          </label>
          <input ref="inputFocus" v-model="name" class="appearance-none block w-full bg-white text-gray-800 border border-gray-200 rounded py-3 px-4" id="grid-last-name" type="text" :placeholder="$options.filters.localize('New Ticket Post')" required>
        </div>
        <div class="px-8 py-4 bg-gray-200">
          <label class="block uppercase tracking-wide text-gray-800 text-xs font-bold mb-2" for="grid-first-name">
            {{ 'Acoso por razón de género' | localize }} <span class="text-gray-500 capitalize">({{ 'required' | localize }})</span>
          </label>
          <div class="flex flex-row items-center">
            <select class="appearance-none block w-full bg-white text-gray-800 border border-gray-200 rounded py-3 px-4" v-model="subjectId">
              <option value="" disabled>{{ 'Choose one' | localize }}</option>
              <option :value="subject.id" v-for="subject in subjects" :key="subject.id">{{ subject.name }}</option>
            </select>
            <font-awesome-icon :icon="faChevronDown"
              class="w-1/6 pointer-events-none flex items-center text-gray-700 -ml-8">
            </font-awesome-icon>
          </div>
        </div>
        <div class="px-8 pb-4 bg-gray-200">
          <label class="block uppercase tracking-wide text-gray-800 text-xs font-bold mb-2" for="grid-first-name">
            {{ 'Usuarios Asignados' | localize }}:
          </label>
         
            <div>
              <div class="relative">
                  <input class="appearance-none block w-full bg-white text-gray-800 border border-gray-200 rounded py-3 px-4" v-model="searchQuery.text" v-on:input="searchUsers" type="search" placeholder="Buscar Usuarios">
                  
                  <div class="rounded shadow-md my-2 absolute pin-t pin-l bg-white z-50" v-if="searchQuery.showSearchResults">
                      <Spinner v-if="searchQuery.isSearching" size="w-8 h-8" />
                      <ul v-else-if="availableUsers.length > 0" class="list-reset search-results overflow-auto">
                        <li v-for="user in availableUsers" :key="user.id" v-on:click="addUser(user)"><p class="py-2 px-8 block text-black hover:bg-grey-400 cursor-pointer"> {{ user.name }} ({{ user.username }})  <button class="no-underline px-2 py-1 bg-indigo-400 text-xs text-white rounded"><strong>+</strong></button></p></li>
                      </ul>
                      <p v-else class="p-4">No hay resultados</p>
                  </div>
              </div>
            </div>

            <div class="flex flex-row flex-wrap border border-gray-300 rounded mx-0 m-4 p-2">
              <div v-for="user in assignedUsers" :key="user.id" class="flex items-center mx-1 my-1 py-1 px-3 bg-indigo-200 font-semibold text-indigo-700 rounded-full">
                <span class="text-sm">
                  {{ user.name }} ({{ user.username }})
                </span>
                <font-awesome-icon @click="removeUser(user)" :icon="faTimesCircle" class="text-sm text-indigo-700 ml-1 cursor-pointer"></font-awesome-icon>
              </div>
            </div>
          

        </div>
        <div class="px-8 pb-8 bg-gray-200">
          <label class="block uppercase tracking-wide text-gray-800 text-xs font-bold mb-2" for="grid-first-name">
            {{ 'Body' | localize }} <span class="text-gray-500 capitalize">({{ 'required' | localize }})</span>
          </label>
          <div id="editor" class="h-80 bg-white">
          </div>
        </div>
      </div>
      <div class="flex flex-row justify-between px-8 py-4 bg-white rounded-b">
        <button @click="closeEditor" class="no-underline px-3 py-2 my-4 bg-white text-base text-red-400 rounded border-red-400 border">{{ 'Cancel' | localize }}</button>
        <div v-if="!this.ticket">
          <button @click="savePost(true)" class="no-underline px-3 py-2 mr-4 my-4 text-indigo-400  text-base bg-white font-medium rounded border-indigo-400  border">{{ 'Save as a Draft' | localize }}</button>
          <button @click="savePost(false)" class="no-underline px-3 py-2 my-4 bg-indigo-400 text-base text-white font-medium rounded">{{ 'Publish' | localize }}</button>
        </div>
        <div v-if="this.ticket">
          <button @click="updatePost(false)" class="no-underline px-3 py-2 my-4 bg-indigo-400 text-base text-white font-medium rounded">{{ 'Save' | localize }}</button>
        </div>
      </div>
    </div>
    <div class="h-16"></div>
  </div>

  <div @click="closeEditor" :class="{'hidden': !formShown}" class="h-screen w-screen fixed inset-0 bg-gray-900 opacity-25 z-20"></div>
</div>
</template>

<script>
import Quill from 'quill';
import { mapState, mapActions } from 'vuex'
import imageUpload from 'quill-plugin-image-upload'
import { faChevronDown,faTimesCircle } from '@fortawesome/free-solid-svg-icons'
import Spinner from "./../partials/spinner.vue";
Quill.register('modules/imageUploadTicket', imageUpload)

export default {
  components: {
    Spinner
  },
  props: {
    formShown: {
      required: true,
      type: Boolean
    },
    resourceId: {
      required: true,
      type: Number
    },
    resourceType: {
      required: true,
      type: String
    },
    ticket: {
      required: false,
      type: Object
    }
  },

  data: () => ({
    quill: null,
    name: '',
    cycleId: '',
    subjectId: '',
    subjects: [],
    faChevronDown,
    faTimesCircle,
    searchQuery: {
      text: '',
      showSearchResults: false,
      isSearching: false,
      searchTimeout: null,
    },
    availableUsers: [],
    assignedUsers: [],
  }),

  mounted () {
    if (this.quill === null) {
      this.quill = new Quill('#editor', {
        modules: {
          toolbar: [
            [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
            ['bold', 'italic', 'underline', 'strike'],
            [{ 'list': 'ordered' }, { 'list': 'bullet' }],
            ['blockquote', 'code-block'],
            ['image', 'link']
          ],
          imageUpload: {
            upload: file => {
              let formData = new FormData()
              formData.append('file', file)
              formData.append('group_type', this.resourceType)
              formData.append('group_id', this.resourceId)
              
              // return a Promise that resolves in a link to the uploaded image
              return new Promise((resolve, reject) => {
                axios.post('/tickets/files',
                  formData,
                  {
                    headers: {
                      'Content-Type': 'multipart/form-data'
                    }
                  })
                  .then((response) => {
                    this.showNotification({type: response.data.status, message: response.data.message})
                    resolve(response.data.url)
                  })
                  .catch((error) => {
                    console.error(error);
                    
                    this.showNotification({type: error.response.data.status, message: error.response.data.message})
                  })
              });
            }
          },
        },
        theme: 'snow'
      })
    }
  },

  watch: {
    formShown: function (value, oldValue) {
      if (value) {
        this.getSubjects()
        this.cycleId = this.selectedCycleId
        if (this.ticket) {
          this.hydrateEditor()
        }
      }
    }
  },

  computed: {
    ...mapState({
      cycles: state => state.cycle.cycles,
      selectedCycleId: state => state.cycle.selectedCycleId
    })
  },

  methods: {
    ...mapActions([
      'showNotification',
    ]),
    savePost (draft = true) {
      axios.post('/tickets', {
        name: this.name,
        subject_id: this.subjectId,
        content: this.quill.root.innerHTML,
        raw_content: JSON.stringify(this.quill.getContents()),
        draft: draft,
        group_type: this.resourceType,
        group_id: this.resourceId,
        assigned_to: this.assignedUsers.map(user => (user.id))
      })
        .then((response) => {
          this.name = ''
          this.subjectId = ''
          this.quill.setContents([])
          this.showNotification({type: response.data.status, message: response.data.message})
          this.$emit('close', response.data.ticket)
        })
        .catch((error) => {
          this.showNotification({type: error.response.data.status, message: error.response.data.message})
          this.$emit('close')
        })
    },
    closeEditor () {
      this.name = ''
      this.cycleId = ''
      this.subjectId = ''
      this.assignedUsers = []
      this.quill.setContents([])
      this.$emit('close')
    },
    getSubjects () {
      if (this.formShown && this.subjects.length < 1) {
        axios.get('/subjects')
          .then((response) => {
            this.subjects = response.data.subjects
          })
          .catch((error) => {})
      }
    },
    hydrateEditor () {
      this.name = this.ticket.name
      this.cycleId = this.ticket.cycle_id
      this.subjectId = this.ticket.subject_id
      this.assignedUsers = this.ticket.assignees,
      this.quill.updateContents(JSON.parse(this.ticket.raw_content))
    },
    updatePost (draft = true) {
      axios.patch('/tickets/' + this.ticket.id, {
        name: this.name,
        subject_id: this.subjectId,
        content: this.quill.root.innerHTML,
        raw_content: JSON.stringify(this.quill.getContents()),
        group_type: this.resourceType,
        group_id: this.resourceId,
        draft: draft,
        assigned_to: this.assignedUsers.map(user => (user.id))
      })
        .then((response) => {
          this.name = ''
          this.subjectId = ''
          this.assignedUsers = []
          this.quill.setContents([])
          this.showNotification({type: response.data.status, message: response.data.message})
          this.$emit('close', null, response.data.ticket)
        })
        .catch((error) => {
          this.showNotification({type: error.response.data.status, message: error.response.data.message})
          this.$emit('close')
        })
    },
    searchUsers() {
      if(this.searchQuery.searchTimeout) {
        clearTimeout(this.searchQuery.searchTimeout);
      } 
      
      this.searchQuery.searchTimeout = setTimeout(this.searchCall, 500);
    
      
    },
    searchCall() {
      this.searchQuery.showSearchResults = true; 
      this.searchQuery.isSearching = true;
      if(this.searchQuery.text) {
        axios.post('/users/search', {name: this.searchQuery.text})
        .then((response) => {
          this.availableUsers = response.data.users.filter(this.userFilter);
          this.searchQuery.isSearching = false;
        })
        .catch((error) => {
          console.log(error)
          this.searchQuery.isSearching = false;
        })
      } else {
        this.searchQuery.showSearchResults = false;
      }
    },
    addUser(user) {
      this.searchQuery.showSearchResults = false;
      this.assignedUsers.push(user);
      this.searchQuery.text = '';
    },
    userFilter(user) {
      const userIds = this.assignedUsers.map(user => (user.id));
      if(user.id != authUser.id && !userIds.includes(user.id)) {
        return true;
      } else {
        return false;
      }
    },
    removeUser(user) {
        let index = this.assignedUsers.indexOf(user)
          if (index !== -1) { 
            this.assignedUsers.splice(index, 1)
          }
    }
  },
}
</script>

<style src="./../../../css/editor.css">
.search-results {
    max-height: 10rem;
}
</style>
