<script setup>
import AppMain from '@/Layouts/AppMain.vue';
import { Head, usePage, router } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

const props = usePage().props;
const rawBitacoras = ref(props.bitacoras.data || []);

const bitacoras = computed(() =>
  rawBitacoras.value.map(b => ({
    id: b.id,
    fecha: b.fecha,
    numero_boleta: b.numero_boleta,
    zona: b.zona,
    centro: b.centro,
    tipo_barco: b.embarcacion?.tipo ?? 'No asignado',
    nombre_embarcacion: b.embarcacion?.nombre ?? 'No asignado',
    encargado_faena: (b.trabajador?.nombre ?? 'No asignado') + ' ' + (b.trabajador?.apellido ?? ''),
    hora_inicial: b.hora_inicial,
    hora_final: b.hora_final,
    observaciones: b.observaciones && b.observaciones.trim() !== ''
      ? 'Con observaciones.'
      : 'Sin observaciones.',
  }))
);

const searchQuery = ref({
  centro: '',
  embarcacion: '',
  numero_boleta: '',
  fechaDesde: '',
  fechaHasta: '',
});

const filteredBitacoras = computed(() => {
  return bitacoras.value.filter(b => {
    const centro = (b.centro || '').toString().toLowerCase();
    const filtroCentro = (searchQuery.value.centro || '').toString().toLowerCase();
    const matchCentro = filtroCentro === '' || centro.includes(filtroCentro);

    const embarcacion = (b.nombre_embarcacion || '').toString().toLowerCase();
    const filtroEmbarcacion = (searchQuery.value.embarcacion || '').toString().toLowerCase();
    const matchEmbarcacion = filtroEmbarcacion === '' || embarcacion.includes(filtroEmbarcacion);

    const numeroBoleta = (b.numero_boleta || '').toString().toLowerCase();
    const filtroNumeroBoleta = (searchQuery.value.numero_boleta || '').toString().toLowerCase();
    const matchNumeroBoleta = filtroNumeroBoleta === '' || numeroBoleta.includes(filtroNumeroBoleta);

    const fechaBitacora = b.fecha ? b.fecha.split('T')[0] : '';
    const fechaDesde = searchQuery.value.fechaDesde;
    const fechaHasta = searchQuery.value.fechaHasta;

    const matchFechaDesde = fechaDesde === '' || fechaBitacora >= fechaDesde;
    const matchFechaHasta = fechaHasta === '' || fechaBitacora <= fechaHasta;

    return matchCentro && matchEmbarcacion && matchNumeroBoleta && matchFechaDesde && matchFechaHasta;
  });
});

// Paginación
const currentPage = ref(1);
const perPage = 10;
const totalPages = computed(() => Math.ceil(filteredBitacoras.value.length / perPage));

const paginatedBitacoras = computed(() => {
  const start = (currentPage.value - 1) * perPage;
  return filteredBitacoras.value.slice(start, start + perPage);
});

const goToPage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page;
  }
};

// Resetear página cuando cambian filtros para no quedar en página inválida
watch(searchQuery, () => {
  currentPage.value = 1;
}, { deep: true });

const handleDelete = (id) => {
  if (confirm('¿Estás seguro de que quieres eliminar esta bitácora?')) {
    router.delete(route('bitacora.destroy', id), {
      preserveScroll: true,
      onSuccess: () => {
        rawBitacoras.value = rawBitacoras.value.filter(b => b.id !== id);
      },
      onError: (error) => {
        alert('Error al eliminar: ' + (error.message || 'Intenta de nuevo.'));
      }
    });
  }
};

const redirectToCreate = () => {
  router.visit(route('bitacora.create'));
};

const formatDate = (fecha) => {
  if (!fecha) return '';
  const [year, month, day] = fecha.split('T')[0].split('-');
  return `${day}/${month}/${year}`;
};

// Limpiar filtros
const clearFilters = () => {
  searchQuery.value = {
    centro: '',
    embarcacion: '',
    numero_boleta: '',
    fechaDesde: '',
    fechaHasta: '',
  };
};
</script>

