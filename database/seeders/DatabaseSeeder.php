<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Doctor;
use App\Models\DoctorAvailability;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crea un usuario administrador de ejemplo
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
        ]);

        // Crea 3 médicos activos de Neumología con slugs
        $doctors = Doctor::factory()->count(3)->create();

        // Para cada médico, crea disponibilidad estándar lunes-viernes 09:00-17:00
        foreach ($doctors as $doctor) {
            foreach ([0,1,2,3,4] as $weekday) { // lunes a viernes
                DoctorAvailability::create([
                    'doctor_id' => $doctor->id,
                    'weekday' => $weekday,
                    'start_time' => '09:00:00',
                    'end_time' => '17:00:00',
                ]);
            }
        }
    }
}
