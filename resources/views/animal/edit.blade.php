@extends('layout.plantilla')

@section('title', 'Editar animal')
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
            <div class="page-title">Editar animal</div>
            <div style="font-size:.85rem;color:var(--muted);margin-top:.15rem;">Modificando <strong>{{ data_get($animal, 'codigo') }}</strong></div>
        </div>
        <a href="{{ route('animal.index') }}" class="animals-top-btn animals-top-btn-secondary">
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

    <form method="POST" action="{{ route('animal.update', data_get($animal, 'id_animal')) }}">
        @csrf
        @method('PUT')
        <div class="form-grid">
            <div>
                <div class="form-section">
                    <div class="form-section-title"><i class="bi bi-tag"></i> Datos del animal</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label-custom">Codigo <span class="required-dot">*</span></label>
                            <input type="text" name="codigo" id="f-codigo" oninput="syncPreview()" class="form-control-custom {{ $errors->has('codigo') ? 'is-invalid' : '' }}" value="{{ old('codigo', data_get($animal, 'codigo')) }}">
                            @error('codigo')<div class="error-msg">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">Especie <span class="required-dot">*</span></label>
                            <select name="especie" id="f-especie" onchange="syncPreview()" class="form-control-custom {{ $errors->has('especie') ? 'is-invalid' : '' }}">
                                @foreach(['Porcino','Vacuno','Avicola'] as $esp)
                                    <option value="{{ $esp }}" {{ old('especie', data_get($animal, 'especie')) === $esp ? 'selected' : '' }}>{{ $esp }}</option>
                                @endforeach
                            </select>
                            @error('especie')<div class="error-msg">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">Lote <span class="required-dot">*</span></label>
                            <input type="text" name="lote" id="f-lote" oninput="syncPreview()" class="form-control-custom {{ $errors->has('lote') ? 'is-invalid' : '' }}" value="{{ old('lote', data_get($animal, 'lote')) }}">
                            @error('lote')<div class="error-msg">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">Raza <span class="label-optional">(opcional)</span></label>
                            <select name="raza" id="f-raza" onchange="syncPreview()" class="form-control-custom {{ $errors->has('raza') ? 'is-invalid' : '' }}" data-current="{{ old('raza', data_get($animal, 'raza')) }}">
                                <option value="">Seleccionar raza</option>
                            </select>
                            @error('raza')<div class="error-msg">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">Pienso recomendado <span class="label-optional">(opcional)</span></label>
                            <select name="id_pienso_recomendado" class="form-control-custom {{ $errors->has('id_pienso_recomendado') ? 'is-invalid' : '' }}">
                                <option value="">Seleccionar tipo de pienso</option>
                                @foreach(($piensos ?? []) as $pienso)
                                    <option value="{{ data_get($pienso, 'id_pienso') }}" {{ (string) old('id_pienso_recomendado', data_get($animal, 'id_pienso_recomendado')) === (string) data_get($pienso, 'id_pienso') ? 'selected' : '' }}>{{ data_get($pienso, 'nombre') }}</option>
                                @endforeach
                            </select>
                            @error('id_pienso_recomendado')<div class="error-msg">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">Fecha de alta <span class="required-dot">*</span></label>
                            <input type="date" name="fecha_alta" id="f-fecha" oninput="syncPreview()" class="form-control-custom {{ $errors->has('fecha_alta') ? 'is-invalid' : '' }}" value="{{ old('fecha_alta', data_get($animal, 'fecha_alta')) }}">
                            @error('fecha_alta')<div class="error-msg">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label-custom">Cebadero <span class="required-dot">*</span></label>
                            <select name="id_cebadero" id="f-cebadero" onchange="syncPreview()" class="form-control-custom {{ $errors->has('id_cebadero') ? 'is-invalid' : '' }}">
                                @foreach($cebaderos as $c)
                                    <option value="{{ data_get($c, 'id_cebadero') }}" {{ old('id_cebadero', data_get($animal, 'id_cebadero')) == data_get($c, 'id_cebadero') ? 'selected' : '' }}>{{ data_get($c, 'nombre') }}</option>
                                @endforeach
                            </select>
                            @error('id_cebadero')<div class="error-msg">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label-custom">Observaciones <span class="label-optional">(opcional)</span></label>
                            <textarea name="observaciones" class="form-control-custom {{ $errors->has('observaciones') ? 'is-invalid' : '' }}" rows="4">{{ old('observaciones', data_get($animal, 'observaciones')) }}</textarea>
                            @error('observaciones')<div class="error-msg">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
                <div class="form-footer">
                    <a href="{{ route('animal.index') }}" class="btn-cancelar">Cancelar</a>
                    <button type="submit" class="btn-guardar"><i class="bi bi-check-lg"></i> Guardar cambios</button>
                </div>
            </div>

            <div>
                @php
                    $icoInit = match(strtolower((string) data_get($animal, 'especie', ''))) {
                        'porcino' => asset('images/cerdo.png'),
                        'vacuno' => asset('images/vaca.png'),
                        'avicola' => asset('images/pollo.png'),
                        default => asset('images/vaca.png'),
                    };
                    $bgInit = match(strtolower((string) data_get($animal, 'especie', ''))) {
                        'porcino' => '#fce7f3',
                        'vacuno' => '#e0f2fe',
                        'avicola' => '#fef9c3',
                        default => '#f3f4f6',
                    };
                @endphp
                <div class="preview-card">
                    <div class="preview-title"><i class="bi bi-eye" style="color:var(--muted);"></i> Vista previa</div>
                    <div class="preview-icon-wrap" id="prev-icon-wrap" style="background:{{ $bgInit }};">
                        <img src="{{ $icoInit }}" alt="especie" id="prev-icon">
                    </div>
                    <div class="preview-row"><span class="pk">Lote</span><span class="pv" id="prev-lote">{{ data_get($animal, 'lote', '-') }}</span></div>
                    <div class="preview-row"><span class="pk">Cebadero</span><span class="pv" id="prev-cebadero">{{ data_get($animal, 'cebadero.nombre', '-') }}</span></div>
                    <div class="preview-row"><span class="pk">Especie</span><span class="pv" id="prev-especie">{{ data_get($animal, 'especie', '-') }}</span></div>
                    <div class="preview-row"><span class="pk">Raza</span><span class="pv" id="prev-raza">{{ data_get($animal, 'raza', '-') ?: '-' }}</span></div>
                    <div class="preview-row"><span class="pk">Fecha alta</span><span class="pv" id="prev-fecha">{{ data_get($animal, 'fecha_alta') ? \Carbon\Carbon::parse(data_get($animal, 'fecha_alta'))->format('d/m/Y') : '-' }}</span></div>
                </div>
            </div>
        </div>
    </form>
