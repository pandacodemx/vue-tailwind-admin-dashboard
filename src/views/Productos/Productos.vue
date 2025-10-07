<template>
  <AdminLayout>
    <PageBreadcrumb :pageTitle="currentPageTitle" />
    <div class="space-y-5 sm:space-y-6">
      <ComponentCard title="Lista de Productos">
        <div class="flex items-center justify-end">
          <Button size="sm" variant="primary" :endIcon="UserCircleIcon" @click="openModal">
            Nuevo Producto
          </Button>
        </div>
        <input v-model="filtro" placeholder="Buscar por nombre o categoria"
          class="w-full max-w-md px-4 py-2 mb-4 border rounded dark:text-white" />
        <ProductosTable :productos="productosPaginados" @editar="editarProduto" @eliminar="solicitarEliminacion" />
        <div class="flex justify-between items-center mt-4">
          <p class="text-sm text-gray-500 dark:text-gray-400 dark:text-white">
            Mostrando {{ productosPaginados.length }} de {{ productosFiltrados.length }} productos
          </p>

          <div class="flex gap-2">
            <Button @click="cambiarPagina(paginaActual - 1)" :disabled="paginaActual === 1" variant="secondary"
              class="dark:text-white">
              <ChevronLeftIcon />
            </Button>
            <span class="text-sm dark:text-white">
              Página {{ paginaActual }} de {{ totalPaginas }}
            </span>
            <Button @click="cambiarPagina(paginaActual + 1)" :disabled="paginaActual === totalPaginas"
              variant="secondary" class="dark:text-white">
              <ChevronRightIcon />
            </Button>
          </div>
        </div>

        <Modal v-if="isModalOpen">
          <template #body>
            <div class="relative w-full max-w-[700px] rounded-3xl bg-white dark:bg-gray-900">

              <div
                class="sticky top-0 bg-white dark:bg-gray-900 px-4 pt-4 lg:px-11 lg:pt-11 pb-4 rounded-t-3xl border-b border-gray-200 dark:border-gray-700 z-50">
                <div class="flex items-center justify-between">
                  <h3 class="text-lg font-medium dark:text-white">
                    {{ productoEditando.id ? 'Editar Producto' : 'Registrar Producto Nuevo' }}
                  </h3>
                  <button @click="isModalOpen = false"
                    class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-100 text-gray-400 hover:bg-gray-200 hover:text-gray-600 dark:bg-gray-700 dark:text-gray-400 dark:hover:bg-white/[0.07] dark:hover:text-gray-300 transition-colors">
                    <svg class="fill-current" width="16" height="16" viewBox="0 0 24 24" fill="none"
                      xmlns="http://www.w3.org/2000/svg">
                      <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M6.04289 16.5418C5.65237 16.9323 5.65237 17.5655 6.04289 17.956C6.43342 18.3465 7.06658 18.3465 7.45711 17.956L11.9987 13.4144L16.5408 17.9565C16.9313 18.347 17.5645 18.347 17.955 17.9565C18.3455 17.566 18.3455 16.9328 17.955 16.5423L13.4129 12.0002L17.955 7.45808C18.3455 7.06756 18.3455 6.43439 17.955 6.04387C17.5645 5.65335 16.9313 5.65335 16.5408 6.04387L11.9987 10.586L7.45711 6.04439C7.06658 5.65386 6.43342 5.65386 6.04289 6.04439C5.65237 6.43491 5.65237 7.06808 6.04289 7.4586L10.5845 12.0002L6.04289 16.5418Z"
                        fill="" />
                    </svg>
                  </button>
                </div>
              </div>


              <div class="max-h-[60vh] overflow-y-auto px-4 py-4 lg:px-11 lg:py-6 custom-scrollbar">
                <form @submit.prevent="guardarProducto" class="space-y-4" enctype="multipart/form-data">

                  <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                      Imagen del Producto
                    </label>
                    <div class="flex items-center space-x-4">
                      <div v-if="imagenPrevia || productoEditando.imagen"
                        class="w-20 h-20 border rounded overflow-hidden">
                        <img :src="imagenPrevia || `${baseUrl}${productoEditando.imagen}`" alt="Vista previa"
                          class="w-full h-full object-cover">
                      </div>
                      <div class="flex-1">
                        <input type="file" ref="fileInput" @change="manejarSeleccionArchivo" accept="image/*"
                          class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900 dark:file:text-blue-300" />
                        <p class="text-xs text-gray-500 mt-1">Formatos: JPG, PNG, GIF. Máx: 2MB</p>
                      </div>
                    </div>
                  </div>

                  <input v-model="productoEditando.nombre" placeholder="Nombre" required
                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />

                  <input v-model="productoEditando.precio" placeholder="Precio" type="number" step="0.01" required
                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />

                  <textarea v-model="productoEditando.descripcion" placeholder="Detalles del producto" rows="3"
                    class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800 resize-none" />

                  <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                      Categoria
                    </label>
                    <select v-model="productoEditando.id_categoria"
                      class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800">
                      <option v-for="cat in categorias" :key="cat.id" :value="cat.id">
                        {{ cat.nombre }}
                      </option>
                    </select>
                  </div>

                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                      <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Stock
                      </label>
                      <input v-model="productoEditando.stock" placeholder="Stock" type="number" required
                        class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
                    </div>
                    <div>
                      <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Stock Mínimo
                      </label>
                      <input v-model="productoEditando.stock_minimo" placeholder="Stock Mínimo" type="number" required
                        class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
                    </div>
                  </div>

                  <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                      Estado
                    </label>
                    <select v-model="productoEditando.activo"
                      class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800">
                      <option value="1" class="dark:text-white">Activo</option>
                      <option value="0" class="dark:text-white">Inactivo</option>
                    </select>
                  </div>
                </form>
              </div>

              <!-- Footer fijo -->
              <div
                class="sticky bottom-0 bg-white dark:bg-gray-900 px-4 py-4 lg:px-11 lg:py-6 rounded-b-3xl border-t border-gray-200 dark:border-gray-700">
                <div class="flex justify-end space-x-2">
                  <Button variant="secondary" @click="closeModal"
                    class="dark:text-white border-2 border-gray-300 ease-in-out hover:scale-105"
                    type="button">Cancelar</Button>
                  <Button type="submit" variant="primary" class="ease-in-out hover:scale-105"
                    @click="guardarProducto">Guardar</Button>
                </div>
              </div>
            </div>
          </template>
        </Modal>

        <!--////////////////////////////MODAL ELIMINAR //////////////////////////-->
        <Modal v-if="isDeleteModalOpen" @close="closeDeleteModal" class="">
          <template #body>
            <div class="bg-white dark:bg-gray-900 rounded-xl p-6 max-w-md mx-auto text-center">
              <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">
                ¿Eliminar producto?
              </h3>
              <p class="text-gray-600 dark:text-gray-400 mb-6">
                ¿Estás seguro de que deseas eliminar a
                <strong>{{ productoAEliminar?.nombre }}</strong>? Esta acción no se puede deshacer.
              </p>
              <div class="flex justify-center gap-4">
                <Button variant="secondary" class="dark:text-white border-2 border-gray-300 ease-in-out hover:scale-105"
                  @click="closeDeleteModal">Cancelar</Button>
                <Button variant="destructive" class="text-white  bg-red-500 ease-in-out hover:scale-105"
                  @click="confirmarEliminacion">Eliminar</Button>
              </div>
            </div>
          </template>
        </Modal>
      </ComponentCard>
    </div>
  </AdminLayout>
