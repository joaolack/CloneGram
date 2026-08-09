<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth';
import { Eye, EyeOff } from 'lucide-vue-next'

const router = useRouter()
const auth = useAuthStore()

const name = ref('')
const username = ref('')
const email = ref('')
const password = ref('')
const passwordConfirmation = ref('')
const showPassword = ref(false)
const showPasswordConfirmation = ref(false)

const error = ref('')
const validationErrors = ref({})

async function register() {
    error.value = ''
    validationErrors.value = {}

    try {
        await auth.register({
            name: name.value,
            username: username.value,
            email: email.value,
            password: password.value,
            password_confirmation: passwordConfirmation.value,
        })
    } catch (err) {
        validationErrors.value = err.response?.data?.errors ?? {} 

        error.value = err.response?.data?.message ?? 'Não foi possivel realizar o cadastro.'

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
      <section
        class="
          rounded-2xl
          bg-white
          px-8
          py-9
          shadow-2xl
        "
      >
        <h1
          class="
            mb-3
            text-center
            text-4xl
            font-bold
            tracking-tight
            text-gray-900
          "
        >
          CloneGram
        </h1>

        <p
          class="
            mb-7
            text-center
            text-sm
            leading-5
            text-gray-500
          "
        >
          Cadastre-se para compartilhar fotos
          e interagir com outros usuários.
        </p>

        <form
          class="space-y-3"
          @submit.prevent="register"
        >
          <!-- Nome -->
          <div>
            <input
              v-model="name"
              type="text"
              placeholder="Nome completo"
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
                outline-none
                transition
                focus:border-gray-500
                focus:bg-white
                focus:ring-1
                focus:ring-gray-400
              "
            />

            <p
              v-if="validationErrors.name"
              class="mt-1 text-xs text-red-600"
            >
              {{ validationErrors.name[0] }}
            </p>
          </div>

          <!-- Username -->
          <div>
            <input
              v-model="username"
              type="text"
              placeholder="Nome de usuário"
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
                outline-none
                transition
                focus:border-gray-500
                focus:bg-white
                focus:ring-1
                focus:ring-gray-400
              "
            />

            <p
              v-if="validationErrors.username"
              class="mt-1 text-xs text-red-600"
            >
              {{ validationErrors.username[0] }}
            </p>
          </div>

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
                outline-none
                transition
                focus:border-gray-500
                focus:bg-white
                focus:ring-1
                focus:ring-gray-400
              "
            />

            <p
              v-if="validationErrors.email"
              class="mt-1 text-xs text-red-600"
            >
              {{ validationErrors.email[0] }}
            </p>
          </div>

          <!-- Senha -->
          <div>
            <div class="relative">
              <input
                v-model="password"
                :type="showPassword ? 'text' : 'password'"
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
                  pr-12
                  text-sm
                  outline-none
                  transition
                  focus:border-gray-500
                  focus:bg-white
                  focus:ring-1
                  focus:ring-gray-400
                "
              />

              <button
                type="button"
                :aria-label="showPassword ? 'Ocultar senha' : 'Ver senha'"
                class="
                  absolute
                  inset-y-0
                  right-0
                  flex
                  w-12
                  items-center
                  justify-center
                  text-gray-400
                  transition
                  hover:text-gray-700
                "
                @click="showPassword = !showPassword"
              >
                <EyeOff
                  v-if="showPassword"
                  class="h-5 w-5"
                  :stroke-width="1.8"
                />

                <Eye
                  v-else
                  class="h-5 w-5"
                  :stroke-width="1.8"
                />
              </button>
            </div>

            <p
              v-if="validationErrors.password"
              class="mt-1 text-xs text-red-600"
            >
              {{ validationErrors.password[0] }}
            </p>
          </div>

          <!-- Confirmar senha -->
          <div class="relative">
            <input
              v-model="passwordConfirmation"
              :type="showPasswordConfirmation ? 'text' : 'password'"
              placeholder="Confirme sua senha"
              required
              class="
                w-full
                rounded-md
                border
                border-gray-300
                bg-gray-50
                px-4
                py-3
                pr-12
                text-sm
                outline-none
                transition
                focus:border-gray-500
                focus:bg-white
                focus:ring-1
                focus:ring-gray-400
              "
            />

            <button
              type="button"
              :aria-label="
                showPasswordConfirmation
                  ? 'Ocultar confirmação de senha'
                  : 'Ver confirmação de senha'
              "
              class="
                absolute
                inset-y-0
                right-0
                flex
                w-12
                items-center
                justify-center
                text-gray-400
                transition
                hover:text-gray-700
              "
              @click="
                showPasswordConfirmation =
                  !showPasswordConfirmation
              "
            >
              <EyeOff
                v-if="showPasswordConfirmation"
                class="h-5 w-5"
                :stroke-width="1.8"
              />

              <Eye
                v-else
                class="h-5 w-5"
                :stroke-width="1.8"
              />
            </button>
          </div>

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
            {{
              auth.loading
                ? 'Criando conta...'
                : 'Cadastre-se'
            }}
          </button>
        </form>
      </section>

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
          Já tem uma conta?

          <RouterLink
            to="/login"
            class="
              font-semibold
              text-sky-500
              hover:text-sky-600
            "
          >
            Entrar
          </RouterLink>
        </p>
      </section>
    </div>
  </main>
</template>
