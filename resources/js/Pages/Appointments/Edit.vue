<template>
  <div class="p-6 space-y-4">
    <HomeButton />
    <BackLink fallback="/admin/appointments" />
    <h1 class="text-2xl font-bold">Editar cita</h1>
    <form @submit.prevent="submit" class="max-w-md space-y-3">
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
      <button class="px-4 py-2 bg-blue-600 text-white rounded">Actualizar</button>
    </form>
  </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3'
import { ref } from 'vue'
import BackLink from '../../Components/BackLink.vue'
import HomeButton from '../../Components/HomeButton.vue'
const props = defineProps({ appointment: Object, doctors: Array })
const form = ref({ patient_name: props.appointment.patient_name, patient_email: props.appointment.patient_email, notes: props.appointment.notes || '' })
function submit() { router.put(route('panel.appointments.update', props.appointment.slug), form.value) }
</script>
