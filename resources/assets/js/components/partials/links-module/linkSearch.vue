<template>
  <section class="p-4">
    <div class="container mx-auto">
      <input
        class="w-full h-16 px-3 rounded mb-8 focus:outline-none focus:shadow-outline text-xl px-8 shadow-lg"
        type="search"
        placeholder="Search..."
        v-model="searchQuery.name"
        v-on:change="handleSearchQuery(searchQuery)"
      />
      <Spinner v-if="isLoading" size="w-8 h-8" />
      <div v-else class="relative inline-flex">
        <font-awesome-icon
          :icon="faChevronDown"
          class="w-2 h-2 absolute top-0 right-0 m-4 pointer-events-none"
        ></font-awesome-icon>
        <select
          class="border border-gray-300 rounded-full text-gray-600 h-10 pl-5 pr-10 bg-white hover:border-gray-400 focus:outline-none appearance-none"
          v-model="searchQuery.tag"
          v-on:change="handleSearchQuery(searchQuery)"
        >
          <option :value="null">{{ "Filter by Tag" | localize }}</option>
          <option v-for="tag in availableTags" :key="tag.id" :value="tag.id">
            {{ tag.label }}
          </option>
        </select>
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
    handleSearchQuery: {
      type: Function,
      required: true,
    },
  },
  data: () => ({
    faChevronDown,
    isLoading: false,
    availableTags: [],
    searchQuery: {
      name: "",
      tag: null,
    },
  }),
  mounted: function() {
    this.getAvailableTags();
  },
  methods: {
    getAvailableTags() {
      this.isLoading = true;
      axios
        .get("/links/tags")
        .then((response) => {
          console.log("response", response.data.tags);
          this.availableTags = response.data.tags;
          this.isLoading = false;
        })
        .catch((error) => {
          console.log(error);
        });
    },
  },
};
</script>

<style></style>
