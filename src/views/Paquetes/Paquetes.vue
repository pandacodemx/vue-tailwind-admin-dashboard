<template>
  <AdminLayout>
    <PageBreadcrumb :pageTitle="currentPageTitle" />
    <div class="space-y-5 sm:space-y-6">
      <ComponentCard title="Lista de Paquetes">
        <div class="flex items-center justify-end">
          <button
            @click="abrirPaqueteModal"
            class="edit-button bg-green-500 hover:bg-green-600 text-white"
          >
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
            Nuevo Paquete
          </button>
        </div>
        <!--TABLA PAQUETES-->
        <input
          v-model="filtro"
          placeholder="Buscar por nombre, categoria, precio"
          class="w-full max-w-md px-4 py-2 mb-4 border rounded dark:text-white"
        />
        <PaquetesTable
          :paquetes="paquetesPaginados"
          @eliminar="solicitarEliminacion"
          @editar="editarPaquete"
        />
        <div class="flex justify-between items-center mt-4">
          <p class="text-sm text-gray-500 dark:text-gray-400 dark:text-white">
            Mostrando {{ paquetesPaginados.length }} de {{ paquetesFiltrados.length }} servicios
          </p>

          <div class="flex gap-2">
            <Button
              @click="cambiarPagina(paginaActual - 1)"
              :disabled="paginaActual === 1"
              variant="secondary"
              class="dark:text-white"
              ><ChevronLeftIcon
            /></Button>
            <span class="text-sm dark:text-white">
              Página {{ paginaActual }} de {{ totalPaginas }}
            </span>
            <Button
              @click="cambiarPagina(paginaActual + 1)"
              :disabled="paginaActual === totalPaginas"
              variant="secondary"
              class="dark:text-white"
              ><ChevronRightIcon
            /></Button>
          </div>
        </div>

        <!--MODAL ELIMINACION-->
        <Modal v-if="isDeleteModalOpen" @close="closeDeleteModal">
          <template #body>
            <div class="bg-white dark:bg-gray-900 rounded-xl p-6 max-w-md mx-auto text-center">
              <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">
                ¿Eliminar Paquete?
              </h3>
              <p class="text-gray-600 dark:text-gray-400 mb-6">
                ¿Estás seguro de que deseas eliminar
                <strong>{{ paqueteAEliminar?.nombre }}</strong
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

        <Modal v-if="isPaqueteModalOpen">
          <template #body>
            <div
              class="no-scrollbar relative w-full max-w-[700px] overflow-y-auto rounded-3xl bg-white p-4 dark:bg-gray-900 lg:p-11"
            >
              <button
                @click="closePaqueteModal"
                class="absolute right-5 top-5 z-999 flex h-11 w-11 items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-white/[0.07]"
              >
                ✕
              </button>

              <h4 class="mb-2 text-2xl font-semibold text-gray-800 dark:text-white/90">
                Crear Paquete de Servicios
              </h4>
              <p class="mb-6 text-sm text-gray-500 dark:text-gray-400 lg:mb-7">
                Selecciona los servicios que formarán parte del paquete.
              </p>

              <div class="mb-4 flex gap-4 items-end">
                <Multiselect
                  v-model="servicioSeleccionado"
                  :options="servicios"
                  :custom-label="(s) => `${s.nombre} - $${s.precio}`"
                  placeholder="--Selecciona servicios--"
                  track-by="id"
                  :searchable="true"
                  :sort-by="['nombre']"
                  :sort-direction="'asc'"
                  :multiple="true"
                />

                <button @click="agregarAlPaquete" class="text-white px-4 py-2 rounded bg-green-600">
                  Agregar
                </button>
              </div>

              <div v-if="paquete.length" class="mb-4">
                <h5 class="mb-2 font-medium text-gray-800 dark:text-white/90">
                  Servicios en el paquete:
                </h5>
                <ul class="space-y-2">
                  <li
                    v-for="(s, index) in paquete"
                    :key="s.id"
                    class="flex justify-between items-center p-2 border rounded dark:border-gray-700"
                  >
                    <span>{{ s.nombre }} - ${{ s.precio }}</span>
                    <button
                      @click="quitarDelPaquete(index)"
                      class="text-red-600 hover:text-red-800"
                    >
                      Eliminar
                    </button>
                  </li>
                </ul>
              </div>

              <div class="mt-4 text-right font-semibold">
                Total por paquete: ${{ precioPaquete.toFixed(2) }}
                <span v-if="descuento.value">(-{{ descuento.value * 100 }}%)</span>
              </div>

              <div class="mt-4 text-right font-semibold text-gray-800 dark:text-white/90">
                Total Base: ${{ totalPaquete }}
              </div>

              <div class="mt-4 text-left font-semibold text-gray-800 dark:text-white/90">
                Duración: {{ totalDuracion }}
              </div>

              <div class="mt-4">
                <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-400"
                  >Nombre del paquete:</label
                >
                <input
                  v-model="nombrePaquete"
                  type="text"
                  placeholder="Paquete Barbería Completo"
                  class="w-full rounded-lg border px-4 py-2 dark:bg-gray-900 dark:text-white/90 dark:border-gray-700"
                />
              </div>

              <div class="flex items-center gap-3 px-2 mt-6 lg:justify-end">
                <button
                  @click="closePaqueteModal"
                  type="button"
                  class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 sm:w-auto dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]"
                >
                  Cerrar
                </button>
                <button
                  @click="guardarPaquete"
                  type="button"
                  class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto"
                >
                  Guardar Paquete
                </button>
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
import PaquetesTable from '@/components/paquetes/PaquetesTable.vue'
import HttpService from '@/services/HttpService'
import { notify } from '@kyvg/vue3-notification'
import ChevronLeftIcon from '@/icons/ChevronLeftIcon.vue'
import ChevronRightIcon from '@/icons/ChevronRightIcon.vue'
const currentPageTitle = ref('Paquetes con servicios')
import Multiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.min.css'
const isDeleteModalOpen = ref(false)
const paqueteAEliminar = ref(null)
const paqueteEditando = ref(null)
const servicios = ref([])
const paquetes = ref([])
const filtro = ref('')
const paginaActual = ref(1)
const porPagina = 10
const isPaqueteModalOpen = ref(false)
const servicioSeleccionado = ref([])
const paquete = ref([])
const nombrePaquete = ref('')

