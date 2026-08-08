<script setup>
import { onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/api/axios'

const route = useRoute()

const post = ref(null)
const loading = ref(true)
const error = ref('')

const newComment = ref('')
const commentLoading = ref(false)
const commentError = ref('')

const likeLoading = ref(false)

async function fetchPost() {
  loading.value = true
  error.value = ''

  try {
    const response = await api.get(
      `/posts/${route.params.id}`
    )

    post.value = response.data.data ?? response.data
  } catch (err) {
    console.error(err)

    error.value =
      err.response?.data?.message ??
      'Não foi possível carregar o post.'
  } finally {
    loading.value = false
  }
}

async function toggleLike() {
  if (likeLoading.value) return

  likeLoading.value = true

  try {
    if (post.value.liked_by_me) {
      await api.delete(
        `/posts/${post.value.id}/like`
      )

      post.value.liked_by_me = false
      post.value.likes_count--
    } else {
      await api.post(
        `/posts/${post.value.id}/like`
      )

      post.value.liked_by_me = true
      post.value.likes_count++
    }
  } catch (err) {
    console.error('Erro ao alterar like:', err)
  } finally {
    likeLoading.value = false
  }
}

async function addComment() {
  commentError.value = ''

  const content = newComment.value.trim()

  if (!content) {
    return
  }

  commentLoading.value = true

  try {
    const response = await api.post(
      `/posts/${post.value.id}/comments`,
      {
        content,
      }
    )

    const comment =
      response.data.data ?? response.data

    post.value.comments.push(comment)

    post.value.comments_count++

    newComment.value = ''
  } catch (err) {
    console.error(err)

    commentError.value =
      err.response?.data?.errors?.content?.[0] ??
      err.response?.data?.message ??
      'Não foi possível adicionar o comentário.'
  } finally {
    commentLoading.value = false
  }
}

onMounted(fetchPost)
</script>

<template>
  <main>
    <p v-if="loading">
      Carregando...
    </p>

    <p v-else-if="error">
      {{ error }}
    </p>

    <article v-else-if="post">
      <!-- Autor -->
      <header>
        <RouterLink
          :to="`/users/${post.author.username}`"
        >
          <strong>
            {{ post.author.username }}
          </strong>
        </RouterLink>
      </header>

      <!-- Mídia -->
      <img
        v-if="post.media_type === 'image'"
        :src="post.media_url"
        :alt="post.caption || 'Publicação'"
      />

      <video
        v-else-if="post.media_type === 'video'"
        :src="post.media_url"
        controls
      />

      <!-- Like -->
      <section>
        <button
          type="button"
          :disabled="likeLoading"
          @click="toggleLike"
        >
          {{ post.liked_by_me ? '♥' : '♡' }}
        </button>

        <span>
          {{ post.likes_count }} curtidas
        </span>
      </section>

      <!-- Legenda -->
      <p v-if="post.caption">
        <strong>
          {{ post.author.username }}
        </strong>

        {{ post.caption }}
      </p>

      <!-- Comentários -->
      <section>
        <h2>
          Comentários ({{ post.comments_count }})
        </h2>

        <div
          v-for="comment in post.comments"
          :key="comment.id"
        >
          <RouterLink
            :to="`/users/${comment.author.username}`"
          >
            <strong>
              {{ comment.author.username }}
            </strong>
          </RouterLink>

          <span>
            {{ comment.content }}
          </span>
        </div>

        <p v-if="post.comments.length === 0">
          Nenhum comentário ainda.
        </p>
      </section>

      <!-- Novo comentário -->
      <form @submit.prevent="addComment">
        <input
          v-model="newComment"
          type="text"
          placeholder="Adicione um comentário..."
        />

        <button
          type="submit"
          :disabled="commentLoading || !newComment.trim()"
        >
          {{
            commentLoading
              ? 'Publicando...'
              : 'Publicar'
          }}
        </button>

        <p v-if="commentError">
          {{ commentError }}
        </p>
      </form>
    </article>
  </main>
</template>