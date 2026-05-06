@extends('layout.plantilla')

@section('title', 'Salud')
@section('active_nav', 'salud')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/animal/index.css') }}">
<link rel="stylesheet" href="{{ asset('css/ficha_medica/index.css') }}">
@endpush

@section('content')
@php
    $iconoEspecie = function (?string $especie) {
        $clave = str($especie ?? '')->lower()->ascii()->value();

        if (str_contains($clave, 'porc')) {
            return ['src' => asset('images/cerdo.png'), 'clase' => 'esp-porcino', 'alt' => 'Porcino'];
        }

        if (str_contains($clave, 'avi')) {
            return ['src' => asset('images/pollo.png'), 'clase' => 'esp-avicola', 'alt' => 'Avicola'];
        }

        if (str_contains($clave, 'vac')) {
            return ['src' => asset('images/vaca.png'), 'clase' => 'esp-vacuno', 'alt' => 'Vacuno'];
        }

        return ['src' => asset('images/vaca.png'), 'clase' => 'esp-otro', 'alt' => 'Otro'];
    };

    $puedeBorrarFicha = !empty($puedeBorrarFicha) && $puedeBorrarFicha;
    $puedeEditarFicha = $puedeGestionarTodo || $puedeEditarObservaciones;
    $mostrarAcciones = $puedeEditarFicha || $puedeBorrarFicha;
@endphp

