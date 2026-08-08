<script setup>
import { computed, onMounted, ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/api/axios'

const route = useRoute()

const profile = ref(null)

const loading = ref(true)
const error = ref('')

const followLoading = ref(false)
const deletingPostId = ref(null)

const editing = ref(false)
const updateLoading = ref(false)
const updateError = ref('')
const validationErrors = ref({})

const name = ref('')
const username = ref('')
const bio = ref('')
const avatar = ref(null)

const isOwnProfile = computed(() => {
  return route.name === 'my-profile'
})

async function fetchProfile() {
  loading.value = true
  error.value = ''

  try {
    let response

    if (isOwnProfile.value) {
      response = await api.get('/profile')
    } else {
      response = await api.get(
        `/users/${route.params.username}`
      )
    }

    profile.value =
      response.data.data ?? response.data

    fillForm()
  } catch (err) {
    console.error(err)

    error.value =
      err.response?.data?.message ??
      'Não foi possível carregar o perfil.'
  } finally {
    loading.value = false
  }
}

function fillForm() {
  if (!profile.value) return

  name.value = profile.value.name ?? ''
  username.value = profile.value.username ?? ''
  bio.value = profile.value.bio ?? ''

  avatar.value = null
}

async function toggleFollow() {
  if (followLoading.value) return

  followLoading.value = true

  try {
    if (profile.value.is_following) {
      await api.delete(
        `/users/${profile.value.username}/follow`
      )

      profile.value.is_following = false
      profile.value.followers_count--
    } else {
      await api.post(
        `/users/${profile.value.username}/follow`
      )

      profile.value.is_following = true
      profile.value.followers_count++
    }
  } catch (err) {
    console.error(
      'Erro ao alterar follow:',
      err
    )
  } finally {
    followLoading.value = false
  }
}

function handleAvatar(event) {
  avatar.value = event.target.files[0] ?? null
}

function startEditing() {
  fillForm()
  updateError.value = ''
  validationErrors.value = {}
  editing.value = true
}

function cancelEditing() {
  editing.value = false
  fillForm()
}

async function updateProfile() {
  updateLoading.value = true
  updateError.value = ''
  validationErrors.value = {}

  try {
    const formData = new FormData()

    formData.append('_method', 'PUT')
    formData.append('name', name.value)
    formData.append('username', username.value)
    formData.append('bio', bio.value)

    if (avatar.value) {
      formData.append('avatar', avatar.value)
    }

    const response = await api.post(
      '/profile',
      formData
    )

    profile.value =
      response.data.data ?? response.data

    editing.value = false
    fillForm()
  } catch (err) {
    console.error(err)

    validationErrors.value =
      err.response?.data?.errors ?? {}

    updateError.value =
      err.response?.data?.message ??
      'Não foi possível atualizar o perfil.'
  } finally {
    updateLoading.value = false
  }
}

async function deletePost(postId) {
  deletingPostId.value = postId

  try {
    await api.delete(`/posts/${postId}`)

    profile.value.posts =
      profile.value.posts.filter(
        (post) => post.id !== postId
      )

    profile.value.posts_count--
  } catch (err) {
    console.error(
      'Erro ao excluir post:',
      err
    )
  } finally {
    deletingPostId.value = null
  }
}

onMounted(fetchProfile)

watch(
  () => route.fullPath,
  () => {
    fetchProfile()
  }
)
</script>

<template>
  <main>
    <p v-if="loading">
      Carregando...
    </p>

    <p v-else-if="error">
      {{ error }}
    </p>

    <template v-else-if="profile">
      <!-- Cabeçalho -->
      <section>
        <img
          v-if="profile.avatar_url"
          :src="profile.avatar_url"
          :alt="profile.username"
        />

        <div v-else>
          Sem foto
        </div>

        <div>
          <h1>
            {{ profile.username }}
          </h1>

          <p>
            {{ profile.name }}
          </p>

          <p v-if="profile.bio">
            {{ profile.bio }}
          </p>
        </div>

        <!-- Próprio perfil -->
        <button
          v-if="isOwnProfile"
          type="button"
          @click="startEditing"
        >
          Editar perfil
        </button>

        <!-- Perfil alheio -->
        <button
          v-else
          type="button"
          :disabled="followLoading"
          @click="toggleFollow"
        >
          {{
            profile.is_following
              ? 'Deixar de seguir'
              : 'Seguir'
          }}
        </button>
      </section>

      <!-- Estatísticas -->
      <section>
        <span>
          <strong>
            {{ profile.posts_count }}
          </strong>
          publicações
        </span>

        <span>
          <strong>
            {{ profile.followers_count }}
          </strong>
          seguidores
        </span>

        <span>
          <strong>
            {{ profile.following_count }}
          </strong>
          seguindo
        </span>
      </section>

      <!-- Edição -->
      <section v-if="isOwnProfile && editing">
        <h2>Editar perfil</h2>

        <form @submit.prevent="updateProfile">
          <div>
            <label for="name">
              Nome
            </label>

            <input
              id="name"
              v-model="name"
              type="text"
            />

            <p v-if="validationErrors.name">
              {{ validationErrors.name[0] }}
            </p>
          </div>

          <div>
            <label for="username">
              Username
            </label>

            <input
              id="username"
              v-model="username"
              type="text"
            />

            <p v-if="validationErrors.username">
              {{ validationErrors.username[0] }}
            </p>
          </div>

          <div>
            <label for="bio">
              Bio
            </label>

            <textarea
              id="bio"
              v-model="bio"
            />
          </div>

          <div>
            <label for="avatar">
              Foto de perfil
            </label>

            <input
              id="avatar"
              type="file"
              accept="image/*"
              @change="handleAvatar"
            />

            <p v-if="validationErrors.avatar">
              {{ validationErrors.avatar[0] }}
            </p>
          </div>

          <p v-if="updateError">
            {{ updateError }}
          </p>

          <button
            type="submit"
            :disabled="updateLoading"
          >
            {{
              updateLoading
                ? 'Salvando...'
                : 'Salvar'
            }}
          </button>

          <button
            type="button"
            @click="cancelEditing"
          >
            Cancelar
          </button>
        </form>
      </section>

      <!-- Posts -->
      <section>
        <h2>Publicações</h2>

        <div>
          <article
            v-for="post in profile.posts"
            :key="post.id"
          >
            <RouterLink
              :to="`/posts/${post.id}`"
            >
              <img
                v-if="post.media_type === 'image'"
                :src="post.media_url"
                :alt="post.caption || 'Post'"
              />

              <video
                v-else-if="post.media_type === 'video'"
                :src="post.media_url"
              />
            </RouterLink>

            <button
              v-if="isOwnProfile"
              type="button"
              :disabled="
                deletingPostId === post.id
              "
              @click="deletePost(post.id)"
            >
              {{
                deletingPostId === post.id
                  ? 'Excluindo...'
                  : 'Excluir'
              }}
            </button>
          </article>
        </div>

        <p v-if="profile.posts.length === 0">
          Nenhuma publicação.
        </p>
      </section>
    </template>
  </main>
</template>