</template>

<script setup>
import { onMounted, ref, computed, nextTick } from 'vue'
import { UserCircleIcon, ChevronRightIcon, ChevronLeftIcon } from '@/icons'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import ComponentCard from '@/components/common/ComponentCard.vue'
import ProductosTable from '@/components/productos/ProductosTable.vue'
import Button from '@/components/ui/Button.vue'
import Modal from '@/components/ui/Modal.vue'
import HttpService from '@/services/HttpService'
import { notify } from '@kyvg/vue3-notification'

const currentPageTitle = ref('Venta de Productos')
const isModalOpen = ref(false)
const isDeleteModalOpen = ref(false)
const productoAEliminar = ref(null)
const productoEditando = ref(null)
const productos = ref([])
const categorias = ref([])
const filtro = ref('')
const paginaActual = ref(1)
const porPagina = 10
const fileInput = ref(null)
const imagenPrevia = ref(null)
const archivoSeleccionado = ref(null)
const baseUrl = 'http://localhost/vue-tailwind-admin-dashboard/'

const productosFiltrados = computed(() =>
  productos.value.filter((producto) =>
    `${producto.nombre} ${producto.categoria}`.toLowerCase().includes(filtro.value.toLowerCase()),
  ),
)

const totalPaginas = computed(() => Math.ceil(productosFiltrados.value.length / porPagina))

const productosPaginados = computed(() => {
  const inicio = (paginaActual.value - 1) * porPagina
  return productosFiltrados.value.slice(inicio, inicio + porPagina)
})

