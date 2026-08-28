<?php

namespace Database\Seeders;

use App\Models\Lane;
use App\Models\Schedule;
use Illuminate\Database\Seeder;

class PoolSeeder extends Seeder
{
    public function run(): void
    {
        // Crear Carriles iniciales
        Lane::create(['name' => 'Carril 1 (Rápido)', 'description' => 'Para entrenamiento intensivo y series rápidas.', 'is_active' => true]);
        Lane::create(['name' => 'Carril 2 (Intermedio)', 'description' => 'Ritmo continuo y técnica.', 'is_active' => true]);
        Lane::create(['name' => 'Carril 3 (Libre / Recreativo)', 'description' => 'Nado libre y calentamiento.', 'is_active' => true]);
        Lane::create(['name' => 'Foso / Técnica', 'description' => 'Área para ejercicios de patada y técnica.', 'is_active' => true]);

        // Crear Franjas Horarias iniciales (Bloques de 1 hora)
        $schedules = [
            ['start_time' => '07:00:00', 'end_time' => '08:00:00', 'max_capacity' => 6],
            ['start_time' => '08:00:00', 'end_time' => '09:00:00', 'max_capacity' => 6],
            ['start_time' => '09:00:00', 'end_time' => '10:00:00', 'max_capacity' => 6],
            ['start_time' => '16:00:00', 'end_time' => '17:00:00', 'max_capacity' => 6],
            ['start_time' => '17:00:00', 'end_time' => '18:00:00', 'max_capacity' => 6],
            ['start_time' => '18:00:00', 'end_time' => '19:00:00', 'max_capacity' => 6],
        ];

        foreach ($schedules as $schedule) {
            Schedule::create($schedule);
        }
    }
}