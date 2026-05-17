<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class ProfileController extends Controller
{
    // Muestra la pantalla de perfil del usuario autenticado.
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    // Actualiza los datos personales del propio usuario.
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        // Si cambia el correo, se invalida la verificacion solo si la tabla tiene esa columna.
        if (
            $request->user()->isDirty('email')
            && Schema::hasColumn($request->user()->getTable(), 'email_verified_at')
        ) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    // Elimina la cuenta propia tras confirmar la contrasena actual.
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        DB::transaction(function () use ($user) {
            if (Schema::hasTable('alimentacion') && Schema::hasColumn('alimentacion', 'id_usuario')) {
                DB::table('alimentacion')
                    ->where('id_usuario', $user->id_usuario)
                    ->update(['id_usuario' => null]);
            }

            if (Schema::hasTable('ficha_medica') && Schema::hasColumn('ficha_medica', 'id_usuario')) {
                DB::table('ficha_medica')
                    ->where('id_usuario', $user->id_usuario)
                    ->update(['id_usuario' => null]);
            }

            $user->delete();
        });

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
