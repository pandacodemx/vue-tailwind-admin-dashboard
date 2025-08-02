<template>
  <div class="bg-white dark:bg-gray-900 rounded p-4 shadow">
    <h2 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white">
      📈 Distribución de Citas por Estado
    </h2>
    <Doughnut :data="chartData" :options="chartOptions" />
  </div>
</template>

<script setup>
import { Doughnut } from 'vue-chartjs'
import { computed } from 'vue'
import { Chart as ChartJS, Title, Tooltip, Legend, ArcElement } from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, ArcElement)

const props = defineProps({
  resumen: {
    type: Object,
    required: true,
  },
})

const chartData = computed(() => ({
  labels: ['Pendientes', 'Atendidas', 'Canceladas'],
  datasets: [
    {
      label: 'Citas',
      data: [props.resumen.pendientes, props.resumen.atendidas, props.resumen.canceladas],
      backgroundColor: ['#facc15', '#4ade80', '#f87171'],
      borderColor: '#ffffff',
      borderWidth: 2,
    },
  ],
}))

const chartOptions = {
  responsive: true,
  plugins: {
    legend: {
      position: 'bottom',
      labels: {
        color: '#333',
      },
    },
  },
}

</script>

