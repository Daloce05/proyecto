<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Doctor;
use App\Models\Appointment;
use Carbon\Carbon;

class PublicController extends Controller
{
    /**
     * Muestra la página principal pública con selector de médicos y calendario semanal.
     */
    public function index(Request $request)
    {
        // Obtiene todos los médicos activos de la especialidad Neumología
        $doctors = Doctor::query()->where('specialty', 'Neumología')->get();

        // Determina la semana a mostrar (inicio de semana - lunes)
        $startOfWeek = Carbon::parse($request->get('week', Carbon::now()))->startOfWeek(Carbon::MONDAY);
        $duration = (int) env('APPOINTMENT_DURATION_MINUTES', 20);

        // Para simplicidad, si se selecciona un médico vía query, calcula disponibilidad
        $selectedSlug = $request->get('doctor');
        $selectedDoctor = $selectedSlug ? Doctor::where('slug', $selectedSlug)->first() : null;
        $availability = $selectedDoctor ? $this->calcularDisponibilidad($selectedDoctor, $startOfWeek, $duration) : [];

        return Inertia::render('Public/Index', [
            'doctors' => $doctors,
            'selectedDoctor' => $selectedDoctor,
            'weekStart' => $startOfWeek->toDateString(),
            'duration' => $duration,
            'availability' => $availability,
        ]);
    }

    /**
     * Muestra el perfil del médico y próximos espacios disponibles.
     */
    public function doctor(Doctor $doctor, Request $request)
    {
        $startOfWeek = Carbon::parse($request->get('week', Carbon::now()))->startOfWeek(Carbon::MONDAY);
        $duration = (int) env('APPOINTMENT_DURATION_MINUTES', 20);
        $availability = $this->calcularDisponibilidad($doctor, $startOfWeek, $duration);

        return Inertia::render('Public/Doctor', [
            'doctor' => $doctor,
            'weekStart' => $startOfWeek->toDateString(),
            'duration' => $duration,
            'availability' => $availability,
        ]);
    }

    /**
     * Muestra el formulario para confirmar datos y reservar la cita.
     */
    public function appointmentNew(Request $request)
    {
        $doctor = Doctor::where('slug', $request->get('doctor'))->firstOrFail();
        $start = Carbon::parse($request->get('start'));
        $duration = (int) env('APPOINTMENT_DURATION_MINUTES', 20);

        return Inertia::render('Public/AppointmentNew', [
            'doctor' => $doctor,
            'start' => $start->toIso8601String(),
            'duration' => $duration,
        ]);
    }

    /**
     * Calcula la disponibilidad semanal del médico.
     * Solo retorna espacios no ocupados por citas pendientes o confirmadas.
     */
    protected function calcularDisponibilidad(Doctor $doctor, Carbon $startOfWeek, int $duration): array
    {
        $slots = [];
        // Construye un mapa de citas existentes para la semana (pendientes/confirmadas)
        $appointments = $doctor->appointments()
            ->whereBetween('start_at', [$startOfWeek, (clone $startOfWeek)->addDays(7)])
            ->whereIn('status', ['pendiente', 'confirmada'])
            ->get()
            ->keyBy(function ($a) {
                return Carbon::parse($a->start_at)->toIso8601String();
            });

        // Por cada disponibilidad declarada del médico, genera slots
        foreach ($doctor->availabilities as $avail) {
            $day = (clone $startOfWeek)->addDays($avail->weekday);
            $start = Carbon::parse($day->toDateString() . ' ' . $avail->start_time);
            $end = Carbon::parse($day->toDateString() . ' ' . $avail->end_time);

            while ($start->lt($end)) {
                $slotStart = $start->copy();
                $slotEnd = $start->copy()->addMinutes($duration);
                if ($slotEnd->gt($end)) {
                    break;
                }
                $key = $slotStart->toIso8601String();
                // Excluye slots que choquen con citas existentes
                if (!$appointments->has($key)) {
                    $slots[] = [
                        'start' => $slotStart->toIso8601String(),
                        'end' => $slotEnd->toIso8601String(),
                        'doctor' => $doctor->slug,
                    ];
                }
                $start->addMinutes($duration);
            }
        }
        // Ordena por fecha de inicio
        usort($slots, fn($a, $b) => strcmp($a['start'], $b['start']));
        return $slots;
    }
}
