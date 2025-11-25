<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\Doctor;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\PublicAppointmentController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\AppointmentController;

// Landing inicial: elección de rol (Paciente / Médico)
Route::get('/', function () {
    // Proporcionamos el slug del primer médico para permitir acceso rápido al calendario
    $firstDoctorSlug = Doctor::orderBy('name')->value('slug');
    return Inertia::render('Landing', [
        'isAuthenticated' => Auth::check(),
        'user' => Auth::user(),
        'firstDoctorSlug' => $firstDoctorSlug,
    ]);
})->name('landing');

// Rutas públicas (explorar disponibilidad y reservar)
// GET "/explorar": Selector de médico y calendario semanal de disponibilidad
Route::get('/explorar', [PublicController::class, 'index'])->name('public.index');
// GET "/doctors/{slug}": Perfil básico y próximos espacios disponibles
Route::get('/doctors/{doctor}', [PublicController::class, 'doctor'])->name('public.doctor');
// GET "/appointments/new": Formulario para confirmar datos y reservar
Route::get('/appointments/new', [PublicController::class, 'appointmentNew'])->name('public.appointments.new');
// POST "/appointments": Crea cita en pendiente; valida colisiones y disponibilidad y envía email
Route::post('/appointments', [PublicAppointmentController::class, 'store'])->name('public.appointments.store');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // Panel protegido (Jetstream)
    // GET "/home": Resumen de pendientes y próximas confirmadas con filtro por médico
    Route::get('/home', [AppointmentController::class, 'home'])->name('panel.home');

    // GET "/calendar": Calendario semanal con citas y huecos del médico (panel)
    Route::get('/calendar', [AppointmentController::class, 'calendar'])->name('panel.calendar');

    // Prefijo para evitar colisiones con rutas públicas
    Route::prefix('admin')->group(function () {
        // Resource "/admin/doctors": CRUD con slug
        Route::resource('/doctors', DoctorController::class)->names('panel.doctors');

        // Resource "/admin/appointments": Listado con filtros; acciones aceptar/rechazar/cancelar
        Route::resource('/appointments', AppointmentController::class)->names('panel.appointments');
        // Aceptar y rechazar citas pendientes
        Route::post('/appointments/{appointment}/accept', [AppointmentController::class, 'accept'])->name('panel.appointments.accept');
        Route::post('/appointments/{appointment}/reject', [AppointmentController::class, 'reject'])->name('panel.appointments.reject');
    });
});
