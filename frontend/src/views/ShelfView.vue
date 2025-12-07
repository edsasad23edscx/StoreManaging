<script setup lang="ts">
import { ref, onMounted, watch } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { useProductStore } from '@/stores/product';
import { useRouter } from 'vue-router';
import ProductCard from '@/components/ProductCard.vue';
import BaseButton from '@/components/ui/BaseButton.vue';
import BaseModal from '@/components/ui/BaseModal.vue';
import BaseInput from '@/components/ui/BaseInput.vue';
import ProductForm from '@/components/ProductForm.vue';

const auth = useAuthStore();
const productStore = useProductStore();
const router = useRouter();

const search = ref('');
const category = ref('');
const showModal = ref(false);
const editingProduct = ref<any>(null);
const categories = ['Electronics', 'Groceries', 'Clothing', 'Books', 'Tools'];

// Search Debounce could be added here, for now simpler watch
let searchTimeout: any;
watch([search, category], () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        productStore.fetchProducts({ search: search.value, category: category.value });
    }, 300);
});

onMounted(() => {
    productStore.fetchProducts();
});

const handleLogout = async () => {
    await auth.logout();
    router.push({ name: 'login' });
};

const openAddModal = () => {
    editingProduct.value = null;
    showModal.value = true;
};

const openEditModal = (product: any) => {
    editingProduct.value = product;
    showModal.value = true;
};

const handleSubmit = async (formData: any) => {
    try {
        if (editingProduct.value) {
            await productStore.updateProduct(editingProduct.value.id, formData);
        } else {
            await productStore.addProduct(formData);
        }
        showModal.value = false;
    } catch (e) {
        alert('Error saving product');
    }
};

const handleDelete = async (product: any) => {
    if (confirm('Are you sure you want to delete ' + product.name + '?')) {
        await productStore.deleteProduct(product.id);
    }
};
</script>

<template>
    <div class="min-h-screen bg-slate-50 dark:bg-slate-900 transition-colors">
        <!-- Navbar -->
        <nav class="sticky top-0 z-30 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 px-6 py-4">
            <div class="max-w-7xl mx-auto flex flex-col md:flex-row gap-4 justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center text-white font-bold text-lg">W</div>
                    <h1 class="text-xl font-bold text-slate-800 dark:text-white">Wirtualna Półka</h1>
                </div>

                <div class="flex-1 w-full md:w-auto md:max-w-xl flex gap-3">
                    <BaseInput id="search" v-model="search" placeholder="Search products..." class="flex-1" />
                    <select v-model="category" class="px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-indigo-500/20 outline-none">
                        <option value="">All Categories</option>
                        <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
                    </select>
                </div>

                <div class="flex items-center gap-3">
                     <span class="hidden md:block text-sm text-slate-500 dark:text-slate-400 font-medium">{{ auth.user?.name }}</span>
                     <BaseButton variant="secondary" @click="handleLogout" class="!py-2 !px-3 text-sm">Logout</BaseButton>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-6 py-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Shelf Inventory</h2>
                <BaseButton @click="openAddModal">
                    <span class="text-lg mr-1">+</span> Add Product
                </BaseButton>
            </div>

            <!-- Loading State -->
            <div v-if="productStore.loading" class="flex justify-center p-20">
                <div class="w-12 h-12 border-4 border-indigo-200 border-t-indigo-600 rounded-full animate-spin"></div>
            </div>

            <!-- Grid -->
            <div v-else-if="productStore.products.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <ProductCard 
                    v-for="product in productStore.products" 
                    :key="product.id" 
                    :product="product"
                    @edit="openEditModal"
                    @delete="handleDelete"
                />
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-20 bg-white dark:bg-slate-800 rounded-2xl border border-dashed border-slate-300 dark:border-slate-700">
                <p class="text-xl text-slate-400 font-medium">No products found on the shelf.</p>
                <p class="text-slate-500 mt-2">Try adjusting your search or add a new product.</p>
            </div>
        </main>

        <!-- Modal -->
        <BaseModal :isOpen="showModal" :title="editingProduct ? 'Edit Product' : 'Add New Product'" @close="showModal = false">
            <ProductForm 
                :initialData="editingProduct" 
                :loading="productStore.loading"
                @submit="handleSubmit"
            />
        </BaseModal>
    </div>
</template>
