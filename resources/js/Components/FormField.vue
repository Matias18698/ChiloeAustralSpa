<template>
    <div>
      <label :for="id" class="block text-sm font-medium text-gray-700">{{ label }}</label>
      <slot />
      <p v-if="error" class="error">{{ error }}</p>
    </div>
  </template>
  
  <script setup>
  defineProps({
    id: String,
    label: String,
    error: String
  })
  </script>
  
  <style scoped>
  .error {
    @apply text-red-500 text-sm mt-1;
  }
  </style>
  