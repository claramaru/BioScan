<?php

namespace App\Http\Controllers;

use App\Models\Cebadero;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class CebaderoController extends Controller
{
    // El modulo de cebaderos no tiene privilegio fino definido en este proyecto.
    // Dejamos la regla de acceso centralizada para que vista y API no diverjan.
    private function puedeVerCebaderos(): bool
    {
        return auth()->check();
    }

    private function denegado()
    {
        abort(403);
    }

    private function denegadoJson()
    {
        return response()->json([
            'ok' => false,
            'mensaje' => 'Acceso denegado',
        ], 403);
    }

    private function esAdministrador(): bool
    {
        return auth()->check() && auth()->user()->esAdministrador();
    }

    // Reutilizamos una comprobacion sencilla por rol para mantener la misma logica del listado.
    private function puedeEditarCebadero(): bool
    {
        return in_array(strtolower((string) optional(auth()->user()->rol)->nombre), ['administrador', 'supervisor'], true);
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

        if ($especie !== '') {
            $consulta->whereHas('animales', function ($query) use ($especie) {
                $query->where('especie', $especie);
            });
        }

        return $consulta;
    }

    // Listado principal de cebaderos.
    public function index(Request $request)
    {
        if (!$this->puedeVerCebaderos()) {
            return $this->denegado();
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
            'puedeCrearCebadero' => $this->esAdministrador(),
            'puedeEditarCebadero' => $this->puedeEditarCebadero(),
            'puedeBorrar' => auth()->user()->esAdministrador(),
        ]);
    }

    // Formulario de alta del cebadero, reservado a administradores.
    public function create()
    {
        if (!$this->esAdministrador()) {
            abort(403);
        }

        return view('cebadero.create');
    }

    // Guarda un nuevo cebadero.
    public function store(Request $request)
    {
        if (!$this->esAdministrador()) {
            abort(403);
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
        if (!$this->puedeEditarCebadero()) {
            abort(403);
        }

        $cebadero = Cebadero::withCount('animales')->find($id);
        if (!$cebadero) {
            abort(404);
        }

        return view('cebadero.edit', compact('cebadero'));
    }

    // Guarda los cambios del cebadero.
    public function update(Request $request, $id)
    {
        if (!$this->puedeEditarCebadero()) {
            abort(403);
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

    // API del listado para el filtrado asincrono.
    public function apiListado(Request $request)
    {
        if (!$this->puedeVerCebaderos()) {
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
                ];
            })
            ->values();

        return response()->json([
            'ok' => true,
            'data' => $cebaderos,
        ]);
    }
}
