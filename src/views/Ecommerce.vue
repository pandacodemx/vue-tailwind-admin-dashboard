<template>
  <admin-layout>
    <DashboardCitas />
  </admin-layout>
</template>

<script>
import AdminLayout from '../components/layout/AdminLayout.vue'
import DashboardCitas from '@/components/dashboard/DashboardCitas.vue'

export default {
  components: {
    AdminLayout,
    DashboardCitas
  },
  name: 'Ecommerce',
}
</script>
