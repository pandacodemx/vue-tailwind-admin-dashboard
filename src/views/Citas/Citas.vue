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
              <Multiselect v-model="cita.cliente" :options="clientes"
                :custom-label="(c) => `${c.nombre} - ${c.telefono}`" placeholder="Selecciona un cliente" track-by="id"
                :searchable="true" :sort-by="['nombre']" :sort-direction="'asc'" />
            </div>

            <!-- Servicios -->
            <div>
              <label class="block mb-1 text-gray-700 dark:text-gray-300">Servicios</label>
              <Multiselect v-model="cita.servicios" :options="servicios" :multiple="true" :close-on-select="false"
                :clear-on-select="false" placeholder="Selecciona uno o varios servicios"
                :custom-label="(s) => `${s.nombre} (${formatearDuracion(s.duracion)})`" track-by="id"
                :searchable="true" />
            </div>

            <!-- Paquetes 
            <div>
              <label class="block mb-1 text-gray-700 dark:text-gray-300">Paquetes</label>
              <Multiselect
                v-model="cita.paquetes"
                :options="paquetes"
                :multiple="true"
                :close-on-select="false"
                :clear-on-select="false"
                placeholder="Selecciona uno o varios paquetes"
                :custom-label="
                  (p) => `${p.nombre} (${formatearDuracion(p.duracion)}) - $${p.precio}`
                "
                track-by="id"
                :searchable="true"
              />
            </div>-->

            <!-- Fecha y Hora -->
            <div>
              <label class="block mb-1 text-gray-700 dark:text-gray-300">Fecha y hora</label>
              <Datepicker v-model="cita.fecha" :locale="es" :enable-time-picker="true" :minute-increment="15"
                format="dd/MM/yyyy HH:mm" placeholder="Selecciona fecha y hora"
                :disabled-dates="(date) => !esDiaPermitido(date)" auto-apply class="w-full" />
            </div>
            <div class="mt-2 text-gray-700 dark:text-gray-300">
              ⏰ Hora estimada de finalización: <strong>{{ formatHora(fechaFinCalculada) }}</strong>
            </div>

            <div>
              <label class="block mb-1 text-gray-700 dark:text-gray-300">Nota</label>
              <textarea v-model="cita.notas" rows="3"
                class="w-full border rounded px-3 py-2 dark:bg-gray-800 dark:text-white"
                placeholder="Agrega alguna nota..."></textarea>
            </div>

            <div class="mt-2 text-gray-700 dark:text-gray-300">
              💰 Total estimado: <strong>${{ totalCita.toFixed(2) }}</strong>
            </div>

            <!-- Botón -->
            <div class="pt-4">
              <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
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
import { ref, onMounted, computed, watch } from 'vue'
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
  paquetes: [],
  fecha: new Date(),
  notas: ''
})

const horariosAtencion = ref([])
const clientes = ref([])
const servicios = ref([])
const paquetes = ref([])

onMounted(() => {
  cargarClientes()
  cargarServicios()
  cargarPaquetes()
  cargarHorarios()
})

/**PAQUETES**/
async function cargarPaquetes() {
  const res = await HttpService.get('/paquetes/obtener.php')
  paquetes.value = res
}

function formatearDuracion(minutos) {
  if (minutos < 60) return `${minutos} min`
  const horas = Math.floor(minutos / 60)
  const mins = minutos % 60
  return mins > 0 ? `${horas}h ${mins}min` : `${horas}h`
}
async function cargarHorarios() {
  const res = await HttpService.get('/configuracion/horarios.php')
  if (Array.isArray(res)) {
    horariosAtencion.value = res
  }
}
async function cargarCitasDisponibles() {
  const res = await HttpService.get('/citas/listar.php')
  return res.filter((cita) => cita.estado !== 'cancelada')
}

async function esHorarioDisponible(fechaInicio, fechaFin) {
  const citas = await cargarCitasDisponibles()

  for (const c of citas) {
    const inicio = new Date(c.fecha)
    const fin = new Date(c.fecha_fin)

    if (
      (fechaInicio >= inicio && fechaInicio < fin) ||
      (fechaFin > inicio && fechaFin <= fin) ||
      (fechaInicio <= inicio && fechaFin >= fin)
    ) {
      return false // choque detectado
    }
  }

  return true
}

async function cargarClientes() {
  const res = await HttpService.get('/clientes/obtener.php')
  clientes.value = res
}

async function cargarServicios() {
  const res = await HttpService.get('/servicios/obtener.php')
  servicios.value = res
}

function esDiaPermitido(date) {
  const dia = date.getDay()
  const horarioDia = horariosAtencion.value.find((h) => h.dia === dia)
  return horarioDia && horarioDia.activo
}

