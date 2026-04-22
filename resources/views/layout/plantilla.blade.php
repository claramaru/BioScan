<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $activeNav = trim($__env->yieldContent('active_nav')) ?: 'dashboard';
        $faviconConfig = [
            'dashboard' => ['label' => 'DB', 'bg' => '#1f6f5b'],
            'animals' => ['label' => 'AN', 'bg' => '#b45309'],
            'cebaderos' => ['label' => 'CE', 'bg' => '#475569'],
            'alimentacion' => ['label' => 'AL', 'bg' => '#15803d'],
            'piensos' => ['label' => 'PI', 'bg' => '#0f766e'],
            'tratamientos' => ['label' => 'TR', 'bg' => '#b91c1c'],
            'revisiones' => ['label' => 'RV', 'bg' => '#7c3aed'],
            'usuarios' => ['label' => 'US', 'bg' => '#1d4ed8'],
        ][$activeNav] ?? ['label' => 'BS', 'bg' => '#1f2937'];

        $faviconSvg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64">
  <rect width="64" height="64" rx="16" fill="{$faviconConfig['bg']}"/>
  <text x="32" y="38" text-anchor="middle" font-size="20" font-family="Arial, sans-serif" font-weight="700" fill="#ffffff">{$faviconConfig['label']}</text>
</svg>
SVG;

        $faviconHref = 'data:image/svg+xml;utf8,' . rawurlencode($faviconSvg);
    @endphp

    <title>@yield('title', config('app.name', 'BioScan'))</title>
    <link rel="icon" type="image/svg+xml" href="{{ $faviconHref }}">

    <link rel="stylesheet" href="{{ asset('css/layout/plantilla.css') }}">

    @stack('styles')
</head>
<body>
    @include('layout._partials.sidebar', ['activeNav' => trim($__env->yieldContent('active_nav')) ?: null])

    @yield('content')

    <script>
    (() => {
        const body = document.body;
        const button = document.getElementById('sidebar-collapse-btn');
        const storageKey = 'bioscan-sidebar-collapsed';
        const mobileQuery = window.matchMedia('(max-width: 991px)');

        if (!button) return;

        const setDesktopCollapsed = (collapsed) => {
            body.classList.toggle('sidebar-collapsed', collapsed);
            button.setAttribute('aria-pressed', collapsed ? 'true' : 'false');
        };

        const setMobileExpanded = (expanded) => {
            body.classList.toggle('sidebar-mobile-expanded', expanded);
            button.setAttribute('aria-pressed', expanded ? 'true' : 'false');
        };

        if (!mobileQuery.matches && window.localStorage.getItem(storageKey) === 'true') {
            setDesktopCollapsed(true);
        }

        button.addEventListener('click', () => {
            if (mobileQuery.matches) {
                const next = !body.classList.contains('sidebar-mobile-expanded');
                setMobileExpanded(next);
                return;
            }

            const next = !body.classList.contains('sidebar-collapsed');
            setDesktopCollapsed(next);
            window.localStorage.setItem(storageKey, next ? 'true' : 'false');
        });

        mobileQuery.addEventListener('change', (event) => {
            if (event.matches) {
                body.classList.remove('sidebar-collapsed');
            } else {
                body.classList.remove('sidebar-mobile-expanded');
                if (window.localStorage.getItem(storageKey) === 'true') {
                    setDesktopCollapsed(true);
                } else {
                    setDesktopCollapsed(false);
                }
            }
        });
    })();
    </script>

    @stack('scripts')
</body>
</html>
