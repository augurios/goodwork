<template>
<div v-if="currentComponent === 'directory'">
  <div class="absolute top-0 left-0 right-0 mt-24 container mx-auto px-4 sm:max-w-xl md:max-w-3xl lg:max-w-5xl xl:max-w-6xl z-60">
    <div v-show="!linkForm" class="bg-gray-100 rounded shadow-lg">
      <div class="px-8 py-6">
          <h1 class="text-2xl text-center font-semibold w-full bg-gray-100 text-gray-800 rounded py-3 px-4" > {{ 'Directorio Municipal' | localize }} </h1>
          <userSearch :availableFilters="availableFilters" :handleSearchQuery="handleSearchQuery"/>
      </div>
      <div class="px-8 py-4 bg-white">
        <div v-if="users.length > 0 && !isLoading" class="flex flex-wrap lg:mx-4 xxl:mx-0 overflow-y-auto sm:h-72 lg:h-96">
          
          <div v-for="user in filteredUsers" :key="user.id" class="p-2 md:w-1/2 w-full h-40">
            <div class="h-full flex items-center border-gray-200 border p-4 rounded-lg">
              <img alt="team" class="w-16 h-16 bg-gray-100 object-cover object-center flex-shrink-0 rounded-full mr-4" :src="user.avatar">
              <div class="flex-grow">
                <h2 class="text-gray-900 title-font font-medium"><a :href="`/users/${user.username}`" class="hover:text-indigo-600"> {{ user.name }} <span class="text-xs">({{ user.username }})</span></a> </h2>
                <p class="text-gray-500" v-if="user.designation || user.location">{{ user.designation }} | {{ user.location }}</p>
              </div>
              <div class="flex-shrink-0 ml-4">
                <baseButton  @click="openChat(user)" btnType="success" ><font-awesome-icon :icon="faComment"></font-awesome-icon></baseButton>
              </div>
            </div>
          </div>
        </div>
        <div v-else-if="links.length === 0 && !isLoading" class="sm:h-72 lg:h-96 lg:mx-4 xxl:mx-0">
          {{ 'No hay resultados' | localize }}
        </div>
        <Spinner v-else size="w-16 h-16" />
      </div>
      
      <div class="flex flex-row justify-end py-6 px-8 bg-gray-100 rounded">
        <baseButton  @click="closeComponent" btnType="primary" class="mr-4 ">{{ 'Close' | localize }}</baseButton>
      </div>
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
import userSearch from './directory-module/userSearch.vue';
import Spinner from './spinner.vue';
import { faComment } from "@fortawesome/free-solid-svg-icons";
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
    userSearch,
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
    users: [],
    searchQuery: {
      name: '',
      location: '',
      position: '',
    },
    faComment
  }),
  computed: {
    ...mapState({
      currentComponent: state => state.dropdown.currentComponent,
    }),
    filteredUsers() {
      var filterResult = [...this.users];
      if(this.searchQuery.location) {
        filterResult = filterResult.filter((user) => {
          return user.location === this.searchQuery.location;
        });
      }
      if(this.searchQuery.position) {
        filterResult = filterResult.filter((user) => {
          return user.designation === this.searchQuery.position;
        });
      }
      if(this.searchQuery.name) {
        filterResult = filterResult.filter((user) => {
          return user.name.toLowerCase().includes(this.searchQuery.name.toLowerCase()) || user.username.toLowerCase().includes(this.searchQuery.name.toLowerCase());
        });
      }
      return filterResult;
    },
    availableFilters() {
      let locations = [],
          positions = [];
      this.users.forEach(function(user) {
        if(user.location && !locations.includes(user.location)) {
          locations.push(user.location)
        }
        if(user.designation && !locations.includes(user.designation)) {
          positions.push(user.designation)
        }
      });
      return {
        locations,
        positions
      };
    }
  },
  watch: {
    currentComponent: function (value) {
      if (value) {
        this.LoadUserList();
      }
    }
  },
  methods: {
    ...mapActions([
      'showNotification',
      'closeComponent',
      'setCurrentComponent'
    ]),
    LoadUserList() {
      this.isLoading = true;
        axios.get('/unread-direct-messages/users')
        .then((response) => {
          this.users = response.data.users;
          this.isLoading = false;
        })
        .catch((error) => {
          console.log(error)
        })
    },
    handleSearchQuery(searchQuery) {
      this.isLoading = true;
      this.searchQuery = searchQuery;
      this.isLoading = false;
    },
    openChat(user) {
      this.setCurrentComponent('direct-message-box')
      EventBus.$emit('openChatMsg',user);
      
    }
  },
}
</script>
<style scoped>
 .loader {
   margin: 0 auto;
 }
</style>