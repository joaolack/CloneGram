<script setup>
import { onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()

onMounted(async () => {
  console.log('Home montada')

  try {
    console.log('Chamando fetchUser')
    await auth.fetchUser()
    console.log('Usuário:', auth.user)
  } catch (error) {
    console.error('Erro no fetchUser:', error)
  }
})
</script>

<template>
  <main>
    <h1>Home</h1>

    <p v-if="auth.user">
      Bem-vindo, {{ auth.user.name }}
    </p>
  </main>
</template>