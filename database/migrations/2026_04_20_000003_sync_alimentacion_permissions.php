<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $roles = DB::table('rol')->pluck('id_rol', 'nombre');
        $privilegios = DB::table('privilegio')->pluck('id_privilegio', 'nombre');
        $config = [
            'crear_alimentacion' => ['administrador', 'supervisor', 'veterinario', 'operario'],
            'editar_alimentacion' => ['administrador', 'supervisor', 'veterinario'],
            'borrar_alimentacion' => ['administrador', 'supervisor', 'veterinario'],
        ];

        foreach ($config as $privilegioNombre => $permitidos) {
            $idPrivilegio = $privilegios[$privilegioNombre] ?? null;
            if (!$idPrivilegio) {
                continue;
            }

            DB::table('rol_privilegio')
                ->where('id_privilegio', $idPrivilegio)
                ->delete();

            foreach ($permitidos as $rolNombre) {
                $idRol = $roles[$rolNombre] ?? null;
                if (!$idRol) {
                    continue;
                }

                DB::table('rol_privilegio')->insert([
                    'id_rol' => $idRol,
                    'id_privilegio' => $idPrivilegio,
                ]);
            }
        }
    }

    public function down(): void
    {
        $roles = DB::table('rol')->pluck('id_rol', 'nombre');
        $privilegios = DB::table('privilegio')->pluck('id_privilegio', 'nombre');

        $restaurar = [
            'crear_alimentacion' => ['administrador', 'supervisor', 'veterinario'],
            'editar_alimentacion' => ['administrador', 'supervisor', 'veterinario'],
            'borrar_alimentacion' => ['administrador', 'supervisor', 'veterinario'],
        ];

        foreach ($restaurar as $privilegioNombre => $rolesPermitidos) {
            $idPrivilegio = $privilegios[$privilegioNombre] ?? null;
            if (!$idPrivilegio) {
                continue;
            }

            DB::table('rol_privilegio')
                ->where('id_privilegio', $idPrivilegio)
                ->delete();

            foreach ($rolesPermitidos as $rolNombre) {
                $idRol = $roles[$rolNombre] ?? null;
                if (!$idRol) {
                    continue;
                }

                DB::table('rol_privilegio')->insert([
                    'id_rol' => $idRol,
                    'id_privilegio' => $idPrivilegio,
                ]);
            }
        }
    }
};
