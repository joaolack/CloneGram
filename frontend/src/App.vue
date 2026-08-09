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
  <div :class="{ 'min-h-screen bg-gray-50 pt-16 md:pl-64 md:pt-0': showNavbar }">
    <NavBar v-if="showNavbar"/>

    <RouterView />
  </div>  
</template>
<style scoped></style>
