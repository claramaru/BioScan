@extends('layout.plantilla')

@section('title', 'Perfil')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/profile/edit.css') }}">
@endpush

@section('content')
<main class="main-wrap">
    <div class="top-bar">
        <div>
            <div class="profile-title">Perfil</div>
            <div class="profile-subtitle">Gestiona tu información personal, correo electrónico, contraseña y opciones de seguridad desde un solo lugar.</div>
        </div>
    </div>

    <div class="profile-layout">
        <div class="profile-stack">
            <div class="profile-card">
                @include('profile.partials.update-profile-information-form')
            </div>

            <div class="profile-card">
                @include('profile.partials.update-password-form')
            </div>

            <div class="profile-card profile-card-danger">
                @include('profile.partials.delete-user-form')
            </div>
        </div>

        <aside class="profile-help-box">
            <div class="profile-card-title">Tu cuenta</div>

            <ul class="profile-help-list">
                <li class="profile-help-item">
                    <div class="profile-help-icon"><i class="bi bi-envelope"></i></div>
                    <div class="profile-help-copy">
                        <strong>Correo</strong>
                        <span>Actualiza tu email y mantén el acceso de la cuenta al día.</span>
                    </div>
                </li>
                <li class="profile-help-item">
                    <div class="profile-help-icon"><i class="bi bi-shield-lock"></i></div>
                    <div class="profile-help-copy">
                        <strong>Seguridad</strong>
                        <span>Cambia la contraseña cuando lo necesites para reforzar la seguridad.</span>
                    </div>
                </li>
                <li class="profile-help-item">
                    <div class="profile-help-icon"><i class="bi bi-person-x"></i></div>
                    <div class="profile-help-copy">
                        <strong>Eliminar cuenta</strong>
                        <span>Esta acción es permanente y borrará el acceso del usuario.</span>
                    </div>
                </li>
            </ul>
        </aside>
    </div>
</main>
@endsection
