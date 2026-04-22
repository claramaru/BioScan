<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $idRol = DB::table('rol')->where('nombre', 'operario')->value('id_rol');
        $idPrivilegio = DB::table('privilegio')->where('nombre', 'crear_alimentacion')->value('id_privilegio');

        if (!$idRol || !$idPrivilegio) {
            return;
        }

        $existe = DB::table('rol_privilegio')
            ->where('id_rol', $idRol)
            ->where('id_privilegio', $idPrivilegio)
            ->exists();

        if (!$existe) {
            DB::table('rol_privilegio')->insert([
                'id_rol' => $idRol,
                'id_privilegio' => $idPrivilegio,
            ]);
        }
    }

    public function down(): void
    {
        $idRol = DB::table('rol')->where('nombre', 'operario')->value('id_rol');
        $idPrivilegio = DB::table('privilegio')->where('nombre', 'crear_alimentacion')->value('id_privilegio');

        if (!$idRol || !$idPrivilegio) {
            return;
        }

        DB::table('rol_privilegio')
            ->where('id_rol', $idRol)
            ->where('id_privilegio', $idPrivilegio)
            ->delete();
    }
};
