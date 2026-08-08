<script setup>
import { ref } from 'vue'
import api from '@/api/axios'

const props = defineProps({
  user: {
    type: Object,
    required: true,
  },
})

const emit = defineEmits([
    'followed',
])

const loading = ref(false)

async function follow() {
    loading.value = true

    try {
        await api.post(
            `/users/${props.user.username}/follow`
        )

        emit('followed', props.user.id)
    } catch (error) {
        console.error('Erro ao seguir usuário:', error)
    } finally {
        loading.value = false
    }
}
</script>
<template>
    <div>
        <RouterLink :to="`/users/${user.username}`">
            <strong>{{ user.username }}</strong>
        </RouterLink>

        <span>
            {{ user.name }}            
        </span>

        <button type="button" :disabled="loading" @click="follow">
            {{ loading ? 'Seguindo...' : 'Seguir'  }}
        </button>
    </div>
</template>