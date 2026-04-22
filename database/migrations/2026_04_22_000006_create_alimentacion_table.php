<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('alimentacion', function (Blueprint $table) {
            $table->increments('id_alimentacion');
            $table->unsignedInteger('id_animal')->nullable();
            $table->string('tipo_pienso', 50)->nullable();
            $table->decimal('cantidad', 6, 2)->nullable();
            $table->date('fecha')->nullable();
            $table->unsignedInteger('id_usuario')->nullable();

            $table->foreign('id_animal')->references('id_animal')->on('animal');
            $table->foreign('id_usuario')->references('id_usuario')->on('usuario');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alimentacion');
    }
};
