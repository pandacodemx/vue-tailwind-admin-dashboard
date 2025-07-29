<template>
  <AdminLayout>
    <PageBreadcrumb :pageTitle="currentPageTitle" />
    <div class="space-y-5 sm:space-y-6">
      <ComponentCard title="Lista de Clientes">
        <div class="flex items-center justify-end">
          <Button size="sm" variant="primary" :endIcon="UserCircleIcon" @click="openModal">
            Registrar Cliente
          </Button>
        </div>
        <ClientesTable />
        <Modal v-if="isModalOpen">
          <template #body>
            <div
              class="no-scrollbar relative w-full max-w-[700px] overflow-y-auto rounded-3xl bg-white p-4 dark:bg-gray-900 lg:p-11"
            >
              <button
                @click="isModalOpen = false"
                class="transition-color absolute right-5 top-5 z-999 flex h-11 w-11 items-center justify-center rounded-full bg-gray-100 text-gray-400 hover:bg-gray-200 hover:text-gray-600 dark:bg-gray-700 dark:bg-white/[0.05] dark:text-gray-400 dark:hover:bg-white/[0.07] dark:hover:text-gray-300"
              >
                <svg
                  class="fill-current"
                  width="24"
                  height="24"
                  viewBox="0 0 24 24"
                  fill="none"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <path
                    fill-rule="evenodd"
                    clip-rule="evenodd"
                    d="M6.04289 16.5418C5.65237 16.9323 5.65237 17.5655 6.04289 17.956C6.43342 18.3465 7.06658 18.3465 7.45711 17.956L11.9987 13.4144L16.5408 17.9565C16.9313 18.347 17.5645 18.347 17.955 17.9565C18.3455 17.566 18.3455 16.9328 17.955 16.5423L13.4129 12.0002L17.955 7.45808C18.3455 7.06756 18.3455 6.43439 17.955 6.04387C17.5645 5.65335 16.9313 5.65335 16.5408 6.04387L11.9987 10.586L7.45711 6.04439C7.06658 5.65386 6.43342 5.65386 6.04289 6.04439C5.65237 6.43491 5.65237 7.06808 6.04289 7.4586L10.5845 12.0002L6.04289 16.5418Z"
                    fill=""
                  />
                </svg>
              </button>
              <h3 class="text-lg font-medium mb-4">Registrar Nuevo Cliente</h3>
              <form @submit.prevent="guardarCliente" class="space-y-4">
                <input
                  v-model="nuevoCliente.nombre"
                  placeholder="Nombre"
                  required
                  class="input-class"
                />
                <input
                  v-model="nuevoCliente.telefono"
                  placeholder="Teléfono"
                  required
                  class="input-class"
                />
                <input
                  v-model="nuevoCliente.correo"
                  type="email"
                  placeholder="Correo"
                  required
                  class="input-class"
                />
                <div class="flex justify-end space-x-2">
                  
                  <Button type="submit" variant="primary">Guardar</Button>
                </div>
              </form>
            </div>
          </template>
        </Modal>
      </ComponentCard>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import ComponentCard from '@/components/common/ComponentCard.vue'
import ClientesTable from '@/components/clientes/ClientesTable.vue'
import Button from '@/components/ui/Button.vue'
import Modal from '@/components/ui/Modal.vue'
import { UserCircleIcon } from '@/icons'
const currentPageTitle = ref('Tabla de Clientes')

const isModalOpen = ref(false)

const clientes = ref([
  { id: 1, nombre: 'Juan Pérez', telefono: '555-1234567', correo: 'juan@example.com' },
  { id: 2, nombre: 'María López', telefono: '555-9876543', correo: 'maria@example.com' },
])

const nuevoCliente = ref({
  nombre: '',
  telefono: '',
  correo: '',
})

function openModal() {
  isModalOpen.value = true
}

function closeModal() {
  isModalOpen.value = false
}

function guardarCliente() {
  clientes.value.push({ id: Date.now(), ...nuevoCliente.value })
  nuevoCliente.value = { nombre: '', telefono: '', correo: '' }
  closeModal()
}
</script>
<style>
.input-class {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #cbd5e0;
  border-radius: 0.375rem;
}
.modal > div:first-child {
  backdrop-filter: blur(32px);
  -webkit-backdrop-filter: blur(32px);
}
</style>
