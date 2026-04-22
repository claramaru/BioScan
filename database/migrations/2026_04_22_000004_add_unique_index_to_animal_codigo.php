<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $duplicados = DB::table('animal')
            ->select('codigo', DB::raw('COUNT(*) as total'))
            ->groupBy('codigo')
            ->havingRaw('COUNT(*) > 1')
            ->count();

        if ($duplicados > 0) {
            throw new RuntimeException('No se puede crear el indice unico de animal.codigo porque existen codigos duplicados.');
        }

        Schema::table('animal', function (Blueprint $table) {
            $table->unique('codigo', 'animal_codigo_unique');
        });
    }

    public function down(): void
    {
        Schema::table('animal', function (Blueprint $table) {
            $table->dropUnique('animal_codigo_unique');
        });
    }
};
