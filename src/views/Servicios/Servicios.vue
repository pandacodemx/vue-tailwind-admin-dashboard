<template>
  <AdminLayout>
    <PageBreadcrumb :pageTitle="currentPageTitle" />
    <div class="space-y-5 sm:space-y-6">
      <ComponentCard title="Lista de Servicios">
        <!-- ELEMENTOS USUARIO-->
        <div class="flex items-center justify-end">
          <button @click="openModal" class="edit-button">
            <svg
              class="fill-current"
              width="18"
              height="18"
              viewBox="0 0 18 18"
              fill="none"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                fill-rule="evenodd"
                clip-rule="evenodd"
                d="M15.0911 2.78206C14.2125 1.90338 12.7878 1.90338 11.9092 2.78206L4.57524 10.116C4.26682 10.4244 4.0547 10.8158 3.96468 11.2426L3.31231 14.3352C3.25997 14.5833 3.33653 14.841 3.51583 15.0203C3.69512 15.1996 3.95286 15.2761 4.20096 15.2238L7.29355 14.5714C7.72031 14.4814 8.11172 14.2693 8.42013 13.9609L15.7541 6.62695C16.6327 5.74827 16.6327 4.32365 15.7541 3.44497L15.0911 2.78206ZM12.9698 3.84272C13.2627 3.54982 13.7376 3.54982 14.0305 3.84272L14.6934 4.50563C14.9863 4.79852 14.9863 5.2734 14.6934 5.56629L14.044 6.21573L12.3204 4.49215L12.9698 3.84272ZM11.2597 5.55281L5.6359 11.1766C5.53309 11.2794 5.46238 11.4099 5.43238 11.5522L5.01758 13.5185L6.98394 13.1037C7.1262 13.0737 7.25666 13.003 7.35947 12.9002L12.9833 7.27639L11.2597 5.55281Z"
                fill=""
              />
            </svg>
            Nuevo Servicio
          </button>
        </div>
        <!--TABLA SERVICIOS-->
        <input
          v-model="filtro"
          placeholder="Buscar por nombre, categoria, precio"
          class="w-full max-w-md px-4 py-2 mb-4 border rounded dark:text-white"
        />
        <ServiciosTable
          :servicios="serviciosPaginados"
          @eliminar="solicitarEliminacion"
          @editar="editarServicio"
        />
        <div class="flex justify-between items-center mt-4">
          <p class="text-sm text-gray-500 dark:text-gray-400 dark:text-white">
            Mostrando {{ serviciosPaginados.length }} de {{ serviciosFiltrados.length }} servicios
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

        <!--MODAL CREACION / EDICION-->
        <Modal v-if="isModalOpen">
          <template #body>
            <div
              class="no-scrollbar relative w-full max-w-[700px] overflow-y-auto rounded-3xl bg-white p-4 dark:bg-gray-900 lg:p-11"
            >
              <!-- close btn -->
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
              <div class="px-2 pr-14">
                <h4 class="mb-2 text-2xl font-semibold text-gray-800 dark:text-white/90">
                  Registro Nuevo Servicio
                </h4>
                <p class="mb-6 text-sm text-gray-500 dark:text-gray-400 lg:mb-7">
                  Detalles sobre el servicio a ofrecer.
                </p>
              </div>
              <form class="flex flex-col" @submit.prevent="guardarServicio">
                <div class="custom-scrollbar h-[458px] overflow-y-auto p-2">
                  <div>
                    <h5 class="mb-5 text-lg font-medium text-gray-800 dark:text-white/90 lg:mb-6">
                      Servicio
                    </h5>

                    <div class="grid grid-cols-1 gap-x-6 gap-y-5 lg:grid-cols-2">
                      <div>
                        <label
                          class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                        >
                          Nombre:
                        </label>
                        <input
                          v-model="servicioEditando.nombre"
                          placeholder="Corte clásico"
                          class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                        />
                      </div>

                      <div>
                        <label
                          class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                        >
                          Precio:
                        </label>
                        <input
                          type="number"
                          v-model="servicioEditando.precio"
                          class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                        />
                      </div>

                      <div>
                        <label
                          class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                        >
                          Duración
                        </label>
                        <input
                          type="input"
                          v-model="servicioEditando.duracion"
                          class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                        />
                      </div>

                      <div>
                        <label
                          class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                        >
                          Categoria
                        </label>
                        <select
                          v-model="servicioEditando.categoria"
                          class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                        >
                          <option value="">Selecciona una categoría</option>
                          <option value="barbería">Barbería</option>
                          <option value="estética">Estética</option>
                          <option value="uñas">Uñas</option>
                          <option value="facial">Facial</option>
                          <option value="cejas">Cejas y Pestañas</option>
                          <option value="depilacion">Depilacion</option>
                          <option value="masajes">Masajes y Spa</option>
                          <option value="corporal">Tratamientos Corporales</option>
                          <option value="otros">Otros</option>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="mt-7">
                    <div class="grid grid-cols-1 gap-x-6 gap-y-5 lg:grid-cols-2">
                      <div class="col-span-2 lg:col-span-1">
                        <label
                          class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                        >
                          Estado
                        </label>
                        <select
                          v-model="servicioEditando.status"
                          class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                        >
                          <option value="" class="dark:text-white">
                            Seleccionar estado cliente
                          </option>
                          <option value="1" class="dark:text-white">Activo</option>
                          <option value="0" class="dark:text-white">Inactivo</option>
                        </select>
                      </div>

                      <div class="col-span-2">
                        <label
                          class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                        >
                          Detalles
                        </label>
                        <input
                          v-model="servicioEditando.detalles"
                          type="text"
                          placeholder="Detalles del servicio"                     
                          class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                        />
                      </div>
                    </div>
                  </div>
                </div>
                <div class="flex items-center gap-3 px-2 mt-6 lg:justify-end">
                  <button
                    @click="closeModal"
                    type="button"
                    class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] sm:w-auto"
                  >
                    Cerrar
                  </button>
                  <button
                    type="submit"
                    class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto"
                  >
                    Guardar
                  </button>
                </div>
              </form>
            </div>
          </template>
        </Modal>
        <!--MODAL ELIMINACION-->
        <Modal v-if="isDeleteModalOpen" @close="closeDeleteModal">
          <template #body>
            <div class="bg-white dark:bg-gray-900 rounded-xl p-6 max-w-md mx-auto text-center">
              <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">
                ¿Eliminar Servicio?
              </h3>
              <p class="text-gray-600 dark:text-gray-400 mb-6">
                ¿Estás seguro de que deseas eliminar
                <strong>{{ servicioAEliminar?.nombre }}</strong
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
import { ref, onMounted, computed } from 'vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import ComponentCard from '@/components/common/ComponentCard.vue'
import Modal from '@/components/ui/Modal.vue'
import ServiciosTable from '@/components/servicios/ServiciosTable.vue'
import HttpService from '@/services/HttpService'
import { notify } from '@kyvg/vue3-notification'
const currentPageTitle = ref('Tabla de Servicios')
const isModalOpen = ref(false)
const isDeleteModalOpen = ref(false)
const isServicioModal = ref(false)
const servicioAEliminar = ref(null)
const servicioEditando = ref(null)
const servicios = ref([])
const filtro = ref('')
const paginaActual = ref(1)
const porPagina = 10

