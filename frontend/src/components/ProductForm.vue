<script setup lang="ts">
import { ref, watch, computed, onMounted } from 'vue'
import api from '@/lib/axios'
import type { Category } from '@/types/product'
import BaseInput from '@/components/ui/BaseInput.vue'
import BaseButton from '@/components/ui/BaseButton.vue'
import CategoryManagement from '@/components/CategoryManagement.vue'

const props = defineProps<{
  initialData?: any
  loading?: boolean
}>()

const emit = defineEmits(['submit'])

/** Bazowy URL API */
const API_BASE_URL = '/api'
/** Maksymalny rozmiar pliku obrazka (2MB) */
const MAX_FILE_SIZE = 2 * 1024 * 1024
const API_ENDPOINTS = {
  CATEGORIES: '/categories',
} as const

/** Lista kategorii pobrana z API */
const categories = ref<Category[]>([])
const showCategoryManagement = ref(false)
/** Komunikat błędu dla pola pliku */
const fileError = ref('')
/** Wybrany plik obrazka */
const fileInput = ref<File | null>(null)
/** URL podglądu wybranego obrazka */
const previewUrl = ref<string | null>(null)

/** Komunikaty błędów walidacji dla poszczególnych pól */
const fieldErrors = ref<{
  name: string
  barcode: string
  price: string
  stock_quantity: string
  minimum_stock: string
}>({
  name: '',
  barcode: '',
  price: '',
  stock_quantity: '',
  minimum_stock: '',
})

/**
 * Zwraca nazwę aktualnego pliku obrazka.
 * Priorytet: nowo wybrany plik > istniejący obrazek z initialData
 */
const currentImageName = computed(() => {
  if (fileInput.value) {
    return fileInput.value.name
  }
  if (props.initialData?.image) {
    const imagePath = props.initialData.image
    return imagePath.split('/').pop() || 'Brak nazwy'
  }
  return null
})

const form = ref({
  name: '',
  barcode: '',
  description: '',
  category_id: null as number | null,
  price: 0,
  stock_quantity: 0,
  minimum_stock: 0,
})

/** Pobiera listę kategorii z API */
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
})

/**
 * Zwraca URL obrazka do wyświetlenia.
 * Priorytet: podgląd nowego pliku > istniejący obrazek
 */
const imageUrl = computed(() => {
  if (previewUrl.value) return previewUrl.value
  if (props.initialData?.image) {
    const imgPath = props.initialData.image
    return imgPath.startsWith('http') ? imgPath : `${API_BASE_URL}${imgPath}`
  }
  return null
})

watch(
  () => props.initialData,
  (newVal) => {
    if (newVal) {
      form.value = {
        name: newVal.name || '',
        barcode: newVal.barcode || '',
        description: newVal.description || '',
        category_id: newVal.category_id ?? null,
        price: newVal.price ?? 0,
        stock_quantity: newVal.stock_quantity ?? 0,
        minimum_stock: newVal.minimum_stock ?? 0,
      }
    } else {
      form.value = {
        name: '',
        barcode: '',
        description: '',
        category_id: null,
        price: 0,
        stock_quantity: 0,
        minimum_stock: 0,
      }
    }
  },
  { immediate: true },
)

/**
 * Sprawdza czy plik nie przekracza maksymalnego rozmiaru.
 * @param file - Plik do sprawdzenia
 * @returns true jeśli rozmiar jest OK
 */
const validateFileSize = (file: File): boolean => {
  if (file.size > MAX_FILE_SIZE) {
    fileError.value = 'Plik jest zbyt duży. Maksymalny rozmiar to 2MB.'
    return false
  }
  return true
}

/** Obsługuje wybór pliku obrazka przez użytkownika */
const handleFileUpload = (event: Event) => {
  const target = event.target as HTMLInputElement
  fileError.value = ''
  fileInput.value = null
  previewUrl.value = null

  if (target.files && target.files[0]) {
    const file = target.files[0]
    if (!validateFileSize(file)) {
      target.value = ''
      return
    }
    fileInput.value = file
    previewUrl.value = URL.createObjectURL(file)
  }
}

