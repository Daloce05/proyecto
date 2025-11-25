<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class DoctorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Lista de médicos con filtros simples
        $doctors = Doctor::query()->orderBy('name')->get();
        return Inertia::render('Doctors/Index', [
            'doctors' => $doctors,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Formulario de creación
        return Inertia::render('Doctors/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Valida y crea un médico nuevo
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['nullable','email'],
            'specialty' => ['required','string','max:255'],
        ]);

        $doctor = Doctor::create([
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'specialty' => $data['specialty'],
            'slug' => Str::slug($data['name'].'-'.Str::random(6)),
        ]);

        return redirect()->route('panel.doctors.index')->with('success', 'Médico creado');
    }

    /**
     * Display the specified resource.
     */
    public function show(Doctor $doctor)
    {
        // Muestra detalle básico del médico
        return Inertia::render('Doctors/Show', [
            'doctor' => $doctor,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Doctor $doctor)
    {
        // Formulario de edición
        return Inertia::render('Doctors/Edit', [
            'doctor' => $doctor,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Doctor $doctor)
    {
        // Valida y actualiza datos del médico
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['nullable','email'],
            'specialty' => ['required','string','max:255'],
        ]);
        $doctor->update($data);
        // Si cambia el nombre, opcionalmente actualizar el slug (mantener enlaces permanentes en producción)
        // $doctor->update(['slug' => Str::slug($data['name'].'-'.Str::random(6))]);
        return redirect()->route('panel.doctors.index')->with('success', 'Médico actualizado');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Doctor $doctor)
    {
        // Elimina el médico
        $doctor->delete();
        return redirect()->route('panel.doctors.index')->with('success', 'Médico eliminado');
    }
}
