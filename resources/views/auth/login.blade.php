<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BioScan - Iniciar sesion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
</head>
<body>

<nav class="top-nav">
    <a href="{{ route('home') }}" class="nav-logo">
        <i class="bi bi-activity"></i>
        Bio<em>Scan</em>
    </a>
    <a href="{{ route('register') }}" class="btn-nav">
        <i class="bi bi-person-plus me-1"></i>Crear cuenta
    </a>
</nav>

<div class="page-wrap">
    <div class="card-wrap">

        <div class="card-logo">Bio<em>Scan</em></div>
        <div class="card-sub">Accede con tu cuenta</div>

        @if(session('status'))
            <div class="alert-status">
                <i class="bi bi-check-circle-fill"></i>{{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label-custom" for="email">Correo electronico</label>
                    <div class="input-icon-wrap">
                        <i class="bi bi-envelope bi-icon"></i>
                        <input type="email" id="email" name="email"
                               class="form-control-custom {{ $errors->get('email') ? 'is-invalid' : '' }}"
                               value="{{ old('email') }}"
                               placeholder="usuario@bioscan.com"
                               required autofocus autocomplete="username">
                    </div>
                    @foreach($errors->get('email') as $msg)
                        <div class="error-msg">{{ $msg }}</div>
                    @endforeach
                </div>

                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label-custom mb-0" for="password">Contraseña</label>
                        @if(Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="forgot-link">¿Has olvidado tu contraseña?</a>
                        @endif
                    </div>
                    <div class="input-icon-wrap">
                        <i class="bi bi-lock bi-icon"></i>
                        <input type="password" id="password" name="password"
                               class="form-control-custom {{ $errors->get('password') ? 'is-invalid' : '' }}"
                               placeholder="Tu contrasena"
                               required autocomplete="current-password">
                        <button type="button" class="toggle-pass" onclick="togglePass('password', this)">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    @foreach($errors->get('password') as $msg)
                        <div class="error-msg">{{ $msg }}</div>
                    @endforeach
                </div>

                <div class="col-12">
                    <label class="remember-wrap">
                        <input type="checkbox" name="remember" id="remember_me">
                        Mantener sesion iniciada
                    </label>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn-submit">
                        <i class="bi bi-box-arrow-in-right me-1"></i>Iniciar sesion
                    </button>
                </div>
            </div>
        </form>

        <div class="card-footer-link">
            ¿No tienes cuenta? <a href="{{ route('register') }}">Registrate</a>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function togglePass(id, btn) {
    const input = document.getElementById(id);
    const icon = btn.querySelector('i');

    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
    }
}
</script>
</body>
</html>
