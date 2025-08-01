<template>
    <AdminLayout>
      <PageBreadcrumb pageTitle="Configuración de Horarios" />
      <ConfigurarHorarios />
    </AdminLayout>
  </template>
  
  <script setup>
  import ConfigurarHorarios from '@/components/citas/ConfiguracionHorario.vue'
  import AdminLayout from '@/components/layout/AdminLayout.vue'
  import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
  </script>
  