<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('usuario', function (Blueprint $table) {
            $table->increments('id_usuario');
            $table->string('nombre', 50);
            $table->string('apellidos', 100)->nullable();
            $table->string('email', 100)->unique();
            $table->unsignedInteger('id_rol');
            $table->string('password', 255);
            $table->rememberToken();
            $table->timestamp('email_verified_at')->nullable();

            $table->foreign('id_rol')->references('id_rol')->on('rol');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuario');
    }
};
