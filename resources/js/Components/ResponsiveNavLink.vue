<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
});

const classes = computed(() =>
    props.active
        ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-black text-start text-base font-medium text-black bg-white focus:outline-none focus:text-black focus:bg-white focus:border-black transition duration-150 ease-in-out sm:px-6 sm:text-sm md:px-8 md:text-base lg:px-10 lg:text-lg'
        : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-black hover:text-gray-800 hover:bg-white hover:border-gray-300 focus:outline-none focus:text-black focus:bg-white focus:border-gray-300 transition duration-150 ease-in-out sm:px-6 sm:text-sm md:px-8 md:text-base lg:px-10 lg:text-lg',
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
