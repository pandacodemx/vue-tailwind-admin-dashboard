<!-- src/components/citas/CalendarioCitas.vue -->
<template>
  <div class="min-h-[calc(100vh-6rem)] overflow-auto">
    <FullCalendar v-if="calendarOptions && calendarOptions.initialView" :options="calendarOptions"
      class="bg-white dark:bg-gray-900 p-4 rounded shadow h-full" />

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
import { notify } from '@kyvg/vue3-notification'

const eventos = ref([])
const modalVisible = ref(false)
const citaSeleccionada = ref(null)
const horariosLaborales = ref([])

const calendarOptions = ref({})

function configurarCalendarOptions(horarios) {
  const businessHours = horarios
    .filter((h) => h.activo)
    .map((h) => ({
      daysOfWeek: [h.dia],
      startTime: h.desde,
      endTime: h.hasta,
    }))

  const todasLasHoras = horarios
    .filter((h) => h.activo)
    .map((h) => [h.desde, h.hasta])
    .flat()

  const slotMinTime = todasLasHoras.length ? todasLasHoras.sort()[0] : '09:00:00'
  const slotMaxTime = todasLasHoras.length ? todasLasHoras.sort().reverse()[0] : '18:00:00'

  calendarOptions.value = {
    plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
    initialView: 'timeGridWeek',
    headerToolbar: {
      left: 'prev,next today',
      center: 'title',
      right: 'dayGridMonth,timeGridWeek,timeGridDay',
    },
    locale: esLocale,
    nowIndicator: true,
    selectable: true,
    editable: true,
    eventDrop: handleEventDrop,
    eventContent: renderEventoPersonalizado,
    eventClick: handleEventClick,
    selectConstraint: 'businessHours',
    businessHours,
    slotMinTime,
    slotMaxTime,
    events: eventos.value, 
    selectAllow: (selectInfo) => {
    const ahora = new Date();
    return selectInfo.start >= ahora;
  },
  }
}


onMounted(async () => {
  const citas = await HttpService.get('/citas/citas_calendario.php')

  eventos.value = citas.map((cita) => ({
    id: cita.id,
    title: cita.cliente_nombre,
    start: cita.fecha,
    end: cita.fecha_fin,  
    backgroundColor: colorPorEstado(cita.estado),
    borderColor: '#ccc',
    estado: cita.estado,
    servicios: cita.servicios,
    notas: cita.notas,
  }))

  const horarios = await HttpService.get('/configuracion/horarios.php')
  horariosLaborales.value = horarios

  configurarCalendarOptions(horarios)
})

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
async function handleEventDrop(info) {
  const id = info.event.id;
  const nuevaFecha = info.event.start;
  
  if (!nuevaFecha) {
    notify({ title: 'Error', text: 'Fecha no válida', type: 'error' });
    info.revert();
    return;
  }
  const ahora = new Date();
  if (nuevaFecha < ahora) {
    const diffHoras = Math.floor((ahora - nuevaFecha) / (1000 * 60 * 60));
    let mensaje = '';
    
    if (nuevaFecha.toDateString() === ahora.toDateString()) {
      mensaje = `No puedes programar citas en horas pasadas (${diffHoras} hora(s) atrás)`;
    } else {
      mensaje = 'No puedes programar citas en fechas pasadas';
    }

    notify({
      title: '⏰ Horario no permitido',
      text: mensaje,
      type: 'error'
    });
    info.revert();
    return;
  }
  const ajustarZonaHoraria = (date) => {
    const offset = date.getTimezoneOffset() * 60000;
    return new Date(date.getTime() - offset);
  };

  const fechaAjustada = ajustarZonaHoraria(nuevaFecha);
  const fechaParaServidor = fechaAjustada.toISOString().slice(0, 19).replace('T', ' ');

  try {
    const res = await HttpService.post('/citas/reprogramar.php', {
      id,
      nueva_fecha: fechaParaServidor
    });

    if (res.success) {
      notify({
        title: 'Éxito',
        text: '✅Cita reprogramada correctamente',
        type: 'success'
      });
      await cargarEventos(); 
    } else {
      notify({
        title: 'Error',
        text: res.error || 'Error al reprogramar',
        type: 'error'
      });
      info.revert();
    }
  } catch (error) {
    console.error("Error completo:", error.response?.data || error.message);
    notify({
      title: 'Error',
      text: 'Error de conexión al reprogramar',
      type: 'error'
    });
    info.revert();
  }
}
async function cargarEventos() {
  try {
    const citas = await HttpService.get('/citas/citas_calendario.php');
    
    eventos.value = citas.map((cita) => ({
      id: cita.id,
      title: cita.cliente_nombre,
      start: cita.fecha,
      end: cita.fecha_fin,   
      backgroundColor: colorPorEstado(cita.estado),
      borderColor: '#ccc',
      estado: cita.estado,
      servicios: cita.servicios,
      notas: cita.notas,
    }));
  
    if (calendarOptions.value) {
      calendarOptions.value.events = eventos.value;
    }
  } catch (error) {
    console.error('Error al cargar citas:', error);
  }
}
</script>

<style>
.fc-past-event {
  opacity: 0.5;
  background-color: #f0f0f0 !important;
  cursor: not-allowed;
}
.fc-timegrid-slot {
  height: 3rem !important;

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
  color: #ffffff;
  font-size: 0.75rem;
}

.vue-notification.success {
  color: #000000;
}

.vue-notification.warn {
  color: #000000;
}
</style>
