@extends('layout.plantilla')

@section('title', $titulo)
@section('active_nav', $activeNav)

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
            <div class="page-title">{{ $titulo }}</div>
        </div>
    </div>

    <section class="section-card">
        <div class="section-eyebrow">
            <i class="bi {{ $icono }}"></i>
            <span>{{ $estado }}</span>
        </div>

        <p class="section-description">{{ $descripcion }}</p>

        <div class="section-meta">
            <div class="section-meta-item">
                <div class="section-meta-label">Ruta</div>
                <div class="section-meta-value">{{ request()->path() }}</div>
            </div>
            <div class="section-meta-item">
                <div class="section-meta-label">Vista</div>
                <div class="section-meta-value">resources/views/seccion.blade.php</div>
            </div>
            <div class="section-meta-item">
                <div class="section-meta-label">Siguiente paso</div>
                <div class="section-meta-value">{{ $siguientePaso }}</div>
            </div>
        </div>
    </section>
</main>
@endsection