/**
 * Buduje obiekt FormData z danych formularza.
 * Używane do wysyłki na serwer (obsługuje pliki).
 */
const buildFormData = (): FormData => {
  const formData = new FormData()
  formData.append('name', form.value.name)
  formData.append('barcode', form.value.barcode)
  formData.append('description', form.value.description)

  const categoryId = form.value.category_id
  if (categoryId !== null) {
    formData.append('category_id', categoryId.toString())
  }

  formData.append('price', (form.value.price ?? 0).toString())
  formData.append('stock_quantity', (form.value.stock_quantity ?? 0).toString())
  formData.append('minimum_stock', (form.value.minimum_stock ?? 0).toString())

  if (fileInput.value) {
    formData.append('image', fileInput.value)
  }

  return formData
}

/** Waliduje pole nazwy produktu (wymagane) */
const validateName = (): boolean => {
  if (!form.value.name.trim()) {
    fieldErrors.value.name = 'Nazwa produktu jest wymagana'
    return false
  }
  fieldErrors.value.name = ''
  return true
}

/** Waliduje kod kreskowy (opcjonalny, 13 cyfr) */
const validateBarcode = (): boolean => {
  const barcode = form.value.barcode
  if (!barcode) {
    fieldErrors.value.barcode = ''
    return true // Barcode is optional
  }
  if (!/^\d+$/.test(barcode)) {
    fieldErrors.value.barcode = 'Kod kreskowy może zawierać tylko cyfry'
    return false
  }
  if (barcode.length !== 13) {
    fieldErrors.value.barcode = 'Kod kreskowy musi mieć dokładnie 13 cyfr'
    return false
  }
  fieldErrors.value.barcode = ''
  return true
}

/** Waliduje cenę (nie może być ujemna) */
const validatePrice = (): boolean => {
  if (form.value.price < 0) {
    fieldErrors.value.price = 'Cena nie może być ujemna'
    return false
  }
  fieldErrors.value.price = ''
  return true
}

/** Waliduje ilość w magazynie (nie może być ujemna) */
const validateStockQuantity = (): boolean => {
  if (form.value.stock_quantity < 0) {
    fieldErrors.value.stock_quantity = 'Ilość nie może być ujemna'
    return false
  }
  fieldErrors.value.stock_quantity = ''
  return true
}

/** Waliduje minimalną ilość (nie może być ujemna) */
const validateMinimumStock = (): boolean => {
  if (form.value.minimum_stock < 0) {
    fieldErrors.value.minimum_stock = 'Minimalna ilość nie może być ujemna'
    return false
  }
  fieldErrors.value.minimum_stock = ''
  return true
}

/** Waliduje wszystkie pola formularza */
const validateForm = (): boolean => {
  const nameValid = validateName()
  const barcodeValid = validateBarcode()
  const priceValid = validatePrice()
  const stockValid = validateStockQuantity()
  const minStockValid = validateMinimumStock()

  return nameValid && barcodeValid && priceValid && stockValid && minStockValid
}

/** Obsługuje wysłanie formularza po walidacji */
const handleSubmit = () => {
  fileError.value = ''

  if (!validateForm()) {
    return
  }

  const formData = buildFormData()
  emit('submit', formData)
}
</script>

