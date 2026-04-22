@extends('layout.plantilla')

@section('title', $permitido ? 'Acceso permitido' : 'Acceso denegado')
@section('active_nav', '')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/seccion.css') }}">
@endpush

@section('content')
<main class="main-wrap">
    <div class="top-bar">
        <div>
            <div class="page-title">{{ $permitido ? 'Acceso permitido' : 'Acceso denegado' }}</div>
        </div>
    </div>

    <section class="section-card">
        <div class="section-eyebrow">
            <i class="bi {{ $permitido ? 'bi-shield-check' : 'bi-shield-exclamation' }}"></i>
            <span>{{ $permitido ? 'Operacion validada' : 'Restriccion de permisos' }}</span>
        </div>

        @if($permitido)
            <p class="section-description">{{ $mensaje }}</p>
        @else
            <p class="section-description">No tienes privilegios para acceder a esta zona o realizar esta accion.</p>

            <div class="section-meta">
                <div class="section-meta-item">
                    <div class="section-meta-label">Permiso requerido</div>
                    <div class="section-meta-value">{{ $permiso }}</div>
                </div>

                @if(!empty($rolesPermitidos))
                    <div class="section-meta-item">
                        <div class="section-meta-label">Roles permitidos</div>
                        <div class="section-meta-value">{{ implode(', ', $rolesPermitidos) }}</div>
                    </div>
                @endif
            </div>
        @endif
    </section>
</main>
@endsection
