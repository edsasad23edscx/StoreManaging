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
        error.value = e.response?.data?.message || e.message || 'Wystąpił błąd';
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <div class="min-h-screen flex items-center justify-center bg-slate-50 relative overflow-hidden">
         <!-- Login Card -->
         <div class="relative z-10 w-full max-w-md p-8 bg-white rounded-2xl shadow-xl border border-slate-100">
            <div class="mb-8 text-center">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-indigo-600 text-white font-bold text-xl mb-4 shadow-lg shadow-indigo-200">W</div>
                <h1 class="text-2xl font-bold text-slate-900 mb-1">Wirtualna Półka</h1>
                <p class="text-slate-500 text-sm">System Zarządzania Magazynem</p>
            </div>

            <form @submit.prevent="handleLogin" class="space-y-5">
                <BaseInput 
                    id="email" 
                    v-model="email" 
                    label="Nazwa użytkownika" 
                    type="email" 
                    placeholder="Wpisz nazwę użytkownika" 
                />
                
                <BaseInput 
                    id="password" 
                    v-model="password" 
                    label="Hasło" 
                    type="password" 
                    placeholder="Wpisz hasło" 
                />
                
                <div v-if="error" class="text-red-600 text-sm text-center bg-red-50 p-3 rounded-lg border border-red-100">
                    {{ error }}
                </div>

                <div class="pt-2">
                    <BaseButton type="submit" variant="primary" class="w-full justify-center !py-2.5 !bg-indigo-600 hover:!bg-indigo-700 !text-white !font-medium" :disabled="loading">
                        <span v-if="loading" class="animate-spin mr-2">⟳</span>
                        {{ loading ? 'Logowanie...' : 'Zaloguj się' }}
                    </BaseButton>
                </div>

                <!-- Demo Credentials -->
                <div class="mt-6 p-4 bg-slate-50 rounded-lg border border-slate-200 text-xs text-slate-600">
                    <p class="font-semibold mb-1">Dane logowania demo:</p>
                    <p>Użytkownik: demo</p>
                    <p>Hasło: demo</p>
                </div>
            </form>
         </div>
    </div>
</template>
