<script setup lang="ts">
import { computed } from 'vue'
import type { Product } from '@/types/product'

const props = defineProps<{
  product: Product
}>()

const emit = defineEmits(['edit', 'delete'])

const API_BASE_URL = 'http://localhost:8000'

const STOCK_STATUSES = {
  OUT_OF_STOCK: { color: 'bg-red-500', label: 'Brak w magazynie' },
  LOW_STOCK: { color: 'bg-amber-500', label: 'Niska ilość' },
  AVAILABLE: { color: 'bg-emerald-500', label: 'Dostępny' },
} as const

const PRICE_FORMAT = {
  LOCALE: 'en-US',
  STYLE: 'currency',
  CURRENCY: 'USD',
} as const

const stockStatus = computed(() => {
  const { stock_quantity, minimum_stock } = props.product
  
  if (stock_quantity === 0) return STOCK_STATUSES.OUT_OF_STOCK
  if (stock_quantity <= minimum_stock) return STOCK_STATUSES.LOW_STOCK
  return STOCK_STATUSES.AVAILABLE
})

const priceFormatted = computed(() => {
  return new Intl.NumberFormat(PRICE_FORMAT.LOCALE, { 
    style: PRICE_FORMAT.STYLE, 
    currency: PRICE_FORMAT.CURRENCY 
  }).format(props.product.price)
})
</script>

<template>
  <div
    class="group relative bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-xl transition-all duration-300 hover:scale-[1.02] flex flex-col h-full"
  >
    <!-- Image -->
    <div class="mb-4 aspect-square overflow-hidden rounded-xl bg-slate-100 dark:bg-slate-700">
      <img
        v-if="product.image"
        :src="product.image.startsWith('http') ? product.image : `${API_BASE_URL}${product.image}`"
        :alt="product.name"
        class="w-full h-full object-cover object-center transform hover:scale-110 transition-transform duration-500"
      />
      <div v-else class="w-full h-full flex items-center justify-center text-slate-400">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          width="48"
          height="48"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="1"
          stroke-linecap="round"
          stroke-linejoin="round"
        >
          <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
          <circle cx="8.5" cy="8.5" r="1.5"></circle>
          <polyline points="21 15 16 10 5 21"></polyline>
        </svg>
      </div>
    </div>

    <!-- Category & Badge -->
    <div class="mb-2 flex items-center justify-between">
      <span
        class="px-2.5 py-1 rounded-full bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 text-xs font-bold uppercase tracking-wider"
      >
        {{ product.category?.name || 'Inne' }}
      </span>
      <div class="flex items-center gap-2">
        <span :class="stockStatus.color" class="w-2 h-2 rounded-full"></span>
        <span class="text-xs font-semibold text-slate-500 dark:text-slate-400"
          >{{ stockStatus.label }} ({{ product.stock_quantity }})</span
        >
      </div>
    </div>

    <!-- Content -->
    <h3
      class="text-lg font-bold text-slate-800 dark:text-white mb-1 line-clamp-1"
      :title="product.name"
    >
      {{ product.name }}
    </h3>
    <p class="text-sm text-slate-500 dark:text-slate-400 line-clamp-2 mb-4 flex-grow">
      {{ product.description || 'Brak opisu.' }}
    </p>

    <!-- Footer -->
    <div
      class="flex items-center justify-between mt-auto pt-4 border-t border-slate-100 dark:border-slate-700"
    >
      <span class="text-xl font-bold text-slate-900 dark:text-white">
        {{ priceFormatted }}
      </span>
      <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
        <button
          @click="emit('edit', product)"
          class="p-2 text-slate-400 hover:text-indigo-500 transition-colors"
          title="Edytuj"
        >
          <!-- Edit Icon -->
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
            <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
          </svg>
        </button>
        <button
          @click="emit('delete', product)"
          class="p-2 text-slate-400 hover:text-red-500 transition-colors"
          title="Usuń"
        >
          <!-- Trash Icon -->
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
    </div>
  </div>
</template>
