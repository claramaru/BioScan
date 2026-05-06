<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use App\Models\FichaMedica;
use App\Models\Privilegio;
use App\Models\User;
use Illuminate\Http\Request;

class FichaMedicaController extends Controller
{
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

    // Permite diagnostico, tratamiento y edicion clinica completa.
    private function puedeGestionarTodo(): bool
    {
        return auth()->check()
            && auth()->user()->tienePrivilegio('gestionar_ficha_medica_completa');
    }

    // Permite crear una revision; el alcance se decide por privilegios adicionales.
    private function puedeCrearRevision(): bool
    {
        return auth()->check()
            && auth()->user()->tienePrivilegio('crear_ficha_medica');
    }

    // Permite editar observaciones aunque no se tenga acceso clinico completo.
    private function puedeEditarObservaciones(): bool
    {
        return $this->puedeGestionarTodo()
            || (
                auth()->check()
                && auth()->user()->tienePrivilegio('editar_observaciones_ficha_medica')
            );
    }

    // Permite eliminar fichas medicas.
    private function puedeBorrarFicha(): bool
    {
        return auth()->check()
            && auth()->user()->tienePrivilegio('borrar_ficha_medica');
    }

    // Calcula el estado visual de la ficha a partir de los campos existentes.
    private function estadoFicha($ficha): array
    {
        $diagnostico = trim((string) data_get($ficha, 'diagnostico', ''));
        $tratamiento = trim((string) data_get($ficha, 'tratamiento', ''));
        $observaciones = trim((string) data_get($ficha, 'observaciones', ''));

        if ($diagnostico !== '' || $tratamiento !== '') {
            return ['texto' => 'Con tratamiento', 'clase' => 'medical-pill-success'];
        }

        if ($observaciones !== '') {
            return ['texto' => 'Seguimiento', 'clase' => 'medical-pill-warning'];
        }

        return ['texto' => 'Pendiente', 'clase' => 'medical-pill-muted'];
    }

    // Consulta base reutilizable para el listado HTML y para la API asincrona.
    private function construirConsulta(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $estado = trim((string) $request->query('estado', ''));
        $fecha = trim((string) $request->query('fecha', ''));
        $animal = trim((string) $request->query('id_animal', ''));
        $codigo = trim((string) $request->query('codigo', ''));
        $especie = trim((string) $request->query('especie', ''));
        $diagnostico = trim((string) $request->query('diagnostico', ''));
        $tratamiento = trim((string) $request->query('tratamiento', ''));
        $observaciones = trim((string) $request->query('observaciones', ''));
        $responsable = trim((string) $request->query('responsable', ''));
        $usuario = trim((string) $request->query('id_usuario', ''));

        $consulta = FichaMedica::query()
            ->with(['animal:id_animal,codigo,especie,lote', 'usuario:id_usuario,nombre,apellidos'])
            ->leftJoin('animal', 'ficha_medica.id_animal', '=', 'animal.id_animal')
            ->leftJoin('usuario', 'ficha_medica.id_usuario', '=', 'usuario.id_usuario')
            ->select('ficha_medica.*');

        // Busqueda general sobre animal, ficha y responsable.
        if ($q !== '') {
            $consulta->where(function ($query) use ($q) {
                $query->where('animal.codigo', 'like', '%' . $q . '%')
                    ->orWhere('animal.especie', 'like', '%' . $q . '%')
                    ->orWhere('animal.lote', 'like', '%' . $q . '%')
                    ->orWhere('ficha_medica.diagnostico', 'like', '%' . $q . '%')
                    ->orWhere('ficha_medica.tratamiento', 'like', '%' . $q . '%')
                    ->orWhere('ficha_medica.observaciones', 'like', '%' . $q . '%')
                    ->orWhere('usuario.nombre', 'like', '%' . $q . '%')
                    ->orWhere('usuario.apellidos', 'like', '%' . $q . '%');
            });
        }

        // Filtros especificos por fecha, animal, campos medicos y responsable.
        if ($fecha !== '') {
            $consulta->whereDate('ficha_medica.fecha', $fecha);
        }

        if ($animal !== '') {
            $consulta->where('ficha_medica.id_animal', $animal);
        }

        if ($codigo !== '') {
            $consulta->where('animal.codigo', 'like', '%' . $codigo . '%');
        }

        if ($especie !== '') {
            $consulta->where('animal.especie', 'like', '%' . $especie . '%');
        }

        if ($diagnostico !== '') {
            $consulta->where('ficha_medica.diagnostico', 'like', '%' . $diagnostico . '%');
        }

        if ($tratamiento !== '') {
            $consulta->where('ficha_medica.tratamiento', 'like', '%' . $tratamiento . '%');
        }

        if ($observaciones !== '') {
            $consulta->where('ficha_medica.observaciones', 'like', '%' . $observaciones . '%');
        }

        if ($responsable !== '') {
            $consulta->where(function ($query) use ($responsable) {
                $query->where('usuario.nombre', 'like', '%' . $responsable . '%')
                    ->orWhere('usuario.apellidos', 'like', '%' . $responsable . '%');
            });
        }

        if ($usuario !== '') {
            $consulta->where('ficha_medica.id_usuario', $usuario);
        }

        // Estado derivado: pendiente no tiene diagnostico, tratamiento ni observaciones.
        if ($estado === 'pendiente') {
            $consulta->where(function ($query) {
                $query->whereNull('ficha_medica.diagnostico')->orWhere('ficha_medica.diagnostico', '');
            })->where(function ($query) {
                $query->whereNull('ficha_medica.tratamiento')->orWhere('ficha_medica.tratamiento', '');
            })->where(function ($query) {
                $query->whereNull('ficha_medica.observaciones')->orWhere('ficha_medica.observaciones', '');
            });
        }

        // Estado derivado: seguimiento solo tiene observaciones.
        if ($estado === 'seguimiento') {
            $consulta->where(function ($query) {
                $query->whereNull('ficha_medica.diagnostico')->orWhere('ficha_medica.diagnostico', '');
            })->where(function ($query) {
                $query->whereNull('ficha_medica.tratamiento')->orWhere('ficha_medica.tratamiento', '');
            })->whereNotNull('ficha_medica.observaciones')
                ->where('ficha_medica.observaciones', '<>', '');
        }

        // Estado derivado: tratamiento si hay diagnostico o tratamiento informado.
        if ($estado === 'tratamiento') {
            $consulta->where(function ($query) {
                $query->whereNotNull('ficha_medica.diagnostico')->where('ficha_medica.diagnostico', '<>', '')
                    ->orWhere(function ($subquery) {
                        $subquery->whereNotNull('ficha_medica.tratamiento')->where('ficha_medica.tratamiento', '<>', '');
                    });
            });
        }

        return $consulta->orderByDesc('ficha_medica.fecha')->orderByDesc('ficha_medica.id_ficha');
    }

