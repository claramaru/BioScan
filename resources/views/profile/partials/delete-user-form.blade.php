<section>
    <header>
        <h2 class="profile-card-title">Eliminar cuenta</h2>
    </header>

    <button
        type="button"
        class="profile-btn profile-btn-danger"
        id="open-delete-account-modal"
    >Eliminar cuenta</button>

    <div
        class="profile-delete-modal-backdrop"
        id="delete-account-modal"
        data-open="{{ $errors->userDeletion->isNotEmpty() ? 'true' : 'false' }}"
        hidden
    >
        <form method="post" action="{{ route('profile.destroy') }}" class="profile-delete-modal" role="dialog" aria-modal="true" aria-labelledby="delete-account-title">
            @csrf
            @method('delete')

            <div class="profile-delete-modal-header">
                <h2 class="profile-card-title" id="delete-account-title">Confirmar eliminacion</h2>
                <button type="button" class="profile-delete-modal-close" id="close-delete-account-modal" aria-label="Cerrar">
                    &times;
                </button>
            </div>

            <div class="profile-field mt-4">
                <label class="profile-label" for="delete-account-password">Contrasena</label>
                <input
                    id="delete-account-password"
                    name="password"
                    type="password"
                    class="profile-input"
                    placeholder="Contrasena"
                />

                @if($errors->userDeletion->get('password'))<div class="profile-error">{{ $errors->userDeletion->first('password') }}</div>@endif
            </div>

            <div class="profile-modal-actions">
                <button type="button" class="profile-btn profile-btn-secondary" id="cancel-delete-account-modal">
                    Cancelar
                </button>

                <button type="submit" class="profile-btn profile-btn-danger">
                    Eliminar cuenta
                </button>
            </div>
        </form>
    </div>
</section>

@push('scripts')
<script>
(() => {
    const modal = document.getElementById('delete-account-modal');
    const openButton = document.getElementById('open-delete-account-modal');
    const closeButton = document.getElementById('close-delete-account-modal');
    const cancelButton = document.getElementById('cancel-delete-account-modal');
    const passwordInput = document.getElementById('delete-account-password');

    if (!modal || !openButton) return;

    function openModal() {
        modal.hidden = false;
        document.body.classList.add('profile-modal-open');
        window.setTimeout(() => passwordInput?.focus(), 50);
    }

    function closeModal() {
        modal.hidden = true;
        document.body.classList.remove('profile-modal-open');
    }

    openButton.addEventListener('click', openModal);
    closeButton?.addEventListener('click', closeModal);
    cancelButton?.addEventListener('click', closeModal);

    modal.addEventListener('click', (event) => {
        if (event.target === modal) {
            closeModal();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !modal.hidden) {
            closeModal();
        }
    });

    if (modal.dataset.open === 'true') {
        openModal();
    }
})();
</script>
@endpush
