<script setup>
import { computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { Home, Search, SquarePlus, SquareArrowRightExit } from 'lucide-vue-next'

const route = useRoute()
const router = useRouter()
const auth = useAuthStore()

const profileInitial = computed(() => {
  const label = auth.user?.username || auth.user?.name || ''

  return label.charAt(0) || '?'
})

function isActive(name) {
  return route.name === name
}

onMounted(async () => {
  if (!auth.token || auth.user) return

  try {
    await auth.fetchUser()
  } catch (error) {
    console.error('Erro ao carregar usuario:', error)
  }
})

async function logout() {
  try {
    await auth.logout()
  } finally {
    await router.replace('/login')
  }
}
</script>

<template>
  <header
    class="
      fixed
      left-0
      right-0
      top-0
      z-40
      flex
      h-16
      items-center
      gap-4
      border-b
      border-gray-200
      bg-white
      px-4
      md:hidden
    "
  >
    <RouterLink
      to="/"
      class="shrink-0 font-logo text-2xl text-gray-900"
    >
      <strong>CloneGram</strong>
    </RouterLink>
    <RouterLink
      to="/search"
      class="
        ml-auto
        flex
        h-10
        min-w-0
        max-w-[190px]
        flex-1
        items-center
        gap-2
        rounded-full
        bg-gray-100
        px-4
        text-sm
        text-gray-500
      "
    >
      <Search class="h-4 w-4 shrink-0" />
      <span class="truncate">Pesquisar</span>
    </RouterLink>


  </header>

  <aside
    class="fixed bottom-0 left-0 top-0 z-40 hidden h-screen w-64 flex-col border-r
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
        <Home class="w-6 h-6" />
        <span>Home</span>
      </RouterLink>

      <RouterLink
        to="/search"
        class="nav-item"
        :class="{ 'bg-gray-100 font-semibold': isActive('search') }"
      >
        <Search class="w-6 h-6" />
        <span>Pesquisar</span>
      </RouterLink>

      <RouterLink
        to="/create-post"
        class="nav-item"
        :class="{ 'bg-gray-100 font-semibold': isActive('create-post') }"
      >
        <SquarePlus class="w-6 h-6" />
        <span>Criar</span>
      </RouterLink>

      <RouterLink
        to="/profile"
        class="nav-item"
        :class="{ 'bg-gray-100 font-semibold': isActive('my-profile') }"
      >
        <span
          class="nav-avatar h-7 w-7"
          :class="{ 'ring-2 ring-gray-900 ring-offset-1': isActive('my-profile') }"
        >
          <img
            v-if="auth.user?.avatar_url"
            :src="auth.user.avatar_url"
            :alt="auth.user.username ?? 'Perfil'"
            class="h-full w-full object-cover"
          />

          <span
            v-else
            class="text-xs font-semibold uppercase"
          >
            {{ profileInitial }}
          </span>
        </span>
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
        <SquareArrowRightExit class="w-6 h-6"/>
        <span>Sair</span>
    </button>
  </aside>


  <nav class="fixed bottom-0 left-0 right-0 z-40 flex items-center justify-around border-t border-gray-200 bg-white px-2 py-2 md:hidden">
    <RouterLink
      to="/"
      class="mobile-nav-item"
      :class="{ 'font-bold text-black': isActive('home') }"
    >
      <Home class="w-6 h-6" />
    </RouterLink>

    <RouterLink
      to="/search"
      class="mobile-nav-item"
      :class="{ 'font-bold text-black': isActive('search') }"
    >
      <Search class="w-6 h-6" />
    </RouterLink>

    <RouterLink
      to="/create-post"
      class="mobile-nav-item"
      :class="{ 'font-bold text-black': isActive('create-post') }"
    >
      <SquarePlus class="w-6 h-6" />
    </RouterLink>

    <RouterLink
      to="/profile"
      class="mobile-nav-item"
      :class="{ 'font-bold text-black': isActive('my-profile') }"
    >
      <span
        class="nav-avatar h-8 w-8"
        :class="{ 'ring-2 ring-gray-900 ring-offset-1': isActive('my-profile') }"
      >
        <img
          v-if="auth.user?.avatar_url"
          :src="auth.user.avatar_url"
          :alt="auth.user.username ?? 'Perfil'"
          class="h-full w-full object-cover"
        />

        <span
          v-else
          class="text-xs font-semibold uppercase"
        >
          {{ profileInitial }}
        </span>
      </span>
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

.nav-avatar {
  @apply flex shrink-0 items-center justify-center overflow-hidden rounded-full bg-gray-200 text-gray-600;
}
</style>
