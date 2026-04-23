<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alimentacion', function (Blueprint $table) {
            $table->unsignedInteger('id_pienso')->nullable()->after('id_animal');

            $table->foreign('id_pienso')
                ->references('id_pienso')
                ->on('pienso');
        });
    }

    public function down(): void
    {
        Schema::table('alimentacion', function (Blueprint $table) {
            $table->dropForeign(['id_pienso']);
            $table->dropColumn('id_pienso');
        });
    }
};
