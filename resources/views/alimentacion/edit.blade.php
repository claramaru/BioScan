@extends('layout.plantilla')

@section('title', 'Editar registro de alimentacion')
@section('active_nav', 'alimentacion')

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
            <div class="page-title">Editar registro de alimentacion</div>
        </div>
        <a href="{{ route('alimentacion.index') }}" class="animals-top-btn animals-top-btn-secondary">
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

    <form method="POST" action="{{ route('alimentacion.update', data_get($registro, 'id_alimentacion')) }}">
        @csrf
        @method('PUT')
        <div class="form-grid">
            <div>
                <div class="form-section">
                    <div class="form-section-title"><i class="bi bi-basket2"></i> Datos de la alimentacion</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="id_animal" class="form-label-custom">Animal <span class="label-optional">(opcional)</span></label>
                            <select name="id_animal" id="id_animal" class="form-control-custom {{ $errors->has('id_animal') ? 'is-invalid' : '' }}">
                                <option value="">Sin asociar a un animal</option>
                                @foreach($animales as $animal)
                                    <option value="{{ data_get($animal, 'id_animal') }}" {{ (string) old('id_animal', data_get($registro, 'id_animal')) === (string) data_get($animal, 'id_animal') ? 'selected' : '' }}>
                                        {{ data_get($animal, 'codigo') }}{{ data_get($animal, 'especie') ? ' - ' . data_get($animal, 'especie') : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_animal')<div class="error-msg">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="id_pienso" class="form-label-custom">Pienso <span class="required-dot">*</span></label>
                            <select name="id_pienso" id="id_pienso" class="form-control-custom {{ $errors->has('id_pienso') ? 'is-invalid' : '' }}" required>
                                <option value="">Seleccionar tipo de pienso</option>
                                @foreach($tiposPienso as $tipo)
                                    <option value="{{ data_get($tipo, 'id_pienso') }}" {{ (string) old('id_pienso', data_get($registro, 'id_pienso')) === (string) data_get($tipo, 'id_pienso') ? 'selected' : '' }}>{{ data_get($tipo, 'nombre') }}</option>
                                @endforeach
                            </select>
                            @error('id_pienso')<div class="error-msg">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="cantidad" class="form-label-custom">Cantidad (kg) <span class="required-dot">*</span></label>
                            <input type="number" step="0.01" min="0.01" name="cantidad" id="cantidad" class="form-control-custom {{ $errors->has('cantidad') ? 'is-invalid' : '' }}" value="{{ old('cantidad', data_get($registro, 'cantidad')) }}" required>
                            @error('cantidad')<div class="error-msg">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="fecha" class="form-label-custom">Fecha <span class="required-dot">*</span></label>
                            <input type="date" name="fecha" id="fecha" class="form-control-custom {{ $errors->has('fecha') ? 'is-invalid' : '' }}" value="{{ old('fecha', data_get($registro, 'fecha')) }}" required>
                            @error('fecha')<div class="error-msg">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="form-footer">
                    <a href="{{ route('alimentacion.index') }}" class="btn-cancelar">Cancelar</a>
                    <button type="submit" class="btn-guardar">
                        <i class="bi bi-check-lg"></i> Guardar cambios
                    </button>
                </div>
            </div>

            <div>
                <div class="preview-card">
                    <div class="preview-title"><i class="bi bi-info-circle"></i> Recordatorio</div>
                    <div class="preview-copy">
                        Este formulario modifica un registro existente de alimentacion y puede dejarlo sin animal asociado.
                    </div>
                    <div class="preview-row"><span class="pk">Registro</span><span class="pv">#{{ data_get($registro, 'id_alimentacion') }}</span></div>
                    <div class="preview-row"><span class="pk">Usuario</span><span class="pv">Actual</span></div>
                    <div class="preview-row"><span class="pk">Fecha actual</span><span class="pv">{{ data_get($registro, 'fecha') ? \Carbon\Carbon::parse(data_get($registro, 'fecha'))->format('d/m/Y') : '-' }}</span></div>
                </div>
            </div>
        </div>
    </form>
</main>
@endsection
