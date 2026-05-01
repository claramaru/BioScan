@extends('layout.plantilla')

@section('title', 'Alimentacion')
@section('active_nav', 'alimentacion')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/animal/index.css') }}">
<link rel="stylesheet" href="{{ asset('css/alimentacion/index.css') }}">
@endpush

@section('content')
<main class="main-wrap">
    <div class="top-bar">
        <div class="page-title">Alimentacion</div>
        <div class="d-flex gap-2 align-items-center flex-wrap">
            <a href="{{ route('dashboard') }}" class="animals-top-btn animals-top-btn-secondary">
                <i class="bi bi-grid-1x2-fill me-1"></i>Dashboard
            </a>
            @if(!empty($puedeGestionarPienos) && $puedeGestionarPienos)
                <a href="{{ route('pienso.index') }}" class="animals-top-btn animals-top-btn-secondary">
                    <i class="bi bi-box-seam me-1"></i>Piensos
                </a>
            @endif
            @if(!empty($puedeCrearCabecera) && $puedeCrearCabecera)
                <a href="{{ route('alimentacion.create') }}" class="animals-top-btn animals-top-btn-primary">
                    <i class="bi bi-plus-lg me-1"></i>Nueva alimentacion
                </a>
            @endif
        </div>
    </div>

    @if(session('ok'))
        <div class="alert-ok mb-3">
            <i class="bi bi-check-circle-fill"></i>{{ session('ok') }}
        </div>
    @endif

    <section class="animals-summary">
        <div class="animals-summary-card">
            <span class="animals-summary-label">Cantidad total</span>
            <strong class="animals-summary-value">{{ number_format($resumen['cantidad_total'] ?? 0, 2, ',', '.') }} kg</strong>
        </div>

        <div class="animals-summary-card">
            <span class="animals-summary-label">Resumen de alimentacion</span>
            <div class="animals-species-list">
                <span class="animals-species-pill">
                    <i class="bi bi-list-check"></i>
                    Registros
                    <strong>{{ $resumen['total'] ?? 0 }}</strong>
                </span>
                <span class="animals-species-pill">
                    <i class="bi bi-box-seam"></i>
                    Piensos
                    <strong>{{ $resumen['tipos_total'] ?? 0 }}</strong>
                </span>
                <span class="animals-species-pill">
                    <i class="bi bi-calendar2-week"></i>
                    Ultima
                    <strong>{{ !empty($resumen['ultima_fecha']) ? \Carbon\Carbon::parse($resumen['ultima_fecha'])->format('d/m') : '-' }}</strong>
                </span>
            </div>
        </div>
    </section>

    <div class="table-card">
        <div class="tc-header d-flex justify-content-between align-items-start gap-3 flex-wrap">
            <div class="tabla-titulo-wrap">
                Lista de alimentacion
                <span id="contador-registros" class="badge rounded-pill ms-1 contador-animales-badge">{{ count($registros) }}</span>
            </div>
            <div id="estado-filtros" class="text-muted estado-filtros"></div>
        </div>

        <form id="filtros-alimentacion" method="GET" action="{{ route('alimentacion.index') }}" class="animals-filters">
            <div class="animals-filters-header">
                <div class="animals-filters-title">Busqueda de alimentacion</div>
            </div>

            <div class="animals-filters-grid">
                <div class="animal-filter-field animal-filter-field-wide">
                    <label for="filtro-q" class="animal-filter-label">Busqueda general</label>
                    <input type="text" id="filtro-q" name="q" class="animal-filter-control" placeholder="Ej: pienso, animal, especie..." value="{{ $filtros['q'] ?? '' }}">
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
                        <th>Responsable</th>
                        <th>Total animales</th>
                        <th>Total kg</th>
                    </tr>
                    <tr>
                        <th>
                            <select id="filtro-tipo" name="tipo_pienso" class="form-select form-select-sm">
                                <option value="">Todos</option>
                                @foreach($tiposPienso as $tipo)
                                    <option value="{{ data_get($tipo, 'id_pienso') }}" {{ (string) ($filtros['tipo_pienso'] ?? '') === (string) data_get($tipo, 'id_pienso') ? 'selected' : '' }}>{{ data_get($tipo, 'nombre') }}</option>
                                @endforeach
                            </select>
                        </th>
                        <th>
                            <select id="filtro-responsable" name="id_usuario" class="form-select form-select-sm">
                                <option value="">Todos</option>
                                @foreach($usuariosFiltro as $usuario)
                                    <option value="{{ data_get($usuario, 'id_usuario') }}" {{ (string) ($filtros['id_usuario'] ?? '') === (string) data_get($usuario, 'id_usuario') ? 'selected' : '' }}>
                                        {{ trim(data_get($usuario, 'nombre') . ' ' . data_get($usuario, 'apellidos')) }}
                                    </option>
                                @endforeach
                            </select>
                        </th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="tabla-alimentacion-body">
                    @forelse($registros as $registro)
                        <tr>
                            <td><span class="feed-pill">{{ data_get($registro, 'tipo_pienso') ?: 'Sin pienso' }}</span></td>
                            <td>{{ data_get($registro, 'responsable', 'Sin responsable') }}</td>
                            <td>
                                <div class="feed-animal-cell">
                                    <div class="feed-animal-icon">
                                        <i class="bi bi-diagram-3-fill"></i>
                                    </div>
                                    <div>
                                        <strong>{{ data_get($registro, 'total_animales', 0) }} animales</strong>
                                    </div>
                                </div>
                            </td>
                            <td>{{ number_format((float) data_get($registro, 'total_kg', 0), 2, ',', '.') }} kg</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">No hay registros de alimentacion todavia.</td>
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
    const form = document.getElementById('filtros-alimentacion');
    const tbody = document.getElementById('tabla-alimentacion-body');
    const contador = document.getElementById('contador-registros');
    const estado = document.getElementById('estado-filtros');
    const limpiar = document.getElementById('limpiar-filtros');
    const baseUrl = @json(route('api.alimentacion.index'));
    const colspan = 4;
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
            if (limpio !== '') {
                params.set(key, limpio);
            }
        }

        return params;
    }

    function actualizarUrl(params) {
        const next = `${form.action}${params.toString() ? `?${params.toString()}` : ''}`;
        window.history.replaceState({}, '', next);
    }

    function formatCantidad(value) {
        const numero = Number(value ?? 0);
        return `${numero.toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} kg`;
    }

    function renderTabla(registros) {
        contador.textContent = Array.isArray(registros) ? registros.length : 0;

        if (!Array.isArray(registros) || registros.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="${colspan}" class="text-center text-muted py-4">
                        No hay resultados con los filtros actuales.
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = registros.map((registro) => `
            <tr>
                <td><span class="feed-pill">${escapeHtml(registro.tipo_pienso || 'Sin pienso')}</span></td>
                <td>${escapeHtml(registro.responsable || 'Sin responsable')}</td>
                <td>
                    <div class="feed-animal-cell">
                        <div class="feed-animal-icon">
                            <i class="bi bi-diagram-3-fill"></i>
                        </div>
                        <div>
                            <strong>${escapeHtml(registro.total_animales ?? 0)} animales</strong>
                        </div>
                    </div>
                </td>
                <td>${formatCantidad(registro.total_kg)}</td>
            </tr>
        `).join('');
    }

    async function cargarRegistros() {
        const params = construirQuery();
        const url = `${baseUrl}${params.toString() ? `?${params.toString()}` : ''}`;
        estado.textContent = 'Filtrando registros...';

        try {
            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const json = await response.json();
            renderTabla(json.data);
            actualizarUrl(params);
            estado.textContent = params.toString() ? 'Filtros aplicados' : '';
        } catch (error) {
            contador.textContent = '0';
            tbody.innerHTML = `
                <tr>
                    <td colspan="${colspan}" class="text-center text-danger py-4">
                        Error cargando alimentacion.
                    </td>
                </tr>
            `;
            estado.textContent = 'No se pudieron aplicar los filtros';
        }
    }

    function programarCarga() {
        clearTimeout(debounceId);
        debounceId = setTimeout(cargarRegistros, 250);
    }

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        cargarRegistros();
    });

    form.querySelectorAll('input, select').forEach((field) => {
        const eventName = field.tagName === 'SELECT' ? 'change' : 'input';
        field.addEventListener(eventName, programarCarga);
    });

    limpiar.addEventListener('click', () => {
        window.setTimeout(() => {
            estado.textContent = '';
            cargarRegistros();
        }, 0);
    });
})();
</script>
@endpush
