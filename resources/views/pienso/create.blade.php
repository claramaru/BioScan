@extends('layout.plantilla')

@section('title', 'Nuevo pienso')
@section('active_nav', 'piensos')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/alimentacion/form.css') }}">
@endpush

@section('content')
<main class="main-wrap">
    <div class="top-bar">
        <div>
            <div class="page-title">Nuevo pienso</div>
        </div>
        <a href="{{ route('pienso.index') }}" class="animals-top-btn animals-top-btn-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver al catalogo
        </a>
    </div>

    @if($errors->any())
        <div class="alert-errors">
            <i class="bi bi-exclamation-triangle-fill me-1"></i>
            <strong>Corrige los siguientes errores:</strong>
            <ul class="mb-0 mt-1 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('pienso.store') }}">
        @csrf
        <div class="form-grid">
            <div>
                <div class="form-section">
                    <div class="form-section-title"><i class="bi bi-box-seam"></i> Datos del pienso</div>
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="nombre" class="form-label-custom">Nombre <span class="required-dot">*</span></label>
                            <input type="text" name="nombre" id="nombre" class="form-control-custom {{ $errors->has('nombre') ? 'is-invalid' : '' }}" value="{{ old('nombre') }}" required>
                            @error('nombre')<div class="error-msg">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label-custom d-flex align-items-center gap-2">
                                <input type="checkbox" name="activo" value="1" {{ old('activo', '1') ? 'checked' : '' }}>
                                Activo
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-footer">
                    <a href="{{ route('pienso.index') }}" class="btn-cancelar">Cancelar</a>
                    <button type="submit" class="btn-guardar">
                        <i class="bi bi-plus-circle"></i> Guardar pienso
                    </button>
                </div>
            </div>
        </div>
    </form>
</main>
@endsection