const paquetesFiltrados = computed(() =>
  paquetes.value.filter((paquete) =>
    `${paquete.nombre} ${paquete.precio}`.toLowerCase().includes(filtro.value.toLowerCase()),
  ),
)

const totalPaginas = computed(() => Math.ceil(paquetesFiltrados.value.length / porPagina))

const paquetesPaginados = computed(() => {
  const inicio = (paginaActual.value - 1) * porPagina
  return paquetesFiltrados.value.slice(inicio, inicio + porPagina)
})

function abrirPaqueteModal() {
  isPaqueteModalOpen.value = true
  paquete.value = []
  servicioSeleccionado.value = []
  nombrePaquete.value = ''
}

function closePaqueteModal() {
  isPaqueteModalOpen.value = false
}
function agregarAlPaquete() {
  servicioSeleccionado.value.forEach((s) => {
    if (!paquete.value.some((ps) => ps.id === s.id)) {
      paquete.value.push(s)
    }
  })
  servicioSeleccionado.value = []
}
function quitarDelPaquete(index) {
  paquete.value.splice(index, 1)
}

const totalPaquete = computed(() => paquete.value.reduce((acc, s) => acc + Number(s.precio), 0))
const totalDuracion = computed(() =>
  paquete.value.reduce((sum, s) => sum + Number(s.duracion || 0), 0),
)
const descuento = computed(() => {
  const count = paquete.value.length
  if (count >= 4) return 0.15
  if (count === 3) return 0.1
  if (count === 2) return 0.05
  return 0
})

const precioPaquete = computed(() => totalPaquete.value * (1 - descuento.value))

async function guardarPaquete() {
  if (!nombrePaquete.value || !paquete.value.length) {
    notify({
      title: 'Error',
      text: 'Completa el nombre y agrega al menos un servicio',
      type: 'error',
    })
    return
  }

  try {
    const payload = {
      nombre: nombrePaquete.value,
      precio: precioPaquete.value,
      duracion: totalDuracion.value,
      status: 1,
      servicios: paquete.value.map(s => s.id),
    }
    if (paqueteEditando.value && paqueteEditando.value.id) {
      payload.id = paqueteEditando.value.id
    }

    const url = paqueteEditando.value?.id
      ? '/paquetes/editar.php'
      : '/paquetes/crear.php'

    const response = await HttpService.post(url, payload)

    if (response.success) {
      notify({
        title: 'Éxito',
        text: paqueteEditando.value?.id
          ? 'Paquete actualizado correctamente'
          : 'Paquete creado correctamente',
        type: 'success',
      })
      closePaqueteModal()
      await cargarPaquetes()
      paqueteEditando.value = null
    } else {
      notify({ title: 'Error', text: 'No se pudo guardar el paquete', type: 'error' })
    }
  } catch (error) {
    console.error('Error al guardar paquete:', error)
  }
}

function cambiarPagina(nueva) {
  if (nueva >= 1 && nueva <= totalPaginas.value) {
    paginaActual.value = nueva
  }
}

onMounted(() => {
  cargarServicios()
  cargarPaquetes()
})

async function cargarServicios() {
  try {
    const data = await HttpService.get('/servicios/obtener.php')
    servicios.value = data
  } catch (error) {
    console.error('Error al cargar servicios:', error)
  }
}

async function cargarPaquetes() {
  try {
    const data = await HttpService.get('/paquetes/obtener.php')
    paquetes.value = data
  } catch (error) {
    console.error('Error al cargar servicios:', error)
  }
}

async function editarPaquete(paqueteItem) {
  try {
    paqueteEditando.value = { ...paqueteItem }
    nombrePaquete.value = paqueteItem.nombre

    const response = await HttpService.get(`/paquetes/obtener_paquete.php?id=${paqueteItem.id}`)

    if (response.success) {
     
      const ids = response.data.servicios_ids || []
      
      paquete.value = servicios.value.filter(s => ids.includes(String(s.id)))
    } else {
      paquete.value = []
    }

    isPaqueteModalOpen.value = true
  } catch (error) {
    console.error('Error cargando servicio del paquete', error)
    paquete.value = []
  }
}

function solicitarEliminacion(servicio) {
  paqueteAEliminar.value = servicio
  isDeleteModalOpen.value = true
}

function closeDeleteModal() {
  isDeleteModalOpen.value = false
  paqueteAEliminar.value = null
}

async function confirmarEliminacion() {
  try {
    const response = await HttpService.post('/paquetes/eliminar.php', {
      id: paqueteAEliminar.value.id,
    })

    if (response.success) {
      notify({
        title: 'Eliminado',
        text: 'Paquete desactivado correctamente.',
        type: 'primary',
      })
      await cargarPaquetes()
      isDeleteModalOpen.value = false
    } else {
      notify({
        title: 'Error',
        text: 'No se pudo desactivar el paquete.',
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
  color: #000000; 
}

.vue-notification.error {
  color: #b91c1c; 
}
.vue-notification.success {
  color: #000000; 
}
</style>
