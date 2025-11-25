<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold">{{ doctor.name }}</h1>
    <p>Especialidad: {{ doctor.specialty }}</p>
    <p>Correo: {{ doctor.email || 'N/A' }}</p>
  </div>
</template>

<script setup>
const props = defineProps({ doctor: Object })
</script>
