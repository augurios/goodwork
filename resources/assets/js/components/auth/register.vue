<template>
<div class="md:bg-white container md:mx-4 md:mx-auto md:shadow-lg rounded md:mt-32 flex md:flex-row flex-col md:max-w-2xl lg:max-w-4xl justify-between">
  <div class="md:w-1/2 text-center px-8 py-4 md:block">
   <img src="/image/RET-MT01-logos-01.jpg" alt="Admuni Logo" class="pt-8 hidden md:block">
    <p class="text-indigo-500 font-bold text-4xl md:pt-8">AdMuni</p>
    <p class="text-indigo-500 text-xl"> Enfoque sensible al trabajo y colaboración para desarrollo local </p>
  </div>
  <div class=""></div>
  <div class="w-full md:bg-indigo-100 md:w-1/2 p-8">
    <form role="form" method="POST" :action="url">
      <input type="hidden" name="_token" :value="token">
      <div class="">
        <p class="py-4">
          <input id="name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-800" type="text" name="name" placeholder="Nombre Y Apellido">
          <span class="text-red-700 block pt-2" v-if="errors">{{ errors.name | localize }}</span>
        </p>
        <p class="py-4">
          <input id="username" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-800" type="text" name="username" placeholder="Nombre De Usuario">
          <span class="text-red-700 block pt-2" v-if="errors">{{ errors.username | localize }}</span>
        </p>
        <p class="py-4">
          <input id="location" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-800" type="text" name="location" placeholder="Municipalidad">
          <span class="text-red-700 block pt-2" v-if="errors">{{ errors.location | localize }}</span>
        </p>
        <p class="py-4">
          <input id="designation" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-800" type="text" name="designation" placeholder="Puesto/Departamento">
          <span class="text-red-700 block pt-2" v-if="errors">{{ errors.designation | localize }}</span>
        </p>
        <p class="py-4">
          <input id="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-800" type="text" name="email" placeholder="Correo Electronico">
          <span class="text-red-700 block pt-2" v-if="errors">{{ errors.email | localize }}</span>
        </p>
        <p class="py-4">
          <input id="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-800" type="password" name="password" placeholder="Contraseña">
          <span class="text-red-700 block pt-2" v-if="errors">{{ errors.password | localize }}</span>
        </p>
        <p class="py-4">
          <button type="submit" class="bg-indigo-500 hover:bg-indigo-600 text-white text-xl font-medium py-2 px-4 rounded">{{ 'Registrarse' | localize }}</button>
        </p>
      </div>
    </form>
  </div>
</div>
</template>

<script>
    export default {
      data: () => ({
        token: Laravel.csrfToken,
        errors: errors,
        url: url
      })
    }
</script>
