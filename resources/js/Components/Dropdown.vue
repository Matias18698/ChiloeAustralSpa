<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';

const props = defineProps({
    align: {
        type: String,
        default: 'right',
    },
    width: {
        type: String,
        default: '48',
    },
    contentClasses: {
        type: String,
        default: 'py-1 bg-white',
    },
});

const open = ref(false);

const closeOnEscape = (e) => {
    if (open.value && e.key === 'Escape') {
        open.value = false;
    }
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));
onUnmounted(() => document.removeEventListener('keydown', closeOnEscape));

const widthClass = computed(() => {
    const widthMap = {
        '48': 'w-48',
        '64': 'w-64',
        '80': 'w-80',
        'full': 'w-full',
    };
    return widthMap[props.width] || 'w-48';
});

const alignmentClasses = computed(() => {
    if (props.align === 'left') {
        return 'ltr:origin-top-left rtl:origin-top-right start-0';
    } else if (props.align === 'right') {
        return 'ltr:origin-top-right rtl:origin-top-left end-0';
    } else {
        return 'origin-top';
    }
});
</script>

<template>
    <div class="relative">
        <!-- Botón disparador -->
        <div @click="open = !open">
            <slot name="trigger" />
        </div>

        <!-- Fondo oscuro al abrir -->
        <div
            v-show="open"
            class="fixed inset-0 z-40 bg-black bg-opacity-30"
            @click="open = false"
        ></div>

        <!-- Contenido del dropdown -->
        <Transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
            <div
                v-show="open"
                class="dropdown-content absolute z-50 mt-2 rounded-md shadow-lg"
                :class="[widthClass, alignmentClasses]"
                style="display: none"
                @click="open = false"
            >
                <div
                    class="rounded-md ring-1 ring-black ring-opacity-5"
                    :class="contentClasses"
                >
                    <slot name="content" />
                </div>
            </div>
        </Transition>
    </div>
</template>

<style scoped>
/* ✅ SOLO para el dropdown en móviles */
@media (max-width: 640px) {
    .dropdown-content {
        left: auto !important;
        right: 1rem !important;
        width: auto !important;
        max-width: 12rem; /* Mantiene el tamaño equivalente a w-48 */
    }
}

</style>
