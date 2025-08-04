<template>
  <AdminLayout>
    <PageBreadcrumb pageTitle="Modulo de Ventas" />
    <ComponentCard title="🛒 Generar Venta">
      <NuevaVenta />
    </ComponentCard>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import ComponentCard from '@/components/common/ComponentCard.vue'
import NuevaVenta from '@/components/ventas/NuevaVenta.vue'
</script>
