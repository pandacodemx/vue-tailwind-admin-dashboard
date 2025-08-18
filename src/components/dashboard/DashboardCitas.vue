<template>
  <div class="space-y-6">
    <!-- Título -->
    <h1 class="text-2xl font-bold text-gray-800 dark:text-white">📊 Dashboard de Citas</h1>
    <div class="flex gap-2">
      <Button><RouterLink to="/citas/nueva" class="btn-primary">+ Nueva Cita</RouterLink></Button>

      <Button><RouterLink to="/citas/calendario" class="btn-secondary">📅 Ver Calendario</RouterLink></Button>
    </div>

    <!-- Totales -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div
        v-for="(valor, key) in resumenCards"
        :key="key"
        class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]"
      >
        <div class="flex items-end justify-between">
          <div>
            <span class="text-sm text-gray-500 dark:text-gray-400">{{ key }}</span>
            <h4 class="mt-2 font-bold text-gray-800 text-title-sm dark:text-white/90">
              {{ valor }}
            </h4>
          </div>
        </div>
      </div>
    </div>
    <!-- Paneles: Próximas Citas, Clientes Frecuentes y Gráfica -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <!-- Próximas Citas -->
      <div class="bg-white dark:bg-gray-900 rounded shadow p-4">
        <h2 class="text-lg font-semibold mb-2 text-gray-800 dark:text-white">📅 Próximas Citas</h2>
        <ul class="divide-y divide-gray-200 dark:divide-gray-700">
          <li v-for="cita in resumen.proximas" :key="cita.id" class="py-2">
            <p class="text-sm text-gray-700 dark:text-gray-300">
              <strong>{{ formatearFecha(cita.fecha) }}</strong> - {{ cita.cliente }}<br />
              <span class="text-xs text-gray-500">Servicios: {{ cita.servicios }}</span>
              <span
                class="ml-2 px-2 py-0.5 rounded-full text-xs"
                :class="{
                  'bg-yellow-100 text-yellow-800': cita.estado === 'pendiente',
                  'bg-green-100 text-green-800': cita.estado === 'atendida',
                  'bg-red-100 text-red-800': cita.estado === 'cancelada',
                }"
              >
                {{ cita.estado }}
              </span>
            </p>
          </li>
        </ul>
      </div>

      <!-- Clientes Frecuentes -->
      <div class="bg-white dark:bg-gray-900 rounded shadow p-4">
        <h2 class="text-lg font-semibold mb-2 text-gray-800 dark:text-white">
          🏆 Clientes frecuentes
        </h2>
        <ul class="divide-y divide-gray-200 dark:divide-gray-700">
          <li v-for="cliente in resumen.top_clientes" :key="cliente.nombre" class="py-2">
            <p class="text-sm text-gray-700 dark:text-gray-300">
              {{ cliente.nombre }} —
              <span class="text-xs text-gray-500">{{ cliente.total_citas }} citas</span>
            </p>
          </li>
        </ul>
      </div>

      <!-- Gráfica -->
      <GraficaCitas :resumen="resumen" />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import HttpService from '@/services/HttpService'
import GraficaCitas from '@/components/dashboard/GraficaCitas.vue'
import { verificarStockBajo, verificarCitasProximas } from '@/utils/notificaciones.js'
import Button from '../ui/Button.vue'

const resumen = ref({
  total: 0,
  pendientes: 0,
  atendidas: 0,
  canceladas: 0,
  proximas: [],
  top_clientes: [],
})

const resumenCards = ref({})
const productos = ref([])
const citas = ref([])

const productosNotificados = new Set(JSON.parse(localStorage.getItem('stockNotificado') || '[]'))
const citasNotificadas = new Set(JSON.parse(localStorage.getItem('citasNotificadas') || '[]'))

onMounted(async () => {
  const data = await HttpService.get('/citas/resumen_dashboard.php')
  resumen.value = data

  resumenCards.value = {
    '🟡 Pendientes': data.pendientes || 0,
    '🟢 Atendidas': data.atendidas || 0,
    '🔴 Canceladas': data.canceladas || 0,
    '📊 Total Citas': data.total || 0,
  }

  const productosData = await HttpService.get('/productos/obtener.php')
  productos.value = productosData
  verificarStockBajo(productos.value, productosNotificados)

  const citasData = await HttpService.get('/citas/listar.php')
  citas.value = citasData
  verificarCitasProximas(citas.value, citasNotificadas)

  // Guardar los sets actualizados
  localStorage.setItem('stockNotificado', JSON.stringify([...productosNotificados]))
  localStorage.setItem('citasNotificadas', JSON.stringify([...citasNotificadas]))
})

function formatearFecha(fecha) {
  return new Date(fecha).toLocaleString('es-MX', {
    dateStyle: 'medium',
    timeStyle: 'short',
  })
}
</script>
