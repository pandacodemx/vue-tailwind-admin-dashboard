<template>
  <div>
    <div class="p-5 mb-6 border border-gray-200 rounded-2xl dark:border-gray-800 lg:p-6">
      <div class="flex flex-col gap-5 xl:flex-row xl:items-center xl:justify-between">
        <div class="flex flex-col items-center w-full gap-6 xl:flex-row">
          <div
            class="w-20 h-20 overflow-hidden border border-gray-200 rounded-full dark:border-gray-800"
          >
            <img src="/images/user/icon_barber.png" alt="user" />
          </div>
          <div class="order-3 xl:order-2">
            <h4
              class="mb-2 text-lg font-semibold text-center text-gray-800 dark:text-white/90 xl:text-left"
            >
            {{ user.first_name }} {{ user.last_name }}
            </h4>
            <div
              class="flex flex-col items-center gap-1 text-center xl:flex-row xl:gap-3 xl:text-left"
            >
              <p class="text-sm text-gray-500 dark:text-gray-400 uppercase">Rol: {{ user.rol }} </p>
              <div class="hidden h-3.5 w-px bg-gray-300 dark:bg-gray-700 xl:block"></div>
              <p class="text-sm text-gray-500 dark:text-gray-400 uppercase">México</p>
            </div>
          </div>
         
        </div>   
      </div>
    </div>
    
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import HttpService from '@/services/HttpService';

const user = ref({})
onMounted(async () =>
{
  cargarPerfil()
})


async function cargarPerfil() {
  try {
    const data = await HttpService.get('/usuarios/perfil.php')
    user.value = data
  } catch (error) {
    console.error('Error al cargar perfil:', error)
  }
}
</script>
