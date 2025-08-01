<script setup>
import { Head, usePage, router } from '@inertiajs/vue3';
import AppMain from '@/Layouts/AppMain.vue';
import { computed, ref, watch } from 'vue';

const props = usePage().props;

const estadoFiltro = ref('pendiente');
const facturaFiltro = ref('');
const fechaInicioFiltro = ref('');
const fechaFinFiltro = ref('');

const errores = ref({
  facturaFiltro: '',
  fechas: '',
  estadoFiltro: ''
});

const paginaActual = ref(1);
const itemsPorPagina = 10;

const rawGastos = ref(Array.isArray(props.gastos) ? props.gastos : props.gastos?.data ?? []);



const gastos = computed(() =>
  rawGastos.value.map(g => ({
    id: g.id,
    cotizacion: g.cotizacion,
    centro_barco: g.centro_barco,
    orden_compra: g.orden_compra,
    fecha_oc: g.fecha_oc,
    hes: g.hes,
    fecha_hes: g.fecha_hes,
    valor_neto: g.valor_neto,
    empresa_facturar: g.empresa_facturar,
    factura: g.factura,
    estado: g.estado,
    valor_facturado: g.valor_facturado,
  }))
);

watch([facturaFiltro, fechaInicioFiltro, fechaFinFiltro, estadoFiltro], () => {
  validarFiltros();
  paginaActual.value = 1; // reset paginación al cambiar filtro
});

const validarFiltros = () => {
  errores.value = {
    facturaFiltro: '',
    fechas: '',
    estadoFiltro: ''
  };

  if (facturaFiltro.value.length > 20) {
    errores.value.facturaFiltro = 'Máximo 20 caracteres.';
  } else if (facturaFiltro.value && !/^[\w\-]*$/.test(facturaFiltro.value)) {
    errores.value.facturaFiltro = 'Solo letras, números y guiones.';
  }

  if (fechaInicioFiltro.value && fechaFinFiltro.value && fechaInicioFiltro.value > fechaFinFiltro.value) {
    errores.value.fechas = 'Fecha inicio no puede ser mayor que fecha fin.';
  }

  if (!['pendiente', 'pagada', 'todas'].includes(estadoFiltro.value)) {
    errores.value.estadoFiltro = 'Estado inválido.';
  }
};

const gastosFiltrados = computed(() => {
  if (Object.values(errores.value).some(msg => msg)) {
    return [];
  }

  return gastos.value.filter(g => {
    if (estadoFiltro.value !== 'todas' && g.estado?.toLowerCase() !== estadoFiltro.value) {
      return false;
    }

    if (facturaFiltro.value && !g.factura?.toLowerCase().includes(facturaFiltro.value.toLowerCase())) {
      return false;
    }

    if (fechaInicioFiltro.value && (!g.fecha_oc || g.fecha_oc < fechaInicioFiltro.value)) {
      return false;
    }

    if (fechaFinFiltro.value && (!g.fecha_oc || g.fecha_oc > fechaFinFiltro.value)) {
      return false;
    }

    return true;
  });
});

const resumenPorEstado = computed(() => {
  const resumen = { pendiente: 0, pagada: 0 };

  gastosFiltrados.value.forEach(g => {
    const estado = g.estado?.toLowerCase();
    if (resumen[estado] !== undefined) {
      resumen[estado] += Number(g.valor_neto || 0);
    }
  });

  return resumen;
});

const chartLabels = computed(() => ['Pendiente', 'Pagada']);
const chartData = computed(() => [
  resumenPorEstado.value.pendiente,
  resumenPorEstado.value.pagada
]);

const totalPaginas = computed(() => Math.ceil(gastosFiltrados.value.length / itemsPorPagina));

const gastosPaginados = computed(() => {
  const inicio = (paginaActual.value - 1) * itemsPorPagina;
  return gastosFiltrados.value.slice(inicio, inicio + itemsPorPagina);
});

const cambiarPagina = (pagina) => {
  if (pagina >= 1 && pagina <= totalPaginas.value) {
    paginaActual.value = pagina;
  }
};

