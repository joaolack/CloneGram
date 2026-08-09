<script setup>
import { ref } from 'vue'
import api from '@/api/axios'

const props = defineProps({
  user: {
    type: Object,
    required: true,
  },
})

const emit = defineEmits(['followed'])

const loading = ref(false)

async function follow() {
  if (loading.value) return

  loading.value = true

  try {
    await api.post(
      `/users/${props.user.username}/follow`
    )

    emit('followed', props.user.id)
  } catch (error) {
    console.error(
      'Erro ao seguir usuário:',
      error
    )
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div
    class="
      flex
      items-center
      justify-between
      gap-3
      py-2
    "
  >
    <RouterLink
      :to="`/users/${user.username}`"
      class="
        flex
        min-w-0
        items-center
        gap-3
      "
    >
      <!-- Avatar -->
      <div
        class="
          flex
          h-11
          w-11
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
          {{ user.username?.charAt(0) }}
        </span>
      </div>

      <div class="min-w-0">
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
            text-xs
            text-gray-500
          "
        >
          {{ user.name }}
        </p>
      </div>
    </RouterLink>

    <button
      type="button"
      :disabled="loading"
      class="
        shrink-0
        text-xs
        font-semibold
        text-sky-500
        transition
        hover:text-sky-700
        disabled:opacity-50
      "
      @click="follow"
    >
      {{ loading ? '...' : 'Seguir' }}
    </button>
  </div>
</template>