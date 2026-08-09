<script setup>
import { onMounted, onUnmounted, ref } from 'vue'
import api from '@/api/axios'

const search = ref('')
const users = ref([])

const loading = ref(false)
const error = ref('')

let searchTimeout = null

async function fetchUsers() {
  loading.value = true
  error.value = ''

  try {
    const response = await api.get('/users', {
      params: {
        search: search.value.trim() || undefined,
      },
    })

    users.value = response.data.data ?? []
  } catch (err) {
    console.error(err)

    error.value =
      err.response?.data?.message ??
      'Não foi possível carregar os usuários.'
  } finally {
    loading.value = false
  }
}

function handleSearch() {
  clearTimeout(searchTimeout)

  searchTimeout = setTimeout(() => {
    fetchUsers()
  }, 400)
}

function clearSearch() {
  search.value = ''
  fetchUsers()
}

onMounted(fetchUsers)

onUnmounted(() => {
  clearTimeout(searchTimeout)
})
</script>

<template>
  <main
    class="
      mx-auto
      w-full
      max-w-3xl
      px-4
      py-8
      pb-24
      sm:px-6
      md:pb-8
    "
  >
    <!-- Cabeçalho -->
    <header class="mb-8">
      <h1
        class="
          text-2xl
          font-bold
          text-gray-900
        "
      >
        Pesquisar
      </h1>

      <p
        class="
          mt-1
          text-sm
          text-gray-500
        "
      >
        Encontre pessoas por nome ou nome de usuário.
      </p>
    </header>

    <!-- Busca -->
    <section class="mb-8">
      <div class="relative">
        <!-- Ícone -->
        <div
          class="
            pointer-events-none
            absolute
            inset-y-0
            left-0
            flex
            items-center
            pl-4
            text-gray-400
          "
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.8"
            stroke="currentColor"
            class="h-5 w-5"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="m21 21-4.35-4.35m2.1-5.4a7.5 7.5 0 1 1-15 0 7.5 7.5 0 0 1 15 0Z"
            />
          </svg>
        </div>

        <input
          v-model="search"
          type="search"
          placeholder="Pesquisar"
          autocomplete="off"
          class="
            w-full
            rounded-xl
            border
            border-gray-200
            bg-gray-100
            py-3.5
            pl-12
            pr-12
            text-sm
            text-gray-900
            outline-none
            transition
            placeholder:text-gray-400
            focus:border-gray-300
            focus:bg-white
            focus:ring-2
            focus:ring-sky-100
          "
          @input="handleSearch"
        />

        <!-- Limpar -->
        <button
          v-if="search"
          type="button"
          class="
            absolute
            inset-y-0
            right-0
            flex
            items-center
            pr-4
            text-gray-400
            transition
            hover:text-gray-700
          "
          @click="clearSearch"
        >
          <span
            class="
              flex
              h-5
              w-5
              items-center
              justify-center
              rounded-full
              bg-gray-300
              text-xs
              font-bold
              text-white
            "
          >
            ×
          </span>
        </button>
      </div>
    </section>

    <!-- Erro -->
    <div
      v-if="error"
      class="
        mb-6
        rounded-xl
        border
        border-red-200
        bg-red-50
        p-4
        text-sm
        text-red-700
      "
    >
      {{ error }}
    </div>

    <!-- Resultados -->
    <section
      class="
        overflow-hidden
        rounded-xl
        border
        border-gray-200
        bg-white
      "
    >
      <header
        class="
          border-b
          border-gray-100
          px-5
          py-4
        "
      >
        <h2
          class="
            text-sm
            font-semibold
            text-gray-900
          "
        >
          {{
            search.trim()
              ? 'Resultados'
              : 'Pessoas'
          }}
        </h2>
      </header>

      <!-- Loading -->
      <div
        v-if="loading"
        class="
          flex
          items-center
          justify-center
          py-16
        "
      >
        <div
          class="
            h-7
            w-7
            animate-spin
            rounded-full
            border-4
            border-gray-200
            border-t-sky-500
          "
        />
      </div>

      <!-- Lista -->
      <div v-else-if="users.length">
        <RouterLink
          v-for="user in users"
          :key="user.id"
          :to="`/users/${user.username}`"
          class="
            flex
            items-center
            gap-4
            border-b
            border-gray-100
            px-5
            py-4
            transition
            last:border-b-0
            hover:bg-gray-50
          "
        >
          <!-- Avatar -->
          <div
            class="
              flex
              h-14
              w-14
              shrink-0
              items-center
              justify-center
              overflow-hidden
              rounded-full
              bg-gray-200
            "
          >
            <img
              v-if="user.avatar_url"
              :src="user.avatar_url"
              :alt="user.username"
              class="
                h-full
                w-full
                object-cover
              "
            />

            <span
              v-else
              class="
                text-lg
                font-semibold
                uppercase
                text-gray-500
              "
            >
              {{ user.username?.charAt(0) }}
            </span>
          </div>

          <!-- Usuário -->
          <div class="min-w-0 flex-1">
            <p
              class="
                truncate
                text-sm
                font-semibold
                text-gray-900
              "
            >
              {{ user.username }}
            </p>

            <p
              class="
                truncate
                text-sm
                text-gray-500
              "
            >
              {{ user.name }}
            </p>

            <p
              v-if="user.bio"
              class="
                mt-1
                line-clamp-1
                text-xs
                text-gray-400
              "
            >
              {{ user.bio }}
            </p>
          </div>

          <!-- Seta -->
          <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.8"
            stroke="currentColor"
            class="
              h-5
              w-5
              shrink-0
              text-gray-300
            "
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="m9 18 6-6-6-6"
            />
          </svg>
        </RouterLink>
      </div>

      <!-- Sem resultados -->
      <div
        v-else
        class="
          flex
          flex-col
          items-center
          justify-center
          px-6
          py-16
          text-center
        "
      >
        <div
          class="
            mb-4
            flex
            h-14
            w-14
            items-center
            justify-center
            rounded-full
            bg-gray-100
            text-gray-400
          "
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor"
            class="h-7 w-7"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="m21 21-4.35-4.35m2.1-5.4a7.5 7.5 0 1 1-15 0 7.5 7.5 0 0 1 15 0Z"
            />
          </svg>
        </div>

        <h3
          class="
            text-base
            font-semibold
            text-gray-900
          "
        >
          Nenhum usuário encontrado
        </h3>

        <p
          class="
            mt-1
            text-sm
            text-gray-500
          "
        >
          Tente pesquisar outro nome ou username.
        </p>
      </div>
    </section>
  </main>
</template>