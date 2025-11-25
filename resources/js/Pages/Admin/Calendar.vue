<template>
  <!-- Calendario semanal del panel: muestra citas pendientes y confirmadas -->
  <div class="p-6 space-y-4">
    <h1 class="text-2xl font-bold">Calendario semanal — {{ doctor.name }}</h1>
    <div class="flex items-center gap-2">
      <button class="px-3 py-1 bg-gray-200 rounded" @click="navegarSemana(-1)">Semana anterior</button>
      <span class="font-semibold">Semana iniciando: {{ weekStart }}</span>
      <button class="px-3 py-1 bg-gray-200 rounded" @click="navegarSemana(1)">Siguiente semana</button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
      <div v-for="a in appointments" :key="a.slug" class="border rounded p-3">
        <div class="font-medium">{{ formatear(a.start_at) }}</div>
        <div>Paciente: {{ a.patient_name }}</div>
        <div>Estado: {{ a.status }}</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3'

const props = defineProps({
  doctor: Object,
  weekStart: String,
  appointments: Array,
})

function navegarSemana(delta) {
  const fecha = new Date(props.weekStart)
  fecha.setDate(fecha.getDate() + (delta * 7))
  router.get('/calendar', { doctor: props.doctor.slug, week: fecha.toISOString().slice(0,10) }, { preserveState: true })
}

function formatear(iso) {
  const d = new Date(iso)
  return d.toLocaleString()
}
</script>
