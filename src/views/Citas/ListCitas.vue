<template>
  <AdminLayout>
    <PageBreadcrumb pageTitle="Citas registradas" />
    <ComponentCard title="Listado de Citas">
      <!-- Filtros -->
      <div class="flex flex-col md:flex-row items-center justify-between mb-4 gap-4">
        <!-- Buscador -->
        <input
          v-model="filtroTexto"
          type="text"
          placeholder="Buscar por cliente o servicio..."
          class="px-4 py-2 border rounded w-full md:w-1/3 dark:bg-gray-800 dark:text-white dark:border-gray-600"
        />

        <!-- Filtro de fechas -->
        <div class="flex gap-2 items-center">
          <Datepicker
            v-model="fechaInicio"
            :locale="es"
            placeholder="Desde"
            class="border rounded px-2 py-1 dark:bg-gray-800 dark:text-white"
          />
          <Datepicker
            v-model="fechaFin"
            :locale="es"
            placeholder="Hasta"
            class="border rounded px-2 py-1 dark:bg-gray-800 dark:text-white"
          />
        </div>
      </div>

      <div
        class="overflow-visible rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]"
      >
        <table class="max-w-full overflow-x-auto custom-scrollbar">
          <thead>
            <tr class="border-b border-gray-200 dark:border-gray-700">
              <th class="px-5 py-3 text-left w-2/11 sm:px-6">
                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Cliente</p>
              </th>
              <th class="px-5 py-3 text-left w-2/11 sm:px-6">
                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Servicios</p>
              </th>
              <th class="px-5 py-3 text-left w-2/11 sm:px-6">
                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Fecha</p>
              </th>
              <th class="px-5 py-3 text-left w-2/11 sm:px-6">
                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Estado</p>
              </th>
              <th class="px-5 py-3 text-left w-2/11 sm:px-6">
                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Notas</p>
              </th>
              <th class="px-5 py-3 text-left w-2/11 sm:px-6">
                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Acciones</p>
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            <tr
              v-for="cita in citasPaginadas"
              :key="cita.id"
              class="border-t border-gray-100 dark:border-gray-800"
            >
              <td class="px-5 py-4 sm:px-6">
                <div class="flex items-center gap-3">
                  <div>
                    <span class="block font-medium text-gray-800 text-theme-sm dark:text-white/90">
                      {{ cita.cliente_nombre }}
                    </span>
                  </div>
                </div>
              </td>
              <td class="px-5 py-4 sm:px-6">
                <div class="flex items-center gap-3">
                  <div>
                    <span class="block font-medium text-gray-800 text-theme-sm dark:text-white/90">
                      <li v-for="s in cita.servicios" :key="s.id">{{ s.nombre }}</li>
                    </span>
                  </div>
                </div>
              </td>
              <td class="px-5 py-4 sm:px-6">
                <div class="flex items-center gap-3">
                  <div>
                    <span class="block font-medium text-gray-800 text-theme-sm dark:text-white/90">
                      {{ formatFecha(cita.fecha) }}
                    </span>
                  </div>
                </div>
              </td>

              <td class="px-4 py-2">
                <!-- Estado con dropdown -->
                <div class="relative inline-block text-left">
                  <button
                    @click="toggleDropdown(cita)"
                    class="inline-flex justify-center items-center px-3 py-1 rounded-full text-sm font-medium"
                    :class="{
                      'bg-yellow-100 text-yellow-800': cita.estado === 'pendiente',
                      'bg-green-100 text-green-800': cita.estado === 'atendida',
                      'bg-red-100 text-red-800': cita.estado === 'cancelada',
                    }"
                  >
                    {{ capitalize(cita.estado) }}
                    <svg class="ml-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                      <path
                        fill-rule="evenodd"
                        d="M5.23 7.21a.75.75 0 011.06.02L10 11.586l3.71-4.356a.75.75 0 111.14.976l-4.25 5a.75.75 0 01-1.14 0l-4.25-5a.75.75 0 01.02-1.06z"
                        clip-rule="evenodd"
                      />
                    </svg>
                  </button>

                  <!-- Dropdown -->
                  <div
                    v-if="cita.mostrandoDropdown"
                    class="absolute z-10 mt-2 w-32 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5"
                  >
                    <ul class="py-1 text-sm text-gray-700">
                      <li
                        v-for="estado in estados"
                        :key="estado"
                        @click="cambiarEstado(cita, estado)"
                        class="block px-4 py-2 hover:bg-gray-100 cursor-pointer"
                      >
                        {{ capitalize(estado) }}
                      </li>
                    </ul>
                  </div>
                </div>
              </td>
              <td class="px-4 py-2">{{ cita.notas || '—' }}</td>
              <td class="px-4 py-2 space-x-2">
                <button @click="editarCita(cita)" class="text-blue-500 hover:underline">✏️</button>
              </td>
            </tr>
          </tbody>
        </table>
        <div class="flex justify-between items-center mt-4 px-4">
          <div class="text-sm text-gray-600 dark:text-gray-400">
            Página {{ paginaActual }} de {{ totalPaginas }}
          </div>

          <div class="flex gap-2">
            <button
              :disabled="paginaActual === 1"
              @click="paginaActual--"
              class="px-3 py-1 rounded border text-sm"
            >
              ← Anterior
            </button>
            <button
              :disabled="paginaActual === totalPaginas"
              @click="paginaActual++"
              class="px-3 py-1 rounded border text-sm"
            >
              Siguiente →
            </button>
          </div>
        </div>
      </div>
    </ComponentCard>
  </AdminLayout>
