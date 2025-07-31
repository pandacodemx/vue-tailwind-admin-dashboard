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
        <input
          v-model="filtro"
          placeholder="Buscar por nombre, teléfono o correo"
          class="w-full max-w-md px-4 py-2 mb-4 border rounded dark:text-white"
        />

        <ClientesTable
          :clientes="clientesPaginados"
          @editar="editarCliente"
          @eliminar="solicitarEliminacion"
        />
        <div class="flex justify-between items-center mt-4">
          <p class="text-sm text-gray-500 dark:text-gray-400 dark:text-white">
            Mostrando {{ clientesPaginados.length }} de {{ clientesFiltrados.length }} clientes
          </p>

          <div class="flex gap-2">
            <Button
              @click="cambiarPagina(paginaActual - 1)"
              :disabled="paginaActual === 1"
              variant="secondary"
              class="dark:text-white"
              >Anterior</Button
            >
            <span class="text-sm dark:text-white">
              Página {{ paginaActual }} de {{ totalPaginas }}
            </span>
            <Button
              @click="cambiarPagina(paginaActual + 1)"
              :disabled="paginaActual === totalPaginas"
              variant="secondary"
              class="dark:text-white"
              >Siguiente</Button
            >
          </div>
        </div>
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
              <h3 class="text-lg font-medium mb-4 dark:text-white">Registrar Nuevo Cliente</h3>
              <form @submit.prevent="guardarCliente" class="space-y-4">
                <input
                  v-model="clienteEditando.nombre"
                  placeholder="Nombre"
                  required
                  class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                />
                <input
                  v-model="clienteEditando.telefono"
                  placeholder="Teléfono"
                  required
                  class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                />
                <div>
                  <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    Email
                  </label>
                  <div class="relative">
                    <span
                      class="absolute left-0 top-1/2 -translate-y-1/2 border-r border-gray-200 px-3.5 py-3 text-gray-500 dark:border-gray-800 dark:text-gray-400"
                    >
                      <svg
                        width="20"
                        height="20"
                        viewBox="0 0 20 20"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                      >
                        <path
                          fill-rule="evenodd"
                          clip-rule="evenodd"
                          d="M3.04175 7.06206V14.375C3.04175 14.6511 3.26561 14.875 3.54175 14.875H16.4584C16.7346 14.875 16.9584 14.6511 16.9584 14.375V7.06245L11.1443 11.1168C10.457 11.5961 9.54373 11.5961 8.85638 11.1168L3.04175 7.06206ZM16.9584 5.19262C16.9584 5.19341 16.9584 5.1942 16.9584 5.19498V5.20026C16.9572 5.22216 16.946 5.24239 16.9279 5.25501L10.2864 9.88638C10.1145 10.0062 9.8862 10.0062 9.71437 9.88638L3.07255 5.25485C3.05342 5.24151 3.04202 5.21967 3.04202 5.19636C3.042 5.15695 3.07394 5.125 3.11335 5.125H16.8871C16.9253 5.125 16.9564 5.15494 16.9584 5.19262ZM18.4584 5.21428V14.375C18.4584 15.4796 17.563 16.375 16.4584 16.375H3.54175C2.43718 16.375 1.54175 15.4796 1.54175 14.375V5.19498C1.54175 5.1852 1.54194 5.17546 1.54231 5.16577C1.55858 4.31209 2.25571 3.625 3.11335 3.625H16.8871C17.7549 3.625 18.4584 4.32843 18.4585 5.19622C18.4585 5.20225 18.4585 5.20826 18.4584 5.21428Z"
                          fill="#667085"
                        />
                      </svg>
                    </span>
                    <input
                      v-model="clienteEditando.correo"
                      type="email"
                      required
                      placeholder="info@gmail.com"
                      class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pl-[62px] text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                    />
                  </div>
                </div>                
                <select v-model="clienteEditando.status" class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800">
                  <option value="" class="dark:text-white">Seleccionar estado cliente</option>
                  <option value="1" class="dark:text-white">Activo</option>
                  <option value="0" class="dark:text-white">Inactivo</option>
                </select>
                <div class="flex justify-end space-x-2">
                  <Button
                    variant="secondary"
                    @click="closeModal"
                    class="dark:text-white"
                    type="button"
                    >Cancelar</Button
                  >
                  <Button type="submit" variant="primary">Guardar</Button>
                </div>
              </form>
            </div>
          </template>
        </Modal>
        <Modal v-if="isDeleteModalOpen" @close="closeDeleteModal">
          <template #body>
            <div class="bg-white dark:bg-gray-900 rounded-xl p-6 max-w-md mx-auto text-center">
              <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">
                ¿Eliminar cliente?
              </h3>
              <p class="text-gray-600 dark:text-gray-400 mb-6">
                ¿Estás seguro de que deseas eliminar a
                <strong>{{ clienteAEliminar?.nombre }}</strong
                >? Esta acción no se puede deshacer.
              </p>
              <div class="flex justify-center gap-4">
                <Button variant="secondary" class="dark:text-white" @click="closeDeleteModal"
                  >Cancelar</Button
                >
                <Button variant="destructive" class="dark:text-white" @click="confirmarEliminacion"
                  >Eliminar</Button
                >
              </div>
            </div>
          </template>
        </Modal>
      </ComponentCard>
    </div>
  </AdminLayout>
