<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const auth = useAuthStore()

const email = ref('')
const password = ref('')
const error = ref('')

async function login() {
    error.value = ''

    try {
        await auth.login({
            email: email.value,
            password: password.value,
        })
    } catch (err) {
        console.error('Erro no login', err)
        error.value =
            err.response?.data?.message ?? 'Não foi possível realizar login.'
        
        return
    }

    await router.replace('/')
}
</script>
<template>
  <main
    class="
      min-h-screen
      flex
      items-center
      justify-center
      px-4
      py-10
      bg-gradient-to-br
      from-indigo-600
      via-fuchsia-600
      to-orange-400
    "
  >
    <div class="w-full max-w-sm">
      <!-- Card principal -->
      <section
        class="
          bg-white
          rounded-2xl
          shadow-2xl
          px-8
          py-10
        "
      >
        
        <h1
          class="text-center text-4xl font-bold tracking-tight text-gray-900 mb-10">
          CloneGram
        </h1>

        <form
          class="space-y-3"
          @submit.prevent="login"
        >
          <!-- E-mail -->
          <div>
            <input
              v-model="email"
              type="email"
              placeholder="E-mail"
              required
              class="
                w-full
                rounded-md
                border
                border-gray-300
                bg-gray-50
                px-4
                py-3
                text-sm
                text-gray-900
                outline-none
                transition
                placeholder:text-gray-400
                focus:border-gray-500
                focus:bg-white
                focus:ring-1
                focus:ring-gray-400
              "
            />
          </div>

          <!-- Senha -->
          <div>
            <input
              v-model="password"
              type="password"
              placeholder="Senha"
              required
              class="
                w-full
                rounded-md
                border
                border-gray-300
                bg-gray-50
                px-4
                py-3
                text-sm
                text-gray-900
                outline-none
                transition
                placeholder:text-gray-400
                focus:border-gray-500
                focus:bg-white
                focus:ring-1
                focus:ring-gray-400
              "
            />
          </div>

          <!-- Erro -->
          <p
            v-if="error"
            class="
              text-center
              text-sm
              text-red-600
            "
          >
            {{ error }}
          </p>

          <!-- Entrar -->
          <button
            type="submit"
            :disabled="auth.loading"
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
              disabled:opacity-60
            "
          >
            {{ auth.loading ? 'Entrando...' : 'Entrar' }}
          </button>
        </form>

        <!-- Separador -->
        <div
          class="
            my-7
            flex
            items-center
            gap-4
          "
        >
          <div class="h-px flex-1 bg-gray-300"></div>

          <span
            class="
              text-xs
              font-semibold
              uppercase
              text-gray-400
            "
          >
            ou
          </span>

          <div class="h-px flex-1 bg-gray-300"></div>
        </div>

        <p
          class="
            text-center
            text-sm
            text-gray-500
          "
        >
          Acesse sua conta para continuar.
        </p>
      </section>

      <!-- Card inferior -->
      <section
        class="
          mt-4
          rounded-2xl
          bg-white
          px-6
          py-5
          text-center
          shadow-lg
        "
      >
        <p class="text-sm text-gray-600">
          Não tem uma conta?

          <RouterLink
            to="/register"
            class="
              font-semibold
              text-sky-500
              hover:text-sky-600
            "
          >
            Cadastre-se
          </RouterLink>
        </p>
      </section>
    </div>
  </main>
</template>