<template>
  <div class="p-6 space-y-4">
    <HomeButton />
    <BackLink fallback="/admin/doctors" />
    <h1 class="text-2xl font-bold">Editar médico</h1>
    <form @submit.prevent="submit" class="max-w-md space-y-3">
      <div>
        <label class="block text-sm">Nombre</label>
        <input v-model="form.name" class="mt-1 w-full border rounded p-2" required />
      </div>
      <div>
        <label class="block text-sm">Correo</label>
        <input v-model="form.email" type="email" class="mt-1 w-full border rounded p-2" />
      </div>
      <div>
        <label class="block text-sm">Especialidad</label>
        <input v-model="form.specialty" class="mt-1 w-full border rounded p-2" required />
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

const props = defineProps({ doctor: Object })
const form = ref({ name: props.doctor.name, email: props.doctor.email, specialty: props.doctor.specialty })
function submit() { router.put(route('panel.doctors.update', props.doctor.slug), form.value) }
</script>