<template>
  <form @submit.prevent="handleSubmit" class="space-y-4">
    <!-- Image Upload -->
    <div class="flex flex-col gap-1.5">
      <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Zdjęcie Produktu</label>
      <div class="flex items-center gap-4">
        <div
          v-if="imageUrl"
          class="w-16 h-16 rounded-lg overflow-hidden bg-slate-100 dark:bg-slate-700 border border-slate-200 dark:border-slate-600"
        >
          <img :src="imageUrl" class="w-full h-full object-cover" />
        </div>
        <div class="flex flex-col gap-1">
          <input
            type="file"
            @change="handleFileUpload"
            accept="image/*"
            class="text-sm text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 dark:file:bg-indigo-900/50 file:text-indigo-700 dark:file:text-indigo-300 hover:file:bg-indigo-100 dark:hover:file:bg-indigo-800/50 file:cursor-pointer file:transition-colors"
          />
          <p v-if="currentImageName" class="text-xs text-slate-500 dark:text-slate-400">
            Aktualny plik: <span class="font-medium">{{ currentImageName }}</span>
          </p>
        </div>
      </div>
      <p v-if="fileError" class="text-red-600 dark:text-red-400 text-sm">{{ fileError }}</p>
    </div>

    <BaseInput
      id="name"
      v-model="form.name"
      label="Nazwa Produktu"
      placeholder="np. Chleb Wiejski"
      :error="fieldErrors.name"
      @blur="validateName"
    />

    <BaseInput
      id="barcode"
      v-model="form.barcode"
      label="Kod Kreskowy (13 cyfr)"
      placeholder="np. 5901234567890"
      maxlength="13"
      pattern="[0-9]*"
      :error="fieldErrors.barcode"
      @blur="validateBarcode"
    />

    <div class="flex flex-col gap-1.5">
      <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Kategoria</label>
      <div class="flex gap-2">
        <select
          v-model="form.category_id"
          class="flex-1 px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none"
        >
          <option :value="null">Inne</option>
          <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
        </select>
        <button
          @click="showCategoryManagement = true"
          type="button"
          class="px-3 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors text-sm font-medium"
          title="Zarządzaj kategoriami"
        >
          ⚙️
        </button>
      </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
      <BaseInput
        id="price"
        v-model.number="form.price"
        label="Cena (PLN)"
        type="number"
        step="0.01"
        min="0"
        placeholder="0.00"
        :error="fieldErrors.price"
        @blur="validatePrice"
      />
      <BaseInput
        id="stock"
        v-model.number="form.stock_quantity"
        label="Ilość w magazynie"
        type="number"
        min="0"
        placeholder="0"
        :error="fieldErrors.stock_quantity"
        @blur="validateStockQuantity"
      />
    </div>

    <div class="flex flex-col gap-1.5">
      <label class="text-sm font-medium text-slate-700 dark:text-slate-300"
        >Minimalna ilość w magazynie</label
      >
      <input
        v-model.number="form.minimum_stock"
        type="number"
        min="0"
        placeholder="0"
        :class="[
          'px-4 py-2.5 rounded-lg border bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 transition-all outline-none',
          fieldErrors.minimum_stock
            ? 'border-red-400 dark:border-red-500 focus:ring-red-500/20 focus:border-red-500'
            : 'border-slate-200 dark:border-slate-700 focus:ring-indigo-500/20 focus:border-indigo-500',
        ]"
        @blur="validateMinimumStock"
      />
      <p v-if="fieldErrors.minimum_stock" class="text-xs text-red-500 dark:text-red-400">
        {{ fieldErrors.minimum_stock }}
      </p>
      <p v-else class="text-xs text-slate-500 dark:text-slate-400">
        Będzie wyświetlany alert gdy ilość spadnie poniżej tej wartości
      </p>
    </div>

    <div class="flex flex-col gap-1.5">
      <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Opis</label>
      <textarea
        v-model="form.description"
        rows="3"
        class="px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none resize-none"
      ></textarea>
    </div>

    <div class="pt-2 flex justify-end gap-2">
      <BaseButton type="submit" :disabled="loading" class="w-full">
        {{ loading ? 'Zapisywanie...' : 'Zapisz Produkt' }}
      </BaseButton>
    </div>
  </form>

  <!-- Category Management Modal -->
  <CategoryManagement
    :isOpen="showCategoryManagement"
    @close="showCategoryManagement = false"
    @updated="loadCategories"
  />
</template>
