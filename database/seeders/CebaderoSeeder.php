<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CebaderoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('cebadero')->insert([
            ['id_cebadero' => 1, 'nombre' => 'Cebadero Norte', 'ubicacion' => 'Murcia - Zona Norte'],
            ['id_cebadero' => 2, 'nombre' => 'Cebadero Sur', 'ubicacion' => 'Cartagena - Zona Sur'],
            ['id_cebadero' => 3, 'nombre' => 'Cebadero Cartagena', 'ubicacion' => 'Cartagena'],
        ]);
    }
}
