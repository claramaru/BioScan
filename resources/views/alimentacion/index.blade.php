@extends('layout.plantilla')

@section('title', 'Alimentacion')
@section('active_nav', 'alimentacion')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/alimentacion/index.css') }}">
@endpush

@section('content')
<main class="main-wrap">
    <div class="top-bar">
        <div>
            <div class="page-title">Alimentacion</div>
            <div class="feed-subtitle">Control diario del pienso registrado en el cebadero.</div>
        </div>
        <div class="d-flex gap-2 align-items-center flex-wrap">
            <a href="{{ route('dashboard') }}" class="feed-top-btn feed-top-btn-secondary">
                <i class="bi bi-grid-1x2-fill me-1"></i>Dashboard
            </a>
            @if(!empty($puedeGestionarPienos) && $puedeGestionarPienos)
                <a href="{{ route('pienso.index') }}" class="feed-top-btn feed-top-btn-secondary">
                    <i class="bi bi-box-seam me-1"></i>Piensos
                </a>
            @endif
            @if(!empty($puedeCrearCabecera) && $puedeCrearCabecera)
                <a href="{{ route('alimentacion.create') }}" class="feed-top-btn feed-top-btn-primary">
                    <i class="bi bi-plus-lg me-1"></i>Añadir nueva alimentación
                </a>
            @endif
        </div>
    </div>

    @if(session('ok'))
        <div class="alert alert-success mb-3">
            {{ session('ok') }}
        </div>
    @endif

    <section class="feed-hero">
        <div class="feed-hero-main">
            <span class="feed-hero-label">Cantidad total registrada</span>
            <strong class="feed-hero-value">{{ number_format($resumen['cantidad_total'] ?? 0, 2, ',', '.') }} kg</strong>
            <p class="feed-hero-copy">Esta vista reune los registros de alimentacion por tipo de pienso, cantidad, fecha y usuario para seguir mejor el consumo del cebadero.</p>

            <div class="feed-chart-block">
                <div class="feed-chart-head">
                    <span class="feed-chart-title">Kilos por tipo de pienso</span>
                </div>
                <div class="feed-chart-wrap">
                    <canvas id="piensoTipoChart" height="120"></canvas>
                </div>
            </div>
        </div>

        <div class="feed-hero-side">
            <div class="feed-mini-card">
                <span>Registros</span>
                <strong>{{ $resumen['total'] ?? 0 }}</strong>
            </div>
            <div class="feed-mini-card">
                <span>Tipos de pienso</span>
                <strong>{{ $resumen['tipos_total'] ?? 0 }}</strong>
            </div>
            <div class="feed-mini-card">
                <span>Ultima fecha</span>
                <strong>{{ !empty($resumen['ultima_fecha']) ? \Carbon\Carbon::parse($resumen['ultima_fecha'])->format('d/m/Y') : '-' }}</strong>
            </div>
        </div>
    </section>

    <section class="feed-panel">
        <div class="feed-panel-head">
            <div>
                <div class="feed-panel-title">Listado de alimentacion</div>
                <p class="feed-panel-copy">Puedes filtrar por tipo de pienso, fecha o animal y ver el resumen agrupado sin repeticiones.</p>
            </div>
            <div id="estado-filtros" class="feed-state"></div>
        </div>

        <form id="filtros-alimentacion" method="GET" action="{{ route('alimentacion.index') }}" class="feed-filters">
            <div class="feed-actions">
                <button type="submit" class="feed-btn-primary">
                    <i class="bi bi-search"></i> Aplicar filtros
                </button>
                <button type="reset" id="limpiar-filtros" class="feed-btn-secondary">
                    Limpiar
                </button>
            </div>
        </form>

        <div class="feed-table-wrap">
            <div class="feed-table-head">
                <div class="feed-table-title">
                    Registros encontrados
                    <span id="contador-registros" class="feed-counter">{{ count($registros) }}</span>
                </div>
            </div>

            <table class="table mb-0 feed-table">
                <thead>
                    <tr>
                        <th>Pienso</th>
                        <th>Total animales</th>
                        <th>Total kg</th>
                    </tr>
                    <tr class="feed-filter-row">
                        <th>
                            <select id="filtro-tipo" name="tipo_pienso" form="filtros-alimentacion" class="feed-control feed-control-sm">
                                <option value="">Todos</option>
                                @foreach($tiposPienso as $tipo)
                                    <option value="{{ data_get($tipo, 'id_pienso') }}" {{ (string) ($filtros['tipo_pienso'] ?? '') === (string) data_get($tipo, 'id_pienso') ? 'selected' : '' }}>{{ data_get($tipo, 'nombre') }}</option>
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
                            <td><span class="feed-pill">{{ data_get($registro, 'tipo_pienso', '-') }}</span></td>
                            <td>
                                <div class="feed-animal-cell">
                                    <div class="feed-animal-icon">
                                        <i class="bi bi-diagram-3-fill"></i>
                                    </div>
                                    <div>
                                        <strong>{{ data_get($registro, 'total_animales', 0) }} animales</strong>
                                        <div class="feed-animal-meta">Con este tipo de pienso registrado</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ number_format((float) data_get($registro, 'total_kg', 0), 2, ',', '.') }} kg</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">No hay registros de alimentacion todavia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</main>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
(() => {
    const form = document.getElementById('filtros-alimentacion');
    const tbody = document.getElementById('tabla-alimentacion-body');
    const contador = document.getElementById('contador-registros');
    const estado = document.getElementById('estado-filtros');
    const limpiar = document.getElementById('limpiar-filtros');
    const baseUrl = @json(route('api.alimentacion.index'));
    const chartData = @json($kilosPorTipo);
    const colspan = 3;
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

    function cargarGraficoTipos() {
        const canvas = document.getElementById('piensoTipoChart');
        if (!canvas || !Array.isArray(chartData) || chartData.length === 0) return;

        new Chart(canvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: chartData.map((item) => item.tipo_pienso),
                datasets: [{
                    label: 'Kg registrados',
                    data: chartData.map((item) => item.total_kg),
                    backgroundColor: ['#2ecc71', '#86efac', '#d8f6e5'],
                    borderRadius: 10,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false,
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                        },
                        ticks: {
                            color: '#5f6f67',
                            font: {
                                size: 11,
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#e8f3ec',
                        },
                        ticks: {
                            color: '#5f6f67',
                            callback: (value) => `${value} kg`,
                            font: {
                                size: 11,
                            }
                        }
                    }
                }
            }
        });
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
                <td><span class="feed-pill">${escapeHtml(registro.tipo_pienso || '-')}</span></td>
                <td>
                    <div class="feed-animal-cell">
                        <div class="feed-animal-icon">
                            <i class="bi bi-diagram-3-fill"></i>
                        </div>
                        <div>
                            <strong>${escapeHtml(registro.total_animales ?? 0)} animales</strong>
                            <div class="feed-animal-meta">Con este tipo de pienso registrado</div>
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

    document.querySelectorAll('#filtros-alimentacion input, #filtros-alimentacion select, [form="filtros-alimentacion"]').forEach((field) => {
        const eventName = field.tagName === 'SELECT' ? 'change' : 'input';
        field.addEventListener(eventName, programarCarga);
    });

    limpiar.addEventListener('click', () => {
        window.setTimeout(() => {
            document.querySelectorAll('[form="filtros-alimentacion"]').forEach((field) => {
                if (field.tagName === 'SELECT') {
                    field.selectedIndex = 0;
                } else {
                    field.value = '';
                }
            });
            estado.textContent = '';
            cargarRegistros();
        }, 0);
    });

    cargarGraficoTipos();
})();
</script>
@endpush
