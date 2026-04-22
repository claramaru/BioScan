@extends('layout.plantilla')

@section('title', 'Acceso rapido')
@section('active_nav', 'animals')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/animal/acceso-rapido.css') }}">
@endpush

@section('content')
<main class="main-wrap">
    <div class="top-bar">
        <div>
            <div class="page-title">Acceso rapido</div>
            <div class="page-subtitle">Entrada preparada para identificar animales o zonas mediante NFC en futuras iteraciones.</div>
        </div>
        <a href="{{ route('animal.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver al listado
        </a>
    </div>

    @if($errors->any())
        <div class="alert-errors mb-3">
            <i class="bi bi-exclamation-triangle-fill me-1"></i>
            <strong>No se pudo completar el acceso:</strong>
            <ul class="mb-0 mt-2 ps-3">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="nfc-grid">
        <section class="access-card">
            <div class="section-eyebrow">Entrada manual</div>
            <h1 class="access-title">Abrir ficha desde codigo</h1>
            <p class="access-copy">
                Mientras se integra la lectura NFC real, este formulario simula el acceso rapido
                introduciendo el codigo asociado al animal o al punto de control.
            </p>

            <form method="POST" action="{{ route('animal.quick.search') }}" class="access-form">
                @csrf

                <label for="codigo" class="field-label">Codigo identificado</label>
                <div class="field-wrap">
                    <i class="bi bi-broadcast-pin"></i>
                    <input
                        type="text"
                        id="codigo"
                        name="codigo"
                        value="{{ old('codigo') }}"
                        maxlength="50"
                        required
                        autofocus
                        placeholder="Ej: ANM-004 o NFC-CEB-SUR"
                    >
                </div>

                <div class="helper-text">
                    En produccion, este valor podria llegar automaticamente desde una etiqueta NFC.
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-access">
                        <i class="bi bi-arrow-right-circle me-1"></i>Acceder a la ficha
                    </button>
                    <a href="{{ route('animal.index') }}" class="btn btn-sm btn-outline-secondary">
                        Cancelar
                    </a>
                </div>
            </form>
        </section>

        <aside class="info-card">
            <div class="info-icon">
                <i class="bi bi-nfc"></i>
            </div>
            <h2 class="info-title">Planteamiento NFC</h2>
            <p class="info-copy">
                Esta pantalla queda preparada para conectar un lector o una etiqueta que identifique
                directamente un animal concreto o una zona del cebadero.
            </p>

            <div class="info-list">
                <div class="info-item">
                    <strong>Animal</strong>
                    <span>La etiqueta redirigiria a su historial o ficha medica.</span>
                </div>
                <div class="info-item">
                    <strong>Zona</strong>
                    <span>Permitiria abrir el listado filtrado de una nave, lote o cebadero.</span>
                </div>
                <div class="info-item">
                    <strong>Estado actual</strong>
                    <span>Simulacion manual lista para demostrar el flujo funcional.</span>
                </div>
            </div>
        </aside>
    </div>
</main>
@endsection
