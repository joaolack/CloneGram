<script setup>
import { ref } from 'vue'
import api from '@/api/axios'

const props = defineProps({
  post: {
    type: Object,
    required: true,
  },
})

const liked = ref(props.post.liked_by_me)
const likesCount = ref(props.post.likes_count ?? 0)
const loadingLike = ref(false)

async function toggleLike() {
  if (loadingLike.value) return

  loadingLike.value = true

  try {
    if (liked.value) {
      await api.delete(`/posts/${props.post.id}/like`)

      liked.value = false

      if (likesCount.value > 0) {
        likesCount.value--
      }
    } else {
      await api.post(`/posts/${props.post.id}/like`)

      liked.value = true
      likesCount.value++
    }
  } catch (error) {
    console.error('Erro ao alterar like:', error)
  } finally {
    loadingLike.value = false
  }
}
</script>

<template>
  <article
    class="
      overflow-hidden
      rounded-xl
      border
      border-gray-200
      bg-white
      shadow-sm
    "
  >
    <!-- Cabeçalho -->
    <header
      class="
        flex
        items-center
        justify-between
        px-4
        py-3
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
        <!-- Avatar -->
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
            class="h-full w-full object-cover"
          />

          <span
            v-else
            class="
              text-sm
              font-semibold
              uppercase
              text-gray-600
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
            class="text-xs text-gray-500"
          >
            {{ post.author.name }}
          </p>
        </div>
      </RouterLink>
    </header>

    <div class="bg-black">
      <img
        v-if="post.media_type === 'image'"
        :src="post.media_url"
        :alt="post.caption || 'Publicação'"
        class="
          max-h-[650px]
          w-full
          object-contain
        "
      />

      <video
        v-else-if="post.media_type === 'video'"
        :src="post.media_url"
        controls
        class="
          max-h-[650px]
          w-full
          object-contain
        "
      />
    </div>

    <div class="px-4 py-3">
      <div
        class="
          mb-3
          flex
          items-center
          gap-4
        "
      >
        <button
          type="button"
          :disabled="loadingLike"
          class="
            transition
            hover:scale-110
            disabled:opacity-50
          "
          @click="toggleLike"
        >

          <svg
            v-if="liked"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            fill="currentColor"
            class="h-7 w-7 text-red-500"
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
            class="h-7 w-7 text-gray-900"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M21.435 2.582a5.375 5.375 0 0 0-7.604 0L12 4.413l-1.831-1.831a5.375 5.375 0 0 0-7.604 7.604L12 19.621l9.435-9.435a5.375 5.375 0 0 0 0-7.604Z"
            />
          </svg>
        </button>


        <RouterLink
          :to="`/posts/${post.id}`"
          class="
            text-gray-900
            transition
            hover:scale-110
          "
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.8"
            stroke="currentColor"
            class="h-7 w-7"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M8.625 12h.008v.008h-.008V12Zm3.375 0h.008v.008H12V12Zm3.375 0h.008v.008h-.008V12ZM21 12c0 4.556-4.03 8.25-9 8.25a9.86 9.86 0 0 1-4.255-.949L3 20.25l1.245-3.32A7.814 7.814 0 0 1 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z"
            />
          </svg>
        </RouterLink>
      </div>


      <p
        class="
          mb-2
          text-sm
          font-semibold
          text-gray-900
        "
      >
        {{ likesCount }}
        {{ likesCount === 1 ? 'curtida' : 'curtidas' }}
      </p>


      <p
        v-if="post.caption"
        class="
          mb-2
          text-sm
          leading-5
          text-gray-800
        "
      >
        <RouterLink
          :to="`/users/${post.author.username}`"
          class="mr-1 font-semibold text-gray-900"
        >
          {{ post.author.username }}
        </RouterLink>

        {{ post.caption }}
      </p>

      <RouterLink
        :to="`/posts/${post.id}`"
        class="text-sm text-gray-500 hover:text-gray-700">
        Ver todos os {{ post.comments_count ?? 0 }} comentários
      </RouterLink>
    </div>
  </article>
</template>