    // Calcula los contadores superiores del modulo de salud.
    private function resumen(): array
    {
        $total = FichaMedica::count();
        $pendientes = FichaMedica::where(function ($query) {
            $query->whereNull('diagnostico')->orWhere('diagnostico', '');
        })->where(function ($query) {
            $query->whereNull('tratamiento')->orWhere('tratamiento', '');
        })->where(function ($query) {
            $query->whereNull('observaciones')->orWhere('observaciones', '');
        })->count();

        $conTratamiento = FichaMedica::where(function ($query) {
            $query->whereNotNull('diagnostico')->where('diagnostico', '<>', '')
                ->orWhere(function ($subquery) {
                    $subquery->whereNotNull('tratamiento')->where('tratamiento', '<>', '');
                });
        })->count();

        return [
            'total' => $total,
            'pendientes' => $pendientes,
            'con_tratamiento' => $conTratamiento,
            'ultima_fecha' => FichaMedica::max('fecha'),
        ];
    }

    // Valida los datos comunes de una ficha medica completa.
    private function validarFicha(Request $request): array
    {
        return $request->validate([
            'id_animal' => 'required|integer|exists:animal,id_animal',
            'fecha' => 'required|date',
            'diagnostico' => 'nullable|string|max:2000',
            'tratamiento' => 'nullable|string|max:2000',
            'observaciones' => 'nullable|string|max:2000',
        ]);
    }

    // Listado principal de salud con permisos, filtros, resumenes y datos auxiliares.
    public function index(Request $request)
    {
        if (!auth()->user()->tienePrivilegio('ver_ficha_medica')) {
            return $this->denegado('ver_ficha_medica');
        }

        $fichas = $this->construirConsulta($request)->paginate(12)->withQueryString();

        return view('ficha_medica.index', [
            'fichas' => $fichas,
            'animalesFiltro' => Animal::orderBy('codigo')->get(['id_animal', 'codigo', 'especie']),
            'usuariosFiltro' => User::orderBy('nombre')->orderBy('apellidos')->get(['id_usuario', 'nombre', 'apellidos']),
            'resumen' => $this->resumen(),
            'filtros' => [
                'q' => trim((string) $request->query('q', '')),
                'estado' => trim((string) $request->query('estado', '')),
                'fecha' => trim((string) $request->query('fecha', '')),
                'id_animal' => trim((string) $request->query('id_animal', '')),
                'codigo' => trim((string) $request->query('codigo', '')),
                'especie' => trim((string) $request->query('especie', '')),
                'diagnostico' => trim((string) $request->query('diagnostico', '')),
                'tratamiento' => trim((string) $request->query('tratamiento', '')),
                'observaciones' => trim((string) $request->query('observaciones', '')),
                'responsable' => trim((string) $request->query('responsable', '')),
                'id_usuario' => trim((string) $request->query('id_usuario', '')),
            ],
            'puedeCrear' => $this->puedeCrearRevision(),
            'puedeGestionarTodo' => $this->puedeGestionarTodo(),
            'puedeEditarObservaciones' => $this->puedeEditarObservaciones(),
            'puedeBorrarFicha' => $this->puedeBorrarFicha(),
            'estadoFicha' => fn ($ficha) => $this->estadoFicha($ficha),
        ]);
    }

