<template>
  <div class="p-6">
    <HomeButton />
    <BackLink fallback="/admin/doctors" />
    <h1 class="text-2xl font-bold">{{ doctor.name }}</h1>
    <p>Especialidad: {{ doctor.specialty }}</p>
    <p>Correo: {{ doctor.email || 'N/A' }}</p>
  </div>
</template>

<script setup>
const props = defineProps({ doctor: Object })
import BackLink from '../../Components/BackLink.vue'
import HomeButton from '../../Components/HomeButton.vue'
</script>
