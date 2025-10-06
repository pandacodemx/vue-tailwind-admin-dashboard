<template>
    <AdminLayout>
        <PageBreadcrumb :pageTitle="currentPageTitle" />
        <div class="space-y-5 sm:space-y-6">
            <ComponentCard title="Citas Pagadas - Reportes">
                <!-- Filtros -->
                <div class="flex flex-col md:flex-row items-center justify-between mb-4 gap-4">
                    <!-- Buscador -->
                    <input v-model="filtroTexto" type="text" placeholder="Buscar por cliente o servicio..."
                        class="px-4 py-2 border rounded w-full md:w-1/3 dark:bg-gray-800 dark:text-white dark:border-gray-600" />

                    <!-- Filtro de fechas -->
                    <div class="flex gap-2 items-center">
                        <Datepicker v-model="fechaInicio" :locale="es" placeholder="Desde"
                            class="border rounded px-2 py-1 dark:bg-gray-800 dark:text-white" />
                        <Datepicker v-model="fechaFin" :locale="es" placeholder="Hasta"
                            class="border rounded px-2 py-1 dark:bg-gray-800 dark:text-white" />
                    </div>

                    <!-- Botón exportar -->
                    <Button size="sm" variant="success" @click="exportarReporte" class="ml-auto">
                        📝 Exportar Reporte
                    </Button>
                </div>

                <!-- Estadísticas -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Citas Pagadas</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ estadisticas.totalCitas }}</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Ingresos Totales</p>
                        <p class="text-2xl font-bold text-green-600">${{ estadisticas.ingresosTotales }}</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Citas Atendidas</p>
                        <p class="text-2xl font-bold text-blue-600">{{ estadisticas.citasAtendidas }}</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Promedio por Cita</p>
                        <p class="text-2xl font-bold text-purple-600">${{ estadisticas.promedioPorCita }}</p>
                    </div>
                </div>

                <CitasPagadasTable :citas="citasPaginadas" @verDetalles="verDetallesCita" />

                <div class="flex justify-between items-center mt-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400 dark:text-white">
                        Mostrando {{ citasPaginadas.length }} de {{ citasFiltradas.length }} citas pagadas
                    </p>

                    <div class="flex gap-2">
                        <Button @click="cambiarPagina(paginaActual - 1)" :disabled="paginaActual === 1"
                            variant="secondary" class="dark:text-white">
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

                <!-- Modal de detalles -->
                <Modal v-if="isDetallesModalOpen" @close="closeDetallesModal">
                    <template #body>
                        <div class="bg-white dark:bg-gray-900 rounded-xl p-6 max-w-2xl mx-auto">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">
                                Detalles de la Cita
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Cliente</p>
                                    <p class="font-medium dark:text-white">{{ citaSeleccionada?.cliente_nombre }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Fecha</p>
                                    <p class="font-medium dark:text-white">{{ formatFecha(citaSeleccionada?.fecha) }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Estado</p>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                        :class="{
                                            'bg-green-100 text-green-800': citaSeleccionada?.estado === 'atendida',
                                            'bg-yellow-100 text-yellow-800': citaSeleccionada?.estado === 'pendiente',
                                        }">
                                        {{ capitalize(citaSeleccionada?.estado || '') }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Total</p>
                                    <p class="font-medium text-green-600">${{ citaSeleccionada?.total }}</p>
                                </div>
                            </div>

                            <div class="mb-4">
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Servicios</p>
                                <ul class="list-disc list-inside space-y-1">
                                    <li v-for="servicio in citaSeleccionada?.servicios" :key="servicio.id"
                                        class="dark:text-white">
                                        {{ servicio.nombre }} - ${{ servicio.precio }}
                                    </li>
                                </ul>
                            </div>

                            <div v-if="citaSeleccionada?.notas" class="mb-4">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Notas</p>
                                <p class="dark:text-white">{{ citaSeleccionada.notas }}</p>
                            </div>

                            <div class="flex justify-end">
                                <Button variant="secondary" @click="closeDetallesModal" class="dark:text-white">
                                    Cerrar
                                </Button>
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
import { format } from 'date-fns'
import { es } from 'date-fns/locale'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import ComponentCard from '@/components/common/ComponentCard.vue'
import CitasPagadasTable from '@/components/citas/CitasPagadasTable.vue'
import Button from '@/components/ui/Button.vue'
import Modal from '@/components/ui/Modal.vue'
import HttpService from '@/services/HttpService'
import Datepicker from 'vue3-datepicker'
import ChevronLeftIcon from '@/icons/ChevronLeftIcon.vue'
import ChevronRightIcon from '@/icons/ChevronRightIcon.vue'
import { notify } from '@kyvg/vue3-notification'

const currentPageTitle = ref('Reportes - Citas Pagadas')
const isDetallesModalOpen = ref(false)
const citaSeleccionada = ref(null)
const citas = ref([])
const filtroTexto = ref('')
const fechaInicio = ref(null)
const fechaFin = ref(null)
const paginaActual = ref(1)
const porPagina = 10

function resetHora(fecha, fin = false) {
    if (!fecha) return null
    const f = new Date(fecha)
    f.setHours(fin ? 23 : 0, fin ? 59 : 0, fin ? 59 : 0, 999)
    return f
}

const citasFiltradas = computed(() => {
    return citas.value.filter((cita) => {
        const texto = filtroTexto.value.toLowerCase()
        const coincideTexto =
            cita.cliente_nombre.toLowerCase().includes(texto) ||
            cita.servicios.some((s) => s.nombre.toLowerCase().includes(texto))

        const fechaCita = new Date(cita.fecha)
        const desde = fechaInicio.value ? resetHora(fechaInicio.value) : null
        const hasta = fechaFin.value ? resetHora(fechaFin.value, true) : null

        const coincideFecha = (!desde || fechaCita >= desde) && (!hasta || fechaCita <= hasta)

        return coincideTexto && coincideFecha
    })
})

const estadisticas = computed(() => {
    const totalCitas = citasFiltradas.value.length
    const ingresosTotales = citasFiltradas.value.reduce((sum, cita) => sum + parseFloat(cita.total), 0)
    const citasAtendidas = citasFiltradas.value.filter(cita => cita.estado === 'atendida').length
    const promedioPorCita = totalCitas > 0 ? (ingresosTotales / totalCitas).toFixed(2) : 0

    return {
        totalCitas,
        ingresosTotales: ingresosTotales.toFixed(2),
        citasAtendidas,
        promedioPorCita
    }
})

const totalPaginas = computed(() => Math.ceil(citasFiltradas.value.length / porPagina))

const citasPaginadas = computed(() => {
    const inicio = (paginaActual.value - 1) * porPagina
    return citasFiltradas.value.slice(inicio, inicio + porPagina)
})

function cambiarPagina(nueva) {
    if (nueva >= 1 && nueva <= totalPaginas.value) {
        paginaActual.value = nueva
    }
}

onMounted(() => {
    cargarCitasPagadas()
})

async function cargarCitasPagadas() {
    try {
        const data = await HttpService.get('/citas/pagadas.php')
        citas.value = data
    } catch (error) {
        console.error('Error al cargar citas pagadas:', error)
        notify({
            title: 'Error',
            text: 'No se pudieron cargar las citas pagadas',
            type: 'error'
        })
    }
}

function formatFecha(fecha) {
    return format(new Date(fecha), "dd 'de' MMMM yyyy, HH:mm", { locale: es })
}

function capitalize(str) {
    return str ? str.charAt(0).toUpperCase() + str.slice(1) : ''
}

function verDetallesCita(cita) {
    citaSeleccionada.value = cita
    isDetallesModalOpen.value = true
}

function closeDetallesModal() {
    isDetallesModalOpen.value = false
    citaSeleccionada.value = null
}

async function exportarReporte() {
    try {
        const response = await HttpService.post('/citas/exportar_reporte.php', {
            fechaInicio: fechaInicio.value,
            fechaFin: fechaFin.value,
            filtro: filtroTexto.value
        }, {
            responseType: 'blob'
        })

        const url = window.URL.createObjectURL(new Blob([response]))
        const link = document.createElement('a')
        link.href = url
        link.setAttribute('download', `reporte-citas-${new Date().toISOString().split('T')[0]}.csv`)
        document.body.appendChild(link)
        link.click()
        link.remove()
        window.URL.revokeObjectURL(url)

    } catch (error) {
        console.error('Error al exportar reporte:', error)
        notify({
            title: 'Error',
            text: 'No se pudo exportar el reporte',
            type: 'error'
        })
    }
}
</script>