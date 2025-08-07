// src/composables/useNotifications.js

import { ref } from 'vue'

const notifications = ref([
  
])

const notifying = ref(true)

function getFechaHoy() {
    const hoy = new Date()
    return hoy.toISOString().split('T')[0] // '2025-08-06'
}

function yaSeNotificoHoy(clave) {
    const hoy = getFechaHoy()
    const fechaGuardada = localStorage.getItem(clave)
    return fechaGuardada === hoy
  }
  
  function marcarNotificadoHoy(clave) {
    const hoy = getFechaHoy()
    localStorage.setItem(clave, hoy)
  }

function agregarNotificacion(nueva) {
  nueva.leido = false 
  notifications.value.unshift(nueva)
  notifying.value = true
}

function marcarComoLeida(id) {
  const noti = notifications.value.find((n) => n.id === id)
  if (noti) {
    noti.leido = true
  }

  notifying.value = notifications.value.some((n) => !n.leido)
}

function marcarTodasComoLeidas() {
  notifications.value.forEach((n) => (n.leido = true))
  notifying.value = false
}

function limpiarNotificaciones() {
  notifications.value = []
  notifying.value = false
}

function eliminarLeidas() {
    notifications.value = notifications.value.filter((n) => !n.leido)
    notifying.value = notifications.value.some((n) => !n.leido)
  }

export function useNotifications() {
  return {
    notifications,
    notifying,
    agregarNotificacion,
    marcarComoLeida,
    marcarTodasComoLeidas,
    limpiarNotificaciones,
    eliminarLeidas,
  }
}
