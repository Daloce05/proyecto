<template>
  <div class="p-6">
    <HomeButton />
    <h1 class="text-2xl font-bold">Médicos</h1>
    <div class="mt-4">
      <table class="min-w-full border">
        <thead>
          <tr class="bg-gray-100">
            <th class="p-2 border">Nombre</th>
            <th class="p-2 border">Especialidad</th>
            <th class="p-2 border">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="d in doctors" :key="d.slug">
            <td class="p-2 border">{{ d.name }}</td>
            <td class="p-2 border">{{ d.specialty }}</td>
            <td class="p-2 border">
              <a :href="route('panel.doctors.edit', d.slug)" class="text-blue-600">Editar</a>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
// Comentarios: Listado simple de médicos.
const props = defineProps({ doctors: Array })
import HomeButton from '../../Components/HomeButton.vue'
</script>
