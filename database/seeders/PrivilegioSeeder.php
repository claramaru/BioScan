<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PrivilegioSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('privilegio')->upsert([
            ['id_privilegio' => 1, 'nombre' => 'ver_animal', 'descripcion' => 'Ver animales'],
            ['id_privilegio' => 2, 'nombre' => 'crear_animal', 'descripcion' => 'Crear animales'],
            ['id_privilegio' => 3, 'nombre' => 'editar_animal', 'descripcion' => 'Editar animales'],
            ['id_privilegio' => 4, 'nombre' => 'borrar_animal', 'descripcion' => 'Eliminar animales'],
            ['id_privilegio' => 5, 'nombre' => 'ver_alimentacion', 'descripcion' => 'Ver registros de alimentación'],
            ['id_privilegio' => 6, 'nombre' => 'crear_alimentacion', 'descripcion' => 'Crear registros de alimentación'],
            ['id_privilegio' => 7, 'nombre' => 'editar_alimentacion', 'descripcion' => 'Editar registros de alimentación'],
            ['id_privilegio' => 8, 'nombre' => 'borrar_alimentacion', 'descripcion' => 'Eliminar registros de alimentación'],
            ['id_privilegio' => 9, 'nombre' => 'ver_ficha_medica', 'descripcion' => 'Ver fichas médicas'],
            ['id_privilegio' => 10, 'nombre' => 'crear_ficha_medica', 'descripcion' => 'Crear fichas médicas'],
            ['id_privilegio' => 11, 'nombre' => 'editar_ficha_medica', 'descripcion' => 'Editar ficha médica completa'],
            ['id_privilegio' => 12, 'nombre' => 'editar_observaciones_ficha_medica', 'descripcion' => 'Editar solo observaciones'],
            ['id_privilegio' => 13, 'nombre' => 'gestionar_usuario', 'descripcion' => 'Gestionar usuarios'],
            ['id_privilegio' => 14, 'nombre' => 'gestionar_rol', 'descripcion' => 'Gestionar roles y privilegios'],
            ['id_privilegio' => 15, 'nombre' => 'gestionar_pienso', 'descripcion' => null],
            ['id_privilegio' => 16, 'nombre' => 'ver_cebadero', 'descripcion' => 'Ver cebaderos'],
            ['id_privilegio' => 17, 'nombre' => 'crear_cebadero', 'descripcion' => 'Crear cebaderos'],
            ['id_privilegio' => 18, 'nombre' => 'editar_cebadero', 'descripcion' => 'Editar cebaderos'],
            ['id_privilegio' => 19, 'nombre' => 'borrar_cebadero', 'descripcion' => 'Eliminar cebaderos'],
            ['id_privilegio' => 20, 'nombre' => 'borrar_ficha_medica', 'descripcion' => 'Eliminar fichas medicas'],
            ['id_privilegio' => 21, 'nombre' => 'gestionar_ficha_medica_completa', 'descripcion' => 'Gestionar diagnostico y tratamiento'],
        ], ['id_privilegio'], ['nombre', 'descripcion']);
    }
}
