<script setup lang="ts">
import { ref } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { useRouter } from 'vue-router';
import BaseInput from '@/components/ui/BaseInput.vue';
import BaseButton from '@/components/ui/BaseButton.vue';

const email = ref('');
const password = ref('');
const auth = useAuthStore();
const router = useRouter();
const loading = ref(false);
const error = ref('');

const handleLogin = async () => {
    loading.value = true;
    error.value = '';
    try {
        await auth.login({ email: email.value, password: password.value });
        router.push({ name: 'shelf' });
    } catch (e: any) {
        console.error(e);
        error.value = e.response?.data?.message || e.message || 'An error occurred';
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <div class="min-h-screen flex items-center justify-center relative overflow-hidden bg-slate-900">
        <!-- Animated Background Effects -->
         <div class="absolute inset-0 z-0">
            <div class="absolute top-[-10%] left-[-10%] w-[60%] h-[60%] bg-indigo-600/20 rounded-full blur-[100px] animate-pulse"></div>
            <div class="absolute bottom-[-10%] right-[-10%] w-[60%] h-[60%] bg-purple-600/20 rounded-full blur-[100px] animate-pulse delay-700"></div>
         </div>

         <!-- Login Card -->
         <div class="relative z-10 w-full max-w-md p-8 md:p-10 bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl">
            <div class="mb-10 text-center">
                <h1 class="text-3xl font-bold text-white mb-2 tracking-tight">Wirtualna Półka</h1>
                <p class="text-slate-400">Warehouse Management System</p>
            </div>

            <form @submit.prevent="handleLogin" class="space-y-6">
                <BaseInput 
                    id="email" 
                    v-model="email" 
                    label="Email Address" 
                    type="email" 
                    placeholder="admin@store.com" 
                />
                
                <BaseInput 
                    id="password" 
                    v-model="password" 
                    label="Password" 
                    type="password" 
                    placeholder="••••••••" 
                />
                
                <div v-if="error" class="text-red-400 text-sm text-center bg-red-500/10 p-3 rounded-lg border border-red-500/20">
                    {{ error }}
                </div>

                <div class="pt-2">
                    <BaseButton type="submit" variant="primary" class="w-full justify-center !py-3 !text-lg" :disabled="loading">
                        <span v-if="loading" class="animate-spin mr-2">⟳</span>
                        {{ loading ? 'Signing in...' : 'Sign In' }}
                    </BaseButton>
                </div>
            </form>
         </div>
    </div>
</template>
