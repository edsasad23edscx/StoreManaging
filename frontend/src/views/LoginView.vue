<script setup lang="ts">
import { ref } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'
import BaseInput from '@/components/ui/BaseInput.vue'
import BaseButton from '@/components/ui/BaseButton.vue'
import ThemeToggle from '@/components/ThemeToggle.vue'
import { useTheme } from '@/composables/useTheme'

// Initialize theme
useTheme()

const email = ref('')
const password = ref('')
const auth = useAuthStore()
const router = useRouter()
const loading = ref(false)
const error = ref('')

const handleLogin = async () => {
  loading.value = true
  error.value = ''
  try {
    await auth.login({ email: email.value, password: password.value })
    router.push({ name: 'shelf' })
  } catch (e: any) {
    console.error(e)
    error.value = e.response?.data?.message || e.message || 'Wystąpił błąd'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-slate-50 dark:bg-slate-900 relative overflow-hidden transition-colors">
    <!-- Theme Toggle -->
    <div class="absolute top-4 right-4 z-20">
      <ThemeToggle />
    </div>

    <!-- Login Card -->
    <div
      class="relative z-10 w-full max-w-md p-8 bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-100 dark:border-slate-700"
    >
      <div class="mb-8 text-center">
        <div
          class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-indigo-600 text-white font-bold text-xl mb-4 shadow-lg shadow-indigo-200 dark:shadow-indigo-900/50"
        >
          W
        </div>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white mb-1">Wirtualna Półka</h1>
        <p class="text-slate-500 dark:text-slate-400 text-sm">System Zarządzania Magazynem</p>
      </div>

      <form @submit.prevent="handleLogin" class="space-y-5">
        <BaseInput
          id="email"
          v-model="email"
          label="Email"
          type="email"
          placeholder="Wpisz swój email"
        />

        <BaseInput
          id="password"
          v-model="password"
          label="Hasło"
          type="password"
          placeholder="Wpisz hasło"
        />

        <div
          v-if="error"
          class="text-red-600 dark:text-red-400 text-sm text-center bg-red-50 dark:bg-red-900/20 p-3 rounded-lg border border-red-100 dark:border-red-800"
        >
          {{ error }}
        </div>

        <div class="pt-2">
          <BaseButton
            type="submit"
            variant="primary"
            class="w-full justify-center !py-2.5 !bg-indigo-600 hover:!bg-indigo-700 !text-white !font-medium"
            :disabled="loading"
          >
            <span v-if="loading" class="animate-spin mr-2">⟳</span>
            {{ loading ? 'Logowanie...' : 'Zaloguj się' }}
          </BaseButton>
        </div>
      </form>
    </div>
  </div>
</template>
