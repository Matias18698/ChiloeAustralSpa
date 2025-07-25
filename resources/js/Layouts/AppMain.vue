<script setup>
    import { ref } from 'vue';
    import { Head, Link, router } from '@inertiajs/vue3';
    import ApplicationMark from '@/Components/ApplicationMark.vue';
    import Banner from '@/Components/Banner.vue';
    import Dropdown from '@/Components/Dropdown.vue';
    import AsideDropdown from '@/Components/AsideDropdown.vue';
    import AsideDropdownLink from '@/Components/AsideDropdownLink.vue';
    import DropdownLink from '@/Components/DropdownLink.vue';
    import AsideLink from '@/Components/AsideLink.vue';
        
    import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
    import { faHouse,faShip,faCalendarCheck,faUsers,faBook,faMoneyBillWave,faUserCog } from '@fortawesome/free-solid-svg-icons'

    defineProps({
        auth: Object,
        title: String,
    });

    const showingNavigationDropdown = ref(false);

    const logout = () => {
        router.post(route('logout'));
    };
    
    const navigationItems = [
        { label: 'Inicio', route: 'dashboard', icon: faHouse },
        { label: 'Embarcaciones', route: 'embarcacion.index', icon: faShip },
        { label: 'Trabajadores', route: 'trabajador.index', icon: faUsers },
        { label: 'Asistencias', route: 'asistencia.index', icon: faCalendarCheck },
        { label: 'Bitácoras', route: 'bitacora.index', icon: faBook },
        { label: 'Gastos', route: 'gastos.index', icon: faMoneyBillWave },
        { label: 'Usuarios', route: 'usuarios.index', icon: faUserCog },
    ];
</script>

<template>
    <Head :title="title" />
    
    <nav class="fixed z-50 min-w-full bg-blue-900 border-black border-b-1 shadow-md">
        
        <!-- Primary Navigation Menu -->
        <div class="flex justify-between h-12">
            <div class="shrink-0 flex items-center w-52">

                    <ApplicationMark class="block h-8  w-auto  px-0" />

                <span class="self-center text-lg font-semibold whitespace-nowrap text-white px-2 hidden sm:inline">Chiloé Austral</span>
                
                <!-- Hamburger -->
                <div class="flex items-center">
                    <button
                        @click="showingNavigationDropdown = !showingNavigationDropdown"
                        class="inline-flex items-center justify-center rounded-md p-2 text-white hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white transition"
                        aria-label="Abrir menú de navegación"
                        :aria-expanded="showingNavigationDropdown.toString()"
                        aria-controls="mobile-menu"
                    >
                        <svg
                        :class="{ 'rotate-180 scale-110': showingNavigationDropdown }"
                        class="h-6 w-6 transition-transform duration-300 ease-in-out"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        >
                        <path v-if="!showingNavigationDropdown" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <div class="ml-2 sm:hidden ">
                        <span class="text-lg font-semibold text-white text-center">Chiloé Austral</span>
                    </div>  
                </div>
            </div>
            <!--Dropdown -->
             <Dropdown align="right" width="48">
                <template #trigger>
                <button
                type="button"
                class="inline-flex items-center rounded-md bg-blue-900 px-4 py-2 text-base font-medium text-white hover:bg-blue-800 focus:outline-none transition-all duration-300"
                >
                {{ $page.props.auth.user.name }}
                <svg class="ms-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                </svg>
                </button>
            </template>

            <template #content>
                <div class="rounded-md shadow-lg bg-blue-900 ring-1 ring-black ring-opacity-5 text-white w-48">
                <DropdownLink
                    :href="route('profile.edit')"
                    class="block px-4 py-2 text-sm hover:bg-blue-800 cursor-pointer"
                >
                    <svg class="inline-block mr-2 w-5 h-5 text-blue-400" viewBox="0 0 20 20"><path d="M5 3a2 2..." /></svg>
                    Perfil
                </DropdownLink>
                <DropdownLink
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="block px-4 py-2 text-sm text-red-500 hover:bg-red-700 hover:text-white cursor-pointer"
                >
                    <svg class="inline-block mr-2 w-5 h-5 text-red-400" viewBox="0 0 20 20"><path d="M3 10a1 1..." /></svg>
                    Cerrar sesión
                </DropdownLink>
                </div>
            </template>
            </Dropdown>


    </div>
  </nav>

    <aside class="fixed top-0 z-40 h-screen bg-white transition-all duration-300 border-r border-gray-200" :class="{'w-52 left-0 sm:w-10 sm:left-0': showingNavigationDropdown, 'w-52 -left-52 sm:w-52 sm:left-0': ! showingNavigationDropdown }">
        <div class="flex flex-col h-full pt-14">
            <!-- Navigation Links -->
            <AsideLink v-for="item in navigationItems" :key="item.route" :href="route(item.route)" :active="route().current(item.route)">
                <FontAwesomeIcon :icon="item.icon" size="lg" class="min-w-6 pr-2" />
                {{ item.label }}
            </AsideLink>
        </div>
    </aside>
    
    <!-- Page Content -->
    <main class="pt-12" :class="{'ms-0 sm:ms-10': showingNavigationDropdown, 'ms-0 sm:ms-52': !showingNavigationDropdown }">
        <slot />
    </main>
</template>
<style scoped>
    main {
        transition: margin-left 0.3s ease;
    }
</style>