const formatDate = (fecha) => {
  if (!fecha) return '';
  const [year, month, day] = fecha.split('T')[0].split('-');
  return `${day}/${month}/${year}`;
};

const formatNumber = (num) => {
  if (!num) return '0';
  return Number(num).toLocaleString('es-CL', { minimumFractionDigits: 0 });
};

const redirectToCreate = () => {
  router.visit(route('gastos.create'));
};

const editGasto = (id) => {
  router.visit(route('gastos.edit', id));
};

const handleDelete = (id) => {
  if (confirm('¿Estás seguro de eliminar este gasto?')) {
    router.delete(route('gastos.destroy', id), {
      preserveScroll: true,
      onSuccess: () => {
        // Actualiza manualmente la lista local
        const updated = [...rawGastos.value].filter(g => g.id !== id);
        rawGastos.value = updated;
      },
      onError: () => {
        alert('Error al eliminar el gasto.');
      }
    });
  }
};



const estadoClass = (estado) => {
  switch (estado?.toLowerCase()) {
    case 'pagada':
      return 'bg-green-100 text-green-800 border border-green-300';
    case 'pendiente':
      return 'bg-yellow-100 text-yellow-800 border border-yellow-300';
    default:
      return 'bg-gray-100 text-gray-800 border border-gray-300';
  }
};

const estadoIcon = (estado) => {
  switch (estado?.toLowerCase()) {
    case 'pagada':
      return '✅';
    case 'pendiente':
      return '⏳';
    default:
      return 'ℹ️';
  }
};

const goToPage = (pagina) => {
  cambiarPagina(pagina);
};
</script>

