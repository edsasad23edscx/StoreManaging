import axios from 'axios'

const API_CONFIG = {
  BASE_URL: '/api',
  WITH_CREDENTIALS: true,
  HEADERS: {
    Accept: 'application/json',
  },
} as const

const api = axios.create({
  baseURL: API_CONFIG.BASE_URL,
  withCredentials: API_CONFIG.WITH_CREDENTIALS,
  headers: API_CONFIG.HEADERS,
})

export default api
