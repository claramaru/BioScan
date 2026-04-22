<?php

// ── Añade estas rutas dentro del middleware 'auth' en routes/web.php ──────────

use App\Http\Controllers\DashboardController;

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // AJAX endpoints
    Route::prefix('api/dashboard')->name('dashboard.')->group(function () {
        Route::get('/stats',                [DashboardController::class, 'stats']               )->name('stats');
        Route::get('/animales-recientes',   [DashboardController::class, 'animalesRecientes']   )->name('animales-recientes');
        Route::get('/actividad-reciente',   [DashboardController::class, 'actividadReciente']   )->name('actividad-reciente');
        Route::get('/estadisticas-cebadero',[DashboardController::class, 'estadisticasCebadero'])->name('estadisticas-cebadero');
    });

});
