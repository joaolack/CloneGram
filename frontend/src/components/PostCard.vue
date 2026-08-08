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
const likesCount = ref(props.post.likes_count)
const loadingLike = ref(false)

async function toggleLike() {
    if (loadingLike.value) return

    loadingLike.value = true

    try {
        if (liked.value) {
            await api.delete(`/posts/${props.post.id}/like`)

            liked.value = false
            likesCount.value--
        } else {
            await api.post(`/posts/${props.post.id}/like`)

            liked.value = true
            likesCount.value++
        }
    } catch (error) {
        console.error('Erro ao alterar like: ', error)
    } finally {
        loadingLike.value = false
    }
}
</script>
<template>
    <article>
        <header>
            <RouterLink :to="`/users/${post.author.username}`">
                {{ post.author.username }}
            </RouterLink>
        </header>

        <img
            v-if="post.media_type === 'image'"
            :src="post.media_url"
            :alt="post.caption || 'Post'"
        />

        <video
            v-else-if="post.media_type === 'video'"
            :src="post.media_url"
            controls
        />

        <div>
            <button type="button" :disabled="loadingLike" @click="toggleLike">
                {{ liked ? '♥' : '♡' }}
            </button>

            <span>
                {{ likesCount }} curtidas
            </span>
        </div>

        <p v-if="post.caption">
            <strong>{{ post.author.username }}</strong>
            {{ post.caption }}
        </p>

        <RouterLink :to="`/posts/${post.id}`">
            Ver {{ post.comments_count }} comentários
        </RouterLink>

    </article>
</template>