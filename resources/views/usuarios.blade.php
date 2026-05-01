@extends('layout.plantilla')

@section('title', 'Usuarios')
@section('active_nav', 'usuarios')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/usuarios.css') }}">
@endpush

@section('content')
<main class="main-wrap">
    <div class="top-bar">
        <div>
            <div class="page-title">Usuarios</div>
        </div>
    </div>

    @if(session('ok'))
        <div class="alert alert-success users-alert" role="alert">
            <i class="bi bi-check-circle-fill"></i>
            <span>{{ session('ok') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger users-alert" role="alert">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <div>
                <strong>Revisa los datos introducidos.</strong>
                <ul class="mb-0 mt-2 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <section class="users-summary">
        <div class="summary-card">
            <span class="summary-label">Total de usuarios</span>
            <strong class="summary-value">{{ $totalUsuarios }}</strong>
        </div>

        <div class="summary-card">
            <span class="summary-label">Roles disponibles</span>
            <div class="roles-list">
                @foreach($roles as $rol)
                    <span class="role-pill">
                        {{ ucfirst($rol->nombre) }}
                        <strong>{{ $resumenRoles[$rol->id_rol] ?? 0 }}</strong>
                    </span>
                @endforeach
            </div>
        </div>
    </section>

    <section class="users-card">
        <div class="users-card-header">
            <div class="users-card-title">Gestion de cuentas</div>
        </div>

        <details class="users-create-box" {{ old('_create_user') ? 'open' : '' }}>
            <summary class="create-user-toggle-button" aria-label="Nuevo usuario">
                <i class="bi bi-person-plus"></i>
                <span>Nuevo usuario</span>
            </summary>

            <div class="users-create-panel-body">
                <form method="POST" action="{{ route('usuario.store') }}">
                    @csrf
                    <input type="hidden" name="_create_user" value="1">

                    <div class="form-section user-form-section create-user-section">
                        <div class="user-form-grid">
                            <div class="user-field">
                                <label class="form-label-custom" for="create-nombre">Nombre</label>
                                <input type="text" id="create-nombre" name="nombre" value="{{ old('nombre') }}" class="form-control-custom">
                            </div>

                            <div class="user-field">
                                <label class="form-label-custom" for="create-apellidos">Apellidos</label>
                                <input type="text" id="create-apellidos" name="apellidos" value="{{ old('apellidos') }}" class="form-control-custom">
                            </div>

                            <div class="user-field">
                                <label class="form-label-custom" for="create-email">Correo electronico</label>
                                <input type="email" id="create-email" name="email" value="{{ old('email') }}" class="form-control-custom">
                            </div>

                            <div class="user-field">
                                <label class="form-label-custom" for="create-rol">Rol</label>
                                <select id="create-rol" name="id_rol" class="form-control-custom">
                                    <option value="">Selecciona un rol</option>
                                    @foreach($roles as $rol)
                                        <option value="{{ $rol->id_rol }}" {{ (string) old('id_rol') === (string) $rol->id_rol ? 'selected' : '' }}>
                                            {{ ucfirst($rol->nombre) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="user-field">
                                <label class="form-label-custom" for="create-password">Contraseña</label>
                                <input type="password" id="create-password" name="password" class="form-control-custom">
                            </div>

                            <div class="user-field">
                                <label class="form-label-custom" for="create-password-confirmation">Confirmar contraseña</label>
                                <input type="password" id="create-password-confirmation" name="password_confirmation" class="form-control-custom">
                            </div>
                        </div>

                        <div class="form-footer create-user-actions">
                            <button type="submit" class="btn-guardar">
                                <i class="bi bi-person-plus"></i> Crear usuario
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </details>

        <form method="GET" action="{{ route('usuario.index') }}" class="users-filters" id="usuarios-filtros">
            <div class="users-filters-grid">
                <div class="user-field">
                    <label class="form-label-custom" for="filtro-usuarios-q">Busqueda por nombre o apellidos</label>
                    <input
                        type="text"
                        id="filtro-usuarios-q"
                        name="q"
                        value="{{ $filtros['q'] ?? '' }}"
                        class="form-control-custom"
                        placeholder="Ej: Clara o Martinez"
                    >
                </div>

                <div class="user-field">
                    <label class="form-label-custom" for="filtro-usuarios-rol">Funcion</label>
                    <select id="filtro-usuarios-rol" name="rol" class="form-control-custom">
                        <option value="">Todas</option>
                        @foreach($roles as $rol)
                            <option value="{{ $rol->id_rol }}" {{ (string) ($filtros['rol'] ?? '') === (string) $rol->id_rol ? 'selected' : '' }}>
                                {{ ucfirst($rol->nombre) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-footer users-filters-actions">
                <a href="{{ route('usuario.index') }}" class="btn-cancelar" id="usuarios-limpiar">Limpiar</a>
            </div>
        </form>

        <div class="users-table-head">
            <span>Usuario</span>
            <span>Correo</span>
            <span>Funcion</span>
            <span class="users-table-head-end">Editar</span>
        </div>

        <div class="users-list" id="usuarios-list">
            @forelse($usuarios as $usuario)
                @php
                    $esUsuarioEditado = (int) old('_edit_id', -1) === (int) $usuario->id_usuario;
                @endphp

                <details class="user-row" {{ $esUsuarioEditado ? 'open' : '' }}>
                    <summary class="user-row-summary">
                        <div class="user-main user-col-name">
                            <div class="user-avatar">{{ strtoupper(substr($usuario->nombre, 0, 1)) }}</div>
                            <div class="user-name-wrap">
                                <strong class="user-name">{{ $usuario->nombre }} {{ $usuario->apellidos }}</strong>
                                <span class="user-email user-email-mobile">{{ $usuario->email }}</span>
                            </div>
                        </div>

                        <div class="user-col">{{ $usuario->email }}</div>
                        <div class="user-col">
                            <span class="user-role-chip">{{ ucfirst($usuario->rol->nombre ?? 'Sin rol') }}</span>
                        </div>
                        <div class="user-col-toggle">
                            <i class="bi bi-chevron-down"></i>
                        </div>
                    </summary>

                    <div class="user-row-panel">
                        <form method="POST" action="{{ route('usuario.update', $usuario->id_usuario) }}" class="user-form">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="_edit_id" value="{{ $usuario->id_usuario }}">

                            <div class="form-section user-form-section">
                                <div class="form-section-title">
                                    <i class="bi bi-person-gear"></i> Datos del usuario
                                </div>

                                <div class="user-form-grid">
                                    <div class="user-field">
                                        <label class="form-label-custom" for="nombre-{{ $usuario->id_usuario }}">Nombre</label>
                                        <input
                                            type="text"
                                            id="nombre-{{ $usuario->id_usuario }}"
                                            name="nombre"
                                            value="{{ $esUsuarioEditado ? old('nombre', $usuario->nombre) : $usuario->nombre }}"
                                            class="form-control-custom"
                                        >
                                    </div>

                                    <div class="user-field">
                                        <label class="form-label-custom" for="apellidos-{{ $usuario->id_usuario }}">Apellidos</label>
                                        <input
                                            type="text"
                                            id="apellidos-{{ $usuario->id_usuario }}"
                                            name="apellidos"
                                            value="{{ $esUsuarioEditado ? old('apellidos', $usuario->apellidos) : $usuario->apellidos }}"
                                            class="form-control-custom"
                                        >
                                    </div>

                                    <div class="user-field">
                                        <label class="form-label-custom" for="email-{{ $usuario->id_usuario }}">Correo electronico</label>
                                        <input
                                            type="email"
                                            id="email-{{ $usuario->id_usuario }}"
                                            name="email"
                                            value="{{ $esUsuarioEditado ? old('email', $usuario->email) : $usuario->email }}"
                                            class="form-control-custom"
                                        >
                                    </div>

                                    <div class="user-field">
                                        <label class="form-label-custom" for="rol-{{ $usuario->id_usuario }}">Rol</label>
                                        <select id="rol-{{ $usuario->id_usuario }}" name="id_rol" class="form-control-custom">
                                            @foreach($roles as $rol)
                                                <option value="{{ $rol->id_rol }}" {{ (int) ($esUsuarioEditado ? old('id_rol', $usuario->id_rol) : $usuario->id_rol) === (int) $rol->id_rol ? 'selected' : '' }}>
                                                    {{ ucfirst($rol->nombre) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="user-field">
                                        <label class="form-label-custom" for="password-{{ $usuario->id_usuario }}">Nueva contraseña</label>
                                        <input
                                            type="password"
                                            id="password-{{ $usuario->id_usuario }}"
                                            name="password"
                                            class="form-control-custom"
                                            placeholder="Solo si quieres cambiarla"
                                        >
                                    </div>

                                    <div class="user-field">
                                        <label class="form-label-custom" for="password_confirmation-{{ $usuario->id_usuario }}">Confirmar contraseña</label>
                                        <input
                                            type="password"
                                            id="password_confirmation-{{ $usuario->id_usuario }}"
                                            name="password_confirmation"
                                            class="form-control-custom"
                                            placeholder="Repite la nueva contraseña"
                                        >
                                    </div>
                                </div>
                            </div>

                            <div class="form-footer">
                                <button type="submit" class="btn-guardar">
                                    <i class="bi bi-check-lg"></i> Guardar cambios
                                </button>

                                @if((int) $usuario->id_usuario !== (int) auth()->id())
                                    <button
                                        type="button"
                                        class="btn-eliminar"
                                        data-delete-action="{{ route('usuario.destroy', $usuario->id_usuario) }}"
                                        data-delete-name="{{ trim($usuario->nombre . ' ' . $usuario->apellidos) }}"
                                    >
                                        <i class="bi bi-trash"></i> Eliminar
                                    </button>
                                @else
                                    <span class="self-lock">Tu propia cuenta no se puede eliminar desde esta vista.</span>
                                @endif
                            </div>
                        </form>
                    </div>
                </details>
            @empty
                <div class="users-empty">
                    No hay usuarios que coincidan con los filtros actuales.
                </div>
            @endforelse
        </div>
    </section>

    <div class="users-delete-modal-backdrop" id="delete-user-modal-backdrop" hidden>
        <div class="users-delete-modal" role="dialog" aria-modal="true" aria-labelledby="delete-user-modal-title">
            <div class="users-delete-modal-header">
                <h2 id="delete-user-modal-title" class="users-delete-modal-title">Confirmar eliminación</h2>
                <button type="button" class="users-delete-modal-close" id="delete-user-modal-close" aria-label="Cerrar">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <div class="users-delete-modal-body">
                <div class="users-delete-word">ELIMINAR</div>
                <p class="users-delete-copy mb-2">Vas a borrar al usuario <strong id="delete-user-name">-</strong>.</p>
                <p class="users-delete-copy mb-3">Escribe <strong>ELIMINAR</strong> para confirmar.</p>
                <input type="text" id="delete-user-confirm-input" class="form-control users-delete-input" autocomplete="off" placeholder="Escribe ELIMINAR">
            </div>

            <div class="users-delete-modal-footer">
                <button type="button" class="btn-cancelar" id="delete-user-cancel">Cancelar</button>
                <form method="POST" id="delete-user-form">
                    @csrf
                    @method('DELETE')
                    <button type="submit" id="delete-user-confirm-btn" class="btn-eliminar" disabled>
                        <i class="bi bi-trash"></i> Eliminar usuario
                    </button>
                </form>
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script>
(() => {
    const form = document.getElementById('usuarios-filtros');
    const list = document.getElementById('usuarios-list');
    const clearLink = document.getElementById('usuarios-limpiar');
    const deleteModalBackdrop = document.getElementById('delete-user-modal-backdrop');
    const deleteModalClose = document.getElementById('delete-user-modal-close');
    const deleteModalCancel = document.getElementById('delete-user-cancel');
    const deleteUserForm = document.getElementById('delete-user-form');
    const deleteUserName = document.getElementById('delete-user-name');
    const deleteUserInput = document.getElementById('delete-user-confirm-input');
    const deleteUserConfirmBtn = document.getElementById('delete-user-confirm-btn');

    if (!form || !list) return;

    const baseUrl = @json(route('usuario.data'));
    const csrf = @json(csrf_token());
    const authUserId = @json((int) auth()->id());
    const roles = @json($roles->map(fn ($rol) => [
        'id_rol' => (int) $rol->id_rol,
        'nombre' => ucfirst($rol->nombre),
    ])->values());
    const urls = {
        update: @json(url('/usuarios/__ID__')),
        destroy: @json(url('/usuarios/__ID__')),
    };

    let debounceId = null;

    function escapeHtml(value) {
        return String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function buildParams() {
        const params = new URLSearchParams();
        const data = new FormData(form);

        for (const [key, value] of data.entries()) {
            const clean = String(value).trim();
            if (clean !== '') {
                params.set(key, clean);
            }
        }

        return params;
    }

    function updateUrl(params) {
        const next = `${form.action}${params.toString() ? `?${params.toString()}` : ''}`;
        window.history.replaceState({}, '', next);
    }

    function renderRoleOptions(user) {
        return roles.map((role) => `
            <option value="${role.id_rol}" ${Number(user.id_rol) === Number(role.id_rol) ? 'selected' : ''}>
                ${escapeHtml(role.nombre)}
            </option>
        `).join('');
    }

    function renderUser(user) {
        const fullName = `${user.nombre ?? ''} ${user.apellidos ?? ''}`.trim();
        const updateUrl = urls.update.replace('__ID__', user.id_usuario);
        const destroyUrl = urls.destroy.replace('__ID__', user.id_usuario);
        const canDelete = Number(user.id_usuario) !== authUserId;

        return `
            <details class="user-row">
                <summary class="user-row-summary">
                    <div class="user-main user-col-name">
                        <div class="user-avatar">${escapeHtml((user.nombre || '?').charAt(0).toUpperCase())}</div>
                        <div class="user-name-wrap">
                            <strong class="user-name">${escapeHtml(fullName)}</strong>
                            <span class="user-email user-email-mobile">${escapeHtml(user.email)}</span>
                        </div>
                    </div>

                    <div class="user-col">${escapeHtml(user.email)}</div>
                    <div class="user-col">
                        <span class="user-role-chip">${escapeHtml(user.rol?.nombre ? user.rol.nombre.charAt(0).toUpperCase() + user.rol.nombre.slice(1) : 'Sin rol')}</span>
                    </div>
                    <div class="user-col-toggle">
                        <i class="bi bi-chevron-down"></i>
                    </div>
                </summary>

                <div class="user-row-panel">
                    <form method="POST" action="${updateUrl}" class="user-form">
                        <input type="hidden" name="_token" value="${csrf}">
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" name="_edit_id" value="${user.id_usuario}">

                        <div class="form-section user-form-section">
                            <div class="form-section-title">
                                <i class="bi bi-person-gear"></i> Datos del usuario
                            </div>

                            <div class="user-form-grid">
                                <div class="user-field">
                                    <label class="form-label-custom" for="nombre-${user.id_usuario}">Nombre</label>
                                    <input type="text" id="nombre-${user.id_usuario}" name="nombre" value="${escapeHtml(user.nombre)}" class="form-control-custom">
                                </div>

                                <div class="user-field">
                                    <label class="form-label-custom" for="apellidos-${user.id_usuario}">Apellidos</label>
                                    <input type="text" id="apellidos-${user.id_usuario}" name="apellidos" value="${escapeHtml(user.apellidos)}" class="form-control-custom">
                                </div>

                                <div class="user-field">
                                    <label class="form-label-custom" for="email-${user.id_usuario}">Correo electronico</label>
                                    <input type="email" id="email-${user.id_usuario}" name="email" value="${escapeHtml(user.email)}" class="form-control-custom">
                                </div>

                                <div class="user-field">
                                    <label class="form-label-custom" for="rol-${user.id_usuario}">Rol</label>
                                    <select id="rol-${user.id_usuario}" name="id_rol" class="form-control-custom">
                                        ${renderRoleOptions(user)}
                                    </select>
                                </div>

                                <div class="user-field">
                                    <label class="form-label-custom" for="password-${user.id_usuario}">Nueva contraseña</label>
                                    <input type="password" id="password-${user.id_usuario}" name="password" class="form-control-custom" placeholder="Solo si quieres cambiarla">
                                </div>

                                <div class="user-field">
                                    <label class="form-label-custom" for="password_confirmation-${user.id_usuario}">Confirmar contraseña</label>
                                    <input type="password" id="password_confirmation-${user.id_usuario}" name="password_confirmation" class="form-control-custom" placeholder="Repite la nueva contraseña">
                                </div>
                            </div>
                        </div>

                        <div class="form-footer">
                            <button type="submit" class="btn-guardar">
                                <i class="bi bi-check-lg"></i> Guardar cambios
                            </button>
                            ${canDelete
                                ? `
                                    <button
                                        type="button"
                                        class="btn-eliminar"
                                        data-delete-action="${destroyUrl}"
                                        data-delete-name="${escapeHtml(fullName)}"
                                    >
                                        <i class="bi bi-trash"></i> Eliminar
                                    </button>
                                `
                                : '<span class="self-lock">Tu propia cuenta no se puede eliminar desde esta vista.</span>'}
                        </div>
                    </form>
                </div>
            </details>
        `;
    }

    function renderUsers(users) {
        if (!Array.isArray(users) || users.length === 0) {
            list.innerHTML = `
                <div class="users-empty">
                    No hay usuarios que coincidan con los filtros actuales.
                </div>
            `;
            return;
        }

        list.innerHTML = users.map(renderUser).join('');
        attachDeleteEvents();
    }

    function closeDeleteModal() {
        if (!deleteModalBackdrop) return;

        deleteModalBackdrop.hidden = true;
        document.body.classList.remove('users-modal-open');
        deleteUserForm.removeAttribute('action');
        deleteUserName.textContent = '-';
        deleteUserInput.value = '';
        deleteUserConfirmBtn.disabled = true;
    }

    function openDeleteModal(action, name) {
        if (!deleteModalBackdrop) return;

        deleteUserForm.setAttribute('action', action);
        deleteUserName.textContent = name || '-';
        deleteUserInput.value = '';
        deleteUserConfirmBtn.disabled = true;
        deleteModalBackdrop.hidden = false;
        document.body.classList.add('users-modal-open');
        window.setTimeout(() => deleteUserInput.focus(), 30);
    }

    function attachDeleteEvents() {
        list.querySelectorAll('[data-delete-action]').forEach((button) => {
            button.addEventListener('click', () => {
                openDeleteModal(button.dataset.deleteAction, button.dataset.deleteName);
            });
        });
    }

    async function loadUsers() {
        const params = buildParams();
        const url = `${baseUrl}${params.toString() ? `?${params.toString()}` : ''}`;

        try {
            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                },
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const json = await response.json();
            renderUsers(json.data);
            updateUrl(params);
        } catch (error) {
            list.innerHTML = `
                <div class="users-empty">
                    No se pudieron cargar los usuarios.
                </div>
            `;
        }
    }

    function scheduleLoad() {
        clearTimeout(debounceId);
        debounceId = setTimeout(loadUsers, 250);
    }

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        loadUsers();
    });

    form.querySelectorAll('input, select').forEach((field) => {
        const eventName = field.tagName === 'SELECT' ? 'change' : 'input';
        field.addEventListener(eventName, scheduleLoad);
    });

    if (clearLink) {
        clearLink.addEventListener('click', (event) => {
            event.preventDefault();
            form.reset();
            clearTimeout(debounceId);
            loadUsers();
        });
    }

    if (deleteUserInput) {
        deleteUserInput.addEventListener('input', () => {
            deleteUserConfirmBtn.disabled = deleteUserInput.value.trim().toUpperCase() !== 'ELIMINAR';
        });
    }

    [deleteModalClose, deleteModalCancel].forEach((button) => {
        if (!button) return;
        button.addEventListener('click', closeDeleteModal);
    });

    if (deleteModalBackdrop) {
        deleteModalBackdrop.addEventListener('click', (event) => {
            if (event.target === deleteModalBackdrop) {
                closeDeleteModal();
            }
        });
    }

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && deleteModalBackdrop && !deleteModalBackdrop.hidden) {
            closeDeleteModal();
        }
    });

    attachDeleteEvents();
})();
</script>
@endpush
