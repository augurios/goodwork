<template>
  <div v-if="showModal">
    <div class="absolute top-0 left-0 right-0 mt-24 container mx-auto px-4 w-full md:max-w-3xl lg:max-w-4xl xl:max-w-6xl z-30 mb-16">
      <div class="bg-gray-100 rounded shadow-lg py-8">
        <div class="flex flex-row justify-between relative px-8">
        <div @click="closeModal" class="cursor-pointer">
          <font-awesome-icon :icon="faArrowLeft"
            class="text-base text-gray-600">
          </font-awesome-icon>
        </div>
        <div @click="toggleMenu" v-click-outside="()=>{dropdownMenuShown = false}" class="cursor-pointer" v-if="hasMenuSlot">
          <font-awesome-icon :icon="faEllipsisH"
            class="text-base text-gray-600">
          </font-awesome-icon>
        </div>
        <div v-if="dropdownMenuShown" class="absolute rounded shadow-md right-0 top-0 mt-6 mr-4 py-1 text-indigo-800 bg-white">
         <slot name="menu"></slot>
        </div>
      </div>
        <slot></slot>
      </div>
      <div class="h-16"></div>
    </div>

    <div @click="closeModal" class="h-screen w-screen fixed inset-0 bg-gray-900 opacity-25 z-20"></div>
  </div>
</template>

<script>
import {
  faArrowLeft,
  faEllipsisH
} from '@fortawesome/free-solid-svg-icons'
export default {
  props: {
    showModal: {
      type: Boolean,
      required: true
    },
  },
  data () {
     return {
        dropdownMenuShown: false,
        faArrowLeft,
        faEllipsisH
      }
  },
  computed: {
    hasMenuSlot() {
      return !!this.$slots.menu
    }
  },
  methods: {
    toggleMenu () {
      this.dropdownMenuShown = !this.dropdownMenuShown
    },
    closeModal() {
      this.$emit('close');
    }
  }
}
</script>

<style>

</style>