@extends('layout.plantilla')

@section('title', 'Cebaderos')
@section('active_nav', 'cebaderos')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/animal/index.css') }}">
<link rel="stylesheet" href="{{ asset('css/cebadero/index.css') }}">
@endpush

@section('content')
@php
    // Este resumen se usa para rellenar el filtro de especies.
    $especiesResumen = collect($resumen['especies'] ?? []);

    // Asi intento unificar nombres parecidos y no repetir especies casi iguales.
    $normalizarEspecie = function (?string $especie) {
        $valor = mb_strtolower(trim((string) $especie));

        return match (true) {
            str_contains($valor, 'porc') => 'Porcino',
            str_contains($valor, 'vac') => 'Vacuno',
            str_contains($valor, 'avi') => 'Avicola',
            default => trim((string) $especie) !== '' ? trim((string) $especie) : 'Sin datos',
        };
    };

    // Aqui preparo la tabla en un formato simple porque los datos pueden llegar como modelo o como array.
    $cebaderosTabla = collect($cebaderos)->map(function ($cebadero) use ($normalizarEspecie) {
        $especies = collect(data_get($cebadero, 'animales', []))
            ->pluck('especie')
            ->filter()
            ->map($normalizarEspecie)
            ->unique()
            ->values();

        $animalesCount = (int) data_get($cebadero, 'animales_count', 0);

        return [
            'id_cebadero' => data_get($cebadero, 'id_cebadero'),
            'nombre' => data_get($cebadero, 'nombre'),
            'ubicacion' => data_get($cebadero, 'ubicacion'),
            'animales_count' => $animalesCount,
            'estado' => $animalesCount > 0 ? 'Con animales' : 'Sin animales',
            'estado_clase' => $animalesCount > 0 ? 'estado-operativo' : 'estado-vacio',
            'especies' => $especies,
        ];
    });

    // Estas variables me dicen si hace falta pintar la columna de acciones.
    $mostrarAcciones =
        (!empty($puedeBorrar) && $puedeBorrar)
        || (!empty($puedeEditarCebadero) && $puedeEditarCebadero);
    $puedeVerEditar = !empty($puedeEditarCebadero) && $puedeEditarCebadero;
@endphp