<template>
  <Head title="Gastos" />
  <AppMain>
    <main class="container mx-auto px-4 py-6 flex flex-col flex-1">

      <!-- Header con filtros -->
      <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-4 sm:space-y-0 gap-4">
        <h2 class="text-3xl font-bold text-blue-900 flex items-center gap-2 select-none">
           Registro de Gastos
        </h2>
        <form @submit.prevent class="flex flex-wrap gap-4 items-center justify-end w-full sm:w-auto" aria-label="Filtros de gastos">

          <select
            v-model="estadoFiltro"
            aria-label="Filtro por estado"
            class="appearance-none w-full sm:w-40 border border-blue-300 rounded-md bg-white px-4 py-2 pr-10 text-sm shadow-md text-blue-800 font-medium focus:outline-none focus:ring-2 focus:ring-blue-200 transition"
          >
            <option value="pendiente">⏳ Pendientes</option>
            <option value="pagada">✅ Pagadas</option>
            <option value="todas">📋 Todas</option>
          </select>

          <div class="flex flex-col w-full sm:w-48">
            <input
              v-model="facturaFiltro"
              type="text"
              placeholder="Buscar por Nº Factura..."
              aria-describedby="error-factura"
              class="border border-blue-300 rounded-md px-3 py-2 text-sm text-blue-800 font-medium focus:outline-none focus:ring-2 focus:ring-blue-200 shadow-md"
            />
            <p v-if="errores.facturaFiltro" id="error-factura" class="text-red-600 text-xs mt-1 font-semibold">
              {{ errores.facturaFiltro }}
            </p>
          </div>

          <div class="flex flex-wrap gap-2 items-center w-full sm:w-auto">
            <label for="fecha-inicio" class="text-sm text-blue-800 font-semibold select-none">Desde:</label>
            <input
              id="fecha-inicio"
              v-model="fechaInicioFiltro"
              type="date"
              :max="fechaFinFiltro || undefined"
              aria-describedby="error-fechas"
              class="border border-blue-300 rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200"
            />

            <label for="fecha-fin" class="text-sm text-blue-800 font-semibold select-none">Hasta:</label>
            <input
              id="fecha-fin"
              v-model="fechaFinFiltro"
              type="date"
              :min="fechaInicioFiltro || undefined"
              aria-describedby="error-fechas"
              class="border border-blue-300 rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200"
            />
          </div>
          <p v-if="errores.fechas" id="error-fechas" class="text-red-600 text-xs mt-1 w-full sm:w-auto font-semibold">
            {{ errores.fechas }}
          </p>

          <button
            type="button"
            @click="redirectToCreate"
            class="bg-blue-800 text-white rounded-lg px-5 py-2 text-sm font-semibold hover:bg-blue-900 shadow-md transition whitespace-nowrap"
            aria-label="Crear nuevo gasto"
          >
            ➕ Nuevo Gasto
          </button>
        </form>
      </header>

      <!-- Resumen gráfico -->
      <section class="mb-6">
        <GastoChart
          :labels="chartLabels"
          :data="chartData"
          title="Distribución de Gastos por Estado"
          class="max-w-md mx-auto sm:max-w-full"
        />
      </section>

      <!-- Tabla para desktop -->
      <section class="hidden sm:block overflow-x-auto rounded-lg border border-gray-200 shadow-sm bg-white">
        <table class="w-full min-w-[1100px] text-sm text-gray-800">
          <thead class="bg-blue-100 text-blue-900 select-none">
            <tr>
              <th class="px-4 py-3 font-semibold text-left">Cotización</th>
              <th class="px-4 py-3 font-semibold text-left">Centro-Barco</th>
              <th class="px-4 py-3 font-semibold text-left">Orden Compra</th>
              <th class="px-4 py-3 font-semibold text-left">Fecha OC</th>
              <th class="px-4 py-3 font-semibold text-left">HES</th>
              <th class="px-4 py-3 font-semibold text-left">Fecha HES</th>
              <th class="px-4 py-3 font-semibold text-left">Valor Neto</th>
              <th class="px-4 py-3 font-semibold text-left">Empresa</th>
              <th class="px-4 py-3 font-semibold text-left">Nº Factura</th>
              <th class="px-4 py-3 font-semibold text-left">Valor Facturado</th>
              <th class="px-4 py-3 font-semibold text-left">Estado</th>
              <th class="px-4 py-3 font-semibold text-center">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="gasto in gastosPaginados"
              :key="gasto.id"
              class="border-t border-gray-200 hover:bg-blue-50 transition"
            >
              <td class="px-4 py-3">{{ gasto.cotizacion }}</td>
              <td class="px-4 py-3">{{ gasto.centro_barco }}</td>
              <td class="px-4 py-3">{{ gasto.orden_compra }}</td>
              <td class="px-4 py-3">{{ formatDate(gasto.fecha_oc) }}</td>
              <td class="px-4 py-3">{{ gasto.hes }}</td>
              <td class="px-4 py-3">{{ formatDate(gasto.fecha_hes) }}</td>
              <td class="px-4 py-3">${{ formatNumber(gasto.valor_neto) }}</td>
              <td class="px-4 py-3">{{ gasto.empresa_facturar }}</td>
              <td class="px-4 py-3">{{ gasto.factura }}</td>
              <td class="px-4 py-3">${{ formatNumber(gasto.valor_facturado) }}</td>
              <td class="px-4 py-3">
                <span
                  :class="estadoClass(gasto.estado)"
                  class="px-3 py-1 rounded-full text-xs font-semibold inline-flex items-center gap-1 shadow-sm transition"
                >
                  {{ estadoIcon(gasto.estado) }} {{ gasto.estado }}
                </span>
              </td>
              <td class="px-4 py-3 text-center space-x-2 whitespace-nowrap">
                <button
                  @click="editGasto(gasto.id)"
                  class="text-blue-600 hover:text-blue-900 font-semibold px-3 py-1 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400"
                  title="Editar gasto"
                  aria-label="Editar gasto"
                >
                  ✏️
                </button>
                <button
                  @click="handleDelete(gasto.id)"
                  class="text-red-600 hover:text-red-900 font-semibold px-3 py-1 rounded-md focus:outline-none focus:ring-2 focus:ring-red-400"
                  title="Eliminar gasto"
                  aria-label="Eliminar gasto"
                >
                  🗑️
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </section>

      <!-- Vista móvil en cards -->
      <section class="sm:hidden w-full space-y-4 mt-6">
        <article
          v-for="gasto in gastosPaginados"
          :key="gasto.id"
          class="bg-white shadow-md rounded-lg p-4 border border-gray-200"
          role="listitem"
          tabindex="0"
          aria-label="Gasto detalle"
        >
          <h3 class="text-blue-800 font-semibold text-lg mb-2 truncate">{{ gasto.centro_barco }}</h3>
          <dl class="grid grid-cols-2 gap-x-4 gap-y-2 text-sm text-gray-700">
            <dt class="font-semibold">Cotización:</dt><dd>{{ gasto.cotizacion }}</dd>
            <dt class="font-semibold">Orden Compra:</dt><dd>{{ gasto.orden_compra }}</dd>
            <dt class="font-semibold">Fecha OC:</dt><dd>{{ formatDate(gasto.fecha_oc) }}</dd>
            <dt class="font-semibold">HES:</dt><dd>{{ gasto.hes }}</dd>
            <dt class="font-semibold">Fecha HES:</dt><dd>{{ formatDate(gasto.fecha_hes) }}</dd>
            <dt class="font-semibold">Valor Neto:</dt><dd>${{ formatNumber(gasto.valor_neto) }}</dd>
            <dt class="font-semibold">Valor Facturado:</dt><dd>${{ formatNumber(gasto.valor_facturado) }}</dd>
            <dt class="font-semibold">Empresa:</dt><dd>{{ gasto.empresa_facturar }}</dd>
            <dt class="font-semibold">Nº Factura:</dt><dd>{{ gasto.factura }}</dd>
            <dt class="font-semibold">Estado:</dt>
            <dd>
              <span
                :class="estadoClass(gasto.estado)"
                class="px-2 py-1 rounded-full text-xs font-semibold inline-flex items-center gap-1 shadow-sm transition"
              >
                {{ estadoIcon(gasto.estado) }} {{ gasto.estado }}
              </span>
            </dd>
          </dl>
          <div class="mt-4 flex flex-wrap gap-2 justify-end">
            <button
              @click="editGasto(gasto.id)"
              class=" text-white px-3 py-1 rounded-md text-sm hover:bg-blue-600 transition focus:outline-none focus:ring-2 focus:ring-blue-400"
              aria-label="Editar gasto"
            >
              ✏️ Editar
            </button>
            <button
              @click="handleDelete(gasto.id)"
              class=" text-white px-3 py-1 rounded-md text-sm hover:bg-red-600 transition focus:outline-none focus:ring-2 focus:ring-red-400"
              aria-label="Eliminar gasto"
            >
              🗑️ Eliminar
            </button>
          </div>
        </article>
      </section>

      <!-- Paginación -->
      <nav
        class="mt-8 flex justify-center items-center space-x-1 text-sm sm:text-base font-medium"
        role="navigation"
        aria-label="Paginación de gastos"
      >
        <button
          @click="goToPage(paginaActual - 1)"
          :disabled="paginaActual === 1"
          class="flex items-center px-3 py-1.5 rounded-md border border-blue-500 text-blue-500 hover:bg-blue-100 transition disabled:opacity-50 disabled:cursor-not-allowed"
          aria-disabled="paginaActual === 1"
          aria-label="Página anterior"
        >
          <span class="mr-1">⬅️</span> Anterior
        </button>

        <template v-for="page in totalPaginas" :key="page">
          <button
            @click="goToPage(page)"
            :class="[
              'px-3 py-1.5 rounded-md transition min-w-[2.5rem] truncate',
              page === paginaActual ? 'bg-blue-600 text-white' : 'border border-blue-500 text-blue-600 hover:bg-blue-100'
            ]"
            aria-current="page === paginaActual ? 'page' : false"
            :aria-label="`Ir a la página ${page}`"
          >
            {{ page }}
          </button>
        </template>

        <button
          @click="goToPage(paginaActual + 1)"
          :disabled="paginaActual === totalPaginas || totalPaginas === 0"
          class="flex items-center px-3 py-1.5 rounded-md border border-blue-500 text-blue-500 hover:bg-blue-100 transition disabled:opacity-50 disabled:cursor-not-allowed"
          aria-disabled="paginaActual === totalPaginas || totalPaginas === 0"
          aria-label="Página siguiente"
        >
          Siguiente <span class="ml-1">➡️</span>
        </button>
      </nav>

    </main>
  </AppMain>
</template>
