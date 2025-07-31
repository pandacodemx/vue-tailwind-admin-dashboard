<template>
    <div class="fixed inset-0 flex items-center justify-center bg-gray-300 bg-opacity-50 z-50">
      <div class="bg-white dark:bg-gray-900 rounded-lg p-6 max-w-lg w-full relative">
        <button
          @click="$emit('cerrar')"
          class="absolute top-3 right-3 text-gray-600 hover:text-gray-900 dark:hover:text-white"
          aria-label="Cerrar modal"
        >
          ✖
        </button>
  
        <h2 class="text-xl font-semibold mb-4 dark:text-white">
          {{ modoEdicion ? 'Editar Cita' : 'Detalles de la Cita' }}
        </h2>
  
        <form v-if="modoEdicion" @submit.prevent="guardarCambios" class="space-y-4">
          <!-- Cliente -->
          <div>
            <label class="block mb-1 text-gray-700 dark:text-gray-300">Cliente</label>
            <Multiselect
              v-model="form.cliente"
              :options="clientes"
              :custom-label="c => `${c.nombre} - ${c.telefono}`"
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
              v-model="form.servicios"
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
              v-model="form.fecha"
              :locale="es"
              :enable-time-picker="true"
              :minute-increment="15"
              format="dd/MM/yyyy HH:mm"
              placeholder="Selecciona fecha y hora"
              auto-apply
              class="w-full"
            />
          </div>
  
          <!-- Notas -->
          <div>
            <label class="block mb-1 text-gray-700 dark:text-gray-300">Notas</label>
            <textarea
              v-model="form.notas"
              rows="3"
              class="w-full border rounded px-3 py-2 dark:bg-gray-800 dark:text-white"
              placeholder="Agrega alguna nota..."
            ></textarea>
          </div>
  
          <div class="flex justify-end space-x-2">
            <button type="button" @click="cancelarEdicion" class="px-4 py-2 border rounded dark:border-gray-600 dark:text-white">
              Cancelar
            </button>
          </div>
        </form>
  
        <!-- Vista solo lectura -->
        <div v-else class="space-y-2 dark:text-white">
          <p><strong>Cliente:</strong> {{ cita.cliente_nombre }}</p>
          <p>
            <strong>Servicios:</strong>
            <ul class="list-disc list-inside">
              <li v-for="s in cita.servicios" :key="s.id">{{ s.nombre }}</li>
            </ul>
          </p>
          <p><strong>Fecha:</strong> {{ formatFecha(cita.fecha) }}</p>
          <p><strong>Estado:</strong> {{ capitalize(cita.estado) }}</p>
          <p><strong>Notas:</strong> {{ cita.notas || '—' }}</p>
  
      
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, watch, onMounted } from 'vue'
  import { es } from 'date-fns/locale'
  import { format } from 'date-fns'
  import Multiselect from 'vue-multiselect'
  import Datepicker from '@vuepic/vue-datepicker'
  import '@vuepic/vue-datepicker/dist/main.css'
  import 'vue-multiselect/dist/vue-multiselect.min.css'
  import HttpService from '@/services/HttpService'
  import { notify } from '@kyvg/vue3-notification'
  
  const props = defineProps({
    cita: Object,
  })
  
  const emit = defineEmits(['cerrar', 'actualizar'])
  
  const modoEdicion = ref(false)
  const clientes = ref([])
  const servicios = ref([])
  const form = ref({
    cliente: null,
    servicios: [],
    fecha: null,
    notas: '',
    id: null,
  })
  
  onMounted(async () => {
    clientes.value = await HttpService.get('/clientes/obtener.php')
    servicios.value = await HttpService.get('/servicios/obtener.php')
  })
  
  watch(
    () => props.cita,
    (newCita) => {
      modoEdicion.value = false
      if (newCita) {
        form.value = {
          cliente: clientes.value.find(c => c.id === newCita.cliente_id) || null,
          servicios: newCita.servicios || [],
          fecha: new Date(newCita.fecha),
          notas: newCita.notas || '',
          id: newCita.id,
        }
      }
    },
    { immediate: true }
  )
  
  function formatFecha(fecha) {
    return format(new Date(fecha), "dd 'de' MMMM yyyy, HH:mm", { locale: es })
  }
  
  function capitalize(str) {
    return str.charAt(0).toUpperCase() + str.slice(1)
  }
  
  async function guardarCambios() {
    try {
      const payload = {
        id: form.value.id,
        cliente_id: form.value.cliente.id,
        servicios: form.value.servicios.map(s => s.id),
        fecha: form.value.fecha,
        notas: form.value.notas,
      }
      const res = await HttpService.post('/citas/editar.php', payload)
  
      if (res.success) {
        notify({
          title: 'Cita actualizada',
          text: 'Los cambios se guardaron correctamente.',
          type: 'success',
        })
        modoEdicion.value = false
        emit('actualizar') // para que el padre recargue datos si quiere
        emit('cerrar')
      } else {
        notify({
          title: 'Error',
          text: 'No se pudo actualizar la cita.',
          type: 'error',
        })
      }
    } catch (error) {
      console.error(error)
      notify({
        title: 'Error',
        text: 'Ocurrió un error inesperado.',
        type: 'error',
      })
    }
  }
  
  function cancelarEdicion() {
    modoEdicion.value = false
  }
  </script>
  