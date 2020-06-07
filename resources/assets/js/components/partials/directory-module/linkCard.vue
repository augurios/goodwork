<template>
  <div
    class="border-r border-b border-l border-t border-gray-400 lg:border-l lg:border-t lg:border-gray-400 bg-white rounded lg:rounded-r p-4 flex flex-col justify-between leading-normal mb-4"
  >
    <div class="mb-1">
      <div class="text-gray-900 font-bold text-xl mb-2">{{ link.title }}</div>
      <div class="bg-white flex flex-row flex-wrap justify-start py-2 pb">
        <div v-if="link.tags.length === 0" class="text-gray-700">
          {{ "Sin Categoria" | localize }}
        </div>
        <div
          v-else
          v-for="tag in link.tags"
          :key="tag.id"
          class="bg-indigo-200 px-3 py-1 rounded-full text-indigo-800 font-medium text-xs mr-4"
        >
          {{ tag.label }}
        </div>
      </div>
      <p  v-if="link.url"  class="text-gray-700 text-base">
        <a :href="link.url">{{ link.url }}</a>
      </p>
      <p class="text-gray-700 text-base mb-2">{{ link.phone }}</p>
      <p v-if="link.email" class="text-gray-700 text-base mb-2">{{ link.email }}</p>
      <p v-if="link.description" class="text-gray-700 text-base mb-2"><em>{{ link.description }}</em></p>
      <p v-if="link.user" class="text-gray-700 text-xs">Created by {{ link.user.name }}</p>

      <div class="bg-white flex flex-row flex-wrap justify-start py-2 pb">
        <button 
          v-if="editLink"
          @click="editLink(link)"
          class="bg-indigo-600 text-white font-medium py-1 px-2 rounded text-xs mr-2"
        >
          {{ "Edit" | localize }}
        </button>
        <button 
          v-if="deleteLink"
          @click="deleteLink(link.id)"
          class="bg-red-600 text-white font-medium py-1 px-2 rounded text-xs"
        >
          {{ "Delete" | localize }}
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    link: {
      type: Object,
      required: true
    },
    editLink: {
      type: Function,
    },
    deleteLink: {
      type: Function,
    }
  }
};
</script>

<style>
</style>
