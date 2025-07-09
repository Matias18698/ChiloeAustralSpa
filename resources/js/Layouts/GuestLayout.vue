<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue'
import { Link } from '@inertiajs/vue3'
</script>

<template>
  <div class="min-h-screen flex flex-col items-center justify-center bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 px-4 sm:px-6 lg:px-8">
    <div>
      <Link href="/">
        <ApplicationLogo class="h-auto w-80 text-gray-500" />
      </Link>
    </div>

    <div class="mt-4 w-full max-w-md bg-blue-100 dark:bg-gray-800 shadow-md rounded-lg px-6 py-4 overflow-hidden">
      <slot />
    </div>
  </div>
</template>

<style scoped>
/* Solo si necesitas estilos adicionales personalizados */
</style>
