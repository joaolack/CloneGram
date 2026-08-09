<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import NavBar from './components/NavBar.vue';

const route = useRoute()

const showNavbar = computed(() => {
  return route.meta.requiresAuth === true
})

</script>
<template>
  <div :class="{'min-h-screen bg-gray-50': showNavbar}">
    <NavBar v-if="showNavbar"/>

    <main
      class="
        pt-16
        pb-16
        md:ml-64
        md:pt-0
        md:pb-0
      "
    >
      <RouterView />
    </main>
  </div>  
</template>
<style scoped></style>
