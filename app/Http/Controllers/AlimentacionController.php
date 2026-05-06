<?php

namespace App\Http\Controllers;

use App\Models\Alimentacion;
use App\Models\Animal;
use App\Models\Pienso;
use App\Models\Privilegio;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AlimentacionController extends Controller
{
    // Comprueba columnas opcionales de animal para mantener compatibilidad con migraciones anteriores.
    private function animalTieneColumna(string $columna): bool
    {
        return \Illuminate\Support\Facades\Schema::hasColumn('animal', $columna);
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

    // Consulta base reutilizable para el listado HTML y para la API asincrona.
    private function construirConsultaAlimentacionBase(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $tipoPienso = trim((string) $request->query('tipo_pienso', ''));
        $fecha = trim((string) $request->query('fecha', ''));
        $animal = trim((string) $request->query('id_animal', ''));
        $responsable = trim((string) $request->query('id_usuario', ''));

        $consulta = DB::table('alimentacion')
            ->leftJoin('animal', 'alimentacion.id_animal', '=', 'animal.id_animal')
            ->leftJoin('pienso', 'alimentacion.id_pienso', '=', 'pienso.id_pienso')
            ->leftJoin('usuario', 'alimentacion.id_usuario', '=', 'usuario.id_usuario');

        // Busqueda general sobre animal, pienso y responsable.
        if ($q !== '') {
            $consulta->where(function ($query) use ($q) {
                $query->where('animal.codigo', 'like', '%' . $q . '%')
                    ->orWhere('animal.especie', 'like', '%' . $q . '%')
                    ->orWhere('animal.lote', 'like', '%' . $q . '%')
                    ->orWhere('pienso.nombre', 'like', '%' . $q . '%')
                    ->orWhere('usuario.nombre', 'like', '%' . $q . '%')
                    ->orWhere('usuario.apellidos', 'like', '%' . $q . '%');
            });
        }

        // Filtros especificos por pienso, fecha, animal y responsable.
        if ($tipoPienso !== '') {
            $consulta->where('alimentacion.id_pienso', $tipoPienso);
        }

        if ($fecha !== '') {
            $consulta->whereDate('alimentacion.fecha', $fecha);
        }

        if ($animal !== '') {
            $consulta->where('alimentacion.id_animal', $animal);
        }

        if ($responsable !== '') {
            $consulta->where('alimentacion.id_usuario', $responsable);
        }

        return $consulta;
    }

    // Agrupa los registros por pienso y responsable para mostrar el resumen de alimentacion.
    private function construirResumenAlimentacion(Request $request)
    {
        $subconsultaAnimales = $this->animalTieneColumna('id_pienso_recomendado')
            ? '(
                    SELECT COUNT(*)
                    FROM animal a2
                    WHERE a2.id_pienso_recomendado = alimentacion.id_pienso
                )'
            : '0';

        return $this->construirConsultaAlimentacionBase($request)
            ->select(
                'alimentacion.id_pienso',
                'alimentacion.id_usuario',
                // Algunos registros antiguos conservan el nombre en alimentacion.tipo_pienso.
                DB::raw("COALESCE(NULLIF(pienso.nombre, ''), NULLIF(alimentacion.tipo_pienso, ''), 'Sin pienso') as tipo_pienso"),
                'usuario.nombre as responsable_nombre',
                'usuario.apellidos as responsable_apellidos',
                DB::raw($subconsultaAnimales . ' as total_animales'),
                DB::raw('SUM(alimentacion.cantidad) as total_kg')
            )
            ->groupBy('alimentacion.id_pienso', 'alimentacion.id_usuario', 'pienso.nombre', 'alimentacion.tipo_pienso', 'usuario.nombre', 'usuario.apellidos')
            ->orderBy('tipo_pienso')
            ->orderBy('usuario.nombre')
            ->get()
            ->map(function ($registro) {
                $responsable = trim((string) $registro->responsable_nombre . ' ' . (string) $registro->responsable_apellidos);

                return [
                    'id_pienso' => $registro->id_pienso,
                    'id_usuario' => $registro->id_usuario,
                    'tipo_pienso' => $registro->tipo_pienso,
                    'responsable' => $responsable !== '' ? $responsable : 'Sin responsable',
                    'total_animales' => (int) ($registro->total_animales ?? 0),
                    'total_kg' => (float) ($registro->total_kg ?? 0),
                ];
            })
            ->values();
    }

    // Normaliza el nombre de especie para usarlo como clave en las recomendaciones.
    private function claveEspecie(?string $especie): string
    {
        return mb_strtolower(trim((string) $especie));
    }

    // Calcula cantidades medias por animal, por especie y por tipo de pienso para sugerencias del formulario.
    private function construirRecomendacionesCantidad(): array
    {
        $porAnimal = [];
        $porEspecie = [];
        $porTipo = [];

        $animalTipo = DB::table('alimentacion')
            ->select('id_animal', 'id_pienso', DB::raw('AVG(cantidad) as media'))
            ->whereNotNull('id_animal')
            ->whereNotNull('id_pienso')
            ->groupBy('id_animal', 'id_pienso')
            ->get();

        foreach ($animalTipo as $fila) {
            $porAnimal[(string) $fila->id_animal][(string) $fila->id_pienso] = round((float) $fila->media, 2);
        }

        $especieTipo = DB::table('alimentacion')
            ->leftJoin('animal', 'alimentacion.id_animal', '=', 'animal.id_animal')
            ->select('animal.especie', 'alimentacion.id_pienso', DB::raw('AVG(alimentacion.cantidad) as media'))
            ->whereNotNull('animal.especie')
            ->whereNotNull('alimentacion.id_pienso')
            ->groupBy('animal.especie', 'alimentacion.id_pienso')
            ->get();

        foreach ($especieTipo as $fila) {
            $claveEspecie = $this->claveEspecie($fila->especie);
            if ($claveEspecie === '') {
                continue;
            }

            $porEspecie[$claveEspecie][(string) $fila->id_pienso] = round((float) $fila->media, 2);
        }

        $tipoGlobal = DB::table('alimentacion')
            ->select('id_pienso', DB::raw('AVG(cantidad) as media'))
            ->whereNotNull('id_pienso')
            ->groupBy('id_pienso')
            ->get();

        foreach ($tipoGlobal as $fila) {
            $porTipo[(string) $fila->id_pienso] = round((float) $fila->media, 2);
        }

        return [
            'por_animal' => $porAnimal,
            'por_especie' => $porEspecie,
            'por_tipo' => $porTipo,
            'animales' => Animal::orderBy('codigo')
                ->get(['id_animal', 'codigo', 'especie'])
                ->mapWithKeys(function ($animal) {
                    return [
                        (string) $animal->id_animal => [
                            'codigo' => $animal->codigo,
                            'especie' => $animal->especie,
                            'especie_key' => $this->claveEspecie($animal->especie),
                        ],
                    ];
                }),
        ];
    }

    // Valida los datos de un registro individual de alimentacion.
    private function validarRegistro(Request $request): array
    {
        return $request->validate([
            'id_animal' => 'nullable|integer|exists:animal,id_animal',
            'id_pienso' => 'required|integer|exists:pienso,id_pienso',
            'cantidad' => 'required|numeric|min:0.01|max:9999.99',
            'fecha' => 'required|date',
        ]);
    }

    // Valida la alimentacion masiva aplicada a varios animales seleccionados.
    private function validarRegistroMasivo(Request $request): array
    {
        return $request->validate([
            'animales' => 'required|array|min:1',
            'animales.*' => 'integer|exists:animal,id_animal',
            'id_pienso' => 'required|integer|exists:pienso,id_pienso',
            'cantidad' => 'required|numeric|min:0.01|max:9999.99',
            'fecha' => 'required|date',
        ]);
    }

    // Decide a que pantalla volver despues de crear un registro.
    private function resolverRetorno(Request $request): array
    {
        $destino = trim((string) $request->input('return_to', $request->query('return_to', '')));
        $animalId = trim((string) $request->input('return_animal_id', $request->query('return_animal_id', '')));

        if ($destino === 'animal.historial' && $animalId !== '') {
            return [
                'return_to' => 'animal.historial',
                'return_animal_id' => $animalId,
                'url' => route('animal.historial', $animalId),
            ];
        }

        if ($destino === 'animal.index') {
            return [
                'return_to' => 'animal.index',
                'return_animal_id' => null,
                'url' => route('animal.index'),
            ];
        }

        return [
            'return_to' => 'alimentacion.index',
            'return_animal_id' => null,
            'url' => route('alimentacion.index'),
        ];
    }

    // Listado principal de alimentacion con permisos, filtros, resumenes y datos auxiliares.
    public function index(Request $request)
    {
        if (!auth()->user()->tienePrivilegio('ver_alimentacion')) {
            return $this->denegado('ver_alimentacion');
        }

        $usuario = auth()->user();
        $puedeCrearCabecera = $usuario->tienePrivilegio('crear_alimentacion')
            && !$usuario->esRol('operario')
            && !$usuario->esRol('invitado');

        $registros = $this->construirResumenAlimentacion($request);

        $resumenBase = DB::table('alimentacion');
        $kilosPorTipo = DB::table('alimentacion')
            ->join('pienso', 'alimentacion.id_pienso', '=', 'pienso.id_pienso')
            ->select('pienso.nombre as tipo_pienso', DB::raw('SUM(cantidad) as total_kg'))
            ->groupBy('pienso.nombre')
            ->orderBy('pienso.nombre')
            ->get()
            ->map(fn ($registro) => [
                'tipo_pienso' => $registro->tipo_pienso,
                'total_kg' => (float) $registro->total_kg,
            ])
            ->values();

        $recomendacionesCantidad = $this->construirRecomendacionesCantidad();

        return view('alimentacion.index', [
            'registros' => $registros,
            'tiposPienso' => Pienso::where('activo', true)->orderBy('nombre')->get(['id_pienso', 'nombre']),
            'animalesFiltro' => Animal::orderBy('codigo')->get(['id_animal', 'codigo', 'especie']),
            'usuariosFiltro' => User::orderBy('nombre')->orderBy('apellidos')->get(['id_usuario', 'nombre', 'apellidos']),
            'resumen' => [
                'total' => (int) $resumenBase->count(),
                'cantidad_total' => (float) DB::table('alimentacion')->sum('cantidad'),
                'tipos_total' => (int) Pienso::count(),
                'ultima_fecha' => DB::table('alimentacion')->max('fecha'),
            ],
            'kilosPorTipo' => $kilosPorTipo,
            'filtros' => [
                'q' => trim((string) $request->query('q', '')),
                'tipo_pienso' => trim((string) $request->query('tipo_pienso', '')),
                'fecha' => trim((string) $request->query('fecha', '')),
                'id_animal' => trim((string) $request->query('id_animal', '')),
                'id_usuario' => trim((string) $request->query('id_usuario', '')),
            ],
            'puedeCrear' => auth()->user()->tienePrivilegio('crear_alimentacion'),
            'puedeCrearCabecera' => $puedeCrearCabecera,
            'puedeGestionarPienos' => auth()->user()->tienePrivilegio('gestionar_pienso'),
            'recomendacionesCantidad' => $recomendacionesCantidad,
        ]);
    }

    // Muestra el formulario para registrar una alimentacion individual.
    public function create(Request $request)
    {
        if (!auth()->user()->tienePrivilegio('crear_alimentacion')) {
            return $this->denegado('crear_alimentacion');
        }

        $animalSeleccionado = trim((string) $request->query('id_animal', ''));
        $recomendacionesCantidad = $this->construirRecomendacionesCantidad();
        $retorno = $this->resolverRetorno($request);

        return view('alimentacion.create', [
            'animales' => Animal::orderBy('codigo')->get(['id_animal', 'codigo', 'especie']),
            'tiposPienso' => Pienso::where('activo', true)->orderBy('nombre')->get(['id_pienso', 'nombre']),
            'animalSeleccionado' => $animalSeleccionado,
            'recomendacionesCantidad' => $recomendacionesCantidad,
            'volverUrl' => $retorno['url'],
            'returnTo' => $retorno['return_to'],
            'returnAnimalId' => $retorno['return_animal_id'],
        ]);
    }

    // Guarda un nuevo registro de alimentacion y vuelve al origen adecuado.
    public function store(Request $request)
    {
        if (!auth()->user()->tienePrivilegio('crear_alimentacion')) {
            return $this->denegado('crear_alimentacion');
        }

        $data = $this->validarRegistro($request);
        $retorno = $this->resolverRetorno($request);
        $data['id_usuario'] = auth()->user()->id_usuario;
        $data['id_animal'] = $data['id_animal'] ?: null;

        Alimentacion::create($data);

        if ($retorno['return_to'] === 'animal.historial' && $retorno['return_animal_id']) {
            return redirect()
                ->route('animal.historial', $retorno['return_animal_id'])
                ->with('ok', 'Registro de alimentacion creado correctamente');
        }

        if ($retorno['return_to'] === 'animal.index') {
            return redirect()
                ->route('animal.index')
                ->with('ok', 'Registro de alimentacion creado correctamente');
        }

        return redirect()->route('alimentacion.index')->with('ok', 'Registro de alimentacion creado correctamente');
    }

    // Crea el mismo registro de alimentacion para todos los animales seleccionados.
    public function storeMasivo(Request $request)
    {
        if (!auth()->user()->tienePrivilegio('crear_alimentacion')) {
            return $this->denegado('crear_alimentacion');
        }

        $data = $this->validarRegistroMasivo($request);
        $usuarioId = auth()->user()->id_usuario;

        DB::transaction(function () use ($data, $usuarioId) {
            foreach ($data['animales'] as $animalId) {
                Alimentacion::create([
                    'id_animal' => $animalId,
                    'id_pienso' => $data['id_pienso'],
                    'cantidad' => $data['cantidad'],
                    'fecha' => $data['fecha'],
                    'id_usuario' => $usuarioId,
                ]);
            }
        });

        return redirect()->route('animal.index')->with('ok', 'Alimentacion registrada para ' . count($data['animales']) . ' animales.');
    }

    // Muestra el formulario de edicion de un registro existente.
    public function edit($id)
    {
        if (!auth()->user()->tienePrivilegio('editar_alimentacion')) {
            return $this->denegado('editar_alimentacion');
        }

        $registro = Alimentacion::find($id);
        if (!$registro) {
            abort(404);
        }

        return view('alimentacion.edit', [
            'registro' => $registro,
            'animales' => Animal::orderBy('codigo')->get(['id_animal', 'codigo', 'especie']),
            'tiposPienso' => Pienso::where('activo', true)->orderBy('nombre')->get(['id_pienso', 'nombre']),
        ]);
    }

    // Actualiza un registro existente y asigna como responsable al usuario que edita.
    public function update(Request $request, $id)
    {
        if (!auth()->user()->tienePrivilegio('editar_alimentacion')) {
            return $this->denegado('editar_alimentacion');
        }

        $registro = Alimentacion::find($id);
        if (!$registro) {
            abort(404);
        }

        $data = $this->validarRegistro($request);
        $data['id_usuario'] = auth()->user()->id_usuario;
        $data['id_animal'] = $data['id_animal'] ?: null;

        $registro->update($data);

        return redirect()->route('alimentacion.index')->with('ok', 'Registro de alimentacion actualizado correctamente');
    }

    // Elimina un registro de alimentacion.
    public function destroy($id)
    {
        if (!auth()->user()->tienePrivilegio('borrar_alimentacion')) {
            return $this->denegado('borrar_alimentacion');
        }

        $registro = Alimentacion::find($id);
        if (!$registro) {
            abort(404);
        }

        $registro->delete();

        return redirect()->route('alimentacion.index')->with('ok', 'Registro de alimentacion eliminado correctamente');
    }

    // Devuelve el listado filtrado en JSON para actualizar la tabla sin recargar la pagina.
    public function apiListado(Request $request)
    {
        if (!auth()->user()->tienePrivilegio('ver_alimentacion')) {
            return response()->json([
                'ok' => false,
                'mensaje' => 'Acceso denegado',
            ], 403);
        }

        $registros = $this->construirResumenAlimentacion($request);

        return response()->json([
            'ok' => true,
            'data' => $registros,
        ]);
    }
}
