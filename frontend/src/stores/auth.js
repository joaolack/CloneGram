import { defineStore } from 'pinia'
import api from '@/api/axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token'),
    loading: false,
  }),

  getters: {
    isAutheticated: (state) => !!state.token,
  },

  actions: {
    async login(credentials) {
      this.loading = true

      try {
        const response = await api.post('/login', credentials)

        this.token = response.data.token
        this.user = response.data.user

        localStorage.setItem('token', this.token)

        return response.data
      } finally {
        this.loading = false
      }
    },

    async register(data) {
      this.loading = true

      try {
        const response = await api.post('/register', data)

        this.token = response.data.token
        this.user = response.data.user

        localStorage.setItem('token', this.token)

        return response.data
      } finally {
        this.loading = false
      }
    },

    async fetchUser() {
      if (!this.token) {
        return
      }

      try {
        const response = await api.get('/me')

        this.user = response.data.data ?? response.data
      } catch (error) {
        if (error.response?.status === 401) {
          this.user = null
          this.token = null

          localStorage.removeItem('token')
        }

        throw error
      }
    },

    async logout() {
      try {
        await api.post('/logout')
      } finally {
        this.user = null
        this.token = null

        localStorage.removeItem('token')
      }
    },
  },
})