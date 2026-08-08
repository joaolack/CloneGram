<script setup>
import { onMounted, ref } from 'vue'
import api from '@/api/axios'

import PostCard from '@/components/PostCard.vue';
import UserSuggestion from '@/components/UserSuggestion.vue';

const posts = ref([])
const suggestions = ref([])

const loading = ref(true)
const error = ref('')

async function fetchHome() {
  loading.value = true
  error.value = ''

  try {
    const response = await api.get('/home')

    console.log('Home:', response.data)

    posts.value = 
      response.data.posts?.data ??
      response.data.posts ??
      []
    
    suggestions.value =
      response.data.suggestions?.data ??
      response.data.suggestions ??
      []
  } catch (err) {
    console.log(err)

    error.value =
      err.response?.data?.message ??
      'Não foi possível carregar a Home.'
  } finally {
    loading.value = false
  }
}

function removeSuggestion(userId) {
  suggestions.value =
    suggestions.value.filter(
      (user) => user.id !== userId
    )
}

onMounted(fetchHome)
</script>

<template>
  <main>
    <h1>Home</h1>

    <p v-if="loading">
      Carregando...
    </p>

    <p v-else-if="error">
      {{ error }}
    </p>

    <template v-else>
      <section>
          <h2>Sugestões para você</h2>

          <UserSuggestion
            v-for="user in suggestions"
            :key="user.id"
            :user="user"
            @followed="removeSuggestion"
          />
          <p v-if="suggestions.length === 0">
            Nenhuma sugestão disponível.
          </p>
      </section>

      <section>
        <h2>Publicações</h2>

        <PostCard
          v-for="post in posts"
          :key="post.id"
          :post="post"
        />

        <p v-if="posts.length === 0">
          Nenhuma publicação disponível.
        </p>
      </section>
    </template>
  </main>
</template>