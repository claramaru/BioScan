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

    public function index()
    {
        if (!$this->puedeGestionar()) {
            return $this->denegado('gestionar_pienso');
        }

        return view('pienso.index', [
            'piensos' => Pienso::orderBy('nombre')->get(),
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
}
