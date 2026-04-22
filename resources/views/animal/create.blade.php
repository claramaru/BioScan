@extends('layout.plantilla')

@section('title', 'Nuevo animal')
@section('active_nav', 'animals')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/animal/create.css') }}">
@endpush

@section('content')
<main class="main-wrap">
    <div class="top-bar">
        <div>
            <div class="page-title">Registrar nuevo animal</div>
            <div class="page-subtitle-create">Completa los campos para dar de alta un animal</div>
        </div>
        <a href="{{ route('animal.index') }}" class="animals-top-btn animals-top-btn-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver al listado
        </a>
    </div>

    @if($errors->any())
        <div class="alert-errors mb-3">
            <i class="bi bi-exclamation-triangle-fill me-1"></i>
            <strong>Corrige los siguientes errores:</strong>
            <ul class="mb-0 mt-1 ps-3">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('animal.store') }}" id="form-animal">
        @csrf
        <div class="form-grid">
            <div>
                <div class="form-section">
                    <div class="form-section-title"><i class="bi bi-tag"></i> Datos del animal</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label-custom">Codigo del animal <span class="required-dot">*</span></label>
                            <input type="text" name="codigo" id="f-codigo" class="form-control-custom {{ $errors->has('codigo') ? 'is-invalid' : '' }}" placeholder="Ej: ANM-2024-001" value="{{ old('codigo') }}" oninput="actualizarPreview()">
                            @error('codigo')<div class="error-msg">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">Especie animal <span class="required-dot">*</span></label>
                            <select name="especie" id="f-especie" class="form-control-custom {{ $errors->has('especie') ? 'is-invalid' : '' }}" onchange="actualizarPreview()">
                                <option value="" disabled {{ old('especie') ? '' : 'selected' }}>Seleccionar especie</option>
                                <option value="Porcino" {{ old('especie') === 'Porcino' ? 'selected' : '' }}>Porcino</option>
                                <option value="Vacuno" {{ old('especie') === 'Vacuno' ? 'selected' : '' }}>Vacuno</option>
                                <option value="Avicola" {{ old('especie') === 'Avicola' ? 'selected' : '' }}>Avicola</option>
                            </select>
                            @error('especie')<div class="error-msg">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">Fecha de alta <span class="required-dot">*</span></label>
                            <input type="date" name="fecha_alta" id="f-fecha" class="form-control-custom {{ $errors->has('fecha_alta') ? 'is-invalid' : '' }}" value="{{ old('fecha_alta', date('Y-m-d')) }}" oninput="actualizarPreview()">
                            @error('fecha_alta')<div class="error-msg">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">Raza <span class="label-optional">(opcional)</span></label>
                            <select name="raza" id="f-raza" class="form-control-custom {{ $errors->has('raza') ? 'is-invalid' : '' }}" onchange="actualizarPreview()">
                                <option value="">Seleccionar raza</option>
                            </select>
                            @error('raza')<div class="error-msg">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">Pienso recomendado <span class="label-optional">(opcional)</span></label>
                            <select name="id_pienso_recomendado" class="form-control-custom {{ $errors->has('id_pienso_recomendado') ? 'is-invalid' : '' }}">
                                <option value="">Seleccionar tipo de pienso</option>
                                @foreach(($piensos ?? []) as $pienso)
                                    <option value="{{ data_get($pienso, 'id_pienso') }}" {{ (string) old('id_pienso_recomendado') === (string) data_get($pienso, 'id_pienso') ? 'selected' : '' }}>{{ data_get($pienso, 'nombre') }}</option>
                                @endforeach
                            </select>
                            @error('id_pienso_recomendado')<div class="error-msg">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">Lote <span class="required-dot">*</span></label>
                            <select name="lote" id="f-lote" class="form-control-custom {{ $errors->has('lote') ? 'is-invalid' : '' }}" onchange="actualizarPreview()">
                                <option value="" disabled {{ old('lote') ? '' : 'selected' }}>Seleccionar lote</option>
                                @foreach(['L001','L002','L003','L004','L005'] as $l)
                                    <option value="{{ $l }}" {{ old('lote') === $l ? 'selected' : '' }}>{{ $l }}</option>
                                @endforeach
                            </select>
                            @error('lote')<div class="error-msg">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">Cebadero <span class="required-dot">*</span></label>
                            <select name="id_cebadero" id="f-cebadero" class="form-control-custom {{ $errors->has('id_cebadero') ? 'is-invalid' : '' }}" onchange="actualizarPreview()">
                                <option value="" disabled {{ old('id_cebadero') ? '' : 'selected' }}>Seleccionar cebadero</option>
                                @foreach($cebaderos as $c)
                                    <option value="{{ data_get($c, 'id_cebadero') }}" {{ old('id_cebadero') == data_get($c, 'id_cebadero') ? 'selected' : '' }}">
                                        {{ data_get($c, 'nombre') }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_cebadero')<div class="error-msg">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-title"><i class="bi bi-chat-left-text"></i> Observaciones</div>
                    <textarea name="observaciones" class="form-control-custom {{ $errors->has('observaciones') ? 'is-invalid' : '' }}" rows="3" placeholder="Informacion adicional sobre el animal...">{{ old('observaciones') }}</textarea>
                    @error('observaciones')<div class="error-msg">{{ $message }}</div>@enderror
                    <div class="field-help-text">Notas opcionales para el alta del animal</div>
                </div>

                <div class="form-section">
                    <div class="form-section-title"><i class="bi bi-tags"></i> Etiquetas</div>
                    <div class="tags-wrap" id="tags-container"></div>
                    <input type="text" class="tag-input" id="tag-input" placeholder="Escribe y presiona Enter">
                    <div class="field-help-text">Anade etiquetas para facilitar la busqueda</div>
                </div>

                <div class="form-footer">
                    <a href="{{ route('animal.index') }}" class="btn-cancelar">Cancelar</a>
                    <button type="button" class="btn-borrador" onclick="alert('Guardado como borrador (proximamente)')">
                        <i class="bi bi-floppy"></i> Guardar como borrador
                    </button>
                    <button type="submit" class="btn-guardar">
                        <i class="bi bi-plus-circle"></i> Registrar animal
                    </button>
                </div>
            </div>

            <div>
                <div class="preview-card">
                    <div class="preview-title"><i class="bi bi-eye preview-title-icon"></i> Vista previa</div>
                    <div class="preview-icon-wrap" id="prev-icon-wrap">
                        <img src="{{ asset('images/vaca.png') }}" alt="especie" id="prev-icon">
                    </div>
                    <div class="preview-row"><span class="pk">Lote</span><span class="pv" id="prev-lote">-</span></div>
                    <div class="preview-row"><span class="pk">Cebadero</span><span class="pv" id="prev-cebadero">-</span></div>
                    <div class="preview-row"><span class="pk">Especie</span><span class="pv" id="prev-especie">-</span></div>
                    <div class="preview-row"><span class="pk">Raza</span><span class="pv" id="prev-raza">-</span></div>
                    <div class="preview-row"><span class="pk">Fecha alta</span><span class="pv" id="prev-fecha">-</span></div>
                </div>
            </div>
        </div>
    </form>
</main>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const ICONOS = {
    'Porcino': '{{ asset("images/cerdo.png") }}',
    'Vacuno': '{{ asset("images/vaca.png") }}',
    'Avicola': '{{ asset("images/pollo.png") }}',
};
const BG_ESPECIE = {
    'Porcino': '#fce7f3',
    'Vacuno': '#e0f2fe',
    'Avicola': '#fef9c3',
};
const RAZAS_POR_ESPECIE = {
    'Avicola': [
        'Pollo de engorde (broiler)',
        'Pavo de engorde',
        'Gallina africana (para carne)',
        'Pollo campero de engorde',
    ],
    'Porcino': [
        'Cerdo ibérico de cebo',
        'Chato murciano',
        'Cerdo blanco de engorde',
        'Cerdo Duroc',
    ],
    'Vacuno': [
        'Ternero de engorde',
        'Novillo',
        'Angus',
        'Ternera de carne',
    ],
};
const RAZA_OLD = @json(old('raza', ''));

function actualizarOpcionesRaza() {
    const especie = document.getElementById('f-especie').value;
    const razaSelect = document.getElementById('f-raza');
    const razaActual = razaSelect.dataset.current ?? RAZA_OLD;
    const razas = RAZAS_POR_ESPECIE[especie] || [];

    razaSelect.innerHTML = '<option value="">Seleccionar raza</option>';

    razas.forEach((raza) => {
        const option = document.createElement('option');
        option.value = raza;
        option.textContent = raza;
        option.selected = raza === razaActual;
        razaSelect.appendChild(option);
    });

    if (!razas.includes(razaActual)) {
        razaSelect.value = '';
    }

    razaSelect.dataset.current = razaSelect.value;
}

function actualizarPreview() {
    const especie = document.getElementById('f-especie').value;
    actualizarOpcionesRaza();
    const raza = document.getElementById('f-raza').value;
    const lote = document.getElementById('f-lote').value;
    const fecha = document.getElementById('f-fecha').value;
    const cebSel = document.getElementById('f-cebadero');
    const cebTxt = cebSel.options[cebSel.selectedIndex]?.text ?? '-';

    document.getElementById('prev-lote').textContent = lote || '-';
    document.getElementById('prev-cebadero').textContent = cebSel.value ? cebTxt : '-';
    document.getElementById('prev-especie').textContent = especie || '-';
    document.getElementById('prev-raza').textContent = raza || '-';
    document.getElementById('prev-fecha').textContent = fecha ? new Date(fecha).toLocaleDateString('es-ES') : '-';

    if (especie && ICONOS[especie]) {
        document.getElementById('prev-icon').src = ICONOS[especie];
        document.getElementById('prev-icon-wrap').style.background = BG_ESPECIE[especie] || '#f3f4f6';
    }

    document.getElementById('f-raza').dataset.current = raza;
}

const tagInput = document.getElementById('tag-input');
const tagContainer = document.getElementById('tags-container');

tagInput.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        const val = this.value.trim();
        if (!val) return;
        const tag = document.createElement('span');
        tag.className = 'tag';
        tag.innerHTML = `${val} <button type="button" onclick="this.parentElement.remove()" title="Eliminar">x</button>`;
        tagContainer.appendChild(tag);
        this.value = '';
    }
});

actualizarPreview();
</script>
@endpush