<main class="main-wrap">
    @if ($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="top-bar">
        <div class="page-title">Cebaderos</div>
        <div class="d-flex gap-2 align-items-center flex-wrap">
            <a href="{{ route('dashboard') }}" class="animals-top-btn animals-top-btn-secondary">
                <i class="bi bi-grid-1x2-fill me-1"></i>Dashboard
            </a>
            @if(!empty($puedeCrearCebadero) && $puedeCrearCebadero)
                <a href="{{ route('cebadero.create') }}" class="animals-top-btn animals-top-btn-primary">
                    <i class="bi bi-plus-lg me-1"></i>Nuevo cebadero
                </a>
            @endif
        </div>
    </div>

    <section class="cebaderos-summary">
        <div class="cebaderos-summary-card">
            <span class="cebaderos-summary-label">Total de cebaderos</span>
            <strong class="cebaderos-summary-value">{{ $resumen['total'] ?? 0 }}</strong>
        </div>

        <div class="cebaderos-summary-card">
            <span class="cebaderos-summary-label">Resumen operativo</span>
            <div class="cebaderos-summary-pills">
                <span class="cebaderos-summary-pill">
                    <i class="bi bi-collection"></i>
                    Animales
                    <strong>{{ number_format($resumen['total_animales'] ?? 0, 0, ',', '.') }}</strong>
                </span>
                <span class="cebaderos-summary-pill">
                    <i class="bi bi-building-check"></i>
                    Con animales
                    <strong>{{ $resumen['con_animales'] ?? 0 }}</strong>
                </span>
                <span class="cebaderos-summary-pill">
                    <i class="bi bi-diagram-3"></i>
                    Especies
                    <strong>{{ $especiesResumen->count() }}</strong>
                </span>
            </div>
        </div>
    </section>

    <div class="table-card">
        <div class="tc-header d-flex justify-content-between align-items-start gap-3 flex-wrap">
            <div class="tabla-titulo-wrap">
                Gestion de cebaderos
                <span id="contador-cebaderos" class="badge rounded-pill ms-1 contador-cebaderos-badge">
                    {{ count($cebaderosTabla) }}
                </span>
            </div>
            <div id="estado-filtros" class="text-muted estado-filtros"></div>
        </div>

        <form id="filtros-cebaderos" method="GET" action="{{ route('cebadero.index') }}" class="animals-filters">
            <div class="animals-filters-header">
                <div class="animals-filters-title">Busqueda de cebaderos</div>
            </div>

            <div class="animals-filters-grid">
                <div class="animal-filter-field animal-filter-field-wide">
                    <label for="filtro-q" class="animal-filter-label">Busqueda general</label>
                    <input type="text" id="filtro-q" name="q" class="animal-filter-control" placeholder="Ej: cebadero, ubicacion o especie..." value="{{ $filtros['q'] ?? '' }}">
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
                    <th>Cebadero</th>
                    <th>Ubicacion</th>
                    <th>Animales</th>
                    <th>Especies</th>
                    <th>Estado</th>
                    @if($mostrarAcciones)
                        <th class="th-acciones">Acciones</th>
                    @endif
                </tr>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>
                        <select id="filtro-especie" name="especie" class="form-select form-select-sm">
                            <option value="">Todas</option>
                            @foreach($especiesResumen as $especie)
                                <option value="{{ $especie }}" {{ ($filtros['especie'] ?? '') === $especie ? 'selected' : '' }}>{{ $especie }}</option>
                            @endforeach
                        </select>
                    </th>
                    <th>
                        <select id="filtro-estado" name="estado" class="form-select form-select-sm">
                            <option value="">Todos</option>
                            <option value="Con animales" {{ ($filtros['estado'] ?? '') === 'Con animales' ? 'selected' : '' }}>Con animales</option>
                            <option value="Sin animales" {{ ($filtros['estado'] ?? '') === 'Sin animales' ? 'selected' : '' }}>Sin animales</option>
                        </select>
                    </th>
                    @if($mostrarAcciones)
                        <th></th>
                    @endif
                </tr>
            </thead>
            <tbody id="tabla-cebaderos-body">
                @forelse($cebaderosTabla as $cebadero)
                    <tr>
                        <td>
                            <div class="cebadero-main-cell">
                                <div class="cebadero-avatar">
                                    <i class="bi bi-building"></i>
                                </div>
                                <div>
                                    <strong>{{ $cebadero['nombre'] }}</strong>
                                </div>
                            </div>
                        </td>
                        <td>{{ $cebadero['ubicacion'] ?: '-' }}</td>
                        <td>{{ $cebadero['animales_count'] }}</td>
                        <td>
                            @if(count($cebadero['especies']))
                                {{ implode(' · ', $cebadero['especies']->all()) }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            <span class="badge-estado {{ $cebadero['estado_clase'] }}">
                                {{ $cebadero['estado'] }}
                            </span>
                        </td>
                        @if($mostrarAcciones)
                            <td class="td-acciones">
                                <div class="d-flex gap-1 justify-content-end">
                                    @if($puedeVerEditar)
                                        <a href="{{ url('/cebaderos/' . $cebadero['id_cebadero'] . '/edit') }}" class="btn-accion btn-editar" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @endif
                                    @if(!empty($puedeBorrar) && $puedeBorrar)
                                        <button
                                            type="button"
                                            class="btn-accion btn-borrar js-borrar-cebadero"
                                            title="Eliminar"
                                            data-delete-url="{{ route('cebadero.destroy', $cebadero['id_cebadero']) }}"
                                            data-nombre="{{ $cebadero['nombre'] }}"
                                            @disabled($cebadero['animales_count'] > 0)
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
                        <td colspan="{{ $mostrarAcciones ? 6 : 5 }}" class="text-center text-muted py-4">
                            No hay cebaderos registrados todavia.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </form>
    </div>

    @if(!empty($puedeBorrar) && $puedeBorrar)
        <div class="modal fade" id="modalEliminarCebadero" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form method="POST" id="delete-cebadero-modal-form">
                    @csrf
                    @method('DELETE')
                    <div class="modal-content animal-delete-modal">
                        <div class="modal-header">
                            <h5 class="modal-title">Confirmar eliminacion</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <div class="animal-delete-word">ELIMINAR</div>
                            <p class="animal-delete-copy mb-2">Vas a borrar el cebadero <strong id="delete-cebadero-name">-</strong>.</p>
                            <p class="animal-delete-copy mb-3">Escribe <strong>ELIMINAR</strong> para confirmar.</p>
                            <input type="text" id="delete-cebadero-confirm-input" class="form-control animal-delete-input" autocomplete="off" placeholder="Escribe ELIMINAR">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="animals-top-btn animals-top-btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" id="confirm-delete-cebadero-btn" class="animals-top-btn animals-top-btn-primary" disabled>
                                <i class="bi bi-trash me-1"></i>Eliminar cebadero
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
    // Lo dejo cerrado en esta funcion para no ensuciar el scope global.
    const form = document.getElementById('filtros-cebaderos');
    const tbody = document.getElementById('tabla-cebaderos-body');
    const contador = document.getElementById('contador-cebaderos');
    const estado = document.getElementById('estado-filtros');
    const limpiar = document.getElementById('limpiar-filtros');
    const baseUrl = @json(route('api.cebaderos.index'));
    const puedeVerEditar = @json($puedeVerEditar);
    const puedeBorrar = @json(!empty($puedeBorrar) && $puedeBorrar);
    const mostrarAcciones = @json($mostrarAcciones);
    const colspan = mostrarAcciones ? 6 : 5;
    const urls = {
        editar: @json(url('/cebaderos/__ID__/edit')),
        borrar: @json(url('/cebaderos/__ID__')),
    };
    const deleteModalElement = document.getElementById('modalEliminarCebadero');
    const deleteModal = deleteModalElement ? new bootstrap.Modal(deleteModalElement) : null;
    const deleteModalForm = document.getElementById('delete-cebadero-modal-form');
    const deleteName = document.getElementById('delete-cebadero-name');
    const deleteInput = document.getElementById('delete-cebadero-confirm-input');
    const deleteConfirmBtn = document.getElementById('confirm-delete-cebadero-btn');
    // Se reutiliza para no lanzar una peticion por cada tecla.
    let debounceId = null;

    function escapeHtml(value) {
        // Escapo el texto antes de meterlo dentro del HTML generado con JS.
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

        // Solo guardo los filtros que realmente tengan valor.
        for (const [key, value] of data.entries()) {
            const limpio = String(value).trim();
            if (limpio !== '') {
                params.set(key, limpio);
            }
        }

        return params;
    }

    function actualizarUrl(params) {
        // Asi los filtros se mantienen aunque luego se refresque la pagina.
        const next = `${form.action}${params.toString() ? `?${params.toString()}` : ''}`;
        window.history.replaceState({}, '', next);
    }

    function renderTabla(cebaderos) {
        contador.textContent = Array.isArray(cebaderos) ? cebaderos.length : 0;

        if (!Array.isArray(cebaderos) || cebaderos.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="${colspan}" class="text-center text-muted py-4">
                        No hay resultados con los filtros actuales.
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = cebaderos.map((cebadero) => {
            // La API ya devuelve el resumen de cada cebadero y aqui solo lo represento.
            const especies = Array.isArray(cebadero.especies) && cebadero.especies.length
                ? cebadero.especies.map((especie) => escapeHtml(especie)).join(' · ')
                : '-';
            const ubicacion = escapeHtml(cebadero.ubicacion || '-');
            const nombre = escapeHtml(cebadero.nombre);
            const estadoClase = escapeHtml(cebadero.estado_clase || 'estado-vacio');
            const estadoTexto = escapeHtml(cebadero.estado || 'Sin datos');
            const editarUrl = urls.editar.replace('__ID__', cebadero.id_cebadero);
            const borrarUrl = escapeHtml(cebadero.delete_url || urls.borrar.replace('__ID__', cebadero.id_cebadero));
            const puedeEliminarFila = Number(cebadero.animales_count ?? 0) === 0;
            const accionesHtml = mostrarAcciones
                ? `
                    <td class="td-acciones">
                        <div class="d-flex gap-1 justify-content-end">
                            ${puedeVerEditar ? `
                                <a href="${editarUrl}" class="btn-accion btn-editar" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            ` : ''}
                            ${puedeBorrar ? `
                                <button
                                    type="button"
                                    class="btn-accion btn-borrar js-borrar-cebadero"
                                    title="Eliminar"
                                    data-delete-url="${borrarUrl}"
                                    data-nombre="${nombre}"
                                    ${puedeEliminarFila ? '' : 'disabled'}
                                >
                                    <i class="bi bi-trash"></i>
                                </button>
                            ` : ''}
                        </div>
                    </td>
                `
                : '';

            return `
                <tr>
                    <td>
                        <div class="cebadero-main-cell">
                            <div class="cebadero-avatar">
                                <i class="bi bi-building"></i>
                            </div>
                            <div>
                                <strong>${nombre}</strong>
                            </div>
                        </div>
                    </td>
                    <td>${ubicacion}</td>
                    <td>${cebadero.animales_count ?? 0}</td>
                    <td>${especies}</td>
                    <td>
                        <span class="badge-estado ${estadoClase}">${estadoTexto}</span>
                    </td>
                    ${accionesHtml}
                </tr>
            `;
        }).join('');
    }

    function syncDeleteConfirmState() {
        if (!deleteConfirmBtn || !deleteInput) return;
        deleteConfirmBtn.disabled = deleteInput.value.trim().toUpperCase() !== 'ELIMINAR';
    }

    function abrirModalEliminar(button) {
        const deleteUrl = button.dataset.deleteUrl;

        if (!deleteUrl || !deleteModal || !deleteModalForm) {
            return;
        }

        if (deleteName) {
            deleteName.textContent = button.dataset.nombre || '-';
        }

        deleteModalForm.action = deleteUrl;

        if (deleteInput) {
            deleteInput.value = '';
        }

        syncDeleteConfirmState();
        deleteModal.show();
    }

    async function cargarCebaderos() {
        const params = construirQuery();
        const url = `${baseUrl}${params.toString() ? `?${params.toString()}` : ''}`;
        // Mientras se filtra, muestro un texto arriba.
        estado.textContent = 'Filtrando cebaderos...';

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
            // Si la peticion falla, dejo el mensaje dentro de la tabla.
            contador.textContent = '0';
            tbody.innerHTML = `
                <tr>
                    <td colspan="${colspan}" class="text-center text-danger py-4">
                        Error cargando cebaderos.
                    </td>
                </tr>
            `;
            estado.textContent = 'No se pudieron aplicar los filtros';
        }
    }

    function programarCarga() {
        clearTimeout(debounceId);
        // Este pequeño retraso evita hacer demasiadas peticiones seguidas.
        debounceId = setTimeout(cargarCebaderos, 250);
    }

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        cargarCebaderos();
    });

    document.addEventListener('click', (event) => {
        const button = event.target.closest('.js-borrar-cebadero');
        if (!button || button.disabled) {
            return;
        }

        abrirModalEliminar(button);
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

    form.querySelectorAll('input, select').forEach((field) => {
        const eventName = field.tagName === 'SELECT' ? 'change' : 'input';
        field.addEventListener(eventName, programarCarga);
    });

    limpiar.addEventListener('click', () => {
        window.setTimeout(() => {
            estado.textContent = '';
            cargarCebaderos();
        }, 0);
    });
})();
</script>
@endpush
