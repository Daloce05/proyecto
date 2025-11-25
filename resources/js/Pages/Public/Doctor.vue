<template>
  <div class="min-h-screen bg-gradient-to-br from-white to-blue-50">
    <div class="max-w-5xl mx-auto px-6 py-8 space-y-8">
      <div class="flex items-start justify-between">
        <div class="space-y-2">
          <h1 class="text-3xl font-extrabold text-gray-800">{{ doctor.name }}</h1>
          <div class="flex flex-wrap gap-2 items-center text-sm text-gray-600">
            <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-700 font-medium">{{ doctor.specialty }}</span>
            <span>Correo: {{ doctor.email || 'N/A' }}</span>
          </div>
        </div>
        <div class="flex gap-2">
          <HomeButton />
          <BackLink fallback="/explorar" />
        </div>
      </div>

      <!-- Navegación de semana -->
      <div class="flex items-center gap-3 rounded-xl border bg-white/80 backdrop-blur px-4 py-3 shadow-sm">
        <button class="px-3 py-1.5 text-xs font-medium bg-gray-100 hover:bg-gray-200 rounded-lg" @click="navegarSemana(-1)">← Semana</button>
        <div class="text-xs text-gray-500">Semana iniciando: <span class="font-semibold text-gray-700">{{ weekStart }}</span></div>
        <button class="px-3 py-1.5 text-xs font-medium bg-gray-100 hover:bg-gray-200 rounded-lg" @click="navegarSemana(1)">Semana →</button>
      </div>

      <!-- Slots agrupados por día -->
      <div v-if="grouped.length === 0" class="text-center py-16 rounded-2xl border border-dashed">
        <p class="text-gray-500">Sin espacios disponibles esta semana.</p>
      </div>
      <div v-else class="space-y-8">
        <div v-for="day in grouped" :key="day.date" class="space-y-3">
          <h2 class="text-sm font-semibold tracking-wide text-gray-600 flex items-center gap-2">
            <span class="inline-block w-2 h-2 rounded-full bg-blue-500"></span>{{ formatearFecha(day.date) }}
          </h2>
          <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <SlotCard
              v-for="slot in day.slots"
              :key="slot.start"
              :start="slot.start"
              :end="slot.end"
              :doctor="slot.doctor"
              :reserve="reservar"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
// Comentarios: Componente público que muestra disponibilidad semanal por médico.
import { router } from '@inertiajs/vue3'
import BackLink from '../../Components/BackLink.vue'
import HomeButton from '../../Components/HomeButton.vue'
import SlotCard from '../../Components/SlotCard.vue'

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

const grouped = aggregateByDay()

function aggregateByDay() {
  const map = {}
  for (const slot of props.availability) {
    const d = new Date(slot.start)
    const key = d.toISOString().slice(0,10)
    if (!map[key]) map[key] = []
    map[key].push(slot)
  }
  return Object.entries(map).map(([date, slots]) => ({ date, slots }))
}

function formatearFecha(dateStr) {
  const d = new Date(dateStr)
  return d.toLocaleDateString(undefined, { weekday: 'long', day: '2-digit', month: 'short' })
}
</script>
