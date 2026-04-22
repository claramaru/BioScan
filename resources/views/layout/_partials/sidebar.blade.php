<aside class="sidebar">
    <div class="sidebar-top">
        <a href="{{ route('dashboard') }}" class="sidebar-logo">
            <i class="bi bi-activity"></i>
            <span class="brand-text">Bio<em>Scan</em></span>
        </a>
    </div>

    <button type="button" class="sidebar-collapse-btn" id="sidebar-collapse-btn" aria-label="Contraer menu lateral" aria-pressed="false">
        <i class="bi bi-chevron-left"></i>
    </button>

    <span class="nav-label">Principal</span>
    <a href="{{ route('dashboard') }}" class="nav-link {{ $activeNav === 'dashboard' ? 'active' : '' }}">
        <i class="bi bi-grid-1x2-fill"></i>
        <span>Dashboard</span>
    </a>
    <a href="{{ route('animal.index') }}" class="nav-link {{ $activeNav === 'animals' ? 'active' : '' }}">
        <i class="bi bi-collection-fill"></i>
        <span>Animales</span>
    </a>
    <a href="{{ route('cebadero.index') }}" class="nav-link {{ $activeNav === 'cebaderos' ? 'active' : '' }}">
        <i class="bi bi-building"></i>
        <span>Cebaderos</span>
    </a>

    <span class="nav-label">Gestión</span>
    <a href="{{ route('alimentacion.index') }}" class="nav-link {{ $activeNav === 'alimentacion' ? 'active' : '' }}">
        <i class="bi bi-basket-fill"></i>
        <span>Alimentación</span>
    </a>
    @if(auth()->user()->tienePrivilegio('gestionar_pienso'))
        <a href="{{ route('pienso.index') }}" class="nav-link {{ $activeNav === 'piensos' ? 'active' : '' }}">
            <i class="bi bi-box-seam-fill"></i>
            <span>Piensos</span>
        </a>
    @endif
    <a href="{{ route('tratamiento.index') }}" class="nav-link {{ $activeNav === 'tratamientos' ? 'active' : '' }}">
        <i class="bi bi-shield-plus"></i>
        <span>Tratamientos</span>
    </a>
    <a href="{{ route('revision.index') }}" class="nav-link {{ $activeNav === 'revisiones' ? 'active' : '' }}">
        <i class="bi bi-heart-pulse-fill"></i>
        <span>Revisiones</span>
    </a>

    @if(auth()->user()->esAdministrador())
        <span class="nav-label">Admin</span>
        <a href="{{ route('usuario.index') }}" class="nav-link {{ $activeNav === 'usuarios' ? 'active' : '' }}">
            <i class="bi bi-people-fill"></i>
            <span>Usuarios</span>
        </a>
    @endif

    <div class="sidebar-footer">
        <div class="avatar">{{ strtoupper(substr(auth()->user()->nombre, 0, 1)) }}</div>
        <div class="sidebar-user-block">
            <div class="sidebar-user-header">
                <div class="sidebar-user-meta">
                    <div class="sidebar-user-name">{{ auth()->user()->nombre }} {{ auth()->user()->apellidos }}</div>
                    <div class="sidebar-user-role">{{ auth()->user()->rol->nombre ?? 'Sin rol' }}</div>
                </div>

                <details class="sidebar-user-menu">
                    <summary class="sidebar-user-menu-toggle" aria-label="Abrir menu de usuario">
                        <i class="bi bi-chevron-up"></i>
                    </summary>

                    <div class="sidebar-user-menu-panel">
                        <a href="{{ route('profile.edit') }}" class="sidebar-user-menu-link">
                            <i class="bi bi-person"></i>
                            <span>Perfil</span>
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="sidebar-user-menu-link sidebar-user-menu-button">
                                <i class="bi bi-box-arrow-right"></i>
                                <span>Cerrar sesión</span>
                            </button>
                        </form>
                    </div>
                </details>
            </div>
        </div>
    </div>
</aside>
