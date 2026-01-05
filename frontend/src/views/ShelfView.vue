<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import api from '@/lib/axios'
import type { Category } from '@/types/product'
import { useAuthStore } from '@/stores/auth'
import { useProductStore } from '@/stores/product'
import { useRouter } from 'vue-router'
import { useNotification } from '@/composables/useNotification'
import { translateErrors } from '@/lib/errorTranslations'
import ProductCard from '@/components/ProductCard.vue'
import BaseButton from '@/components/ui/BaseButton.vue'
import BaseModal from '@/components/ui/BaseModal.vue'
import BaseInput from '@/components/ui/BaseInput.vue'
import ProductForm from '@/components/ProductForm.vue'
import CategoryManagement from '@/components/CategoryManagement.vue'
import ConfirmDialog from '@/components/ConfirmDialog.vue'
import Notification from '@/components/Notification.vue'

const API_ENDPOINTS = {
  CATEGORIES: '/categories',
} as const

const CATEGORY_FILTERS = {
  ALL: '',
  UNCATEGORIZED: 'null',
} as const

const SEARCH_DEBOUNCE_MS = 300

const auth = useAuthStore()
const productStore = useProductStore()
const router = useRouter()
const { currentNotification, success, error: showError } = useNotification()
const confirmDialogRef = ref<any>(null)

const search = ref('')
const category_id = ref('')
const categories = ref<Category[]>([])
const showModal = ref(false)
const showCategoryManagement = ref(false)
const editingProduct = ref<any>(null)

const loadCategories = async () => {
  try {
    const response = await api.get(API_ENDPOINTS.CATEGORIES)
    categories.value = response.data
  } catch (error) {
    console.error('Failed to load categories:', error)
  }
}

onMounted(async () => {
  await loadCategories()
  productStore.fetchProducts()
})

const buildProductFilters = () => {
  const filters: any = { search: search.value }
  if (category_id.value === CATEGORY_FILTERS.UNCATEGORIZED) {
    filters.category_id = CATEGORY_FILTERS.UNCATEGORIZED
  } else if (category_id.value) {
    filters.category_id = parseInt(category_id.value)
  }
  return filters
}

// Search Debounce
let searchTimeout: any
watch([search, category_id], () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    const filters = buildProductFilters()
    productStore.fetchProducts(filters)
  }, SEARCH_DEBOUNCE_MS)
})

const handleLogout = async () => {
  await auth.logout()
  router.push({ name: 'login' })
}

const openAddModal = () => {
  editingProduct.value = null
  showModal.value = true
}

const openEditModal = (product: any) => {
  editingProduct.value = product
  showModal.value = true
}

const handleError = (e: any) => {
  if (e.response?.data?.errors) {
    const translatedErrors = translateErrors(e.response.data.errors)
    const errorList = translatedErrors.map((err) => `• ${err}`).join('\n')
    showError('Sprawdź poprawność danych:', errorList)
  } else if (e.response?.data?.message) {
    showError('Błąd', e.response.data.message)
  } else {
    showError('Błąd', 'Nie udało się zapisać produktu')
  }
}

const handleSubmit = async (formData: any) => {
  try {
    if (editingProduct.value) {
      await productStore.updateProduct(editingProduct.value.id, formData)
      success('Produkt zaktualizowany')
    } else {
      await productStore.addProduct(formData)
      success('Produkt dodany')
    }
    showModal.value = false
  } catch (e: any) {
    console.error('Product submission failed:', e)
    handleError(e)
  }
}

const handleDelete = async (product: any) => {
  const confirmed = await confirmDialogRef.value?.confirm({
    title: 'Usunąć produkt?',
    message: `Czy na pewno chcesz usunąć "${product.name}"? Tej operacji nie można cofnąć.`,
    confirmText: 'Usuń',
    cancelText: 'Anuluj',
    isDangerous: true,
  })

  if (!confirmed) return

  try {
    await productStore.deleteProduct(product.id)
    success('Produkt usunięty')
  } catch (e: any) {
    console.error('Delete failed:', e)
    const message = e.response?.data?.message || 'Błąd podczas usuwania produktu'
    showError(message)
  }
}