    // Muestra el formulario para crear una nueva revision o ficha medica.
    public function create()
    {
        if (!$this->puedeCrearRevision()) {
            return $this->denegado('crear_ficha_medica');
        }

        return view('ficha_medica.create', [
            'animales' => Animal::orderBy('codigo')->get(['id_animal', 'codigo', 'especie', 'lote']),
            'puedeGestionarTodo' => $this->puedeGestionarTodo(),
        ]);
    }

    // Guarda una ficha nueva; sin gestion completa solo registra observaciones.
    public function store(Request $request)
    {
        if (!$this->puedeCrearRevision()) {
            return $this->denegado('crear_ficha_medica');
        }

        $data = $this->validarFicha($request);
        $data['id_usuario'] = auth()->user()->id_usuario;

        // Quien no tiene gestion completa deja una revision pendiente o de seguimiento.
        if (!$this->puedeGestionarTodo()) {
            $data['diagnostico'] = null;
            $data['tratamiento'] = null;
        }

        FichaMedica::create($data);

        return redirect()->route('salud.index')->with('ok', 'Registro de salud creado correctamente');
    }

    // Muestra el formulario de edicion completa o solo observaciones segun permisos.
    public function edit($id)
    {
        if (!$this->puedeGestionarTodo() && !$this->puedeEditarObservaciones()) {
            return $this->denegado('editar_ficha_medica');
        }

        $ficha = FichaMedica::with(['animal', 'usuario'])->findOrFail($id);

        return view('ficha_medica.edit', [
            'ficha' => $ficha,
            'animales' => Animal::orderBy('codigo')->get(['id_animal', 'codigo', 'especie', 'lote']),
            'puedeGestionarTodo' => $this->puedeGestionarTodo(),
        ]);
    }

    // Actualiza una ficha; gestion completa edita todo y el resto solo observaciones.
    public function update(Request $request, $id)
    {
        if (!$this->puedeGestionarTodo() && !$this->puedeEditarObservaciones()) {
            return $this->denegado('editar_ficha_medica');
        }

        $ficha = FichaMedica::findOrFail($id);

        if ($this->puedeGestionarTodo()) {
            $data = $this->validarFicha($request);
            $data['id_usuario'] = auth()->user()->id_usuario;
            $ficha->update($data);
        } else {
            $data = $request->validate([
                'observaciones' => 'nullable|string|max:2000',
            ]);
            $data['id_usuario'] = auth()->user()->id_usuario;
            $ficha->update($data);
        }

        return redirect()->route('salud.index')->with('ok', 'Registro de salud actualizado correctamente');
    }

    // Elimina una ficha medica.
    public function destroy($id)
    {
        if (!$this->puedeBorrarFicha()) {
            return $this->denegado('borrar_ficha_medica');
        }

        FichaMedica::findOrFail($id)->delete();

        return redirect()->route('salud.index')->with('ok', 'Registro de salud eliminado correctamente');
    }

    // Devuelve el listado filtrado en JSON para actualizar la tabla sin recargar la pagina.
    public function apiListado(Request $request)
    {
        if (!auth()->user()->tienePrivilegio('ver_ficha_medica')) {
            return response()->json([
                'ok' => false,
                'mensaje' => 'Acceso denegado',
            ], 403);
        }

        $fichas = $this->construirConsulta($request)
            ->get()
            ->map(function ($ficha) {
                $estado = $this->estadoFicha($ficha);

                return [
                    'id_ficha' => $ficha->id_ficha,
                    'id_animal' => $ficha->id_animal,
                    'codigo_animal' => data_get($ficha, 'animal.codigo', 'Animal #' . $ficha->id_animal),
                    'especie' => data_get($ficha, 'animal.especie', '-'),
                    'estado' => $estado,
                    'diagnostico' => $ficha->diagnostico ?: 'Sin diagnostico',
                    'tratamiento' => $ficha->tratamiento ?: 'Sin tratamiento',
                    'observaciones' => $ficha->observaciones ?: '-',
                    'fecha' => optional($ficha->fecha)->format('Y-m-d'),
                    'responsable' => trim(data_get($ficha, 'usuario.nombre', '') . ' ' . data_get($ficha, 'usuario.apellidos', '')) ?: '-',
                    'historial_url' => route('animal.historial', $ficha->id_animal),
                    'edit_url' => route('salud.edit', $ficha->id_ficha),
                    'delete_url' => route('salud.destroy', $ficha->id_ficha),
                ];
            })
            ->values();

        return response()->json([
            'ok' => true,
            'data' => $fichas,
        ]);
    }
}
