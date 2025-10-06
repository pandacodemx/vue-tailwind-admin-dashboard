import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  scrollBehavior(to, from, savedPosition) {
    return savedPosition || { left: 0, top: 0 }
  },
  routes: [
    {
      path: '/',
      name: 'Ecommerce',
      component: () => import('../views/Ecommerce.vue'),
      meta: {
        title: 'eCommerce Dashboard',
      },
    },
    {
      path: '/ajustes/horario',
      name: 'Horario',
      component: () => import('../views/Configuracion/ConfiguracionGeneral.vue'),
      meta: {
        title: 'Horario',
      },
    },
    {
      path: '/clientes',
      name: 'Clientes',
      component: () => import('../views/Clientes/Clientes.vue'),
      meta: {
        title: 'Clientes',
      },
    },
    {
      path: '/servicios',
      name: 'Servicios',
      component: () => import('../views/Servicios/Servicios.vue'),
      meta: {
        title: 'Servicios',
      },
    },
    {
      path: '/citas/nueva',
      name: 'Citas',
      component: () => import('../views/Citas/Citas.vue'),
      meta: {
        title: 'Citas',
      },
    },
    {
      path: '/citas/consulta',
      name: 'Consulta Citas',
      component: () => import('../views/Citas/ListCitas.vue'),
      meta: {
        title: 'Consulta Citas',
      },
    },
    {
      path: '/citas/pagadas',
      name: 'Citas Pagadas',
      component: () => import('../views/Citas/CitasPagadas.vue'),
      meta: {
        title: 'Citas Pagadas',
      },
    },
    {
      path: '/citas/calendario',
      name: 'Calendario',
      component: () => import('../views/Citas/Calendario.vue'),
      meta: {
        title: 'Calendario',
      },
    },
  {
      path: '/productos',
      name: 'Productos',
      component: () => import('../views/Productos/Productos.vue'),
      meta: {
        title: 'Productos',
      },
    },
    {
      path: '/categorias',
      name: 'Categorias',
      component: () => import('../views/Categorias/Categorias.vue'),
      meta: {
        title: 'Categorias',
      },
    },
    {
      path: '/paquetes',
      name: 'Paquetes',
      component: () => import('../views/Paquetes/Paquetes.vue'),
      meta: {
        title: 'Paquetes',
      },
    },
    {
      path: '/ventas/nueva',
      name: 'Venta Nueva',
      component: () => import('../views/Ventas/Ventas.vue'),
      meta: {
        title: 'Venta Nueva',
      },
    },
    {
      path: '/ventas/consultas',
      name: 'Consulta Ventas',
      component: () => import('../views/Ventas/Ventas_Consulta.vue'),
      meta: {
        title: 'Consulta Ventas',
      },
    },
    {
      path: '/profile',
      name: 'Profile',
      component: () => import('../views/Others/UserProfile.vue'),
      meta: {
        title: 'Profile',
      },
    },
    {
      path: '/alerts',
      name: 'Alerts',
      component: () => import('../views/UiElements/Alerts.vue'),
      meta: {
        title: 'Alerts',
      },
    },
    {
      path: '/avatars',
      name: 'Avatars',
      component: () => import('../views/UiElements/Avatars.vue'),
      meta: {
        title: 'Avatars',
      },
    },
    {
      path: '/badge',
      name: 'Badge',
      component: () => import('../views/UiElements/Badges.vue'),
      meta: {
        title: 'Badge',
      },
    },

    {
      path: '/buttons',
      name: 'Buttons',
      component: () => import('../views/UiElements/Buttons.vue'),
      meta: {
        title: 'Buttons',
      },
    },

    {
      path: '/images',
      name: 'Images',
      component: () => import('../views/UiElements/Images.vue'),
      meta: {
        title: 'Images',
      },
    },
    {
      path: '/videos',
      name: 'Videos',
      component: () => import('../views/UiElements/Videos.vue'),
      meta: {
        title: 'Videos',
      },
    },
    {
      path: '/blank',
      name: 'Blank',
      component: () => import('../views/Pages/BlankPage.vue'),
      meta: {
        title: 'Blank',
      },
    },

    {
      path: '/error-404',
      name: '404 Error',
      component: () => import('../views/Errors/FourZeroFour.vue'),
      meta: {
        title: '404 Error',
      },
    },

    {
      path: '/signin',
      name: 'Signin',
      component: () => import('../views/Auth/Signin.vue'),
      meta: {
        title: 'Signin',
      },
    },
  ],
})

export default router

router.beforeEach((to, from, next) => {
  document.title = `Vue.js ${to.meta.title} | TailAdmin - Vue.js Tailwind CSS Dashboard Template`
  next()
})
