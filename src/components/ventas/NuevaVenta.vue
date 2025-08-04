<!-- src/components/ventas/NuevaVenta.vue -->
<template>
  <div class="p-6 bg-white rounded shadow dark:bg-dark-900">
    <h2 class="text-xl font-bold mb-4 text-gray-800 dark:text-white">Registrar Nueva Venta</h2>

    <!-- Selector de producto -->
    <div class="flex gap-4 mb-4">
      <Multiselect
        v-model="productoSeleccionado"
        :options="productos"
        :custom-label="(c) => `${c.nombre} - $${c.precio}`"
        placeholder="--Selecciona un producto--"
        track-by="id"
        :searchable="true"
        :sort-by="['nombre']"
        :sort-direction="'asc'"
      />

      <input type="number" min="1" v-model.number="cantidad" class="w-24 p-2 rounded border" />
      <button @click="agregarProducto" class="bg-blue-600 text-white px-4 py-2 rounded">
        Agregar
      </button>
    </div>

    <!-- Tabla de productos agregados -->
    <table class="w-full text-sm mb-4 border dark:border-gray-700">
      <thead class="bg-gray-100 dark:bg-dark-800">
        <tr>
          <th class="p-2">Producto</th>
          <th class="p-2">Precio</th>
          <th class="p-2">Cantidad</th>
          <th class="p-2">Subtotal</th>
          <th class="p-2">Acción</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(item, index) in venta.productos" :key="index">
          <td class="p-2">{{ item.nombre }}</td>
          <td class="p-2">${{ item.precio }}</td>
          <td class="p-2">{{ item.cantidad }}</td>
          <td class="p-2">${{ item.precio * item.cantidad }}</td>
          <td class="p-2">
            <button @click="quitarProducto(index)" class="text-red-600">🗑️Eliminar</button>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Total y botón guardar -->
    <div class="text-right mb-4 text-lg font-semibold text-gray-800 dark:text-white">
      Total: ${{ totalVenta }}
    </div>
    <button @click="guardarVenta" class="bg-green-600 text-white px-6 py-2 rounded">
      Guardar Venta
    </button>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import HttpService from '@/services/HttpService'
import Multiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.min.css'
import { notify } from '@kyvg/vue3-notification'

const productos = ref([])
const productoSeleccionado = ref('')
const cantidad = ref(1)

const venta = ref({
  productos: [],
})

const totalVenta = computed(() =>
  venta.value.productos.reduce((acc, p) => acc + p.precio * p.cantidad, 0),
)

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

  venta.value.productos.push({
    id: productoSeleccionado.value.id,
    nombre: productoSeleccionado.value.nombre,
    precio: Number(productoSeleccionado.value.precio),
    cantidad: cantidad.value,
  })

  productoSeleccionado.value = ''
  cantidad.value = 1
}

function quitarProducto(index) {
  venta.value.productos.splice(index, 1)
}

async function guardarVenta() {
  if (venta.value.productos.length === 0) return

  const payload = {
    productos: venta.value.productos,
    total: totalVenta.value,
    metodo_pago: 'efectivo',
  }

  const res = await HttpService.post('/ventas/crear.php', payload)

  if (res.success) {
    const ventaData = {
      id: res.venta_id,
      fecha: new Date(),    
      metodo_pago: payload.metodo_pago,
      total: payload.total,
      productos: venta.value.productos,
    }

    imprimirTicket(ventaData)
    notify({
      title: 'Venta registrada',
      text: '✅ Venta registrada con éxito',
      type: 'success',
    })
    venta.value.productos = []
  } else {
    notify({
      title: 'Error',
      text: 'No se pudo guardar la venta.',
      type: 'error',
    })
  }
}

onMounted(async () => {
  const res = await HttpService.get('/productos/obtener.php')
  productos.value = res
})

function imprimirTicket(venta) {
  const ventana = window.open('', '_blank', 'width=300,height=600');

  const estilo = `
    <style>
      body { font-family: monospace; font-size: 12px; padding: 10px; }
      h2, p, .center { text-align: center; margin: 0; }
      table { width: 100%; border-collapse: collapse; margin-top: 10px; }
      td, th { padding: 4px 0; text-align: left; }
      .total, .gracias { font-weight: bold; text-align: center; margin-top: 10px; }
      .linea { text-align: center; margin: 8px 0; border-top: 1px dashed #000; }
    </style>
  `;

  let html = `
    ${estilo}
    <h2>Barbería El Corte</h2>
    <p>Ticket #${venta.id}</p>
    <p>${new Date(venta.fecha).toLocaleString()}</p>   
    <p class="linea">------------------------------</p>
    <table>
      <thead>
        <tr><th>Producto</th><th>Cant</th><th>P/U</th></tr>
      </thead>
      <tbody>
  `;

  venta.productos.forEach((p) => {
    html += `
      <tr>
        <td>${p.nombre}</td>
        <td>${p.cantidad}</td>
        <td>$${Number(p.precio).toFixed(2)}</td>
      </tr>
    `;
  });

  html += `
      </tbody>
    </table>
    <p class="linea">------------------------------</p>
    <p class="total">TOTAL: $${Number(venta.total).toFixed(2)}</p>
    <p class="center">Pago: ${venta.metodo_pago}</p>
    <p class="linea">------------------------------</p>
    <p class="gracias">¡Gracias por su compra!</p>
  `;

  ventana.document.write(`<html><body>${html}</body></html>`);
  ventana.document.close();
  ventana.focus();
  ventana.print();
  ventana.close();
}

</script>

<style>
.vue-notification.success {
  color: #181717; 
}
.vue-notification.warn {
  color: #181717; 
}
</style>