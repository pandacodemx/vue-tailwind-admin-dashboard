<template>
  <div
    class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]"
  >
    <div class="max-w-full overflow-x-auto custom-scrollbar">
      <table class="min-w-full">
        <thead>
          <tr class="border-b border-gray-200 dark:border-gray-700">
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
          <tr
            v-for="producto in productos"
            :key="producto.id"
            class="border-t border-gray-100 dark:border-gray-800"
          >
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
              <span
                  :class="{
                    'text-green-700 font-medium ': producto.activo == 1,
                    'text-red-600 font-medium': producto.activo == 0,
                  }"
                >
                  {{ producto.activo == 1 ? 'Activo' : 'Inactivo' }}
                </span>
            </td>
            <td class="px-5 py-4 sm:px-6 flex items-center gap-2">
              <button @click="$emit('editar', producto)" title="Editar" class="dark:text-white">✏️</button>

              <button @click="$emit('eliminar', producto)" class="text-red-500 hover:underline dark:text-white">
                🗑️
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>


defineProps({
  productos: {
    type: Array,
    required: true
  }
})

defineEmits(['editar', 'eliminar'])
</script>

<style scoped>
/* Add any additional styles here if needed */
</style>
