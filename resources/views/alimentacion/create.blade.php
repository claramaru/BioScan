@extends('layout.plantilla')

@section('title', 'Nuevo registro de alimentacion')
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
            <div class="page-title">Nuevo registro de alimentacion</div>
            <div class="page-subtitle-feed">Registra una nueva alimentacion, con o sin asociarla a un animal concreto.</div>
        </div>
        <a href="{{ $volverUrl ?? route('alimentacion.index') }}" class="animals-top-btn animals-top-btn-secondary">
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

    <form method="POST" action="{{ route('alimentacion.store') }}">
        @csrf
        <input type="hidden" name="return_to" value="{{ old('return_to', $returnTo ?? 'alimentacion.index') }}">
        <input type="hidden" name="return_animal_id" value="{{ old('return_animal_id', $returnAnimalId ?? '') }}">
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
                                    <option value="{{ data_get($animal, 'id_animal') }}" {{ (string) old('id_animal', $animalSeleccionado ?? '') === (string) data_get($animal, 'id_animal') ? 'selected' : '' }}>
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
                                    <option value="{{ data_get($tipo, 'id_pienso') }}" {{ (string) old('id_pienso') === (string) data_get($tipo, 'id_pienso') ? 'selected' : '' }}>{{ data_get($tipo, 'nombre') }}</option>
                                @endforeach
                            </select>
                            @error('id_pienso')<div class="error-msg">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="cantidad" class="form-label-custom">Cantidad (kg) <span class="required-dot">*</span></label>
                            <input type="number" step="0.01" min="0.01" name="cantidad" id="cantidad" class="form-control-custom {{ $errors->has('cantidad') ? 'is-invalid' : '' }}" value="{{ old('cantidad') }}" required>
                            @error('cantidad')<div class="error-msg">{{ $message }}</div>@enderror
                            <div id="cantidad-recomendada" class="field-help-text"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="fecha" class="form-label-custom">Fecha <span class="required-dot">*</span></label>
                            <input type="date" name="fecha" id="fecha" class="form-control-custom {{ $errors->has('fecha') ? 'is-invalid' : '' }}" value="{{ old('fecha', date('Y-m-d')) }}" required>
                            @error('fecha')<div class="error-msg">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="form-footer">
                    <a href="{{ $volverUrl ?? route('alimentacion.index') }}" class="btn-cancelar">Cancelar</a>
                    <button type="submit" class="btn-guardar">
                        <i class="bi bi-plus-circle"></i> Guardar registro
                    </button>
                </div>
            </div>

            <div>
                <div class="preview-card">
                    <div class="preview-title"><i class="bi bi-info-circle"></i> Recordatorio</div>
                    <div class="preview-copy">
                        Puedes guardar una alimentacion general o dejar ya asociado el animal si vienes desde su ficha.
                    </div>
                    <div class="preview-row"><span class="pk">Modo</span><span class="pv">Flexible</span></div>
                    <div class="preview-row"><span class="pk">Usuario</span><span class="pv">Actual</span></div>
                    <div class="preview-row"><span class="pk">Fecha por defecto</span><span class="pv">{{ date('d/m/Y') }}</span></div>
                </div>
            </div>
        </div>
    </form>
</main>
@endsection

@push('scripts')
<script>
(() => {
    const recomendaciones = @json($recomendacionesCantidad ?? []);
    const animalSelect = document.getElementById('id_animal');
    const tipoSelect = document.getElementById('id_pienso');
    const cantidadInput = document.getElementById('cantidad');
    const ayuda = document.getElementById('cantidad-recomendada');
    let ultimaCantidadSugerida = '';

    function formatoKg(valor) {
        return `${Number(valor).toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} kg`;
    }

    function calcularSugerencia() {
        const animalId = animalSelect?.value || '';
        const tipo = tipoSelect?.value || '';
        if (!tipo) return null;

        const animal = recomendaciones.animales?.[animalId];
        const especieKey = animal?.especie_key || '';
        const porAnimal = recomendaciones.por_animal?.[animalId]?.[tipo];
        if (porAnimal) {
            return { cantidad: porAnimal, origen: `media previa de ${animal?.codigo || 'este animal'}` };
        }

        const porEspecie = recomendaciones.por_especie?.[especieKey]?.[tipo];
        if (porEspecie) {
            return { cantidad: porEspecie, origen: `media de la especie ${animal?.especie || ''}`.trim() };
        }

        const porTipo = recomendaciones.por_tipo?.[tipo];
        if (porTipo) {
            return { cantidad: porTipo, origen: 'media general de este pienso' };
        }

        return null;
    }

    function actualizarSugerencia() {
        const sugerencia = calcularSugerencia();
        if (!ayuda) return;

        if (!sugerencia) {
            ayuda.textContent = 'No hay una recomendacion historica disponible todavia.';
            return;
        }

        ayuda.textContent = `Cantidad recomendada: ${formatoKg(sugerencia.cantidad)} segun ${sugerencia.origen}.`;
        if (!cantidadInput.value || cantidadInput.value === ultimaCantidadSugerida) {
            ultimaCantidadSugerida = Number(sugerencia.cantidad).toFixed(2);
            cantidadInput.value = ultimaCantidadSugerida;
        }
    }

    animalSelect?.addEventListener('change', actualizarSugerencia);
    tipoSelect?.addEventListener('change', actualizarSugerencia);
    actualizarSugerencia();
})();
</script>
@endpush
