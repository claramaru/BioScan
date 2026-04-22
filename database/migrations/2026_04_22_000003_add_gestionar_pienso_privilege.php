<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('privilegio')->updateOrInsert(
            ['nombre' => 'gestionar_pienso'],
            []
        );

        $idPrivilegio = DB::table('privilegio')->where('nombre', 'gestionar_pienso')->value('id_privilegio');
        $roles = DB::table('rol')
            ->whereIn('nombre', ['administrador', 'supervisor', 'veterinario'])
            ->pluck('id_rol');

        foreach ($roles as $idRol) {
            DB::table('rol_privilegio')->updateOrInsert([
                'id_rol' => $idRol,
                'id_privilegio' => $idPrivilegio,
            ], []);
        }
    }

    public function down(): void
    {
        $idPrivilegio = DB::table('privilegio')->where('nombre', 'gestionar_pienso')->value('id_privilegio');
        if (!$idPrivilegio) {
            return;
        }

        DB::table('rol_privilegio')
            ->where('id_privilegio', $idPrivilegio)
            ->delete();

        DB::table('privilegio')
            ->where('id_privilegio', $idPrivilegio)
            ->delete();
    }
};
