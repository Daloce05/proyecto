<template>
  <!-- Perfil del médico y próximos espacios disponibles -->
  <div class="p-6 space-y-4">
    <HomeButton />
    <BackLink fallback="/explorar" />
    <h1 class="text-2xl font-bold">{{ doctor.name }} — {{ doctor.specialty }}</h1>
    <p class="text-gray-600">Correo: {{ doctor.email || 'N/A' }}</p>

    <!-- Navegación de semana -->
    <div class="flex items-center gap-2">
      <button class="px-3 py-1 bg-gray-200 rounded" @click="navegarSemana(-1)">Semana anterior</button>
      <span class="font-semibold">Semana iniciando: {{ weekStart }}</span>
      <button class="px-3 py-1 bg-gray-200 rounded" @click="navegarSemana(1)">Siguiente semana</button>
    </div>

    <div v-if="availability.length === 0" class="text-gray-600">Sin espacios disponibles.</div>
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
      <div v-for="slot in availability" :key="slot.start" class="border rounded p-3">
        <div class="font-medium">{{ formatear(slot.start) }} - {{ formatear(slot.end) }}</div>
        <button class="mt-2 px-3 py-1 bg-blue-600 text-white rounded" @click="reservar(slot)">Reservar</button>
      </div>
    </div>
  </div>
</template>

<script setup>
// Comentarios: Componente público que muestra disponibilidad semanal por médico.
import { router } from '@inertiajs/vue3'
import BackLink from '../../Components/BackLink.vue'
import HomeButton from '../../Components/HomeButton.vue'

const props = defineProps({
  doctor: Object,
  weekStart: String,
  duration: Number,
  availability: Array,
})

function navegarSemana(delta) {
  const fecha = new Date(props.weekStart)
  fecha.setDate(fecha.getDate() + (delta * 7))
  router.get(`/doctors/${props.doctor.slug}`, { week: fecha.toISOString().slice(0,10) }, { preserveState: true })
}

function reservar(slot) {
  router.get('/appointments/new', { doctor: slot.doctor, start: slot.start })
}

function formatear(iso) {
  const d = new Date(iso)
  return d.toLocaleString()
}
</script>
