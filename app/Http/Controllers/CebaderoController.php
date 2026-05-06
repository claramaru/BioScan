<?php

namespace App\Http\Controllers;

use App\Models\Cebadero;
use App\Models\Privilegio;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class CebaderoController extends Controller
{
    // Comprueba un privilegio concreto del modulo de cebaderos.
    private function puede(string $permiso): bool
    {
        return auth()->check() && auth()->user()->tienePrivilegio($permiso);
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
    private function denegadoJson()
    {
        return response()->json([
            'ok' => false,
            'mensaje' => 'Acceso denegado',
        ], 403);
    }

    // Consulta base del modulo de cebaderos reutilizada por la vista y la API.
    private function construirConsultaCebaderos(Request $request): Builder
    {
        $q = trim((string) $request->query('q', ''));
        $estado = trim((string) $request->query('estado', ''));
        $especie = trim((string) $request->query('especie', ''));

        $consulta = Cebadero::with(['animales:id_animal,id_cebadero,especie'])
            ->withCount('animales');

        // Busqueda general por nombre, ubicacion o especies presentes.
        if ($q !== '') {
            $consulta->where(function ($query) use ($q) {
                $query->where('nombre', 'like', '%' . $q . '%')
                    ->orWhere('ubicacion', 'like', '%' . $q . '%')
                    ->orWhereHas('animales', function ($animalQuery) use ($q) {
                        $animalQuery->where('especie', 'like', '%' . $q . '%');
                    });
            });
        }

        // Estado calculado segun si el cebadero tiene animales asociados.
        if ($estado !== '') {
            if ($estado === 'Con animales') {
                $consulta->has('animales');
            }

            if ($estado === 'Sin animales') {
                $consulta->doesntHave('animales');
            }
        }

        // Filtro por especie presente en los animales asociados al cebadero.
        if ($especie !== '') {
            $consulta->whereHas('animales', function ($query) use ($especie) {
                $query->where('especie', $especie);
            });
        }

        return $consulta;
    }

    // Listado principal de cebaderos con filtros, resumenes y permisos para acciones.
    public function index(Request $request)
    {
        if (!$this->puede('ver_cebadero')) {
            return $this->denegado('ver_cebadero');
        }

        $cebaderos = $this->construirConsultaCebaderos($request)
            ->orderBy('nombre')
            ->get();

        $todosLosCebaderos = Cebadero::with(['animales:id_animal,id_cebadero,especie'])
            ->withCount('animales')
            ->orderBy('nombre')
            ->get();

        return view('cebadero.index', [
            'cebaderos' => $cebaderos,
            'resumen' => [
                'total' => $todosLosCebaderos->count(),
                'con_animales' => $todosLosCebaderos->where('animales_count', '>', 0)->count(),
                'total_animales' => $todosLosCebaderos->sum('animales_count'),
                'especies' => $todosLosCebaderos
                    ->flatMap(fn ($cebadero) => $cebadero->animales->pluck('especie'))
                    ->filter()
                    ->map(fn ($especie) => trim((string) $especie))
                    ->unique()
                    ->sort()
                    ->values(),
            ],
            'filtros' => [
                'q' => trim((string) $request->query('q', '')),
                'estado' => trim((string) $request->query('estado', '')),
                'especie' => trim((string) $request->query('especie', '')),
            ],
            'puedeCrearCebadero' => $this->puede('crear_cebadero'),
            'puedeEditarCebadero' => $this->puede('editar_cebadero'),
            'puedeBorrar' => $this->puede('borrar_cebadero'),
        ]);
    }

    // Formulario de alta del cebadero.
    public function create()
    {
        if (!$this->puede('crear_cebadero')) {
            return $this->denegado('crear_cebadero');
        }

        return view('cebadero.create');
    }

    // Valida y guarda un nuevo cebadero.
    public function store(Request $request)
    {
        if (!$this->puede('crear_cebadero')) {
            return $this->denegado('crear_cebadero');
        }

        $data = $request->validate([
            'nombre' => 'required|max:255',
            'ubicacion' => 'nullable|max:255',
        ]);

        Cebadero::create($data);

        return redirect()->route('cebadero.index')->with('ok', 'Cebadero creado correctamente');
    }

    // Formulario de edicion del cebadero.
    public function edit($id)
    {
        if (!$this->puede('editar_cebadero')) {
            return $this->denegado('editar_cebadero');
        }

        $cebadero = Cebadero::withCount('animales')->find($id);
        if (!$cebadero) {
            abort(404);
        }

        return view('cebadero.edit', compact('cebadero'));
    }

    // Valida y guarda los cambios de un cebadero existente.
    public function update(Request $request, $id)
    {
        if (!$this->puede('editar_cebadero')) {
            return $this->denegado('editar_cebadero');
        }

        $request->validate([
            'nombre' => 'required|max:255',
            'ubicacion' => 'nullable|max:255',
        ]);

        $cebadero = Cebadero::find($id);
        if (!$cebadero) {
            abort(404);
        }

        $cebadero->nombre = $request->nombre;
        $cebadero->ubicacion = $request->ubicacion;
        $cebadero->save();

        return redirect()->route('cebadero.index')->with('ok', 'Cebadero actualizado correctamente');
    }

    // Elimina un cebadero si no tiene animales asociados.
    public function destroy($id)
    {
        if (!$this->puede('borrar_cebadero')) {
            return $this->denegado('borrar_cebadero');
        }

        $cebadero = Cebadero::withCount('animales')->find($id);
        if (!$cebadero) {
            abort(404);
        }

        if ($cebadero->animales_count > 0) {
            return redirect()
                ->route('cebadero.index')
                ->withErrors(['cebadero' => 'No se puede eliminar un cebadero con animales asociados.']);
        }

        $cebadero->delete();

        return redirect()->route('cebadero.index')->with('ok', 'Cebadero eliminado correctamente');
    }

    // Devuelve el listado filtrado en JSON para actualizar la tabla sin recargar la pagina.
    public function apiListado(Request $request)
    {
        if (!$this->puede('ver_cebadero')) {
            return $this->denegadoJson();
        }

        $cebaderos = $this->construirConsultaCebaderos($request)
            ->orderBy('nombre')
            ->get()
            ->map(function ($cebadero) {
                $especies = $cebadero->animales
                    ->pluck('especie')
                    ->filter()
                    ->map(fn ($especie) => trim((string) $especie))
                    ->unique()
                    ->values();

                return [
                    'id_cebadero' => $cebadero->id_cebadero,
                    'nombre' => $cebadero->nombre,
                    'ubicacion' => $cebadero->ubicacion,
                    'animales_count' => $cebadero->animales_count,
                    'estado' => $cebadero->animales_count > 0 ? 'Con animales' : 'Sin animales',
                    'estado_clase' => $cebadero->animales_count > 0 ? 'estado-operativo' : 'estado-vacio',
                    'especies' => $especies,
                    'delete_url' => route('cebadero.destroy', $cebadero->id_cebadero),
                ];
            })
            ->values();

        return response()->json([
            'ok' => true,
            'data' => $cebaderos,
        ]);
    }
}
