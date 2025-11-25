<!-- Correo: Cita aceptada -->
<!-- Comentarios: Esta vista confirma la aceptación de la cita. -->
@php($a = $appointment)
<p>Hola {{ $a->patient_name }},</p>
<p>Tu cita con el médico de Neumología <strong>{{ $a->doctor->name }}</strong> ha sido <strong>aceptada</strong>.</p>
<ul>
  <li>Fecha y hora: {{ \Carbon\Carbon::parse($a->start_at)->format('d/m/Y H:i') }}</li>
  <li>Duración: {{ (int) env('APPOINTMENT_DURATION_MINUTES', 20) }} minutos</li>
  <li>Estado: {{ $a->status }}</li>
</ul>
<p>Por favor llega con 10 minutos de anticipación.</p>
<p>Gracias,</p>
<p>{{ config('app.name') }}</p>
