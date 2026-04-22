<?php

namespace Database\Seeders;

use App\Models\Animal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AnimalBreedSeeder extends Seeder
{
    private const RAZAS = [
        'Avicola' => [
            'Pollo de engorde (broiler)',
            'Pavo de engorde',
            'Gallina africana (para carne)',
            'Pollo campero de engorde',
        ],
        'Porcino' => [
            'Cerdo ibérico de cebo',
            'Chato murciano',
            'Cerdo blanco de engorde',
            'Cerdo Duroc',
        ],
        'Vacuno' => [
            'Ternero de engorde',
            'Novillo',
            'Angus',
            'Ternera de carne',
        ],
    ];

    public function run(): void
    {
        $cebaderoIds = DB::table('cebadero')
            ->orderBy('id_cebadero')
            ->pluck('id_cebadero')
            ->values();

        if ($cebaderoIds->isEmpty()) {
            $this->command?->warn('No hay cebaderos disponibles. Se omite la carga de razas.');
            return;
        }

        DB::transaction(function () use ($cebaderoIds) {
            foreach (self::RAZAS as $especie => $razas) {
                $animales = Animal::where('especie', $especie)
                    ->orderBy('id_animal')
                    ->get();

                $this->asignarRazasExistentes($animales, $razas);
                $this->crearAnimalesFaltantes($especie, $razas, $animales->count(), $cebaderoIds);
            }
        });
    }

    private function asignarRazasExistentes(Collection $animales, array $razas): void
    {
        foreach ($animales->values() as $indice => $animal) {
            $animal->raza = $razas[$indice % count($razas)];
            $animal->save();
        }
    }

    private function crearAnimalesFaltantes(string $especie, array $razas, int $existentes, Collection $cebaderoIds): void
    {
        if ($existentes >= count($razas)) {
            return;
        }

        $faltan = array_slice($razas, $existentes);

        foreach (array_values($faltan) as $offset => $raza) {
            Animal::create([
                'codigo' => $this->generarCodigo($especie),
                'especie' => $especie,
                'raza' => $raza,
                'tipo_pienso_recomendado' => $this->resolverTipoPienso($especie),
                'lote' => $this->resolverLote($especie, $existentes + $offset),
                'fecha_alta' => now()->subDays($offset)->format('Y-m-d'),
                'observaciones' => 'Animal generado para cubrir la raza ' . $raza . '.',
                'id_cebadero' => $cebaderoIds[($existentes + $offset) % $cebaderoIds->count()],
            ]);
        }
    }

    private function generarCodigo(string $especie): string
    {
        $prefijo = match ($especie) {
            'Avicola' => 'AVI',
            'Porcino' => 'POR',
            'Vacuno' => 'VAC',
            default => 'ANI',
        };

        $numero = 1;
        do {
            $codigo = sprintf('%s-RAZA-%03d', $prefijo, $numero);
            $numero++;
        } while (Animal::where('codigo', $codigo)->exists());

        return $codigo;
    }

    private function resolverTipoPienso(string $especie): ?string
    {
        return DB::table('alimentacion')
            ->join('animal', 'alimentacion.id_animal', '=', 'animal.id_animal')
            ->where('animal.especie', $especie)
            ->whereNotNull('alimentacion.tipo_pienso')
            ->where('alimentacion.tipo_pienso', '!=', '')
            ->orderBy('alimentacion.tipo_pienso')
            ->value('alimentacion.tipo_pienso');
    }

    private function resolverLote(string $especie, int $indice): string
    {
        $prefijo = match ($especie) {
            'Avicola' => 'AVI',
            'Porcino' => 'POR',
            'Vacuno' => 'VAC',
            default => 'LOT',
        };

        return sprintf('%s-%03d', $prefijo, $indice + 1);
    }
}
