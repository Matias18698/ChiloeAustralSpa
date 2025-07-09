<script setup>
import { onMounted, watch, ref } from 'vue'
import { Chart, registerables } from 'chart.js'

Chart.register(...registerables)

const props = defineProps({
  gastos: Array,
  mensuales: Array
})

const chartRef = ref(null)
let chartInstance = null

const renderChart = () => {
  if (chartInstance) chartInstance.destroy()

  const labels = props.mensuales.map(m => m.mes)
  const dataNeto = props.mensuales.map(m => m.total_neto)
  const dataFacturado = props.mensuales.map(m => m.total_facturado)

  chartInstance = new Chart(chartRef.value, {
    type: 'bar',
    data: {
      labels,
      datasets: [
        {
          label: 'Valor Neto',
          backgroundColor: '#4ade80',
          data: dataNeto,
        },
        {
          label: 'Valor Facturado',
          backgroundColor: '#60a5fa',
          data: dataFacturado,
        },
      ]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { position: 'top' },
        title: { display: true, text: 'Gastos Mensuales' }
      }
    }
  })
}

onMounted(() => renderChart())
watch(() => props.mensuales, () => renderChart(), { deep: true })
</script>

<template>
  <canvas ref="chartRef"></canvas>
</template>
