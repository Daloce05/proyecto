<template>
  <!-- Formulario para confirmar datos y reservar -->
  <div class="p-6 space-y-4">
    <HomeButton />
    <BackLink fallback="/explorar" />
    <h1 class="text-2xl font-bold">Reservar cita con {{ doctor.name }}</h1>
    <p>Horario seleccionado: <strong>{{ formatear(start) }}</strong> ({{ duration }} minutos)</p>

    <form @submit.prevent="submit" class="space-y-3 max-w-md">
      <div>
        <label class="block text-sm">Nombre del paciente</label>
        <input v-model="form.patient_name" class="mt-1 w-full border rounded p-2" required />
      </div>
      <div>
        <label class="block text-sm">Correo electrónico</label>
        <input v-model="form.patient_email" type="email" class="mt-1 w-full border rounded p-2" required />
      </div>
      <div>
        <label class="block text-sm">Notas (opcional)</label>
        <textarea v-model="form.notes" class="mt-1 w-full border rounded p-2" />
      </div>
      <button class="px-4 py-2 bg-blue-600 text-white rounded">Confirmar reserva</button>
    </form>
  </div>
</template>

<script setup>
// Comentarios: Envia POST a /appointments creando cita en estado pendiente.
import { router } from '@inertiajs/vue3'
import { ref } from 'vue'
import BackLink from '../../Components/BackLink.vue'
import HomeButton from '../../Components/HomeButton.vue'

const props = defineProps({
  doctor: Object,
  start: String,
  duration: Number,
})

const form = ref({
  patient_name: '',
  patient_email: '',
  notes: '',
})

function submit() {
  router.post('/appointments', { doctor: props.doctor.slug, start: props.start, ...form.value })
}

function formatear(iso) {
  const d = new Date(iso)
  return d.toLocaleString()
}
</script>
