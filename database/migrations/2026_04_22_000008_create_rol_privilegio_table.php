<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rol_privilegio', function (Blueprint $table) {
            $table->unsignedInteger('id_rol');
            $table->unsignedInteger('id_privilegio');

            $table->primary(['id_rol', 'id_privilegio']);
            $table->foreign('id_rol')->references('id_rol')->on('rol');
            $table->foreign('id_privilegio')->references('id_privilegio')->on('privilegio');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rol_privilegio');
    }
};
