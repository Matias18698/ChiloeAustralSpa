<script setup>
import AppMain from '@/Layouts/AppMain.vue';
import { Head, usePage, router } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import MapView from '@/Components/MapView.vue';

// Props desde el backend
const { props } = usePage();
const embarcacionesRaw = props.embarcaciones || [];

const embarcaciones = computed(() =>
  embarcacionesRaw.map(({ id, nombre, tipo, patente, capacidad, estado, latitud, longitud, lon, lat }) => ({
    id, nombre, tipo, patente, capacidad, estado, latitud, longitud, lon, lat
  }))
);

// Paginación
const currentPage = ref(1);
const perPage = 5;
const totalPages = computed(() => Math.ceil(embarcaciones.value.length / perPage));
const embarcacionesPaginadas = computed(() => {
  const start = (currentPage.value - 1) * perPage;
  return embarcaciones.value.slice(start, start + perPage);
});
const goToPage = (page) => {
  if (page >= 1 && page <= totalPages.value) currentPage.value = page;
};

watch(embarcaciones, () => {
  currentPage.value = 1;
});


// Navegación
const redirectToCreateEmbarcacion = () => router.visit(route('embarcacion.create'));
const editarEmbarcacion = (id) => router.visit(route('embarcacion.edit', id));

const handleDeleteEmbarcacion = (id) => {
  if (confirm('¿Estás seguro de que quieres eliminar esta embarcación?')) {
    router.delete(route('embarcacion.destroy', id), {
      preserveScroll: true,
      onSuccess: () => router.reload({ preserveScroll: true }),
      onError: (error) => {
        alert('Error al eliminar: ' + (error.message || 'Intenta de nuevo.'));
      }
    });
  }
};
// GPS
const isValidCoordinate = (value) =>
  value !== null && value !== undefined && !isNaN(Number(value));


</script>
<template>
  <Head title="Embarcaciones" />

  <AppMain>
    <div class="max-w-screen-xl mx-auto px-4 py-6">
      <!-- Título y botón -->
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <h2 class="text-2xl sm:text-3xl font-bold text-blue-800">Lista de Embarcaciones</h2>
        <button
          @click="redirectToCreateEmbarcacion"
          class="inline-flex items-center gap-2 bg-blue-800 text-white px-4 py-2 rounded-lg text-sm sm:text-base font-semibold hover:bg-blue-900"
        >
          ➕ <span>Crear embarcación</span>
        </button>
      </div>

      <!-- Resultado ubicación -->
      <div>
        <p v-if="latitud && longitud" class="text-green-700 text-sm">
          Latitud: {{ latitud.toFixed(6) }}<br />
          Longitud: {{ longitud.toFixed(6) }}
        </p>
        <p v-else-if="errorGPS" class="text-red-600 text-sm">{{ errorGPS }}</p>
      </div>

      <!-- Vista móvil -->
      <div class="md:hidden space-y-4 mt-6">
        <div
          v-for="embarcacion in embarcacionesPaginadas"
          :key="embarcacion.id"
          class="bg-white shadow rounded-lg p-4 border border-gray-200"
        >
          <h3 class="font-bold text-blue-800 text-lg">{{ embarcacion.nombre }}</h3>
          <p><strong>Tipo:</strong> {{ embarcacion.tipo }}</p>
          <p><strong>Patente:</strong> {{ embarcacion.patente }}</p>
          <p><strong>Capacidad:</strong> {{ embarcacion.capacidad }}</p>
          <p><strong>Estado:</strong> {{ embarcacion.estado }}</p>

            <div
            class="mt-3 h-40 rounded overflow-hidden z-0"
            :style="{ position: 'relative' }"
            >
            
            <MapView
             v-if="isValidCoordinate(embarcacion.lat) && isValidCoordinate(embarcacion.lon)"

              :lat="Number(embarcacion.lat)"
              :lon="Number(embarcacion.lon)"
              :mapId="`map-mobile-${embarcacion.id}`"
              :zoom="14"
              markerPopupText="Embarcación"
              class="w-full h-full"
              style="z-index: 0; position: relative;"
            />
            <p v-else class="flex items-center justify-center h-full text-center text-red-600 text-sm">Ubicación no disponible</p>
            </div>

          <div class="mt-3 flex justify-end space-x-2">
            <button @click="editarEmbarcacion(embarcacion.id)" class="text-blue-600 hover:text-blue-900 text-sm">✏️</button>
            <button @click="handleDeleteEmbarcacion(embarcacion.id)" class="text-red-600 hover:text-red-900 text-sm">🗑️</button>
          </div>
        </div>
      </div>

