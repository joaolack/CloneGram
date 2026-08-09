<script setup>
import { onMounted, ref } from 'vue'
import api from '@/api/axios'

import PostCard from '@/components/PostCard.vue'
import UserSuggestion from '@/components/UserSuggestion.vue'

const posts = ref([])
const suggestions = ref([])

const loading = ref(true)
const error = ref('')

async function fetchHome() {
  loading.value = true
  error.value = ''

  try {
    const response = await api.get('/home')

    posts.value =
      response.data.posts?.data ??
      response.data.posts ??
      []

    suggestions.value =
      response.data.suggestions?.data ??
      response.data.suggestions ??
      []
  } catch (err) {
    console.error(err)

    error.value =
      err.response?.data?.message ??
      'Não foi possível carregar a Home.'
  } finally {
    loading.value = false
  }
}

function removeSuggestion(userId) {
  suggestions.value = suggestions.value.filter(
    (user) => user.id !== userId
  )
}

onMounted(fetchHome)
</script>

<template>
  <main
    class="
      mx-auto
      w-full
      max-w-6xl
      px-4
      py-8
      pb-24
      md:px-8
      md:pb-8
    "
  >
    <!-- Loading -->
    <div
      v-if="loading"
      class="
        flex
        min-h-[60vh]
        items-center
        justify-center
      "
    >
      <div
        class="
          h-8
          w-8
          animate-spin
          rounded-full
          border-4
          border-gray-200
          border-t-sky-500
        "
      />
    </div>

    <!-- Erro -->
    <div
      v-else-if="error"
      class="
        mx-auto
        max-w-xl
        rounded-xl
        border
        border-red-200
        bg-red-50
        p-4
        text-center
        text-sm
        text-red-700
      "
    >
      {{ error }}
    </div>

    <!-- Home -->
    <div
      v-else
      class="
        grid
        grid-cols-1
        gap-12
        lg:grid-cols-[minmax(0,600px)_300px]
        lg:justify-center
      "
    >
      <!-- Feed -->
      <section class="min-w-0">
        <div
          v-if="posts.length"
          class="space-y-8"
        >
          <PostCard
            v-for="post in posts"
            :key="post.id"
            :post="post"
          />
        </div>

        <!-- Sem posts -->
        <div
          v-else
          class="
            rounded-xl
            border
            border-gray-200
            bg-white
            px-6
            py-16
            text-center
          "
        >
          <h2
            class="
              mb-2
              text-lg
              font-semibold
              text-gray-900
            "
          >
            Nenhuma publicação
          </h2>

          <p class="text-sm text-gray-500">
            Ainda não existem publicações para mostrar.
          </p>

          <RouterLink
            to="/create-post"
            class="
              mt-5
              inline-block
              rounded-lg
              bg-sky-500
              px-5
              py-2.5
              text-sm
              font-semibold
              text-white
              transition
              hover:bg-sky-600
            "
          >
            Criar publicação
          </RouterLink>
        </div>
      </section>

      <!-- Lateral -->
      <aside
        class="
          hidden
          lg:block
        "
      >
        <div class="sticky top-8">
          <div
            class="
              rounded-xl
              bg-white
              p-5
            "
          >
            <div
              class="
                mb-3
                flex
                items-center
                justify-between
              "
            >
              <h2
                class="
                  text-sm
                  font-semibold
                  text-gray-500
                "
              >
                Sugestões para você
              </h2>
            </div>

            <div v-if="suggestions.length">
              <UserSuggestion
                v-for="user in suggestions"
                :key="user.id"
                :user="user"
                @followed="removeSuggestion"
              />
            </div>

            <p
              v-else
              class="
                py-4
                text-sm
                text-gray-400
              "
            >
              Nenhuma sugestão disponível.
            </p>
          </div>

          <!-- Rodapé -->
          <div
            class="
              mt-6
              px-5
              text-xs
              leading-5
              text-gray-400
            "
          >
          </div>
        </div>
      </aside>
    </div>
  </main>
</template>