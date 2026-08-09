<script setup>
import { onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import api from '@/api/axios'

const route = useRoute()
const router = useRouter()
const auth = useAuthStore()

const deleteLoading = ref(false)

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

    post.value =
      response.data.data ?? response.data
  } catch (err) {
    console.error(err)

    error.value =
      err.response?.data?.message ??
      'Não foi possível carregar a publicação.'
  } finally {
    loading.value = false
  }
}

async function deletePost() {
  const confirmed = window.confirm(
    'Deseja realmente excluir esta publicação?'
  )

  if (!confirmed) return

  deleteLoading.value = true

  try {
    await api.delete(`/posts/${post.value.id}`)

    await router.replace('/profile')
  } catch (err) {
    console.error(
      'Erro ao excluir publicação:',
      err
    )
  } finally {
    deleteLoading.value = false
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

      if (post.value.likes_count > 0) {
        post.value.likes_count--
      }
    } else {
      await api.post(
        `/posts/${post.value.id}/like`
      )

      post.value.liked_by_me = true
      post.value.likes_count++
    }
  } catch (err) {
    console.error(
      'Erro ao alterar like:',
      err
    )
  } finally {
    likeLoading.value = false
  }
}

async function addComment() {
  commentError.value = ''

  const content = newComment.value.trim()

  if (!content) return

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
  <main
    class="
      mx-auto
      w-full
      max-w-6xl
      px-4
      py-8
      pb-24
      sm:px-6
      md:pb-8
      lg:px-8
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
          h-9
          w-9
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
        p-5
        text-center
        text-sm
        text-red-700
      "
    >
      {{ error }}
    </div>

    <!-- Post -->
    <section
      v-else-if="post"
      class="
        overflow-hidden
        rounded-2xl
        border
        border-gray-200
        bg-white
        shadow-sm
        lg:grid
        lg:grid-cols-[minmax(0,1.4fr)_minmax(340px,0.6fr)]
      "
    >
      <!-- Mídia -->
      <div
        class="
          flex
          min-h-[420px]
          items-center
          justify-center
          bg-black
          lg:min-h-[720px]
        "
      >
        <img
          v-if="post.media_type === 'image'"
          :src="post.media_url"
          :alt="post.caption || 'Publicação'"
          class="
            max-h-[760px]
            w-full
            object-contain
          "
        />

        <video
          v-else-if="post.media_type === 'video'"
          :src="post.media_url"
          controls
          class="
            max-h-[760px]
            w-full
            object-contain
          "
        />
      </div>

      <!-- Painel lateral -->
      <div
        class="
          flex
          min-h-[520px]
          flex-col
          border-t
          border-gray-200
          lg:min-h-0
          lg:border-l
          lg:border-t-0
        "
      >
        <!-- Autor -->
        <header
          class="
            flex
            items-center
            gap-3
            border-b
            border-gray-100
            px-5
            py-4
          "
        >
          <RouterLink
            :to="`/users/${post.author.username}`"
            class="
              flex
              items-center
              gap-3
            "
          >
            <div
              class="
                flex
                h-10
                w-10
                items-center
                justify-center
                overflow-hidden
                rounded-full
                bg-gray-200
              "
            >
              <img
                v-if="post.author.avatar_url"
                :src="post.author.avatar_url"
                :alt="post.author.username"
                class="
                  h-full
                  w-full
                  object-cover
                "
              />

              <span
                v-else
                class="
                  text-sm
                  font-semibold
                  uppercase
                  text-gray-500
                "
              >
                {{ post.author.username?.charAt(0) }}
              </span>
            </div>

            <div>
              <p
                class="
                  text-sm
                  font-semibold
                  text-gray-900
                "
              >
                {{ post.author.username }}
              </p>

              <p
                v-if="post.author.name"
                class="
                  text-xs
                  text-gray-500
                "
              >
                {{ post.author.name }}
              </p>
            </div>
          </RouterLink>
        </header>

        <!-- Comentários -->
        <div
          class="
            flex-1
            space-y-5
            overflow-y-auto
            px-5
            py-5
            lg:max-h-[500px]
          "
        >
          <!-- Legenda -->
          <div
            v-if="post.caption"
            class="
              flex
              gap-3
            "
          >
            <div
              class="
                flex
                h-9
                w-9
                shrink-0
                items-center
                justify-center
                overflow-hidden
                rounded-full
                bg-gray-200
              "
            >
              <img
                v-if="post.author.avatar_url"
                :src="post.author.avatar_url"
                class="
                  h-full
                  w-full
                  object-cover
                "
              />

              <span
                v-else
                class="
                  text-xs
                  font-semibold
                  uppercase
                  text-gray-500
                "
              >
                {{ post.author.username?.charAt(0) }}
              </span>
            </div>

            <p
              class="
                text-sm
                leading-5
                text-gray-800
              "
            >
              <RouterLink
                :to="`/users/${post.author.username}`"
                class="
                  mr-1
                  font-semibold
                  text-gray-900
                "
              >
                {{ post.author.username }}
              </RouterLink>

              {{ post.caption }}
            </p>
          </div>

          <!-- Lista -->
          <div
            v-for="comment in post.comments"
            :key="comment.id"
            class="
              flex
              gap-3
            "
          >
            <div
              class="
                flex
                h-9
                w-9
                shrink-0
                items-center
                justify-center
                overflow-hidden
                rounded-full
                bg-gray-200
              "
            >
              <img
                v-if="comment.author.avatar_url"
                :src="comment.author.avatar_url"
                :alt="comment.author.username"
                class="
                  h-full
                  w-full
                  object-cover
                "
              />

              <span
                v-else
                class="
                  text-xs
                  font-semibold
                  uppercase
                  text-gray-500
                "
              >
                {{ comment.author.username?.charAt(0) }}
              </span>
            </div>

            <div class="min-w-0">
              <p
                class="
                  text-sm
                  leading-5
                  text-gray-800
                "
              >
                <RouterLink
                  :to="`/users/${comment.author.username}`"
                  class="
                    mr-1
                    font-semibold
                    text-gray-900
                  "
                >
                  {{ comment.author.username }}
                </RouterLink>

                {{ comment.content }}
              </p>
            </div>
          </div>

          <div
            v-if="post.comments.length === 0"
            class="
              flex
              flex-col
              items-center
              justify-center
              py-12
              text-center
            "
          >
            <h3
              class="
                text-base
                font-semibold
                text-gray-900
              "
            >
              Nenhum comentário
            </h3>

            <p
              class="
                mt-1
                text-sm
                text-gray-500
              "
            >
              Seja o primeiro a comentar.
            </p>
          </div>
        </div>

        <!-- Like e contador -->
        <div
          class="
            border-t
            border-gray-100
            px-5
            py-4
          "
        >
          <button
            type="button"
            :disabled="likeLoading"
            class="
              mb-2
              transition
              hover:scale-110
              disabled:opacity-50
            "
            @click="toggleLike"
          >
            <svg
              v-if="post.liked_by_me"
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 24 24"
              fill="currentColor"
              class="
                h-7
                w-7
                text-red-500
              "
            >
              <path
                d="M11.645 20.91a.75.75 0 0 0 .71 0c.695-.398 6.862-4.072 8.56-8.474C22.488 8.36 20.287 4.5 16.25 4.5c-1.98 0-3.46 1.126-4.25 2.12C11.21 5.626 9.73 4.5 7.75 4.5 3.713 4.5 1.512 8.36 3.085 12.436c1.698 4.402 7.865 8.076 8.56 8.474Z"
              />
            </svg>

            <svg
              v-else
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke-width="1.8"
              stroke="currentColor"
              class="
                h-7
                w-7
                text-gray-900
              "
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M21.435 2.582a5.375 5.375 0 0 0-7.604 0L12 4.413l-1.831-1.831a5.375 5.375 0 0 0-7.604 7.604L12 19.621l9.435-9.435a5.375 5.375 0 0 0 0-7.604Z"
              />
            </svg>
          </button>

          <p
            class="
              text-sm
              font-semibold
              text-gray-900
            "
          >
            {{ post.likes_count ?? 0 }}
            {{
              post.likes_count === 1
                ? 'curtida'
                : 'curtidas'
            }}
          </p>

          <p
            class="
              mt-1
              text-xs
              text-gray-400
            "
          >
            {{ post.comments_count ?? 0 }}
            {{
              post.comments_count === 1
                ? 'comentário'
                : 'comentários'
            }}
          </p>
        </div>

        <form class="border-t border-gray-100 p-4" @submit.prevent="addComment">
          <div
            class="
              flex
              items-center
              gap-3
            "
          >
            <input
              v-model="newComment"
              type="text"
              maxlength="1000"
              placeholder="Adicione um comentário..."
              class="
                min-w-0
                flex-1
                border-0
                bg-transparent
                text-sm
                text-gray-900
                outline-none
                placeholder:text-gray-400
              "
            />

            <button
              type="submit"
              :disabled="
                commentLoading ||
                !newComment.trim()
              "
              class="
                shrink-0
                text-sm
                font-semibold
                text-sky-500
                transition
                hover:text-sky-700
                disabled:cursor-not-allowed
                disabled:text-sky-200
              "
            >
              {{
                commentLoading
                  ? 'Publicando...'
                  : 'Publicar'
              }}
            </button>
            <button
                v-if="auth.user?.id === post.author.id"
                type="button"
                :disabled="deleteLoading"
                class="
                  ml-auto
                  text-sm
                  font-semibold
                  text-red-500
                  transition
                  hover:text-red-700
                  disabled:opacity-50
                "
                @click="deletePost"
              >
                {{
                  deleteLoading
                    ? 'Excluindo...'
                    : 'Excluir'
                }}
            </button>
          </div>

          <p
            v-if="commentError" class="mt-3 text-xs text-red-600">
            {{ commentError }}
          </p>
        </form>
      </div>
    </section>
  </main>
</template>