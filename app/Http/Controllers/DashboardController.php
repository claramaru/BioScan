<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    // Carga la vista principal del dashboard.
    // Los bloques dinamicos se rellenan despues con fetch() desde el frontend.
    public function index()
    {
        return view('index');
    }

    // Devuelve las metricas principales de las tarjetas superiores.
    // Aqui se concentran los totales y comparativas que luego pinta el dashboard.
    public function stats()
    {
        $ahora = Carbon::now();
        $inicioMes = $ahora->copy()->startOfMonth();
        $inicioMesAnterior = $ahora->copy()->subMonth()->startOfMonth();
        $finMesAnterior = $ahora->copy()->subMonth()->endOfMonth();

        // Total general de animales registrados en el sistema.
        $totalAnimales = DB::table('animal')->count();

        // Recuento por especie para mostrar el reparto actual.
        $especiesRaw = DB::table('animal')
            ->select('especie', DB::raw('count(*) as total'))
            ->groupBy('especie')
            ->get();

        // Consideramos activo un cebadero si al menos tiene un animal asociado.
        $cebaderosActivos = DB::table('cebadero')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('animal')
                    ->whereColumn('animal.id_cebadero', 'cebadero.id_cebadero');
            })
            ->count();

        $totalCebaderos = DB::table('cebadero')->count();

        // Media de cantidad de pienso por especie.
        // Se formatea aqui mismo para que el frontend lo consuma ya listo.
        $piensoEspecies = DB::table('alimentacion')
            ->join('animal', 'alimentacion.id_animal', '=', 'animal.id_animal')
            ->select('animal.especie', DB::raw('AVG(alimentacion.cantidad) as promedio'))
            ->groupBy('animal.especie')
            ->get()
            ->map(fn ($registro) => [
                'especie' => $registro->especie,
                'unidad' => $registro->promedio >= 1000
                    ? round($registro->promedio / 1000, 2) . 'T'
                    : round($registro->promedio, 2) . 'kg',
            ]);

        // Tratamientos registrados este mes para la tarjeta medica.
        $tratamientosActivos = DB::table('ficha_medica')
            ->where('fecha', '>=', $inicioMes)
            ->whereNotNull('tratamiento')
            ->where('tratamiento', '!=', '')
            ->count();

        $tratamientosMesAnterior = DB::table('ficha_medica')
            ->whereBetween('fecha', [$inicioMesAnterior, $finMesAnterior])
            ->whereNotNull('tratamiento')
            ->where('tratamiento', '!=', '')
            ->count();

        // Si no habia datos el mes anterior, devolvemos null para evitar porcentajes falsos.
        $pctTratamientos = $tratamientosMesAnterior > 0
            ? round((($tratamientosActivos - $tratamientosMesAnterior) / $tratamientosMesAnterior) * 100, 1)
            : null;

        // Mismo enfoque para comparar altas de animales entre meses.
        $animalesMes = DB::table('animal')->where('fecha_alta', '>=', $inicioMes)->count();
        $animalesMesAnterior = DB::table('animal')
            ->whereBetween('fecha_alta', [$inicioMesAnterior, $finMesAnterior])
            ->count();

        $pctAnimales = $animalesMesAnterior > 0
            ? round((($animalesMes - $animalesMesAnterior) / $animalesMesAnterior) * 100, 1)
            : null;

        return response()->json([
            'total_animales' => $totalAnimales,
            'pct_animales' => $pctAnimales,
            'especies' => $especiesRaw,
            'cebaderos_activos' => $cebaderosActivos,
            'total_cebaderos' => $totalCebaderos,
            'pienso_especies' => $piensoEspecies,
            'tratamientos_activos' => $tratamientosActivos,
            'pct_tratamientos' => $pctTratamientos,
        ]);
    }

    // Devuelve los ultimos animales incorporados para la tabla lateral del dashboard.
    public function animalesRecientes()
    {
        $animales = DB::table('animal')
            ->join('cebadero', 'animal.id_cebadero', '=', 'cebadero.id_cebadero')
            ->leftJoin('ficha_medica', function ($join) {
                // Solo nos interesa la ultima ficha medica de cada animal para resumir su estado.
                $join->on('ficha_medica.id_animal', '=', 'animal.id_animal')
                    ->whereRaw('ficha_medica.fecha = (
                        SELECT MAX(f2.fecha) FROM ficha_medica f2
                        WHERE f2.id_animal = animal.id_animal
                    )');
            })
            ->select(
                'animal.id_animal',
                'animal.codigo',
                'animal.especie',
                'animal.lote',
                'animal.fecha_alta',
                'cebadero.nombre as cebadero_nombre',
                'cebadero.id_cebadero',
                DB::raw("CASE
                    WHEN ficha_medica.diagnostico IS NOT NULL AND ficha_medica.diagnostico != '' THEN 'Revision'
                    ELSE 'Saludable'
                END as estado")
            )
            ->orderByDesc('animal.fecha_alta')
            ->limit(5)
            ->get();

        return response()->json($animales);
    }

    // Mezcla alimentacion y ficha medica para mostrar una actividad reciente unica.
    public function actividadReciente()
    {
        // Cargamos cada fuente por separado para evitar choques de collation en MySQL
        // cuando se combinan literales y columnas de tablas distintas mediante UNION.
        $alimentaciones = DB::table('alimentacion')
            ->leftJoin('animal', 'alimentacion.id_animal', '=', 'animal.id_animal')
            ->leftJoin('pienso', 'alimentacion.id_pienso', '=', 'pienso.id_pienso')
            ->select(
                'alimentacion.fecha as fecha',
                DB::raw("'alimentacion' as tipo"),
                DB::raw("COALESCE(animal.codigo, 'Sin animal') as codigo"),
                DB::raw("COALESCE(animal.especie, 'Sin especie') as especie"),
                DB::raw("COALESCE(pienso.nombre, 'Sin pienso') as detalle")
            )
            ->orderByDesc('alimentacion.fecha')
            ->limit(5)
            ->get();

        // Eventos medicos. Distinguimos revision y tratamiento para la UI.
        $fichas = DB::table('ficha_medica')
            ->join('animal', 'ficha_medica.id_animal', '=', 'animal.id_animal')
            ->select(
                'ficha_medica.fecha as fecha',
                DB::raw("CASE
                    WHEN ficha_medica.tratamiento IS NOT NULL AND ficha_medica.tratamiento != ''
                    THEN 'tratamiento'
                    ELSE 'revision'
                END as tipo"),
                'animal.codigo',
                'animal.especie',
                'ficha_medica.diagnostico as detalle'
            )
            ->orderByDesc('ficha_medica.fecha')
            ->limit(5)
            ->get();

        // Unimos ambas fuentes ya en PHP para conservar una sola lista cronologica
        // sin depender de UNION SQL entre collations heterogeneas.
        $actividad = $alimentaciones
            ->concat($fichas)
            ->sortByDesc(function ($item) {
                return (string) $item->fecha;
            })
            ->take(5)
            ->values();

        return response()->json($actividad);
    }

    // Resume cada cebadero con numero de animales y media de pienso.
    public function estadisticasCebadero()
    {
        $cebaderos = DB::table('cebadero')
            ->leftJoin('animal', 'cebadero.id_cebadero', '=', 'animal.id_cebadero')
            ->leftJoin('alimentacion', 'animal.id_animal', '=', 'alimentacion.id_animal')
            ->select(
                'cebadero.id_cebadero',
                'cebadero.nombre',
                DB::raw('COUNT(DISTINCT animal.id_animal) as total_animales'),
                DB::raw('COALESCE(ROUND(AVG(alimentacion.cantidad) / 1000, 2), 0) as pienso_promedio_t')
            )
            ->groupBy('cebadero.id_cebadero', 'cebadero.nombre')
            ->orderBy('cebadero.nombre')
            ->get();

        return response()->json($cebaderos);
    }
}
