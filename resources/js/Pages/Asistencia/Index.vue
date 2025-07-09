<script setup>
import { ref, computed, watch } from 'vue';
import { usePage, router } from '@inertiajs/vue3';

import AppMain from '@/Layouts/AppMain.vue';
import CalendarioAsistencia from '@/Components/CalendarioAsistencia.vue';

const { props } = usePage();
const asistenciasData = props.asistencias || [];
const trabajadoresData = props.trabajadores || [];

const selectedMes = ref(props.mes || new Date().getMonth() + 1);
const selectedAño = ref(props.año || new Date().getFullYear());
const loading = ref(false);

const primerAño = ref(2024);

const years = computed(() => {
  const currentYear = new Date().getFullYear() + 1;
  return Array.from({ length: currentYear - primerAño.value + 1 }, (_, i) => primerAño.value + i);
});


const asistencias = computed(() =>
  asistenciasData
    .filter(({ fecha }) => {
      const f = new Date(fecha);
      return f.getMonth() + 1 === selectedMes.value && f.getFullYear() === selectedAño.value;
    })
    .map(({ id, fecha, estado, trabajador, embarcacion }) => ({
      id,
      fecha,
      estado,
      trabajador: trabajador.nombre,
      cargo: trabajador.cargo,
      embarcacion: embarcacion.nombre,
    }))
);

const trabajadoresOrdenados = computed(() =>
  [...trabajadoresData].sort((a, b) => a.nombre.localeCompare(b.nombre))
);

const mesNombre = computed(() =>
  new Date(selectedAño.value, selectedMes.value - 1)
    .toLocaleString('default', { month: 'long' })
    .toUpperCase()
);

watch([selectedMes, selectedAño], async ([newMes, newAño]) => {
  loading.value = true;
  await router.get(route('asistencia.index'), { mes: newMes, año: newAño }, {
    preserveScroll: true,
    onFinish: () => (loading.value = false),
  });
});

const redirectToCreateAsistencia = () => router.visit(route('asistencia.create'));
const redirectToManualAsistencia = () => router.visit(route('asistencia.manual'));
</script>

<template>
  <AppMain>
    <div>
      <div class="px-2 sm:px-4 lg:px-4 space-y-10">

        <!-- Encabezado -->
        <div class="space-y-4">
          <div class="flex items-center gap-3 text-blue-900">
            <h1 class="text-3xl font-extrabold tracking-tight">Asistencia de Trabajadores</h1>
          </div>

          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <p class="text-gray-700 text-lg">
              Calendario correspondiente a
              <span class="text-blue-800 font-semibold">{{ mesNombre }} {{ selectedAño }}</span>
            </p>
            <div class="flex flex-wrap gap-3">
              <button
                @click="redirectToCreateAsistencia"
                class="inline-flex items-center gap-2 bg-blue-700 hover:bg-blue-800 text-white rounded-md px-4 py-2 font-medium shadow-md transition-all"
              >
                ➕ Crear Asistencia
              </button>
              <button
                @click="redirectToManualAsistencia"
                class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white rounded-md px-4 py-2 font-medium shadow-md transition-all"
              >
                📝 Asistencia Manual
              </button>
            </div>
          </div>
        </div>

        <!-- Filtros -->
        <div class="flex flex-wrap justify-center gap-4">
          <select
            v-model="selectedMes"
            class="border border-blue-300 rounded-lg py-2 px-4 text-gray-800 bg-white shadow-sm focus:ring-2 focus:ring-blue-400 focus:outline-none"
          >
            <option disabled value="">Selecciona un mes</option>
            <option v-for="i in 12" :key="i" :value="i">
              {{ new Date(0, i - 1).toLocaleString('default', { month: 'long' }) }}
            </option>
          </select>

          <select
            v-model="selectedAño"
            class="border border-blue-300 rounded-lg py-2 px-4 text-gray-800 bg-white shadow-sm focus:ring-2 focus:ring-blue-400 focus:outline-none"
          >
            <option disabled value="">Selecciona un año</option>
            <option v-for="year in years" :key="year" :value="year">{{ year }}</option>
          </select>
        </div>

        <!-- Cargando... -->
        <div v-if="loading" class="flex justify-center py-8">
          <svg class="animate-spin h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z" />
          </svg>
        </div>

        <!-- Calendario -->
        <transition name="fade" mode="out-in">
          <div
            v-if="!loading"
            :key="selectedMes + '-' + selectedAño"
            class="bg-white rounded-xl shadow-lg p-2 ring-1 ring-blue-100 transition-all overflow-x-auto"
          >
            <CalendarioAsistencia
              :trabajadores="trabajadoresOrdenados"
              :mes="selectedMes"
              :año="selectedAño"
              :asistencias="asistencias"
            />
          </div>
        </transition>
      </div>
      
    </div>
  </AppMain>
</template>
<style scoped>
/* Scroll suave para selectores largos */
select {
  scroll-behavior: smooth;
}

/* Mejora de la transición fade (si está implementada en Tailwind o tu config) */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
