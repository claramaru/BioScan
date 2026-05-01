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
})();
</script>
@endpush