<main class="main-wrap">
    <div class="top-bar">
        <div class="page-title">Salud</div>
        <div class="d-flex gap-2 align-items-center flex-wrap">
            <a href="{{ route('dashboard') }}" class="animals-top-btn animals-top-btn-secondary">
                <i class="bi bi-grid-1x2-fill me-1"></i>Dashboard
            </a>
            @if($puedeCrear)
                <a href="{{ route('salud.create') }}" class="animals-top-btn animals-top-btn-primary">
                    <i class="bi bi-plus-lg me-1"></i>Nueva revision
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
            <span class="animals-summary-label">Fichas medicas</span>
            <strong class="animals-summary-value">{{ $resumen['total'] ?? 0 }}</strong>
        </div>

        <div class="animals-summary-card">
            <span class="animals-summary-label">Estado sanitario</span>
            <div class="animals-species-list">
                <span class="animals-species-pill">
                    <i class="bi bi-hourglass-split"></i>
                    Pendientes
                    <strong>{{ $resumen['pendientes'] ?? 0 }}</strong>
                </span>
                <span class="animals-species-pill">
                    <i class="bi bi-capsule-pill"></i>
                    Con tratamiento
                    <strong>{{ $resumen['con_tratamiento'] ?? 0 }}</strong>
                </span>
                <span class="animals-species-pill">
                    <i class="bi bi-calendar-heart"></i>
                    Ultima
                    <strong>{{ !empty($resumen['ultima_fecha']) ? \Carbon\Carbon::parse($resumen['ultima_fecha'])->format('d/m') : '-' }}</strong>
                </span>
            </div>
        </div>
    </section>

    <div class="table-card">
        <div class="tc-header d-flex justify-content-between align-items-start gap-3 flex-wrap">
            <div class="tabla-titulo-wrap">
                Lista de salud
                <span id="contador-salud" class="badge rounded-pill ms-1 contador-animales-badge">{{ $fichas->total() }}</span>
            </div>
            <div id="estado-filtros" class="text-muted estado-filtros">{{ request()->query() ? 'Filtros aplicados' : '' }}</div>
        </div>

        <form id="filtros-salud" method="GET" action="{{ route('salud.index') }}" class="animals-filters">
            <div class="animals-filters-header">
                <div class="animals-filters-title">Busqueda de salud</div>
            </div>

            <div class="animals-filters-grid">
                <div class="animal-filter-field animal-filter-field-wide">
                    <label for="filtro-q" class="animal-filter-label">Busqueda general</label>
                    <input type="text" id="filtro-q" name="q" class="animal-filter-control" placeholder="Ej: porcino, antibiotico, cojera..." value="{{ $filtros['q'] ?? '' }}">
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
                        <th>Animal</th>
                        <th>Especie</th>
                        <th>Estado</th>
                        <th>Diagnostico</th>
                        <th>Tratamiento</th>
                        <th>Observaciones</th>
                        <th>Fecha</th>
                        <th>Responsable</th>
                        @if($mostrarAcciones)
                            <th class="th-acciones">Acciones</th>
                        @endif
                    </tr>
                    <tr>
                        <th>
                            <input type="text" name="codigo" class="form-control form-control-sm" value="{{ $filtros['codigo'] ?? '' }}" placeholder="Filtrar codigo">
                        </th>
                        <th>
                            <select name="especie" class="form-select form-select-sm">
                                <option value="">Todas</option>
                                @foreach(['Porcino', 'Vacuno', 'Avicola'] as $especie)
                                    <option value="{{ $especie }}" {{ ($filtros['especie'] ?? '') === $especie ? 'selected' : '' }}>{{ $especie }}</option>
                                @endforeach
                            </select>
                        </th>
                        <th>
                            <select name="estado" class="form-select form-select-sm">
                                <option value="">Todos</option>
                                <option value="pendiente" {{ ($filtros['estado'] ?? '') === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                <option value="seguimiento" {{ ($filtros['estado'] ?? '') === 'seguimiento' ? 'selected' : '' }}>Seguimiento</option>
                                <option value="tratamiento" {{ ($filtros['estado'] ?? '') === 'tratamiento' ? 'selected' : '' }}>Con tratamiento</option>
                            </select>
                        </th>
                        <th>
                            <input type="text" name="diagnostico" class="form-control form-control-sm" value="{{ $filtros['diagnostico'] ?? '' }}" placeholder="Filtrar diagnostico">
                        </th>
                        <th>
                            <input type="text" name="tratamiento" class="form-control form-control-sm" value="{{ $filtros['tratamiento'] ?? '' }}" placeholder="Filtrar tratamiento">
                        </th>
                        <th>
                            <input type="text" name="observaciones" class="form-control form-control-sm" value="{{ $filtros['observaciones'] ?? '' }}" placeholder="Filtrar observaciones">
                        </th>
                        <th>
                            <input type="date" name="fecha" class="form-control form-control-sm" value="{{ $filtros['fecha'] ?? '' }}">
                        </th>
                        <th>
                            <select name="id_usuario" class="form-select form-select-sm">
                                <option value="">Todos</option>
                                @foreach($usuariosFiltro as $usuario)
                                    <option value="{{ data_get($usuario, 'id_usuario') }}" {{ (string) ($filtros['id_usuario'] ?? '') === (string) data_get($usuario, 'id_usuario') ? 'selected' : '' }}>
                                        {{ trim(data_get($usuario, 'nombre') . ' ' . data_get($usuario, 'apellidos')) }}
                                    </option>
                                @endforeach
                            </select>
                        </th>
                        @if($mostrarAcciones)
                            <th></th>
                        @endif
                    </tr>
                </thead>
                <tbody id="tabla-salud-body">
                    @forelse($fichas as $ficha)
                        @php
                            $estado = $estadoFicha($ficha);
                            $animalIcono = $iconoEspecie(data_get($ficha, 'animal.especie'));
                        @endphp
                        <tr>
                            <td>
                                <img src="{{ $animalIcono['src'] }}" class="animal-icon" alt="{{ $animalIcono['alt'] }}">
                                <strong>{{ data_get($ficha, 'animal.codigo', 'Animal #' . data_get($ficha, 'id_animal')) }}</strong>
                            </td>
                            <td>
                                <span class="badge-especie {{ $animalIcono['clase'] }}">
                                    {{ data_get($ficha, 'animal.especie', '-') }}
                                </span>
                            </td>
                            <td><span class="medical-pill {{ $estado['clase'] }}">{{ $estado['texto'] }}</span></td>
                            <td>{{ data_get($ficha, 'diagnostico') ?: 'Sin diagnostico' }}</td>
                            <td>{{ data_get($ficha, 'tratamiento') ?: 'Sin tratamiento' }}</td>
                            <td>{{ data_get($ficha, 'observaciones') ?: '-' }}</td>
                            <td>{{ data_get($ficha, 'fecha') ? \Carbon\Carbon::parse(data_get($ficha, 'fecha'))->format('d/m/Y') : '-' }}</td>
                            <td>{{ trim(data_get($ficha, 'usuario.nombre', '') . ' ' . data_get($ficha, 'usuario.apellidos', '')) ?: '-' }}</td>
                            @if($mostrarAcciones)
                                <td class="td-acciones">
                                    <div class="d-flex gap-1 justify-content-end">
                                        <a href="{{ route('animal.historial', data_get($ficha, 'id_animal')) }}" class="btn-accion btn-historial" title="Historial">
                                            <i class="bi bi-clock-history"></i>
                                        </a>
                                        @if($puedeEditarFicha)
                                            <a href="{{ route('salud.edit', data_get($ficha, 'id_ficha')) }}" class="btn-accion btn-editar" title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        @endif
                                        @if($puedeBorrarFicha)
                                            <button
                                                type="button"
                                                class="btn-accion btn-borrar salud-delete-trigger"
                                                title="Borrar"
                                                data-action="{{ route('salud.destroy', data_get($ficha, 'id_ficha')) }}"
                                                data-codigo="{{ data_get($ficha, 'animal.codigo', 'Ficha #' . data_get($ficha, 'id_ficha')) }}"
                                            >
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $mostrarAcciones ? 9 : 8 }}" class="text-center text-muted py-4">No hay registros de salud todavia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div id="salud-pagination" class="medical-pagination">
                {{ $fichas->links() }}
            </div>
        </form>
    </div>

    @if($puedeBorrarFicha)
        <div class="modal fade" id="modalEliminarSalud" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form method="POST" id="delete-salud-modal-form">
                    @csrf
                    @method('DELETE')
                    <div class="modal-content animal-delete-modal">
                        <div class="modal-header">
                            <h5 class="modal-title">Confirmar eliminacion</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <div class="animal-delete-word">ELIMINAR</div>
                            <p class="animal-delete-copy mb-2">Vas a borrar el registro de salud de <strong id="delete-salud-code">-</strong>.</p>
                            <p class="animal-delete-copy mb-3">Escribe <strong>ELIMINAR</strong> para confirmar.</p>
                            <input type="text" id="delete-salud-confirm-input" class="form-control animal-delete-input" autocomplete="off" placeholder="Escribe ELIMINAR">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="animals-top-btn animals-top-btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" id="confirm-delete-salud-btn" class="animals-top-btn animals-top-btn-primary" disabled>
                                <i class="bi bi-trash me-1"></i>Eliminar registro
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif
</main>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
(() => {
    const form = document.getElementById('filtros-salud');
    const tbody = document.getElementById('tabla-salud-body');
    const contador = document.getElementById('contador-salud');
    const estado = document.getElementById('estado-filtros');
    const limpiar = document.getElementById('limpiar-filtros');
    const pagination = document.getElementById('salud-pagination');
    const baseUrl = @json(route('api.salud.index'));
    const mostrarAcciones = @json($mostrarAcciones);
    const puedeEditarFicha = @json($puedeEditarFicha);
    const puedeBorrarFicha = @json($puedeBorrarFicha);
    const colspan = mostrarAcciones ? 9 : 8;
    const deleteModalElement = document.getElementById('modalEliminarSalud');
    const deleteModal = deleteModalElement ? new bootstrap.Modal(deleteModalElement) : null;
    const deleteModalForm = document.getElementById('delete-salud-modal-form');
    const deleteCode = document.getElementById('delete-salud-code');
    const deleteInput = document.getElementById('delete-salud-confirm-input');
    const deleteConfirmBtn = document.getElementById('confirm-delete-salud-btn');
    const iconos = {
        porcino: @json(asset('images/cerdo.png')),
        vacuno: @json(asset('images/vaca.png')),
        avicola: @json(asset('images/pollo.png')),
        otro: @json(asset('images/vaca.png')),
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

    function especieConfig(especie) {
        const key = String(especie ?? '').toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
        if (key.includes('porc')) return { clase: 'esp-porcino', icono: iconos.porcino, alt: 'Porcino' };
        if (key.includes('avi')) return { clase: 'esp-avicola', icono: iconos.avicola, alt: 'Avicola' };
        if (key.includes('vac')) return { clase: 'esp-vacuno', icono: iconos.vacuno, alt: 'Vacuno' };
        return { clase: 'esp-otro', icono: iconos.otro, alt: 'Otro' };
    }

    function formatFecha(fecha) {
        if (!fecha) return '-';
        const [year, month, day] = fecha.split('-');
        return year && month && day ? `${day}/${month}/${year}` : fecha;
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

        if (deleteCode) {
            deleteCode.textContent = triggerElement.dataset.codigo || '-';
        }

        deleteModalForm.action = triggerElement.dataset.action || '';

        if (deleteInput) {
            deleteInput.value = '';
        }

        syncDeleteConfirmState();
        deleteModal.show();
    }

    function attachDeleteEvents() {
        if (!deleteModal || !deleteConfirmBtn || !deleteInput) return;

        document.querySelectorAll('.salud-delete-trigger').forEach((buttonElement) => {
            buttonElement.addEventListener('click', () => {
                abrirModalEliminar(buttonElement);
            });
        });
    }

    function renderAcciones(ficha) {
        if (!mostrarAcciones) return '';

        return `
            <td class="td-acciones">
                <div class="d-flex gap-1 justify-content-end">
                    <a href="${ficha.historial_url}" class="btn-accion btn-historial" title="Historial">
                        <i class="bi bi-clock-history"></i>
                    </a>
                    ${puedeEditarFicha ? `
                        <a href="${ficha.edit_url}" class="btn-accion btn-editar" title="Editar">
                            <i class="bi bi-pencil"></i>
                        </a>
                    ` : ''}
                    ${puedeBorrarFicha ? `
                        <button type="button" class="btn-accion btn-borrar salud-delete-trigger" title="Borrar" data-action="${ficha.delete_url}" data-codigo="${escapeHtml(ficha.codigo_animal)}">
                            <i class="bi bi-trash"></i>
                        </button>
                    ` : ''}
                </div>
            </td>
        `;
    }

    function renderTabla(fichas) {
        contador.textContent = Array.isArray(fichas) ? fichas.length : 0;
        if (pagination) pagination.hidden = true;

        if (!Array.isArray(fichas) || fichas.length === 0) {
            tbody.innerHTML = `<tr><td colspan="${colspan}" class="text-center text-muted py-4">No hay resultados con los filtros actuales.</td></tr>`;
            return;
        }

        tbody.innerHTML = fichas.map((ficha) => {
            const config = especieConfig(ficha.especie);
            return `
                <tr>
                    <td>
                        <img src="${config.icono}" class="animal-icon" alt="${config.alt}">
                        <strong>${escapeHtml(ficha.codigo_animal)}</strong>
                    </td>
                    <td><span class="badge-especie ${config.clase}">${escapeHtml(ficha.especie || '-')}</span></td>
                    <td><span class="medical-pill ${escapeHtml(ficha.estado?.clase || 'medical-pill-muted')}">${escapeHtml(ficha.estado?.texto || 'Pendiente')}</span></td>
                    <td>${escapeHtml(ficha.diagnostico)}</td>
                    <td>${escapeHtml(ficha.tratamiento)}</td>
                    <td>${escapeHtml(ficha.observaciones)}</td>
                    <td>${formatFecha(ficha.fecha)}</td>
                    <td>${escapeHtml(ficha.responsable)}</td>
                    ${renderAcciones(ficha)}
                </tr>
            `;
        }).join('');

        attachDeleteEvents();
    }

    async function cargarSalud() {
        const params = construirQuery();
        const url = `${baseUrl}${params.toString() ? `?${params.toString()}` : ''}`;
        estado.textContent = 'Filtrando salud...';

        try {
            const response = await fetch(url, { headers: { 'Accept': 'application/json' } });
            if (!response.ok) throw new Error(`HTTP ${response.status}`);

            const json = await response.json();
            renderTabla(json.data);
            actualizarUrl(params);
            estado.textContent = params.toString() ? 'Filtros aplicados' : '';
        } catch (error) {
            contador.textContent = '0';
            tbody.innerHTML = `<tr><td colspan="${colspan}" class="text-center text-danger py-4">Error cargando salud.</td></tr>`;
            estado.textContent = 'No se pudieron aplicar los filtros';
        }
    }

    function programarCarga() {
        clearTimeout(debounceId);
        debounceId = setTimeout(cargarSalud, 250);
    }

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        cargarSalud();
    });

    form.querySelectorAll('input, select').forEach((field) => {
        const eventName = field.tagName === 'SELECT' ? 'change' : 'input';
        field.addEventListener(eventName, programarCarga);
    });

    limpiar.addEventListener('click', () => {
        window.setTimeout(() => {
            estado.textContent = '';
            cargarSalud();
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

    attachDeleteEvents();
})();
</script>
@endpush
