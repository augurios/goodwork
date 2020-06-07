<template>
  <section class="p-4">
    <div class="container mx-auto">
      <input
        class="w-full h-16 px-3 rounded mb-8 focus:outline-none focus:shadow-outline text-xl px-8 shadow-lg"
        type="search"
        placeholder="Buscar Usuarios Municipales"
        v-model="searchQuery.name"
        v-on:change="handleSearchQuery(searchQuery)"
      />
      <div class="relative inline-flex">
         <div class="relative mr-4">
          <font-awesome-icon
            :icon="faChevronDown"
            class="w-2 h-2 absolute top-0 right-0 m-4 pointer-events-none"
          ></font-awesome-icon>
          <select
            class="border border-gray-300 rounded-full text-gray-600 h-10 pl-5 pr-10 bg-white hover:border-gray-400 focus:outline-none appearance-none"
            v-model="searchQuery.location"
            v-on:change="handleSearchQuery(searchQuery)"
          >
            <option :value="null">{{ "Filtrar por Ubicación" | localize }}</option>
            <option v-for="location in availableFilters.locations" :key="location" :value="location">
              {{ location }}
            </option>
          </select>
         </div>
        <div class="relative">
          <font-awesome-icon
            :icon="faChevronDown"
            class="w-2 h-2 absolute top-0 right-0 m-4 pointer-events-none"
          ></font-awesome-icon>
          <select
            class="border border-gray-300 rounded-full text-gray-600 h-10 pl-5 pr-10 bg-white hover:border-gray-400 focus:outline-none appearance-none"
            v-model="searchQuery.position"
            v-on:change="handleSearchQuery(searchQuery)"
          >
            <option :value="null">{{ "Filtrar por Puesto" | localize }}</option>
            <option v-for="position in availableFilters.positions" :key="position" :value="position">
              {{ position }}
            </option>
          </select>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import { faChevronDown } from "@fortawesome/free-solid-svg-icons";
import Spinner from "./../spinner.vue";
export default {
  components: {
    Spinner,
  },
  props: {
    availableFilters: {
      type: Object,
      required: true
    },
    handleSearchQuery: {
      type: Function,
      required: true,
    },
  },
  data: () => ({
    faChevronDown,
    searchQuery: {
      name: "",
      location: null,
      position: null,
    },
  }),
};
</script>

<style></style>
