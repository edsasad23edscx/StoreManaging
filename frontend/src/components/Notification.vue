<script setup lang="ts">
import { ref, watch } from 'vue'

interface Notification {
  id: string
  type: 'success' | 'error' | 'info' | 'warning'
  message: string
  details?: string
  duration?: number
}

const props = defineProps<{
  notification?: Notification | null
}>()

const emit = defineEmits(['close'])
const visible = ref(false)
let timeoutId: any = null

watch(
  () => props.notification,
  (newVal) => {
    if (newVal) {
      visible.value = true
      if (timeoutId) clearTimeout(timeoutId)
      timeoutId = setTimeout(() => {
        visible.value = false
        emit('close')
      }, newVal.duration || 5000)
    }
  },
)

const close = () => {
  visible.value = false
  if (timeoutId) clearTimeout(timeoutId)
  emit('close')
}

const typeConfig = {
  success: {
    bg: 'bg-emerald-50 dark:bg-emerald-900/20',
    border: 'border-emerald-200 dark:border-emerald-800',
    text: 'text-emerald-700 dark:text-emerald-400',
  },
  error: {
    bg: 'bg-red-50 dark:bg-red-900/20',
    border: 'border-red-200 dark:border-red-800',
    text: 'text-red-700 dark:text-red-400',
  },
  warning: {
    bg: 'bg-amber-50 dark:bg-amber-900/20',
    border: 'border-amber-200 dark:border-amber-800',
    text: 'text-amber-700 dark:text-amber-400',
  },
  info: {
    bg: 'bg-blue-50 dark:bg-blue-900/20',
    border: 'border-blue-200 dark:border-blue-800',
    text: 'text-blue-700 dark:text-blue-400',
  },
}
</script>

<template>
  <transition
    enter-active-class="transition-all duration-300"
    leave-active-class="transition-all duration-300"
    enter-from-class="opacity-0 translate-y-4"
    enter-to-class="opacity-100 translate-y-0"
    leave-from-class="opacity-100 translate-y-0"
    leave-to-class="opacity-0 translate-y-4"
  >
    <div
      v-if="visible && notification"
      :class="[
        typeConfig[notification.type].bg,
        typeConfig[notification.type].border,
        'fixed bottom-6 right-6 z-50 max-w-md p-4 rounded-lg border shadow-lg',
      ]"
    >
      <div class="flex gap-3 items-start">
        <div class="flex-1 min-w-0">
          <p :class="[typeConfig[notification.type].text, 'font-semibold']">
            {{ notification.message }}
          </p>
          <div
            v-if="notification.details"
            :class="[
              typeConfig[notification.type].text,
              'text-sm mt-2 opacity-90 whitespace-pre-wrap break-words',
            ]"
          >
            {{ notification.details }}
          </div>
        </div>
        <button
          @click="close"
          :class="[
            typeConfig[notification.type].text,
            'flex-shrink-0 hover:opacity-70 transition-opacity ml-2 text-lg leading-none',
          ]"
        >
          ✕
        </button>
      </div>
    </div>
  </transition>
</template>
