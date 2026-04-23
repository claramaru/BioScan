<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FichaMedicaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('ficha_medica')->upsert([
            ['id_ficha' => 1, 'id_animal' => 1, 'id_usuario' => 1, 'diagnostico' => 'Leve infección respiratoria', 'tratamiento' => 'Antibiótico 5 días', 'observaciones' => 'Control semanal y aislamiento', 'fecha' => '2025-02-12'],
            ['id_ficha' => 2, 'id_animal' => 2, 'id_usuario' => 2, 'diagnostico' => 'Cojera leve', 'tratamiento' => 'Anti-inflamatorio 3 días', 'observaciones' => 'Revisar suelo del corral', 'fecha' => '2025-02-13'],
            ['id_ficha' => 3, 'id_animal' => 3, 'id_usuario' => 3, 'diagnostico' => 'Deshidratación', 'tratamiento' => 'Suero + control de agua', 'observaciones' => 'Vigilar bebederos', 'fecha' => '2025-02-14'],
            ['id_ficha' => 4, 'id_animal' => 4, 'id_usuario' => 4, 'diagnostico' => '', 'tratamiento' => '', 'observaciones' => '', 'fecha' => '2026-02-25'],
            ['id_ficha' => 7, 'id_animal' => 21, 'id_usuario' => 4, 'diagnostico' => '', 'tratamiento' => '', 'observaciones' => '', 'fecha' => '2026-04-21'],
        ], ['id_ficha'], ['id_animal', 'id_usuario', 'diagnostico', 'tratamiento', 'observaciones', 'fecha']);
    }
}
