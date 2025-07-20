<script setup>
import { ref, computed, nextTick } from 'vue';
import html2pdf from 'html2pdf.js';
import { defineProps } from 'vue';

// Props
const props = defineProps({
  trabajadores: Array,
  embarcaciones: Array,
  mes: Number,
  año: Number,
});

// Estado del buscador
const searchQuery = ref('');

// Utilidad: rellenar con ceros
const pad = (n) => n.toString().padStart(2, '0');

// Días del mes actual
const diasDelMes = computed(() => {
  const fecha = new Date(props.año, props.mes, 0);
  return Array.from({ length: fecha.getDate() }, (_, i) => i + 1);
});

// Nombre del mes en texto
const mesNombre = computed(() =>
  new Date(props.año, props.mes - 1).toLocaleString('default', {
    month: 'long',
  }).toUpperCase()
);

// Estilos por tipo de asistencia
const getEstiloAsistencia = (tipo) => {
  const estilos = {
    D: 'bg-green-100 text-green-800 border-green-300',
    TR: 'bg-blue-100 text-blue-800 border-blue-300',
    L: 'bg-yellow-100 text-yellow-800 border-yellow-300',
    F: 'bg-red-100 text-red-800 border-red-300',
  };
  return estilos[tipo] || 'bg-gray-100 text-gray-400 border-gray-300';
};

// Asistencia de trabajador por día
const obtenerAsistencia = (trabajador, dia) => {
  const fecha = `${props.año}-${pad(props.mes)}-${pad(dia)}`;
  return trabajador.asistencias?.[fecha] || null;
};

// Contar días de trabajo real (TR)
const contarDiasTR = (asistencias) =>
  diasDelMes.value.reduce((total, dia) => {
    const fecha = `${props.año}-${pad(props.mes)}-${pad(dia)}`;
    return asistencias?.[fecha] === 'TR' ? total + 1 : total;
  }, 0);

// Exportación PDF
const exportandoPDF = ref(false);

const exportarCalendarioPDF = async () => {
  exportandoPDF.value = true;
  await nextTick();

  const element = document.getElementById('calendario-pdf');

  const options = {
    margin: -0.1,
    filename: `calendario-${new Date().toISOString().slice(0, 10)}.pdf`,
    image: { type: 'jpeg', quality: 0.8 },
    html2canvas: { scale: 2, useCORS: true, backgroundColor: '#fff' },
    jsPDF: {
      unit: 'in',
      format: [8.5, 16],
      orientation: 'landscape',
    },
    pagebreak: { mode: ['avoid-all', 'css', 'legacy'] },
  };

  await html2pdf().set(options).from(element).save();
  exportandoPDF.value = false;
};

// Paginación
const currentPage = ref(1);
const itemsPerPage = 10; // Cambia este valor según tus necesidades

const totalPages = computed(() =>
  Math.ceil(props.trabajadores.length / itemsPerPage)
);

const trabajadoresPaginados = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  return props.trabajadores.slice(start, start + itemsPerPage);
});

// Mostrar todos para PDF o solo paginados
const trabajadoresVisibles = computed(() =>
  exportandoPDF.value ? props.trabajadores : trabajadoresPaginados.value
);

// Filtrar trabajadores por nombre o embarcación
const trabajadoresFiltrados = computed(() => {
  if (!searchQuery.value) return props.trabajadores;

  const queryLowerCase = searchQuery.value.toLowerCase();
  return props.trabajadores.filter(trabajador => {
    const nombreCompleto = `${trabajador.nombre} ${trabajador.apellido || ''}`.toLowerCase();
    const embarcacion = obtenerNombreEmbarcacion(trabajador).toLowerCase();
    return nombreCompleto.includes(queryLowerCase) || embarcacion.includes(queryLowerCase);
  });
});

// Navegar entre páginas
const irPagina = (pagina) => {
  if (pagina >= 1 && pagina <= totalPages.value) {
    currentPage.value = pagina;
  }
};
const obtenerNombreEmbarcacion = (trabajador) => {
  if (!trabajador.embarcacion_id) return 'Sin asignar';
  const embarcacion = props.embarcaciones.find(e => e.id === trabajador.embarcacion_id);
  return embarcacion?.nombre || 'Sin asignar';
};
</script>

<template>
  <div class="p-4 space-y-6 overflow-auto">
    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4" v-if="!exportandoPDF">
      <h2 class="text-2xl font-bold text-blue-800">
        📅 Calendario de Asistencia - {{ mesNombre }} {{ año }}
      </h2>
      <button
        @click="exportarCalendarioPDF"
        class="bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded-lg shadow transition-all"
      >
        📄 Exportar PDF
      </button>
    </div>
    <!-- Buscador -->
