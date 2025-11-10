import { defineStore } from 'pinia'
import axios from 'axios'
import router from '../router'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token') || null,
  }),

  actions: {
    async login(credentials) {
      try {
        const res = await axios.post('/api/login', credentials)
        this.token = res.data.token
        localStorage.setItem('token', this.token)
        await this.fetchUser()
        router.push('/dashboard')
      } catch (error) {
        console.error('Login failed:', error.response?.data || error)
        throw error
      }
    },

    async fetchUser() {
      if (!this.token) return
      try {
        const res = await axios.get('/api/me', {
          headers: { Authorization: `Bearer ${this.token}` }
        })
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