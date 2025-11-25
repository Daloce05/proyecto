<template>
  <div class="p-6">
    <HomeButton />
    <BackLink fallback="/admin/appointments" />
    <h1 class="text-2xl font-bold">Detalle de cita</h1>
    <p>Paciente: {{ appointment.patient_name }}</p>
    <p>Correo: {{ appointment.patient_email }}</p>
    <p>Médico: {{ appointment.doctor?.name }}</p>
    <p>Inicio: {{ formatear(appointment.start_at) }}</p>
    <p>Estado: {{ appointment.status }}</p>
  </div>
</template>

<script setup>
const props = defineProps({ appointment: Object })
import BackLink from '../../Components/BackLink.vue'
import HomeButton from '../../Components/HomeButton.vue'
function formatear(iso) { const d = new Date(iso); return d.toLocaleString() }
</script>
