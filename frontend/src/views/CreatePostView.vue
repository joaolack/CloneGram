<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/api/axios'

const router = useRouter()

const media = ref(null)
const preview = ref(null)
const mediaType = ref(null)
const caption = ref('')

const loading = ref(false)
const error = ref('')
const validationErrors = ref({})

function handleFile(event) {
    const file = event.target.files[0]

    if (!file) {
        media.value = null
        preview.value = null
        mediaType.value = null
        return
    }

    media.value = file

    mediaType.value = file.type.startsWith('video/')
        ? 'video'
        : 'image'

    if (preview.value) {
        URL.revokeObjectURL(preview.value)
    }

    preview.value = URL.createObjectURL(file)
}

async function createPost() {
    error.value = ''
    validationErrors.value = {}

    if (!media.value) {
        error.value = 'Selecione uma imagem ou vídeo.'
        return
    }

    loading.value = true

    try {
        const formData = new FormData()
        
        formData.append('media', media.value)

        if (caption.value.trim()) {
            formData.append(
                'caption',
                caption.value.trim()
            )
        }

        const response = await api.post('/posts', formData)

        const post = response.data.data ?? response.data

        await router.push(`/posts/${post.id}`)
    } catch (err) {
        console.error(err)

        validationErrors.value =
            err.response?.data?.errors ?? {}

        error.value = err.response?.data?.message ??
            'Não foi possível criar a publicação.'
    } finally {
        loading.value = false
    }
}
</script>
<template>
    <main>
        <h1>Nova publicação</h1>

        <form @submit.prevent="createPost">
            <div>
                <label for="media">
                    Imagem ou vídeo
                </label>

                <input
                    id="media"
                    type="file"
                    accept="image/jpeg,image/png,image/webp,video/mp4,video/quicktime"
                    @change="handleFile"
                />

                <p v-if="validationErrors.media">
                    {{ validationErrors.media[0] }}
                </p>
            </div>

            <div v-if="preview">
                <img
                    v-if="mediaType === 'image'"
                    :src="preview"
                    alt="Pré-visualização"
                />

                <video
                    v-else
                    :src="preview"
                    controls
                />
            </div>

            <div>
                <label for="caption">
                    Legenda
                </label>

                <textarea
                    id="caption"
                    v-model="caption"
                    placeholder="Escreva uma legenda..."
                />

                <p v-if="validationErrors.caption">
                    {{ validationErrors.caption[0] }}
                </p>
            </div>

            <p v-if="error">
                {{ error }}
            </p>

            <button type="submit" :disabled="loading">
                {{ loading ? 'Publicando' : 'Publicar' }}
            </button>
        </form>
    </main>
</template>