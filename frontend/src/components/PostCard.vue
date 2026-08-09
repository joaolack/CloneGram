<script setup>
import { ref } from 'vue'
import api from '@/api/axios'
import { Heart, MessageCircle } from 'lucide-vue-next'

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
            <Heart
                class="h-7 w-7"
                :class="
                liked
                    ? 'fill-red-500 text-red-500'
                    : 'text-gray-900'
                "
                :stroke-width="1.8"
            />
        </button>


        <RouterLink
          :to="`/posts/${post.id}`"
          class="
            text-gray-900
            transition
            hover:scale-110
          "
        >
            <MessageCircle
                class="h-7 w-7"
                :stroke-width="1.8"
            />
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