<template>
  <!-- Resumen de pendientes y próximas confirmadas con filtro por médico -->
  <div class="p-6 space-y-4">
    <h1 class="text-2xl font-bold">Panel de administración</h1>
    <div>
      <label class="block text-sm font-medium">Filtrar por médico</label>
      <select class="mt-1 border rounded p-2" v-model="doctorSlug" @change="filtrar">
        <option value="">Todos</option>
        <option v-for="d in doctors" :key="d.slug" :value="d.slug">{{ d.name }}</option>
      </select>
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
  </div>
</template>

<script setup>
// Comentarios: Acciones de aceptar/rechazar y filtro por médico.
import { router } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
  pending: Array,
  upcoming: Array,
  doctors: Array,
})

const doctorSlug = ref('')

function filtrar() {
  router.get('/home', { doctor: doctorSlug.value }, { preserveState: true })
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
