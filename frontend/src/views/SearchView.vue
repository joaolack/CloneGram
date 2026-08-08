<script setup>
import { onMounted, ref } from 'vue'
import api from '@/api/axios'

const search = ref('')
const users = ref([])

const loading = ref(false)
const error = ref('')

async function fetchUsers() {
    loading.value = true
    error.value = ''

    try {
        const response = await api.get('/users', {
            params: {
                search: search.value || undefined,
            },
        })

        users.value = response.data.data ?? []
    } catch (err) {
        console.error(err)

        error.value =
            err.response?.data?.message ??
            'Não foi possível carregar os usuários.'
    } finally {
        loading.value = false
    }
}

async function submitSearch() {
    await fetchUsers()
}

let timeout = null

function handleSearch() {
    clearTimeout(timeout)

    timeout = setTimeout(() => {
        fetchUsers()
    }, 400)
}

onMounted(fetchUsers)
</script>

<template>
    <main>
        <h1>Pesquisar</h1>

        <form @submit.prevent="submitSearch">
            <input 
                v-model="search"
                type="search"
                placeholder="Pesquisar por nome ou username"
            />

            <button type="submit" :disabled="loading">
                {{ loading ? 'Pesquisando...' : 'Pesquisar' }}
            </button>
        </form>

        <p v-if="error">
            {{ error }}
        </p>

        <template v-else>
            <RouterLink v-for="user in users" :key="user.id" :to="`/users/${user.username}`">
                <article>
                    <img
                        v-if="user.avatar_url"
                        :src="user.avatar_url"
                        :alt="user.username"
                    />

                    <div v-else>
                        Sem foto
                    </div>

                    <div>
                        <strong>
                        {{ user.username }}
                        </strong>

                        <p>
                            {{ user.name }}
                        </p>

                        <p v-if="user.bio">
                            {{ user.bio }}
                        </p>
                    </div>
                </article>
            </RouterLink>

            <p v-if="users.length === 0">
                Nenhum usuário encontrado.
            </p>
        </template>
    </main>
</template>