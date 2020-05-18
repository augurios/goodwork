<template>
  <div :class="{ calling: calling }">
    <div class="text-2xl text-gray-700 text-center font-semibold px-8 py-6">
          <h1 class="w-full bg-gray-100 text-gray-800 text-center rounded py-3 px-4" > {{ isEdit ? 'Edit ' : 'Create' | localize }} {{ 'Provider' | localize }} </h1>
    </div>
    <div class="bg-white flex flex-row flex-wrap justify-between lg:flex-no-wrap pt-4">
        <!-- Proveedor -->
        <div>
          <label class="text-sm text-gray-600 px-8" for="link-title">
            {{'Proveedor' | localize }}
          </label>
          <div class="flex flex-row items-center mx-8 py-2 relative">
            
            <input v-model="newLink.title" class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" id="link-title" type="text" placeholder="Nombre o Marca" />
            
          </div>
        </div>
        <!-- URL -->
        <div>
          <label class="text-sm text-gray-600 px-8" name="link-url">
            {{ 'URL' | localize }}
          </label>
          <div class="flex flex-row items-center mx-8 py-2 relative">
            <input v-model="newLink.url" class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" id="link-url" type="text" placeholder="Sitio Web" />
          </div>
        </div>
        <!-- Phone -->
        <div>
          <label class="text-sm text-gray-600 px-8" for="cycle">
              {{ 'Phone' | localize }}
          </label>
          <div class="flex flex-row items-center mx-8 py-2 relative">
            <input v-model="newLink.phone" class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" id="link-title" type="text" placeholder="Telefono" />
          </div>
        </div>
        <!-- Email -->
        <div>
          <label class="text-sm text-gray-600 px-8" for="cycle">
              {{ 'Email' | localize }}
          </label>
          <div class="flex flex-row items-center mx-8 py-2 relative">
            <input v-model="newLink.email" class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" id="link-title" type="email" placeholder="Correo" />
          </div>
        </div>
      </div>
      <div class="px-8 py-4 bg-white">
        <label class="text-sm text-gray-600" for="description">
          {{ 'Notes' | localize }}
        </label>
        <div class="py-2">
          <textarea v-model="newLink.description" class="appearance-none block w-full text-gray-800 border border-gray-200 rounded py-3 px-4 resize-none" :placeholder="$options.filters.localize('Description')" name="description" rows="2"></textarea>
        </div>
      </div>
      <div v-if="enabled" class="px-8 py-4 bg-white">
        <LinkTags :tags="newLink.tags" />
      </div>
      <div class="flex flex-row justify-end py-6 px-8 bg-gray-100 rounded">
        <button @click="closeLinkForm" class="border border-gray-400 bg-white text-gray-700 font-medium py-2 px-4 mr-4 rounded">{{ 'Cancel' | localize }}</button>
       
        <button v-show="!isEdit" @click="AddNewLink" :disabled="newLink.tags.length === 0" class="bg-indigo-600 text-white font-medium py-2 px-4 rounded">{{ 'Add' | localize }}</button>
        <button v-show="isEdit" @click="SaveNewLink" :disabled="newLink.tags.length === 0" class="bg-indigo-600 text-white font-medium py-2 px-4 rounded">{{ 'Save' | localize }}</button>
      </div>
  </div>
</template>

<script>
import { mapActions } from 'vuex';
import LinkTags from './linkTags.vue';
export default {
  components:{
    LinkTags
  },
  props: {
    enabled: {
      type: Boolean,
      required: true
    },
    newLink: {
      type: Object,
      required: true,
    },
    closeLinkForm: {
      type: Function,
      required: true,
    },
    isEdit: {
      type: Boolean,
      required: true
    }
  },
  data: () => ({
    calling: false
  }),
  methods: {
    ...mapActions([
      'showNotification',
    ]),
    AddNewLink() {  
      this.calling = true;
      let tags = [];
      this.newLink.tags.forEach(tag => {
        tags.push(tag.id);
      });
      const payload = {
        ...this.newLink,
        tags
      }    
      axios.post('/links',payload)
        .then((response) => {
          this.showNotification({type: response.data.status, message: response.data.message})
          this.closeLinkForm();
          this.calling = false;
        })
        .catch((error, resp) => {
          this.calling = false;
          console.log(error, resp)
          this.showNotification({type: error.response.data.status, message: error.response.data.message})
        });
    },
    SaveNewLink() {
      this.calling = true;
      let tags = [];
      this.newLink.tags.forEach(tag => {
        tags.push(tag.id);
      });
      const payload = {
        ...this.newLink,
        tags
      }
      axios.put('/links',payload)
        .then((response) => {
          this.showNotification({type: response.data.status, message: response.data.message})
          this.closeLinkForm();
          this.calling = false;
        })
        .catch((error, resp) => {
          this.calling = false;
          console.log(error, resp)
          this.showNotification({type: error.response.data.status, message: error.response.data.message})
        })
    },
  }
}
</script>

<style>
.calling {
  opacity: 0.5;
  pointer-events: none;
}
button:disabled {
  opacity: 0.5;
}
</style>