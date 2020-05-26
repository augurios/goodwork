<template>
<div class="sticky top-0 z-40">
  <div class="h-1" style="background: linear-gradient(to right, #8497EC, #3958E3);"></div>
  <nav class="bg-white h-12 border-b">
    <div class="sm:container sm:mx-auto px-4 sm:max-w-xl md:max-w-3xl lg:max-w-5xl xl:max-w-6xl flex flex-row justify-between h-full">
      <a class="flex items-center text-indigo-500 text-2xl font-bold md:font-normal no-underline h-12" :href="url.site">
          <img src="/image/RET-MT01-logos-01.jpg" alt="Admuni Logo" class="w-9 h-9 md:block">
      </a>

      <div class="md:flex">
        <div class="flex flex-row h-full">
          <notification-dropdown></notification-dropdown>
          <message-dropdown></message-dropdown>
          <profile-dropdown></profile-dropdown>
        </div>
      </div>
    </div>
  </nav>
</div>
</template>

<script>
  import notificationDropdown from './notificationDropdown'
  import messageDropdown from './messageDropdown'
  import profileDropdown from './profileDropdown'
  export default {
    components: {notificationDropdown, messageDropdown, profileDropdown},
    data: () => ({
      token: Laravel.csrfToken,
      url: navUrl
    })
  }
</script>
