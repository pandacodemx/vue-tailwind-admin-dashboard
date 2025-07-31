<template>
  <AdminLayout>
    <PageBreadcrumb :pageTitle="currentPageTitle" />
    <div class="space-y-5 sm:space-y-6">
      <ComponentCard title="Registrar nueva cita">
        <div class="p-6 bg-white rounded-lg shadow dark:bg-gray-900">
          <h2 class="text-2xl font-semibold mb-4 text-gray-800 dark:text-white">💇 Agendar Cita</h2>

          <form @submit.prevent="guardarCita" class="space-y-5">
            <!-- Cliente -->
            <div>
              <label class="block mb-1 text-gray-700 dark:text-gray-300">Cliente</label>
              <Multiselect
                v-model="cita.cliente"
                :options="clientes"
                :custom-label="(c) => `${c.nombre} - ${c.telefono}`"
                placeholder="Selecciona un cliente"
                track-by="id"
                :searchable="true"
                :sort-by="['nombre']"
                :sort-direction="'asc'"
              />
            </div>

            <!-- Servicios -->
            <div>
              <label class="block mb-1 text-gray-700 dark:text-gray-300">Servicios</label>
              <Multiselect
                v-model="cita.servicios"
                :options="servicios"
                :multiple="true"
                :close-on-select="false"
                :clear-on-select="false"
                placeholder="Selecciona uno o varios servicios"
                label="nombre"
                track-by="id"
                :searchable="true"
              />
            </div>

            <!-- Fecha y Hora -->
            <div>
              <label class="block mb-1 text-gray-700 dark:text-gray-300">Fecha y hora</label>
              <Datepicker
                v-model="cita.fecha"
                :locale="es"
                :enable-time-picker="true"
                :minute-increment="15"
                format="dd/MM/yyyy HH:mm"
                placeholder="Selecciona fecha y hora"
                auto-apply
                class="w-full"
              />
            </div>

            <!-- Botón -->
            <div class="pt-4">
              <button
                type="submit"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
              >
                Guardar Cita
              </button>
            </div>
          </form>
        </div>
      </ComponentCard>
    </div>
  </AdminLayout>
</template>

<script setup>
import { es } from 'date-fns/locale'
import { ref, onMounted } from 'vue'
import HttpService from '@/services/HttpService'
import { notify } from '@kyvg/vue3-notification'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import ComponentCard from '@/components/common/ComponentCard.vue'
import Multiselect from 'vue-multiselect'
import Datepicker from '@vuepic/vue-datepicker'
import '@vuepic/vue-datepicker/dist/main.css'
import 'vue-multiselect/dist/vue-multiselect.min.css'

const cita = ref({
  cliente: null,
  servicios: [],
  fecha: new Date(),
})

const clientes = ref([])
const servicios = ref([])

onMounted(() => {
  cargarClientes()
  cargarServicios()
})

async function cargarClientes() {
  const res = await HttpService.get('/clientes/obtener.php')
  clientes.value = res
}

async function cargarServicios() {
  const res = await HttpService.get('/servicios/obtener.php')
  servicios.value = res
}

async function guardarCita() {
  try {
    const response = await HttpService.post('/citas/crear.php', cita.value)
    if (response.success) {
      notify({
        title: 'Cita registrada',
        text: 'La cita fue guardada correctamente.',
        type: 'success',
      })
      // Resetear el formulario
      cita.value = { cliente_id: '', fecha: '', servicios: [], notas: '' }
    } else {
      notify({
        title: 'Error',
        text: 'No se pudo guardar la cita.',
        type: 'error',
      })
    }
  } catch (error) {
    console.error('Error al guardar cita:', error)
  }
}
</script>
