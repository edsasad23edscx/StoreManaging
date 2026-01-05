import { defineStore } from 'pinia'
import api from '@/lib/axios'
import { ref } from 'vue'
import type { Product } from '@/types/product'

interface ProductFilters {
  search?: string
  category_id?: number | string
}

interface ProductFormData {
  name: string
  description?: string
  category_id?: number | null
  price: number
  stock_quantity: number
  minimum_stock: number
  image?: File
}

const API_ENDPOINTS = {
  PRODUCTS: '/products',
  PRODUCT_BY_ID: (id: number) => `/products/${id}`,
} as const

export const useProductStore = defineStore('product', () => {
  const products = ref<Product[]>([])
  const loading = ref(false)

  const buildQueryParams = (filters: ProductFilters): URLSearchParams => {
    const params = new URLSearchParams()
    if (filters.search) params.append('search', filters.search)
    if (filters.category_id) params.append('category_id', filters.category_id.toString())
    return params
  }

  async function fetchProducts(filters: ProductFilters = {}) {
    loading.value = true
    try {
      const params = buildQueryParams(filters)
      const response = await api.get(`${API_ENDPOINTS.PRODUCTS}?${params.toString()}`)
      products.value = response.data
    } catch (error) {
      console.error('Failed to fetch products:', error)
      throw error
    } finally {
      loading.value = false
    }
  }

  async function addProduct(product: FormData) {
    try {
      const response = await api.post(API_ENDPOINTS.PRODUCTS, product)
      products.value.unshift(response.data)
      return response.data
    } catch (error) {
      console.error('Failed to add product:', error)
      throw error
    }
  }

  async function updateProduct(id: number, data: FormData | ProductFormData) {
    try {
      let response
      if (data instanceof FormData) {
        data.append('_method', 'PUT')
        response = await api.post(API_ENDPOINTS.PRODUCT_BY_ID(id), data)
      } else {
        response = await api.put(API_ENDPOINTS.PRODUCT_BY_ID(id), data)
      }

      const index = products.value.findIndex((p) => p.id === id)
      if (index !== -1) {
        products.value[index] = response.data
      }
      return response.data
    } catch (error) {
      console.error('Failed to update product:', error)
      throw error
    }
  }

  async function deleteProduct(id: number) {
    try {
      await api.delete(API_ENDPOINTS.PRODUCT_BY_ID(id))
      products.value = products.value.filter((p) => p.id !== id)
    } catch (error) {
      console.error('Failed to delete product:', error)
      throw error
    }
  }

  return { products, loading, fetchProducts, addProduct, updateProduct, deleteProduct }
})
