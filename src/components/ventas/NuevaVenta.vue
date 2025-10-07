<template>
  <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Registrar Nueva Venta</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Agrega productos y completa la transacción</p>
      </div>
      <div class="text-right">
        <div class="text-2xl font-bold text-green-600">${{ totalVenta.toFixed(2) }}</div>
        <div class="text-sm text-gray-500 dark:text-gray-400">Total de la venta</div>
      </div>
    </div>

    <!-- Selector de producto -->
    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 mb-6">
      <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Agregar Productos</h3>

      <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-end">
        <div class="lg:col-span-6">
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Buscar Producto
          </label>
          <Multiselect 
            v-model="productoSeleccionado" 
            :options="productos"
            :custom-label="(c) => `${c.nombre} - $${c.precio} (Stock: ${c.stock})`"
            placeholder="Selecciona o busca un producto..." 
            track-by="id" 
            :searchable="true" 
            :sort-by="['nombre']"
            :sort-direction="'asc'" 
            class="multiselect-custom"
          >
            <template #option="{ option }">
              <div class="flex items-center gap-3 py-2">
                <div class="flex-shrink-0 w-10 h-10 rounded-lg overflow-hidden bg-gray-200 dark:bg-gray-600">
                  <img 
                    v-if="option.imagen" 
                    :src="getImageUrl(option.imagen)" 
                    :alt="option.nombre"
                    class="w-full h-full object-cover"
                  >
                  <div v-else class="w-full h-full flex items-center justify-center text-gray-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                  </div>
                </div>
                <div class="flex-1 min-w-0">
                  <div class="text-sm font-medium text-gray-900 dark:text-white truncate">
                    {{ option.nombre }}
                  </div>
                  <div class="text-sm text-gray-500 dark:text-gray-400">
                    ${{ option.precio }} • Stock: {{ option.stock }}
                  </div>
                </div>
              </div>
            </template>
          </Multiselect>
        </div>

        <div class="lg:col-span-3">
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Cantidad
          </label>
          <div class="flex items-center">
            <button @click="decrementarCantidad" :disabled="cantidad <= 1"
              class="w-10 h-10 flex items-center justify-center border border-r-0 rounded-l-lg bg-gray-100 dark:bg-gray-600 text-gray-600 dark:text-gray-300 disabled:opacity-50 disabled:cursor-not-allowed">
              −
            </button>
            <input type="number" min="1" v-model.number="cantidad"
              class="w-20 h-10 text-center border-y border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-amber-500 focus:border-transparent" />
            <button @click="incrementarCantidad"
              class="w-10 h-10 flex items-center justify-center border border-l-0 rounded-r-lg bg-gray-100 dark:bg-gray-600 text-gray-600 dark:text-gray-300">
              +
            </button>
          </div>
        </div>

        <div class="lg:col-span-3">
          <button @click="agregarProducto" :disabled="!productoSeleccionado || cantidad <= 0"
            class="w-full h-10 bg-gradient-to-r from-amber-600 to-amber-700 hover:from-amber-700 hover:to-amber-800 text-white font-medium rounded-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Agregar
          </button>
        </div>
      </div>

      <!-- Info del producto seleccionado -->
      <div v-if="productoSeleccionado"
        class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
        <div class="flex items-center gap-3">
          <div class="flex-shrink-0 w-12 h-12 rounded-lg overflow-hidden bg-white dark:bg-gray-600 border border-gray-200 dark:border-gray-600">
            <img 
              v-if="productoSeleccionado.imagen" 
              :src="getImageUrl(productoSeleccionado.imagen)" 
              :alt="productoSeleccionado.nombre"
              class="w-full h-full object-cover"
            >
            <div v-else class="w-full h-full flex items-center justify-center text-gray-400">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
            </div>
          </div>
          <div class="flex-1">
            <div class="flex justify-between items-center">
              <span class="text-sm font-medium text-blue-800 dark:text-blue-300">
                {{ productoSeleccionado.nombre }}
              </span>
              <span class="text-sm text-blue-600 dark:text-blue-400">
                Stock: {{ productoSeleccionado.stock }} | Precio: ${{ productoSeleccionado.precio }}
              </span>
            </div>
            <div class="text-xs text-blue-600 dark:text-blue-400 mt-1">
              Subtotal: ${{ (productoSeleccionado.precio * cantidad).toFixed(2) }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Tabla de productos agregados -->
    <div class="mb-6">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
          Productos en la Venta
        </h3>
        <span class="text-sm text-gray-500 dark:text-gray-400">
          {{ venta.productos.length }} producto(s) agregado(s)
        </span>
      </div>

      <div v-if="venta.productos.length === 0"
        class="text-center py-8 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg">
        <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
        </svg>
        <p class="text-gray-500 dark:text-gray-400">No hay productos en la venta</p>
        <p class="text-sm text-gray-400 dark:text-gray-500">Agrega productos usando el formulario superior</p>
      </div>

      <div v-else class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <thead class="bg-gray-50 dark:bg-gray-700/50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                Producto
              </th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                Precio Unit.
              </th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                Cantidad
              </th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                Subtotal
              </th>
              <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                Acciones
              </th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            <tr v-for="(item, index) in venta.productos" :key="index"
              class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center gap-3">
                  <div class="flex-shrink-0 w-10 h-10 rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-600 border border-gray-200 dark:border-gray-600">
                    <img 
                      v-if="item.imagen" 
                      :src="getImageUrl(item.imagen)" 
                      :alt="item.nombre"
                      class="w-full h-full object-cover"
                    >
                    <div v-else class="w-full h-full flex items-center justify-center text-gray-400">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                      </svg>
                    </div>
                  </div>
                  <div class="text-sm font-medium text-gray-900 dark:text-white">{{ item.nombre }}</div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900 dark:text-white">
                ${{ item.precio.toFixed(2) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900 dark:text-white">
                {{ item.cantidad }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold text-green-600">
                ${{ (item.precio * item.cantidad).toFixed(2) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-center">
                <button @click="quitarProducto(index)"
                  class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition-colors p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20"
                  title="Eliminar producto">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                </button>
              </td>
            </tr>
          </tbody>
          <tfoot class="bg-gray-50 dark:bg-gray-700/50">
            <tr>
              <td colspan="3" class="px-6 py-4 text-right text-sm font-medium text-gray-900 dark:text-white">
                Total:
              </td>
              <td class="px-6 py-4 text-right text-lg font-bold text-green-600">
                ${{ totalVenta.toFixed(2) }}
              </td>
              <td></td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>

    <!-- Método de pago y botones -->
    <div
      class="flex flex-col sm:flex-row justify-between items-center gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
      <div class="flex items-center gap-4">
        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Método de pago:</label>
        <select v-model="metodoPago"
          class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 focus:outline-hidden focus:ring-2 focus:ring-amber-500">
          <option value="efectivo">Efectivo</option>
          <option value="tarjeta">Tarjeta</option>
          <option value="transferencia">Transferencia</option>
        </select>
      </div>

      <div class="flex gap-3">
        <button @click="limpiarVenta" :disabled="venta.productos.length === 0 || guardandoVenta"
          class="px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
          Limpiar
        </button>
        
        <!-- Botón Finalizar Venta con Loading -->
        <button 
          @click="guardarVenta" 
          :disabled="venta.productos.length === 0 || guardandoVenta"
          class="px-8 py-3 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-medium rounded-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2 shadow-lg shadow-green-500/25 relative overflow-hidden"
        >
          <!-- Spinner de loading -->
          <div v-if="guardandoVenta" class="absolute inset-0 flex items-center justify-center bg-green-600">
            <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-white"></div>
          </div>
          
          <!-- Contenido del botón -->
          <div class="flex items-center gap-2" :class="{ 'opacity-0': guardandoVenta }">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ guardandoVenta ? 'Procesando...' : 'Finalizar Venta' }}
          </div>
        </button>
      </div>
    </div>

    <!-- Overlay de loading -->
    <div v-if="guardandoVenta" class="fixed inset-0 bg-gray-700/25 bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white dark:bg-gray-800 rounded-xl p-8 max-w-sm w-full mx-4 shadow-2xl">
        <div class="text-center">
          <!-- Spinner animado -->
          <div class="flex justify-center mb-4">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600"></div>
          </div>
          
          <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">
            Procesando Venta
          </h3>
          <p class="text-gray-600 dark:text-gray-400 text-sm">
            Guardando información y generando ticket...
          </p>
          
          <!-- Progress bar -->
          <div class="mt-4 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
            <div class="bg-green-600 h-2 rounded-full animate-pulse"></div>
          </div>
          
          <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
            Por favor espere
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import HttpService from '@/services/HttpService'
import Multiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.min.css'
import { notify } from '@kyvg/vue3-notification'

const BASE_URL = 'http://localhost/vue-tailwind-admin-dashboard'

const productos = ref([])
const productoSeleccionado = ref(null)
const cantidad = ref(1)
const metodoPago = ref('efectivo')
const guardandoVenta = ref(false) // Estado de loading

const venta = ref({
  productos: [],
})

const getImageUrl = (imagePath) => {
  if (!imagePath) return null
  if (imagePath.startsWith('http')) return imagePath
  return `${BASE_URL}/${imagePath.replace(/^\//, '')}`
}

const totalVenta = computed(() =>
  venta.value.productos.reduce((acc, p) => acc + p.precio * p.cantidad, 0),
)

function incrementarCantidad() {
  cantidad.value++
}

function decrementarCantidad() {
  if (cantidad.value > 1) {
    cantidad.value--
  }
}

function agregarProducto() {
  if (!productoSeleccionado.value || cantidad.value <= 0) return

  if (productoSeleccionado.value.stock <= 0) {
    notify({
      title: 'Sin stock',
      text: `⛔ El producto "${productoSeleccionado.value.nombre}" no tiene stock disponible.`,
      type: 'warn',
    })
    return
  }

  if (cantidad.value > productoSeleccionado.value.stock) {
    notify({
      title: 'Stock insuficiente',
      text: `Solo hay ${productoSeleccionado.value.stock} unidades disponibles de "${productoSeleccionado.value.nombre}".`,
      type: 'warn',
    })
    return
  }

  // Verificar si el producto ya está en la venta
  const productoExistente = venta.value.productos.find(p => p.id === productoSeleccionado.value.id)
  if (productoExistente) {
    productoExistente.cantidad += cantidad.value
  } else {
    venta.value.productos.push({
      id: productoSeleccionado.value.id,
      nombre: productoSeleccionado.value.nombre,
      precio: Number(productoSeleccionado.value.precio),
      cantidad: cantidad.value,
      imagen: productoSeleccionado.value.imagen
    })
  }

  productoSeleccionado.value = null
  cantidad.value = 1
}

function quitarProducto(index) {
  venta.value.productos.splice(index, 1)
}

function limpiarVenta() {
  venta.value.productos = []
  productoSeleccionado.value = null
  cantidad.value = 1
}

async function guardarVenta() {
  if (venta.value.productos.length === 0 || guardandoVenta.value) return

  guardandoVenta.value = true

  const payload = {
    productos: venta.value.productos,
    total: totalVenta.value,
    metodo_pago: metodoPago.value,
  }

  try {
    // Simular un pequeño delay para que se vea el loading
    await new Promise(resolve => setTimeout(resolve, 500))

    const res = await HttpService.post('/ventas/crear.php', payload)

    if (res.success) {
      const ventaData = {
        id: res.venta_id,
        fecha: new Date(),
        metodo_pago: payload.metodo_pago,
        total: payload.total,
        productos: venta.value.productos,
      }

      // Simular tiempo de generación del ticket
      await new Promise(resolve => setTimeout(resolve, 1000))
      
      imprimirTicket(ventaData)
      notify({
        title: 'Venta registrada',
        text: '✅ Venta registrada con éxito',
        type: 'success',
      })
      limpiarVenta()
    } else {
      throw new Error(res.message || 'Error al guardar la venta')
    }
  } catch (error) {
    console.error('Error:', error)
    notify({
      title: 'Error',
      text: 'No se pudo guardar la venta.',
      type: 'error',
    })
  } finally {
    guardandoVenta.value = false
  }
}

onMounted(async () => {
  try {
    const res = await HttpService.get('/productos/obtener.php')
    productos.value = res
  } catch (error) {
    console.error('Error al cargar productos:', error)
    notify({
      title: 'Error',
      text: 'No se pudieron cargar los productos',
      type: 'error',
    })
  }
})

function imprimirTicket(venta) {
  const ventana = window.open('', '_blank', 'width=300,height=600');

  const estilo = `
    <style>
      body { font-family: 'Courier New', monospace; font-size: 12px; padding: 15px; line-height: 1.3; }
      h2, p, .center { text-align: center; margin: 0; }
      table { width: 100%; border-collapse: collapse; margin: 10px 0; }
      td, th { padding: 3px 0; }
      .total, .gracias { font-weight: bold; text-align: center; margin: 8px 0; }
      .linea { text-align: center; margin: 8px 0; border-top: 1px dashed #000; }
      .left { text-align: left; }
      .right { text-align: right; }
      .bold { font-weight: bold; }
    </style>
  `;

  let html = `
    ${estilo}
    <h2 class="bold">BARBERÍA EL CORTE</h2>
    <p>Ticket #${venta.id}</p>
    <p>${new Date(venta.fecha).toLocaleString()}</p>   
    <p class="linea">------------------------------</p>
    <table>
      <thead>
        <tr>
          <th class="left">Producto</th>
          <th class="right">Cant</th>
          <th class="right">Total</th>
        </tr>
      </thead>
      <tbody>
  `;

  venta.productos.forEach((p) => {
    html += `
      <tr>
        <td class="left">${p.nombre}</td>
        <td class="right">${p.cantidad}</td>
        <td class="right">$${(p.precio * p.cantidad).toFixed(2)}</td>
      </tr>
    `;
  });

  html += `
      </tbody>
    </table>
    <p class="linea">------------------------------</p>
    <p class="total">TOTAL: $${Number(venta.total).toFixed(2)}</p>
    <p class="center">Pago: ${venta.metodo_pago.toUpperCase()}</p>
    <p class="linea">------------------------------</p>
    <p class="gracias">¡Gracias por su preferencia!</p>
    <p class="center">Vuelva pronto</p>
  `;

  ventana.document.write(`<html><body>${html}</body></html>`);
  ventana.document.close();
  ventana.focus();
  ventana.print();
  ventana.close();
}
</script>

<style scoped>

/* Animación para el spinner */
@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}
.animate-spin {
  animation: spin 1s linear infinite;
}
</style>