</main>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const ICONOS={'Porcino':'{{ asset("images/cerdo.png") }}','Vacuno':'{{ asset("images/vaca.png") }}','Avicola':'{{ asset("images/pollo.png") }}'};
const BG={'Porcino':'#fce7f3','Vacuno':'#e0f2fe','Avicola':'#fef9c3'};
const RAZAS_POR_ESPECIE={
    'Avicola':['Pollo de engorde (broiler)','Pavo de engorde','Gallina africana (para carne)','Pollo campero de engorde'],
    'Porcino':['Cerdo ibérico de cebo','Chato murciano','Cerdo blanco de engorde','Cerdo Duroc'],
    'Vacuno':['Ternero de engorde','Novillo','Angus','Ternera de carne'],
};
function syncRazaOptions(){
    const esp=document.getElementById('f-especie').value;
    const razaSelect=document.getElementById('f-raza');
    const actual=razaSelect.dataset.current||'';
    const razas=RAZAS_POR_ESPECIE[esp]||[];
    razaSelect.innerHTML='<option value="">Seleccionar raza</option>';
    razas.forEach((raza)=>{
        const option=document.createElement('option');
        option.value=raza;
        option.textContent=raza;
        option.selected=raza===actual;
        razaSelect.appendChild(option);
    });
    if(!razas.includes(actual)){razaSelect.value='';}
    razaSelect.dataset.current=razaSelect.value;
}
function syncPreview(){
    const esp=document.getElementById('f-especie').value;
    syncRazaOptions();
    const raza=document.getElementById('f-raza').value;
    const lote=document.getElementById('f-lote').value;
    const fecha=document.getElementById('f-fecha').value;
    const ceb=document.getElementById('f-cebadero');
    document.getElementById('prev-lote').textContent=lote||'-';
    document.getElementById('prev-cebadero').textContent=ceb.options[ceb.selectedIndex]?.text||'-';
    document.getElementById('prev-especie').textContent=esp||'-';
    document.getElementById('prev-raza').textContent=raza||'-';
    document.getElementById('prev-fecha').textContent=fecha?new Date(fecha).toLocaleDateString('es-ES'):'-';
    document.getElementById('f-raza').dataset.current=raza;
    if(ICONOS[esp]){document.getElementById('prev-icon').src=ICONOS[esp];document.getElementById('prev-icon-wrap').style.background=BG[esp]||'#f3f4f6';}
}
syncPreview();
</script>
@endpush