<!-- Vista escritorio -->
<div class="hidden md:block overflow-x-auto bg-white shadow rounded-md mt-6">
  <table class="min-w-[800px] w-full text-sm text-gray-700 border border-gray-200">
    <thead class="bg-blue-50 text-blue-800">
      <tr>
        <th class="px-3 py-2 text-left">Nombre</th>
        <th class="px-3 py-2 text-left">Tipo</th>
        <th class="px-3 py-2 text-left">Patente</th>
        <th class="px-3 py-2 text-left">Capacidad</th>
        <th class="px-3 py-2 text-left">Estado</th>
        <th class="px-3 py-2 text-center">Ubicación</th>
        <th class="px-3 py-2 text-center">Acciones</th>
      </tr>
    </thead>
    <tbody>
      <tr
        v-for="embarcacion in embarcacionesPaginadas"
        :key="embarcacion.id"
        class="hover:bg-blue-50 transition border-b border-gray-200"
      >
        <td class="px-3 py-2">{{ embarcacion.nombre }}</td>
        <td class="px-3 py-2">{{ embarcacion.tipo }}</td>
        <td class="px-3 py-2">{{ embarcacion.patente }}</td>
        <td class="px-3 py-2">{{ embarcacion.capacidad }}</td>
        <td class="px-3 py-2">{{ embarcacion.estado }}</td>
        <td class="px-3 py-2 text-center">
          <div
            v-if="embarcacion.lat && embarcacion.lon"
            class="rounded overflow-hidden"
            style="height: 160px; min-width: 200px;"
          >
            <MapView
             v-if="isValidCoordinate(embarcacion.lat) && isValidCoordinate(embarcacion.lon)"

              :lat="Number(embarcacion.lat)"
              :lon="Number(embarcacion.lon)"
              :mapId="`map-desktop-${embarcacion.id}`"
              :zoom="14"
              markerPopupText="Embarcación"
              class="w-full h-full"
              style="z-index: 0; position: relative;"
            />
            <p v-else class="flex items-center justify-center h-full text-center text-red-600 text-sm">Ubicación no disponible</p>
          </div>
        </td>
        <td class="px-3 py-2 text-center space-x-2">
          <button @click="editarEmbarcacion(embarcacion.id)" class="text-blue-600 hover:text-blue-900">✏️</button>
          <button @click="handleDeleteEmbarcacion(embarcacion.id)" class="text-red-600 hover:text-red-900">🗑️</button>
        </td>
      </tr>
    </tbody>
  </table>
</div>

      <!-- Paginación -->
      <div class="mt-6 flex justify-center gap-2 text-sm flex-wrap" role="navigation" aria-label="Paginación de embarcaciones">
        <button
          @click="goToPage(currentPage - 1)"
          :disabled="currentPage === 1"
          class="px-3 py-2 border rounded text-blue-600 hover:bg-blue-100 disabled:opacity-50"
        >
          ⬅️ Anterior
        </button>
        <button
          v-for="page in totalPages"
          :key="page"
          @click="goToPage(page)"
          :class="[
            'px-3 py-2 rounded',
            page === currentPage ? 'bg-blue-600 text-white' : 'border border-blue-300 text-blue-600 hover:bg-blue-100'
          ]"
        >
          {{ page }}
        </button>
        <button
          @click="goToPage(currentPage + 1)"
          :disabled="currentPage === totalPages"
          class="px-3 py-2 border rounded text-blue-600 hover:bg-blue-100 disabled:opacity-50"
        >
          Siguiente ➡️
        </button>
      </div>
    </div>
  </AppMain>
</template>
