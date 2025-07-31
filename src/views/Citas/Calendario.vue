<template>
  <AdminLayout>
    <PageBreadcrumb pageTitle="Calendario de Citas" />
    <ComponentCard title="📅 Agenda Visual">
      <CalendarioCitas />
    </ComponentCard>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import ComponentCard from '@/components/common/ComponentCard.vue'
import CalendarioCitas from '@/components/citas/CalendarioCitas.vue'
</script>
