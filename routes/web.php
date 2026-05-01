<?php

use App\Http\Controllers\AnimalController;
use App\Http\Controllers\AlimentacionController;
use App\Http\Controllers\CebaderoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FichaMedicaController;
use App\Http\Controllers\PiensoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

// Portada publica.
Route::get('/', fn () => view('home'))->name('home');

// Estas rutas necesitan sesion iniciada y correo verificado.
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard principal.
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Endpoints JSON del dashboard.
    Route::prefix('api/dashboard')->name('dashboard.')->group(function () {
        // Datos de las tarjetas resumen.
        Route::get('/stats', [DashboardController::class, 'stats'])->name('stats');

        // Tabla de animales recientes.
        Route::get('/animales-recientes', [DashboardController::class, 'animalesRecientes'])->name('animales-recientes');

        // Actividad reciente.
        Route::get('/actividad-reciente', [DashboardController::class, 'actividadReciente'])->name('actividad-reciente');

        // Datos para el grafico por cebadero.
        Route::get('/estadisticas-cebadero', [DashboardController::class, 'estadisticasCebadero'])->name('estadisticas-cebadero');
    });

    // Vista del perfil.
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');

    // Actualizar perfil.
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Eliminar cuenta propia.
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Listado principal de animales.
    Route::get('/animal', [AnimalController::class, 'index'])->name('animal.index');

    // Demo para probar la API de animales.
    Route::get('/animal/api-demo', [AnimalController::class, 'apiDemo'])->name('animal.api.demo');

    // Formulario de acceso rapido.
    Route::get('/animal/acceso-rapido', [AnimalController::class, 'accesoRapidoForm'])->name('animal.quick.form');

    // Busca el codigo y redirige a la ficha.
    Route::post('/animal/acceso-rapido', [AnimalController::class, 'accesoRapidoBuscar'])->name('animal.quick.search');

    // Formulario para crear animal.
    Route::get('/animal/create', [AnimalController::class, 'create'])->name('animal.create');

    // Guardar animal nuevo.
    Route::post('/animal', [AnimalController::class, 'store'])->name('animal.store');

    // Historial del animal.
    Route::get('/animal/{id}/historial', [AnimalController::class, 'historial'])->name('animal.historial');

    // Editar animal segun permisos.
    Route::get('/animal/{id}/edit', [AnimalController::class, 'edit'])->name('animal.edit');

    // Guardar cambios del animal.
    Route::put('/animal/{id}', [AnimalController::class, 'update'])->name('animal.update');

    // Guardar solo observaciones.
    Route::put('/animal/{id}/observaciones', [AnimalController::class, 'updateObservaciones'])->name('animal.obs.update');

    // Borrar animal.
    Route::delete('/animal/{id}', [AnimalController::class, 'destroy'])->name('animal.destroy');

    // Vista principal de cebaderos.
    Route::get('/cebaderos', [CebaderoController::class, 'index'])->name('cebadero.index');

    // Formulario para crear cebadero.
    Route::get('/cebaderos/create', [CebaderoController::class, 'create'])->name('cebadero.create');

    // Guardar nuevo cebadero.
    Route::post('/cebaderos', [CebaderoController::class, 'store'])->name('cebadero.store');

    // Editar cebadero.
    Route::get('/cebaderos/{id}/edit', [CebaderoController::class, 'edit'])->name('cebadero.edit');

    // Guardar cambios del cebadero.
    Route::put('/cebaderos/{id}', [CebaderoController::class, 'update'])->name('cebadero.update');

    // Vista principal de alimentacion.
    Route::get('/alimentacion', [AlimentacionController::class, 'index'])->name('alimentacion.index');

    // Catalogo de piensos.
    Route::get('/piensos', [PiensoController::class, 'index'])->name('pienso.index');
    Route::get('/piensos/create', [PiensoController::class, 'create'])->name('pienso.create');
    Route::post('/piensos', [PiensoController::class, 'store'])->name('pienso.store');
    Route::get('/piensos/{id}/edit', [PiensoController::class, 'edit'])->name('pienso.edit');
    Route::put('/piensos/{id}', [PiensoController::class, 'update'])->name('pienso.update');

    // Formulario para crear un registro de alimentacion.
    Route::get('/alimentacion/create', [AlimentacionController::class, 'create'])->name('alimentacion.create');

    // Guardar un registro nuevo de alimentacion.
    Route::post('/alimentacion', [AlimentacionController::class, 'store'])->name('alimentacion.store');

    // Guardar varios registros de alimentacion de una sola vez.
    Route::post('/alimentacion/masiva', [AlimentacionController::class, 'storeMasivo'])->name('alimentacion.store.bulk');

    // Editar registro de alimentacion.
    Route::get('/alimentacion/{id}/edit', [AlimentacionController::class, 'edit'])->name('alimentacion.edit');

    // Guardar cambios del registro de alimentacion.
    Route::put('/alimentacion/{id}', [AlimentacionController::class, 'update'])->name('alimentacion.update');

    // Eliminar registro de alimentacion.
    Route::delete('/alimentacion/{id}', [AlimentacionController::class, 'destroy'])->name('alimentacion.destroy');

    // Datos de alimentacion para el filtrado asincrono.
    Route::get('/api/alimentacion', [AlimentacionController::class, 'apiListado'])->name('api.alimentacion.index');
    Route::get('/api/piensos', [PiensoController::class, 'apiListado'])->name('api.pienso.index');

    // Endpoints JSON internos del modulo de animales.
    Route::get('/api/animales', [AnimalController::class, 'apiListado'])->name('api.animales.index');
    Route::get('/api/animal/{codigo}', [AnimalController::class, 'apiPorCodigo'])->name('api.animal.codigo');

    // Endpoint JSON interno del modulo de cebaderos.
    Route::get('/api/cebaderos', [CebaderoController::class, 'apiListado'])->name('api.cebaderos.index');

    // Modulo de salud basado en ficha_medica.
    Route::get('/salud', [FichaMedicaController::class, 'index'])->name('salud.index');
    Route::get('/salud/create', [FichaMedicaController::class, 'create'])->name('salud.create');
    Route::post('/salud', [FichaMedicaController::class, 'store'])->name('salud.store');
    Route::get('/salud/{id}/edit', [FichaMedicaController::class, 'edit'])->name('salud.edit');
    Route::put('/salud/{id}', [FichaMedicaController::class, 'update'])->name('salud.update');
    Route::delete('/salud/{id}', [FichaMedicaController::class, 'destroy'])->name('salud.destroy');
    Route::get('/api/salud', [FichaMedicaController::class, 'apiListado'])->name('api.salud.index');

    // Gestion de usuarios.
    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuario.index');

    // Crear usuario.
    Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuario.store');

    // Datos de usuarios para los filtros.
    Route::get('/api/usuarios', [UsuarioController::class, 'data'])->name('usuario.data');

    // Actualizar usuario.
    Route::put('/usuarios/{id}', [UsuarioController::class, 'update'])->name('usuario.update');

    // Borrar usuario.
    Route::delete('/usuarios/{id}', [UsuarioController::class, 'destroy'])->name('usuario.destroy');
});

// Rutas de autenticacion de Breeze.
require __DIR__ . '/auth.php';
