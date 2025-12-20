import { defineStore } from 'pinia'
import api from '@/lib/axios'
import { ref } from 'vue'
import type { Product } from '@/types/product'

export const useProductStore = defineStore('product', () => {
  const products = ref<Product[]>([])
  const loading = ref(false)

  async function fetchProducts(filters: { search?: string; category_id?: number } = {}) {
    loading.value = true
    try {
      const params = new URLSearchParams()
      if (filters.search) params.append('search', filters.search)
      if (filters.category_id) params.append('category_id', filters.category_id.toString())

      const response = await api.get(`/products?${params.toString()}`)
      products.value = response.data
    } catch (error) {
      console.error('Failed to fetch products', error)
    } finally {
      loading.value = false
    }
  }

  async function addProduct(product: any) {
    try {
      const response = await api.post('/products', product)
      products.value.unshift(response.data)
      return response.data
    } catch (error) {
      throw error
    }
  }

  async function updateProduct(id: number, data: any) {
    try {
      let response
      if (data instanceof FormData) {
        data.append('_method', 'PUT')
        response = await api.post(`/products/${id}`, data)
      } else {
        response = await api.put(`/products/${id}`, data)
      }

      const index = products.value.findIndex((p) => p.id === id)
      if (index !== -1) {
        products.value[index] = response.data
      }
      return response.data
    } catch (error) {
      throw error
    }
  }

  async function deleteProduct(id: number) {
    try {
      await api.delete(`/products/${id}`)
      products.value = products.value.filter((p) => p.id !== id)
    } catch (error) {
      console.error(error)
    }
  }

  return { products, loading, fetchProducts, addProduct, updateProduct, deleteProduct }
})
