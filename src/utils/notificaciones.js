import { useNotifications } from '@/composables/useNotifications'
const { agregarNotificacion } = useNotifications()
import { format } from 'date-fns'

export function verificarStockBajo(productos) {
    const notificados = JSON.parse(localStorage.getItem('stockNotificado')) || []
  
    productos.forEach((producto) => {
      if (producto.stock <= producto.stock_minimo && !notificados.includes(producto.id)) {
        agregarNotificacion({
          userImage: '/img/icono-stock.png',
          userName: 'Sistema',
          action: ' detectó bajo stock de',
          project: producto.nombre,
          time: 'Revísalo',
          type: 'Stock crítico',
        })
  
        notificados.push(producto.id)
      }
    })
  
    localStorage.setItem('stockNotificado', JSON.stringify(notificados))
  }
  
  
  export function verificarCitasProximas(citas) {
    const notificados = JSON.parse(localStorage.getItem('citasNotificadas')) || []
    const ahora = new Date()
    const TRES_HORAS = 3 * 60 * 60 * 1000
  
    citas.forEach((cita) => {
      const fechaCita = new Date(cita.fecha)
      const diferencia = fechaCita - ahora
  
      if (diferencia > 0 && diferencia <= TRES_HORAS && !notificados.includes(cita.id)) {
        agregarNotificacion({
          userImage: '/img/icono-cita.png',
          userName: 'Sistema',
          action: ` te recuerda la cita con`,
          project: cita.cliente_nombre,
          time: format(fechaCita, "dd 'de' MMMM yyyy, HH:mm"),
          type: 'Cita próxima',
        })
  
        notificados.push(cita.id)
      }
    })
  
    localStorage.setItem('citasNotificadas', JSON.stringify(notificados))
  }
  