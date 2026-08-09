<script setup>
import { useRouter } from 'vue-router'
import { TriangleAlert } from 'lucide-vue-next'

const router = useRouter()

function goBack() {
  router.back()
}
</script>

<template>
  <main class="flex min-h-[80vh] items-center justify-center px-4 py-12">
    <div class="max-w-md text-center">
      <div
        class="
          mx-auto
          mb-6
          flex
          h-20
          w-20
          items-center
          justify-center
          rounded-full
          bg-gray-100
        "
      >
        <TriangleAlert class="w-6 h-6" />
      </div>

      <p
        class="
          mb-2
          text-sm
          font-semibold
          uppercase
          tracking-widest
          text-sky-500
        "
      >
        Erro 404
      </p>

      <h1
        class="
          text-3xl
          font-bold
          tracking-tight
          text-gray-900
          sm:text-4xl
        "
      >
        Página não encontrada
      </h1>

      <p
        class="
          mx-auto
          mt-4
          max-w-sm
          text-sm
          leading-6
          text-gray-500
        "
      >
        A página que você está procurando não existe
        ou pode ter sido removida.
      </p>

      <!-- Ações -->
      <div
        class="
          mt-8
          flex
          flex-col
          justify-center
          gap-3
          sm:flex-row
        "
      >
        <RouterLink
          to="/"
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
          "
        >
          Voltar para a Home
        </RouterLink>

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
          @click="goBack"
        >
          Página anterior
        </button>
      </div>
    </div>
  </main>
</template>