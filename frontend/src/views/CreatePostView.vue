<script setup>
import { onUnmounted, ref } from 'vue'
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
    clearMedia()
    return
  }

  if (preview.value) {
    URL.revokeObjectURL(preview.value)
  }

  media.value = file

  mediaType.value = file.type.startsWith('video/')
    ? 'video'
    : 'image'

  preview.value = URL.createObjectURL(file)
}

function clearMedia() {
  if (preview.value) {
    URL.revokeObjectURL(preview.value)
  }

  media.value = null
  preview.value = null
  mediaType.value = null
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

    const response = await api.post(
      '/posts',
      formData
    )

    const post =
      response.data.data ?? response.data

    clearMedia()

    await router.push(`/posts/${post.id}`)
  } catch (err) {
    console.error(err)

    validationErrors.value =
      err.response?.data?.errors ?? {}

    error.value =
      err.response?.data?.message ??
      'Não foi possível criar a publicação.'
  } finally {
    loading.value = false
  }
}

onUnmounted(() => {
  if (preview.value) {
    URL.revokeObjectURL(preview.value)
  }
})
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
    <!-- Cabeçalho -->
    <header class="mb-6">
      <h1
        class="
          text-2xl
          font-bold
          text-gray-900
        "
      >
        Nova publicação
      </h1>

      <p
        class="
          mt-1
          text-sm
          text-gray-500
        "
      >
        Compartilhe uma imagem ou vídeo.
      </p>
    </header>

    <!-- Card -->
    <section
      class="
        overflow-hidden
        rounded-2xl
        border
        border-gray-200
        bg-white
        shadow-sm
      "
    >
      <div
        class="
          grid
          grid-cols-1
          lg:grid-cols-[minmax(0,1.4fr)_minmax(320px,0.6fr)]
        "
      >
        <!-- Área da mídia -->
        <div
          class="
            relative
            flex
            min-h-[420px]
            items-center
            justify-center
            bg-gray-100
            lg:min-h-[620px]
          "
        >
          <!-- Sem mídia -->
          <div
            v-if="!preview"
            class="
              flex
              max-w-sm
              flex-col
              items-center
              px-6
              text-center
            "
          >
            <div
              class="
                mb-5
                flex
                h-20
                w-20
                items-center
                justify-center
                rounded-full
                bg-white
                shadow-sm
              "
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
                class="h-10 w-10 text-gray-500"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5A2.25 2.25 0 0 0 22.5 17.25V6.75A2.25 2.25 0 0 0 20.25 4.5H3.75A2.25 2.25 0 0 0 1.5 6.75v10.5A2.25 2.25 0 0 0 3.75 19.5Z"
                />
              </svg>
            </div>

            <h2
              class="
                text-lg
                font-semibold
                text-gray-900
              "
            >
              Selecione uma foto ou vídeo
            </h2>

            <p
              class="
                mt-2
                text-sm
                leading-5
                text-gray-500
              "
            >
              Escolha um arquivo do seu dispositivo
              para criar uma nova publicação.
            </p>

            <label
              for="media"
              class="
                mt-6
                cursor-pointer
                rounded-lg
                bg-sky-500
                px-5
                py-2.5
                text-sm
                font-semibold
                text-white
                transition
                hover:bg-sky-600
              "
            >
              Selecionar arquivo
            </label>
          </div>

          <!-- Preview imagem -->
          <img
            v-else-if="mediaType === 'image'"
            :src="preview"
            alt="Pré-visualização"
            class="
              h-full
              max-h-[720px]
              w-full
              object-contain
            "
          />

          <!-- Preview vídeo -->
          <video
            v-else
            :src="preview"
            controls
            class="
              h-full
              max-h-[720px]
              w-full
              object-contain
            "
          />

          <!-- Trocar/remover -->
          <div
            v-if="preview"
            class="
              absolute
              right-4
              top-4
              flex
              gap-2
            "
          >
            <label
              for="media"
              class="
                cursor-pointer
                rounded-lg
                bg-black/70
                px-3
                py-2
                text-xs
                font-semibold
                text-white
                backdrop-blur
                transition
                hover:bg-black/80
              "
            >
              Trocar
            </label>

            <button
              type="button"
              class="
                rounded-lg
                bg-black/70
                px-3
                py-2
                text-xs
                font-semibold
                text-white
                backdrop-blur
                transition
                hover:bg-red-600
              "
              @click="clearMedia"
            >
              Remover
            </button>
          </div>

          <input
            id="media"
            type="file"
            accept="image/jpeg,image/png,image/webp,video/mp4,video/quicktime"
            class="hidden"
            @change="handleFile"
          />
        </div>

        <!-- Formulário -->
        <div
          class="
            flex
            flex-col
            border-t
            border-gray-200
            lg:border-l
            lg:border-t-0
          "
        >
          <div
            class="
              flex-1
              p-6
            "
          >
            <h2
              class="
                mb-5
                text-base
                font-semibold
                text-gray-900
              "
            >
              Detalhes da publicação
            </h2>

            <!-- Legenda -->
            <div>
              <label
                for="caption"
                class="
                  mb-2
                  block
                  text-sm
                  font-semibold
                  text-gray-700
                "
              >
                Legenda
              </label>

              <textarea
                id="caption"
                v-model="caption"
                rows="8"
                maxlength="2200"
                placeholder="Escreva uma legenda..."
                class="
                  w-full
                  resize-none
                  rounded-xl
                  border
                  border-gray-300
                  px-4
                  py-3
                  text-sm
                  text-gray-900
                  outline-none
                  transition
                  placeholder:text-gray-400
                  focus:border-sky-500
                  focus:ring-1
                  focus:ring-sky-500
                "
              />

              <div
                class="
                  mt-2
                  flex
                  items-start
                  justify-between
                  gap-4
                "
              >
                <p
                  v-if="validationErrors.caption"
                  class="text-xs text-red-600"
                >
                  {{ validationErrors.caption[0] }}
                </p>

                <span
                  class="
                    ml-auto
                    text-xs
                    text-gray-400
                  "
                >
                  {{ caption.length }}/2200
                </span>
              </div>
            </div>

            <!-- Erro mídia -->
            <p
              v-if="validationErrors.media"
              class="
                mt-5
                rounded-lg
                bg-red-50
                p-3
                text-sm
                text-red-600
              "
            >
              {{ validationErrors.media[0] }}
            </p>

            <!-- Erro geral -->
            <p
              v-if="error"
              class="
                mt-5
                rounded-lg
                bg-red-50
                p-3
                text-sm
                text-red-600
              "
            >
              {{ error }}
            </p>
          </div>

          <!-- Rodapé -->
          <footer
            class="
              border-t
              border-gray-100
              p-6
            "
          >
            <button
              type="button"
              :disabled="loading || !media"
              class="
                w-full
                rounded-lg
                bg-sky-500
                py-3
                text-sm
                font-semibold
                text-white
                transition
                hover:bg-sky-600
                disabled:cursor-not-allowed
                disabled:bg-gray-300
              "
              @click="createPost"
            >
              {{
                loading
                  ? 'Publicando...'
                  : 'Publicar'
              }}
            </button>
          </footer>
        </div>
      </div>
    </section>
  </main>
</template>