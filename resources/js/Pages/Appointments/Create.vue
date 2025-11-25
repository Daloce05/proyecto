<template>
  <div class="p-6 space-y-4">
    <h1 class="text-2xl font-bold">Crear cita (panel)</h1>
    <form @submit.prevent="submit" class="max-w-md space-y-3">
      <div>
        <label class="block text-sm">Médico</label>
        <select v-model="form.doctor_id" class="mt-1 w-full border rounded p-2" required>
          <option v-for="d in doctors" :key="d.id" :value="d.id">{{ d.name }}</option>
        </select>
      </div>
      <div>
        <label class="block text-sm">Fecha y hora de inicio</label>
        <input v-model="form.start_at" type="datetime-local" class="mt-1 w-full border rounded p-2" required />
      </div>
      <div>
        <label class="block text-sm">Paciente</label>
        <input v-model="form.patient_name" class="mt-1 w-full border rounded p-2" required />
      </div>
      <div>
        <label class="block text-sm">Correo del paciente</label>
        <input v-model="form.patient_email" type="email" class="mt-1 w-full border rounded p-2" required />
      </div>
      <div>
        <label class="block text-sm">Notas</label>
        <textarea v-model="form.notes" class="mt-1 w-full border rounded p-2" />
      </div>
      <button class="px-4 py-2 bg-blue-600 text-white rounded">Guardar</button>
    </form>
  </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3'
import { ref } from 'vue'
const props = defineProps({ doctors: Array })
const form = ref({ doctor_id: props.doctors?.[0]?.id || '', start_at: '', patient_name: '', patient_email: '', notes: '' })
function submit() { router.post(route('panel.appointments.store'), form.value) }
</script>
