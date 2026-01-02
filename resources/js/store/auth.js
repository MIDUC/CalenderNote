import { defineStore } from 'pinia'
import api from '../api'
import router from '../router'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token') || null,
  }),

  actions: {
    async login(credentials) {
      try {
        const res = await api.post('/login', credentials)
        this.token = res.data.token
        localStorage.setItem('token', this.token)
        await this.fetchUser()
        router.push('/home')
      } catch (error) {
        console.error('Login failed:', error.response?.data || error)
        throw error
      }
    },

    async fetchUser() {
      if (!this.token) return
      try {
        const res = await api.get('/me')
        this.user = res.data
      } catch (error) {
        console.error('Fetch user failed:', error.response?.data || error)
        this.logout()
      }
    },

    logout() {
      this.user = null
      this.token = null
      localStorage.removeItem('token')
      router.push('/login')
    },
  },
})