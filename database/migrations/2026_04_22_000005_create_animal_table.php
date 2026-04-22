<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('animal', function (Blueprint $table) {
            $table->increments('id_animal');
            $table->string('codigo', 50)->unique();
            $table->string('especie', 50)->nullable();
            $table->string('tipo_pienso_recomendado', 50)->nullable();
            $table->string('lote', 50)->nullable();
            $table->date('fecha_alta')->nullable();
            $table->text('observaciones')->nullable();
            $table->unsignedInteger('id_cebadero')->nullable();

            $table->foreign('id_cebadero')->references('id_cebadero')->on('cebadero');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('animal');
    }
};
