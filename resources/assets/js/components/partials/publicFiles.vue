<template>
  <ModalComponent v-if="currentComponent === 'files'" :showModal="currentComponent === 'files'" @close="closeComponent"> 
    <div class="pub-files">
      <div class="text-2xl text-gray-700 text-center font-semibold px-8 pb-6">
          <h1 class="w-full bg-gray-100 text-gray-800 text-center rounded pb-3 px-4" > Repositorio de Archivos </h1>
      </div>
      <file-board resourceType="office" :resource="{id:1}" :activeTab="'files'"></file-board>
    </div>
  </ModalComponent>
</template>

<script>
import { mapState, mapActions } from 'vuex'
import ModalComponent from './modalComponent.vue'
import fileBoard from './fileBoard.vue'
export default {
  components: {
    ModalComponent,
    fileBoard
  },
  computed: {
    ...mapState({
      currentComponent: state => state.dropdown.currentComponent,
    })
  },
  methods: {
    ...mapActions([
      'showNotification',
      'closeComponent',
    ]),
  }
}
</script>

<style>

</style>