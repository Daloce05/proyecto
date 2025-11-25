<template>
  <!-- Página pública: selector de médico y calendario semanal -->
  <div class="p-6 space-y-4">
    <HomeButton />
    <h1 class="text-2xl font-bold">Agendamiento de Citas - Neumología</h1>
    <!-- Selector de médicos -->
    <div>
      <label class="block text-sm font-medium">Selecciona un médico</label>
      <select class="mt-1 border rounded p-2" v-model="selectedSlug" @change="cargarDisponibilidad">
        <option value="">-- Selecciona --</option>
        <option v-for="d in doctors" :key="d.slug" :value="d.slug">{{ d.name }}</option>
      </select>
    </div>

    <!-- Navegación de semana -->
    <div class="flex items-center gap-2">
      <button class="px-3 py-1 bg-gray-200 rounded" @click="navegarSemana(-1)">Semana anterior</button>
      <span class="font-semibold">Semana iniciando: {{ weekStart }}</span>
      <button class="px-3 py-1 bg-gray-200 rounded" @click="navegarSemana(1)">Siguiente semana</button>
    </div>

    <!-- Calendario: muestra solo espacios disponibles -->
    <div v-if="availability.length === 0" class="text-gray-600">No hay disponibilidad para los criterios seleccionados.</div>
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
      <div v-for="slot in availability" :key="slot.start" class="border rounded p-3">
        <div class="font-medium">{{ formatear(slot.start) }} - {{ formatear(slot.end) }}</div>
        <button class="mt-2 px-3 py-1 bg-blue-600 text-white rounded" @click="reservar(slot)">Reservar</button>
      </div>
    </div>
  </div>
</template>

<script setup>
// Comentarios: Componente simple que consume props de Inertia y ofrece navegación semanal y reserva.
import { router } from '@inertiajs/vue3'
import { ref } from 'vue'
import HomeButton from '../../Components/HomeButton.vue'

const props = defineProps({
  doctors: Array,
  selectedDoctor: Object,
  weekStart: String,
  duration: Number,
  availability: Array,
})

const selectedSlug = ref(props.selectedDoctor?.slug || '')

function navegarSemana(delta) {
  const fecha = new Date(props.weekStart)
  fecha.setDate(fecha.getDate() + (delta * 7))
  router.get('/explorar', { week: fecha.toISOString().slice(0,10), doctor: selectedSlug.value }, { preserveState: true })
}

function cargarDisponibilidad() {
  router.get('/explorar', { week: props.weekStart, doctor: selectedSlug.value }, { preserveState: true })
}

function formatear(iso) {
  const d = new Date(iso)
  return d.toLocaleString()
}

function reservar(slot) {
  router.get('/appointments/new', { doctor: slot.doctor, start: slot.start })
}
</script>