const serviciosFiltrados = computed(() =>
  servicios.value.filter((servicio) =>
    `${servicio.nombre} ${servicio.precio} ${servicio.categoria}`.toLowerCase().includes(filtro.value.toLowerCase()),
  ),
)

const totalPaginas = computed(() => Math.ceil(serviciosFiltrados.value.length / porPagina))

const serviciosPaginados = computed(() => {
  const inicio = (paginaActual.value - 1) * porPagina
  return serviciosFiltrados.value.slice(inicio, inicio + porPagina)
})

function cambiarPagina(nueva) {
  if (nueva >= 1 && nueva <= totalPaginas.value) {
    paginaActual.value = nueva
  }
}

onMounted(() => {
  cargarServicios()
})

async function cargarServicios() {
  try {
    const data = await HttpService.get('/servicios/obtener.php')
    servicios.value = data
  } catch (error) {
    console.error('Error al cargar servicios:', error)
  }
}

function openModal() {
  servicioEditando.value = { nombre: '', precio: '', duracion: '', status: '' , detalles: ''}
  isModalOpen.value = true
}

function closeModal() {
  isModalOpen.value = false
  servicioEditando.value = null
}

async function guardarServicio() {
  try {
    if (servicioEditando.value.id) {
      const response = await HttpService.post('/servicios/editar.php', servicioEditando.value)

      if (response.success) {
        notify({
          title: 'Éxito',
          text: 'Servicio guardado correctamente',
          type: 'primary',
          duration: 4000,
          speed: 500,
        })
        await cargarServicios()
        closeModal()
      } else {
        notify({
          title: 'Error',
          text: 'No se pudo guardar el servicio',
          type: 'error',
        })
      }
    } else {
      const response = await HttpService.post('/servicios/crear.php', servicioEditando.value)

      if (response.success) {
        await cargarServicios()
        closeModal()
      } else {
        notify({
          title: 'Error',
          text: 'No se pudo guardar el servicio',
          type: 'error',
        })
      }
    }
  } catch (error) {
    console.error('Error al guardar servicio:', error)
    alert('Ocurrió un error al guardar el servicio.')
  }
}

function editarServicio(servicio) {
  servicioEditando.value = { ...servicio }
  isModalOpen.value = true
}
function solicitarEliminacion(servicio) {
  servicioAEliminar.value = servicio
  isDeleteModalOpen.value = true
}

function closeDeleteModal() {
  isDeleteModalOpen.value = false
  servicioAEliminar.value = null
}

async function confirmarEliminacion() {
  try {
    const response = await HttpService.post('/servicios/eliminar.php', {
      id: servicioAEliminar.value.id,
    })

    if (response.success) {
      notify({
        title: 'Eliminado',
        text: 'Servicio desactivado correctamente.',
        type: 'primary',
      })
      await cargarServicios()
      isDeleteModalOpen.value = false
    } else {
      notify({
        title: 'Error',
        text: 'No se pudo desactivar el servicio.',
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

<!--ESTILOS VISTA -->
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
  color: #000000; /* verde oscuro */
}

.vue-notification.error {
  color: #b91c1c; /* rojo fuerte */
}
</style>
