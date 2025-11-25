<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Carbon\Carbon;
use App\Mail\AppointmentAcceptedMail;
use App\Mail\AppointmentRejectedMail;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Listado de citas con filtros por estado y médico
        $appointments = Appointment::query()
            ->latest('start_at')
            ->with('doctor')
            ->paginate(15);
        return Inertia::render('Appointments/Index', [
            'appointments' => $appointments,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Formulario de creación (panel)
        $doctors = Doctor::orderBy('name')->get();
        return Inertia::render('Appointments/Create', [
            'doctors' => $doctors,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Creación de cita desde panel (opcional)
        $data = $request->validate([
            'doctor_id' => ['required','exists:doctors,id'],
            'patient_name' => ['required','string','max:255'],
            'patient_email' => ['required','email'],
            'start_at' => ['required','date'],
            'notes' => ['nullable','string'],
        ]);
        $duration = (int) env('APPOINTMENT_DURATION_MINUTES', 20);
        $start = Carbon::parse($data['start_at']);
        $end = (clone $start)->addMinutes($duration);
        // Valida colisión
        $collision = Appointment::where('doctor_id', $data['doctor_id'])
            ->where('start_at', $start)
            ->whereIn('status', ['pendiente', 'confirmada'])
            ->exists();
        if ($collision) {
            return back()->withErrors(['start_at' => 'Ese horario ya está ocupado para este médico.']);
        }
        Appointment::create([
            'doctor_id' => $data['doctor_id'],
            'slug' => \Illuminate\Support\Str::uuid()->toString(),
            'patient_name' => $data['patient_name'],
            'patient_email' => $data['patient_email'],
            'start_at' => $start,
            'end_at' => $end,
            'status' => 'pendiente',
            'notes' => $data['notes'] ?? null,
        ]);
        return redirect()->route('panel.appointments.index')->with('success', 'Cita creada en pendiente');
    }

    /**
     * Display the specified resource.
     */
    public function show(Appointment $appointment)
    {
        // Muestra detalle de la cita
        return Inertia::render('Appointments/Show', [
            'appointment' => $appointment->load('doctor'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Appointment $appointment)
    {
        // Formulario de edición
        $doctors = Doctor::orderBy('name')->get();
        return Inertia::render('Appointments/Edit', [
            'appointment' => $appointment,
            'doctors' => $doctors,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Appointment $appointment)
    {
        // Actualiza datos básicos de la cita (no estado)
        $data = $request->validate([
            'patient_name' => ['required','string','max:255'],
            'patient_email' => ['required','email'],
            'notes' => ['nullable','string'],
        ]);
        $appointment->update($data);
        return redirect()->route('panel.appointments.show', $appointment)->with('success', 'Cita actualizada');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Appointment $appointment)
    {
        // Cancela/elimina la cita
        $appointment->delete();
        return redirect()->route('panel.appointments.index')->with('success', 'Cita eliminada');
    }

    /**
     * Resumen de pendientes y próximas confirmadas.
     */
    public function home(Request $request)
    {
        $doctorSlug = $request->get('doctor');
        $query = Appointment::query()->with('doctor');
        if ($doctorSlug) {
            $doctor = Doctor::where('slug', $doctorSlug)->first();
            if ($doctor) {
                $query->where('doctor_id', $doctor->id);
            }
        }
        $pending = (clone $query)->where('status', 'pendiente')->orderBy('start_at')->limit(20)->get();
        $upcoming = (clone $query)->where('status', 'confirmada')->where('start_at', '>=', Carbon::now())->orderBy('start_at')->limit(20)->get();
        $doctors = Doctor::orderBy('name')->get();
        return Inertia::render('Admin/Home', [
            'pending' => $pending,
            'upcoming' => $upcoming,
            'doctors' => $doctors,
        ]);
    }

    /**
     * Calendario semanal del panel: muestra citas pendientes y confirmadas.
     */
    public function calendar(Request $request)
    {
        $doctor = Doctor::where('slug', $request->get('doctor'))->firstOrFail();
        $startOfWeek = Carbon::parse($request->get('week', Carbon::now()))->startOfWeek(Carbon::MONDAY);
        $weekAppointments = $doctor->appointments()
            ->whereBetween('start_at', [$startOfWeek, (clone $startOfWeek)->addDays(7)])
            ->orderBy('start_at')
            ->get();
        return Inertia::render('Admin/Calendar', [
            'doctor' => $doctor,
            'weekStart' => $startOfWeek->toDateString(),
            'appointments' => $weekAppointments,
        ]);
    }

    /**
     * Acepta una cita pendiente y envía email.
     */
    public function accept(Appointment $appointment)
    {
        if ($appointment->status !== 'pendiente') {
            return back()->withErrors(['status' => 'Solo se pueden aceptar citas pendientes.']);
        }
        $appointment->update(['status' => 'confirmada']);
        Mail::to($appointment->patient_email)->send(new AppointmentAcceptedMail($appointment));
        return back()->with('success', 'Cita aceptada');
    }

    /**
     * Rechaza una cita pendiente y envía email.
     */
    public function reject(Appointment $appointment)
    {
        if ($appointment->status !== 'pendiente') {
            return back()->withErrors(['status' => 'Solo se pueden rechazar citas pendientes.']);
        }
        $appointment->update(['status' => 'rechazada']);
        Mail::to($appointment->patient_email)->send(new AppointmentRejectedMail($appointment));
        return back()->with('success', 'Cita rechazada');
    }
}
