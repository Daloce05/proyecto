<template>
  <div class="p-6">
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
function formatear(iso) { const d = new Date(iso); return d.toLocaleString() }
</script>
