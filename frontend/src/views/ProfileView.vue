<script setup>
import { computed, onMounted, ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/api/axios'
import { useAuthStore } from '@/stores/auth'

const route = useRoute()
const auth = useAuthStore()

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
const avatarPreview = ref(null)

const isOwnProfile = computed(() => {
  return route.name === 'my-profile'
})

const posts = computed(() => {
  return profile.value?.posts ?? []
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
  } catch (err) {
    console.error(err)

    error.value =
      err.response?.data?.message ??
      'Não foi possível carregar o perfil.'
  } finally {
    loading.value = false
  }
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

      if (profile.value.followers_count > 0) {
        profile.value.followers_count--
      }
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

function fillForm() {
  if (!profile.value) return

  name.value = profile.value.name ?? ''
  username.value = profile.value.username ?? ''
  bio.value = profile.value.bio ?? ''
  avatar.value = null
  avatarPreview.value = null
}

function startEditing() {
  fillForm()

  updateError.value = ''
  validationErrors.value = {}

  editing.value = true
}

function cancelEditing() {
  editing.value = false

  if (avatarPreview.value) {
    URL.revokeObjectURL(avatarPreview.value)
  }

  avatarPreview.value = null
  avatar.value = null
}

function handleAvatar(event) {
  const file = event.target.files[0]

  if (!file) {
    avatar.value = null
    avatarPreview.value = null
    return
  }

  avatar.value = file

  if (avatarPreview.value) {
    URL.revokeObjectURL(avatarPreview.value)
  }

  avatarPreview.value =
    URL.createObjectURL(file)
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

    if (isOwnProfile.value) {
      auth.user = profile.value
    }

    editing.value = false

    if (avatarPreview.value) {
      URL.revokeObjectURL(avatarPreview.value)
    }

    avatarPreview.value = null
    avatar.value = null
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
  const confirmed = window.confirm(
    'Deseja realmente excluir esta publicação?'
  )

  if (!confirmed) return

  deletingPostId.value = postId

  try {
    await api.delete(`/posts/${postId}`)

    profile.value.posts =
      profile.value.posts.filter(
        (post) => post.id !== postId
      )

    if (profile.value.posts_count > 0) {
      profile.value.posts_count--
    }
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
  <main
    class="
      mx-auto
      w-full
      max-w-5xl
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

    <template v-else-if="profile">
      <!-- Cabeçalho do perfil -->
      <section
        class="
          grid
          grid-cols-[90px_1fr]
          gap-6
          border-b
          border-gray-200
          pb-10
          sm:grid-cols-[140px_1fr]
          sm:gap-10
          md:grid-cols-[180px_1fr]
          md:gap-16
          md:px-8
        "
      >
        <!-- Avatar -->
        <div
          class="
            flex
            justify-center
            sm:items-start
          "
        >
          <div
            class="
              flex
              h-20
              w-20
              items-center
              justify-center
              overflow-hidden
              rounded-full
              bg-gray-200
              sm:h-32
              sm:w-32
              md:h-40
              md:w-40
            "
          >
            <img
              v-if="profile.avatar_url"
              :src="profile.avatar_url"
              :alt="profile.username"
              class="
                h-full
                w-full
                object-cover
              "
            />

            <span
              v-else
              class="
                text-3xl
                font-semibold
                uppercase
                text-gray-500
                md:text-5xl
              "
            >
              {{ profile.username?.charAt(0) }}
            </span>
          </div>
        </div>

        <!-- Informações -->
        <div class="min-w-0">
          <!-- Username + ação -->
          <div
            class="
              mb-5
              flex
              flex-col
              items-start
              gap-3
              sm:flex-row
              sm:items-center
            "
          >
            <h1
              class="
                break-all
                text-xl
                font-normal
                text-gray-900
                sm:text-2xl
              "
            >
              {{ profile.username }}
            </h1>

            <!-- Meu perfil -->
            <button
              v-if="isOwnProfile"
              type="button"
              class="
                rounded-lg
                bg-gray-200
                px-4
                py-2
                text-sm
                font-semibold
                text-gray-900
                transition
                hover:bg-gray-300
              "
              @click="startEditing"
            >
              Editar perfil
            </button>

            <!-- Perfil alheio -->
            <button
              v-else
              type="button"
              :disabled="followLoading"
              class="
                rounded-lg
                px-5
                py-2
                text-sm
                font-semibold
                transition
                disabled:cursor-not-allowed
                disabled:opacity-60
              "
              :class="
                profile.is_following
                  ? 'bg-gray-200 text-gray-900 hover:bg-gray-300'
                  : 'bg-sky-500 text-white hover:bg-sky-600'
              "
              @click="toggleFollow"
            >
              <template v-if="followLoading">
                Aguarde...
              </template>

              <template v-else>
                {{
                  profile.is_following
                    ? 'Deixar de seguir'
                    : 'Seguir'
                }}
              </template>
            </button>
          </div>

          <!-- Estatísticas desktop -->
          <div
            class="
              mb-5
              hidden
              items-center
              gap-8
              text-sm
              sm:flex
              sm:text-base
            "
          >
            <span>
              <strong>
                {{ profile.posts_count ?? 0 }}
              </strong>
              publicações
            </span>

            <span>
              <strong>
                {{ profile.followers_count ?? 0 }}
              </strong>
              seguidores
            </span>

            <span>
              <strong>
                {{ profile.following_count ?? 0 }}
              </strong>
              seguindo
            </span>
          </div>

          <!-- Nome e bio -->
          <div class="text-sm text-gray-900">
            <p class="font-semibold">
              {{ profile.name }}
            </p>

            <p
              v-if="profile.bio"
              class="
                mt-1
                max-w-lg
                whitespace-pre-line
                leading-5
              "
            >
              {{ profile.bio }}
            </p>
          </div>
        </div>
      </section>

      <!-- Estatísticas mobile -->
      <section
        class="
          grid
          grid-cols-3
          border-b
          border-gray-200
          py-4
          text-center
          sm:hidden
        "
      >
        <div>
          <p class="font-semibold">
            {{ profile.posts_count ?? 0 }}
          </p>

          <p class="text-xs text-gray-500">
            publicações
          </p>
        </div>

        <div>
          <p class="font-semibold">
            {{ profile.followers_count ?? 0 }}
          </p>

          <p class="text-xs text-gray-500">
            seguidores
          </p>
        </div>

        <div>
          <p class="font-semibold">
            {{ profile.following_count ?? 0 }}
          </p>

          <p class="text-xs text-gray-500">
            seguindo
          </p>
        </div>
      </section>

      <!-- Título dos posts -->
      <div
        class="
          flex
          justify-center
          border-b
          border-gray-200
        "
      >
        <div
          class="
            -mb-px
            border-t
            border-gray-900
            px-6
            py-4
            text-xs
            font-semibold
            uppercase
            tracking-widest
            text-gray-900
          "
        >
          Publicações
        </div>
      </div>

      <!-- Grid -->
      <section class="mt-1">
        <div
          v-if="posts.length"
          class="
            grid
            grid-cols-3
            gap-1
            sm:gap-2
            md:gap-4
          "
        >
          <article
            v-for="post in posts"
            :key="post.id"
            class="
              group
              relative
              aspect-square
              overflow-hidden
              bg-gray-100
            "
          >
            <!-- Post -->
            <RouterLink
              :to="`/posts/${post.id}`"
              class="block h-full w-full"
            >
              <img
                v-if="post.media_type === 'image'"
                :src="post.media_url"
                :alt="post.caption || 'Publicação'"
                class="
                  h-full
                  w-full
                  object-cover
                  transition
                  duration-300
                  group-hover:scale-105
                "
              />

              <video
                v-else-if="post.media_type === 'video'"
                :src="post.media_url"
                class="
                  h-full
                  w-full
                  object-cover
                "
              />

              <!-- Overlay -->
              <div
                class="
                  absolute
                  inset-0
                  hidden
                  items-center
                  justify-center
                  gap-6
                  bg-black/40
                  text-sm
                  font-semibold
                  text-white
                  group-hover:flex
                  sm:text-base
                "
              >
                <span v-if="post.likes_count !== undefined">
                  ♥ {{ post.likes_count }}
                </span>

                <span v-if="post.comments_count !== undefined">
                  ◉ {{ post.comments_count }}
                </span>
              </div>
            </RouterLink>

            <!-- Excluir -->
            <button
              v-if="isOwnProfile"
              type="button"
              :disabled="deletingPostId === post.id"
              class="
                absolute
                right-2
                top-2
                z-10
                rounded-full
                bg-black/60
                px-2.5
                py-1.5
                text-xs
                font-semibold
                text-white
                opacity-100
                transition
                hover:bg-red-600
                disabled:opacity-50
                md:opacity-0
                md:group-hover:opacity-100
              "
              @click.stop="deletePost(post.id)"
            >
              {{
                deletingPostId === post.id
                  ? '...'
                  : 'Excluir'
              }}
            </button>
          </article>
        </div>

        <!-- Nenhuma publicação -->
        <div
          v-else
          class="
            flex
            flex-col
            items-center
            justify-center
            py-20
            text-center
          "
        >
          <div
            class="
              mb-4
              flex
              h-16
              w-16
              items-center
              justify-center
              rounded-full
              border-2
              border-gray-900
              text-3xl
            "
          >
            +
          </div>

          <h2
            class="
              mb-2
              text-xl
              font-semibold
              text-gray-900
            "
          >
            Nenhuma publicação
          </h2>

          <p
            class="
              mb-5
              max-w-sm
              text-sm
              text-gray-500
            "
          >
            {{
              isOwnProfile
                ? 'Compartilhe sua primeira publicação.'
                : 'Este usuário ainda não publicou nada.'
            }}
          </p>

          <RouterLink
            v-if="isOwnProfile"
            to="/create-post"
            class="
              text-sm
              font-semibold
              text-sky-500
              hover:text-sky-700
            "
          >
            Criar publicação
          </RouterLink>
        </div>
      </section>

      <!-- Modal de edição -->
      <div
        v-if="editing"
        class="
          fixed
          inset-0
          z-50
          flex
          items-center
          justify-center
          bg-black/60
          px-4
          py-8
        "
        @click.self="cancelEditing"
      >
        <section
          class="
            max-h-full
            w-full
            max-w-lg
            overflow-y-auto
            rounded-2xl
            bg-white
            shadow-2xl
          "
        >
          <!-- Cabeçalho modal -->
          <header
            class="
              flex
              items-center
              justify-between
              border-b
              border-gray-200
              px-6
              py-4
            "
          >
            <h2
              class="
                text-lg
                font-semibold
                text-gray-900
              "
            >
              Editar perfil
            </h2>

            <button
              type="button"
              class="
                text-2xl
                leading-none
                text-gray-500
                hover:text-gray-900
              "
              @click="cancelEditing"
            >
              ×
            </button>
          </header>

          <form
            class="space-y-5 p-6"
            @submit.prevent="updateProfile"
          >
            <!-- Avatar -->
            <div
              class="
                flex
                items-center
                gap-4
                rounded-xl
                bg-gray-50
                p-4
              "
            >
              <div
                class="
                  flex
                  h-16
                  w-16
                  shrink-0
                  items-center
                  justify-center
                  overflow-hidden
                  rounded-full
                  bg-gray-200
                "
              >
                <img
                  v-if="
                    avatarPreview ||
                    profile.avatar_url
                  "
                  :src="
                    avatarPreview ||
                    profile.avatar_url
                  "
                  class="
                    h-full
                    w-full
                    object-cover
                  "
                />

                <span
                  v-else
                  class="
                    text-xl
                    font-semibold
                    uppercase
                    text-gray-500
                  "
                >
                  {{ profile.username?.charAt(0) }}
                </span>
              </div>

              <div>
                <p
                  class="
                    mb-2
                    text-sm
                    font-semibold
                    text-gray-900
                  "
                >
                  {{ profile.username }}
                </p>

                <label
                  for="avatar"
                  class="
                    cursor-pointer
                    text-sm
                    font-semibold
                    text-sky-500
                    hover:text-sky-700
                  "
                >
                  Alterar foto
                </label>

                <input
                  id="avatar"
                  type="file"
                  accept="image/*"
                  class="hidden"
                  @change="handleAvatar"
                />
              </div>
            </div>

            <p
              v-if="validationErrors.avatar"
              class="text-sm text-red-600"
            >
              {{ validationErrors.avatar[0] }}
            </p>

            <!-- Nome -->
            <div>
              <label
                for="name"
                class="
                  mb-1.5
                  block
                  text-sm
                  font-semibold
                  text-gray-700
                "
              >
                Nome
              </label>

              <input
                id="name"
                v-model="name"
                type="text"
                class="
                  w-full
                  rounded-lg
                  border
                  border-gray-300
                  px-4
                  py-3
                  text-sm
                  outline-none
                  transition
                  focus:border-sky-500
                  focus:ring-1
                  focus:ring-sky-500
                "
              />

              <p
                v-if="validationErrors.name"
                class="mt-1 text-sm text-red-600"
              >
                {{ validationErrors.name[0] }}
              </p>
            </div>

            <!-- Username -->
            <div>
              <label
                for="username"
                class="
                  mb-1.5
                  block
                  text-sm
                  font-semibold
                  text-gray-700
                "
              >
                Username
              </label>

              <input
                id="username"
                v-model="username"
                type="text"
                class="
                  w-full
                  rounded-lg
                  border
                  border-gray-300
                  px-4
                  py-3
                  text-sm
                  outline-none
                  transition
                  focus:border-sky-500
                  focus:ring-1
                  focus:ring-sky-500
                "
              />

              <p
                v-if="validationErrors.username"
                class="mt-1 text-sm text-red-600"
              >
                {{ validationErrors.username[0] }}
              </p>
            </div>

            <!-- Bio -->
            <div>
              <label
                for="bio"
                class="
                  mb-1.5
                  block
                  text-sm
                  font-semibold
                  text-gray-700
                "
              >
                Bio
              </label>

              <textarea
                id="bio"
                v-model="bio"
                rows="4"
                class="
                  w-full
                  resize-none
                  rounded-lg
                  border
                  border-gray-300
                  px-4
                  py-3
                  text-sm
                  outline-none
                  transition
                  focus:border-sky-500
                  focus:ring-1
                  focus:ring-sky-500
                "
              />
            </div>

            <!-- Erro -->
            <p
              v-if="updateError"
              class="
                rounded-lg
                bg-red-50
                p-3
                text-sm
                text-red-600
              "
            >
              {{ updateError }}
            </p>

            <!-- Botões -->
            <div
              class="
                flex
                justify-end
                gap-3
                border-t
                border-gray-100
                pt-5
              "
            >
              <button
                type="button"
                class="
                  rounded-lg
                  bg-gray-100
                  px-5
                  py-2.5
                  text-sm
                  font-semibold
                  text-gray-700
                  transition
                  hover:bg-gray-200
                "
                @click="cancelEditing"
              >
                Cancelar
              </button>

              <button
                type="submit"
                :disabled="updateLoading"
                class="
                  rounded-lg
                  bg-sky-500
                  px-5
                  py-2.5
                  text-sm
                  font-semibold
                  text-white
                  transition
                  hover:bg-sky-600
                  disabled:cursor-not-allowed
                  disabled:opacity-60
                "
              >
                {{
                  updateLoading
                    ? 'Salvando...'
                    : 'Salvar'
                }}
              </button>
            </div>
          </form>
        </section>
      </div>
    </template>
  </main>
</template>
