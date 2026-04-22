<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('pienso')) {
            Schema::create('pienso', function (Blueprint $table) {
                $table->increments('id_pienso');
                $table->string('nombre', 120)->unique();
                $table->boolean('activo')->default(true);
            });
        }

        Schema::table('alimentacion', function (Blueprint $table) {
            if (!Schema::hasColumn('alimentacion', 'id_pienso')) {
                $table->integer('id_pienso')->nullable()->after('id_animal');
            }
        });

        Schema::table('animal', function (Blueprint $table) {
            if (!Schema::hasColumn('animal', 'id_pienso_recomendado')) {
                $table->integer('id_pienso_recomendado')->nullable()->after('raza');
            }
        });

        DB::transaction(function () {
            $nombres = collect();

            if (Schema::hasColumn('alimentacion', 'tipo_pienso')) {
                $nombres = $nombres->concat(
                    DB::table('alimentacion')
                        ->whereNotNull('tipo_pienso')
                        ->where('tipo_pienso', '!=', '')
                        ->pluck('tipo_pienso')
                );
            }

            if (Schema::hasColumn('animal', 'tipo_pienso_recomendado')) {
                $nombres = $nombres->concat(
                    DB::table('animal')
                        ->whereNotNull('tipo_pienso_recomendado')
                        ->where('tipo_pienso_recomendado', '!=', '')
                        ->pluck('tipo_pienso_recomendado')
                );
            }

            $nombres
                ->map(fn ($nombre) => trim((string) $nombre))
                ->filter()
                ->unique()
                ->sort()
                ->values()
                ->each(function ($nombre) {
                    DB::table('pienso')->updateOrInsert(
                        ['nombre' => $nombre],
                        ['activo' => true]
                    );
                });

            $piensos = DB::table('pienso')->pluck('id_pienso', 'nombre');

            if (Schema::hasColumn('alimentacion', 'tipo_pienso')) {
                DB::table('alimentacion')
                    ->select('id_alimentacion', 'tipo_pienso')
                    ->orderBy('id_alimentacion')
                    ->get()
                    ->each(function ($fila) use ($piensos) {
                        $nombre = trim((string) $fila->tipo_pienso);
                        if ($nombre === '' || !isset($piensos[$nombre])) {
                            return;
                        }

                        DB::table('alimentacion')
                            ->where('id_alimentacion', $fila->id_alimentacion)
                            ->update(['id_pienso' => $piensos[$nombre]]);
                    });
            }

            if (Schema::hasColumn('animal', 'tipo_pienso_recomendado')) {
                DB::table('animal')
                    ->select('id_animal', 'tipo_pienso_recomendado')
                    ->orderBy('id_animal')
                    ->get()
                    ->each(function ($fila) use ($piensos) {
                        $nombre = trim((string) $fila->tipo_pienso_recomendado);
                        if ($nombre === '' || !isset($piensos[$nombre])) {
                            return;
                        }

                        DB::table('animal')
                            ->where('id_animal', $fila->id_animal)
                            ->update(['id_pienso_recomendado' => $piensos[$nombre]]);
                    });
            }
        });
    }

    public function down(): void
    {
        Schema::table('animal', function (Blueprint $table) {
            if (Schema::hasColumn('animal', 'id_pienso_recomendado')) {
                $table->dropColumn('id_pienso_recomendado');
            }
        });

        Schema::table('alimentacion', function (Blueprint $table) {
            if (Schema::hasColumn('alimentacion', 'id_pienso')) {
                $table->dropColumn('id_pienso');
            }
        });

        Schema::dropIfExists('pienso');
    }
};
