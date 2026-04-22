<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('animal', function (Blueprint $table) {
            $table->string('tipo_pienso_recomendado', 50)->nullable()->after('especie');
        });
    }

    public function down(): void
    {
        Schema::table('animal', function (Blueprint $table) {
            $table->dropColumn('tipo_pienso_recomendado');
        });
    }
};
