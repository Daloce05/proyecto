<template>
  <!-- Resumen de pendientes y próximas confirmadas con filtro por médico -->
  <div class="p-6 space-y-4">
    <HomeButton />
    <h1 class="text-2xl font-bold">Panel de administración</h1>
    <div>
      <label class="block text-sm font-medium">Filtrar por médico</label>
      <select class="mt-1 border rounded p-2" v-model="doctorSlug" @change="filtrar">
        <option value="">Todos</option>
        <option v-for="d in doctors" :key="d.slug" :value="d.slug">{{ d.name }}</option>
      </select>
      <div class="mt-3 flex items-center gap-2">
        <button
          @click="verCalendario"
          :disabled="!doctorSlug"
          class="px-3 py-1 text-sm rounded transition-colors"
          :class="doctorSlug ? 'bg-indigo-600 text-white hover:bg-indigo-700' : 'bg-gray-200 text-gray-500 cursor-not-allowed'"
        >Ver calendario del médico</button>
        <span v-if="!doctorSlug" class="text-xs text-gray-500">Selecciona un médico para habilitar</span>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div>
        <h2 class="text-xl font-semibold">Pendientes</h2>
        <ul class="divide-y">
          <li v-for="a in pending" :key="a.slug" class="py-2 flex items-center justify-between">
            <span>{{ a.patient_name }} — {{ formatear(a.start_at) }} ({{ a.doctor.name }})</span>
            <div class="space-x-2">
              <button class="px-3 py-1 bg-green-600 text-white rounded" @click="aceptar(a.slug)">Aceptar</button>
              <button class="px-3 py-1 bg-red-600 text-white rounded" @click="rechazar(a.slug)">Rechazar</button>
            </div>
          </li>
        </ul>
      </div>
      <div>
        <h2 class="text-xl font-semibold">Próximas confirmadas</h2>
        <ul class="divide-y">
          <li v-for="a in upcoming" :key="a.slug" class="py-2">
            {{ a.patient_name }} — {{ formatear(a.start_at) }} ({{ a.doctor.name }})
          </li>
        </ul>
      </div>
    </div>

    <!-- Acceso rápido calendario por médico -->
    <div class="mt-10">
      <h2 class="text-lg font-semibold text-gray-700 mb-3">Calendario por médico</h2>
      <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        <div v-for="d in doctors" :key="d.slug" class="border rounded-lg p-4 bg-white shadow-sm flex flex-col gap-2">
          <div class="text-sm font-medium text-gray-800">{{ d.name }}</div>
          <div class="text-xs text-gray-500">{{ d.specialty }}</div>
          <button @click="router.get('/calendar', { doctor: d.slug })" class="mt-auto px-3 py-1.5 text-xs bg-indigo-600 text-white rounded hover:bg-indigo-700">Ver calendario</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
// Comentarios: Acciones de aceptar/rechazar y filtro por médico.
import { router } from '@inertiajs/vue3'
import { ref } from 'vue'
import HomeButton from '../../Components/HomeButton.vue'

const props = defineProps({
  pending: Array,
  upcoming: Array,
  doctors: Array,
})

const doctorSlug = ref('')

function filtrar() {
  router.get('/home', { doctor: doctorSlug.value }, { preserveState: true })
}

function verCalendario() {
  if (doctorSlug.value) {
    router.get('/calendar', { doctor: doctorSlug.value })
  }
}

function aceptar(slug) {
  router.post(`/admin/appointments/${slug}/accept`)
}

function rechazar(slug) {
  router.post(`/admin/appointments/${slug}/reject`)
}

function formatear(iso) {
  const d = new Date(iso)
  return d.toLocaleString()
}
</script>