</template>

<script setup>
import { onMounted, ref, computed } from 'vue'
import { UserCircleIcon } from '@/icons'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import ComponentCard from '@/components/common/ComponentCard.vue'
import ClientesTable from '@/components/clientes/ClientesTable.vue'
import Button from '@/components/ui/Button.vue'
import Modal from '@/components/ui/Modal.vue'
import HttpService from '@/services/HttpService'
import { notify } from '@kyvg/vue3-notification'

const currentPageTitle = ref('Tabla de Clientes')
const isModalOpen = ref(false)
const isDeleteModalOpen = ref(false)
const clienteAEliminar = ref(null)
const clienteEditando = ref(null)
const clientes = ref([])
const filtro = ref('')
const paginaActual = ref(1)
const porPagina = 10

const clientesFiltrados = computed(() =>
  clientes.value.filter((cliente) =>
    `${cliente.nombre} ${cliente.telefono} ${cliente.correo}`
      .toLowerCase()
      .includes(filtro.value.toLowerCase()),
  ),
)

const totalPaginas = computed(() => Math.ceil(clientesFiltrados.value.length / porPagina))

const clientesPaginados = computed(() => {
  const inicio = (paginaActual.value - 1) * porPagina
  return clientesFiltrados.value.slice(inicio, inicio + porPagina)
})

function cambiarPagina(nueva) {
  if (nueva >= 1 && nueva <= totalPaginas.value) {
    paginaActual.value = nueva
  }
}

onMounted(() => {
  cargarClientes()
})

async function cargarClientes() {
  try {
    const data = await HttpService.get('/clientes/obtener.php')
    clientes.value = data
  } catch (error) {
    console.error('Error al cargar clientes:', error)
  }
}
function openModal() {
  clienteEditando.value = { nombre: '', telefono: '', correo: '', status: '' }
  isModalOpen.value = true
}

function closeModal() {
  isModalOpen.value = false
  clienteEditando.value = null
}

async function guardarCliente() {
  try {
    if (clienteEditando.value.id) {
      const response = await HttpService.post('/clientes/editar.php', clienteEditando.value)

      if (response.success) {
        notify({
          title: 'Éxito',
          text: 'Cliente guardado correctamente',
          type: 'primary',
          duration: 4000,
          speed: 500,
        })
        await cargarClientes()
        closeModal()
      } else {
        notify({
          title: 'Error',
          text: 'No se pudo guardar el cliente',
          type: 'error',
        })
      }
    } else {
      const response = await HttpService.post('/clientes/crear.php', clienteEditando.value)

      if (response.success) {
        await cargarClientes()
        closeModal()
      } else {
        notify({
          title: 'Error',
          text: 'No se pudo guardar el cliente',
          type: 'error',
        })
      }
    }
  } catch (error) {
    console.error('Error al guardar cliente:', error)
    alert('Ocurrió un error al guardar el cliente.')
  }
}

function editarCliente(cliente) {
  clienteEditando.value = { ...cliente }
  isModalOpen.value = true
}

function solicitarEliminacion(cliente) {
  clienteAEliminar.value = cliente
  isDeleteModalOpen.value = true
}

function closeDeleteModal() {
  isDeleteModalOpen.value = false
  clienteAEliminar.value = null
}

async function confirmarEliminacion() {
  try {
    const response = await HttpService.post('/clientes/eliminar.php', {
      id: clienteAEliminar.value.id,
    })

    if (response.success) {
      notify({
        title: 'Eliminado',
        text: 'Cliente desactivado correctamente.',
        type: 'primary',
      })
      await cargarClientes()
      isDeleteModalOpen.value = false
    } else {
      notify({
        title: 'Error',
        text: 'No se pudo desactivar el cliente.',
        type: 'error',
      })
    }
  } catch (error) {
    console.error('Error al eliminar:', error)
    notify({
      title: 'Error',
      text: 'Ocurrió un error inesperado.',
      type: 'error',
    })
  }
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
.vue-notification.primary {
  color: #000000;
}

.vue-notification.error {
  color: #b91c1c; /* rojo fuerte */
}
</style>
