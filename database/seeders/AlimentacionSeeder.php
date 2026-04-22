<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AlimentacionSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('alimentacion')->insert([
            ['id_alimentacion' => 1, 'id_animal' => 1, 'id_pienso' => 1, 'tipo_pienso' => 'Pienso crecimiento', 'cantidad' => 25.50, 'fecha' => '2025-02-10', 'id_usuario' => 1],
            ['id_alimentacion' => 2, 'id_animal' => 1, 'id_pienso' => 1, 'tipo_pienso' => 'Pienso crecimiento', 'cantidad' => 26.00, 'fecha' => '2025-02-11', 'id_usuario' => 2],
            ['id_alimentacion' => 3, 'id_animal' => 2, 'id_pienso' => 2, 'tipo_pienso' => 'Pienso engorde', 'cantidad' => 30.00, 'fecha' => '2025-02-10', 'id_usuario' => 3],
            ['id_alimentacion' => 4, 'id_animal' => 3, 'id_pienso' => 3, 'tipo_pienso' => 'Pienso mantenimiento', 'cantidad' => 18.75, 'fecha' => '2025-02-10', 'id_usuario' => 4],
            ['id_alimentacion' => 6, 'id_animal' => 17, 'id_pienso' => 1, 'tipo_pienso' => 'Pienso crecimiento', 'cantidad' => 2.00, 'fecha' => '2026-04-20', 'id_usuario' => 4],
            ['id_alimentacion' => 7, 'id_animal' => 10, 'id_pienso' => 1, 'tipo_pienso' => 'Pienso crecimiento', 'cantidad' => 2.00, 'fecha' => '2026-04-20', 'id_usuario' => 4],
        ]);
    }
}
