<?php

namespace App\Http\Controllers;

use App\Models\Pienso;
use App\Models\Privilegio;
use Illuminate\Http\Request;

class PiensoController extends Controller
{
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

    private function puedeGestionar(): bool
    {
        return auth()->check() && auth()->user()->tienePrivilegio('gestionar_pienso');
    }

    private function construirConsulta(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $estado = trim((string) $request->query('estado', ''));

        $consulta = Pienso::query()
            ->withCount(['animalesRecomendados', 'alimentaciones']);

        if ($q !== '') {
            $consulta->where('nombre', 'like', '%' . $q . '%');
        }

        if ($estado === 'activo') {
            $consulta->where('activo', true);
        }

        if ($estado === 'inactivo') {
            $consulta->where('activo', false);
        }

        return $consulta->orderBy('nombre');
    }

    public function index(Request $request)
    {
        if (!$this->puedeGestionar()) {
            return $this->denegado('gestionar_pienso');
        }

        return view('pienso.index', [
            'piensos' => $this->construirConsulta($request)->get(),
            'filtros' => [
                'q' => trim((string) $request->query('q', '')),
                'estado' => trim((string) $request->query('estado', '')),
            ],
        ]);
    }

    public function create()
    {
        if (!$this->puedeGestionar()) {
            return $this->denegado('gestionar_pienso');
        }

        return view('pienso.create');
    }

    public function store(Request $request)
    {
        if (!$this->puedeGestionar()) {
            return $this->denegado('gestionar_pienso');
        }

        $data = $request->validate([
            'nombre' => 'required|max:120|unique:pienso,nombre',
            'activo' => 'nullable|boolean',
        ]);

        Pienso::create([
            'nombre' => trim((string) $data['nombre']),
            'activo' => (bool) ($data['activo'] ?? true),
        ]);

        return redirect()->route('pienso.index')->with('ok', 'Pienso creado correctamente');
    }

    public function edit($id)
    {
        if (!$this->puedeGestionar()) {
            return $this->denegado('gestionar_pienso');
        }

        $pienso = Pienso::find($id);
        if (!$pienso) {
            abort(404);
        }

        return view('pienso.edit', compact('pienso'));
    }

    public function update(Request $request, $id)
    {
        if (!$this->puedeGestionar()) {
            return $this->denegado('gestionar_pienso');
        }

        $pienso = Pienso::find($id);
        if (!$pienso) {
            abort(404);
        }

        $data = $request->validate([
            'nombre' => 'required|max:120|unique:pienso,nombre,' . $id . ',id_pienso',
            'activo' => 'nullable|boolean',
        ]);

        $pienso->nombre = trim((string) $data['nombre']);
        $pienso->activo = (bool) ($data['activo'] ?? false);
        $pienso->save();

        return redirect()->route('pienso.index')->with('ok', 'Pienso actualizado correctamente');
    }

    public function destroy($id)
    {
        if (!$this->puedeGestionar()) {
            return $this->denegado('gestionar_pienso');
        }

        $pienso = Pienso::withCount(['animalesRecomendados', 'alimentaciones'])->find($id);
        if (!$pienso) {
            abort(404);
        }

        if ($pienso->animales_recomendados_count > 0 || $pienso->alimentaciones_count > 0) {
            return redirect()
                ->route('pienso.index')
                ->withErrors(['pienso' => 'No se puede eliminar un pienso asociado a animales o registros de alimentacion.']);
        }

        $pienso->delete();

        return redirect()->route('pienso.index')->with('ok', 'Pienso eliminado correctamente');
    }

    public function apiListado(Request $request)
    {
        if (!$this->puedeGestionar()) {
            return response()->json([
                'ok' => false,
                'mensaje' => 'Acceso denegado',
            ], 403);
        }

        return response()->json([
            'ok' => true,
            'data' => $this->construirConsulta($request)
                ->get()
                ->map(fn ($pienso) => [
                    'id_pienso' => $pienso->id_pienso,
                    'nombre' => $pienso->nombre,
                    'activo' => (bool) $pienso->activo,
                    'animales_count' => (int) $pienso->animales_recomendados_count,
                    'alimentaciones_count' => (int) $pienso->alimentaciones_count,
                    'can_delete' => $pienso->animales_recomendados_count === 0 && $pienso->alimentaciones_count === 0,
                    'edit_url' => route('pienso.edit', $pienso->id_pienso),
                    'delete_url' => route('pienso.destroy', $pienso->id_pienso),
                ])
                ->values(),
        ]);
    }
}
