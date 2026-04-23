<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cebadero', function (Blueprint $table) {
            $table->increments('id_cebadero');
            $table->string('nombre', 100);
            $table->string('ubicacion', 150)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cebadero');
    }
};
