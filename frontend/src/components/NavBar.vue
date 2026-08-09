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
  <aside
    class="fixed left-0 top-0 z-40 hidden h-screen w-64 flex-col border-r
      border-gray-200 bg-white px-4 py-8 md:flex"
  >
    <RouterLink
      to="/"
      class="mb-10 px-3 text-2xl font-bold tracking-tight text-gray-900">
      CloneGram
    </RouterLink>

    <nav class="flex flex-1 flex-col gap-2">
      <RouterLink
        to="/"
        class="nav-item"
        :class="{ 'bg-gray-100 font-semibold': isActive('home') }"
      >
        <span class="text-xl">⌂</span>
        Home
      </RouterLink>

      <RouterLink
        to="/search"
        class="nav-item"
        :class="{ 'bg-gray-100 font-semibold': isActive('search') }"
      >
        <span class="text-xl">⌕</span>
        Pesquisar
      </RouterLink>

      <RouterLink
        to="/create-post"
        class="nav-item"
        :class="{ 'bg-gray-100 font-semibold': isActive('create-post') }"
      >
        <span class="text-xl">＋</span>
        Criar
      </RouterLink>

      <RouterLink
        to="/profile"
        class="nav-item"
        :class="{ 'bg-gray-100 font-semibold': isActive('my-profile') }"
      >
        <span class="text-xl">○</span>
        Perfil
      </RouterLink>
    </nav>

    <button
      type="button"
      class="
        flex
        w-full
        items-center
        gap-4
        rounded-lg
        px-3
        py-3
        text-left
        text-gray-700
        transition
        hover:bg-gray-100
      "
      @click="logout"
    >
      <span class="text-xl">↪</span>

      Sair
    </button>
  </aside>


  <nav class="fixed bottom-0 left-0 right-0 z-40 flex items-center justify-around border-t border-gray-200 bg-white px-2 py-2 md:hidden">
    <RouterLink
      to="/"
      class="mobile-nav-item"
      :class="{ 'font-bold text-black': isActive('home') }"
    >
      <span class="text-2xl">⌂</span>
    </RouterLink>

    <RouterLink
      to="/search"
      class="mobile-nav-item"
      :class="{ 'font-bold text-black': isActive('search') }"
    >
      <span class="text-2xl">⌕</span>
    </RouterLink>

    <RouterLink
      to="/create-post"
      class="mobile-nav-item"
      :class="{ 'font-bold text-black': isActive('create-post') }"
    >
      <span class="text-2xl">＋</span>
    </RouterLink>

    <RouterLink
      to="/profile"
      class="mobile-nav-item"
      :class="{ 'font-bold text-black': isActive('my-profile') }"
    >
      <span class="text-2xl">○</span>
    </RouterLink>
  </nav>
</template>

<style scoped>
@reference "tailwindcss";

.nav-item {
  @apply flex items-center gap-4 rounded-lg px-3 py-3 text-gray-700 transition hover:bg-gray-100;
}

.mobile-nav-item {
  @apply flex items-center justify-center rounded-lg p-2 text-gray-600;
}
</style>