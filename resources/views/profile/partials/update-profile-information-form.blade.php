<section>
    <header>
        <h2 class="profile-card-title">Información personal</h2>
        <p class="profile-card-text">Actualiza tu nombre, apellidos y dirección de correo electrónico.</p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="profile-form">
        @csrf
        @method('patch')

        <div class="profile-form-grid">
            <div class="profile-field">
                <label class="profile-label" for="nombre">Nombre</label>
                <input id="nombre" name="nombre" type="text" class="profile-input" value="{{ old('nombre', $user->nombre) }}" required autofocus autocomplete="given-name">
                @error('nombre')<div class="profile-error">{{ $message }}</div>@enderror
            </div>

            <div class="profile-field">
                <label class="profile-label" for="apellidos">Apellidos</label>
                <input id="apellidos" name="apellidos" type="text" class="profile-input" value="{{ old('apellidos', $user->apellidos) }}" required autocomplete="family-name">
                @error('apellidos')<div class="profile-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="profile-field">
            <label class="profile-label" for="email">Correo electrónico</label>
            <input id="email" name="email" type="email" class="profile-input" value="{{ old('email', $user->email) }}" required autocomplete="username">
            @error('email')<div class="profile-error">{{ $message }}</div>@enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="profile-inline-note">
                    Tu correo electrónico todavía no está verificado.
                    <button form="send-verification" class="profile-btn profile-btn-secondary" style="padding:.5rem .8rem;margin-top:.6rem;">
                        Reenviar correo de verificación
                    </button>
                </div>

                @if (session('status') === 'verification-link-sent')
                    <div class="profile-status">Hemos enviado un nuevo enlace de verificación a tu correo.</div>
                @endif
            @endif
        </div>

        <div class="profile-actions">
            <button type="submit" class="profile-btn profile-btn-primary">Guardar cambios</button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="profile-status"
                >Guardado correctamente.</p>
            @endif
        </div>
    </form>
</section>
