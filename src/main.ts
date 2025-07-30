import './assets/main.css'
// Import Swiper styles
import 'swiper/css'
import 'swiper/css/navigation'
import 'swiper/css/pagination'
import 'jsvectormap/dist/jsvectormap.css'
import 'flatpickr/dist/flatpickr.css'

import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import VueApexCharts from 'vue3-apexcharts'
import Notifications from '@kyvg/vue3-notification'

const app = createApp(App)

app.use(router)
app.use(Notifications)
app.use(VueApexCharts)

app.mount('#app')
