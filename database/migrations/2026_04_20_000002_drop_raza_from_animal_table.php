<?php

use Illuminate\Database\Migrations\Migration;
return new class extends Migration
{
    public function up(): void
    {
        // Migracion vacia dejada a proposito.
        // Se conserva el fichero para no romper el historial ya ejecutado,
        // pero la columna `raza` nunca debio formar parte del esquema final.
    }

    public function down(): void
    {
        // Sin cambios que revertir.
    }
};
