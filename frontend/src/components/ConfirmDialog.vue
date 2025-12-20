<script setup lang="ts">
import { ref } from 'vue'

interface ConfirmOptions {
  title: string
  message: string
  confirmText?: string
  cancelText?: string
  isDangerous?: boolean
}

const isOpen = ref(false)
const options = ref<ConfirmOptions>({
  title: '',
  message: '',
  confirmText: 'Potwierdź',
  cancelText: 'Anuluj',
  isDangerous: false,
})
let resolveCallback: ((confirmed: boolean) => void) | null = null

const confirm = (opts: ConfirmOptions): Promise<boolean> => {
  return new Promise((resolve) => {
    options.value = {
      title: opts.title,
      message: opts.message,
      confirmText: opts.confirmText || 'Potwierdź',
      cancelText: opts.cancelText || 'Anuluj',
      isDangerous: opts.isDangerous || false,
    }
    resolveCallback = resolve
    isOpen.value = true
  })
}

const handleConfirm = () => {
  if (resolveCallback) resolveCallback(true)
  isOpen.value = false
}

const handleCancel = () => {
  if (resolveCallback) resolveCallback(false)
  isOpen.value = false
}

defineExpose({ confirm })
</script>

<template>
  <transition
    enter-active-class="transition-all duration-300"
    leave-active-class="transition-all duration-300"
    enter-from-class="opacity-0 scale-95"
    enter-to-class="opacity-100 scale-100"
    leave-from-class="opacity-100 scale-100"
    leave-to-class="opacity-0 scale-95"
  >
    <div v-if="isOpen" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
      <div class="bg-white dark:bg-slate-800 rounded-2xl p-8 max-w-md w-full shadow-2xl">
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-4">
          {{ options.title }}
        </h2>
        <p class="text-slate-600 dark:text-slate-300 mb-8 leading-relaxed">
          {{ options.message }}
        </p>
        <div class="flex gap-3 justify-end">
          <button
            @click="handleCancel"
            class="px-6 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors font-medium"
          >
            {{ options.cancelText }}
          </button>
          <button
            @click="handleConfirm"
            :class="[
              'px-6 py-2.5 rounded-lg font-medium transition-colors',
              options.isDangerous
                ? 'bg-red-600 hover:bg-red-700 text-white'
                : 'bg-indigo-600 hover:bg-indigo-700 text-white',
            ]"
          >
            {{ options.confirmText }}
          </button>
        </div>
      </div>
    </div>
  </transition>
</template>
