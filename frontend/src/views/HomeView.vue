<script setup lang="ts">
import TheWelcome from '../components/TheWelcome.vue'
import ThemeToggle from '@/components/ThemeToggle.vue'
import { useTheme } from '@/composables/useTheme'

// Initialize theme
useTheme()
</script>

<template>
  <main class="min-h-screen bg-slate-50 dark:bg-slate-900 transition-colors p-8">
    <div class="absolute top-4 right-4">
      <ThemeToggle />
    </div>
    <TheWelcome />
  </main>
</template>
