<template>
  <AppMain>
    <Head title="Informe Diario de Buceo" />

    <!-- Contenedor general centrado con padding -->
    <div class="max-w-4xl mx-auto p-4">

      <!-- Botón exportar PDF -->
      <div class="flex justify-end mb-2">
        <button
          @click="exportarPDF"
          class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded shadow"
          type="button"
        >
          Exportar PDF
        </button>
      </div>

      <!-- Contenedor con scroll horizontal en móvil -->
      <div class="overflow-auto md:overflow-visible max-w-full">
        <!-- Contenido que se exporta -->
         <div id="bitacora-pdf" class="bg-white shadow-md rounded-md border border-gray-300 text-gray-800 print:shadow-none print:border-none print:rounded-none text-sm p-8 min-w-[700px]">
            <!-- ENCABEZADO -->
            <div class="flex justify-between items-start border-b pb-2 mb-4">
              <!-- Logo -->
              <div class="w-1/5">
                <img src="/storage/logo.png" alt="Logo Empresa" class="h-28 object-contain" />
              </div>

              <!-- Datos Empresa -->
              <div class="w-4/5 text-left">
                <div>
                  <p class="text-sm font-semibold leading-tight uppercase">Sociedad Servicios Acuícolas</p>
                  <p class="text-sm">Chiloé Austral SPA</p>
                  <p class="text-sm">RUT: 76.755.845-7</p>
                  <p class="text-sm">Camino San Antonio S/N, Quellón</p>
                </div>
                <p class="mt-4 text-base font-bold uppercase text-center">
                  Informe Diario de Buceo N° {{ bitacora.numero_boleta || '----' }}
                </p>
              </div>

              <!-- Fecha -->
              <div class="flex justify-end text-xs">
                <div class="flex space-x-1 items-center">
                  <div class="flex flex-col items-center text-center">
                    <div class="border border-gray-400 px-1 py-0.5 w-10 h-5 text-[10px] text-gray-700 flex items-center justify-center">Día</div>
                    <div class="border border-gray-400 border-t-0 px-1 py-1 w-10 h-8 flex items-center justify-center">
                      <strong class="text-[11px]">{{ (bitacora.fecha || '----').split('-')[2] || '--' }}</strong>
                    </div>
                  </div>
                  <div class="flex flex-col items-center text-center">
                    <div class="border border-gray-400 px-1 py-0.5 w-10 h-5 text-[10px] text-gray-700 flex items-center justify-center">Mes</div>
                    <div class="border border-gray-400 border-t-0 px-1 py-1 w-10 h-8 flex items-center justify-center">
                      <strong class="text-[11px]">{{ (bitacora.fecha || '----').split('-')[1] || '--' }}</strong>
                    </div>
                  </div>
                  <div class="flex flex-col items-center text-center">
                    <div class="border border-gray-400 px-1 py-0.5 w-12 h-5 text-[10px] text-gray-700 flex items-center justify-center">Año</div>
                    <div class="border border-gray-400 border-t-0 px-1 py-1 w-12 h-8 flex items-center justify-center">
                      <strong class="text-[11px]">{{ (bitacora.fecha || '----').split('-')[0] || '----' }}</strong>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- CUADRO PRINCIPAL -->
            <div class="border border-black p-4 mb-6 space-y-4">
              <!-- Supervisor y Centro -->
              <div class="flex border-b border-black pb-2">
                <p class="flex-1 text-left">
                  <strong>Supervisor:</strong> {{ bitacora.trabajador?.nombre + ' ' + bitacora.trabajador?.apellido || 'N/A' }}
                </p>
                <p class="flex-1 text-center">
                  <strong>Centro:</strong> {{ bitacora.centro || '----' }}
                </p>
              </div>

              <!-- Team Buceo y Total Jaulas -->
              <div class="border-b border-black pb-2 flex space-x-8">
                <!-- Team Buceo -->
                <div class="border-r border-black pr-4 flex items-center">
                  <p class="text-sm font-bold uppercase">Team Buceo</p>
                </div>
                <table>
                  <tr v-for="i in 5" :key="i">
                    <td class="text-sm font-bold uppercase text-center">{{ i }}.-</td>
                    <td class="text-sm uppercase border-b border-dotted border-gray-500 pb-1" style="width:300px">{{ bitacora[`buzo${i}`]?.nombre ? bitacora[`buzo${i}`]?.nombre + ' ' + bitacora[`buzo${i}`]?.apellido : ""}}</td>
                  </tr>
                </table>

                <!-- Total Jaulas -->
                <div class="border-l border-black pl-4 flex flex-col w-1/2">
                  <p class="text-center font-bold uppercase mb-2">Total Jaulas</p>
                  <div class="flex items-center relative h-6 border-b border-dotted border-gray-500">
                    <span class="font-mono w-full text-center text-sm z-10">
                      {{ bitacora.total_jaulas !== undefined ? bitacora.total_jaulas : '---' }}
                      {{ bitacora.dimension_jaulas !== undefined ? ` (${bitacora.dimension_jaulas})` : '' }}
                    </span>
                  </div>
                </div>
              </div>

              <!-- Horario en una sola línea -->
              <div class="flex items-center justify-between pb-2 text-sm">
                <!-- Hora Inicio -->
                <div>
                  <strong>Hora Inicio:</strong>
                  <span class="ml-1">
                    {{ bitacora.hora_inicial || '----' }}
                  </span>
                </div>

                <!-- Texto centrado -->
                <div class="text-center font-semibold">
                  TRABAJOS REALIZADOS POR BUZOS
                </div>

                <!-- Hora Termino -->
                <div class="text-right">
                  <strong>Hora Término:</strong>
                  <span class="ml-1">
                    {{ bitacora.hora_final || '----' }}
                  </span>
                </div>
              </div>

              <!-- Actividades AM y PM -->
              <div
                v-for="(titulo, tipo) in { actividades_am: 'Actividades AM', actividades_pm: 'Actividades PM' }"
                :key="tipo"
                class="pb-4 border-t border-black"
              >
                <p class="text-sm font-bold uppercase mb-2 pt-2">{{ titulo }}</p>
                <div class="text-justify whitespace-pre-wrap bg-white p-1">
                  {{ bitacora[tipo] || '---' }}
                </div>
              </div>

              <!-- Observaciones -->
              <div class="min-h-[80px] pb-4 border-t border-black bg-white whitespace-pre-wrap">
                <strong class="block mb-2 pt-2">OBSERVACIONES</strong>
                <div class="whitespace-pre-wrap">
                  {{ bitacora.observaciones || 'Sin observaciones registradas.' }}
                </div>
              </div>
            </div>

            <!-- Firma -->
            <div class="mt-8">
              <div class="flex justify-end items-start">
                <div class="text-center w-64">
                  <div class="text-left mb-3">
                    <div class="relative h-5 mb-2">
                      <span class="absolute top-[-0.5rem] text-sm">
                        Nombre: {{ bitacora.trabajador?.nombre + ' ' + bitacora.trabajador?.apellido || 'N/A' }}
                      </span>
                      <div class="border-b border-gray-700 w-full absolute bottom-0"></div>
                    </div>
                    <div class="relative h-5">
                      <span class="absolute top-[-0.5rem] text-sm">
                        Cargo: Supervisor
                      </span>
                      <div class="border-b border-gray-700 w-full absolute bottom-0"></div>
                    </div>
                  </div>

                  <div class="h-20 flex items-end justify-center mb-1 relative">
                    <img
                      v-if="bitacora.firma_encargado"
                      :src="`/storage/${bitacora.firma_encargado}`"
                      alt="Firma"
                      class="h-full object-contain"
                    />
                  </div>
                  <div class="border-t border-gray-700 mt-2 mb-1"></div>
                    <p class="text-sm mb-2">
                    Supervisor de Buceo<br>
                    Rut: {{ bitacora.trabajador?.rut || '----' }}
                    </p>
                  <p class="text-xs text-gray-600">
                  </p>
                </div>
              </div>
            </div>
          </div>
      </div>

    </div>
  </AppMain>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import AppMain from '@/Layouts/AppMain.vue'
