<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('animal', function (Blueprint $table) {
            $table->index('fecha_alta', 'animal_fecha_alta_index');
            $table->index('especie', 'animal_especie_index');
            $table->index(['id_cebadero', 'fecha_alta'], 'animal_cebadero_fecha_alta_index');
        });

        Schema::table('alimentacion', function (Blueprint $table) {
            $table->index('fecha', 'alimentacion_fecha_index');
            $table->index(['id_animal', 'fecha'], 'alimentacion_animal_fecha_index');
        });

        Schema::table('ficha_medica', function (Blueprint $table) {
            $table->index('fecha', 'ficha_medica_fecha_index');
            $table->index(['id_animal', 'fecha'], 'ficha_medica_animal_fecha_index');
        });
    }

    public function down(): void
    {
        Schema::table('ficha_medica', function (Blueprint $table) {
            $table->dropIndex('ficha_medica_animal_fecha_index');
            $table->dropIndex('ficha_medica_fecha_index');
        });

        Schema::table('alimentacion', function (Blueprint $table) {
            $table->dropIndex('alimentacion_animal_fecha_index');
            $table->dropIndex('alimentacion_fecha_index');
        });

        Schema::table('animal', function (Blueprint $table) {
            $table->dropIndex('animal_cebadero_fecha_alta_index');
            $table->dropIndex('animal_especie_index');
            $table->dropIndex('animal_fecha_alta_index');
        });
    }
};
