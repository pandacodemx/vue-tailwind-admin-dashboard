<template>
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="max-w-full overflow-x-auto custom-scrollbar">
            <table class="min-w-full">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="px-5 py-3 text-left w-2/11 sm:px-6">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Cliente</p>
                        </th>
                        <th class="px-5 py-3 text-left w-2/11 sm:px-6">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Servicios</p>
                        </th>
                        <th class="px-5 py-3 text-left w-2/11 sm:px-6">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Fecha</p>
                        </th>
                        <th class="px-5 py-3 text-left w-2/11 sm:px-6">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Estado</p>
                        </th>
                        <th class="px-5 py-3 text-left w-2/11 sm:px-6">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Total</p>
                        </th>
                        <th class="px-5 py-3 text-left w-1/11 sm:px-6">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Acciones</p>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <tr v-for="cita in citas" :key="cita.id" class="border-t border-gray-100 dark:border-gray-800">
                        <td class="px-5 py-4 sm:px-6">
                            <div class="flex items-center gap-3">
                                <div>
                                    <span class="block font-medium text-gray-800 text-theme-sm dark:text-white/90">
                                        {{ cita.cliente_nombre }}
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 sm:px-6">
                            <div class="flex flex-col gap-1">
                                <span v-for="servicio in cita.servicios" :key="servicio.id"
                                    class="text-sm text-gray-600 dark:text-gray-300">
                                    • {{ servicio.nombre }}
                                </span>
                            </div>
                        </td>
                        <td class="px-5 py-4 sm:px-6">
                            <div class="flex items-center gap-3">
                                <div>
                                    <span class="block font-medium text-gray-800 text-theme-sm dark:text-white/90">
                                        {{ formatFecha(cita.fecha) }}
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 sm:px-6">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                :class="{
                                    'bg-green-100 text-green-800': cita.estado === 'atendida',
                                    'bg-yellow-100 text-yellow-800': cita.estado === 'pendiente',
                                }">
                                {{ capitalize(cita.estado) }}
                            </span>
                        </td>
                        <td class="px-5 py-4 sm:px-6">
                            <div class="flex items-center gap-3">
                                <div>
                                    <span class="block font-medium text-green-600 text-theme-sm">
                                        $ {{ cita.total }}
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 sm:px-6">
                            <button @click="$emit('verDetalles', cita)" title="Ver detalles"
                                class="text-blue-500 hover:text-blue-700 ease-in-out hover:scale-125 hover:duration-300 dark:text-blue-400">
                                👁️
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import { format } from 'date-fns'
import { es } from 'date-fns/locale'

defineProps({
    citas: {
        type: Array,
        required: true
    }
})

defineEmits(['verDetalles'])

function formatFecha(fecha) {
    return format(new Date(fecha), "dd/MM/yyyy HH:mm", { locale: es })
}

function capitalize(str) {
    return str.charAt(0).toUpperCase() + str.slice(1)
}
</script>