<script setup>
import { computed } from 'vue';

const emit = defineEmits(['update:checked']);

const props = defineProps({
    checked: {
        type: [Array, Boolean],
        required: true,
    },
    value: {
        default: null,
    },
});

const proxyChecked = computed({
    get() {
        return props.checked;
    },

    set(val) {
        emit('update:checked', val);
    },
});
</script>

<template>
  <div class="flex items-center space-x-2">
    <input
      type="checkbox"
      :value="value"
      v-model="proxyChecked"
      id="checkbox"
      class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 transition-colors duration-200 ease-in-out"
    />
    <label
      for="checkbox"
      class="text-sm text-gray-700 dark:text-gray-300 cursor-pointer select-none"
    >
      {{ value }}
    </label>
  </div>
</template>

<style scoped>
/* Opcional: agregar ajustes de estilo específicos para hacer el checkbox más visible en pantallas más grandes o pequeñas */
@media (max-width: 640px) {
    .w-5 {
        width: 1.25rem; /* Ajusta el tamaño del checkbox en pantallas pequeñas */
        height: 1.25rem;
    }
}
</style>
