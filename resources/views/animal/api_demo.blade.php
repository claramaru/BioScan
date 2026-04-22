@extends('layout.plantilla')

@section('title', 'API de animales')
@section('active_nav', 'animals')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/animal/api-demo.css') }}">
@endpush

@section('content')
<main class="main-wrap">
    <div class="top-bar">
        <div>
            <div class="page-title">API de animales</div>
            <p class="page-subtitle">Demo sencilla para consultar el listado desde <code>/api/animales</code>.</p>
        </div>
        <div class="toolbar">
            <input id="buscador-api" class="api-input" type="text" placeholder="Buscar por codigo o lote">
            <button id="btn-cargar-api" class="api-button" type="button">Recargar</button>
        </div>
    </div>

    <div id="estado-api" class="alert d-none" role="alert"></div>

    <section class="table-card">
        <div class="tc-header">
            <div class="api-card-title">Resultado JSON consumido en la vista</div>
            <a class="api-endpoint" href="{{ route('api.animales.index') }}" target="_blank" rel="noreferrer">
                {{ route('api.animales.index') }}
            </a>
        </div>

        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Codigo</th>
                        <th>Lote</th>
                        <th>Fecha alta</th>
                    </tr>
                </thead>
                <tbody id="tabla-api-animales"></tbody>
            </table>
        </div>
    </section>
</main>
@endsection

@push('scripts')
<script>
(() => {
    // Demo simple para probar la API desde la propia vista.
    const estado = document.getElementById('estado-api');
    const tabla = document.getElementById('tabla-api-animales');
    const buscador = document.getElementById('buscador-api');
    const boton = document.getElementById('btn-cargar-api');
    const baseUrl = @json(route('api.animales.index'));

    function mostrarEstado(texto, tipo) {
        // Uso el mismo aviso para carga y errores.
        estado.className = 'alert alert-' + tipo;
        estado.textContent = texto;
        estado.classList.remove('d-none');
    }

    function ocultarEstado() {
        estado.classList.add('d-none');
    }

    function pintarFilas(animales) {
        tabla.innerHTML = '';

        if (!animales || animales.length === 0) {
            // Si no hay resultados, dejo una fila con el mensaje.
            const fila = document.createElement('tr');
            fila.innerHTML = '<td colspan="4" class="text-center text-muted py-4">No hay resultados</td>';
            tabla.appendChild(fila);
            return;
        }

        animales.forEach((animal) => {
            // Pinto cada fila directamente con lo que devuelve la API.
            const fila = document.createElement('tr');
            fila.innerHTML = `
                <td>${animal.id_animal}</td>
                <td><strong>${animal.codigo}</strong></td>
                <td>${animal.lote ?? '-'}</td>
                <td>${animal.fecha_alta ?? '-'}</td>
            `;
            tabla.appendChild(fila);
        });
    }

    async function cargarAnimales() {
        const q = buscador.value.trim();
        // Si hay texto, lo mando como parametro en la URL.
        const url = q === '' ? baseUrl : `${baseUrl}?q=${encodeURIComponent(q)}`;

        mostrarEstado('Cargando datos de la API...', 'info');

        try {
            // Pido JSON de forma explicita.
            const respuesta = await fetch(url, {
                headers: {
                    'Accept': 'application/json'
                }
            });

            if (!respuesta.ok) {
                throw new Error('HTTP ' + respuesta.status);
            }

            const json = await respuesta.json();

            // Compruebo que la respuesta venga con la estructura esperada.
            if (json.ok !== true || !Array.isArray(json.data)) {
                throw new Error('Respuesta JSON no valida');
            }

            pintarFilas(json.data);
            ocultarEstado();
        } catch (error) {
            mostrarEstado('Error cargando datos: ' + error.message, 'danger');
        }
    }

    // El boton vuelve a cargar el endpoint.
    boton.addEventListener('click', cargarAnimales);

    // Si pulso Enter en el buscador, hago la misma carga.
    buscador.addEventListener('keyup', (event) => {
        if (event.key === 'Enter') {
            cargarAnimales();
        }
    });

    // Primera carga al entrar en la vista.
    cargarAnimales();
})();
</script>
@endpush