<template>
  <Head title="Bitácoras" />
  <AppMain>
    <div class="w-full mx-auto px-6 py-3">

      <!-- Título y botón -->
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <h2 class="text-2xl sm:text-3xl font-bold text-blue-800">Lista de Bitácoras</h2>
        <button
          @click="redirectToCreate"
          class="inline-flex items-center gap-2 bg-blue-800 text-white px-4 py-2 rounded-lg text-sm sm:text-base font-semibold hover:bg-blue-900"
        >
          ➕ <span>Crear bitácora</span>
        </button>
      </div>

      <!-- Filtros -->
      <div class="bg-white p-6 rounded-lg shadow-md grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">

        <div class="flex flex-col flex-1 min-w-0">
          <label class="mb-1 font-semibold text-gray-700">Centro</label>
          <input v-model="searchQuery.centro" type="text" placeholder="Buscar por Centro"
            class="border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-400 w-full" />
        </div>

        <div class="flex flex-col flex-1 min-w-0">
          <label class="mb-1 font-semibold text-gray-700">Embarcación</label>
          <input v-model="searchQuery.embarcacion" type="text" placeholder="Buscar por Embarcación"
            class="border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-400 w-full" />
        </div>

        <div class="flex flex-col flex-1 min-w-0">
          <label class="mb-1 font-semibold text-gray-700">Nº Boleta</label>
          <input v-model="searchQuery.numero_boleta" type="text" placeholder="Buscar Nº Boleta"
            class="border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-400 w-full" />
        </div>

        <div class="flex flex-col min-w-0">
          <label class="mb-1 font-semibold text-gray-700">Fecha Desde</label>
          <input v-model="searchQuery.fechaDesde" type="date"
            class="border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-400 w-full" />
        </div>

        <div class="flex flex-col min-w-0">
          <label class="mb-1 font-semibold text-gray-700">Fecha Hasta</label>
          <input v-model="searchQuery.fechaHasta" type="date"
            class="border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-400 w-full" />
        </div>

        <div class="sm:col-span-2 lg:col-span-1 flex items-end justify-end">
          <button @click="clearFilters"
            class="w-full bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold px-4 py-2 rounded-lg shadow-sm transition">
            🧹 Limpiar filtros
          </button>
        </div>

      </div>

      <!-- Tabla Desktop -->
      <div class="mt-8 overflow-auto bg-white rounded-lg shadow-lg border border-gray-200 w-full hidden sm:block">
        <table class="min-w-full divide-y divide-gray-200 text-left text-gray-700 text-sm">
          <thead class="bg-blue-100 text-blue-900">
            <tr>
              <th class="px-5 py-3 font-semibold">Fecha</th>
              <th class="px-5 py-3 font-semibold">Nº Boleta</th>
              <th class="px-5 py-3 font-semibold">Zona</th>
              <th class="px-5 py-3 font-semibold">Centro</th>
              <th class="px-5 py-3 font-semibold">Tipo de Barco</th>
              <th class="px-5 py-3 font-semibold">Embarcación</th>
              <th class="px-5 py-3 font-semibold">Encargado</th>
              <th class="px-5 py-3 font-semibold">Hora Inicial</th>
              <th class="px-5 py-3 font-semibold">Hora Final</th>
              <th class="px-5 py-3 font-semibold">Observaciones</th>
              <th class="px-5 py-3 font-semibold text-center">Acciones</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="bitacora in paginatedBitacoras" :key="bitacora.id" class="hover:bg-blue-50 align-top">
              <td class="px-5 py-3 whitespace-nowrap">{{ formatDate(bitacora.fecha) }}</td>
              <td class="px-5 py-3 whitespace-nowrap">{{ bitacora.numero_boleta }}</td>
              <td class="px-5 py-3 whitespace-nowrap">{{ bitacora.zona }}</td>
              <td class="px-5 py-3 whitespace-nowrap">{{ bitacora.centro }}</td>
              <td class="px-5 py-3 whitespace-nowrap">{{ bitacora.tipo_barco }}</td>
              <td class="px-5 py-3 whitespace-nowrap">{{ bitacora.nombre_embarcacion }}</td>
              <td class="px-5 py-3 whitespace-nowrap">{{ bitacora.encargado_faena }}</td>
              <td class="px-5 py-3 whitespace-nowrap">{{ bitacora.hora_inicial }}</td>
              <td class="px-5 py-3 whitespace-nowrap">{{ bitacora.hora_final }}</td>
              <td class="px-5 py-3 whitespace-normal break-words">{{ bitacora.observaciones }}</td>
              <td class="px-5 py-3 whitespace-nowrap text-center space-x-1">
                <button @click="router.visit(route('bitacora.edit', bitacora.id))"
                  class="text-indigo-600 hover:text-indigo-900 font-semibold">✏️</button>
                <button @click="router.visit(route('bitacora.documento', bitacora.id))"
                  class="text-green-600 hover:text-green-900 font-semibold">📄</button>
                <button @click="handleDelete(bitacora.id)"
                  class="text-red-600 hover:text-red-900 font-semibold">🗑️</button>
              </td>
            </tr>
            <tr v-if="filteredBitacoras.length === 0">
              <td colspan="11" class="text-center py-6 text-gray-500 italic">No se encontraron bitácoras con esos filtros.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Tarjetas Mobile -->
      <div class="mt-6 flex flex-col gap-4 sm:hidden">
        <div v-for="bitacora in paginatedBitacoras" :key="bitacora.id"
          class="bg-white rounded-lg shadow-md p-4 border border-gray-200 w-full break-words">
          <div class="flex justify-between items-center mb-2">
            <h3 class="text-lg font-bold text-blue-800">#{{ bitacora.numero_boleta }}</h3>
            <span class="text-sm text-gray-500">{{ formatDate(bitacora.fecha) }}</span>
          </div>
          <div class="text-sm text-gray-700 space-y-1 mb-3">
            <p><strong>Centro:</strong> {{ bitacora.centro }}</p>
            <p><strong>Zona:</strong> {{ bitacora.zona }}</p>
            <p><strong>Tipo Barco:</strong> {{ bitacora.tipo_barco }}</p>
            <p><strong>Embarcación:</strong> {{ bitacora.nombre_embarcacion }}</p>
            <p><strong>Encargado:</strong> {{ bitacora.encargado_faena }}</p>
            <p><strong>Hora Inicial:</strong> {{ bitacora.hora_inicial }}</p>
            <p><strong>Hora Final:</strong> {{ bitacora.hora_final }}</p>
            <p><strong>Observaciones:</strong> {{ bitacora.observaciones }}</p>
          </div>
          <div class="flex justify-end gap-2">
            <button @click="router.visit(route('bitacora.edit', bitacora.id))" class="text-indigo-600">✏️</button>
            <button @click="router.visit(route('bitacora.documento', bitacora.id))" class="text-green-600">📄</button>
            <button @click="handleDelete(bitacora.id)" class="text-red-600">🗑️</button>
          </div>
        </div>
      </div>

      <!-- Paginación -->
      <div class="mt-8 flex justify-center items-center space-x-1 text-sm sm:text-base font-medium">
        <button @click="goToPage(currentPage - 1)" :disabled="currentPage === 1"
          class="flex items-center px-3 py-1.5 rounded-md border border-blue-500 text-blue-500 hover:bg-blue-100 transition disabled:opacity-50 disabled:cursor-not-allowed">
          ⬅️ Anterior
        </button>
        <template v-for="page in totalPages" :key="page">
          <button @click="goToPage(page)"
            :class="[page === currentPage ? 'bg-blue-600 text-white shadow-md' : 'border border-blue-300 text-blue-600 hover:bg-blue-100']"
            class="px-3 py-1.5 rounded-md min-w-[2.5rem] transition">
            {{ page }}
          </button>
        </template>
        <button @click="goToPage(currentPage + 1)" :disabled="currentPage === totalPages"
          class="flex items-center px-3 py-1.5 rounded-md border border-blue-500 text-blue-500 hover:bg-blue-100 transition disabled:opacity-50 disabled:cursor-not-allowed">
          Siguiente ➡️
        </button>
      </div>

    </div>
  </AppMain>
</template>

<style scoped>
/* Aquí podrías agregar estilos específicos si quieres */
</style>
