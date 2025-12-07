<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
    modelValue: string | number;
    label?: string;
    type?: string;
    placeholder?: string;
    id: string;
}>();

const emit = defineEmits(['update:modelValue']);

const value = computed({
    get: () => props.modelValue,
    set: (val) => emit('update:modelValue', val)
});
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
            class="px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none placeholder:text-slate-400"
        />
    </div>
</template>
