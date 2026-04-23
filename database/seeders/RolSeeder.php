<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('rol')->upsert([
            ['id_rol' => 1, 'nombre' => 'administrador', 'descripcion' => 'Control total del sistema'],
            ['id_rol' => 2, 'nombre' => 'supervisor', 'descripcion' => 'Supervisa procesos y consulta información'],
            ['id_rol' => 3, 'nombre' => 'operario', 'descripcion' => 'Registra alimentación y tareas básicas'],
            ['id_rol' => 4, 'nombre' => 'veterinario', 'descripcion' => 'Gestiona fichas médicas y control sanitario'],
            ['id_rol' => 5, 'nombre' => 'invitado', 'descripcion' => 'Acceso solo lectura'],
        ], ['id_rol'], ['nombre', 'descripcion']);
    }
}
