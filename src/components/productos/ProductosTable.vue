<template>
  <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="max-w-full overflow-x-auto custom-scrollbar">
      <table class="min-w-full">
        <thead>
          <tr class="border-b border-gray-200 dark:border-gray-700">
            <th class="px-5 py-3 text-left sm:px-6">
              <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Imagen</p>
            </th>
            <th class="px-5 py-3 text-left w-3/11 sm:px-6">
              <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Nombre</p>
            </th>
            <th class="px-5 py-3 text-left w-2/11 sm:px-6">
              <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Precio</p>
            </th>
            <th class="px-5 py-3 text-left w-2/11 sm:px-6">
              <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Inventario</p>
            </th>
            <th class="px-5 py-3 text-left w-2/11 sm:px-6">
              <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Estado</p>
            </th>
            <th class="px-5 py-3 text-left w-2/11 sm:px-6">
              <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Acciones</p>
            </th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
          <tr v-for="producto in productos" :key="producto.id" class="border-t border-gray-100 dark:border-gray-800">
            <td class="px-5 py-4">
              <div class="size-20 rounded overflow-hidden group cursor-pointer relative"
                @click="abrirModalImagen(producto)">
                <img v-if="producto.imagen"
                  :src="`http://localhost/vue-tailwind-admin-dashboard/${producto.imagen}`"
                  :alt="producto.nombre"
                  class="w-full h-full object-cover transition-all duration-300 group-hover:scale-110 group-hover:shadow-lg">
                <div v-else
                  class="w-full h-full bg-gray-200 flex items-center justify-center group-hover:bg-gray-300 transition-colors duration-300">
                  <span class="text-gray-500 text-xs">Sin imagen</span>
                </div>
                <div
                  class="absolute inset-0 flex items-center justify-center  bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-300 rounded">
                  <svg v-if="producto.imagen"
                    class="w-6 h-6 text-dark opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                  </svg>
                </div>
              </div>
            </td>
            <td class="px-5 py-4 sm:px-6">
              <div class="flex items-center gap-3">
                <div>
                  <span class="block font-medium text-gray-800 text-theme-sm dark:text-white/90">
                    {{ producto.nombre }}
                  </span>
                  <span class="block text-gray-500 text-theme-xs dark:text-gray-400">
                    Categoria: {{ producto.categoria }}
                  </span>
                </div>
              </div>
            </td>
            <td class="px-5 py-4 sm:px-6">
              <p class="text-gray-500 text-theme-sm dark:text-gray-600"> ${{ producto.precio }}</p>
            </td>
            <td class="px-5 py-4 sm:px-6">
              <div class="flex -space-x-2">
                <span class="block text-gray-500 text-theme-xs dark:text-gray-400">
                  {{ producto.stock }}
                </span>
              </div>
            </td>
            <td class="px-5 py-4 sm:px-6 text-theme-sm">
              <span :class="{
                'text-green-700 font-medium ': producto.activo == 1,
                'text-red-600 font-medium': producto.activo == 0,
              }">
                {{ producto.activo == 1 ? 'Activo' : 'Inactivo' }}
              </span>
            </td>
            <td class="px-5 py-4 sm:px-6 flex items-center gap-2">
              <button @click="$emit('editar', producto)" title="Editar"
                class="dark:text-white ease-in-out hover:scale-125 hover:duration-300 ">✏️</button>

              <button @click="$emit('eliminar', producto)"
                class="text-red-500 hover:underline dark:text-white ease-in-out hover:scale-125 hover:duration-300 ">
                🗑️
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <!--////////////////////////////MODAL PREVIEW //////////////////////////-->
    <div v-if="modalImagenAbierto"
      class="fixed inset-0 bg-gray-700/35 bg-opacity-75 flex items-center justify-center z-50 p-4"
      @click="cerrarModalImagen">
      <div class="max-w-4xl max-h-full" @click.stop>
        <div class="bg-white rounded-lg overflow-hidden">
          <img :src="imagenAmpliada" :alt="productoSeleccionado?.nombre"
            class="w-full h-auto max-h-[80vh] object-contain">
          <div class="p-4 bg-white">
            <h3 class="text-lg font-semibold">{{ productoSeleccionado?.nombre }}</h3>
            <p class="text-gray-600">Categoría: {{ productoSeleccionado?.categoria }}</p>
          </div>
        </div>
        <button @click="cerrarModalImagen"
          class="absolute top-4 right-4 text-black text-2xl bg-black bg-opacity-50 rounded-full w-10 h-10 flex items-center justify-center">
          ×
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const modalImagenAbierto = ref(false)
const imagenAmpliada = ref('')
const productoSeleccionado = ref(null)

const abrirModalImagen = (producto) => {
  if (producto.imagen) {
    productoSeleccionado.value = producto
    imagenAmpliada.value = `http://localhost/vue-tailwind-admin-dashboard/${producto.imagen}`
    modalImagenAbierto.value = true
  }
}

const cerrarModalImagen = () => {
  modalImagenAbierto.value = false
  productoSeleccionado.value = null
  imagenAmpliada.value = ''
}


const handleKeydown = (e) => {
  if (e.key === 'Escape') {
    cerrarModalImagen()
  }
}


import { onMounted, onUnmounted } from 'vue'
onMounted(() => {
  document.addEventListener('keydown', handleKeydown)
})
onUnmounted(() => {
  document.removeEventListener('keydown', handleKeydown)
})

defineProps({
  productos: {
    type: Array,
    required: true
  }
})

defineEmits(['editar', 'eliminar'])
</script>