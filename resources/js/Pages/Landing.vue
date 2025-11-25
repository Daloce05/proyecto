<template>
  <div class="min-h-screen flex flex-col">
    <!-- Nav bar -->
    <nav class="flex items-center justify-between px-6 py-4 bg-white shadow">
      <div class="font-bold text-lg">Plataforma Citas</div>
      <ul class="flex gap-6 text-sm font-medium">
        <li><a href="#roles" class="hover:text-blue-600">Roles</a></li>
        <li><a href="#funciona" class="hover:text-blue-600">Cómo Funciona</a></li>
        <li><a href="#contacto" class="hover:text-blue-600">Contacto</a></li>
      </ul>
    </nav>

    <!-- Hero / rol selection -->
    <section id="roles" class="flex-1 bg-gradient-to-b from-blue-50 to-white py-16">
      <div class="max-w-5xl mx-auto px-6">
        <h1 class="text-3xl md:text-4xl font-extrabold mb-4">Bienvenido al sistema de agendamiento de Neumología</h1>
        <p class="text-gray-600 mb-8 max-w-2xl">Selecciona tu rol para continuar. Si eres paciente podrás explorar disponibilidad y reservar una cita. Si eres médico accede al panel para gestionar tus citas.</p>
        <div class="grid md:grid-cols-2 gap-8">
          <!-- Paciente Card -->
          <div class="border rounded-xl p-6 bg-white shadow-sm flex flex-col">
            <h2 class="text-xl font-semibold mb-2">Soy Paciente</h2>
            <p class="text-sm text-gray-600 mb-4">Explora médicos, ve su disponibilidad semanal y reserva tu cita.</p>
            <div class="mt-auto flex flex-col gap-3">
              <button @click="goPaciente" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">Explorar Disponibilidad</button>
              <button @click="goReservar" class="px-4 py-2 rounded bg-indigo-600 text-white hover:bg-indigo-700">Reservar Directo</button>
            </div>
          </div>

          <!-- Médico Card -->
          <div class="border rounded-xl p-6 bg-white shadow-sm flex flex-col">
            <h2 class="text-xl font-semibold mb-2">Soy Médico</h2>
            <p class="text-sm text-gray-600 mb-4">Accede al panel para ver citas pendientes, confirmadas y tu calendario semanal.</p>
            <div class="mt-auto flex flex-col gap-3">
              <!-- Siempre visible: si no está autenticado se redirige a login -->
              <button @click="goPanel" class="px-4 py-2 rounded bg-emerald-600 text-white hover:bg-emerald-700">Ir al Panel</button>
              <button @click="goCalendar" class="px-4 py-2 rounded bg-teal-600 text-white hover:bg-teal-700">Ver Calendario</button>
              <div v-if="!isAuthenticated" class="text-xs text-gray-500">
                (Necesitas iniciar sesión para ver el calendario)
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Cómo funciona -->
    <section id="funciona" class="py-16 border-t bg-white">
      <div class="max-w-5xl mx-auto px-6">
        <h2 class="text-2xl font-bold mb-6">Cómo Funciona</h2>
        <ol class="space-y-4 list-decimal list-inside text-gray-700">
          <li>El paciente ingresa y elige un médico disponible.</li>
          <li>Selecciona un horario libre según la disponibilidad semanal.</li>
          <li>Confirma la cita y recibe un correo (modo log actualmente).</li>
          <li>El médico ingresa al panel y acepta o rechaza la cita.</li>
          <li>Estado se actualiza y se notifica por correo al paciente.</li>
        </ol>
      </div>
    </section>

    <!-- Contacto / Footer -->
    <section id="contacto" class="py-16 bg-gray-50 border-t">
      <div class="max-w-5xl mx-auto px-6">
        <h2 class="text-2xl font-bold mb-4">Contacto</h2>
        <p class="text-gray-600 mb-6">Para soporte técnico o mejoras, comunícate con el equipo de desarrollo.</p>
        <div class="text-sm text-gray-500">&copy; {{ new Date().getFullYear() }} Plataforma Citas Neumología</div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3'

const props = defineProps({
  isAuthenticated: Boolean,
  user: Object,
  firstDoctorSlug: String,
})

function goPaciente() {
  router.get('/explorar')
}
function goReservar() {
  // Antes apuntaba a /appointments/new sin parámetros y causaba 404.
  // Redirigimos al explorador donde el paciente puede elegir médico y horario.
  router.get('/explorar')
}
function goPanel() {
  if (props.isAuthenticated) {
    router.get('/home')
  } else {
    router.get('/login')
  }
}
function goCalendar() {
  if (props.isAuthenticated && props.firstDoctorSlug) {
    router.get(`/calendar?doctor=${props.firstDoctorSlug}`)
  } else {
    // Si no está autenticado se envía al login
    router.get('/login')
  }
}
function goLogin() {
  router.get('/login')
}
function goRegister() {
  router.get('/register')
}
</script>
