<!-- src/components/citas/ModalDetalleCita.vue -->
<template>
    <ModalBase :fullScreenBackdrop="true" @close="$emit('cerrar')">
      <template #body>
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-6 w-full max-w-md z-50">
          <h2 class="text-xl font-bold mb-4">Detalles de la Cita</h2>
  
          <p><strong>Cliente:</strong> {{ cita.cliente_nombre }}</p>
          <p><strong>Servicios:</strong> <span v-if="cita.servicios">{{ cita.servicios.map(s => s.nombre).join(', ') }}</span></p>
          <p><strong>Fecha:</strong> {{ formatFecha(cita.fecha) }}</p>
          <p><strong>Estado:</strong> {{ capitalize(cita.estado) }}</p>
          <p><strong>Notas:</strong> {{ cita.notas || '—' }}</p>
  
          <div class="mt-6 text-right">
            <button @click="$emit('cerrar')" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
              Cerrar
            </button>
          </div>
        </div>
      </template>
    </ModalBase>
  </template>
  
  <script setup>
  import { format } from 'date-fns'
  import { es } from 'date-fns/locale'
  import ModalBase from '@/components/ui/Modal.vue'
  
  const props = defineProps({
    cita: Object,
  })
  defineEmits(['cerrar'])
  
  function formatFecha(fecha) {
    return format(new Date(fecha), "dd 'de' MMMM yyyy, HH:mm", { locale: es })
  }
  
  function capitalize(str) {
    return str.charAt(0).toUpperCase() + str.slice(1)
  }
  </script>
  