<div class="flex justify-between items-center mb-4 w-80">
  <input
    v-model="searchQuery"
    type="text"
    placeholder="Buscar por nombre o embarcación..."
    class="w-full px-4 py-2 border border-gray-300 rounded-md text-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
  />
</div>


    <!-- Tabla calendario -->
    <div id="calendario-pdf" class="overflow-x-auto rounded-xl shadow ring-1 ring-blue-200 bg-white p-4">
      <table class="min-w-full border-collapse text-sm text-gray-800">
        <thead class="bg-blue-50 border-b border-blue-200 text-xs uppercase sticky top-0 z-10">
          <tr>
            <th class="px-4 py-3 text-left">Trabajador</th>
            <th class="px-4 py-3 text-left">Cargo</th>
            <th class="px-4 py-3 text-left">Embarcación</th>
            <th v-for="dia in diasDelMes" :key="dia" class="px-2 py-3 text-center text-gray-600">
              {{ dia }}
            </th>
            <th class="px-4 py-3 text-center text-blue-700">Total TR</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="trabajador in trabajadoresFiltrados" :key="trabajador.id" class="hover:bg-blue-50 transition-colors">
            <td class="px-4 py-2 text-gray-600 font-semibold whitespace-nowrap">
              {{ trabajador.nombre }} {{ trabajador.apellido || '' }}
            </td>
            <td class="px-4 py-2 text-gray-600 whitespace-nowrap">
              {{ trabajador.cargo || 'Sin asignar' }}
            </td>
            <td class="px-4 py-2 text-gray-600 whitespace-nowrap">
              {{ obtenerNombreEmbarcacion(trabajador) }}
            </td>
            <td v-for="dia in diasDelMes" :key="dia" class="px-1 py-1 text-center">
              <span
                class="inline-flex items-center justify-center w-6 h-6 rounded-full text-[11px] font-bold border"
                :class="getEstiloAsistencia(obtenerAsistencia(trabajador, dia))"
                :title="obtenerAsistencia(trabajador, dia) || 'Sin registro'"
              >
                {{ obtenerAsistencia(trabajador, dia) || '-' }}
              </span>
            </td>
            <td class="px-4 py-2 text-center font-bold text-blue-700">
              {{ contarDiasTR(trabajador.asistencias) }}
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Leyenda -->
      <div class="flex flex-wrap gap-6 mt-6 text-sm font-medium text-gray-700">
        <div class="flex items-center gap-2">
          <span class="w-4 h-4 rounded-full bg-green-100 border border-green-400"></span>
          Descanso (D)
        </div>
        <div class="flex items-center gap-2">
          <span class="w-4 h-4 rounded-full bg-blue-100 border border-blue-400"></span>
          Trabajo (TR)
        </div>
        <div class="flex items-center gap-2">
          <span class="w-4 h-4 rounded-full bg-yellow-100 border border-yellow-400"></span>
          Licencia (L)
        </div>
        <div class="flex items-center gap-2">
          <span class="w-4 h-4 rounded-full bg-red-100 border border-red-400"></span>
          Falta (F)
        </div>
      </div>
    </div>

    <!-- Paginación -->
    <div class="mt-8 flex justify-center items-center space-x-1 text-sm sm:text-base font-medium">
      <button
        @click="irPagina(currentPage - 1)"
        :disabled="currentPage === 1"
        class="flex items-center px-3 py-1.5 rounded-md border border-blue-500 text-blue-500 hover:bg-blue-100 transition disabled:opacity-50 disabled:cursor-not-allowed"
      >
        <span class="mr-1">⬅️</span> Anterior
      </button>

      <template v-for="page in totalPages" :key="page">
        <button
          @click="irPagina(page)"
          :class="[
            'px-3 py-1.5 rounded-md transition min-w-[2.5rem]',
            page === currentPage
              ? 'bg-blue-600 text-white shadow-md'
              : 'border border-blue-300 text-blue-600 hover:bg-blue-100'
          ]"
        >
          {{ page }}
        </button>
      </template>
      <!-- Botón Siguiente -->
      <button
        @click="irPagina(currentPage + 1)"
        :disabled="currentPage === totalPages"
        class="flex items-center px-3 py-1.5 rounded-md border border-blue-500 text-blue-500 hover:bg-blue-100 transition disabled:opacity-50 disabled:cursor-not-allowed"
      >
        Siguiente <span class="ml-1">➡️</span>
      </button>
    </div>
  </div>
</template>
