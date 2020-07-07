<template>
  <div id="home-container" v-if="activeTab === 'home'">
    <div class="bg-white rounded-lg shadow p-8">
      <div class="text-xl">
        {{ 'What\'s on your plate today' | localize }} ⬇️
      </div>
      <div v-if="currentWork.length === 0" class="mt-4">{{ 'There is currently no work "In Progress" assigned to you' | localize }}</div>
      <a v-if="currentWork.length !== 0" :href="'/' + currentWork[0].taskable_type + 's/' + currentWork[0].taskable_id + '?tool=tasks&id=' + currentWork[0].id" class="block mt-4 p-4 rounded-lg bg-gray-100 border-2 border-indigo-300 cursor-pointer">
        <div class="flex items-center">
          <div class="pr-2 text-sm">{{ 'Due on' | localize }}: </div>
          <div class="text-sm rounded-full px-3 py-1 bg-indigo-200 text-indigo-700 font-semibold inline">
            {{ dueOn(currentWork[0].due_on) }}
          </div>
        </div>
        <div class="text-2xl font-medium">
          {{ currentWork[0].name }}
        </div>
        <div class="text-lg">
          {{ currentWork[0].notes }}
        </div>
        <div class="py-4">
          <div class="text-lg font-medium border-b-2 pb-1">
            {{ 'Progress' | localize }}
          </div>
          <!-- Progress list -->
          <div v-if="currentWork.length !== 0 && currentWork[0].steps.length === 0" class="pt-2">
            {{ 'No progress update yet' | localize }}
          </div>
          <div v-for="step in currentWork[0].steps" class="bg-white rounded shadow px-4 py-2 my-4">
            <div class="text-xs text-indigo-700">
              <span class="text-gray-700">{{ 'Last updated' | localize }}:</span> {{ step.updated_at }}
            </div>
            <div class="text-2xl">
              {{ step.description }}
            </div>
            <div class="flex">
              <div class="py-2 pr-4 text-gray-700">
                <input type="checkbox" :id="'step-done-' + step.id" class="checkbox" :checked="step.done">
                <label :for="'step-done-' + step.id">{{ 'Done' | localize }}</label>
              </div>
              <div class="py-2 text-gray-700">
                <input type="checkbox" :id="'step-unknown-' + step.id" class="checkbox" :checked="step.unknown">
                <label :for="'step-unknown-' + step.id">{{ 'Unknown' | localize }}</label>
              </div>
            </div>
          </div>
        </div>
      </a>
      <div v-if="currentWork.length !== 0" class="pt-8 flex items-center pb-4">
        <div class="text-base mr-2">
          {{ 'Other tasks in progress' | localize }}
        </div>
        <div class="text-xs font-bold bg-indigo-600 text-white flex items-center justify-center w-6 h-6 rounded-full">{{ currentWork.length - 1 }}</div>
      </div>
      <a :href="'/' + task.taskable_type + 's/' + task.taskable_id + '?tool=tasks&id=' + task.id" v-if="index !== 0" v-for="(task, index) in currentWork" class="block bg-gray-100 rounded shadow px-4 py-2 my-4">
        <div class="text-blue-500 text-sm cursor-pointer">
          {{ task.taskable.name }}
        </div>
        <div class="text-lg">
          {{ task.name }}
        </div>
      </a>
    </div>
    <div class="h-16"></div>

    <ModalComponent :showModal="showModal" @close="toggleWelcome"> 
       <div class="text-2xl text-gray-700 text-center font-semibold px-8 pb-6">
          <h1 class="w-full bg-gray-100 text-gray-800 text-center rounded pb-3 px-4" > Bienvenid@ a AdMuni Beta Version 0.1</h1>
      </div>
      <div class="bg-white p-8">
        <p class="text-center mb-4">&iexcl;Informaci&oacute;n importante!</p>
        <p class="mb-4"><strong>AdMuni</strong> es una aplicaci&oacute;n para colaboraci&oacute;n laboral entre gobiernos locales. Actualmente en desarrollo, las caracteristicas y funcionalidades actuales podrian cambiar, la informacion agregada a esta version no sera restaurada.</p>
        <p class="mb-4">Para reportar errores o enviar sugerencias puede agregar un comentario en la tarea respectiva en el proyecto "<a href="/?group_type=project&group_id=2&tool=tasks" class="text-indigo-700">Admuni desarrollo</a>"</p>
        <p class="mb-4">Cualquier duda puede enviar un mensaje al usuario "Admin".</p>
        <p>&nbsp;&nbsp;</p>
      </div>
      <div class="flex flex-row justify-end pt-6 px-8 bg-gray-100 rounded">
        <button @click="toggleWelcome" class="border border-gray-400 bg-white text-gray-700 font-medium py-2 px-4 mr-4 rounded">Cerrar</button>
      </div>
    </ModalComponent>

  </div>
</template>

<script>
import { mapState } from 'vuex';
import ModalComponent from './../partials/modalComponent'

export default {
  components: {
    ModalComponent
  },
  props: {
    activeTab: {
      required: true,
      type: String
    }
  },

  data() {
    return {
      showModal: true,
    }
  },

  computed: {
    ...mapState({
      currentWork: state => state.currentWork
    })
  },

  methods: {
    dueOn: function (value) {
      return luxon.DateTime.fromSQL(value).toFormat('d LLL')
    },
    toggleWelcome() {
      this.showModal = !this.showModal;
    }
  }
}
</script>

<style>

</style>
