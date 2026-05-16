@extends('layout.plantilla')

@section('title', 'Animales')
@section('active_nav', 'animals')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/animal/index.css') }}">
@endpush

@section('content')
<main class="main-wrap">
    <div class="top-bar">
        <div class="page-title">Animales</div>
        <div class="d-flex gap-2 align-items-center flex-wrap">
            <a href="{{ route('animal.quick.form') }}" class="animals-top-btn animals-top-btn-secondary">
                <i class="bi bi-phone me-1"></i>Acceso NFC
            </a>
            <a href="{{ route('animal.api.demo') }}" class="animals-top-btn animals-top-btn-secondary">
                <i class="bi bi-braces me-1"></i>API
            </a>
            @if(!empty($puedeCrear) && $puedeCrear)
                <a href="{{ route('animal.create') }}" class="animals-top-btn animals-top-btn-primary">
                    <i class="bi bi-plus-lg me-1"></i>Nuevo animal
                </a>
            @endif
        </div>
    </div>

    @if(session('ok'))
        <div class="alert-ok mb-3">
            <i class="bi bi-check-circle-fill"></i>{{ session('ok') }}
        </div>
    @endif

    @php
        $resumenEspecies = collect([
            ['nombre' => 'Porcino', 'icono' => asset('images/cerdo.png')],
            ['nombre' => 'Vacuno', 'icono' => asset('images/vaca.png')],
            ['nombre' => 'Avicola', 'icono' => asset('images/pollo.png')],
        ])->map(function ($item) use ($animales) {
            return [
                'nombre' => $item['nombre'],
                'icono' => $item['icono'],
                'total' => $animales->filter(function ($animal) use ($item) {
                    $especie = mb_strtolower((string) ($animal->especie ?? ''));

                    return match ($item['nombre']) {
                        'Porcino' => str_contains($especie, 'porc'),
                        'Vacuno' => str_contains($especie, 'vac'),
                        'Avicola' => str_contains($especie, 'avi'),
                        default => false,
                    };
                })->count(),
            ];
        });
    @endphp

    @php
        $puedeCrearAlimentacion = !empty($puedeCrearAlimentacion) && $puedeCrearAlimentacion;
        $mostrarAcciones =
            $puedeCrearAlimentacion
            || (!empty($puedeBorrar) && $puedeBorrar)
            || (!empty($puedeEditarAnimal) && $puedeEditarAnimal)
            || (!empty($puedeObs) && $puedeObs);
        $puedeVerEditar = (!empty($puedeEditarAnimal) && $puedeEditarAnimal) || (!empty($puedeObs) && $puedeObs);
        $razasPorEspecie = [
            'Avicola' => [
                'Pollo de engorde (broiler)',
                'Pavo de engorde',
                'Gallina africana (para carne)',
                'Pollo campero de engorde',
            ],
            'Porcino' => [
                'Cerdo ibérico de cebo',
                'Chato murciano',
                'Cerdo blanco de engorde',
                'Cerdo Duroc',
            ],
            'Vacuno' => [
                'Ternero de engorde',
                'Novillo',
                'Angus',
                'Ternera de carne',
            ],
        ];
        $razasFiltro = collect($razasPorEspecie[$filtros['especie'] ?? ''] ?? [])
            ->when(empty($filtros['especie'] ?? ''), function ($collection) use ($razasPorEspecie) {
                return collect($razasPorEspecie)
                    ->flatten()
                    ->values();
            });
    @endphp

    <section class="animals-summary">
        <div class="animals-summary-card">
            <span class="animals-summary-label">Total de animales</span>
            <strong class="animals-summary-value">{{ $animales->count() }}</strong>
        </div>

        <div class="animals-summary-card">
            <span class="animals-summary-label">Especies registradas</span>
            <div class="animals-species-list">
                @foreach($resumenEspecies as $especie)
                    <span class="animals-species-pill">
                        <img src="{{ $especie['icono'] }}" alt="{{ $especie['nombre'] }}" class="animals-species-pill-icon">
                        {{ $especie['nombre'] }}
                        <strong>{{ $especie['total'] }}</strong>
                    </span>
                @endforeach
            </div>
        </div>
    </section>

    @if($puedeCrearAlimentacion)
        <section class="animals-bulk-panel">
            <div>
                <div class="animals-filters-title">Alimentacion masiva</div>
                <p class="animals-filters-copy mb-0">Selecciona varios animales del listado y registra el mismo tipo de pienso de una sola vez.</p>
                <div id="bulk-alert" class="animals-inline-alert" role="alert" aria-live="polite" hidden>
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <span>Debes seleccionar al menos un animal antes de registrar la alimentacion masiva.</span>
                </div>
            </div>
            <button type="button" id="abrir-alimentacion-masiva" class="animals-top-btn animals-top-btn-primary">
                <i class="bi bi-basket2 me-1"></i>Registrar alimentacion masiva
            </button>
        </section>
    @endif

    <div class="table-card">
        <div class="tc-header d-flex justify-content-between align-items-start gap-3 flex-wrap">
            <div class="tabla-titulo-wrap">
                Lista de animales
                <span id="contador-animales" class="badge rounded-pill ms-1 contador-animales-badge">
                    {{ count($animales) }}
                </span>
            </div>
            <div id="estado-filtros" class="text-muted estado-filtros"></div>
        </div>

        <form id="filtros-animales" method="GET" action="{{ route('animal.index') }}" class="animals-filters">
            <div class="animals-filters-header">
                <div class="animals-filters-title">Busqueda de animales</div>
                <p class="animals-filters-copy">Refina el listado por codigo, especie, lote, fecha o cebadero para encontrar antes la ficha que necesitas sin perder la vista general del registro.</p>
            </div>

            <div class="animals-filters-grid">
                <div class="animal-filter-field animal-filter-field-wide">
                    <label for="filtro-q" class="animal-filter-label">Busqueda general</label>
                    <input type="text" id="filtro-q" name="q" class="animal-filter-control" placeholder="Ej: A-204, porcino, lote norte..." value="{{ $filtros['q'] ?? '' }}">
                </div>
            </div>

            <div class="animals-filters-actions">
                <button type="reset" id="limpiar-filtros" class="animals-btn-reset">
                    Limpiar
                </button>
            </div>

            <table class="table mb-0" id="tabla-animales">
                <thead>
                    <tr>
                        @if($puedeCrearAlimentacion)
                            <th class="th-select">
                                <input type="checkbox" id="seleccionar-todos">
                            </th>
                        @endif
                        <th>Codigo</th>
                        <th>Especie</th>
                        <th>Raza</th>
                        <th>Lote</th>
                        <th>Fecha alta</th>
                        <th>Pienso recomendado</th>
                        <th>Observaciones</th>
                        <th>Cebadero</th>
                        @if($mostrarAcciones)
                            <th class="th-acciones">Acciones</th>
                        @endif
                    </tr>
                    <tr>
                        @if($puedeCrearAlimentacion)
                            <th></th>
                        @endif
                        <th>
                            <input type="text" id="filtro-codigo" name="codigo" class="form-control form-control-sm" value="{{ $filtros['codigo'] ?? '' }}" placeholder="Filtrar codigo">
                        </th>
                        <th>
                            <select id="filtro-especie" name="especie" class="form-select form-select-sm">
                                <option value="">Todas</option>
                                @foreach(['Porcino', 'Vacuno', 'Avicola'] as $especie)
                                    <option value="{{ $especie }}" {{ ($filtros['especie'] ?? '') === $especie ? 'selected' : '' }}>{{ $especie }}</option>
                                @endforeach
                            </select>
                        </th>
                        <th>
                            <select id="filtro-raza" name="raza" class="form-select form-select-sm">
                                <option value="">Todas</option>
                                @foreach($razasFiltro as $raza)
                                    <option value="{{ $raza }}" {{ ($filtros['raza'] ?? '') === $raza ? 'selected' : '' }}>{{ $raza }}</option>
                                @endforeach
                            </select>
                        </th>
                        <th>
                            <input type="text" id="filtro-lote" name="lote" class="form-control form-control-sm" value="{{ $filtros['lote'] ?? '' }}" placeholder="Filtrar lote">
                        </th>
                        <th>
                            <input type="date" id="filtro-fecha" name="fecha_alta" class="form-control form-control-sm" value="{{ $filtros['fecha_alta'] ?? '' }}">
                        </th>
                        <th>
                            <select id="filtro-tipo-pienso" name="id_pienso" class="form-select form-select-sm">
                                <option value="">Todos</option>
                                @foreach(($piensos ?? []) as $pienso)
                                    <option value="{{ data_get($pienso, 'id_pienso') }}" {{ (string) ($filtros['id_pienso'] ?? '') === (string) data_get($pienso, 'id_pienso') ? 'selected' : '' }}>{{ data_get($pienso, 'nombre') }}</option>
                                @endforeach
                            </select>
                        </th>
                        <th>
                            <input type="text" id="filtro-observaciones" name="observaciones" class="form-control form-control-sm" value="{{ $filtros['observaciones'] ?? '' }}" placeholder="Filtrar observaciones">
                        </th>
                        <th>
                            <select id="filtro-cebadero" name="cebadero" class="form-select form-select-sm">
                                <option value="">Todos</option>
                                @foreach($cebaderos as $cebadero)
                                    <option value="{{ data_get($cebadero, 'nombre') }}" {{ ($filtros['cebadero'] ?? '') === data_get($cebadero, 'nombre') ? 'selected' : '' }}>
                                        {{ data_get($cebadero, 'nombre') }}
                                    </option>
                                @endforeach
                            </select>
                        </th>
                        @if($mostrarAcciones)
                            <th></th>
                        @endif
                    </tr>
                </thead>
                <tbody id="tabla-animales-body">
                @forelse($animales as $a)
                    @php
                        $espClass = match(mb_strtolower($a->especie ?? '')) {
                            'porcino' => 'esp-porcino',
                            'vacuno' => 'esp-vacuno',
                            'avicola', 'avícola' => 'esp-avicola',
                            default => 'esp-otro',
                        };
                        $ico = match(mb_strtolower($a->especie ?? '')) {
                            'porcino' => asset('images/cerdo.png'),
                            'vacuno' => asset('images/vaca.png'),
                            'avicola', 'avícola' => asset('images/pollo.png'),
                            default => asset('images/vaca.png'),
                        };
                    @endphp
                    <tr class="{{ $puedeCrearAlimentacion ? 'animal-select-row' : '' }}">
                        @if($puedeCrearAlimentacion)
                            <td class="td-select">
                                <input type="checkbox" class="animal-select" value="{{ $a->id_animal }}" data-codigo="{{ $a->codigo }}">
                            </td>
                        @endif
                        <td>
                            <img src="{{ $ico }}" class="animal-icon" alt="{{ $a->especie }}">
                            <strong>{{ $a->codigo }}</strong>
                        </td>
                        <td><span class="badge-especie {{ $espClass }}">{{ $a->especie }}</span></td>
                        <td>{{ $a->raza ?: '-' }}</td>
                        <td>{{ $a->lote }}</td>
                        <td>{{ \Carbon\Carbon::parse($a->fecha_alta)->format('d/m/Y') }}</td>
                        <td>{{ data_get($a, 'piensoRecomendado.nombre', '-') ?: '-' }}</td>
                        <td>{{ $a->observaciones ?: '-' }}</td>
                        <td>{{ data_get($a, 'cebadero.nombre', '-') }}</td>
                        @if($mostrarAcciones)
                            <td class="td-acciones">
                                <div class="d-flex gap-1 justify-content-end">
                                    <a href="{{ route('animal.historial', $a->id_animal) }}" class="btn-accion btn-historial" title="Historial">
                                        <i class="bi bi-clock-history"></i>
                                    </a>
                                    @if($puedeCrearAlimentacion)
                                        <a href="{{ route('alimentacion.create', ['id_animal' => $a->id_animal, 'return_to' => 'animal.index']) }}" class="btn-accion btn-alimentacion" title="Registrar alimentacion">
                                            <i class="bi bi-basket2"></i>
                                        </a>
                                    @endif
                                    @if($puedeVerEditar)
                                        <a href="{{ route('animal.edit', $a->id_animal) }}" class="btn-accion btn-editar" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @endif
                                    @if(!empty($puedeBorrar) && $puedeBorrar)
                                        <button type="button" class="btn-accion btn-borrar animal-delete-trigger" title="Borrar" data-action="{{ route('animal.destroy', $a->id_animal) }}" data-codigo="{{ $a->codigo }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $puedeCrearAlimentacion ? ($mostrarAcciones ? 10 : 9) : ($mostrarAcciones ? 9 : 8) }}" class="text-center text-muted py-4">
                            No hay animales registrados todavia.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </form>
    </div>

    @if($puedeCrearAlimentacion)
        <div class="modal fade" id="modalAlimentacionMasiva" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <form method="POST" action="{{ route('alimentacion.store.bulk') }}" id="form-alimentacion-masiva">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Registrar alimentacion masiva</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <p class="text-muted mb-3">Se creara un registro de alimentacion para cada animal seleccionado.</p>
                            <div class="selected-animals-box mb-3">
                                <strong>Animales seleccionados:</strong>
                                <div id="animales-seleccionados-lista" class="mt-2 text-muted">No hay animales seleccionados.</div>
                            </div>
                            <div id="animales-seleccionados-inputs"></div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="bulk-tipo-pienso" class="form-label">Tipo de pienso</label>
                                    <select name="id_pienso" id="bulk-tipo-pienso" class="form-select" required>
                                        <option value="">Seleccionar tipo de pienso</option>
                                        @foreach(($piensos ?? []) as $pienso)
                                            <option value="{{ data_get($pienso, 'id_pienso') }}">{{ data_get($pienso, 'nombre') }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="bulk-cantidad" class="form-label">Cantidad (kg)</label>
                                    <input type="number" step="0.01" min="0.01" name="cantidad" id="bulk-cantidad" class="form-control" required>
                                </div>
                                <div class="col-md-3">
                                    <label for="bulk-fecha" class="form-label">Fecha</label>
                                    <input type="date" name="fecha" id="bulk-fecha" class="form-control" value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="animals-top-btn animals-top-btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" id="submit-alimentacion-masiva" class="animals-top-btn animals-top-btn-primary" disabled>
                                <i class="bi bi-check2-circle me-1"></i>Guardar registros
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @if(!empty($puedeBorrar) && $puedeBorrar)
        <div class="modal fade" id="modalEliminarAnimal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form method="POST" id="delete-animal-modal-form">
                    @csrf
                    @method('DELETE')
                    <div class="modal-content animal-delete-modal">
                        <div class="modal-header">
                            <h5 class="modal-title">Confirmar eliminacion</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <div class="animal-delete-word">ELIMINAR</div>
                            <p class="animal-delete-copy mb-2">Vas a borrar el animal <strong id="delete-animal-code">-</strong> y sus registros asociados de alimentacion y ficha medica.</p>
                            <p class="animal-delete-copy mb-3">Escribe <strong>ELIMINAR</strong> para confirmar.</p>
                            <input type="text" id="delete-confirm-input" class="form-control animal-delete-input" autocomplete="off" placeholder="Escribe ELIMINAR">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="animals-top-btn animals-top-btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" id="confirm-delete-btn" class="animals-top-btn animals-top-btn-primary" disabled>
                                <i class="bi bi-trash me-1"></i>Eliminar animal
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
    const form = document.getElementById('filtros-animales');
    const tbody = document.getElementById('tabla-animales-body');
    const contador = document.getElementById('contador-animales');
    const estado = document.getElementById('estado-filtros');
    const limpiar = document.getElementById('limpiar-filtros');
    const baseUrl = @json(route('api.animales.index'));
    const csrf = @json(csrf_token());
    const puedeVerEditar = @json($puedeVerEditar);
    const puedeBorrar = @json(!empty($puedeBorrar) && $puedeBorrar);
    const puedeCrearAlimentacion = @json($puedeCrearAlimentacion);
    const mostrarAcciones = @json($mostrarAcciones);
    const razasPorEspecie = @json($razasPorEspecie);
    const colspan = puedeCrearAlimentacion ? (mostrarAcciones ? 10 : 9) : (mostrarAcciones ? 9 : 8);
    const urls = {
        historial: @json(url('/animal/__ID__/historial')),
        editar: @json(url('/animal/__ID__/edit')),
        borrar: @json(url('/animal/__ID__')),
        alimentacion: @json(url('/alimentacion/create?id_animal=__ID__&return_to=animal.index')),
    };
    const iconos = {
        porcino: @json(asset('images/cerdo.png')),
        vacuno: @json(asset('images/vaca.png')),
        avicola: @json(asset('images/pollo.png')),
        otro: @json(asset('images/vaca.png')),
    };

    const bulkOpenBtn = document.getElementById('abrir-alimentacion-masiva');
    const bulkForm = document.getElementById('form-alimentacion-masiva');
    const bulkAlert = document.getElementById('bulk-alert');
    const bulkInputs = document.getElementById('animales-seleccionados-inputs');
    const bulkLista = document.getElementById('animales-seleccionados-lista');
    const bulkSubmit = document.getElementById('submit-alimentacion-masiva');
    const bulkModalElement = document.getElementById('modalAlimentacionMasiva');
    const bulkModal = bulkModalElement ? new bootstrap.Modal(bulkModalElement) : null;
    const deleteModalElement = document.getElementById('modalEliminarAnimal');
    const deleteModal = deleteModalElement ? new bootstrap.Modal(deleteModalElement) : null;
    const deleteModalForm = document.getElementById('delete-animal-modal-form');
    const deleteCode = document.getElementById('delete-animal-code');
    const deleteInput = document.getElementById('delete-confirm-input');
    const deleteConfirmBtn = document.getElementById('confirm-delete-btn');
    const selectedAnimals = new Map();
    let debounceId = null;

    function syncRazaFilterOptions() {
        const especieSelect = document.getElementById('filtro-especie');
        const razaSelect = document.getElementById('filtro-raza');
        if (!especieSelect || !razaSelect) return;

        const seleccionActual = razaSelect.dataset.current || razaSelect.value || '';
        const especie = especieSelect.value || '';
        const razas = especie
            ? (razasPorEspecie[especie] || [])
            : Object.values(razasPorEspecie).flat();

        razaSelect.innerHTML = '<option value="">Todas</option>';
        razas.forEach((raza) => {
            const option = document.createElement('option');
            option.value = raza;
            option.textContent = raza;
            option.selected = raza === seleccionActual;
            razaSelect.appendChild(option);
        });

        if (!razas.includes(seleccionActual)) {
            razaSelect.value = '';
        }

        razaSelect.dataset.current = razaSelect.value;
    }

    function alertarSeleccionAnimales() {
        if (!bulkAlert) return;

        bulkAlert.hidden = false;
        bulkAlert.classList.remove('is-visible');
        window.requestAnimationFrame(() => bulkAlert.classList.add('is-visible'));
    }

    function ocultarAlertaSeleccionAnimales() {
        if (!bulkAlert) return;

        bulkAlert.classList.remove('is-visible');
        window.setTimeout(() => {
            if (!bulkAlert.classList.contains('is-visible')) {
                bulkAlert.hidden = true;
            }
        }, 180);
    }

    function escapeHtml(value) {
        return String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function formatFecha(fecha) {
        if (!fecha) return '-';

        const match = String(fecha).match(/^(\d{4})-(\d{2})-(\d{2})/);
        if (!match) return fecha;

        const [, year, month, day] = match;
        return year && month && day ? `${day}/${month}/${year}` : fecha;
    }

    function especieConfig(especie) {
        const key = String(especie ?? '').toLowerCase();
        if (key === 'porcino') return { clase: 'esp-porcino', icono: iconos.porcino };
        if (key === 'vacuno') return { clase: 'esp-vacuno', icono: iconos.vacuno };
        if (key === 'avicola' || key === 'avícola') return { clase: 'esp-avicola', icono: iconos.avicola };
        return { clase: 'esp-otro', icono: iconos.otro };
    }

    function especieConfigRobusta(especie) {
        const key = String(especie ?? '')
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '');
        if (key.includes('porc')) return { clase: 'esp-porcino', icono: iconos.porcino };
        if (key.includes('vac')) return { clase: 'esp-vacuno', icono: iconos.vacuno };
        if (key.includes('avi')) return { clase: 'esp-avicola', icono: iconos.avicola };
        return { clase: 'esp-otro', icono: iconos.otro };
    }

    function corregirIconosVisibles() {
        document.querySelectorAll('#tabla-animales-body tr').forEach((row) => {
            const badge = row.querySelector('.badge-especie');
            const icon = row.querySelector('.animal-icon');
            if (!badge || !icon) return;

            const config = especieConfigRobusta(badge.textContent);
            icon.src = config.icono;
        });
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

    function syncSelectionUI() {
        if (!puedeCrearAlimentacion) return;

        bulkInputs.innerHTML = '';
        const values = Array.from(selectedAnimals.entries());

        if (!values.length) {
            bulkLista.textContent = 'No hay animales seleccionados.';
            bulkSubmit.disabled = true;
        } else {
            bulkLista.innerHTML = values.map(([id, codigo]) => `<span class="selected-animal-pill">${escapeHtml(codigo)}</span>`).join(' ');
            bulkSubmit.disabled = false;
            ocultarAlertaSeleccionAnimales();
        }

        values.forEach(([id]) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'animales[]';
            input.value = id;
            bulkInputs.appendChild(input);
        });

        document.querySelectorAll('.animal-select').forEach((checkbox) => {
            checkbox.checked = selectedAnimals.has(String(checkbox.value));
            checkbox.closest('tr')?.classList.toggle('animal-row-selected', checkbox.checked);
        });

        const selectAll = document.getElementById('seleccionar-todos');
        if (selectAll) {
            const visibles = Array.from(document.querySelectorAll('.animal-select'));
            selectAll.checked = visibles.length > 0 && visibles.every((checkbox) => checkbox.checked);
        }
    }

    function attachSelectionEvents() {
        if (!puedeCrearAlimentacion) return;

        document.querySelectorAll('.animal-select').forEach((checkbox) => {
            checkbox.addEventListener('change', () => {
                const id = String(checkbox.value);
                const codigo = checkbox.dataset.codigo || id;

                if (checkbox.checked) {
                    selectedAnimals.set(id, codigo);
                } else {
                    selectedAnimals.delete(id);
                }

                syncSelectionUI();
            });
        });

        document.querySelectorAll('.animal-select-row').forEach((row) => {
            row.addEventListener('click', (event) => {
                if (
                    event.target.closest('a, button, input, select, textarea, label, .td-acciones') ||
                    event.target.classList.contains('animal-select')
                ) {
                    return;
                }

                const checkbox = row.querySelector('.animal-select');
                if (!checkbox) return;

                checkbox.checked = !checkbox.checked;
                checkbox.dispatchEvent(new Event('change', { bubbles: true }));
            });
        });

        const selectAll = document.getElementById('seleccionar-todos');
        if (selectAll) {
            selectAll.addEventListener('change', () => {
                document.querySelectorAll('.animal-select').forEach((checkbox) => {
                    const id = String(checkbox.value);
                    const codigo = checkbox.dataset.codigo || id;
                    checkbox.checked = selectAll.checked;

                    if (selectAll.checked) {
                        selectedAnimals.set(id, codigo);
                    } else {
                        selectedAnimals.delete(id);
                    }
                });

                syncSelectionUI();
            });
        }
    }

    function syncDeleteConfirmState() {
        if (!deleteConfirmBtn || !deleteInput) return;
        deleteConfirmBtn.disabled = deleteInput.value.trim().toUpperCase() !== 'ELIMINAR';
    }

    function abrirModalEliminar(triggerElement) {
        if (deleteCode) {
            deleteCode.textContent = triggerElement.dataset.codigo || '-';
        }
        if (deleteModalForm) {
            deleteModalForm.action = triggerElement.dataset.action || '';
        }
        if (deleteInput) {
            deleteInput.value = '';
        }
        syncDeleteConfirmState();
        deleteModal?.show();
    }

    function attachDeleteEvents() {
        if (!deleteModal || !deleteConfirmBtn || !deleteInput) return;

        document.querySelectorAll('.animal-delete-trigger').forEach((buttonElement) => {
            buttonElement.addEventListener('click', () => {
                abrirModalEliminar(buttonElement);
            });
        });
    }

    if (bulkOpenBtn) {
        bulkOpenBtn.addEventListener('click', (event) => {
            if (selectedAnimals.size > 0) {
                ocultarAlertaSeleccionAnimales();
                bulkModal?.show();
                return;
            }

            event.preventDefault();
            event.stopPropagation();
            alertarSeleccionAnimales();
        });
    }

    if (bulkForm) {
        bulkForm.addEventListener('submit', (event) => {
            if (selectedAnimals.size > 0) return;

            event.preventDefault();
            alertarSeleccionAnimales();
        });
    }

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

    function renderAcciones(animal) {
        if (!mostrarAcciones) return '';

        const historialUrl = urls.historial.replace('__ID__', animal.id_animal);
        const editarUrl = urls.editar.replace('__ID__', animal.id_animal);
        const borrarUrl = urls.borrar.replace('__ID__', animal.id_animal);
        const alimentacionUrl = urls.alimentacion.replace('__ID__', animal.id_animal);
        const codigo = escapeHtml(animal.codigo);

        let html = `
            <td class="td-acciones">
                <div class="d-flex gap-1 justify-content-end">
                    <a href="${historialUrl}" class="btn-accion btn-historial" title="Historial">
                        <i class="bi bi-clock-history"></i>
                    </a>
        `;

        if (puedeCrearAlimentacion) {
            html += `
                    <a href="${alimentacionUrl}" class="btn-accion btn-alimentacion" title="Registrar alimentacion">
                        <i class="bi bi-basket2"></i>
                    </a>
            `;
        }

        if (puedeVerEditar) {
            html += `
                    <a href="${editarUrl}" class="btn-accion btn-editar" title="Editar">
                        <i class="bi bi-pencil"></i>
                    </a>
            `;
        }

        if (puedeBorrar) {
            html += `
                    <button type="button" class="btn-accion btn-borrar animal-delete-trigger" title="Borrar" data-action="${borrarUrl}" data-codigo="${codigo}">
                        <i class="bi bi-trash"></i>
                    </button>
            `;
        }

        html += `
                </div>
            </td>
        `;

        return html;
    }

    function renderTabla(animales) {
        contador.textContent = Array.isArray(animales) ? animales.length : 0;

        if (!Array.isArray(animales) || animales.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="${colspan}" class="text-center text-muted py-4">
                        No hay resultados con los filtros actuales.
                    </td>
                </tr>
            `;
            syncSelectionUI();
            return;
        }

        tbody.innerHTML = animales.map((animal) => {
            const especie = escapeHtml(animal.especie || '-');
            const config = especieConfigRobusta(animal.especie);
            const cebadero = escapeHtml(animal.cebadero?.nombre || '-');
            const observaciones = escapeHtml(animal.observaciones || '-');
            const pienso = escapeHtml(animal.tipo_pienso_recomendado || '-');
            const raza = escapeHtml(animal.raza || '-');
            const checkbox = puedeCrearAlimentacion
                ? `<td class="td-select"><input type="checkbox" class="animal-select" value="${animal.id_animal}" data-codigo="${escapeHtml(animal.codigo)}" ${selectedAnimals.has(String(animal.id_animal)) ? 'checked' : ''}></td>`
                : '';
            const rowClass = puedeCrearAlimentacion ? 'animal-select-row' : '';

            return `
                <tr class="${rowClass}">
                    ${checkbox}
                    <td>
                        <img src="${config.icono}" class="animal-icon" alt="${especie}">
                        <strong>${escapeHtml(animal.codigo)}</strong>
                    </td>
                    <td><span class="badge-especie ${config.clase}">${especie}</span></td>
                    <td>${raza}</td>
                    <td>${escapeHtml(animal.lote || '-')}</td>
                    <td>${formatFecha(animal.fecha_alta)}</td>
                    <td>${pienso}</td>
                    <td>${observaciones}</td>
                    <td>${cebadero}</td>
                    ${renderAcciones(animal)}
                </tr>
            `;
        }).join('');

        attachSelectionEvents();
        attachDeleteEvents();
        syncSelectionUI();
        corregirIconosVisibles();
    }

    async function cargarAnimales() {
        const params = construirQuery();
        const url = `${baseUrl}${params.toString() ? `?${params.toString()}` : ''}`;
        estado.textContent = 'Filtrando animales...';

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
                        Error cargando animales.
                    </td>
                </tr>
            `;
            estado.textContent = 'No se pudieron aplicar los filtros';
        }
    }

    function programarCarga() {
        clearTimeout(debounceId);
        debounceId = setTimeout(cargarAnimales, 250);
    }

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        cargarAnimales();
    });

    form.querySelectorAll('input, select').forEach((field) => {
        const eventName = field.tagName === 'SELECT' ? 'change' : 'input';
        field.addEventListener(eventName, programarCarga);
    });

    document.getElementById('filtro-especie')?.addEventListener('change', () => {
        syncRazaFilterOptions();
    });

    document.getElementById('filtro-raza')?.addEventListener('change', () => {
        document.getElementById('filtro-raza').dataset.current = document.getElementById('filtro-raza').value;
    });

    limpiar.addEventListener('click', () => {
        window.setTimeout(() => {
            estado.textContent = '';
            const razaSelect = document.getElementById('filtro-raza');
            if (razaSelect) {
                razaSelect.dataset.current = '';
            }
            syncRazaFilterOptions();
            cargarAnimales();
        }, 0);
    });

    syncRazaFilterOptions();
    attachSelectionEvents();
    attachDeleteEvents();
    syncSelectionUI();
    corregirIconosVisibles();
})();
</script>
@endpush
