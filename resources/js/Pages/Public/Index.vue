<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50">
    <div class="max-w-6xl mx-auto px-6 py-10 space-y-8">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-extrabold tracking-tight text-gray-800">Agendar Cita Neumología</h1>
          <p class="text-sm text-gray-500 mt-1">Explora disponibilidad semanal y reserva fácilmente.</p>
        </div>
        <HomeButton />
      </div>

      <!-- Selector médico + semana -->
      <div class="rounded-2xl border bg-white/80 backdrop-blur p-5 shadow-sm space-y-4">
        <div class="grid gap-4 md:grid-cols-3 md:items-end">
          <div class="md:col-span-2">
            <label class="block text-xs font-semibold uppercase tracking-wide text-gray-600 mb-1">Médico</label>
            <select class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-blue-400 focus:border-blue-400" v-model="selectedSlug" @change="cargarDisponibilidad">
              <option value="">-- Selecciona --</option>
              <option v-for="d in doctors" :key="d.slug" :value="d.slug">{{ d.name }}</option>
            </select>
          </div>
          <div class="flex gap-2 justify-start md:justify-end">
            <button class="px-3 py-2 text-xs font-medium bg-gray-100 hover:bg-gray-200 rounded-lg" @click="navegarSemana(-1)">← Semana</button>
            <button class="px-3 py-2 text-xs font-medium bg-gray-100 hover:bg-gray-200 rounded-lg" @click="navegarSemana(1)">Semana →</button>
          </div>
        </div>
        <div class="text-xs text-gray-500">Semana iniciando: <span class="font-semibold text-gray-700">{{ weekStart }}</span></div>
      </div>

      <!-- Slots -->
      <div v-if="availability.length === 0" class="text-center py-16 rounded-2xl border border-dashed">
        <p class="text-gray-500">No hay disponibilidad para los criterios seleccionados.</p>
      </div>
      <div v-else>
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Espacios Disponibles</h2>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
          <SlotCard
            v-for="slot in availability"
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
</template>

<script setup>
// Comentarios: Componente simple que consume props de Inertia y ofrece navegación semanal y reserva.
import { router } from '@inertiajs/vue3'
import { ref } from 'vue'
import HomeButton from '../../Components/HomeButton.vue'
import SlotCard from '../../Components/SlotCard.vue'

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

function reservar(slot) {
  router.get('/appointments/new', { doctor: slot.doctor, start: slot.start })
}
</script>