function cambiarPagina(nueva) {
  if (nueva >= 1 && nueva <= totalPaginas.value) {
    paginaActual.value = nueva
  }
}

const productosNotificados = ref(new Set())

onMounted(() => {
  cargarProductos()
  cargarCategorias()
})

async function cargarProductos() {
  try {
    const data = await HttpService.get('/productos/obtener.php')
    productos.value = data
  } catch (error) {
    console.error('Error al cargar productos:', error)
  }
}

async function cargarCategorias() {
  try {
    const data = await HttpService.get('/categorias/obtener.php')
    categorias.value = data
  } catch (error) {
    console.error('Error al cargar categorías:', error)
  }
}

function manejarSeleccionArchivo(event) {
  const archivo = event.target.files[0]
  if (archivo) {

    if (!archivo.type.startsWith('image/')) {
      notify({
        title: 'Error',
        text: 'Por favor, selecciona un archivo de imagen válido.',
        type: 'error',
      })
      return
    }

    // Validar tamaño (2MB máximo)
    if (archivo.size > 2 * 1024 * 1024) {
      notify({
        title: 'Error',
        text: 'La imagen no debe superar los 2MB.',
        type: 'error',
      })
      return
    }

    archivoSeleccionado.value = archivo


    const reader = new FileReader()
    reader.onload = (e) => {
      imagenPrevia.value = e.target.result
    }
    reader.readAsDataURL(archivo)
  }
}

function openModal() {
  productoEditando.value = {
    id: null,
    nombre: '',
    precio: '',
    descripcion: '',
    id_categoria: null,
    stock: '',
    stock_minimo: '',
    activo: 1,
    imagen: ''
  }
  imagenPrevia.value = null
  archivoSeleccionado.value = null
  isModalOpen.value = true


  nextTick(() => {
    if (fileInput.value) {
      fileInput.value.value = ''
    }
  })
}

function closeModal() {
  isModalOpen.value = false
  productoEditando.value = null
  imagenPrevia.value = null
  archivoSeleccionado.value = null
}

async function guardarProducto() {
  try {
    const formData = new FormData()

    Object.keys(productoEditando.value).forEach(key => {
      if (productoEditando.value[key] !== null) {
        formData.append(key, productoEditando.value[key])
      }
    })


    if (archivoSeleccionado.value) {
      formData.append('imagen_archivo', archivoSeleccionado.value)
    }

    let response
    if (productoEditando.value.id) {

      response = await HttpService.post('/productos/editar.php', formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })
    } else {

      response = await HttpService.post('/productos/crear.php', formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })
    }

    if (response.success) {
      notify({
        title: 'Éxito',
        text: 'Producto guardado correctamente',
        type: 'primary',
        duration: 4000,
        speed: 500,
      })
      productosNotificados.value.delete(productoEditando.value.id)
      await cargarProductos()
      closeModal()
    } else {
      notify({
        title: 'Error',
        text: response.message || 'No se pudo guardar el producto',
        type: 'error',
      })
    }
  } catch (error) {
    console.error('Error al guardar producto:', error)
    notify({
      title: 'Error',
      text: 'Ocurrió un error al guardar el producto.',
      type: 'error',
    })
  }
}

function editarProduto(producto) {
  productoEditando.value = { ...producto }
  imagenPrevia.value = producto.imagen ? `${baseUrl}${producto.imagen}` : null
  archivoSeleccionado.value = null
  isModalOpen.value = true

  nextTick(() => {
    if (fileInput.value) {
      fileInput.value.value = ''
    }
  })
}

function solicitarEliminacion(cliente) {
  productoAEliminar.value = cliente
  isDeleteModalOpen.value = true
}

function closeDeleteModal() {
  isDeleteModalOpen.value = false
  productoAEliminar.value = null
}

async function confirmarEliminacion() {
  try {
    const response = await HttpService.post('/productos/eliminar.php', {
      id: productoAEliminar.value.id,
    })

    if (response.success) {
      notify({
        title: 'Eliminado',
        text: 'Producto desactivado correctamente.',
        type: 'primary',
      })
      await cargarProductos()
      isDeleteModalOpen.value = false
    } else {
      notify({
        title: 'Error',
        text: 'No se pudo desactivar el producto.',
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

.modal>div:first-child {
  backdrop-filter: blur(32px);
  -webkit-backdrop-filter: blur(32px);
}

.vue-notification.primary {
  color: #000000;
}

.vue-notification.error {
  color: #b91c1c;
  /* rojo fuerte */
}
</style>
