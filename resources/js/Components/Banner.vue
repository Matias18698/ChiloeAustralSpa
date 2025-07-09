<template>
  <div v-if="message" class="bg-indigo-600 text-white text-sm py-2 px-4 text-center relative z-50">
    {{ message }}
    <button
      @click="dismiss"
      class="absolute right-4 top-1/2 transform -translate-y-1/2 text-white hover:text-gray-200"
      aria-label="Cerrar"
    >
      <span class="material-icons text-sm">close</span>
    </button>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const message = ref('Este es un banner de ejemplo. Puedes personalizar este mensaje.');

function dismiss() {
  message.value = null;
}
</script>

<style scoped>
.material-icons {
  font-size: 16px;
}
</style>