function esHoraPermitida(date) {
  const dia = date.getDay() // 0 (Dom) - 6 (Sáb)
  const horarioDia = horariosAtencion.value.find((h) => h.dia === dia)

  if (!horarioDia || !horarioDia.activo) return false

  const hora =
    date.getHours().toString().padStart(2, '0') +
    ':' +
    date.getMinutes().toString().padStart(2, '0')

  return hora >= horarioDia.desde && hora <= horarioDia.hasta
}

function formatFechaLocal(fecha) {
  const pad = (n) => n.toString().padStart(2, '0')
  return `${fecha.getFullYear()}-${pad(fecha.getMonth() + 1)}-${pad(fecha.getDate())} ${pad(fecha.getHours())}:${pad(fecha.getMinutes())}:00`
}

function calcularFechaFin() {
  if (!cita.value.fecha) return null

  let totalMinutos = 0

  // Servicios individuales
  totalMinutos += (cita.value.servicios || []).reduce((total, serv) => total + serv.duracion, 0)

  // Servicios que vienen dentro de los paquetes
  cita.value.paquetes.forEach(paquete => {
    if (paquete.servicios && paquete.servicios.length) {
      totalMinutos += paquete.servicios.reduce((total, s) => total + s.duracion, 0)
    }
  })

  const fechaFin = new Date(cita.value.fecha)
  fechaFin.setMinutes(fechaFin.getMinutes() + totalMinutos)

  return fechaFin
}

function prepararServiciosParaGuardar(cita) {
  const servicios = [...cita.servicios]

  // Agregar servicios de cada paquete
  cita.paquetes.forEach(paquete => {
    if (paquete.servicios && paquete.servicios.length) {
      servicios.push(...paquete.servicios)
    }
  })

  return servicios
}

async function guardarCita() {

  if (!esHoraPermitida(cita.value.fecha)) {
    notify({
      title: 'Horario no válido',
      text: 'La cita debe estar dentro del horario de atención.',
      type: 'warn',
    })
    return
  }

  const fechaFin = calcularFechaFin()
  if (!fechaFin) return

  const disponible = await esHorarioDisponible(cita.value.fecha, fechaFin)
  if (!disponible) {
    notify({
      title: 'Horario ocupado',
      text: 'Ya hay una cita en ese horario.',
      type: 'warn',
    })
    return
  }
  const serviciosFinales = prepararServiciosParaGuardar(cita.value)
  const payload = {
    cliente_id: cita.value.cliente.id,
    fecha: formatFechaLocal(new Date(cita.value.fecha)),
    fecha_fin: fechaFin ? formatFechaLocal(fechaFin) : null,
    notas: cita.value.notas,
    total: totalCita.value,
    servicios: serviciosFinales.map((s) => ({
      id: s.id,
      precio: s.precio,
    })),
  }
  try {
    const response = await HttpService.post('/citas/crear.php', payload)
    if (response.success) {
      notify({
        title: 'Cita registrada',
        text: 'La cita fue guardada correctamente.',
        type: 'success',
      })
      resetFormulario()
      cita.value = { cliente: null, fecha: new Date(), servicios: [], notas: '' }
    } else {
      notify({
        title: 'Error',
        text: response.message,
        type: 'error',
      })
    }
  } catch (error) {
    console.error('Error al guardar cita:', error)
  }
}
const totalCita = computed(() => {
  let total = 0

  // Servicios
  total += cita.value.servicios.reduce((sum, s) => sum + parseFloat(s.precio), 0)

  // Paquete //Ya tiene descuento aplic
  total += cita.value.paquetes.reduce((sum, p) => sum + parseFloat(p.precio), 0)

  return total
})

const fechaFinCalculada = computed(() => {
  if (!cita.value.fecha) return null
  return calcularFechaFin()
})

function formatHora(fecha) {
  if (!fecha) return '--:--'
  const pad = (n) => n.toString().padStart(2, '0')
  return `${pad(fecha.getHours())}:${pad(fecha.getMinutes())}`
}

function resetFormulario() {
  cita.value = {
    cliente: null,
    servicios: [],
    paquetes: [],
    fecha: new Date(),
    notas: ''
  }
}


watch([() => cita.value.fecha, () => cita.value.servicios], () => {
  console.log('La cita terminaría a:', formatHora(fechaFinCalculada.value))
})
</script>

<style>
.vue-notification.warn {
  color: #000000;
}

.vue-notification.error {
  color: #ffff;
}

.vue-notification.success {
  color: #000000;
}

.multiselect__option--highlight {
  background: #25875b;
  outline: 0;
  color: #fff;
}
</style>
