<!-- src/views/VentasView.vue -->
<template>
  <AdminLayout>
    <PageBreadcrumb :pageTitle="currentPageTitle" />
    <div class="space-y-5 sm:space-y-6">
      <ComponentCard>
        <div class="p-6">
          <h1 class="text-2xl font-bold mb-4 text-gray-800 dark:text-white">Historial de Ventas</h1>
          <!-- Filtros de fecha -->
          <!-- Filtros con Datepicker -->
          <div class="flex flex-wrap gap-4 mb-4 items-end">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                >Desde:</label
              >
              <Datepicker
                v-model="filtro.fechaInicio"
                :enable-time="false"
                :format="'yyyy-MM-dd'"
                placeholder="Selecciona fecha inicio"
                auto-apply
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                >Hasta:</label
              >
              <Datepicker
                v-model="filtro.fechaFin"
                :enable-time="false"
                :format="'yyyy-MM-dd'"
                placeholder="Selecciona fecha fin"
                auto-apply
              />
            </div>

            <button
              @click="cargarVentas"
              class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
            >
              Filtrar
            </button>
          </div>
          
          <TablaVentas :ventas="ventasPaginadas" @ver_pdf="abrirPDF" @eliminar="eliminarVenta" />
          <div class="flex justify-end gap-2 mt-4">
            <button @click="anterior" :disabled="pagina === 1" class="btn-pagination">
              Anterior
            </button>
            <button
              @click="siguiente"
              :disabled="pagina * porPagina >= ventas.length"
              class="btn-pagination"
            >
              Siguiente
            </button>
          </div>
        </div>
      </ComponentCard>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import HttpService from '@/services/HttpService'
import TablaVentas from '@/components/Ventas/VentasTable.vue'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import ComponentCard from '@/components/common/ComponentCard.vue'
import Datepicker from '@vuepic/vue-datepicker'
import '@vuepic/vue-datepicker/dist/main.css'

const pagina = ref(1)
const porPagina = 5

const ventasPaginadas = computed(() => {
  const inicio = (pagina.value - 1) * porPagina
  return ventas.value.slice(inicio, inicio + porPagina)
})

function siguiente() {
  if (pagina.value * porPagina < ventas.value.length) pagina.value++
}

function anterior() {
  if (pagina.value > 1) pagina.value--
}

const ventas = ref([])
const filtro = ref({
  fechaInicio: null,
  fechaFin: null,
})

async function cargarVentas() {
  const params = new URLSearchParams()
  if (filtro.value.fechaInicio)
    params.append('fecha_inicio', filtro.value.fechaInicio.toISOString().split('T')[0])
  if (filtro.value.fechaFin)
    params.append('fecha_fin', filtro.value.fechaFin.toISOString().split('T')[0])

  const res = await HttpService.get(`/ventas/obtener.php?${params.toString()}`)
  ventas.value = res
}


function abrirPDF(venta) {
  window.open(`/tickets/ticket_${venta.id}.pdf`, '_blank')
}

function eliminarVenta(venta) {
  // Si deseas implementar eliminación lógica más adelante
  console.log('Eliminar:', venta)
}

onMounted(() => {
  cargarVentas()
})
</script>

<style scoped>
.input-fecha {
  padding: 6px;
  border: 1px solid #ccc;
  border-radius: 6px;
}
.btn-pagination {
  background-color: #4b5563;
  color: white;
  padding: 6px 12px;
  border-radius: 6px;
}
.btn-pagination:disabled {
  background-color: #9ca3af;
  cursor: not-allowed;
}
</style>
