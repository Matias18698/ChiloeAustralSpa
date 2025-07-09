<script setup>
defineProps({
    value: {
        type: String,
    },
});
</script>

<template>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 label-responsive">
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
    </label>
</template>

<style scoped>
.label-responsive {
    width: 100%;  /* Asegura que el label ocupe el 100% del ancho disponible */
    max-width: 600px; /* Puedes ajustar esto según lo que necesites */
    display: block;
    margin: 0 auto;  /* Centra el label en su contenedor */
    padding: 0.5rem;  /* Añade algo de espacio alrededor del texto */
    box-sizing: border-box;  /* Asegura que el padding no altere el tamaño total */
    font-size: 1rem; /* Asegura que el texto sea adecuado en pantallas pequeñas */
    text-align: left; /* Para que el texto esté alineado a la izquierda */
}

/* Estilo responsivo para pantallas pequeñas */
@media (max-width: 600px) {
    .label-responsive {
        font-size: 0.875rem; /* Reducir el tamaño de la fuente en pantallas pequeñas */
        padding: 0.25rem; /* Reducir el padding en pantallas pequeñas */
    }
}
</style>
