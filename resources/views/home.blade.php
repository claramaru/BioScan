<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BioScan - Acceso al sistema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
</head>
<body>

<!-- Navbar -->
<nav class="top-nav">
    <a href="{{ route('home') }}" class="nav-logo">
        <i class="bi bi-activity"></i>
        Bio<em>Scan</em>
    </a>
    <div class="nav-links">
        @auth
            <a href="{{ route('dashboard') }}" class="btn-register">
                <i class="bi bi-grid-1x2-fill me-1"></i>Dashboard
            </a>
        @else
            <a href="{{ route('login') }}" class="btn-login">Iniciar sesión</a>
            <a href="{{ route('register') }}" class="btn-register">Registrarse</a>
        @endauth
    </div>
</nav>

<!-- Hero -->
<section class="hero">
    <div class="hero-inner">

        @auth
            <!-- Usuario ya logueado -->
            <div class="auth-banner">
                <div class="auth-avatar">{{ strtoupper(substr(auth()->user()->nombre, 0, 1)) }}</div>
                <div>
                    <div class="auth-name">Hola, {{ auth()->user()->nombre }} {{ auth()->user()->apellidos }}</div>
                    <div class="auth-role">{{ auth()->user()->rol->nombre ?? 'Sin rol' }}</div>
                </div>
            </div>
            <div class="hero-cta">
                <a href="{{ route('dashboard') }}" class="cta-primary">
                    <i class="bi bi-grid-1x2-fill me-1"></i>Ir al dashboard
                </a>
                <a href="{{ route('animal.index') }}" class="cta-secondary">
                    Ver animales
                </a>
            </div>
        @else
            <!-- No logueado -->
            <div class="hero-brand">
                <i class="bi bi-activity"></i>
                Bio<em>Scan</em>
            </div>
            <div class="hero-badge"><i class="bi bi-shield-check"></i> Sistema de gestión ganadera</div>
            <h1 class="hero-title">Control total de tu <em>ganadería</em></h1>
            <p class="hero-sub">BioScan centraliza el seguimiento de animales, alimentación, tratamientos y revisiones médicas en un solo lugar.</p>
            <div class="hero-cta">
                <a href="{{ route('login') }}" class="cta-primary">
                    <i class="bi bi-box-arrow-in-right me-1"></i>Iniciar sesión
                </a>
                <a href="{{ route('register') }}" class="cta-secondary">Crear cuenta</a>
            </div>
        @endauth

    </div>
</section>

<!-- Feature cards -->
@guest
<div class="features">
    <div class="feat-grid">
        <div class="feat-card">
            <div class="feat-icon" style="background:#fce7f3;">&#128055;</div>
            <div class="feat-title">Gestión de animales</div>
            <div class="feat-desc">Registro completo por especie, lote y cebadero con historial médico.</div>
        </div>
        <div class="feat-card">
            <div class="feat-icon" style="background:#fef3c7;">&#127806;</div>
            <div class="feat-title">Control de pienso</div>
            <div class="feat-desc">Seguimiento de alimentación y consumo promedio por tipo de animal.</div>
        </div>
        <div class="feat-card">
            <div class="feat-icon" style="background:#e0f2fe;">&#128137;</div>
            <div class="feat-title">Fichas médicas</div>
            <div class="feat-desc">Diagnósticos, tratamientos y observaciones vinculados a cada animal.</div>
        </div>
    </div>
</div>
@endguest

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
