<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('animal', function (Blueprint $table) {
            $table->string('raza', 120)->nullable()->after('especie');
            $table->unsignedInteger('id_pienso_recomendado')->nullable()->after('raza');

            $table->foreign('id_pienso_recomendado')
                ->references('id_pienso')
                ->on('pienso');
        });
    }

    public function down(): void
    {
        Schema::table('animal', function (Blueprint $table) {
            $table->dropForeign(['id_pienso_recomendado']);
            $table->dropColumn(['id_pienso_recomendado', 'raza']);
        });
    }
};
