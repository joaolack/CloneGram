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
  <NavBar v-if="showNavbar"/>

  <RouterView />
</template>
<style scoped></style>
