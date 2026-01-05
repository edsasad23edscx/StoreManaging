import { defineStore } from 'pinia'
import api from '@/lib/axios'
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'

interface User {
  id: number
  name: string
  email: string
}

interface LoginCredentials {
  email: string
  password: string
}

interface LoginResponse {
  user: User
  access_token?: string
}

const STORAGE_KEYS = {
  TOKEN: 'token',
} as const

const API_ENDPOINTS = {
  LOGIN: '/login',
  LOGOUT: '/logout',
  USER: '/user',
} as const

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(null)
  const isAuthenticated = computed(() => !!user.value)
  const router = useRouter()

  const setAuthToken = (token: string) => {
    localStorage.setItem(STORAGE_KEYS.TOKEN, token)
    api.defaults.headers.common['Authorization'] = `Bearer ${token}`
  }

  const clearAuthToken = () => {
    localStorage.removeItem(STORAGE_KEYS.TOKEN)
    delete api.defaults.headers.common['Authorization']
  }

  async function login(credentials: LoginCredentials) {
    try {
      const response = await api.post<LoginResponse>(API_ENDPOINTS.LOGIN, credentials)
      user.value = response.data.user

      if (response.data.access_token) {
        setAuthToken(response.data.access_token)
      }
      return true
    } catch (error) {
      console.error('Login failed:', error)
      throw error
    }
  }

  async function logout() {
    try {
      await api.post(API_ENDPOINTS.LOGOUT)
    } catch (error) {
      console.error('Logout request failed:', error)
    } finally {
      user.value = null
      clearAuthToken()
    }
  }

  async function checkAuth() {
    const token = localStorage.getItem(STORAGE_KEYS.TOKEN)
    if (!token) return

    api.defaults.headers.common['Authorization'] = `Bearer ${token}`
    try {
      const response = await api.get<User>(API_ENDPOINTS.USER)
      user.value = response.data
    } catch (error) {
      console.error('Auth check failed:', error)
      await logout()
    }
  }

  return { user, isAuthenticated, login, logout, checkAuth }
})
