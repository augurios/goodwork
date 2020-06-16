<template>
  <ModalComponent :showModal="show" @close="toggleEventForm"> 
       <div class="text-2xl text-gray-700 text-center font-semibold px-8 pb-6">
          <h1 class="w-full bg-gray-100 text-gray-800 text-center rounded pb-3 px-4" > Evento <span v-if="event.id">{{event.title}}</span> </h1>
      </div>
      <div class="bg-white flex flex-row flex-wrap justify-between lg:flex-no-wrap pt-4">
        <!-- Proveedor -->
        <div>
          <label class="text-sm text-gray-600 px-8" for="link-title">
            Asunto
          </label>
          <div class="flex flex-row items-center mx-8 py-2 relative">
            
            <input v-model="event.title" class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" id="link-title" type="text" placeholder="Asunto del evento" />
            
          </div>
        </div>
        <!-- URL -->
        <div>
          <label class="text-sm text-gray-600 px-8" name="link-url">
            {{ 'URL' | localize }}
          </label>
          <div class="flex flex-row items-center mx-8 py-2 relative">
            <input v-model="event.url" class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" id="link-url" type="url" placeholder="Sitio Web" />
          </div>
        </div>

        <div>
          <label class="text-sm text-gray-600 px-8" name="link-url">
            Etiqueta (Opcional)
          </label>
        <div class="flex flex-row items-center mx-8 py-2 relative">
            <select class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" style="min-width: 14rem;" v-model="event.classes">
              <option value="" disabled>Seleccione una</option>
              <option :value="tag.value" v-for="tag in tags" :key="tag.label">{{ tag.label }}</option>
            </select>
            <font-awesome-icon :icon="faChevronDown"
              class="w-1/6 pointer-events-none flex items-center text-gray-700 -ml-8">
            </font-awesome-icon>
          </div>
      </div>
       
      </div>
      <div class="bg-white flex flex-row flex-wrap justify-between lg:flex-no-wrap py-4">
         <!-- Phone -->
        <div>
          <label class="text-sm text-gray-600 px-8" for="link-phone">
              Fecha De Inicio(requerido)
          </label>
          <div class="flex flex-row items-center mx-8 py-2 relative">
            <input v-model="event.startDate" v-on:change="function(){event.endDate = event.startDate}" class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" id="link-phone" type="date" />
          </div>
        </div>
        <div>
          <label class="text-sm text-gray-600 px-8" for="starthour">
              Hora
          </label>
          <div class="flex flex-row items-center mx-8 py-2 relative">
            <input v-model="event.startTime" class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" id="starthour" type="time" />
          </div>
        </div>
        <!-- Email -->
        <div>
          <label class="text-sm text-gray-600 px-8" for="link-email">
              Fecha Final(requerido)
          </label>
          <div class="flex flex-row items-center mx-8 py-2 relative">
            <input v-model="event.endDate" v-on:change="updateStart" class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" id="link-email" type="date" />
          </div>
        </div>
        <div>
          <label class="text-sm text-gray-600 px-8" for="endthour">
              Hora
          </label>
          <div class="flex flex-row items-center mx-8 py-2 relative">
            <input v-model="event.endTime" class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" id="endthour" type="time" />
          </div>
        </div>
        <div>
          <label class="text-sm text-gray-600 pr-8" for="endthour">
               Recurrente?
          </label>         
          <div class="relative rounded-full w-12 h-6 transition duration-200 ease-linear mt-2"
            :class="[event.recurent ? 'bg-green-400' : 'bg-gray-400']">
            <label for="toggle"
                  class="absolute left-0 bg-white border-2 mb-2 w-6 h-6 rounded-full transition transform duration-100 ease-linear cursor-pointer"
                  :class="[event.recurent ? 'translate-x-full border-green-400' : 'translate-x-0 border-gray-400']"></label>
            <input type="checkbox" id="toggle" name="toggle" v-model="event.recurent"
                  class="appearance-none w-full h-full active:outline-none focus:outline-none"
                  />
          </div>
        </div>
      </div>
      


  
      
      <div class="flex flex-row justify-end pt-6 px-8 bg-gray-100 rounded">
        <button @click="toggleEventForm" class="border border-gray-400 bg-white text-gray-700 font-medium py-2 px-4 mr-4 rounded">Cerrar</button>
        <button @click="deleteEvent" v-if="event.id" class="border border-red-600 bg-red-600 text-white font-medium py-2 px-4 mr-4 rounded">Eliminar</button>
        
        <baseButton  @click="saveForm" :btnDisabled="event.title.length === 0 || !event.endDate || !event.startDate" btnType="primary">Guardar</baseButton>
      </div>
    </ModalComponent>
</template>

<script>
import { faChevronDown,faTimesCircle } from '@fortawesome/free-solid-svg-icons'
import ModalComponent from './../modalComponent.vue'
export default {
  components: {
    ModalComponent
  },
  props: {
    show: {
      type: Boolean,
      required: true
    },
    event: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      faChevronDown,
      faTimesCircle,
      tags: [
        {
          label: 'Importante',
          value: 'orange'
        },
        {
          label: 'Cumpleaños',
          value: 'purple'
        },
        {
          label: 'Feriado',
          value: 'green'
        }
      ]
    }
  },
  methods: {
    toggleEventForm() {
      this.$emit('closeForm');
    },
    saveForm() {
      this.$emit('saveEvent');
    },
    updateStart() {
      if(new Date(this.event.endDate) < new Date(this.event.startDate)) {
        this.event.startDate = this.event.endDate;
      }      
    },
    deleteEvent() {
      this.$emit('delete')
    }
  }
}
</script>

<style>
.theme-default .cv-event.green {
  background-color: aquamarine;
}
</style>