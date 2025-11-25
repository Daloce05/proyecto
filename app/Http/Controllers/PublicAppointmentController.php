<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\Doctor;
use App\Models\Appointment;
use App\Mail\AppointmentCreatedMail;
use Carbon\Carbon;

class PublicAppointmentController extends Controller
{
    /**
     * Crea una cita en estado pendiente: valida colisión y disponibilidad, y envía email.
     */
    public function store(Request $request)
    {
        // Valida datos básicos del paciente y parámetros
        $data = $request->validate([
            'doctor' => ['required', 'string'],
            'start' => ['required', 'date'],
            'patient_name' => ['required', 'string', 'max:255'],
            'patient_email' => ['required', 'email'],
            'notes' => ['nullable', 'string'],
        ]);

        $doctor = Doctor::where('slug', $data['doctor'])->firstOrFail();
        $duration = (int) env('APPOINTMENT_DURATION_MINUTES', 20);
        $start = Carbon::parse($data['start']);
        $end = (clone $start)->addMinutes($duration);

        // Valida que el horario esté dentro de alguna disponibilidad del médico
        $weekday = (int) $start->dayOfWeekIso - 1; // lunes=0 ... domingo=6
        $enDisponibilidad = $doctor->availabilities()
            ->where('weekday', $weekday)
            ->where('start_time', '<=', $start->format('H:i:s'))
            ->where('end_time', '>=', $end->format('H:i:s'))
            ->exists();
        if (!$enDisponibilidad) {
            return back()->withErrors(['start' => 'El horario seleccionado no está dentro de la disponibilidad del médico.']);
        }

        // Valida colisiones: mismo médico no puede tener dos citas pendientes o confirmadas en misma hora
        $collision = $doctor->appointments()
            ->where('start_at', $start)
            ->whereIn('status', ['pendiente', 'confirmada'])
            ->exists();
        if ($collision) {
            return back()->withErrors(['start' => 'Ese horario ya está ocupado para este médico.']);
        }

        // Crea cita en pendiente
        $appointment = Appointment::create([
            'doctor_id' => $doctor->id,
            'slug' => Str::uuid()->toString(),
            'patient_name' => $data['patient_name'],
            'patient_email' => $data['patient_email'],
            'start_at' => $start,
            'end_at' => $end,
            'status' => 'pendiente',
            'notes' => $data['notes'] ?? null,
        ]);

        // Envía correo de cita reservada
        Mail::to($appointment->patient_email)->send(new AppointmentCreatedMail($appointment));

        return redirect()->route('public.doctor', $doctor)->with('success', 'Cita creada en estado pendiente.');
    }
}
