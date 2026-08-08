<script setup>
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const route = useRoute()
const router = useRouter()
const auth = useAuthStore()

function isActive(name) {
  return route.name === name
}

async function logout() {
  try {
    await auth.logout()
  } finally {
    await router.replace('/login')
  }
}
</script>

<template>
  <nav>
    <RouterLink to="/">
      Instagram Clone
    </RouterLink>

    <div>
      <RouterLink
        to="/"
        :class="{ active: isActive('home') }"
      >
        Home
      </RouterLink>

      <RouterLink
        to="/search"
        :class="{ active: isActive('search') }"
      >
        Search
      </RouterLink>

      <RouterLink
        to="/create-post"
        :class="{ active: isActive('create-post') }"
      >
        Criar
      </RouterLink>

      <RouterLink
        to="/profile"
        :class="{ active: isActive('my-profile') }"
      >
        Profile
      </RouterLink>

      <button
        type="button"
        @click="logout"
      >
        Sair
      </button>
    </div>
  </nav>
</template>

<style scoped>
nav {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem;
  border-bottom: 1px solid #ddd;
}

nav div {
  display: flex;
  gap: 1rem;
  align-items: center;
}

a {
  text-decoration: none;
}

.active {
  font-weight: bold;
  text-decoration: underline;
}
</style>