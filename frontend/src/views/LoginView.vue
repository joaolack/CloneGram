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
    <main>
        <h1>Login</h1>

        <form @submit.prevent="login">
            <div>
                <label for="email">E-mail</label>

                <input
                    id="email"
                    v-model="email"
                    type="email"
                    required
                />
            </div>

            <div>
                <label for="password">Senha</label>

                <input
                    id="password"
                    v-model="password"
                    type="password"
                    required
                />
            </div>

            <p v-if="error">
                {{ error }}
            </p>

            <button type="submnit" :disabled="auth.loading">
                {{ auth.loading ? 'Entrando...' : 'Entrar'}}
            </button>
        </form>
    </main>
</template>