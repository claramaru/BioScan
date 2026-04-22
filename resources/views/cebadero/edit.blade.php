@extends('layout.plantilla')

@section('title', 'Editar cebadero')
@section('active_nav', 'cebaderos')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/cebadero/edit.css') }}">
@endpush

@section('content')
<main class="main-wrap">
    <div class="top-bar">
        <div>
            <div class="page-title">Editar cebadero</div>
            <div style="font-size:.85rem;color:var(--muted);margin-top:.15rem;">Modificando <strong>{{ $cebadero->nombre }}</strong></div>
        </div>
        <a href="{{ route('cebadero.index') }}" class="animals-top-btn animals-top-btn-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver al listado
        </a>
    </div>

    @if($errors->any())
        <div class="alert-errors">
            <i class="bi bi-exclamation-triangle-fill me-1"></i>
            <strong>Corrige los siguientes errores:</strong>
            <ul class="mb-0 mt-1 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('cebadero.update', $cebadero->id_cebadero) }}">
        @csrf
        @method('PUT')
        <div class="form-grid">
            <div>
                <div class="form-section">
                    <div class="form-section-title"><i class="bi bi-building"></i> Datos del cebadero</div>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label-custom">Nombre <span class="required-dot">*</span></label>
                            <input type="text" name="nombre" id="f-nombre" oninput="syncPreview()" class="form-control-custom {{ $errors->has('nombre') ? 'is-invalid' : '' }}" value="{{ old('nombre', $cebadero->nombre) }}">
                            @error('nombre')<div class="error-msg">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label-custom">Ubicacion</label>
                            <input type="text" name="ubicacion" id="f-ubicacion" oninput="syncPreview()" class="form-control-custom {{ $errors->has('ubicacion') ? 'is-invalid' : '' }}" value="{{ old('ubicacion', $cebadero->ubicacion) }}">
                            @error('ubicacion')<div class="error-msg">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
                <div class="form-footer">
                    <a href="{{ route('cebadero.index') }}" class="btn-cancelar">Cancelar</a>
                    <button type="submit" class="btn-guardar"><i class="bi bi-check-lg"></i> Guardar cambios</button>
                </div>
            </div>

            <div>
                <div class="preview-card">
                    <div class="preview-title"><i class="bi bi-eye" style="color:var(--muted);"></i> Vista previa</div>
                    <div class="preview-icon-wrap">
                        <i class="bi bi-building"></i>
                    </div>
                    <div class="preview-row"><span class="pk">Nombre</span><span class="pv" id="prev-nombre">{{ $cebadero->nombre ?? '-' }}</span></div>
                    <div class="preview-row"><span class="pk">Ubicacion</span><span class="pv" id="prev-ubicacion">{{ $cebadero->ubicacion ?: '-' }}</span></div>
                    <div class="preview-row"><span class="pk">Animales asociados</span><span class="pv">{{ $cebadero->animales_count ?? 0 }}</span></div>
                    <div class="preview-row"><span class="pk">Identificador</span><span class="pv">{{ $cebadero->id_cebadero }}</span></div>
                </div>
            </div>
        </div>
    </form>
</main>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function syncPreview(){
    // La vista previa se actualiza al momento segun lo que se va escribiendo.
    const nombre=document.getElementById('f-nombre').value;
    const ubicacion=document.getElementById('f-ubicacion').value;
    document.getElementById('prev-nombre').textContent=nombre||'-';
    document.getElementById('prev-ubicacion').textContent=ubicacion||'-';
}
</script>
@endpush
