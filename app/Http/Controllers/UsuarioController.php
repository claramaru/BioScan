<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    // Reutilizamos la misma vista de acceso denegado para mantener coherencia.
    private function denegado()
    {
        return response()->view('acceso_denegado', [
            'permitido' => false,
            'permiso' => 'administrador',
            'rolesPermitidos' => ['administrador'],
            'mensaje' => null,
        ], 403);
    }

    // Toda la gestion de usuarios queda reservada a administradores.
    private function asegurarAdministrador()
    {
        if (!auth()->check() || !auth()->user()->esAdministrador()) {
            return $this->denegado();
        }

        return null;
    }

    private function construirConsultaUsuarios(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $rol = trim((string) $request->query('rol', ''));

        $consulta = User::with('rol');

        if ($q !== '') {
            $consulta->where(function ($query) use ($q) {
                $query->where('nombre', 'like', '%' . $q . '%')
                    ->orWhere('apellidos', 'like', '%' . $q . '%');
            });
        }

        if ($rol !== '') {
            $consulta->where('id_rol', $rol);
        }

        return $consulta;
    }

    // Listado de usuarios con filtros por texto y por rol.
    public function index(Request $request)
    {
        if ($respuesta = $this->asegurarAdministrador()) {
            return $respuesta;
        }

        $q = trim((string) $request->query('q', ''));
        $rol = trim((string) $request->query('rol', ''));

        $totalUsuarios = User::count();

        $resumenRoles = User::selectRaw('id_rol, COUNT(*) as total')
            ->groupBy('id_rol')
            ->pluck('total', 'id_rol');

        return view('usuarios', [
            'usuarios' => $this->construirConsultaUsuarios($request)->orderBy('nombre')->orderBy('apellidos')->get(),
            'roles' => Rol::orderBy('nombre')->get(),
            'totalUsuarios' => $totalUsuarios,
            'resumenRoles' => $resumenRoles,
            'filtros' => [
                'q' => $q,
                'rol' => $rol,
            ],
        ]);
    }

    public function data(Request $request)
    {
        if ($respuesta = $this->asegurarAdministrador()) {
            return $respuesta;
        }

        $usuarios = $this->construirConsultaUsuarios($request)
            ->orderBy('nombre')
            ->orderBy('apellidos')
            ->get();

        return response()->json([
            'data' => $usuarios,
        ]);
    }

    // Alta de usuario desde el panel de administracion.
    public function store(Request $request)
    {
        if ($respuesta = $this->asegurarAdministrador()) {
            return $respuesta;
        }

        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'apellidos' => ['required', 'string', 'max:150'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class, 'email')],
            'id_rol' => ['required', 'integer', Rule::exists('rol', 'id_rol')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        User::create([
            'nombre' => $datos['nombre'],
            'apellidos' => $datos['apellidos'],
            'email' => $datos['email'],
            'id_rol' => (int) $datos['id_rol'],
            'password' => $datos['password'],
        ]);

        return redirect()
            ->route('usuario.index')
            ->with('ok', 'Usuario creado correctamente.');
    }

    // Permite editar datos generales, rol y password opcionalmente.
    public function update(Request $request, $id)
    {
        if ($respuesta = $this->asegurarAdministrador()) {
            return $respuesta;
        }

        $usuario = User::find($id);
        if (!$usuario) {
            abort(404);
        }

        $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'apellidos' => ['required', 'string', 'max:150'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class, 'email')->ignore($usuario->id_usuario, 'id_usuario'),
            ],
            'id_rol' => ['required', 'integer', Rule::exists('rol', 'id_rol')],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $usuario->nombre = $request->nombre;
        $usuario->apellidos = $request->apellidos;
        $usuario->email = $request->email;
        $usuario->id_rol = (int) $request->id_rol;

        // Solo se cambia la password si el admin rellena este bloque.
        if ($request->filled('password')) {
            $usuario->password = $request->password;
        }

        $usuario->save();

        return redirect()
            ->route('usuario.index')
            ->with('ok', 'Usuario actualizado correctamente.');
    }

    // Borrado definitivo. Se bloquea la autoeliminacion para evitar perder el acceso admin activo.
    public function destroy($id)
    {
        if ($respuesta = $this->asegurarAdministrador()) {
            return $respuesta;
        }

        $usuario = User::find($id);
        if (!$usuario) {
            abort(404);
        }

        if ((int) $usuario->id_usuario === (int) auth()->id()) {
            return redirect()
                ->route('usuario.index')
                ->withErrors(['usuarios' => 'No puedes eliminar tu propia cuenta desde esta pantalla.']);
        }

        $usuario->delete();

        return redirect()
            ->route('usuario.index')
            ->with('ok', 'Usuario eliminado correctamente.');
    }
}
