<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolPrivilegioSeeder extends Seeder
{
    public function run(): void
    {
        $roles = DB::table('rol')->pluck('id_rol', 'nombre');
        $privilegios = DB::table('privilegio')->pluck('id_privilegio', 'nombre');

        $privilegiosPorRol = [
            'administrador' => [
                'ver_animal', 'crear_animal', 'editar_animal', 'borrar_animal',
                'ver_alimentacion', 'crear_alimentacion', 'editar_alimentacion', 'borrar_alimentacion',
                'ver_ficha_medica', 'crear_ficha_medica', 'editar_ficha_medica',
                'editar_observaciones_ficha_medica', 'borrar_ficha_medica', 'gestionar_ficha_medica_completa',
                'gestionar_usuario', 'gestionar_rol', 'gestionar_pienso',
                'ver_cebadero', 'crear_cebadero', 'editar_cebadero', 'borrar_cebadero',
            ],
            'supervisor' => [
                'ver_animal', 'crear_animal', 'editar_animal',
                'ver_alimentacion', 'crear_alimentacion', 'editar_alimentacion', 'borrar_alimentacion',
                'ver_ficha_medica', 'crear_ficha_medica',
                'gestionar_pienso',
                'ver_cebadero', 'editar_cebadero',
            ],
            'operario' => [
                'ver_animal',
                'ver_alimentacion', 'crear_alimentacion',
                'ver_ficha_medica', 'crear_ficha_medica', 'editar_observaciones_ficha_medica',
                'ver_cebadero',
            ],
            'veterinario' => [
                'ver_animal', 'editar_animal',
                'ver_alimentacion', 'crear_alimentacion', 'editar_alimentacion', 'borrar_alimentacion',
                'ver_ficha_medica', 'crear_ficha_medica', 'editar_ficha_medica',
                'editar_observaciones_ficha_medica', 'borrar_ficha_medica', 'gestionar_ficha_medica_completa',
                'gestionar_pienso',
                'ver_cebadero',
            ],
            'invitado' => [
                'ver_animal',
                'ver_alimentacion',
                'ver_ficha_medica',
                'ver_cebadero',
            ],
        ];

        $relaciones = [];

        foreach ($privilegiosPorRol as $rol => $nombresPrivilegios) {
            if (!$roles->has($rol)) {
                continue;
            }

            foreach ($nombresPrivilegios as $privilegio) {
                if (!$privilegios->has($privilegio)) {
                    continue;
                }

                $relaciones[] = [
                    'id_rol' => $roles[$rol],
                    'id_privilegio' => $privilegios[$privilegio],
                ];
            }
        }

        DB::table('rol_privilegio')->insertOrIgnore($relaciones);
    }
}
