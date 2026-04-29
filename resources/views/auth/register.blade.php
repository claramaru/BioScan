<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BioScan - Crear cuenta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/auth/register.css') }}">
</head>
<body>

<nav class="top-nav">
    <a href="{{ route('home') }}" class="nav-logo">
        <i class="bi bi-activity"></i>
        Bio<em>Scan</em>
    </a>
    <a href="{{ route('login') }}" class="btn-nav">
        <i class="bi bi-box-arrow-in-right me-1"></i>Iniciar sesion
    </a>
</nav>

<div class="page-wrap">
    <div class="card-wrap">

        <div class="card-logo">Bio<em>Scan</em></div>
        <div class="card-sub">Crea tu cuenta para acceder al sistema</div>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label-custom" for="nombre">Nombre</label>
                    <div class="input-icon-wrap">
                        <i class="bi bi-person bi-icon"></i>
                        <input type="text" id="nombre" name="nombre"
                               class="form-control-custom {{ $errors->get('nombre') ? 'is-invalid' : '' }}"
                               value="{{ old('nombre') }}"
                               placeholder="Admin"
                               required autofocus autocomplete="given-name">
                    </div>
                    @foreach($errors->get('nombre') as $msg)
                        <div class="error-msg">{{ $msg }}</div>
                    @endforeach
                </div>

                <div class="col-md-6">
                    <label class="form-label-custom" for="apellidos">Apellidos</label>
                    <div class="input-icon-wrap">
                        <i class="bi bi-person bi-icon"></i>
                        <input type="text" id="apellidos" name="apellidos"
                               class="form-control-custom {{ $errors->get('apellidos') ? 'is-invalid' : '' }}"
                               value="{{ old('apellidos') }}"
                               placeholder="Garcia Lopez"
                               required autocomplete="family-name">
                    </div>
                    @foreach($errors->get('apellidos') as $msg)
                        <div class="error-msg">{{ $msg }}</div>
                    @endforeach
                </div>

                <div class="col-12">
                    <label class="form-label-custom" for="email">Correo electronico</label>
                    <div class="input-icon-wrap">
                        <i class="bi bi-envelope bi-icon"></i>
                        <input type="email" id="email" name="email"
                               class="form-control-custom {{ $errors->get('email') ? 'is-invalid' : '' }}"
                               value="{{ old('email') }}"
                               placeholder="usuario@bioscan.com"
                               required autocomplete="username">
                    </div>
                    @foreach($errors->get('email') as $msg)
                        <div class="error-msg">{{ $msg }}</div>
                    @endforeach
                </div>

                <div class="col-12"><hr class="form-divider"></div>

                <div class="col-12">
                    <label class="form-label-custom" for="password">Contrasena</label>
                    <div class="input-icon-wrap">
                        <i class="bi bi-lock bi-icon"></i>
                        <input type="password" id="password" name="password"
                               class="form-control-custom {{ $errors->get('password') ? 'is-invalid' : '' }}"
                               placeholder="Minimo 8 caracteres"
                               required autocomplete="new-password">
                        <button type="button" class="toggle-pass" onclick="togglePass('password', this)" aria-label="Mostrar contrasena">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    @foreach($errors->get('password') as $msg)
                        <div class="error-msg">{{ $msg }}</div>
                    @endforeach
                </div>

                <div class="col-12">
                    <label class="form-label-custom" for="password_confirmation">Confirmar contrasena</label>
                    <div class="input-icon-wrap">
                        <i class="bi bi-lock-fill bi-icon"></i>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                               class="form-control-custom {{ $errors->get('password_confirmation') ? 'is-invalid' : '' }}"
                               placeholder="Repite la contrasena"
                               required autocomplete="new-password">
                        <button type="button" class="toggle-pass" onclick="togglePass('password_confirmation', this)" aria-label="Mostrar contrasena">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    @foreach($errors->get('password_confirmation') as $msg)
                        <div class="error-msg">{{ $msg }}</div>
                    @endforeach
                </div>

                <div class="col-12">
                    <button type="submit" class="btn-submit">
                        <i class="bi bi-person-plus me-1"></i>Crear cuenta
                    </button>
                </div>
            </div>
        </form>

        <div class="card-footer-link">
            Ya tienes cuenta? <a href="{{ route('login') }}">Inicia sesion</a>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function togglePass(id, btn) {
    // Esto permite enseñar u ocultar la contraseña sin tocar el formulario.
    const input = document.getElementById(id);
    const icon = btn.querySelector('i');

    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
        btn.setAttribute('aria-label', 'Ocultar contrasena');
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
        btn.setAttribute('aria-label', 'Mostrar contrasena');
    }
}
</script>
</body>
</html>
