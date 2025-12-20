<script setup lang="ts">
import { defineProps, defineEmits } from 'vue';

defineProps<{
    isOpen: boolean;
    title: string;
}>();

const emit = defineEmits(['close']);
</script>

<template>
    <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="emit('close')"></div>

        <!-- content -->
        <div class="relative w-full max-w-lg bg-white dark:bg-slate-800 rounded-xl shadow-2xl transform transition-all p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-slate-800 dark:text-white">{{ title }}</h2>
                <button @click="emit('close')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            
            <slot></slot>
        </div>
    </div>
</template>
