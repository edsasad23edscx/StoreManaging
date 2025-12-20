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

const API_BASE_URL = 'http://localhost:8000'
const categories = ref<Category[]>([])
const showCategoryManagement = ref(false)
const fileError = ref('')

const form = ref({
  name: '',
  description: '',
  category_id: null as number | null,
  price: 0,
  stock_quantity: 0,
  minimum_stock: 0,
})

const loadCategories = async () => {
  try {
    const response = await api.get('/categories')
    categories.value = response.data
  } catch (error) {
    console.error('Failed to load categories', error)
  }
}

onMounted(async () => {
  await loadCategories()
})

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
        description: newVal.description || '',
        category_id: newVal.category_id ?? null,
        price: newVal.price ?? 0,
        stock_quantity: newVal.stock_quantity ?? 0,
        minimum_stock: newVal.minimum_stock ?? 0,
      }
    } else {
      form.value = {
        name: '',
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

const fileInput = ref<File | null>(null)
const previewUrl = ref<string | null>(null)

const handleFileUpload = (event: Event) => {
  const target = event.target as HTMLInputElement
  fileError.value = ''
  if (target.files && target.files[0]) {
    const file = target.files[0]
    if (file.size > 2 * 1024 * 1024) {
      fileError.value = 'Plik jest zbyt duży. Maksymalny rozmiar to 2MB.'
      target.value = '' // Reset input
      fileInput.value = null
      previewUrl.value = null
      return
    }
    fileInput.value = file
    previewUrl.value = URL.createObjectURL(file)
  }
}

const handleSubmit = () => {
  fileError.value = ''
  const formData = new FormData()
  formData.append('name', form.value.name)
  formData.append('description', form.value.description)
  const categoryId = form.value.category_id ?? null
  if (categoryId !== null) {
    formData.append('category_id', categoryId.toString())
  }
  formData.append('price', (form.value.price ?? 0).toString())
  formData.append('stock_quantity', (form.value.stock_quantity ?? 0).toString())
  formData.append('minimum_stock', (form.value.minimum_stock ?? 0).toString())

  if (fileInput.value) {
    formData.append('image', fileInput.value)
  }

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
          class="w-16 h-16 rounded-lg overflow-hidden bg-slate-100 border border-slate-200"
        >
          <img :src="imageUrl" class="w-full h-full object-cover" />
        </div>
        <input
          type="file"
          @change="handleFileUpload"
          accept="image/*"
          class="text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
        />
      </div>
      <p v-if="fileError" class="text-red-600 dark:text-red-400 text-sm">{{ fileError }}</p>
    </div>

    <BaseInput
      id="name"
      v-model="form.name"
      label="Nazwa Produktu"
      placeholder="np. Chleb Wiejski"
    />

    <div class="flex flex-col gap-1.5">
      <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Kategoria</label>
      <div class="flex gap-2">
        <select
          v-model.number="form.category_id"
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
      />
      <BaseInput
        id="stock"
        v-model.number="form.stock_quantity"
        label="Ilość w magazynie"
        type="number"
        min="0"
        placeholder="0"
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
        class="px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none"
      />
      <p class="text-xs text-slate-500">
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
