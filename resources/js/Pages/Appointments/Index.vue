<template>
  <div class="p-6">
    <HomeButton />
    <h1 class="text-2xl font-bold">Citas</h1>
    <div class="mt-4">
      <table class="min-w-full border">
        <thead>
          <tr class="bg-gray-100">
            <th class="p-2 border">Paciente</th>
            <th class="p-2 border">Médico</th>
            <th class="p-2 border">Inicio</th>
            <th class="p-2 border">Estado</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="a in appointments.data" :key="a.slug">
            <td class="p-2 border">{{ a.patient_name }}</td>
            <td class="p-2 border">{{ a.doctor?.name }}</td>
            <td class="p-2 border">{{ formatear(a.start_at) }}</td>
            <td class="p-2 border">{{ a.status }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({ appointments: Object })
function formatear(iso) { const d = new Date(iso); return d.toLocaleString() }
import HomeButton from '../../Components/HomeButton.vue'
</script>
