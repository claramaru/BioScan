<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use App\Models\Cebadero;
use App\Models\FichaMedica;
use App\Models\Pienso;
use App\Models\Privilegio;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class AnimalController extends Controller
{
    private function animalTieneColumna(string $columna): bool
    {
        return Schema::hasColumn('animal', $columna);
    }

    // Respuesta comun para accesos denegados por permisos.
    private function denegado(string $permiso)
    {
        $priv = Privilegio::where('nombre', $permiso)->first();
        $rolesPermitidos = $priv ? $priv->roles()->pluck('nombre')->toArray() : [];

        return response()->view('acceso_denegado', [
            'permitido' => false,
            'permiso' => $permiso,
            'rolesPermitidos' => $rolesPermitidos,
            'mensaje' => null,
        ], 403);
    }

    // Version JSON del acceso denegado para los endpoints asincronos.
    private function denegadoJson(string $permiso)
    {
        return response()->json([
            'ok' => false,
            'mensaje' => 'No tienes permiso para acceder a este recurso.',
            'permiso' => $permiso,
        ], 403);
    }

    // Consulta base reutilizable para el listado HTML y para la API.
    private function construirConsultaAnimales(Request $request): Builder
    {
        $q = trim((string) $request->query('q', ''));
        $codigo = trim((string) $request->query('codigo', ''));
        $especie = trim((string) $request->query('especie', ''));
        $raza = trim((string) $request->query('raza', ''));
        $lote = trim((string) $request->query('lote', ''));
        $fechaAlta = trim((string) $request->query('fecha_alta', ''));
        $idPienso = trim((string) $request->query('id_pienso', ''));
        $observaciones = trim((string) $request->query('observaciones', ''));
        $cebadero = trim((string) $request->query('cebadero', ''));

        $consulta = Animal::with(['cebadero', 'piensoRecomendado']);
        $tieneRaza = $this->animalTieneColumna('raza');
        $tienePiensoRecomendado = $this->animalTieneColumna('id_pienso_recomendado');
        $tieneObservaciones = $this->animalTieneColumna('observaciones');

        // Busqueda general sobre los campos principales del modulo.
        if ($q !== '') {
            $consulta->where(function ($query) use ($q, $tieneRaza, $tienePiensoRecomendado, $tieneObservaciones) {
                    $query->where('codigo', 'like', '%' . $q . '%')
                    ->orWhere('especie', 'like', '%' . $q . '%')
                    ->orWhere('lote', 'like', '%' . $q . '%')
                    ->orWhere('fecha_alta', 'like', '%' . $q . '%');

                    if ($tieneRaza) {
                        $query->orWhere('raza', 'like', '%' . $q . '%');
                    }

                    if ($tienePiensoRecomendado) {
                        $query->orWhereHas('piensoRecomendado', function ($piensoQuery) use ($q) {
                            $piensoQuery->where('nombre', 'like', '%' . $q . '%');
                        });
                    }

                    if ($tieneObservaciones) {
                        $query->orWhere('observaciones', 'like', '%' . $q . '%');
                    }

                    $query->orWhereHas('cebadero', function ($cebaderoQuery) use ($q) {
                        $cebaderoQuery->where('nombre', 'like', '%' . $q . '%');
                    });
            });
        }

        // Filtros especificos por columna.
        if ($codigo !== '') {
            $consulta->where('codigo', 'like', '%' . $codigo . '%');
        }

        if ($especie !== '') {
            $consulta->where('especie', $especie);
        }

        if ($tieneRaza && $raza !== '') {
            $consulta->where('raza', 'like', '%' . $raza . '%');
        }

        if ($lote !== '') {
            $consulta->where('lote', 'like', '%' . $lote . '%');
        }

        if ($fechaAlta !== '') {
            $consulta->whereDate('fecha_alta', $fechaAlta);
        }

        if ($tienePiensoRecomendado && $idPienso !== '') {
            $consulta->where('id_pienso_recomendado', $idPienso);
        }

        if ($tieneObservaciones && $observaciones !== '') {
            $consulta->where('observaciones', 'like', '%' . $observaciones . '%');
        }

        if ($cebadero !== '') {
            $consulta->whereHas('cebadero', function ($query) use ($cebadero) {
                $query->where('nombre', $cebadero);
            });
        }

        return $consulta;
    }

    // Listado principal de animales con permisos, filtros y datos auxiliares.
    public function index(Request $request)
    {
        if (!auth()->user()->tienePrivilegio('ver_animal')) {
            return $this->denegado('ver_animal');
        }

        $animales = $this->construirConsultaAnimales($request)
            ->orderByDesc('id_animal')
            ->get();

        return view('animal.index', [
            'animales' => $animales,
            'cebaderos' => Cebadero::orderBy('nombre')->get(['id_cebadero', 'nombre']),
            'piensos' => $this->animalTieneColumna('id_pienso_recomendado')
                ? Pienso::where('activo', true)->orderBy('nombre')->get(['id_pienso', 'nombre'])
                : collect(),
            'filtros' => [
                'q' => trim((string) $request->query('q', '')),
                'codigo' => trim((string) $request->query('codigo', '')),
                'especie' => trim((string) $request->query('especie', '')),
                'raza' => trim((string) $request->query('raza', '')),
                'lote' => trim((string) $request->query('lote', '')),
                'fecha_alta' => trim((string) $request->query('fecha_alta', '')),
                'id_pienso' => trim((string) $request->query('id_pienso', '')),
                'observaciones' => trim((string) $request->query('observaciones', '')),
                'cebadero' => trim((string) $request->query('cebadero', '')),
            ],
            'puedeCrear' => auth()->user()->tienePrivilegio('crear_animal'),
            'puedeCrearAlimentacion' => auth()->user()->tienePrivilegio('crear_alimentacion'),
            'puedeEditarAnimal' => auth()->user()->tienePrivilegio('editar_animal'),
            'puedeBorrar' => auth()->user()->tienePrivilegio('borrar_animal'),
            'puedeObs' => auth()->user()->tienePrivilegio('editar_observaciones_ficha_medica'),
        ]);
    }

    // Pantalla de acceso rapido pensada para el flujo futuro con NFC.
    public function accesoRapidoForm()
    {
        if (!auth()->user()->tienePrivilegio('ver_animal')) {
            return $this->denegado('ver_animal');
        }

        return view('animal.acceso_rapido');
    }

    // Resuelve el codigo manualmente introducido y abre el historial del animal.
    public function accesoRapidoBuscar(Request $request)
    {
        if (!auth()->user()->tienePrivilegio('ver_animal')) {
            return $this->denegado('ver_animal');
        }

        $request->validate([
            'codigo' => 'required|max:50',
        ]);

        $animal = Animal::where('codigo', $request->codigo)->first();

        if (!$animal) {
            return back()
                ->withInput()
                ->withErrors(['codigo' => 'No existe ningun animal con ese codigo.']);
        }

        return redirect()
            ->route('animal.historial', $animal->id_animal)
            ->with('ok', 'Acceso rapido aplicado para el codigo: ' . $animal->codigo);
    }

    // Vista demostrativa para enseñar el consumo del endpoint JSON.
    public function apiDemo()
    {
        if (!auth()->user()->tienePrivilegio('ver_animal')) {
            return $this->denegado('ver_animal');
        }

        return view('animal.api_demo');
    }

    // Historial unificado del animal: combina eventos medicos y de alimentacion.
    public function historial($id)
    {
        if (!auth()->user()->tienePrivilegio('ver_animal')) {
            return $this->denegado('ver_animal');
        }

        $animal = Animal::with(['cebadero', 'piensoRecomendado'])->find($id);
        if (!$animal) {
            abort(404);
        }

        $fichas = FichaMedica::where('id_animal', $id)
            ->orderByDesc('fecha')
            ->orderByDesc('id_ficha')
            ->get();

        $alimentaciones = collect();

        // La tabla de alimentacion puede no existir aun segun la fase del proyecto.
        if (Schema::hasTable('alimentacion')) {
            $alimentaciones = DB::table('alimentacion')
                ->leftJoin('usuario', 'alimentacion.id_usuario', '=', 'usuario.id_usuario')
                ->where('alimentacion.id_animal', $id)
                ->select(
                    'alimentacion.id_alimentacion',
                    'alimentacion.id_animal',
                    'pienso.nombre as tipo_pienso',
                    'alimentacion.cantidad',
                    'alimentacion.fecha',
                    'alimentacion.id_usuario',
                    DB::raw("TRIM(CONCAT(COALESCE(usuario.nombre, ''), ' ', COALESCE(usuario.apellidos, ''))) as usuario_nombre")
                )
                ->leftJoin('pienso', 'alimentacion.id_pienso', '=', 'pienso.id_pienso')
                ->orderByDesc('alimentacion.fecha')
                ->orderByDesc('alimentacion.id_alimentacion')
                ->get()
                ->map(function ($alim) {
                    $alim->usuario_nombre = trim((string) $alim->usuario_nombre) !== '' ? $alim->usuario_nombre : 'Sin asignar';
                    return $alim;
                });
        }

        $eventos = $fichas->map(function ($ficha) {
            return [
                'tipo' => 'ficha_medica',
                'fecha' => $ficha->fecha,
                'titulo' => 'Ficha médica',
                'detalle' => trim(implode(' | ', array_filter([
                    $ficha->diagnostico ? 'Diagnóstico: ' . $ficha->diagnostico : null,
                    $ficha->tratamiento ? 'Tratamiento: ' . $ficha->tratamiento : null,
                    $ficha->observaciones ? 'Observaciones: ' . $ficha->observaciones : null,
                ]))) ?: 'Registro médico sin detalle',
                'usuario' => null,
            ];
        })->concat(
            $alimentaciones->map(function ($alim) {
                return [
                    'tipo' => 'alimentacion',
                    'fecha' => $alim->fecha,
                    'titulo' => 'Alimentación',
                    'detalle' => trim(implode(' · ', array_filter([
                        $alim->tipo_pienso ? $alim->tipo_pienso : null,
                        isset($alim->cantidad) ? number_format((float) $alim->cantidad, 2, ',', '.') . ' kg' : null,
                    ]))),
                    'usuario' => $alim->usuario_nombre,
                ];
            })
        )
            ->sortByDesc(fn ($evento) => $evento['fecha'] ?? '0000-00-00')
            ->values();

        return view('animal.historial', [
            'animal' => $animal,
            'fichas' => $fichas,
            'alimentaciones' => $alimentaciones,
            'eventos' => $eventos,
            'totalFichas' => $fichas->count(),
            'totalAlimentacion' => $alimentaciones->count(),
        ]);
    }

    // API de detalle por codigo. Devuelve el animal y su historial en JSON.
    public function apiPorCodigo(string $codigo)
    {
        if (!auth()->check() || !auth()->user()->tienePrivilegio('ver_animal')) {
            return $this->denegadoJson('ver_animal');
        }

        $animal = Animal::with(['cebadero', 'piensoRecomendado'])
            ->where('codigo', $codigo)
            ->first();
        if (!$animal) {
            return response()->json([
                'ok' => false,
                'mensaje' => 'Animal no encontrado',
            ], 404);
        }

        $fichas = FichaMedica::where('id_animal', $animal->id_animal)
            ->orderByDesc('fecha')
            ->orderByDesc('id_ficha')
            ->get();

        $alimentaciones = collect();

        // Igual que en la vista de historial, la alimentacion es opcional.
        if (Schema::hasTable('alimentacion')) {
            $alimentaciones = DB::table('alimentacion')
                ->leftJoin('usuario', 'alimentacion.id_usuario', '=', 'usuario.id_usuario')
                ->where('alimentacion.id_animal', $animal->id_animal)
                ->select(
                    'alimentacion.id_alimentacion',
                    'alimentacion.id_animal',
                    'alimentacion.id_pienso',
                    'pienso.nombre as pienso_nombre',
                    'alimentacion.cantidad',
                    'alimentacion.fecha',
                    'alimentacion.id_usuario',
                    DB::raw("TRIM(CONCAT(COALESCE(usuario.nombre, ''), ' ', COALESCE(usuario.apellidos, ''))) as usuario_nombre")
                )
                ->leftJoin('pienso', 'alimentacion.id_pienso', '=', 'pienso.id_pienso')
                ->orderByDesc('alimentacion.fecha')
                ->orderByDesc('alimentacion.id_alimentacion')
                ->get()
                ->map(function ($alim) {
                    return [
                        'id_alimentacion' => $alim->id_alimentacion,
                        'id_animal' => $alim->id_animal,
                        'id_pienso' => $alim->id_pienso,
                        'tipo_pienso' => $alim->pienso_nombre,
                        'cantidad' => (float) $alim->cantidad,
                        'fecha' => $alim->fecha,
                        'id_usuario' => $alim->id_usuario,
                        'usuario_nombre' => trim((string) $alim->usuario_nombre) !== '' ? $alim->usuario_nombre : 'Sin asignar',
                    ];
                });
        }

        $eventos = $fichas->map(function ($ficha) {
            return [
                'tipo' => 'ficha_medica',
                'fecha' => $ficha->fecha,
                'detalle' => [
                    'diagnostico' => $ficha->diagnostico,
                    'tratamiento' => $ficha->tratamiento,
                    'observaciones' => $ficha->observaciones,
                ],
            ];
        })->concat(
            $alimentaciones->map(function ($alim) {
                return [
                    'tipo' => 'alimentacion',
                    'fecha' => $alim['fecha'],
                    'detalle' => $alim,
                ];
            })
        )
            ->sortByDesc(fn ($evento) => $evento['fecha'] ?? '0000-00-00')
            ->values();

        return response()->json([
            'ok' => true,
            'animal' => [
                'id_animal' => $animal->id_animal,
                'codigo' => $animal->codigo,
                'especie' => $animal->especie,
                'raza' => $animal->raza,
                'pienso_recomendado' => $animal->piensoRecomendado?->nombre,
                'lote' => $animal->lote,
                'fecha_alta' => $animal->fecha_alta?->toDateString(),
                'observaciones' => $animal->observaciones,
                'cebadero' => [
                    'id_cebadero' => $animal->cebadero->id_cebadero ?? null,
                    'nombre' => $animal->cebadero->nombre ?? null,
                ],
            ],
            'totales' => [
                'fichas_medicas' => $fichas->count(),
                'alimentacion' => $alimentaciones->count(),
                'eventos' => $eventos->count(),
            ],
            'historial' => $eventos,
        ]);
    }

    // API del listado que usa la tabla asincrona y la vista demo.
    public function apiListado(Request $request)
    {
        if (!auth()->check() || !auth()->user()->tienePrivilegio('ver_animal')) {
            return $this->denegadoJson('ver_animal');
        }

        $animales = $this->construirConsultaAnimales($request)
            ->orderByDesc('id_animal')
            ->get()
            ->map(function ($animal) {
                return [
                    'id_animal' => $animal->id_animal,
                    'codigo' => $animal->codigo,
                    'especie' => $animal->especie,
                    'raza' => $animal->raza,
                    'id_pienso_recomendado' => $animal->id_pienso_recomendado,
                    'tipo_pienso_recomendado' => $animal->piensoRecomendado?->nombre,
                    'lote' => $animal->lote,
                    'fecha_alta' => $animal->fecha_alta?->toDateString(),
                    'observaciones' => $animal->observaciones,
                    'cebadero' => [
                        'id_cebadero' => $animal->cebadero->id_cebadero ?? null,
                        'nombre' => $animal->cebadero->nombre ?? null,
                    ],
                ];
            })
            ->values();

        return response()->json([
            'ok' => true,
            'data' => $animales,
        ]);
    }

    // Formulario de alta de un nuevo animal.
    public function create()
    {
        if (!auth()->user()->tienePrivilegio('crear_animal')) {
            return $this->denegado('crear_animal');
        }

        $cebaderos = Cebadero::orderBy('nombre')->get();
        $piensos = $this->animalTieneColumna('id_pienso_recomendado')
            ? Pienso::where('activo', true)->orderBy('nombre')->get(['id_pienso', 'nombre'])
            : collect();

        return view('animal.create', compact('cebaderos', 'piensos'));
    }

    // Persistencia del alta de animal.
    public function store(Request $request)
    {
        if (!auth()->user()->tienePrivilegio('crear_animal')) {
            return $this->denegado('crear_animal');
        }

        $rules = [
            'codigo' => ['required', 'max:50', 'unique:animal,codigo'],
            'especie' => 'required|max:50',
            'lote' => 'required|max:50',
            'fecha_alta' => 'required|date',
            'id_cebadero' => 'required|integer|exists:cebadero,id_cebadero',
        ];

        if ($this->animalTieneColumna('raza')) {
            $rules['raza'] = 'nullable|max:120';
        }

        if ($this->animalTieneColumna('id_pienso_recomendado')) {
            $rules['id_pienso_recomendado'] = 'nullable|integer|exists:pienso,id_pienso';
        }

        if ($this->animalTieneColumna('observaciones')) {
            $rules['observaciones'] = 'nullable|max:1000';
        }

        $data = $request->validate($rules);

        Animal::create($data);

        return redirect()->route('animal.index')->with('ok', 'Animal creado correctamente');
    }

    // Punto de entrada unico de edicion: decide entre vista completa
    // o vista reducida de observaciones segun permisos.
    public function edit($id)
    {
        $puedeEditarAnimal = auth()->user()->tienePrivilegio('editar_animal');
        $puedeObs = auth()->user()->tienePrivilegio('editar_observaciones_ficha_medica');

        if (!$puedeEditarAnimal && !$puedeObs) {
            // Si no puede editar ni el animal ni las observaciones, se deniega.
            return $this->denegado('editar_animal');
        }

        // Si solo puede modificar observaciones, se le lleva a la vista limitada.
        if (!$puedeEditarAnimal && $puedeObs) {
            $ficha = FichaMedica::where('id_animal', $id)->orderByDesc('id_ficha')->first();

            if (!$ficha) {
                $ficha = new FichaMedica();
                $ficha->id_animal = $id;
                $ficha->id_usuario = auth()->user()->id_usuario;
                $ficha->diagnostico = '';
                $ficha->tratamiento = '';
                $ficha->observaciones = '';
                $ficha->fecha = date('Y-m-d');
                $ficha->save();
            }

            return view('animal.edit_observaciones', compact('ficha'));
        }

        // Si tiene permiso completo, se abre el formulario general del animal.
        $animal = Animal::find($id);
        if (!$animal) {
            abort(404);
        }

        $cebaderos = Cebadero::orderBy('nombre')->get();
        $animal->loadMissing('piensoRecomendado');
        $piensos = $this->animalTieneColumna('id_pienso_recomendado')
            ? Pienso::where('activo', true)->orderBy('nombre')->get(['id_pienso', 'nombre'])
            : collect();

        return view('animal.edit', compact('animal', 'cebaderos', 'piensos'));
    }

    // Guarda la edicion completa del animal.
    public function update(Request $request, $id)
    {
        if (!auth()->user()->tienePrivilegio('editar_animal')) {
            return $this->denegado('editar_animal');
        }

        $rules = [
            'codigo' => ['required', 'max:50', Rule::unique('animal', 'codigo')->ignore($id, 'id_animal')],
            'especie' => 'required|max:50',
            'lote' => 'required|max:50',
            'fecha_alta' => 'required|date',
            'id_cebadero' => 'required|integer|exists:cebadero,id_cebadero',
        ];

        if ($this->animalTieneColumna('raza')) {
            $rules['raza'] = 'nullable|max:120';
        }

        if ($this->animalTieneColumna('id_pienso_recomendado')) {
            $rules['id_pienso_recomendado'] = 'nullable|integer|exists:pienso,id_pienso';
        }

        if ($this->animalTieneColumna('observaciones')) {
            $rules['observaciones'] = 'nullable|max:1000';
        }

        $data = $request->validate($rules);

        $animal = Animal::find($id);
        if (!$animal) {
            abort(404);
        }

        $animal->fill($data);
        $animal->save();

        return redirect()->route('animal.index')->with('ok', 'Animal actualizado correctamente');
    }

    // Guarda solo observaciones para perfiles con permiso limitado.
    public function updateObservaciones(Request $request, $id)
    {
        if (!auth()->user()->tienePrivilegio('editar_observaciones_ficha_medica')) {
            return $this->denegado('editar_observaciones_ficha_medica');
        }

        $request->validate([
            'observaciones' => 'required|max:255',
        ]);

        $ficha = FichaMedica::where('id_animal', $id)->orderByDesc('id_ficha')->first();
        if (!$ficha) {
            abort(404);
        }

        $ficha->observaciones = $request->observaciones;
        $ficha->save();

        return redirect()->route('animal.index')->with('ok', 'Observaciones actualizadas correctamente');
    }

    // Elimina un animal del sistema.
    public function destroy($id)
    {
        if (!auth()->user()->tienePrivilegio('borrar_animal')) {
            return $this->denegado('borrar_animal');
        }

        $animal = Animal::find($id);
        if (!$animal) {
            abort(404);
        }

        DB::transaction(function () use ($animal) {
            if (Schema::hasTable('alimentacion')) {
                DB::table('alimentacion')
                    ->where('id_animal', $animal->id_animal)
                    ->delete();
            }

            if (Schema::hasTable('ficha_medica')) {
                DB::table('ficha_medica')
                    ->where('id_animal', $animal->id_animal)
                    ->delete();
            }

            $animal->delete();
        });

        return redirect()->route('animal.index')->with('ok', 'Animal eliminado correctamente de manera satisfactoria');
    }
}
