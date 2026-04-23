<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ficha_medica', function (Blueprint $table) {
            $table->increments('id_ficha');
            $table->unsignedInteger('id_animal');
            $table->unsignedInteger('id_usuario')->nullable();
            $table->text('diagnostico')->nullable();
            $table->text('tratamiento')->nullable();
            $table->text('observaciones')->nullable();
            $table->date('fecha');

            $table->foreign('id_animal')->references('id_animal')->on('animal');
            $table->foreign('id_usuario')->references('id_usuario')->on('usuario');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ficha_medica');
    }
};
