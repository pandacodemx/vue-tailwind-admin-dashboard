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
                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Telefono</p>
              </th>
              <th class="px-5 py-3 text-left w-2/11 sm:px-6">
                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Correo</p>
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
              v-for="cliente in clientes":key="cliente.id"
              class="border-t border-gray-100 dark:border-gray-800"
            >
              <td class="px-5 py-4 sm:px-6">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 overflow-hidden rounded-full">
                    <img :src="cliente.avatar" :alt="cliente.nombre" />
                  </div>
                  <div>
                    <span class="block font-medium text-gray-800 text-theme-sm dark:text-white/90">
                      {{ cliente.nombre }}
                    </span>
                    <span class="block text-gray-500 text-theme-xs dark:text-gray-400">
                      {{ cliente.correo }}
                    </span>
                  </div>
                </div>
              </td>
              <td class="px-5 py-4 sm:px-6">
                <p class="text-gray-500 text-theme-sm dark:text-gray-400">{{ cliente.telefono }}</p>
              </td>
              <td class="px-5 py-4 sm:px-6">
                <div class="flex -space-x-2">
                  <span class="block text-gray-500 text-theme-xs dark:text-gray-400">
                      {{ cliente.correo }}
                    </span>
                </div>
              </td>
              <td class="px-5 py-4 sm:px-6">
                <span
                  :class="[
                    'rounded-full px-2 py-0.5 text-theme-xs font-medium',
                    {
                      'bg-success-50 text-success-700 dark:bg-success-500/15 dark:text-success-500':
                        cliente.status === 'Activo',
                      'bg-warning-50 text-warning-700 dark:bg-warning-500/15 dark:text-warning-400':
                      cliente.status === 'Inactivo',
                      'bg-error-50 text-error-700 dark:bg-error-500/15 dark:text-error-500':
                      cliente.status === 'Error',
                    },
                  ]"
                >
                  {{ cliente.status }}
                </span>
              </td>
              <td class="px-5 py-4 sm:px-6">
                <p class="text-gray-500 text-theme-sm dark:text-gray-400">{{ cliente.budget }}</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue'
  
  const clientes = ref([
    { id: 1, nombre: 'Juan Pérez', telefono: '555-1234567', correo: 'juan@example.com', status: 'Inactivo' },
    { id: 2, nombre: 'María López', telefono: '555-9876543', correo: 'maria@example.com' , status: 'Activo'},
  ]);
  </script>
  
  <style scoped>
  /* Add any additional styles here if needed */
  </style>
  