@extends('layout.plantilla')

@section('title', 'Editar observaciones')
@section('active_nav', 'animals')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/animal/edit.css') }}">
@endpush

@section('content')
<main class="main-wrap">
    <div class="top-bar">
        <div>
            <div class="page-title">Editar observaciones</div>
            <div class="page-subtitle">Actualiza solo las observaciones de la ficha asociada al animal.</div>
        </div>
        <a href="{{ route('animal.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver al listado
        </a>
    </div>

    @if($errors->any())
        <div class="alert-errors">
            <i class="bi bi-exclamation-triangle-fill me-1"></i>
            <strong>Corrige los siguientes errores:</strong>
            <ul class="mb-0 mt-1 ps-3">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('animal.obs.update', data_get($ficha, 'id_animal')) }}">
        @csrf
        @method('PUT')

        <div class="form-grid">
            <div>
                <div class="form-section">
                    <div class="form-section-title"><i class="bi bi-chat-left-text"></i> Observaciones</div>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label-custom" for="observaciones">Observaciones <span class="required-dot">*</span></label>
                            <textarea
                                id="observaciones"
                                name="observaciones"
                                rows="5"
                                class="form-control-custom {{ $errors->has('observaciones') ? 'is-invalid' : '' }}"
                                placeholder="Anota aqui la informacion relevante de seguimiento..."
                            >{{ old('observaciones', data_get($ficha, 'observaciones')) }}</textarea>
                            @error('observaciones')
                                <div class="error-msg">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-footer">
                    <a href="{{ route('animal.index') }}" class="btn-cancelar">Cancelar</a>
                    <button type="submit" class="btn-guardar">
                        <i class="bi bi-check-lg"></i> Guardar observaciones
                    </button>
                </div>
            </div>

            <div>
                <div class="preview-card">
                    <div class="preview-title"><i class="bi bi-journal-text preview-icon-muted"></i> Resumen</div>
                    <div class="preview-row"><span class="pk">Animal</span><span class="pv">#{{ data_get($ficha, 'id_animal') }}</span></div>
                    <div class="preview-row"><span class="pk">Fecha</span><span class="pv">{{ data_get($ficha, 'fecha', '-') }}</span></div>
                    <div class="preview-row"><span class="pk">Modo</span><span class="pv">Edicion limitada</span></div>
                    <div class="preview-row"><span class="pk">Campo editable</span><span class="pv">Observaciones</span></div>
                </div>
            </div>
        </div>
    </form>
</main>
@endsection
