<template>
  <div class="fixed bottom-6 right-6 flex flex-col gap-3 z-50">
    <transition-group name="toast" tag="div">
      <div
        v-for="toast in toasts"
        :key="toast.id"
        :class="toastClasses(toast.type)"
        class="max-w-xs rounded shadow-lg p-4 text-white cursor-pointer select-none"
        @click="remove(toast.id)"
        role="alert"
        aria-live="assertive"
      >
        {{ toast.message }}
      </div>
    </transition-group>
  </div>
</template>

<script setup>
import { inject } from 'vue';

const toastStore = inject('toastStore');

const toasts = toastStore.toasts;
const remove = toastStore.remove;

const toastClasses = (type) => {
  switch(type) {
    case 'success': return 'bg-green-500 hover:bg-green-600';
    case 'error': return 'bg-red-500 hover:bg-red-600';
    case 'warning': return 'bg-yellow-500 hover:bg-yellow-600 text-black';
    case 'info': return 'bg-blue-500 hover:bg-blue-600';
    default: return 'bg-gray-700 hover:bg-gray-800';
  }
}
</script>

<style>
.toast-enter-active,
.toast-leave-active {
  transition: all 0.3s ease;
}
.toast-enter-from {
  opacity: 0;
  transform: translateY(10px);
}
.toast-leave-to {
  opacity: 0;
  transform: translateY(10px);
}
</style>
