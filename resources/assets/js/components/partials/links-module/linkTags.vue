<template>
  <div class="bg-gray-100 rounded-b p-8">
    <div>
      <div class="pb-6 px-2 flex items-center">
        <div class="w-6 h-6 rounded-full bg-indigo-400 flex items-center justify-center">
          <font-awesome-icon :icon="faTags" class="text-white fa-xs"></font-awesome-icon>
        </div>
        <div class="pl-2 text-lg text-gray-600">
          {{'Select tags you want to be available in this' | localize }} {{ 'Provider' | localize }}
        </div>
      </div>
      <div class="flex">
        <div v-click-outside="hideTagsSuggestion">
          <div class="px-2 flex items-center">
            <input
              @click="toggleTagsSuggestion"
              type="text"
              readonly
              :value="[availableTags.length === 0 ? $options.filters.localize('No tag left to add') : $options.filters.localize('Add from list of all tags') ]"
              :class="[availableTags.length === 0 ? 'border-gray-300 border-2' : 'border-indigo-400 border-2']"
              class="px-3 py-2 rounded-l w-64">
            <div @click="toggleTagsSuggestion" :class="[availableTags.length === 0 ? 'border-gray-300 border-2 bg-gray-300' : 'border-indigo-400 border-2 bg-indigo-400']" class="px-3 py-2 rounded-r text-white -ml-1 cursor-pointer">
              <font-awesome-icon :icon="faCaretDown" class=""></font-awesome-icon>
            </div>
          </div>
          <div class="mx-2 w-64 bg-white">
            <div v-if="tagSuggestionShown && availableTags.length !== 0" class="shadow-md border py-1 rounded-b">
              <div @click="addTag(tag)" v-for="tag in availableTags" class="px-4 py-2 cursor-pointer -mt-1 hover:bg-indigo-400 hover:text-white">
                {{ tag.label | localize }}
              </div>
            </div>
          </div>
        </div>

        <div class="pl-8" v-click-outside="hideCreateForm">
          <div class="py-2">
            {{'or' | localize }},
            <button @click="toggleCreateForm" class="text-lg text-indigo-500 underline pl-2">{{'Create a New Tag' | localize }}</button>
          </div>
          <div v-if="createFormShown" class="flex shadow-lg">
            <input v-model="label" type="text" class="px-3 py-2 rounded-l w-64 border-indigo-400 border-2">
            <div @click="createNewTag" class="px-3 py-2 rounded-r text-white -ml-1 cursor-pointer border-indigo-400 border-2 bg-indigo-400">
              <font-awesome-icon :icon="faPlus" class=""></font-awesome-icon>
            </div>
          </div>
        </div>
      </div>

      <div class="px-2 pt-8">{{'Tags available in this' | localize }} {{ 'Provider' | localize }}</div>
      <div class="flex flex-row flex-wrap border border-gray-300 rounded mx-2 m-4 p-2">
        <div v-if="tags.length === 0">{{'No tags selected.' | localize }}</div>
        <div v-else v-for="tag in tags" :key="tag.id" class="flex items-center mx-1 my-1 py-1 px-3 bg-indigo-200 font-semibold text-indigo-700 rounded-full">
          <span class="text-sm">
            {{ tag.label | localize }}
          </span>
          <font-awesome-icon @click="deleteTag(tag)" :icon="faTimesCircle" class="text-sm text-indigo-700 ml-1 cursor-pointer"></font-awesome-icon>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions } from 'vuex'
import {
  faTimesCircle,
  faTags,
  faPlus,
  faCaretDown
} from '@fortawesome/free-solid-svg-icons'

export default {
  props: {
    tags: {
      type: Array,
      required: true
    }
  },

  data: () => ({
    tagSuggestionShown: false,
    createFormShown: false,
    availableTags: [],
    label: '',
    faTimesCircle,
    faTags,
    faPlus,
    faCaretDown
  }),

  mounted: function() {
    this.getTags();
  },

  methods: {
    ...mapActions([
      'showNotification',
    ]),
    getTags () {
      if (this.availableTags.length === 0) {
        axios.get('/tags')
          .then((response) => {
            this.availableTags = response.data.tags.filter(el => this.tags.findIndex(x => x.id === el.id) === -1)
          })
          .catch((error) => {
            console.error(error)
          })
      }
    },
    addTag (tag) {
          this.tags.push(tag)
          this.availableTags = this.availableTags.filter(x => x.id !== tag.id);
    },
    toggleTagsSuggestion () {
      this.tagSuggestionShown = ! this.tagSuggestionShown
    },
    hideTagsSuggestion () {
      this.tagSuggestionShown = false
    },
    deleteTag (tag) {
          let index = this.tags.indexOf(tag)
          if (index !== -1) { 
            this.tags.splice(index, 1)
            this.availableTags.push(tag)
          }
    },
    toggleCreateForm () {
      this.createFormShown = ! this.createFormShown
    },
    hideCreateForm () {
      this.createFormShown = false
    },
    createNewTag () {
      axios.post('/tags', {
        label: this.label
      })
        .then((response) => {
          this.availableTags.push(response.data.tag)
          this.label = ''
          this.createFormShown = false
          this.showNotification({type: response.data.status, message: response.data.message})
        })
        .catch((error) => {
          this.showNotification({type: error.response.data.status, message: error.response.data.message})
        })
    }
  }
}
</script>
