@extends('layout.plantilla')

@section('title', 'Historial')
@section('active_nav', 'animals')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/animal/historial.css') }}">
@endpush

@section('content')
<main class="main-wrap">
    <div class="top-bar">
        <div class="page-title">Historial del animal</div>
        <div class="d-flex gap-2 align-items-center flex-wrap">
            @if(auth()->check() && auth()->user()->tienePrivilegio('crear_alimentacion'))
                <a href="{{ route('alimentacion.create', ['id_animal' => data_get($animal, 'id_animal'), 'return_to' => 'animal.historial', 'return_animal_id' => data_get($animal, 'id_animal')]) }}" class="animals-top-btn animals-top-btn-primary">
                    <i class="bi bi-plus-lg me-1"></i>Registrar alimentación
                </a>
            @endif
            <a href="{{ route('animal.index') }}" class="animals-top-btn animals-top-btn-secondary">
                <i class="bi bi-arrow-left me-1"></i>Volver al listado
            </a>
        </div>
    </div>

    @if(session('ok'))
        <div class="alert-ok"><i class="bi bi-check-circle-fill"></i>{{ session('ok') }}</div>
    @endif

    @php
        $especie = strtolower((string) data_get($animal, 'especie', ''));
        $ico = match($especie) {
            'porcino' => asset('images/cerdo.png'),
            'vacuno' => asset('images/vaca.png'),
            'avícola', 'avicola' => asset('images/pollo.png'),
            default => asset('images/vaca.png'),
        };
        $espClass = match($especie) {
            'porcino' => 'esp-porcino',
            'vacuno' => 'esp-vacuno',
            'avícola', 'avicola' => 'esp-avicola',
            default => 'esp-otro',
        };
    @endphp

    <div class="info-card">
        <div class="animal-header">
            <img src="{{ $ico }}" alt="{{ data_get($animal, 'especie') }}">
            <div>
                <div class="animal-codigo">{{ data_get($animal, 'codigo') }}</div>
                <span class="badge-especie {{ $espClass }}">{{ data_get($animal, 'especie') }}</span>
            </div>
        </div>
        <div class="info-grid">
            <div class="info-item">
                <label>Lote</label>
                <span>{{ data_get($animal, 'lote', '-') }}</span>
            </div>
            <div class="info-item">
                <label>Cebadero</label>
                <span>{{ data_get($animal, 'cebadero.nombre', '-') }}</span>
            </div>
            <div class="info-item">
                <label>Raza</label>
                <span>{{ data_get($animal, 'raza', '-') ?: '-' }}</span>
            </div>
            <div class="info-item">
                <label>Fecha de alta</label>
                <span>{{ data_get($animal, 'fecha_alta') ? \Carbon\Carbon::parse(data_get($animal, 'fecha_alta'))->format('d/m/Y') : '-' }}</span>
            </div>
            <div class="info-item">
                <label>ID interno</label>
                <span>#{{ data_get($animal, 'id_animal') }}</span>
            </div>
        </div>
    </div>

    <div class="stat-chips">
        <div class="stat-chip">
            <i class="bi bi-file-medical" style="color:#0369a1;"></i>
            <span class="chip-val">{{ $totalFichas }}</span>
            <span style="color:var(--muted);">Fichas médicas</span>
        </div>
        <div class="stat-chip">
            <i class="bi bi-basket" style="color:#16a34a;"></i>
            <span class="chip-val">{{ $totalAlimentacion }}</span>
            <span style="color:var(--muted);">Registros de alimentación</span>
        </div>
        <div class="stat-chip">
            <i class="bi bi-calendar3" style="color:var(--muted);"></i>
            <span class="chip-val">{{ $eventos->count() }}</span>
            <span style="color:var(--muted);">Eventos totales</span>
        </div>
    </div>

    <div class="tabs-wrap">
        <button class="tab-btn active" type="button" onclick="filtrarTab('todos', this)">Todos</button>
        <button class="tab-btn" type="button" onclick="filtrarTab('fichas', this)">Fichas médicas</button>
        <button class="tab-btn" type="button" onclick="filtrarTab('alimentacion', this)">Alimentación</button>
    </div>

    <div class="table-card tab-panel active" data-tab="todos">
        @if($eventos->isEmpty())
            <div class="empty-state">
                <i class="bi bi-inbox"></i>
                No hay registros para este animal.
            </div>
        @else
            <table class="table mb-0" id="tabla-historial-todos">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Detalle</th>
                        <th>Usuario</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($eventos as $evento)
                        <tr>
                            <td>{{ $evento['fecha'] ? \Carbon\Carbon::parse($evento['fecha'])->format('d/m/Y') : 'Sin fecha' }}</td>
                            <td>
                                <span class="badge-tipo {{ $evento['tipo'] === 'ficha_medica' ? 'tipo-ficha' : 'tipo-alim' }}">
                                    {{ $evento['titulo'] }}
                                </span>
                            </td>
                            <td>{{ $evento['detalle'] }}</td>
                            <td>{{ $evento['usuario'] ?: '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="table-card tab-panel" data-tab="fichas">
        @if($fichas->isEmpty())
            <div class="empty-state">
                <i class="bi bi-file-earmark-medical"></i>
                Aún no hay fichas médicas para este animal.
            </div>
        @else
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Diagnóstico</th>
                        <th>Tratamiento</th>
                        <th>Observaciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($fichas as $ficha)
                        <tr>
                            <td>{{ $ficha->fecha ? \Carbon\Carbon::parse($ficha->fecha)->format('d/m/Y') : '-' }}</td>
                            <td>{{ $ficha->diagnostico ?: '-' }}</td>
                            <td>{{ $ficha->tratamiento ?: '-' }}</td>
                            <td>{{ $ficha->observaciones ?: '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="table-card tab-panel" data-tab="alimentacion">
        @if($alimentaciones->isEmpty())
            <div class="empty-state">
                <i class="bi bi-basket"></i>
                Aún no hay registros de alimentación para este animal.
            </div>
        @else
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Tipo de pienso</th>
                        <th>Cantidad</th>
                        <th>Usuario</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($alimentaciones as $alimentacion)
                        <tr>
                            <td>{{ $alimentacion->fecha ? \Carbon\Carbon::parse($alimentacion->fecha)->format('d/m/Y') : '-' }}</td>
                            <td>{{ $alimentacion->tipo_pienso ?: '-' }}</td>
                            <td>{{ number_format((float) $alimentacion->cantidad, 2, ',', '.') }} kg</td>
                            <td>{{ $alimentacion->usuario_nombre ?: 'Sin asignar' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</main>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function filtrarTab(tab, btn) {
    document.querySelectorAll('.tab-btn').forEach((button) => button.classList.remove('active'));
    btn.classList.add('active');

    document.querySelectorAll('.tab-panel').forEach((panel) => {
        panel.classList.toggle('active', panel.dataset.tab === tab);
    });
}
</script>
@endpush
