<script setup lang="ts">
import { ref, watch } from 'vue';
import BaseInput from '@/components/ui/BaseInput.vue';
import BaseButton from '@/components/ui/BaseButton.vue';

const props = defineProps<{
    initialData?: any;
    loading?: boolean;
}>();

const emit = defineEmits(['submit']);

const form = ref({
    name: '',
    description: '',
    category: '',
    price: '',
    stock_quantity: ''
});

watch(() => props.initialData, (newVal) => {
    if (newVal) {
        form.value = { ...newVal };
    } else {
        form.value = { name: '', description: '', category: '', price: '', stock_quantity: '' };
    }
}, { immediate: true });

const categories = ['Electronics', 'Groceries', 'Clothing', 'Books', 'Tools', 'Other'];

const handleSubmit = () => {
    emit('submit', form.value);
};
</script>

<template>
    <form @submit.prevent="handleSubmit" class="space-y-4">
        <BaseInput id="name" v-model="form.name" label="Product Name" placeholder="e.g. Wireless Mouse" />
        
        <div class="flex flex-col gap-1.5">
            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Category</label>
            <select v-model="form.category" class="px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none">
                <option value="" disabled>Select a category</option>
                <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
            </select>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <BaseInput id="price" v-model="form.price" label="Price ($)" type="number" step="0.01" />
            <BaseInput id="stock" v-model="form.stock_quantity" label="Stock Quantity" type="number" />
        </div>

        <div class="flex flex-col gap-1.5">
            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Description</label>
            <textarea 
                v-model="form.description" 
                rows="3"
                class="px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none resize-none"
            ></textarea>
        </div>

        <div class="pt-2 flex justify-end gap-2">
            <BaseButton type="submit" :disabled="loading" class="w-full">
                {{ loading ? 'Saving...' : 'Save Product' }}
            </BaseButton>
        </div>
    </form>
</template>
