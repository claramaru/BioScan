@extends('layout.plantilla')

@section('title', 'Editar registro de salud')
@section('active_nav', 'salud')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/ficha_medica/form.css') }}">
@endpush

@section('content')
<main class="main-wrap">
    <div class="top-bar">
        <div>
            <div class="page-title">Editar registro de salud</div>
        </div>
        <a href="{{ route('salud.index') }}" class="animals-top-btn animals-top-btn-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver al listado
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

    <form method="POST" action="{{ route('salud.update', data_get($ficha, 'id_ficha')) }}">
        @csrf
        @method('PUT')
        <div class="form-grid">
            <div>
                <div class="form-section">
                    <div class="form-section-title"><i class="bi bi-heart-pulse-fill"></i> Datos de la ficha</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="id_animal" class="form-label-custom">Animal <span class="required-dot">*</span></label>
                            <select name="id_animal" id="id_animal" class="form-control-custom {{ $errors->has('id_animal') ? 'is-invalid' : '' }}" {{ $puedeGestionarTodo ? 'required' : 'disabled' }}>
                                @foreach($animales as $animal)
                                    <option value="{{ data_get($animal, 'id_animal') }}" {{ (string) old('id_animal', data_get($ficha, 'id_animal')) === (string) data_get($animal, 'id_animal') ? 'selected' : '' }}>
                                        {{ data_get($animal, 'codigo') }}{{ data_get($animal, 'especie') ? ' - ' . data_get($animal, 'especie') : '' }}{{ data_get($animal, 'lote') ? ' / Lote ' . data_get($animal, 'lote') : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_animal')<div class="error-msg">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="fecha" class="form-label-custom">Fecha <span class="required-dot">*</span></label>
                            <input type="date" name="fecha" id="fecha" class="form-control-custom {{ $errors->has('fecha') ? 'is-invalid' : '' }}" value="{{ old('fecha', optional(data_get($ficha, 'fecha'))->format('Y-m-d')) }}" {{ $puedeGestionarTodo ? 'required' : 'disabled' }}>
                            @error('fecha')<div class="error-msg">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="diagnostico" class="form-label-custom">Diagnostico</label>
                            <textarea name="diagnostico" id="diagnostico" rows="4" class="form-control-custom {{ $errors->has('diagnostico') ? 'is-invalid' : '' }}" {{ $puedeGestionarTodo ? '' : 'disabled' }}>{{ old('diagnostico', data_get($ficha, 'diagnostico')) }}</textarea>
                            @error('diagnostico')<div class="error-msg">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="tratamiento" class="form-label-custom">Tratamiento</label>
                            <textarea name="tratamiento" id="tratamiento" rows="4" class="form-control-custom {{ $errors->has('tratamiento') ? 'is-invalid' : '' }}" {{ $puedeGestionarTodo ? '' : 'disabled' }}>{{ old('tratamiento', data_get($ficha, 'tratamiento')) }}</textarea>
                            @error('tratamiento')<div class="error-msg">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label for="observaciones" class="form-label-custom">Observaciones</label>
                            <textarea name="observaciones" id="observaciones" rows="4" class="form-control-custom {{ $errors->has('observaciones') ? 'is-invalid' : '' }}">{{ old('observaciones', data_get($ficha, 'observaciones')) }}</textarea>
                            @error('observaciones')<div class="error-msg">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="form-footer">
                    <a href="{{ route('salud.index') }}" class="btn-cancelar">Cancelar</a>
                    <button type="submit" class="btn-guardar">
                        <i class="bi bi-check-lg"></i> Guardar cambios
                    </button>
                </div>
            </div>

            <div>
                <div class="preview-card">
                    <div class="preview-title"><i class="bi bi-info-circle"></i> Registro</div>
                    <div class="preview-copy">
                        @if($puedeGestionarTodo)
                            Puedes modificar la ficha completa.
                        @else
                            Solo puedes actualizar las observaciones.
                        @endif
                    </div>
                    <div class="preview-row"><span class="pk">Ficha</span><span class="pv">#{{ data_get($ficha, 'id_ficha') }}</span></div>
                    <div class="preview-row"><span class="pk">Animal</span><span class="pv">{{ data_get($ficha, 'animal.codigo', '-') }}</span></div>
                    <div class="preview-row"><span class="pk">Responsable</span><span class="pv">{{ trim(data_get($ficha, 'usuario.nombre', '') . ' ' . data_get($ficha, 'usuario.apellidos', '')) ?: '-' }}</span></div>
                </div>
            </div>
        </div>
    </form>
</main>
@endsection