</template>
<script setup>
import { onMounted, ref, computed, onUnmounted } from 'vue'
import HttpService from '@/services/HttpService'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import ComponentCard from '@/components/common/ComponentCard.vue'
import { format } from 'date-fns'
import { es } from 'date-fns/locale'
import { notify } from '@kyvg/vue3-notification'
import Datepicker from 'vue3-datepicker'


const citas = ref([])
const filtroTexto = ref('')
const fechaInicio = ref(null)
const fechaFin = ref(null)
const paginaActual = ref(1)
const citasPorPagina = ref(7)
const citasNotificadas = ref(new Set())

function resetHora(fecha, fin = false) {
  const f = new Date(fecha)
  f.setHours(fin ? 23 : 0, fin ? 59 : 0, fin ? 59 : 0, 999)
  return f
}

const totalPaginas = computed(() => {
  return Math.ceil(citasFiltradas.value.length / citasPorPagina.value)
})

const citasPaginadas = computed(() => {
  const inicio = (paginaActual.value - 1) * citasPorPagina.value
  const fin = inicio + citasPorPagina.value
  return citasFiltradas.value.slice(inicio, fin)
})

const citasFiltradas = computed(() => {
  return citas.value.filter((cita) => {
    const texto = filtroTexto.value.toLowerCase()
    const coincideTexto =
      cita.cliente_nombre.toLowerCase().includes(texto) ||
      cita.servicios.some((s) => s.nombre.toLowerCase().includes(texto))

    const fechaCita = new Date(cita.fecha)
    const desde = fechaInicio.value ? resetHora(fechaInicio.value) : null
    const hasta = fechaFin.value ? resetHora(fechaFin.value, true) : null

    const coincideFecha = (!desde || fechaCita >= desde) && (!hasta || fechaCita <= hasta)

    return coincideTexto && coincideFecha
  })
})

let intervaloCitas = null

onMounted(() => {
  cargarCitas()

})

onUnmounted(() => {
  clearInterval(intervaloCitas)
})

onMounted(() => {
  cargarCitas()
})

async function cargarCitas() {
  const res = await HttpService.get('/citas/listar.php')
  citas.value = res.map((cita) => ({
    ...cita,
    mostrandoDropdown: false,
  }))

}

function formatFecha(fecha) {
  return format(new Date(fecha), "dd 'de' MMMM yyyy, HH:mm", { locale: es })
}



const estados = ['pendiente', 'atendida', 'cancelada']

function toggleDropdown(citaSeleccionada) {
  citas.value.forEach((cita) => {
    if (cita.id !== citaSeleccionada.id) {
      cita.mostrandoDropdown = false
    }
  })
  citaSeleccionada.mostrandoDropdown = !citaSeleccionada.mostrandoDropdown
}

function capitalize(str) {
  return str.charAt(0).toUpperCase() + str.slice(1)
}

async function cambiarEstado(cita, nuevoEstado) {
  cita.estado = nuevoEstado
  cita.mostrandoDropdown = false

  try {
    const res = await HttpService.post('/citas/cambiar_estado.php', {
      id: cita.id,
      estado: nuevoEstado,
    })

    if (!res.success) throw new Error()

    notify({
      title: 'Estado actualizado',
      text: 'El estado se cambió correctamente.',
      type: 'success',
    })
  } catch (err) {
    notify({
      title: 'Error',
      text: 'No se pudo actualizar el estado.',
      type: 'error',
    })
  }
}
</script>
<style>
.vue-notification.success {
  color: #041f0e; /* verde oscuro */
}

.vue-notification.error {
  color: #b91c1c; /* rojo fuerte */
}
</style>