import html2pdf from 'html2pdf.js'

defineProps({
  bitacora: Object,
})

const exportarPDF = () => {
  const element = document.getElementById('bitacora-pdf')
  const opt = {
    margin: 0,
    filename: `bitacora-${new Date().toISOString().slice(0, 10)}.pdf`,
    image: { type: 'jpeg', quality: 0.65 },
    html2canvas: { scale: 3, useCORS: true, backgroundColor: '#fff' },
    jsPDF: {
      unit: 'in',
      format: [8.5, 14], // tamaño carta
      orientation: 'portrait',
    },
    pagebreak: { avoid: ['*'] }, // evita cortes en elementos
  }

  html2pdf()
    .set(opt)
    .from(element)
    .toCanvas()
    .toPdf()
    .get('pdf')
    .then((pdf) => {
      const totalPages = pdf.internal.getNumberOfPages()
      if (totalPages > 1) {
        // si hay más de 1 página, elimina páginas extras para evitar cortes
        for (let i = totalPages; i > 1; i--) {
          pdf.deletePage(i)
        }
      }
    })
    .save()
}
</script>

<style scoped>
@media print {
  #bitacora-pdf {
    box-shadow: none;
    border: none;
    border-radius: 0;
    padding: 0;
  }
}

html, body, #app {
  height: 100%;
  overflow: visible !important;
}

#bitacora-pdf {
  overflow: visible !important;
  max-height: none !important;
  min-height: auto;
  box-sizing: border-box;
}

/* Ajuste para scroll horizontal en pantallas pequeñas */
@media (max-width: 640px) {
  .overflow-auto {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }
}

.flex.justify-end.mb-2 {
  margin-top: 0;
  margin-bottom: 0.5rem;
}
</style>
