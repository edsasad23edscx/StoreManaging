<script setup lang="ts">
defineProps<{
    variant?: 'primary' | 'secondary' | 'danger';
    type?: 'button' | 'submit';
}>();
</script>

<template>
    <button
        :type="type || 'button'"
        class="px-5 py-2.5 rounded-lg font-medium transition-all transform active:scale-95 flex items-center justify-center gap-2 shadow-sm"
        :class="{
            'bg-indigo-600 hover:bg-indigo-700 text-white shadow-indigo-500/30': variant === 'primary' || !variant,
            'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700': variant === 'secondary',
            'bg-red-500 hover:bg-red-600 text-white shadow-red-500/30': variant === 'danger'
        }"
    >
        <slot />
    </button>
</template>
