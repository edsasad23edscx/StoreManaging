<script setup lang="ts">
import { computed } from 'vue'

const props = withDefaults(
  defineProps<{
    modelValue: string | number
    label?: string
    type?: string
    placeholder?: string
    id: string
    step?: string | number
    error?: string
    maxlength?: string | number
    minlength?: string | number
    min?: string | number
    max?: string | number
    pattern?: string
  }>(),
  {
    step: 'any',
  },
)

const emit = defineEmits(['update:modelValue', 'blur'])

const value = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val),
})

const inputClasses = computed(() => {
  const base =
    'px-4 py-2.5 rounded-lg border bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 transition-all outline-none placeholder:text-slate-400'
  if (props.error) {
    return `${base} border-red-400 dark:border-red-500 focus:ring-red-500/20 focus:border-red-500`
  }
  return `${base} border-slate-200 dark:border-slate-700 focus:ring-indigo-500/20 focus:border-indigo-500`
})
</script>

<template>
  <div class="flex flex-col gap-1.5">
    <label v-if="label" :for="id" class="text-sm font-medium text-slate-700 dark:text-slate-300">
      {{ label }}
    </label>
    <input
      :id="id"
      :type="type || 'text'"
      v-model="value"
      :placeholder="placeholder"
      :step="step"
      :maxlength="maxlength"
      :minlength="minlength"
      :min="min"
      :max="max"
      :pattern="pattern"
      :class="inputClasses"
      @blur="emit('blur')"
    />
    <p v-if="error" class="text-xs text-red-500 dark:text-red-400">{{ error }}</p>
  </div>
</template>
