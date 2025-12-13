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

const categories = ['Warzywa', 'Owoce', 'Mięso', 'Nabiał', 'Pieczywo', 'Napoje', 'Inne'];

const fileInput = ref<File | null>(null);
const previewUrl = ref<string | null>(null);

const handleFileUpload = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        fileInput.value = target.files[0];
        previewUrl.value = URL.createObjectURL(target.files[0]);
    }
};

const handleSubmit = () => {
    const formData = new FormData();
    formData.append('name', form.value.name);
    formData.append('description', form.value.description);
    formData.append('category', form.value.category);
    formData.append('price', form.value.price);
    formData.append('stock_quantity', form.value.stock_quantity);

    if (fileInput.value) {
        formData.append('image', fileInput.value);
    }

    // Identify if it's an edit or create
    if (props.initialData?.id) {
        formData.append('_method', 'PUT'); // Laravel method spoofing for FormData
        // emit custom event because we can't pass FormData to json listener easily if expected structure differs
        // easier to emit tuple or object
    }

    emit('submit', formData);
};
</script>

<template>
    <form @submit.prevent="handleSubmit" class="space-y-4">
        <!-- Image Upload -->
        <div class="flex flex-col gap-1.5">
            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Product Image</label>
            <div class="flex items-center gap-4">
                <div v-if="previewUrl || initialData?.image" class="w-16 h-16 rounded-lg overflow-hidden bg-slate-100 border border-slate-200">
                    <img :src="previewUrl || `http://127.0.0.1:8000${initialData.image}`" class="w-full h-full object-cover">
                </div>
                <input type="file" @change="handleFileUpload" accept="image/*" class="text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"/>
            </div>
        </div>

        <BaseInput id="name" v-model="form.name" label="Nazwa Produktu" placeholder="np. Chleb Wiejski" />
        
        <div class="flex flex-col gap-1.5">
            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Kategoria</label>
            <select v-model="form.category" class="px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none">
                <option value="" disabled>Wybierz kategorię</option>
                <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
            </select>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <BaseInput id="price" v-model="form.price" label="Cena (PLN)" type="number" step="0.01" />
            <BaseInput id="stock" v-model="form.stock_quantity" label="Ilość w magazynie" type="number" />
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
</template>
