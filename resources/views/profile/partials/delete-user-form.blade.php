<section>
    <header>
        <h2 class="profile-card-title">Eliminar cuenta</h2>
    </header>

    <button
        type="button"
        class="profile-btn profile-btn-danger"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >Eliminar cuenta</button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="profile-card-title">Confirmar eliminacion</h2>

            <div class="profile-field mt-4">
                <label class="profile-label" for="password">Contraseña</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    class="profile-input"
                    placeholder="Contraseña"
                />

                @if($errors->userDeletion->get('password'))<div class="profile-error">{{ $errors->userDeletion->first('password') }}</div>@endif
            </div>

            <div class="profile-modal-actions">
                <button type="button" class="profile-btn profile-btn-secondary" x-on:click="$dispatch('close')">
                    Cancelar
                </button>

                <button type="submit" class="profile-btn profile-btn-danger">
                    Eliminar cuenta
                </button>
            </div>
        </form>
    </x-modal>
</section>
