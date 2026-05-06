@extends('layout.plantilla')

@section('title', 'Piensos')
@section('active_nav', 'piensos')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/animal/index.css') }}">
<link rel="stylesheet" href="{{ asset('css/alimentacion/index.css') }}">
@endpush

@section('content')
<main class="main-wrap">
    <div class="top-bar">
        <div class="page-title">Piensos</div>
        <div class="d-flex gap-2 align-items-center flex-wrap">
            <a href="{{ route('alimentacion.index') }}" class="animals-top-btn animals-top-btn-secondary">
                <i class="bi bi-arrow-left me-1"></i>Alimentacion
            </a>
            <a href="{{ route('pienso.create') }}" class="animals-top-btn animals-top-btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Nuevo pienso
            </a>
        </div>
    </div>

    @if(session('ok'))
        <div class="alert-ok mb-3">
            <i class="bi bi-check-circle-fill"></i>{{ session('ok') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="table-card">
        <div class="tc-header d-flex justify-content-between align-items-start gap-3 flex-wrap">
            <div class="tabla-titulo-wrap">
                Catalogo de piensos
                <span id="contador-piensos" class="badge rounded-pill ms-1 contador-animales-badge">{{ count($piensos) }}</span>
            </div>
            <div id="estado-filtros" class="text-muted estado-filtros"></div>
        </div>

        <form id="filtros-piensos" method="GET" action="{{ route('pienso.index') }}" class="animals-filters">
            <div class="animals-filters-header">
                <div class="animals-filters-title">Busqueda de piensos</div>
            </div>

            <div class="animals-filters-grid">
                <div class="animal-filter-field animal-filter-field-wide">
                    <label for="filtro-q" class="animal-filter-label">Busqueda general</label>
                    <input type="text" id="filtro-q" name="q" class="animal-filter-control" placeholder="Ej: crecimiento, engorde..." value="{{ $filtros['q'] ?? '' }}">
                </div>
            </div>

            <div class="animals-filters-actions">
                <button type="reset" id="limpiar-filtros" class="animals-btn-reset">
                    Limpiar
                </button>
            </div>

            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Pienso</th>
                        <th>Estado</th>
                        <th class="th-acciones">Acciones</th>
                    </tr>
                    <tr>
                        <th></th>
                        <th>
                            <select id="filtro-estado" name="estado" class="form-select form-select-sm">
                                <option value="">Todos</option>
                                <option value="activo" {{ ($filtros['estado'] ?? '') === 'activo' ? 'selected' : '' }}>Activo</option>
                                <option value="inactivo" {{ ($filtros['estado'] ?? '') === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                            </select>
                        </th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="tabla-piensos-body">
                    @forelse($piensos as $pienso)
                    <tr>
                        <td><span class="feed-pill">{{ $pienso->nombre ?: 'Sin nombre' }}</span></td>
                        <td>
                            <span class="badge-especie {{ $pienso->activo ? 'esp-vacuno' : 'esp-otro' }}">
                                {{ $pienso->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="td-acciones">
                            <div class="d-flex gap-1 justify-content-end">
                                <a href="{{ route('pienso.edit', $pienso->id_pienso) }}" class="btn-accion btn-editar" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button
                                    type="button"
                                    class="btn-accion btn-borrar pienso-delete-trigger"
                                    title="Borrar"
                                    data-action="{{ route('pienso.destroy', $pienso->id_pienso) }}"
                                    data-nombre="{{ $pienso->nombre ?: 'Sin nombre' }}"
                                    data-can-delete="{{ (($pienso->animales_recomendados_count ?? 0) === 0 && ($pienso->alimentaciones_count ?? 0) === 0) ? '1' : '0' }}"
                                    data-animales="{{ $pienso->animales_recomendados_count ?? 0 }}"
                                    data-alimentaciones="{{ $pienso->alimentaciones_count ?? 0 }}"
                                >
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center text-muted py-4">No hay piensos registrados todavia.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </form>
    </div>

    <div class="modal fade" id="modalEliminarPienso" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" id="delete-pienso-modal-form">
                @csrf
                @method('DELETE')
                <div class="modal-content animal-delete-modal">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirmar eliminacion</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="animal-delete-word">ELIMINAR</div>
                        <p class="animal-delete-copy mb-2">Vas a borrar el pienso <strong id="delete-pienso-name">-</strong>.</p>
                        <p class="animal-delete-copy mb-3">Escribe <strong>ELIMINAR</strong> para confirmar.</p>
                        <input type="text" id="delete-pienso-confirm-input" class="form-control animal-delete-input" autocomplete="off" placeholder="Escribe ELIMINAR">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="animals-top-btn animals-top-btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" id="confirm-delete-pienso-btn" class="animals-top-btn animals-top-btn-primary" disabled>
                            <i class="bi bi-trash me-1"></i>Eliminar pienso
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="modalPiensoAsociado" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content animal-delete-modal">
                <div class="modal-header">
                    <h5 class="modal-title">No se puede eliminar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="animal-delete-word">ASOCIADO</div>
                    <p class="animal-delete-copy mb-2">El pienso <strong id="pienso-asociado-name">-</strong> no se puede borrar porque esta en uso.</p>
                    <p class="animal-delete-copy mb-0" id="pienso-asociado-detail">Hay animales o registros de alimentacion asociados.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="animals-top-btn animals-top-btn-secondary" data-bs-dismiss="modal">Entendido</button>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
(() => {
    const form = document.getElementById('filtros-piensos');
    const tbody = document.getElementById('tabla-piensos-body');
    const contador = document.getElementById('contador-piensos');
    const estado = document.getElementById('estado-filtros');
    const limpiar = document.getElementById('limpiar-filtros');
    const baseUrl = @json(route('api.pienso.index'));
    const deleteModalElement = document.getElementById('modalEliminarPienso');
    const deleteModal = deleteModalElement ? new bootstrap.Modal(deleteModalElement) : null;
    const deleteModalForm = document.getElementById('delete-pienso-modal-form');
    const deleteName = document.getElementById('delete-pienso-name');
    const deleteInput = document.getElementById('delete-pienso-confirm-input');
    const deleteConfirmBtn = document.getElementById('confirm-delete-pienso-btn');
    const associatedModalElement = document.getElementById('modalPiensoAsociado');
    const associatedModal = associatedModalElement ? new bootstrap.Modal(associatedModalElement) : null;
    const associatedName = document.getElementById('pienso-asociado-name');
    const associatedDetail = document.getElementById('pienso-asociado-detail');
    let debounceId = null;

    function escapeHtml(value) {
        return String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function construirQuery() {
        const params = new URLSearchParams();
        const data = new FormData(form);
        for (const [key, value] of data.entries()) {
            const limpio = String(value).trim();
            if (limpio !== '') params.set(key, limpio);
        }
        return params;
    }

    function actualizarUrl(params) {
        const next = `${form.action}${params.toString() ? `?${params.toString()}` : ''}`;
        window.history.replaceState({}, '', next);
    }

    function syncDeleteConfirmState() {
        if (!deleteConfirmBtn || !deleteInput) return;
        deleteConfirmBtn.disabled = deleteInput.value.trim().toUpperCase() !== 'ELIMINAR';
    }

    function abrirModalEliminar(triggerElement) {
        if (!deleteModal || !deleteModalForm) return;

        if (deleteName) {
            deleteName.textContent = triggerElement.dataset.nombre || '-';
        }

        deleteModalForm.action = triggerElement.dataset.action || '';

        if (deleteInput) {
            deleteInput.value = '';
        }

        syncDeleteConfirmState();
        deleteModal.show();
    }

    function mostrarAlertaAsociado(triggerElement) {
        if (!associatedModal) return;

        const nombre = triggerElement.dataset.nombre || '-';
        const animales = Number(triggerElement.dataset.animales || 0);
        const alimentaciones = Number(triggerElement.dataset.alimentaciones || 0);
        const partes = [];

        if (animales > 0) {
            partes.push(`${animales} animal${animales === 1 ? '' : 'es'} asociado${animales === 1 ? '' : 's'}`);
        }

        if (alimentaciones > 0) {
            partes.push(`${alimentaciones} registro${alimentaciones === 1 ? '' : 's'} de alimentacion`);
        }

        if (associatedName) {
            associatedName.textContent = nombre;
        }

        if (associatedDetail) {
            associatedDetail.textContent = partes.length
                ? `Tiene ${partes.join(' y ')}.`
                : 'Hay animales o registros de alimentacion asociados.';
        }

        associatedModal.show();
    }

    function renderTabla(piensos) {
        contador.textContent = Array.isArray(piensos) ? piensos.length : 0;

        if (!Array.isArray(piensos) || piensos.length === 0) {
            tbody.innerHTML = '<tr><td colspan="3" class="text-center text-muted py-4">No hay resultados con los filtros actuales.</td></tr>';
            return;
        }

        tbody.innerHTML = piensos.map((pienso) => `
            <tr>
                <td><span class="feed-pill">${escapeHtml(pienso.nombre || 'Sin nombre')}</span></td>
                <td>
                    <span class="badge-especie ${pienso.activo ? 'esp-vacuno' : 'esp-otro'}">
                        ${pienso.activo ? 'Activo' : 'Inactivo'}
                    </span>
                </td>
                <td class="td-acciones">
                    <div class="d-flex gap-1 justify-content-end">
                        <a href="${pienso.edit_url}" class="btn-accion btn-editar" title="Editar">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <button
                            type="button"
                            class="btn-accion btn-borrar pienso-delete-trigger"
                            title="Borrar"
                            data-action="${pienso.delete_url}"
                            data-nombre="${escapeHtml(pienso.nombre || 'Sin nombre')}"
                            data-can-delete="${pienso.can_delete ? '1' : '0'}"
                            data-animales="${pienso.animales_count ?? 0}"
                            data-alimentaciones="${pienso.alimentaciones_count ?? 0}"
                        >
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    async function cargarPiensos() {
        const params = construirQuery();
        const url = `${baseUrl}${params.toString() ? `?${params.toString()}` : ''}`;
        estado.textContent = 'Filtrando piensos...';

        try {
            const response = await fetch(url, { headers: { 'Accept': 'application/json' } });
            if (!response.ok) throw new Error(`HTTP ${response.status}`);

            const json = await response.json();
            renderTabla(json.data);
            actualizarUrl(params);
            estado.textContent = params.toString() ? 'Filtros aplicados' : '';
        } catch (error) {
            contador.textContent = '0';
            tbody.innerHTML = '<tr><td colspan="3" class="text-center text-danger py-4">Error cargando piensos.</td></tr>';
            estado.textContent = 'No se pudieron aplicar los filtros';
        }
    }

    function programarCarga() {
        clearTimeout(debounceId);
        debounceId = setTimeout(cargarPiensos, 250);
    }

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        cargarPiensos();
    });

    document.addEventListener('click', (event) => {
        const button = event.target.closest('.pienso-delete-trigger');
        if (!button) {
            return;
        }

        if (button.dataset.canDelete !== '1') {
            mostrarAlertaAsociado(button);
            return;
        }

        abrirModalEliminar(button);
    });

    form.querySelectorAll('input, select').forEach((field) => {
        const eventName = field.tagName === 'SELECT' ? 'change' : 'input';
        field.addEventListener(eventName, programarCarga);
    });

    limpiar.addEventListener('click', () => {
        window.setTimeout(() => {
            estado.textContent = '';
            cargarPiensos();
        }, 0);
    });

    if (deleteInput) {
        deleteInput.addEventListener('input', syncDeleteConfirmState);
    }

    if (deleteModalForm) {
        deleteModalForm.addEventListener('submit', (event) => {
            if (deleteInput.value.trim().toUpperCase() === 'ELIMINAR') return;

            event.preventDefault();
            syncDeleteConfirmState();
        });
    }
})();
</script>
@endpush
