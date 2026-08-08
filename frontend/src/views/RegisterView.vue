<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth';

const router = useRouter()
const auth = useAuthStore()

const name = ref('')
const username = ref('')
const email = ref('')
const password = ref('')
const passwordConfirmation = ref('')

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
    <main>
    <h1>Criar conta</h1>

    <form @submit.prevent="register">
      <div>
        <label for="name">Nome</label>

        <input
          id="name"
          v-model="name"
          type="text"
          required
        />

        <p v-if="validationErrors.name">
          {{ validationErrors.name[0] }}
        </p>
      </div>

      <div>
        <label for="username">Username</label>

        <input
          id="username"
          v-model="username"
          type="text"
          required
        />

        <p v-if="validationErrors.username">
          {{ validationErrors.username[0] }}
        </p>
      </div>

      <div>
        <label for="email">E-mail</label>

        <input
          id="email"
          v-model="email"
          type="email"
          required
        />

        <p v-if="validationErrors.email">
          {{ validationErrors.email[0] }}
        </p>
      </div>

      <div>
        <label for="password">Senha</label>

        <input
          id="password"
          v-model="password"
          type="password"
          required
        />

        <p v-if="validationErrors.password">
          {{ validationErrors.password[0] }}
        </p>
      </div>

      <div>
        <label for="password_confirmation">
          Confirmar senha
        </label>

        <input
          id="password_confirmation"
          v-model="passwordConfirmation"
          type="password"
          required
        />
      </div>

      <p v-if="error">
        {{ error }}
      </p>

      <button
        type="submit"
        :disabled="auth.loading"
      >
        {{ auth.loading ? 'Criando conta...' : 'Criar conta' }}
      </button>
    </form>

    <RouterLink to="/login">
      Já possui uma conta? Entrar
    </RouterLink>'
  </main>
</template>