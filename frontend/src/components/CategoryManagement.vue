<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import api from '@/lib/axios'
import type { Category } from '@/types/product'
import BaseInput from '@/components/ui/BaseInput.vue'
import BaseButton from '@/components/ui/BaseButton.vue'
import ConfirmDialog from '@/components/ConfirmDialog.vue'

const props = defineProps<{
  isOpen: boolean
}>()

const emit = defineEmits(['close', 'updated'])

const API_ENDPOINTS = {
  CATEGORIES: '/categories',
  CATEGORY_BY_ID: (id: number) => `/categories/${id}`,
} as const

const ERROR_MESSAGES = {
  EMPTY_NAME: 'Nazwa kategorii nie może być pusta',
  LOAD_FAILED: 'Nie udało się załadować kategorii',
  ADD_FAILED: 'Nie udało się dodać kategorii',
  DELETE_FAILED: 'Nie udało się usunąć kategorii',
} as const

const categories = ref<Category[]>([])
const newCategoryName = ref('')
const confirmDialogRef = ref<any>(null)
const loading = ref(false)
const error = ref('')

onMounted(() => {
  if (props.isOpen) {
    loadCategories()
  }
})

watch(
  () => props.isOpen,
  (newVal) => {
    if (newVal) {
      loadCategories()
    }
  },
)

const loadCategories = async () => {
  try {
    const response = await api.get(API_ENDPOINTS.CATEGORIES)
    categories.value = response.data
    error.value = ''
  } catch (e) {
    console.error('Failed to load categories:', e)
    error.value = ERROR_MESSAGES.LOAD_FAILED
  }
}

const validateCategoryName = (): boolean => {
  if (!newCategoryName.value.trim()) {
    error.value = ERROR_MESSAGES.EMPTY_NAME
    return false
  }
  return true
}

const addCategory = async () => {
  if (!validateCategoryName()) return

  loading.value = true
  error.value = ''

  try {
    const response = await api.post(API_ENDPOINTS.CATEGORIES, {
      name: newCategoryName.value,
    })
    categories.value.push(response.data)
    newCategoryName.value = ''
    emit('updated')
  } catch (e: any) {
    console.error('Failed to add category:', e)
    error.value = e.response?.data?.message || ERROR_MESSAGES.ADD_FAILED
  } finally {
    loading.value = false
  }
}

const deleteCategory = async (categoryId: number, categoryName: string) => {
  const confirmed = await confirmDialogRef.value?.confirm({
    title: 'Usunąć kategorię?',
    message: `Czy na pewno chcesz usunąć kategorię "${categoryName}"? Produkty z tej kategorii zostaną przypisane do "Inne".`,
    confirmText: 'Usuń',
    cancelText: 'Anuluj',
    isDangerous: true,
  })

  if (!confirmed) return

  error.value = ''

  try {
    await api.delete(API_ENDPOINTS.CATEGORY_BY_ID(categoryId))
    categories.value = categories.value.filter((c) => c.id !== categoryId)
    emit('updated')
  } catch (e: any) {
    console.error('Failed to delete category:', e)
    error.value = e.response?.data?.message || ERROR_MESSAGES.DELETE_FAILED
  }
}

const handleClose = () => {
  newCategoryName.value = ''
  error.value = ''
  emit('close')
}
</script>

<template>
  <div v-if="isOpen" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white dark:bg-slate-800 rounded-2xl p-8 max-w-md w-full mx-4 shadow-xl">
      <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-6">
        Zarządzanie Kategoriami
      </h2>

      <div
        v-if="error"
        class="mb-4 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg text-red-600 dark:text-red-400 text-sm"
      >
        {{ error }}
      </div>

      <!-- Add New Category -->
      <div class="mb-6 p-4 bg-slate-50 dark:bg-slate-700/50 rounded-lg">
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2"
          >Dodaj nową kategorię</label
        >
        <div class="flex gap-2">
          <input
            v-model="newCategoryName"
            type="text"
            placeholder="np. Elektronika"
            class="flex-1 px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none"
            @keyup.enter="addCategory"
          />
          <BaseButton
            variant="primary"
            :disabled="loading"
            @click="addCategory"
            class="!py-2 !px-3"
          >
            Dodaj
          </BaseButton>
        </div>
      </div>

      <!-- Categories List -->
      <div class="space-y-2 max-h-96 overflow-y-auto">
        <div
          v-for="category in categories"
          :key="category.id"
          class="flex items-center justify-between p-3 bg-slate-100 dark:bg-slate-700 rounded-lg"
        >
          <span class="text-slate-800 dark:text-white font-medium">{{ category.name }}</span>
          <button
            @click="deleteCategory(category.id, category.name)"
            class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition-colors"
            title="Usuń kategorię"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="18"
              height="18"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <polyline points="3 6 5 6 21 6"></polyline>
              <path
                d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"
              ></path>
            </svg>
          </button>
        </div>

        <div
          v-if="categories.length === 0"
          class="text-center py-8 text-slate-500 dark:text-slate-400"
        >
          Brak kategorii. Dodaj nową!
        </div>
      </div>

      <!-- Close Button -->
      <div class="mt-6 flex justify-end gap-2">
        <BaseButton variant="secondary" @click="handleClose" class="!py-2 !px-4">
          Zamknij
        </BaseButton>
      </div>
    </div>
  </div>

  <!-- Confirm Dialog -->
  <ConfirmDialog ref="confirmDialogRef" />
</template>
