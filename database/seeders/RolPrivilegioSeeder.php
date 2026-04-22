<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolPrivilegioSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('rol_privilegio')->insert([
            ['id_rol' => 1, 'id_privilegio' => 1], ['id_rol' => 1, 'id_privilegio' => 2],
            ['id_rol' => 1, 'id_privilegio' => 3], ['id_rol' => 1, 'id_privilegio' => 4],
            ['id_rol' => 1, 'id_privilegio' => 5], ['id_rol' => 1, 'id_privilegio' => 6],
            ['id_rol' => 1, 'id_privilegio' => 7], ['id_rol' => 1, 'id_privilegio' => 8],
            ['id_rol' => 1, 'id_privilegio' => 9], ['id_rol' => 1, 'id_privilegio' => 10],
            ['id_rol' => 1, 'id_privilegio' => 11], ['id_rol' => 1, 'id_privilegio' => 12],
            ['id_rol' => 1, 'id_privilegio' => 13], ['id_rol' => 1, 'id_privilegio' => 14],
            ['id_rol' => 1, 'id_privilegio' => 15],
            ['id_rol' => 2, 'id_privilegio' => 1], ['id_rol' => 2, 'id_privilegio' => 2],
            ['id_rol' => 2, 'id_privilegio' => 3], ['id_rol' => 2, 'id_privilegio' => 5],
            ['id_rol' => 2, 'id_privilegio' => 6], ['id_rol' => 2, 'id_privilegio' => 7],
            ['id_rol' => 2, 'id_privilegio' => 8], ['id_rol' => 2, 'id_privilegio' => 9],
            ['id_rol' => 2, 'id_privilegio' => 10], ['id_rol' => 2, 'id_privilegio' => 15],
            ['id_rol' => 3, 'id_privilegio' => 1], ['id_rol' => 3, 'id_privilegio' => 5],
            ['id_rol' => 3, 'id_privilegio' => 6], ['id_rol' => 3, 'id_privilegio' => 9],
            ['id_rol' => 3, 'id_privilegio' => 12],
            ['id_rol' => 4, 'id_privilegio' => 1], ['id_rol' => 4, 'id_privilegio' => 3],
            ['id_rol' => 4, 'id_privilegio' => 5], ['id_rol' => 4, 'id_privilegio' => 6],
            ['id_rol' => 4, 'id_privilegio' => 7], ['id_rol' => 4, 'id_privilegio' => 8],
            ['id_rol' => 4, 'id_privilegio' => 9], ['id_rol' => 4, 'id_privilegio' => 10],
            ['id_rol' => 4, 'id_privilegio' => 11], ['id_rol' => 4, 'id_privilegio' => 12],
            ['id_rol' => 4, 'id_privilegio' => 15],
            ['id_rol' => 5, 'id_privilegio' => 1], ['id_rol' => 5, 'id_privilegio' => 5],
            ['id_rol' => 5, 'id_privilegio' => 9],
        ]);
    }
}
