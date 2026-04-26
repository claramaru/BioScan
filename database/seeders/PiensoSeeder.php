<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PiensoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('pienso')->upsert([
            ['id_pienso' => 1, 'nombre' => 'Pienso crecimiento', 'activo' => 1],
            ['id_pienso' => 2, 'nombre' => 'Pienso engorde', 'activo' => 1],
            ['id_pienso' => 3, 'nombre' => 'Pienso mantenimiento', 'activo' => 1],
            ['id_pienso' => 4, 'nombre' => 'Pienso adaptacion', 'activo' => 1],
        ], ['id_pienso'], ['nombre', 'activo']);
    }
}
