<script setup>
import { onMounted, ref } from 'vue';

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <div class="file-input-container">
        <input
            type="file"
            id="avatar"
            name="avatar"
            accept="image/*"
            class="borde"
            ref="input"
        />
    </div>
</template>

<style scoped>
.file-input-container {
    display: flex;
    justify-content: center;
    width: 100%;
}

input[type="file"] {
    width: 100%;        /* Se adapta al 100% del contenedor */
    max-width: 300px;   /* Establece un tamaño máximo si es necesario */
    padding: 10px;      /* Un poco de relleno para que sea más fácil de interactuar */
    box-sizing: border-box; /* Asegura que el padding no rompa el tamaño */
}

.borde {
    border: 1px solid #ccc; /* Añadir un borde suave */
    border-radius: 4px; /* Borde redondeado */
    font-size: 1rem; /* Tamaño de fuente adecuado */
    padding: 0.5rem; /* Añadir algo de espacio interno */
    width: 100%;
}
</style>
