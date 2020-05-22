<template>
<div v-if="currentComponent === 'providers'">
  <div class="absolute top-0 left-0 right-0 mt-24 container mx-auto px-4 sm:max-w-xl md:max-w-3xl lg:max-w-5xl xl:max-w-6xl z-60">
    <div v-show="!linkForm" class="bg-gray-100 rounded shadow-lg">
      <div class="px-8 py-6">
          <h1 class="text-2xl text-center font-semibold w-full bg-gray-100 text-gray-800 rounded py-3 px-4" > {{ 'Providers' | localize }} </h1>
          <linkSearch :handleSearchQuery="handleSearchQuery"/>
      </div>
      <div class="px-8 py-4 bg-white">
        <div v-if="links.length > 0 && !isLoading" class="lg:mx-4 xxl:mx-0 overflow-y-auto sm:h-72 lg:h-96">
          <div v-for="link in links" :key="link.id" class="w-full">
            <linkCard :link="link" :editLink="editLink" :deleteLink="deleteLink" />
          </div>
        </div>
        <div v-else-if="links.length === 0 && !isLoading" class="sm:h-72 lg:h-96 lg:mx-4 xxl:mx-0">
          {{ 'No provider results found' | localize }}
        </div>
        <Spinner v-else size="w-16 h-16" />
      </div>
      
      <div class="flex flex-row justify-end py-6 px-8 bg-gray-100 rounded">
        <button @click="closeComponent" class="border border-gray-400 bg-white text-gray-700 font-medium py-2 px-4 mr-4 rounded">{{ 'Close' | localize }}</button>
       
        <button @click="toggleLinkForm" class="bg-indigo-600 text-white font-medium py-2 px-4 rounded">{{ 'Add New' | localize }}</button>
      </div>
    </div>
    <div v-show="linkForm" class="bg-gray-100 rounded shadow-lg">
      <linkForm :enabled="linkForm" :newLink="newLink" :closeLinkForm="toggleLinkForm" :isEdit="isEdit"/>
    </div>
    <div class="h-16"></div>
  </div>
  <div @click="closeComponent" class="h-screen w-screen fixed inset-0 bg-gray-900 opacity-25 z-50">
  </div>
</div>
</template>

<script>
import { mapState, mapActions } from 'vuex'
import linkCard from './links-module/linkCard.vue';
import linkSearch from './links-module/linkSearch.vue';
import linkForm from './links-module/linkForm.vue';
import Spinner from './spinner.vue';

const linkDefaults = {
      title: '',       
      url: 'http://',         
      phone: null,
      email: null,    
      description: '',
      user_id: user.id,
      tags: []
    }

export default {
  components: {
    linkCard,
    linkSearch,
    linkForm,
    Spinner
    },
  data: () => ({
    isLoading: false,
    links: [],
    newLink: {
      ...linkDefaults
    },
    isEdit: false,
    linkForm: false,
  }),
  computed: {
    ...mapState({
      currentComponent: state => state.dropdown.currentComponent,
    })
  },
  watch: {
    currentComponent: function (value) {
      if (value) {
        this.loadLinkList();
      }
    }
  },
  methods: {
    ...mapActions([
      'showNotification',
      'closeComponent',
    ]),
    loadLinkList() {
      this.isLoading = true;
      axios.get('/links')
        .then((response) => {
          console.log('response', response.data.links);
          this.links = response.data.links;
          this.isLoading = false;
        })
        .catch((error) => {
          console.log(error)
        })
    },
    toggleLinkForm() {
      if(this.linkForm) {
        this.resetForm();
      }
      this.linkForm = !this.linkForm;
      if (this.isEdit) {
        this.handleSearchQuery();
        this.isEdit = false;
      } else {
        this.loadLinkList();
      }
    },
    editLink (link) {
      this.newLink = link;
      this.toggleLinkForm();
      this.isEdit = true;
    },
    deleteLink(id) {
      let confirm = window.confirm(this.$options.filters.localize('Are you sure you want to delete this provider\'s contact info?'));

      if(confirm) {
        axios.delete(`/links/${id}`)
          .then((response) => {
            this.showNotification({type: response.data.status, message: response.data.message})
            this.handleSearchQuery();
          })
          .catch((error, resp) => {
            console.log(error.response, resp)
            this.handleSearchQuery();
            this.showNotification({type: error.response.data.status, message: error.response.data.message})
          });
      } 
    },
    closeLinkForm () {
      this.resetForm();
    },
    resetForm () {
      this.newLink = linkDefaults;
      this.isEdit = false;
    },
    handleSearchQuery(searchQuery) {
      this.isLoading = true;
      this.links = [];
      if(searchQuery && (searchQuery.name || searchQuery.tag)) {
        axios.post('/links/search',searchQuery)
        .then((response) => {
          this.links = response.data.links;
          this.isLoading = false;
        })
        .catch((error) => {
          console.log(error)
        })
      } else {
        this.loadLinkList();
      }
    }
  },
}
</script>
<style scoped>
 .loader {
   margin: 0 auto;
 }
</style>