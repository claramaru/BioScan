@extends('layout.plantilla')

@section('title', 'Piensos')
@section('active_nav', 'piensos')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/alimentacion/index.css') }}">
@endpush

@section('content')
<main class="main-wrap">
    <div class="top-bar">
        <div>
            <div class="page-title">Piensos</div>
            <div class="feed-subtitle">Catalogo general de piensos disponibles en el sistema.</div>
        </div>
        <div class="d-flex gap-2 align-items-center flex-wrap">
            <a href="{{ route('alimentacion.index') }}" class="feed-top-btn feed-top-btn-secondary">
                <i class="bi bi-arrow-left me-1"></i>Alimentacion
            </a>
            <a href="{{ route('pienso.create') }}" class="feed-top-btn feed-top-btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Nuevo pienso
            </a>
        </div>
    </div>

    @if(session('ok'))
        <div class="alert alert-success mb-3">{{ session('ok') }}</div>
    @endif

    <section class="feed-panel">
        <div class="feed-panel-head">
            <div>
                <div class="feed-panel-title">Catalogo de piensos</div>
                <p class="feed-panel-copy">Estos son los tipos de pienso que despues pueden usarse en animales y registros de alimentacion.</p>
            </div>
        </div>

        <div class="feed-table-wrap">
            <table class="table mb-0 feed-table">
                <thead>
                    <tr>
                        <th>Pienso</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($piensos as $pienso)
                        <tr>
                            <td><span class="feed-pill">{{ $pienso->nombre }}</span></td>
                            <td>{{ $pienso->activo ? 'Activo' : 'Inactivo' }}</td>
                            <td class="text-end">
                                <a href="{{ route('pienso.edit', $pienso->id_pienso) }}" class="btn-accion btn-editar" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">No hay piensos registrados todavia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</main>
@endsection
