<section>
    <header>
        <h2 class="profile-card-title">Contraseña</h2>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="profile-form">
        @csrf
        @method('put')

        <div class="profile-field">
            <label class="profile-label" for="update_password_current_password">Contraseña actual</label>
            <input id="update_password_current_password" name="current_password" type="password" class="profile-input" autocomplete="current-password" />
            @if($errors->updatePassword->get('current_password'))<div class="profile-error">{{ $errors->updatePassword->first('current_password') }}</div>@endif
        </div>

        <div class="profile-field">
            <label class="profile-label" for="update_password_password">Nueva contraseña</label>
            <input id="update_password_password" name="password" type="password" class="profile-input" autocomplete="new-password" />
            @if($errors->updatePassword->get('password'))<div class="profile-error">{{ $errors->updatePassword->first('password') }}</div>@endif
        </div>

        <div class="profile-field">
            <label class="profile-label" for="update_password_password_confirmation">Confirmar contraseña</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="profile-input" autocomplete="new-password" />
            @if($errors->updatePassword->get('password_confirmation'))<div class="profile-error">{{ $errors->updatePassword->first('password_confirmation') }}</div>@endif
        </div>

        <div class="profile-actions">
            <button type="submit" class="profile-btn profile-btn-primary">Actualizar contraseña</button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="profile-status"
                >Contrasena actualizada.</p>
            @endif
        </div>
    </form>
</section>
