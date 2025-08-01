<!-- src/components/citas/CalendarioCitas.vue -->
<template>
  <div>
    <FullCalendar :options="calendarOptions" class="bg-white dark:bg-gray-900 p-4 rounded shadow" />

    <!-- Modal con detalles -->
    <ModalDetalleCita v-if="modalVisible" :cita="citaSeleccionada" @cerrar="modalVisible = false" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import interactionPlugin from '@fullcalendar/interaction'
import esLocale from '@fullcalendar/core/locales/es'
import HttpService from '@/services/HttpService'
import ModalDetalleCita from '@/components/citas/ModalDetalleCita.vue'

const eventos = ref([])
const modalVisible = ref(false)
const citaSeleccionada = ref(null)

const calendarOptions = ref({
  plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
  initialView: 'timeGridWeek',
  headerToolbar: {
    left: 'prev,next today',
    center: 'title',
    right: 'dayGridMonth,timeGridWeek,timeGridDay',
  },
  locale: esLocale,
  events: eventos,
  nowIndicator: true,
  selectable: true,
  editable: false,
  eventContent: renderEventoPersonalizado,
  eventClick: handleEventClick,  
  selectConstraint: "businessHours",
  businessHours: [
    {
      daysOfWeek: [1, 2, 3, 4, 5], // Lunes a viernes
      startTime: '09:00',
      endTime: '18:00',
    },
    {
      daysOfWeek: [6], // Sábados
      startTime: '09:00',
      endTime: '14:00',
    },
  ],
})

onMounted(async () => {
  const citas = await HttpService.get('/citas/listar.php')

  eventos.value = citas.map((cita) => ({
    id: cita.id,
    title: cita.cliente_nombre,
    start: cita.fecha,
    end: calcularFin(cita.fecha, cita.duracion || 30),
    backgroundColor: colorPorEstado(cita.estado),
    borderColor: '#ccc',
    estado: cita.estado,
    servicios: cita.servicios,
    notas: cita.notas,
  }))

  calendarOptions.value.events = eventos.value
})

function calcularFin(fechaInicio, minutosDuracion) {
  const inicio = new Date(fechaInicio)
  return new Date(inicio.getTime() + minutosDuracion * 60000)
}

function colorPorEstado(estado) {
  switch (estado) {
    case 'pendiente':
      return '#fa4e15'
    case 'atendida':
      return '#219c4e'
    case 'cancelada':
      return '#f87171'
    default:
      return '#93c5fd'
  }
}

function renderEventoPersonalizado(info) {
  const estado = info.event.extendedProps.estado || 'pendiente'

  const icono = {
    pendiente: '⏳',
    atendida: '✅',
    cancelada: '❌',
  }[estado]

  return {
    html: `
      <div class="fc-custom-event">
        <span class="fc-icon">${icono}</span>
        <span class="fc-title">${info.event.title}</span>
        <br />
        <small class="fc-hora">${info.timeText}</small>
      </div>
    `,
  }
}

function handleEventClick(info) {
  const evento = info.event
  citaSeleccionada.value = {
    id: evento.id,
    cliente_nombre: evento.title,
    fecha: evento.start,
    estado: evento.extendedProps.estado,
    servicios: evento.extendedProps.servicios || [],
    notas: evento.extendedProps.notas,
  }

  modalVisible.value = true
}
</script>

<style>
.fc-timegrid-slot {
  height: 3rem !important; /* o 48px, puedes ajustar */
}
.fc-event-title {
  white-space: normal !important;
  overflow: visible !important;
  text-overflow: unset !important;
  font-size: 0.85rem;
  line-height: 1.1rem;
}
.fc-event {
  padding: 4px 6px !important;
  border-radius: 0.5rem;
  font-weight: 500;
  font-size: 0.875rem;
}
.fc-timegrid-event .fc-event-time {
  font-weight: bold;
  margin-right: 6px;
}
.fc-custom-event {
  display: flex;
  flex-direction: column;
  font-size: 0.8rem;
  line-height: 1.2;
  white-space: normal;
  padding: 4px;
}

.fc-icon {
  font-size: 1.1rem;
  margin-bottom: 2px;
}

.fc-title {
  font-weight: 600;
}

.fc-hora {
  color: #555;
  font-size: 0.75rem;
}
</style>
