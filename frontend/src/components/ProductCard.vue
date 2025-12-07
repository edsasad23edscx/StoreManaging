<script setup lang="ts">
import { computed } from 'vue';

interface Product {
    id: number;
    name: string;
    description: string;
    category: string;
    price: number;
    stock_quantity: number;
}

const props = defineProps<{
    product: Product;
}>();

const emit = defineEmits(['edit', 'delete']);

const stockStatus = computed(() => {
    if (props.product.stock_quantity === 0) return { color: 'bg-red-500', label: 'Out of Stock' };
    if (props.product.stock_quantity < 10) return { color: 'bg-amber-500', label: 'Low Stock' };
    return { color: 'bg-emerald-500', label: 'In Stock' };
});

const priceFormatted = computed(() => {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(props.product.price);
});
</script>

<template>
    <div class="group relative bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-xl transition-all duration-300 hover:scale-[1.02] flex flex-col h-full">
        <!-- Badge -->
        <div class="absolute top-4 right-4 flex items-center gap-2">
            <span :class="stockStatus.color" class="w-2.5 h-2.5 rounded-full animate-pulse"></span>
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">{{ stockStatus.label }} ({{ product.stock_quantity }})</span>
        </div>

        <!-- Category -->
        <div class="mb-2">
            <span class="px-2.5 py-1 rounded-full bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 text-xs font-bold uppercase tracking-wider">
                {{ product.category }}
            </span>
        </div>

        <!-- Content -->
        <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-1 line-clamp-1" :title="product.name">
            {{ product.name }}
        </h3>
        <p class="text-sm text-slate-500 dark:text-slate-400 line-clamp-2 mb-4 flex-grow">
            {{ product.description || 'No description available.' }}
        </p>

        <!-- Footer -->
        <div class="flex items-center justify-between mt-auto pt-4 border-t border-slate-100 dark:border-slate-700">
            <span class="text-xl font-bold text-slate-900 dark:text-white">
                {{ priceFormatted }}
            </span>
            <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                <button @click="emit('edit', product)" class="p-2 text-slate-400 hover:text-indigo-500 transition-colors" title="Edit">
                    <!-- Edit Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg>
                </button>
                <button @click="emit('delete', product)" class="p-2 text-slate-400 hover:text-red-500 transition-colors" title="Delete">
                    <!-- Trash Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                </button>
            </div>
        </div>
    </div>
</template>
