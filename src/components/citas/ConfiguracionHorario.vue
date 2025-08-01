<template>
    <div class="space-y-4 p-6 bg-white dark:bg-gray-900 rounded shadow">
      <h2 class="text-xl font-bold text-gray-800 dark:text-white">🕒 Configurar Horarios de Atención</h2>
  
      <div
        v-for="(dia, index) in horarios"
        :key="index"
        class="flex items-center gap-4 border-b py-2"
      >
        <label class="w-32 font-semibold dark:text-white">{{ diasSemana[index] }}</label>
  
        <label class="flex items-center gap-2 text-sm dark:text-white">
          <input type="checkbox" v-model="dia.activo" class="form-checkbox text-blue-600" />
          Activo
        </label>
  
        <div class="flex gap-2">
          <input
            type="time"
            v-model="dia.desde"
            :disabled="!dia.activo"
            class="border rounded px-2 py-1 w-28 dark:bg-gray-800 dark:text-white"
          />
          <span class="dark:text-white">a</span>
          <input
            type="time"
            v-model="dia.hasta"
            :disabled="!dia.activo"
            class="border rounded px-2 py-1 w-28 dark:bg-gray-800 dark:text-white"
          />
        </div>
      </div>
  
      <div class="pt-4 text-right">
        <button
          @click="guardarHorarios"
          class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded"
        >
          Guardar Horarios
        </button>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue'
  import { notify } from '@kyvg/vue3-notification'
  import HttpService from '@/services/HttpService'
  
  const diasSemana = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado']
  
  const horarios = ref(
    Array.from({ length: 7 }, (_, i) => ({
      dia: i,
      activo: [1, 2, 3, 4, 5].includes(i),
      desde: '09:00',
      hasta: '18:00',
    }))
  )
  
  onMounted(async () => {
    const data = await HttpService.get('/configuracion/horarios.php')
    if (Array.isArray(data)) {
      horarios.value = data
    }
  })
  
  async function guardarHorarios() {
    const res = await HttpService.post('/configuracion/guardar_horario.php', horarios.value)
    if (res.success) {
      notify({
        title: 'Horarios guardados',
        text: 'Los horarios de atención se actualizaron correctamente.',
        type: 'success',
      })
    } else {
      notify({
        title: 'Error',
        text: 'No se pudieron guardar los horarios.',
        type: 'error',
      })
    }
  }
  </script>

<style>
.vue-notification.success {
  color: #000000;
}
</style>

  