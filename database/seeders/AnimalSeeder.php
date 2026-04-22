<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AnimalSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('animal')->insert([
            ['id_animal' => 1, 'codigo' => 'ANM-001', 'especie' => 'Porcino', 'raza' => 'Cerdo ibérico de cebo', 'id_pienso_recomendado' => 1, 'tipo_pienso_recomendado' => 'Pienso crecimiento', 'lote' => 'L001', 'fecha_alta' => '2025-02-01', 'observaciones' => 'Ingreso estable. Revision inicial completada.', 'id_cebadero' => 1],
            ['id_animal' => 2, 'codigo' => 'ANM-002', 'especie' => 'Porcino', 'raza' => 'Chato murciano', 'id_pienso_recomendado' => 2, 'tipo_pienso_recomendado' => 'Pienso engorde', 'lote' => 'L001', 'fecha_alta' => '2025-02-03', 'observaciones' => 'Buen consumo de pienso durante la primera semana.', 'id_cebadero' => 1],
            ['id_animal' => 3, 'codigo' => 'ANM-003', 'especie' => 'Porcino', 'raza' => 'Cerdo blanco de engorde', 'id_pienso_recomendado' => 3, 'tipo_pienso_recomendado' => 'Pienso mantenimiento', 'lote' => 'L002', 'fecha_alta' => '2025-02-05', 'observaciones' => 'Se recomienda seguimiento de peso quincenal.', 'id_cebadero' => 2],
            ['id_animal' => 4, 'codigo' => 'ANM-004', 'especie' => 'Vacuno', 'raza' => 'Ternero de engorde', 'id_pienso_recomendado' => 3, 'tipo_pienso_recomendado' => 'Pienso mantenimiento', 'lote' => 'L004', 'fecha_alta' => '2025-02-06', 'observaciones' => 'Adaptacion correcta al nuevo cebadero.', 'id_cebadero' => 2],
            ['id_animal' => 5, 'codigo' => 'ANM-005', 'especie' => 'Porcino', 'raza' => 'Cerdo Duroc', 'id_pienso_recomendado' => 1, 'tipo_pienso_recomendado' => 'Pienso crecimiento', 'lote' => 'L002', 'fecha_alta' => '2025-02-08', 'observaciones' => 'Sin incidencias en el alta.', 'id_cebadero' => 1],
            ['id_animal' => 6, 'codigo' => 'ANM-006', 'especie' => 'Porcino', 'raza' => 'Cerdo ibérico de cebo', 'id_pienso_recomendado' => 2, 'tipo_pienso_recomendado' => 'Pienso engorde', 'lote' => 'L003', 'fecha_alta' => '2025-02-10', 'observaciones' => 'Control sanitario al dia.', 'id_cebadero' => 1],
            ['id_animal' => 7, 'codigo' => 'ANM-007', 'especie' => 'Vacuno', 'raza' => 'Novillo', 'id_pienso_recomendado' => 3, 'tipo_pienso_recomendado' => 'Pienso mantenimiento', 'lote' => 'L004', 'fecha_alta' => '2025-02-12', 'observaciones' => 'Buen estado corporal en recepcion.', 'id_cebadero' => 2],
            ['id_animal' => 8, 'codigo' => 'ANM-008', 'especie' => 'Vacuno', 'raza' => 'Angus', 'id_pienso_recomendado' => 3, 'tipo_pienso_recomendado' => 'Pienso mantenimiento', 'lote' => 'L005', 'fecha_alta' => '2025-02-14', 'observaciones' => 'Pendiente de revision dental rutinaria.', 'id_cebadero' => 2],
            ['id_animal' => 9, 'codigo' => 'ANM-009', 'especie' => 'Avicola', 'raza' => 'Pollo de engorde (broiler)', 'id_pienso_recomendado' => 1, 'tipo_pienso_recomendado' => 'Pienso crecimiento', 'lote' => 'L003', 'fecha_alta' => '2025-02-16', 'observaciones' => 'Lote activo y con comportamiento normal.', 'id_cebadero' => 1],
            ['id_animal' => 10, 'codigo' => 'ANM-010', 'especie' => 'Avicola', 'raza' => 'Pavo de engorde', 'id_pienso_recomendado' => 1, 'tipo_pienso_recomendado' => 'Pienso crecimiento', 'lote' => 'L003', 'fecha_alta' => '2025-02-18', 'observaciones' => 'Se reforzo control de temperatura ambiental.', 'id_cebadero' => 1],
            ['id_animal' => 11, 'codigo' => 'ANM-011', 'especie' => 'Porcino', 'raza' => 'Chato murciano', 'id_pienso_recomendado' => 1, 'tipo_pienso_recomendado' => 'Pienso crecimiento', 'lote' => 'L001', 'fecha_alta' => '2025-02-20', 'observaciones' => 'Consumo regular y actividad adecuada.', 'id_cebadero' => 1],
            ['id_animal' => 12, 'codigo' => 'ANM-012', 'especie' => 'Vacuno', 'raza' => 'Ternera de carne', 'id_pienso_recomendado' => 3, 'tipo_pienso_recomendado' => 'Pienso mantenimiento', 'lote' => 'L005', 'fecha_alta' => '2025-02-22', 'observaciones' => 'Sin lesiones visibles en inspeccion.', 'id_cebadero' => 2],
            ['id_animal' => 13, 'codigo' => 'ANM-013', 'especie' => 'Porcino', 'raza' => 'Cerdo blanco de engorde', 'id_pienso_recomendado' => 1, 'tipo_pienso_recomendado' => 'Pienso crecimiento', 'lote' => 'L002', 'fecha_alta' => '2025-02-24', 'observaciones' => 'Buen comportamiento en corral compartido.', 'id_cebadero' => 1],
            ['id_animal' => 14, 'codigo' => 'ANM-014', 'especie' => 'Avicola', 'raza' => 'Gallina africana (para carne)', 'id_pienso_recomendado' => 1, 'tipo_pienso_recomendado' => 'Pienso crecimiento', 'lote' => 'L004', 'fecha_alta' => '2025-02-26', 'observaciones' => 'Observacion preventiva por leve estres de traslado.', 'id_cebadero' => 2],
            ['id_animal' => 15, 'codigo' => 'ANM-015', 'especie' => 'Vacuno', 'raza' => 'Ternero de engorde', 'id_pienso_recomendado' => 3, 'tipo_pienso_recomendado' => 'Pienso mantenimiento', 'lote' => 'L004', 'fecha_alta' => '2025-02-28', 'observaciones' => 'Revision veterinaria favorable.', 'id_cebadero' => 2],
            ['id_animal' => 16, 'codigo' => 'ANM-016', 'especie' => 'Porcino', 'raza' => 'Cerdo Duroc', 'id_pienso_recomendado' => 2, 'tipo_pienso_recomendado' => 'Pienso engorde', 'lote' => 'L005', 'fecha_alta' => '2025-03-02', 'observaciones' => 'Alta reciente con buena adaptacion.', 'id_cebadero' => 1],
            ['id_animal' => 17, 'codigo' => 'ANM-017', 'especie' => 'Vacuno', 'raza' => 'Novillo', 'id_pienso_recomendado' => 3, 'tipo_pienso_recomendado' => 'Pienso mantenimiento', 'lote' => 'L001', 'fecha_alta' => '2025-03-04', 'observaciones' => 'Se recomienda control de hidratacion.', 'id_cebadero' => 2],
            ['id_animal' => 18, 'codigo' => 'ANM-018', 'especie' => 'Avicola', 'raza' => 'Pollo campero de engorde', 'id_pienso_recomendado' => 1, 'tipo_pienso_recomendado' => 'Pienso crecimiento', 'lote' => 'L002', 'fecha_alta' => '2025-03-06', 'observaciones' => 'Lote homogeneo y sin bajas registradas.', 'id_cebadero' => 1],
            ['id_animal' => 21, 'codigo' => 'ANM-020', 'especie' => 'Avicola', 'raza' => 'Pollo de engorde (broiler)', 'id_pienso_recomendado' => 2, 'tipo_pienso_recomendado' => 'Pienso engorde', 'lote' => 'L005', 'fecha_alta' => '2026-04-21', 'observaciones' => 'Relleno a tope.', 'id_cebadero' => 1],
        ]);
    }
}
