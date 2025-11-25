<!-- Correo: Cita creada en estado pendiente -->
<!-- Comentarios: Esta vista muestra los detalles básicos de la cita recién creada. -->
@php($a = $appointment)
<p>Hola {{ $a->patient_name }},</p>
<p>Tu cita ha sido registrada en estado <strong>pendiente</strong> con el médico de Neumología <strong>{{ $a->doctor->name }}</strong>.</p>
<ul>
  <li>Fecha y hora: {{ \Carbon\Carbon::parse($a->start_at)->format('d/m/Y H:i') }}</li>
  <li>Duración: {{ (int) env('APPOINTMENT_DURATION_MINUTES', 20) }} minutos</li>
  <li>Estado: {{ $a->status }}</li>
</ul>
<p>Pronto recibirás la confirmación o rechazo de tu cita.</p>
<p>Gracias,</p>
<p>{{ config('app.name') }}</p>
