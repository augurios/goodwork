<template>
<div id="tools-dropdown-container" class="pr-6 h-full flex items-center cursor-pointer">
  <div id="tools-dropdown" class="text-indigo-500  text-base no-underline cursor-pointer" @click="toggleToolsDropdown" v-click-outside="hideToolsDropdown">
    <font-awesome-icon :icon="faTools" class="font-bold text-xl"></font-awesome-icon>
  </div>
  <div v-if="currentComponent === 'tools-dropdown'" id="tools-menu" class="absolute bg-white w-48 -ml-32 mr-2 py-1 shadow-lg rounded z-50" style="top:3.5rem;">
    <a name="directorio-menu" class="px-4 py-2 hover:bg-indigo-500 hover:text-white text-gray-600 font-medium no-underline block" @click="showDirectory">
      <span class="w-6 inline-block">
        <font-awesome-icon :icon="faUsers" class="pr-1 font-regular"></font-awesome-icon>
      </span>
      {{ 'Directorio' | localize }}
    </a>
    <a name="providers-menu" class="px-4 py-2 hover:bg-indigo-500 hover:text-white text-gray-600 font-medium no-underline block" @click="showProviders">
      <span class="w-6 inline-block">
        <font-awesome-icon :icon="faUser" class="pr-1 font-regular"></font-awesome-icon>
      </span>
      {{ 'Providers' | localize }}
    </a>
    <a name="files-menu" class="px-4 py-2 hover:bg-indigo-500 hover:text-white text-gray-600 font-medium no-underline block" @click="showFiles">
      <span class="w-6 inline-block">
        <font-awesome-icon :icon="faFile" class="pr-1 font-regular"></font-awesome-icon>
      </span>
      {{ 'Repositorio' | localize }}
    </a>
    <a name="timer-menu" class="px-4 py-2 hover:bg-indigo-500 hover:text-white text-gray-600 font-medium no-underline block" @click="showTimer">
      <span class="w-6 inline-block">
        <font-awesome-icon :icon="faStopwatch" class="pr-1 font-regular"></font-awesome-icon>
      </span>
      {{ 'Timer' | localize }}
    </a>
  </div>
 
</div>
</template>

<script>
import { mapState, mapActions } from 'vuex'
import {
  faAngleDown,
  faCog,
  faShieldAlt,
  faSignOutAlt,
  faUser,
  faUsers,
  faEnvelope,
  faUserMinus,
  faStopwatch,
  faFile,
  faTools
} from '@fortawesome/free-solid-svg-icons'
export default {
  data: () => ({
    user: user,
    token: Laravel.csrfToken,
    url: navUrl,
    avatar: '',
    profileUrl: navUrl.site + '/users/' + user.username,
    impersonating: impersonating,
    authenticated,
    faAngleDown,
    faCog,
    faShieldAlt,
    faSignOutAlt,
    faUser,
    faUsers,
    faUserMinus,
    faEnvelope,
    faStopwatch,
    faFile,
    faTools
  }),

  computed: {
    ...mapState({
      currentComponent: state => state.dropdown.currentComponent,
    }),
  },

  methods: {
    ...mapActions([
      'setCurrentComponent',
      'closeComponent'
    ]),
    logoutUser (event) {
      event.preventDefault()
      document.getElementById('logout-form').submit()
    },
    toggleToolsDropdown (event) {
      if (this.currentComponent === 'tools-dropdown') {
        this.hideToolsDropdown(event)
        document.body.removeEventListener('keyup', this.hideToolsDropdown)
      } else {
        this.showToolsDropdown()
        document.body.addEventListener('keyup', this.hideToolsDropdown)
      }
    },
    showToolsDropdown (event) {
      if (this.notificationShown) {
        this.notificationShown = false
      }
      this.setCurrentComponent('tools-dropdown')
    },
    hideToolsDropdown (event) {
      if (event.type === 'keyup' && event.key !== 'Escape') {
        return false
      }
      if (this.currentComponent === 'tools-dropdown') {
        this.closeComponent('')
      }
    },
    showTimer (event) {
      this.setCurrentComponent('timer')
    },
    showProviders (event) {
      this.setCurrentComponent('providers')
    },
    showDirectory () {
      this.setCurrentComponent('directory')
    },
    showFiles (event) {
      this.setCurrentComponent('files')
    }
  }
}
</script>
