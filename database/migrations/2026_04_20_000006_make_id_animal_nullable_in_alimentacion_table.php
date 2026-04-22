<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE alimentacion MODIFY id_animal INT(11) NULL');
    }

    public function down(): void
    {
        DB::statement('UPDATE alimentacion SET id_animal = 1 WHERE id_animal IS NULL');
        DB::statement('ALTER TABLE alimentacion MODIFY id_animal INT(11) NOT NULL');
    }
};
