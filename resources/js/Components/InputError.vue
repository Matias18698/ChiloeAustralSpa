<script setup>
defineProps({
    message: {
        type: String,
    },
});
</script>

<template>
    <!-- Se renderiza solo si hay un mensaje -->
    <div v-if="message" role="alert" class="p-2 mt-2 rounded-md bg-red-100 border border-red-600">
        <p class="text-sm text-red-600 dark:text-red-400">
            {{ message }}
        </p>
    </div>
</template>

<style scoped>
/* Responsividad: asegúrate de que se vea bien en móviles */
@media (max-width: 640px) {
    .bg-red-100 {
        background-color: rgba(254, 202, 202, 0.6);
    }
}
</style>