const handleCategoryManagementClose = () => {
  showCategoryManagement.value = false
}

const handleCategoryUpdated = async () => {
  await loadCategories()
  category_id.value = ''
  productStore.fetchProducts()
}
</script>

<template>
  <div class="min-h-screen bg-slate-50 dark:bg-slate-900 transition-colors">
    <!-- Navbar -->
    <nav
      class="sticky top-0 z-30 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 px-6 py-4"
    >
      <div class="max-w-7xl mx-auto flex flex-col md:flex-row gap-4 justify-between items-center">
        <div class="flex items-center gap-3">
          <div
            class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center text-white font-bold text-lg"
          >
            W
          </div>
          <h1 class="text-xl font-bold text-slate-800 dark:text-white">Wirtualna Półka</h1>
        </div>

        <div class="flex-1 w-full md:w-auto md:max-w-xl flex gap-3">
          <BaseInput
            id="search"
            v-model="search"
            placeholder="Szukaj produktów..."
            class="flex-1"
          />
          <select
            v-model="category_id"
            class="px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-indigo-500/20 outline-none"
          >
            <option value="">Wszystkie Kategorie</option>
            <option value="null">Inne</option>
            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
          </select>
          <button
            @click="showCategoryManagement = true"
            class="px-3 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors text-sm font-medium"
            title="Zarządzaj kategoriami"
          >
            ⚙️
          </button>
        </div>

        <div class="flex items-center gap-3">
          <span class="hidden md:block text-sm text-slate-500 dark:text-slate-400 font-medium">{{
            auth.user?.name
          }}</span>
          <BaseButton variant="secondary" @click="handleLogout" class="!py-2 !px-3 text-sm"
            >Wyloguj</BaseButton
          >
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-6 py-8">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Magazyn Sklepu</h2>
        <BaseButton @click="openAddModal">
          <span class="text-lg mr-1">+</span> Dodaj Produkt
        </BaseButton>
      </div>

      <!-- Loading State -->
      <div v-if="productStore.loading" class="flex justify-center p-20">
        <div
          class="w-12 h-12 border-4 border-indigo-200 border-t-indigo-600 rounded-full animate-spin"
        ></div>
      </div>

      <!-- Grid -->
      <div
        v-else-if="productStore.products.length > 0"
        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6"
      >
        <ProductCard
          v-for="product in productStore.products"
          :key="product.id"
          :product="product"
          @edit="openEditModal"
          @delete="handleDelete"
        />
      </div>

      <!-- Empty State -->
      <div
        v-else
        class="text-center py-20 bg-white dark:bg-slate-800 rounded-2xl border border-dashed border-slate-300 dark:border-slate-700"
      >
        <p class="text-xl text-slate-400 font-medium">Brak produktów na półce.</p>
        <p class="text-slate-500 mt-2">Spróbuj zmienić wyszukiwanie lub dodaj nowy produkt.</p>
      </div>
    </main>

    <!-- Modal -->
    <BaseModal
      :isOpen="showModal"
      :title="editingProduct ? 'Edytuj Produkt' : 'Dodaj Nowy Produkt'"
      @close="showModal = false"
    >
      <ProductForm
        :initialData="editingProduct"
        :loading="productStore.loading"
        @submit="handleSubmit"
      />
    </BaseModal>

    <!-- Category Management Modal -->
    <CategoryManagement
      :isOpen="showCategoryManagement"
      @close="handleCategoryManagementClose"
      @updated="handleCategoryUpdated"
    />

    <!-- Notification Toast -->
    <Notification :notification="currentNotification" />

    <!-- Confirm Dialog -->
    <ConfirmDialog ref="confirmDialogRef" />
  </div>
</template>
