@php
use Illuminate\Support\Facades\DB;
// Helper para determinar si está en modo técnico
$isViewingAsTechnician = session('view_as_technician', false) && auth()->check() && auth()->user()->hasRole('super-admin');

// Helper function para obtener la ruta correcta según el modo
function getTechnicianRoute($routeName, ...$params) {
    $isViewingAsTechnician = session('view_as_technician', false) && auth()->check() && auth()->user()->hasRole('super-admin');

    if ($isViewingAsTechnician) {
        // Mapear rutas de technician a technician-view
        $routeMap = [
            'technician.service.detail' => 'technician-view.service.detail',
            'technician.service.checklist' => 'technician-view.service.checklist',
            'technician.service.checklist.stage' => 'technician-view.service.checklist.stage',
            'technician.service.checklist.location' => 'technician-view.service.checklist.location',
            'technician.service.checklist.process-location' => 'technician-view.service.checklist.process-location',
            'technician.service.checklist.submit' => 'technician-view.service.checklist.submit',
            'technician.service.pdf' => 'technician-view.service.pdf',
            'technician.service.checklist-details' => 'technician-view.service.checklist-details',
        ];

        $mappedRoute = $routeMap[$routeName] ?? $routeName;
        return route($mappedRoute, ...$params);
    }

    return route($routeName, ...$params);
}
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full" id="html-root">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <title>{{ config('app.name', 'PestController') }} - @yield('title', 'Dashboard')</title>

    <!-- PWA Meta Tags -->
    <meta name="application-name" content="{{ config('pwa.name', 'PestController') }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="{{ config('pwa.short_name', 'PestCtrl') }}">
    <meta name="description" content="{{ config('pwa.description', 'Sistema de gestión para control de plagas') }}">
    <meta name="format-detection" content="telephone=no">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="theme-color" content="{{ config('pwa.theme_color', '#1f2937') }}">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <!-- PWA Manifest -->
    <link rel="manifest" href="/manifest.json">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- FullCalendar -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Critical CSS para prevenir FOUC en iconos y menús -->
    <style>
        /* Prevenir FOUC en iconos de notificaciones y usuario */
        #notification-button,
        #user-menu-button,
        #notification-button-mobile,
        #user-menu-button-mobile {
            width: 40px !important;
            height: 40px !important;
            min-width: 40px !important;
            min-height: 40px !important;
            max-width: 40px !important;
            max-height: 40px !important;
            padding: 8px !important;
            box-sizing: border-box !important;
        }

        #notification-button svg,
        #notification-button-mobile svg {
            width: 24px !important;
            height: 24px !important;
            min-width: 24px !important;
            min-height: 24px !important;
            max-width: 24px !important;
            max-height: 24px !important;
            display: block !important;
            flex-shrink: 0 !important;
        }

        #user-menu-button > div,
        #user-menu-button-mobile > div {
            width: 40px !important;
            height: 40px !important;
            min-width: 40px !important;
            min-height: 40px !important;
            max-width: 40px !important;
            max-height: 40px !important;
        }

        #user-menu-button > div > span,
        #user-menu-button-mobile > div > span {
            font-size: 14px !important;
            line-height: 1 !important;
        }

        /* Asegurar que los menús dropdown estén ocultos por defecto */
        .notification-menu,
        .user-menu,
        #notification-menu-mobile,
        #user-menu-mobile {
            opacity: 0 !important;
            visibility: hidden !important;
            transform: translateY(-10px) !important;
            pointer-events: none !important;
        }

        .notification-menu.show,
        .user-menu.show,
        #notification-menu-mobile.show,
        #user-menu-mobile.show {
            opacity: 1 !important;
            visibility: visible !important;
            transform: translateY(0) !important;
            pointer-events: auto !important;
        }

        /* Optimizar carga en móvil */
        @media (max-width: 767px) {
            body {
                overflow-x: hidden !important;
            }

            /* Asegurar que el sidebar esté oculto por defecto en móvil */
            #sidebar {
                transform: translateX(-100%) !important;
            }

            /* Asegurar que los menús no se muestren accidentalmente */
            .notification-menu:not(.show),
            .user-menu:not(.show),
            #notification-menu-mobile:not(.show),
            #user-menu-mobile:not(.show) {
                display: none !important;
            }
        }
    </style>

    <!-- Script inline para prevenir FOUC - se ejecuta inmediatamente -->
    <script>
        (function() {
            // Función para aplicar estilos críticos a los iconos
            function applyCriticalIconStyles() {
                const notificationButton = document.getElementById('notification-button');
                const userMenuButton = document.getElementById('user-menu-button');

                if (notificationButton) {
                    notificationButton.style.setProperty('width', '40px', 'important');
                    notificationButton.style.setProperty('height', '40px', 'important');
                    notificationButton.style.setProperty('min-width', '40px', 'important');
                    notificationButton.style.setProperty('min-height', '40px', 'important');
                    notificationButton.style.setProperty('max-width', '40px', 'important');
                    notificationButton.style.setProperty('max-height', '40px', 'important');
                    notificationButton.style.setProperty('padding', '8px', 'important');
                    notificationButton.style.setProperty('box-sizing', 'border-box', 'important');

                    const svg = notificationButton.querySelector('svg');
                    if (svg) {
                        svg.style.setProperty('width', '24px', 'important');
                        svg.style.setProperty('height', '24px', 'important');
                        svg.style.setProperty('min-width', '24px', 'important');
                        svg.style.setProperty('min-height', '24px', 'important');
                        svg.style.setProperty('max-width', '24px', 'important');
                        svg.style.setProperty('max-height', '24px', 'important');
                        svg.style.setProperty('display', 'block', 'important');
                        svg.style.setProperty('flex-shrink', '0', 'important');
                    }
                }

                if (userMenuButton) {
                    userMenuButton.style.setProperty('width', '40px', 'important');
                    userMenuButton.style.setProperty('height', '40px', 'important');
                    userMenuButton.style.setProperty('min-width', '40px', 'important');
                    userMenuButton.style.setProperty('min-height', '40px', 'important');
                    userMenuButton.style.setProperty('max-width', '40px', 'important');
                    userMenuButton.style.setProperty('max-height', '40px', 'important');
                    userMenuButton.style.setProperty('padding', '0', 'important');
                    userMenuButton.style.setProperty('box-sizing', 'border-box', 'important');

                    const div = userMenuButton.querySelector('div');
                    if (div) {
                        div.style.setProperty('width', '40px', 'important');
                        div.style.setProperty('height', '40px', 'important');
                        div.style.setProperty('min-width', '40px', 'important');
                        div.style.setProperty('min-height', '40px', 'important');
                        div.style.setProperty('max-width', '40px', 'important');
                        div.style.setProperty('max-height', '40px', 'important');

                        const span = div.querySelector('span');
                        if (span) {
                            span.style.setProperty('font-size', '14px', 'important');
                            span.style.setProperty('line-height', '1', 'important');
                        }
                    }
                }
            }

            // Aplicar inmediatamente si el DOM ya está listo
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    applyCriticalIconStyles();
                    setTimeout(applyCriticalIconStyles, 0);
                    setTimeout(applyCriticalIconStyles, 10);
                    setTimeout(applyCriticalIconStyles, 50);
                    setTimeout(applyCriticalIconStyles, 100);
                });
            } else {
                applyCriticalIconStyles();
                setTimeout(applyCriticalIconStyles, 0);
                setTimeout(applyCriticalIconStyles, 10);
                setTimeout(applyCriticalIconStyles, 50);
                setTimeout(applyCriticalIconStyles, 100);
            }

            // Usar MutationObserver para aplicar estilos cuando los elementos se agreguen al DOM
            if (typeof MutationObserver !== 'undefined' && document.body) {
                try {
                    const observer = new MutationObserver(function(mutations) {
                        let shouldApply = false;
                        mutations.forEach(function(mutation) {
                            if (mutation.addedNodes.length > 0) {
                                mutation.addedNodes.forEach(function(node) {
                                    if (node && node.nodeType === 1) {
                                        if (node.id === 'notification-button' || node.id === 'user-menu-button' ||
                                            (node.querySelector && (node.querySelector('#notification-button') || node.querySelector('#user-menu-button')))) {
                                            shouldApply = true;
                                        }
                                    }
                                });
                            }
                        });
                        if (shouldApply) {
                            applyCriticalIconStyles();
                        }
                    });

                    observer.observe(document.body, {
                        childList: true,
                        subtree: true
                    });
                } catch (e) {
                    console.warn('MutationObserver error:', e);
                }
            }

            // Aplicar también en requestAnimationFrame para máxima prioridad
            if (typeof requestAnimationFrame !== 'undefined') {
                requestAnimationFrame(function() {
                    applyCriticalIconStyles();
                    requestAnimationFrame(function() {
                        applyCriticalIconStyles();
                    });
                });
            }
        })();
    </script>

    @stack('styles')
</head>
<body class="h-full" id="body-element" style="overflow-x: hidden; background: #f9fafb;">
    <div id="app" class="flex h-full flex-row">
        <!-- Mobile Overlay -->
        <div id="mobile-overlay" class="md:hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-40 hidden" style="z-index: 9998;"></div>

        <!-- Sidebar -->
        <aside id="sidebar" class="fixed md:static flex-shrink-0 w-80 md:w-72 flex flex-col bg-white border-r border-gray-200 transform -translate-x-full md:translate-x-0 md:transform-none transition-transform duration-300 ease-in-out z-50 h-full">
            <div class="flex grow flex-col gap-y-3 overflow-y-auto px-6 md:px-5 pb-4">
                <div class="flex h-16 shrink-0 items-center justify-center pt-6 pb-4">
                    <img src="https://pestcontroller.cl/wp-content/uploads/2022/07/pestcontroller-logo.png" alt="PestController Logo" class="h-14 w-auto object-contain max-w-full">
                </div>

                <!-- Separator -->
                <div class="border-t border-gray-200 my-3"></div>

                <!-- Quick Actions - Modo Oscuro/Claro Switch -->
                <div class="mb-3">
                    <div id="dark-mode-container" class="w-full flex items-center justify-between gap-x-3 rounded-lg py-2.5 px-3 text-xs transition-colors duration-200">
                        <div class="flex items-center gap-x-2">
                            <svg id="dark-mode-icon" class="h-4 w-4 transition-colors duration-200" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="text-gray-600 dark:text-gray-300">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                            </svg>
                            <span id="dark-mode-text" class="text-xs font-medium transition-colors duration-200" style="font-size: 11px; color: #111827;">Modo Claro</span>
                        </div>
                        <button id="dark-mode-toggle" type="button" class="relative inline-flex h-7 w-12 items-center rounded-full transition-all duration-300 ease-in-out focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2" style="background-color: #d1d5db;" role="switch" aria-checked="false">
                            <span id="dark-mode-switch-thumb" class="inline-block h-6 w-6 transform rounded-full bg-white shadow-md transition-all duration-300 ease-in-out" style="translate: 0.125rem 0;"></span>
                        </button>
                    </div>
                </div>

                <!-- Ver como Técnico (solo para super-admin) -->
                @auth
                    @if(auth()->user()->hasRole('super-admin'))
                        @if(session('view_as_technician'))
                            <form action="{{ route('admin.stop-viewing-as-technician') }}" method="POST" class="mb-2.5">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-x-2 rounded-lg py-2 px-2.5 text-xs hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="text-xs font-medium">Salir de Vista Técnico</span>
                                </button>
                            </form>
                        @else
                            <form action="{{ route('admin.view-as-technician') }}" method="POST" class="mb-2.5">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-x-2 rounded-lg py-2 px-2.5 text-xs hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="text-gray-600 dark:text-gray-300">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span class="text-xs font-medium">Ver como Técnico</span>
                                </button>
                            </form>
                        @endif
                    @endif
                @endauth

                <!-- Separator -->
                <div class="border-t border-gray-200 my-3"></div>

                <nav class="flex flex-1 flex-col">
                    <ul role="list" class="flex flex-1 flex-col gap-y-7">
                        @if($isViewingAsTechnician)
                            <!-- MENÚ DE TÉCNICO -->
                            <li>
                                <div class="text-xs font-semibold uppercase mb-3">Menú Principal</div>
                                <ul role="list" class="-mx-2 space-y-2">
                                <li>
                                    <a href="{{ route('technician-view.dashboard') }}" class="group flex items-center gap-x-4 md:gap-x-6 rounded-lg px-4 py-3.5 md:py-3 text-sm leading-5 font-medium {{ request()->routeIs('technician-view.dashboard') ? 'bg-green-500 text-white' : 'text-gray-900 dark:text-gray-300 hover:bg-gray-800 hover:text-white dark:hover:bg-gray-800' }}" style="{{ !request()->routeIs('technician-view.dashboard') ? 'color: #111827 !important;' : '' }}">
                                        <svg class="h-6 w-6 md:h-5 md:w-5 shrink-0 {{ request()->routeIs('technician-view.dashboard') ? 'text-white' : 'text-gray-900 dark:text-gray-400 group-hover:text-white' }}" style="margin-right: 12px !important; {{ !request()->routeIs('technician-view.dashboard') ? 'color: #111827 !important;' : '' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                                        </svg>
                                        <span class="{{ request()->routeIs('technician-view.dashboard') ? 'text-white' : 'text-gray-900 dark:text-gray-300 group-hover:text-white' }}" style="font-size: 15px; font-weight: 500; {{ !request()->routeIs('technician-view.dashboard') ? 'color: #111827 !important;' : '' }}">Dashboard</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('technician-view.services') }}" class="group flex items-center gap-x-4 md:gap-x-6 rounded-lg px-4 py-3.5 md:py-3 text-sm leading-5 font-medium {{ request()->routeIs('technician-view.services') || request()->routeIs('technician-view.service.*') ? 'bg-green-500 text-white' : 'text-gray-900 dark:text-gray-300 hover:bg-gray-800 hover:text-white dark:hover:bg-gray-800' }}" style="{{ !(request()->routeIs('technician-view.services') || request()->routeIs('technician-view.service.*')) ? 'color: #111827 !important;' : '' }}">
                                        <svg class="h-6 w-6 md:h-5 md:w-5 shrink-0 {{ request()->routeIs('technician-view.services') || request()->routeIs('technician-view.service.*') ? 'text-white' : 'text-gray-900 dark:text-gray-400 group-hover:text-white' }}" style="margin-right: 12px !important; {{ !(request()->routeIs('technician-view.services') || request()->routeIs('technician-view.service.*')) ? 'color: #111827 !important;' : '' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5a2.25 2.25 0 002.25-2.25m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5a2.25 2.25 0 012.25 2.25v7.5" />
                                        </svg>
                                        <span class="{{ request()->routeIs('technician-view.services') || request()->routeIs('technician-view.service.*') ? 'text-white' : 'text-gray-900 dark:text-gray-300 group-hover:text-white' }}" style="font-size: 15px; font-weight: 500; {{ !(request()->routeIs('technician-view.services') || request()->routeIs('technician-view.service.*')) ? 'color: #111827 !important;' : '' }}">Mis Servicios</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('technician-view.profile') }}" class="group flex items-center gap-x-4 md:gap-x-6 rounded-lg px-4 py-3.5 md:py-3 text-sm leading-5 font-medium {{ request()->routeIs('technician-view.profile') ? 'bg-green-500 text-white' : 'text-gray-900 dark:text-gray-300 hover:bg-gray-800 hover:text-white dark:hover:bg-gray-800' }}" style="{{ !request()->routeIs('technician-view.profile') ? 'color: #111827 !important;' : '' }}">
                                        <svg class="h-6 w-6 md:h-5 md:w-5 shrink-0 {{ request()->routeIs('technician-view.profile') ? 'text-white' : 'text-gray-900 dark:text-gray-400 group-hover:text-white' }}" style="margin-right: 12px !important; {{ !request()->routeIs('technician-view.profile') ? 'color: #111827 !important;' : '' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                        </svg>
                                        <span class="{{ request()->routeIs('technician-view.profile') ? 'text-white' : 'text-gray-900 dark:text-gray-300 group-hover:text-white' }}" style="font-size: 15px; font-weight: 500; {{ !request()->routeIs('technician-view.profile') ? 'color: #111827 !important;' : '' }}">Mi Perfil</span>
                                    </a>
                                </li>
                                </ul>
                            </li>
                        @else
                            <!-- MENÚ DE ADMIN -->
                            <li>
                                <div class="text-xs font-semibold uppercase mb-3">Menú Principal</div>
                                <ul role="list" class="-mx-2 space-y-1.5">
                                    <li>
                                        <a href="{{ auth()->user()->hasRole('super-admin') ? route('admin.dashboard') : route('technician.dashboard') }}" class="group flex items-center gap-x-6 rounded-md px-3 py-3 text-sm leading-5 font-medium {{ request()->routeIs('dashboard') || request()->routeIs('admin.dashboard') || request()->routeIs('technician.dashboard') ? 'bg-green-500 text-white' : 'text-gray-900 dark:text-gray-300 hover:bg-gray-800 hover:text-white dark:hover:bg-gray-800' }}" style="{{ !(request()->routeIs('dashboard') || request()->routeIs('admin.dashboard') || request()->routeIs('technician.dashboard')) ? 'color: #111827 !important;' : '' }}">
                                            <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('dashboard') || request()->routeIs('admin.dashboard') || request()->routeIs('technician.dashboard') ? 'text-white' : 'text-gray-900 dark:text-gray-400 group-hover:text-white' }}" style="margin-right: 20px !important; {{ !(request()->routeIs('dashboard') || request()->routeIs('admin.dashboard') || request()->routeIs('technician.dashboard')) ? 'color: #111827 !important;' : '' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                                            </svg>
                                            <span class="{{ request()->routeIs('dashboard') || request()->routeIs('admin.dashboard') || request()->routeIs('technician.dashboard') ? 'text-white' : 'text-gray-900 dark:text-gray-300 group-hover:text-white' }}" style="font-size: 15px; font-weight: 500; {{ !(request()->routeIs('dashboard') || request()->routeIs('admin.dashboard') || request()->routeIs('technician.dashboard')) ? 'color: #111827 !important;' : '' }}">Dashboard</span>
                                        </a>
                                    </li>
                                {{-- <li>
                                    <a href="{{ route('calendar') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('calendar') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                                        <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5a2.25 2.25 0 002.25-2.25m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5a2.25 2.25 0 012.25 2.25v7.5" />
                                        </svg>
                                        Agenda
                                    </a>
                                </li> --}}
                                {{-- <li>
                                    <a href="{{ route('work-orders.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('work-orders.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                                        <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                                        </svg>
                                        Órdenes de Trabajo
                                    </a>
                                </li> --}}
                                @if(auth()->user()->hasRole('super-admin'))
                                <li>
                                    <a href="{{ route('admin.clients.index') ?? route('clients.index') ?? '#' }}" class="group flex items-center gap-x-6 rounded-md px-3 py-3 text-sm leading-5 font-medium {{ request()->routeIs('clients.*') || request()->routeIs('admin.clients.*') ? 'bg-green-500 text-white' : 'text-gray-900 dark:text-gray-300 hover:bg-gray-800 hover:text-white dark:hover:bg-gray-800' }}" style="{{ !(request()->routeIs('clients.*') || request()->routeIs('admin.clients.*')) ? 'color: #111827 !important;' : '' }}">
                                        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('clients.*') || request()->routeIs('admin.clients.*') ? 'text-white' : 'text-gray-900 dark:text-gray-400 group-hover:text-white' }}" style="margin-right: 20px !important; {{ !(request()->routeIs('clients.*') || request()->routeIs('admin.clients.*')) ? 'color: #111827 !important;' : '' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                        </svg>
                                        <span class="{{ request()->routeIs('clients.*') || request()->routeIs('admin.clients.*') ? 'text-white' : 'text-gray-900 dark:text-gray-300 group-hover:text-white' }}" style="font-size: 15px; font-weight: 500; {{ !(request()->routeIs('clients.*') || request()->routeIs('admin.clients.*')) ? 'color: #111827 !important;' : '' }}">Clientes</span>
                                    </a>
                                </li>
                                @endif
                                {{-- <li>
                                    <a href="{{ route('sites.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('sites.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                                        <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                                        </svg>
                                        Sitios
                                    </a>
                                </li> --}}
                                <li>
                                    <a href="{{ auth()->user()->hasRole('super-admin') ? (route('admin.services.index') ?? route('services.index') ?? '#') : route('technician.services') }}" class="group flex items-center gap-x-6 rounded-md px-3 py-3 text-sm leading-5 font-medium {{ request()->routeIs('services.*') || request()->routeIs('admin.services.*') || request()->routeIs('technician.services') ? 'bg-green-500 text-white' : 'text-gray-900 dark:text-gray-300 hover:bg-gray-800 hover:text-white dark:hover:bg-gray-800' }}" style="{{ !(request()->routeIs('services.*') || request()->routeIs('admin.services.*') || request()->routeIs('technician.services')) ? 'color: #111827 !important;' : '' }}">
                                        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('services.*') || request()->routeIs('admin.services.*') || request()->routeIs('technician.services') ? 'text-white' : 'text-gray-900 dark:text-gray-400 group-hover:text-white' }}" style="margin-right: 20px !important; {{ !(request()->routeIs('services.*') || request()->routeIs('admin.services.*') || request()->routeIs('technician.services')) ? 'color: #111827 !important;' : '' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5a2.25 2.25 0 002.25-2.25m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5a2.25 2.25 0 012.25 2.25v7.5" />
                                        </svg>
                                        <span class="{{ request()->routeIs('services.*') || request()->routeIs('admin.services.*') || request()->routeIs('technician.services') ? 'text-white' : 'text-gray-900 dark:text-gray-300 group-hover:text-white' }}" style="font-size: 15px; font-weight: 500; {{ !(request()->routeIs('services.*') || request()->routeIs('admin.services.*') || request()->routeIs('technician.services')) ? 'color: #111827 !important;' : '' }}">{{ auth()->user()->hasRole('super-admin') ? 'Servicios' : 'Mis Servicios' }}</span>
                                    </a>
                                </li>
                                @if(auth()->user()->hasRole('technician'))
                                <li>
                                    <a href="{{ route('technician.profile') }}" class="group flex items-center gap-x-6 rounded-md px-3 py-3 text-sm leading-5 font-medium {{ request()->routeIs('technician.profile') ? 'bg-green-500 text-white' : 'text-gray-900 dark:text-gray-300 hover:bg-gray-800 hover:text-white dark:hover:bg-gray-800' }}" style="{{ !request()->routeIs('technician.profile') ? 'color: #111827 !important;' : '' }}">
                                        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('technician.profile') ? 'text-white' : 'text-gray-900 dark:text-gray-400 group-hover:text-white' }}" style="margin-right: 20px !important; {{ !request()->routeIs('technician.profile') ? 'color: #111827 !important;' : '' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                        </svg>
                                        <span class="{{ request()->routeIs('technician.profile') ? 'text-white' : 'text-gray-900 dark:text-gray-300 group-hover:text-white' }}" style="font-size: 15px; font-weight: 500; {{ !request()->routeIs('technician.profile') ? 'color: #111827 !important;' : '' }}">Mi Perfil</span>
                                    </a>
                                </li>
                                @endif
                                @if(auth()->user()->hasRole('super-admin'))
                                <li>
                                    <a href="{{ route('admin.reports.index') ?? '#' }}" class="group flex items-center gap-x-6 rounded-md px-3 py-3 text-sm leading-5 font-medium {{ request()->routeIs('admin.reports.*') || request()->routeIs('reports.*') ? 'bg-green-500 text-white' : 'text-gray-900 dark:text-gray-300 hover:bg-gray-800 hover:text-white dark:hover:bg-gray-800' }}" style="{{ !(request()->routeIs('admin.reports.*') || request()->routeIs('reports.*')) ? 'color: #111827 !important;' : '' }}">
                                        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('admin.reports.*') || request()->routeIs('reports.*') ? 'text-white' : 'text-gray-900 dark:text-gray-400 group-hover:text-white' }}" style="margin-right: 20px !important; {{ !(request()->routeIs('admin.reports.*') || request()->routeIs('reports.*')) ? 'color: #111827 !important;' : '' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                        </svg>
                                        <span class="{{ request()->routeIs('admin.reports.*') || request()->routeIs('reports.*') ? 'text-white' : 'text-gray-900 dark:text-gray-300 group-hover:text-white' }}" style="font-size: 15px; font-weight: 500; {{ !(request()->routeIs('admin.reports.*') || request()->routeIs('reports.*')) ? 'color: #111827 !important;' : '' }}">Reportes</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.users.index', ['role' => 'technician']) }}" class="group flex items-center gap-x-6 rounded-md px-3 py-3 text-sm leading-5 font-medium {{ request()->routeIs('admin.users.index') && request()->get('role') === 'technician' ? 'bg-green-500 text-white' : 'text-gray-900 dark:text-gray-300 hover:bg-gray-800 hover:text-white dark:hover:bg-gray-800' }}" style="{{ !(request()->routeIs('admin.users.index') && request()->get('role') === 'technician') ? 'color: #111827 !important;' : '' }}">
                                        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('admin.users.index') && request()->get('role') === 'technician' ? 'text-white' : 'text-gray-900 dark:text-gray-400 group-hover:text-white' }}" style="margin-right: 20px !important; {{ !(request()->routeIs('admin.users.index') && request()->get('role') === 'technician') ? 'color: #111827 !important;' : '' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                        </svg>
                                        <span class="{{ request()->routeIs('admin.users.index') && request()->get('role') === 'technician' ? 'text-white' : 'text-gray-900 dark:text-gray-300 group-hover:text-white' }}" style="font-size: 15px; font-weight: 500; {{ !(request()->routeIs('admin.users.index') && request()->get('role') === 'technician') ? 'color: #111827 !important;' : '' }}">Técnicos</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.pests') ?? '#' }}" class="group flex items-center gap-x-6 rounded-md px-3 py-3 text-sm leading-5 font-medium {{ request()->routeIs('admin.pests') ? 'bg-green-500 text-white' : 'text-gray-900 dark:text-gray-300 hover:bg-gray-800 hover:text-white dark:hover:bg-gray-800' }}" style="{{ !request()->routeIs('admin.pests') ? 'color: #111827 !important;' : '' }}">
                                        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('admin.pests') ? 'text-white' : 'text-gray-900 dark:text-gray-400 group-hover:text-white' }}" style="margin-right: 20px !important; {{ !request()->routeIs('admin.pests') ? 'color: #111827 !important;' : '' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 12.75c.855 0 1.515-.49 2.164-1.193a9.753 9.753 0 001.847-2.745 1.99 1.99 0 00-.133-1.623 4.117 4.117 0 00-1.09-1.466c.465-.73.93-1.52 1.242-2.371a48.626 48.626 0 00-8.68 0c.312.851.777 1.641 1.242 2.371a4.117 4.117 0 00-1.09 1.466 1.99 1.99 0 00-.133 1.623 9.753 9.753 0 001.847 2.745c.649.703 1.309 1.193 2.164 1.193z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6.75a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                                        </svg>
                                        <span class="{{ request()->routeIs('admin.pests') ? 'text-white' : 'text-gray-900 dark:text-gray-300 group-hover:text-white' }}" style="font-size: 15px; font-weight: 500; {{ !request()->routeIs('admin.pests') ? 'color: #111827 !important;' : '' }}">Plagas</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.products.index') ?? '#' }}" class="group flex items-center gap-x-6 rounded-md px-3 py-3 text-sm leading-5 font-medium {{ request()->routeIs('admin.products.*') ? 'bg-green-500 text-white' : 'text-gray-900 dark:text-gray-300 hover:bg-gray-800 hover:text-white dark:hover:bg-gray-800' }}" style="{{ !request()->routeIs('admin.products.*') ? 'color: #111827 !important;' : '' }}">
                                        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('admin.products.*') ? 'text-white' : 'text-gray-900 dark:text-gray-400 group-hover:text-white' }}" style="margin-right: 20px !important; {{ !request()->routeIs('admin.products.*') ? 'color: #111827 !important;' : '' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                        </svg>
                                        <span class="{{ request()->routeIs('admin.products.*') ? 'text-white' : 'text-gray-900 dark:text-gray-300 group-hover:text-white' }}" style="font-size: 15px; font-weight: 500; {{ !request()->routeIs('admin.products.*') ? 'color: #111827 !important;' : '' }}">Productos</span>
                                    </a>
                                </li>
                                @endif
                                {{-- <li>
                                    <a href="{{ route('materials.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('materials.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                                        <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                        </svg>
                                        Materiales
                                    </a>
                                </li> --}}
                                {{-- <li>
                                    <a href="{{ route('reports.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('reports.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                                        <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                                        </svg>
                                        Reportes
                                    </a>
                                </li> --}}
                            </ul>
                        </li>
                        @endif

                        @if(auth()->user()->hasRole('super-admin') && !$isViewingAsTechnician)
                        <li>
                            <div class="text-xs font-semibold uppercase mb-3">Administración</div>
                            <ul role="list" class="-mx-2 space-y-1.5">
                                <li>
                                    <a href="{{ route('admin.users.index') }}" class="group flex items-center gap-x-6 rounded-md px-3 py-3 text-sm leading-5 font-medium {{ request()->routeIs('admin.users.*') ? 'bg-green-500 text-white' : 'text-gray-900 dark:text-gray-300 hover:bg-gray-800 hover:text-white dark:hover:bg-gray-800' }}">
                                        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('admin.users.*') ? 'text-white' : 'text-gray-900 dark:text-gray-400 group-hover:text-white' }}" style="margin-right: 20px !important; {{ !request()->routeIs('admin.users.*') ? 'color: #111827 !important;' : '' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                        </svg>
                                        <span class="{{ request()->routeIs('admin.users.*') ? 'text-white' : 'text-gray-900 dark:text-gray-300 group-hover:text-white' }}" style="font-size: 15px; font-weight: 500; {{ !request()->routeIs('admin.users.*') ? 'color: #111827 !important;' : '' }}">Usuarios</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.notification-center') ?? '#' }}" class="group flex items-center gap-x-6 rounded-md px-3 py-3 text-sm leading-5 font-medium {{ request()->routeIs('admin.notification-center') ? 'bg-green-500 text-white' : 'text-gray-900 dark:text-gray-300 hover:bg-gray-800 hover:text-white dark:hover:bg-gray-800' }}" style="{{ !request()->routeIs('admin.notification-center') ? 'color: #111827 !important;' : '' }}">
                                        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('admin.notification-center') ? 'text-white' : 'text-gray-900 dark:text-gray-400 group-hover:text-white' }}" style="margin-right: 20px !important; {{ !request()->routeIs('admin.notification-center') ? 'color: #111827 !important;' : '' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                                        </svg>
                                        <span class="{{ request()->routeIs('admin.notification-center') ? 'text-white' : 'text-gray-900 dark:text-gray-300 group-hover:text-white' }}" style="font-size: 15px; font-weight: 500; {{ !request()->routeIs('admin.notification-center') ? 'color: #111827 !important;' : '' }}">Notificaciones</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ Route::has('admin.settings') ? route('admin.settings') : '#' }}" class="group flex items-center gap-x-6 rounded-md px-3 py-3 text-sm leading-5 font-medium {{ request()->routeIs('admin.settings.*') || request()->routeIs('admin.settings') ? 'bg-green-500 text-white' : 'text-gray-900 dark:text-gray-300 hover:bg-gray-800 hover:text-white dark:hover:bg-gray-800' }}" style="{{ !(request()->routeIs('admin.settings.*') || request()->routeIs('admin.settings')) ? 'color: #111827 !important;' : '' }}">
                                        <svg class="h-5 w-5 shrink-0 {{ (request()->routeIs('admin.settings.*') || request()->routeIs('admin.settings')) ? 'text-white' : 'text-gray-900 dark:text-gray-400 group-hover:text-white' }}" style="margin-right: 20px !important; {{ !(request()->routeIs('admin.settings.*') || request()->routeIs('admin.settings')) ? 'color: #111827 !important;' : '' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span class="{{ (request()->routeIs('admin.settings.*') || request()->routeIs('admin.settings')) ? 'text-white' : 'text-gray-900 dark:text-gray-300 group-hover:text-white' }}" style="font-size: 15px; font-weight: 500; {{ !(request()->routeIs('admin.settings.*') || request()->routeIs('admin.settings')) ? 'color: #111827 !important;' : '' }}">Configuraciones</span>
                                    </a>
                                </li>
                                {{-- <li>
                                    <a href="{{ route('admin.settings') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.settings') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                                        <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        Configuración
                                    </a>
                                </li> --}}
                            </ul>
                        </li>
                        @endif

                    </ul>
                </nav>
            </div>
        </aside>

        <!-- Main content -->
        <div class="flex-1 flex flex-col min-w-0 overflow-x-hidden">

            <!-- Fixed Header (solo móvil) -->
            <header class="md:hidden fixed top-0 left-0 right-0 bg-white border-b border-gray-200" style="width: 100%; z-index: 9999;">
                <div class="w-full px-4 py-3 flex items-center justify-between gap-4">
                    <!-- Left: Mobile Menu Button -->
                    <button id="header-mobile-menu-button" class="flex-shrink-0 p-2 rounded-lg bg-white border border-gray-300 shadow-md hover:bg-gray-50 transition-colors" style="z-index: 50; background-color: rgb(255, 255, 255);">
                        <svg id="header-menu-icon" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="text-gray-900 dark:text-white">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"></path>
                        </svg>
                        <svg id="header-close-icon" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="text-gray-900 dark:text-white">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>

                    <!-- Center: Success/Status Message -->
                    <div class="flex-1 max-w-3xl">
                        @if(session('success'))
                            <div class="rounded-lg px-4 py-2.5 flex items-center gap-3">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <p class="text-sm font-medium">{{ session('success') }}</p>
                            </div>
                        @elseif(session('view_as_technician') && auth()->check() && auth()->user()->hasRole('super-admin'))
                            <div class="rounded-lg px-4 py-2.5 flex items-center gap-3">
                                <svg class="h-5 w-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                <p class="text-sm font-medium">
                                    Viendo como <strong>Técnico</strong>
                                </p>
                            </div>
                        @endif
                    </div>

                    <!-- Right: Notifications & User Menu -->
                    <div class="flex items-center gap-3 flex-shrink-0">
                        <!-- Notifications -->
                        <div class="relative" id="header-notification-dropdown">
                            <button type="button" id="header-notification-button" class="relative flex items-center justify-center w-10 h-10 rounded-full hover:bg-gray-100 transition-colors">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="text-gray-600 dark:text-gray-300">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                                </svg>
                                @php
                                    $unreadCount = auth()->check() ? auth()->user()->unreadNotifications()->count() : 0;
                                @endphp
                                @if($unreadCount > 0)
                                    <span class="absolute text-white text-xs rounded-full flex items-center justify-center font-semibold">
                                        {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                                    </span>
                                @endif
                            </button>

                            <!-- Notification Dropdown Menu -->
                            <div id="header-notification-menu" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50" style="max-height: 400px; overflow-y: auto;">
                                <div class="p-3 border-b border-gray-200 flex justify-between items-center">
                                    <h3 class="font-semibold text-gray-900">Notificaciones</h3>
                                    @if(Route::has('admin.notification-center'))
                                    <a href="{{ auth()->user()->hasRole('super-admin') ? route('admin.notification-center') : route('technician.notifications.index') }}" class="text-sm text-green-600 hover:text-green-700">Ver todas</a>
                                    @endif
                                </div>
                                <div class="p-2">
                                    @php
                                        $recentNotifications = auth()->check() ? auth()->user()->notifications()->take(5)->get() : collect();
                                    @endphp
                                    @if($recentNotifications->count() > 0)
                                        @foreach($recentNotifications as $notification)
                                            @php
                                                $data = is_array($notification->data) ? $notification->data : json_decode($notification->data, true);
                                                $title = $data['title'] ?? 'Notificación';
                                                $message = $data['message'] ?? '';
                                                $isRead = !is_null($notification->read_at);
                                            @endphp
                                            <div class="p-3 hover:bg-gray-50 rounded-lg cursor-pointer {{ !$isRead ? 'bg-green-50' : '' }}">
                                                <div class="flex justify-between items-start">
                                                    <h4 class="font-semibold text-sm text-gray-900">{{ $title }}</h4>
                                                    <span class="text-xs text-gray-500">{{ $notification->created_at->diffForHumans() }}</span>
                                                </div>
                                                <p class="text-sm text-gray-600 mt-1">{{ Str::limit($message, 80) }}</p>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="p-6 text-center text-gray-500">
                                            <p>No hay notificaciones</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- User Menu -->
                        <div class="relative" id="header-user-dropdown">
                            <button type="button" id="header-user-button" class="flex items-center justify-center w-10 h-10 rounded-full hover:bg-gray-100 transition-colors">
                                <div class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center">
                                    <span class="text-sm font-medium text-white">
                                        {{ auth()->check() ? strtoupper(substr(auth()->user()->name, 0, 1)) : 'U' }}
                                    </span>
                                </div>
                            </button>

                            <!-- User Dropdown Menu -->
                            <div id="header-user-menu" class="hidden absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
                                <div class="p-3 border-b border-gray-200">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-full bg-green-500 flex items-center justify-center flex-shrink-0">
                                            <span class="text-sm font-medium text-white">
                                                {{ auth()->check() ? strtoupper(substr(auth()->user()->name, 0, 1)) : 'U' }}
                                            </span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold text-gray-900 truncate">{{ auth()->check() ? auth()->user()->name : 'Usuario' }}</p>
                                            <p class="text-xs text-gray-500 truncate">{{ auth()->check() ? auth()->user()->email : '' }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-2">
                                    @if(Route::has('profile'))
                                    <a href="{{ route('profile') }}" class="flex items-center gap-3 px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                        </svg>
                                        <span>Mi Perfil</span>
                                    </a>
                                    @endif
                                    @if(Route::has('admin.settings'))
                                    <a href="{{ route('admin.settings') }}" class="flex items-center gap-3 px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span>Configuración</span>
                                    </a>
                                    @endif
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                                            </svg>
                                            <span>Cerrar Sesión</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page content -->
            <main class="flex-1">
                <style>
                    @media (min-width: 768px) {
                        main {
                            padding-top: 0.75rem !important;
                        }
                    }
                </style>

                <div class="px-3 sm:px-4 md:px-6 lg:px-8 max-w-full py-3 md:py-3">
                    @if(session('error'))
                        <div class="mb-4 rounded-md bg-red-50 p-4">
                            <div class="flex">
                                <div class="shrink-0">
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js')
                    .then(function(registration) {
                        console.log('SW registered: ', registration);
                    })
                    .catch(function(registrationError) {
                        console.log('SW registration failed: ', registrationError);
                    });
            });
        }
    </script>

    @stack('scripts')

    <script>
        // Mobile Menu Toggle
        (function() {
            const sidebar = document.getElementById('sidebar');
            const mobileOverlay = document.getElementById('mobile-overlay');

            function openMobileMenu() {
                if (!sidebar) {
                    console.warn('Sidebar no encontrado');
                    return;
                }

                console.log('Abriendo menú móvil desde layout...');

                // Remover clase que oculta el sidebar
                sidebar.classList.remove('-translate-x-full');
                // Agregar clase que muestra el sidebar
                sidebar.classList.add('translate-x-0');

                // Crear un style tag para sobrescribir el CSS crítico
                let styleTag = document.getElementById('mobile-menu-override-style');
                if (!styleTag) {
                    styleTag = document.createElement('style');
                    styleTag.id = 'mobile-menu-override-style';
                    document.head.appendChild(styleTag);
                }
                styleTag.textContent = `
                    #sidebar {
                        transform: translateX(0) !important;
                        display: flex !important;
                        visibility: visible !important;
                        opacity: 1 !important;
                        z-index: 9999 !important;
                        position: fixed !important;
                        left: 0 !important;
                        top: 0 !important;
                        width: 288px !important;
                        height: 100vh !important;
                    }
                `;

                // También aplicar estilos inline como respaldo
                sidebar.style.cssText = `
                    display: flex !important;
                    transform: translateX(0) !important;
                    visibility: visible !important;
                    opacity: 1 !important;
                    z-index: 9999 !important;
                    position: fixed !important;
                    left: 0 !important;
                    top: 0 !important;
                    width: 288px !important;
                    height: 100vh !important;
                `;

                // Mostrar overlay
                if (mobileOverlay) {
                    mobileOverlay.classList.remove('hidden');
                    mobileOverlay.style.cssText = `
                        display: block !important;
                        visibility: visible !important;
                        z-index: 9998 !important;
                    `;
                }

                // Cambiar iconos (layout, dashboard, páginas y header)
                const menuIcon = document.getElementById('menu-icon');
                const closeIcon = document.getElementById('close-icon');
                const dashboardMenuIcon = document.getElementById('dashboard-menu-icon');
                const dashboardCloseIcon = document.getElementById('dashboard-close-icon');
                const pageMenuIcon = document.getElementById('page-menu-icon');
                const pageCloseIcon = document.getElementById('page-close-icon');
                const headerMenuIcon = document.getElementById('header-menu-icon');
                const headerCloseIcon = document.getElementById('header-close-icon');
                if (menuIcon) menuIcon.classList.add('hidden');
                if (closeIcon) closeIcon.classList.remove('hidden');
                if (dashboardMenuIcon) dashboardMenuIcon.classList.add('hidden');
                if (dashboardCloseIcon) dashboardCloseIcon.classList.remove('hidden');
                if (pageMenuIcon) pageMenuIcon.classList.add('hidden');
                if (pageCloseIcon) pageCloseIcon.classList.remove('hidden');
                if (headerMenuIcon) headerMenuIcon.classList.add('hidden');
                if (headerCloseIcon) headerCloseIcon.classList.remove('hidden');

                // Bloquear scroll del body
                document.body.style.overflow = 'hidden';

                console.log('Menú móvil abierto desde layout');
            }

            function closeMobileMenu() {
                // Solo cerrar en móvil, no en desktop
                if (window.innerWidth < 768) {
                    if (sidebar) {
                        sidebar.classList.remove('translate-x-0');
                        sidebar.classList.add('-translate-x-full');

                        // Remover el style tag de override
                        const styleTag = document.getElementById('mobile-menu-override-style');
                        if (styleTag) {
                            styleTag.remove();
                        }

                        // Asegurar que el sidebar esté oculto
                        sidebar.style.cssText = `
                            transform: translateX(-100%) !important;
                        `;
                    }
                    if (mobileOverlay) {
                        mobileOverlay.classList.add('hidden');
                        mobileOverlay.style.display = 'none';
                    }
                    document.body.style.overflow = '';
                }
                // Cambiar iconos (layout, dashboard, páginas y header)
                const menuIcon = document.getElementById('menu-icon');
                const closeIcon = document.getElementById('close-icon');
                const dashboardMenuIcon = document.getElementById('dashboard-menu-icon');
                const dashboardCloseIcon = document.getElementById('dashboard-close-icon');
                const pageMenuIcon = document.getElementById('page-menu-icon');
                const pageCloseIcon = document.getElementById('page-close-icon');
                const headerMenuIcon = document.getElementById('header-menu-icon');
                const headerCloseIcon = document.getElementById('header-close-icon');
                if (menuIcon) menuIcon.classList.remove('hidden');
                if (closeIcon) closeIcon.classList.add('hidden');
                if (dashboardMenuIcon) dashboardMenuIcon.classList.remove('hidden');
                if (dashboardCloseIcon) dashboardCloseIcon.classList.add('hidden');
                if (pageMenuIcon) pageMenuIcon.classList.remove('hidden');
                if (pageCloseIcon) pageCloseIcon.classList.add('hidden');
                if (headerMenuIcon) headerMenuIcon.classList.remove('hidden');
                if (headerCloseIcon) headerCloseIcon.classList.add('hidden');
            }

            if (mobileOverlay) {
                mobileOverlay.addEventListener('click', function() {
                    closeMobileMenu();
                });
            }

            // Cerrar menú al hacer clic en un enlace (solo en móvil)
            if (sidebar) {
                const sidebarLinks = sidebar.querySelectorAll('a');
                sidebarLinks.forEach(link => {
                    link.addEventListener('click', function() {
                        if (window.innerWidth < 768) {
                            closeMobileMenu();
                        }
                    });
                });
            }

            // Asegurar que el sidebar esté visible en desktop
            function ensureDesktopSidebar() {
                if (window.innerWidth >= 768) {
                    if (sidebar) {
                        sidebar.classList.remove('-translate-x-full');
                        sidebar.classList.add('translate-x-0');
                        // Forzar estilos de desktop
                        sidebar.style.transform = 'none';
                        sidebar.style.position = 'static';
                        sidebar.style.zIndex = 'auto';
                        sidebar.style.left = 'auto';
                        sidebar.style.top = 'auto';
                    }
                    if (mobileOverlay) {
                        mobileOverlay.classList.add('hidden');
                    }
                    document.body.style.overflow = '';
                } else {
                    // En móvil, restaurar fixed y z-index alto
                    if (sidebar) {
                        sidebar.style.position = 'fixed';
                        sidebar.style.zIndex = '9999';
                        sidebar.style.left = '0';
                        sidebar.style.top = '0';
                    }
                }
            }

            // Ejecutar al cargar y al redimensionar
            ensureDesktopSidebar();

            // Cerrar menú al redimensionar a desktop
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 768) {
                    closeMobileMenu();
                    ensureDesktopSidebar();
                }
            });

            // Conectar el botón de hamburguesa del header
            const headerMobileMenuButton = document.getElementById('header-mobile-menu-button');
            if (headerMobileMenuButton) {
                headerMobileMenuButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    if (sidebar && sidebar.classList.contains('-translate-x-full')) {
                        openMobileMenu();
                    } else {
                        closeMobileMenu();
                    }
                });
            }
        })();

        // Header Notification and User Menu Dropdowns
        (function() {
            const notificationButton = document.getElementById('header-notification-button');
            const notificationMenu = document.getElementById('header-notification-menu');
            const userButton = document.getElementById('header-user-button');
            const userMenu = document.getElementById('header-user-menu');

            // Toggle notification menu
            if (notificationButton && notificationMenu) {
                notificationButton.addEventListener('click', function(e) {
                    e.stopPropagation();
                    notificationMenu.classList.toggle('hidden');
                    // Close user menu if open
                    if (userMenu) {
                        userMenu.classList.add('hidden');
                    }
                });
            }

            // Toggle user menu
            if (userButton && userMenu) {
                userButton.addEventListener('click', function(e) {
                    e.stopPropagation();
                    userMenu.classList.toggle('hidden');
                    // Close notification menu if open
                    if (notificationMenu) {
                        notificationMenu.classList.add('hidden');
                    }
                });
            }

            // Close menus when clicking outside
            document.addEventListener('click', function(e) {
                if (notificationMenu && !notificationMenu.contains(e.target) && e.target !== notificationButton) {
                    notificationMenu.classList.add('hidden');
                }
                if (userMenu && !userMenu.contains(e.target) && e.target !== userButton) {
                    userMenu.classList.add('hidden');
                }
            });
        })();

        // Dark Mode Toggle Switch
        (function() {
            const htmlRoot = document.documentElement;
            const toggleButton = document.getElementById('dark-mode-toggle');
            const switchThumb = document.getElementById('dark-mode-switch-thumb');
            const modeText = document.getElementById('dark-mode-text');
            const modeIcon = document.getElementById('dark-mode-icon');
            const modeContainer = document.getElementById('dark-mode-container');
            const sidebar = document.querySelector('aside');
            const mainContent = document.querySelector('main');

            // Función para obtener el tema actual
            function getCurrentTheme() {
                const saved = localStorage.getItem('darkMode');
                if (saved !== null) {
                    return saved === 'true';
                }
                // Por defecto, siempre iniciar en modo claro (no seguir preferencia del sistema)
                return false;
            }

            // Función para aplicar el tema
            function applyTheme(isDark) {
                if (isDark) {
                    // Aplicar modo oscuro
                    htmlRoot.classList.add('dark');

                    // Actualizar switch
                    toggleButton.style.backgroundColor = '#22c55e';
                    switchThumb.style.transform = 'translateX(1.375rem)';
                    switchThumb.style.transition = 'transform 300ms cubic-bezier(0.4, 0, 0.2, 1)';
                    modeText.textContent = 'Modo Oscuro';
                    modeText.style.color = '#e5e7eb';
                    modeIcon.style.color = '#e5e7eb';
                    modeContainer.style.background = '#1f2937';
                    modeContainer.style.borderColor = '#374151';

                    // Actualizar icono
                    modeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />';

                    // Actualizar sidebar
                    if (sidebar) {
                        sidebar.style.background = '#111827';
                        sidebar.style.borderColor = '#374151';
                    }

                    // Actualizar main content
                    if (mainContent) {
                        mainContent.style.background = '#0f172a';
                    }

                    // Actualizar body y html
                    const bodyEl = document.getElementById('body-element');
                    const htmlEl = document.getElementById('html-root');
                    if (bodyEl) {
                        bodyEl.style.background = '#0f172a';
                    }
                    if (htmlEl) {
                        htmlEl.style.background = '#0f172a';
                    }

                    // Actualizar todos los elementos con bg-white
                    const whiteElements = document.querySelectorAll('.bg-white, [class*="bg-white"]');
                    whiteElements.forEach(el => {
                        if (!el.closest('.bg-green-500') && !el.classList.contains('bg-green-500')) {
                            el.style.backgroundColor = '#1f2937';
                            el.style.color = '#e5e7eb';
                        }
                    });

                    // Actualizar tablas
                    const tableBodies = document.querySelectorAll('tbody');
                    tableBodies.forEach(tbody => {
                        tbody.style.backgroundColor = '#1f2937';
                        tbody.style.color = '#e5e7eb';
                    });

                    const tableHeaders = document.querySelectorAll('thead');
                    tableHeaders.forEach(thead => {
                        thead.style.backgroundColor = '#111827';
                    });

                    // Actualizar inputs y selects
                    const inputs = document.querySelectorAll('input, select, textarea');
                    inputs.forEach(input => {
                        if (input.type !== 'checkbox' && input.type !== 'radio') {
                            input.style.backgroundColor = '#1f2937';
                            input.style.color = '#e5e7eb';
                            input.style.borderColor = '#374151';
                        }
                    });

                    // Actualizar textos en modo oscuro - solo cambiar a blanco
                    const textElements = document.querySelectorAll('h1, h2, h3, h4, h5, h6, p, span, td, th, label, .statistics-text, .statistics-number');
                    textElements.forEach(el => {
                        if (!el.closest('.bg-green-500') && !el.classList.contains('bg-green-500')) {
                            const style = el.getAttribute('style') || '';

                            // Solo cambiar si tiene color #111827 o #6b7280 en el estilo inline
                            if (style.includes('color: #111827')) {
                                el.style.color = '#ffffff';
                            } else if (style.includes('color: #6b7280')) {
                                el.style.color = '#e5e7eb';
                            }
                        }
                    });

                    // Actualizar todos los textos e iconos del menú
                    const menuTexts = document.querySelectorAll('nav span, nav a:not(.bg-green-500)');
                    const menuIcons = document.querySelectorAll('nav svg:not(.bg-green-500 svg)');
                    const menuLabels = document.querySelectorAll('.text-xs.font-semibold.uppercase');
                    const verComoTecnico = document.querySelector('button:has(svg[viewBox="0 0 24 24"])');

                    menuTexts.forEach(el => {
                        if (!el.closest('.bg-green-500')) {
                            el.style.color = '#d1d5db';
                        }
                    });

                    menuIcons.forEach(el => {
                        if (!el.closest('.bg-green-500')) {
                            el.style.color = '#9ca3af';
                        }
                    });

                    menuLabels.forEach(el => {
                        el.style.color = '#9ca3af';
                    });

                    if (verComoTecnico) {
                        const verComoTecnicoText = verComoTecnico.querySelector('span');
                        const verComoTecnicoIcon = verComoTecnico.querySelector('svg');
                        if (verComoTecnicoText) verComoTecnicoText.style.color = '#d1d5db';
                        if (verComoTecnicoIcon) verComoTecnicoIcon.style.color = '#9ca3af';
                    }
                } else {
                    // Aplicar modo claro
                    htmlRoot.classList.remove('dark');

                    // Actualizar switch
                    toggleButton.style.backgroundColor = '#d1d5db';
                    switchThumb.style.transform = 'translateX(0.125rem)';
                    switchThumb.style.transition = 'transform 300ms cubic-bezier(0.4, 0, 0.2, 1)';
                    modeText.textContent = 'Modo Claro';
                    modeText.style.color = '#111827';
                    modeIcon.style.color = '#6b7280';
                    modeContainer.style.background = '#f3f4f6';
                    modeContainer.style.borderColor = '#e5e7eb';

                    // Actualizar icono
                    modeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />';

                    // Actualizar sidebar
                    if (sidebar) {
                        sidebar.style.background = '#ffffff';
                        sidebar.style.borderColor = '#e5e7eb';
                    }

                    // Actualizar main content
                    if (mainContent) {
                        mainContent.style.background = '#f9fafb';
                    }

                    // Restaurar body y html
                    const bodyEl = document.getElementById('body-element');
                    const htmlEl = document.getElementById('html-root');
                    if (bodyEl) {
                        bodyEl.style.background = '#f9fafb';
                    }
                    if (htmlEl) {
                        htmlEl.style.background = '#f9fafb';
                    }

                    // Restaurar todos los elementos con bg-white
                    const whiteElements = document.querySelectorAll('.bg-white, [class*="bg-white"]');
                    whiteElements.forEach(el => {
                        if (!el.closest('.bg-green-500') && !el.classList.contains('bg-green-500')) {
                            el.style.backgroundColor = '#ffffff';
                        }
                    });

                    // Restaurar tablas
                    const tableBodies = document.querySelectorAll('tbody');
                    tableBodies.forEach(tbody => {
                        tbody.style.backgroundColor = '#ffffff';
                        tbody.style.color = '#111827';
                    });

                    const tableHeaders = document.querySelectorAll('thead');
                    tableHeaders.forEach(thead => {
                        thead.style.backgroundColor = '#f9fafb';
                    });

                    // Restaurar inputs y selects
                    const inputs = document.querySelectorAll('input, select, textarea');
                    inputs.forEach(input => {
                        if (input.type !== 'checkbox' && input.type !== 'radio') {
                            input.style.backgroundColor = '#ffffff';
                            input.style.color = '#111827';
                            input.style.borderColor = '#e5e7eb';
                        }
                    });

                    // Restaurar textos en modo claro - forzar colores correctos
                    const textElements = document.querySelectorAll('h1, h2, h3, h4, h5, h6, p, span, td, th, label, .statistics-text, .statistics-number, div[style*="color"]');
                    textElements.forEach(el => {
                        // Excluir elementos dentro de botones verdes o con texto blanco
                        if (el.closest('.bg-green-500') ||
                            el.classList.contains('bg-green-500') ||
                            el.classList.contains('text-white') ||
                            el.closest('button[style*="background: #22c55e"]') ||
                            el.closest('a[style*="background: #22c55e"]')) {
                            return;
                        }

                        if (!el.closest('.bg-green-500') && !el.classList.contains('bg-green-500')) {
                            // Si tiene clase dark:text-white, en modo claro debe ser negro
                            if (el.classList.contains('dark:text-white')) {
                                el.style.color = '#111827';
                            } else {
                                // Verificar el color actual
                                const currentColor = el.style.color || '';
                                const computedColor = window.getComputedStyle(el).color;

                                // Si el color es blanco o gris claro (de modo oscuro), restaurar
                                if (currentColor.includes('#ffffff') || currentColor.includes('#e5e7eb') ||
                                    currentColor.includes('rgb(255, 255, 255)') || currentColor.includes('rgb(229, 231, 235)') ||
                                    computedColor === 'rgb(255, 255, 255)' || computedColor === 'rgb(229, 231, 235)') {

                                    // Determinar color correcto según el tipo de elemento
                                    const tagName = el.tagName.toLowerCase();
                                    const computedStyle = window.getComputedStyle(el);
                                    const fontWeight = parseInt(computedStyle.fontWeight) || 400;
                                    const fontSize = parseInt(computedStyle.fontSize) || 14;

                                    // Títulos y textos importantes: negro
                                    if (['h1', 'h2', 'h3', 'h4', 'h5', 'h6'].includes(tagName) ||
                                        fontWeight >= 600 || fontSize >= 16 ||
                                        el.classList.contains('statistics-number') ||
                                        el.classList.contains('font-bold') ||
                                        el.classList.contains('font-semibold')) {
                                        el.style.color = '#111827';
                                    } else {
                                        // Textos secundarios: gris
                                        el.style.color = '#6b7280';
                                    }
                                }
                            }
                        }
                    });

                    // Actualizar todos los textos e iconos del menú
                    const menuTexts = document.querySelectorAll('nav span, nav a:not(.bg-green-500)');
                    const menuIcons = document.querySelectorAll('nav svg:not(.bg-green-500 svg)');
                    const menuLabels = document.querySelectorAll('.text-xs.font-semibold.uppercase');
                    const verComoTecnico = document.querySelector('button:has(svg[viewBox="0 0 24 24"])');

                    menuTexts.forEach(el => {
                        if (!el.closest('.bg-green-500')) {
                            el.style.color = '#111827';
                        }
                    });

                    menuIcons.forEach(el => {
                        if (!el.closest('.bg-green-500')) {
                            el.style.color = '#111827';
                        }
                    });

                    menuLabels.forEach(el => {
                        el.style.color = '#6b7280';
                    });

                    if (verComoTecnico) {
                        const verComoTecnicoText = verComoTecnico.querySelector('span');
                        const verComoTecnicoIcon = verComoTecnico.querySelector('svg');
                        if (verComoTecnicoText) verComoTecnicoText.style.color = '#111827';
                        if (verComoTecnicoIcon) verComoTecnicoIcon.style.color = '#6b7280';
                    }
                }

                localStorage.setItem('darkMode', isDark);
                toggleButton.setAttribute('aria-checked', isDark);
            }

            // Función para actualizar fondos después de que la página cargue
            function updateBackgroundsAfterLoad() {
                setTimeout(() => {
                    const isDark = htmlRoot.classList.contains('dark');
                    if (isDark) {
                        // Actualizar body y html
                        const bodyEl = document.getElementById('body-element');
                        const htmlEl = document.getElementById('html-root');
                        if (bodyEl) {
                            bodyEl.style.background = '#0f172a';
                        }
                        if (htmlEl) {
                            htmlEl.style.background = '#0f172a';
                        }

                        // Actualizar todos los elementos con bg-white
                        const whiteElements = document.querySelectorAll('.bg-white');
                        whiteElements.forEach(el => {
                            if (!el.closest('.bg-green-500') && !el.classList.contains('bg-green-500')) {
                                el.style.backgroundColor = '#1f2937';
                            }
                        });

                        // Actualizar tablas
                        const tableBodies = document.querySelectorAll('tbody');
                        tableBodies.forEach(tbody => {
                            tbody.style.backgroundColor = '#1f2937';
                            tbody.style.color = '#e5e7eb';
                        });

                        const tableHeaders = document.querySelectorAll('thead');
                        tableHeaders.forEach(thead => {
                            thead.style.backgroundColor = '#111827';
                        });

                        // Actualizar inputs y selects
                        const inputs = document.querySelectorAll('input:not([type="checkbox"]):not([type="radio"]), select, textarea');
                        inputs.forEach(input => {
                            input.style.backgroundColor = '#1f2937';
                            input.style.color = '#e5e7eb';
                            input.style.borderColor = '#374151';
                        });
                    } else {
                        // Restaurar body y html en modo claro
                        const bodyEl = document.getElementById('body-element');
                        const htmlEl = document.getElementById('html-root');
                        if (bodyEl) {
                            bodyEl.style.background = '#f9fafb';
                        }
                        if (htmlEl) {
                            htmlEl.style.background = '#f9fafb';
                        }
                    }
                }, 100);
            }

            // Asegurar que los menús estén ocultos al cargar
            function hideAllDropdowns() {
                const notificationMenus = document.querySelectorAll('.notification-menu, #notification-menu-mobile');
                const userMenus = document.querySelectorAll('.user-menu, #user-menu-mobile');

                notificationMenus.forEach(menu => {
                    if (menu) {
                        menu.classList.remove('show');
                        menu.style.opacity = '0';
                        menu.style.visibility = 'hidden';
                        menu.style.transform = 'translateY(-10px)';
                        menu.style.pointerEvents = 'none';
                    }
                });

                userMenus.forEach(menu => {
                    if (menu) {
                        menu.classList.remove('show');
                        menu.style.opacity = '0';
                        menu.style.visibility = 'hidden';
                        menu.style.transform = 'translateY(-10px)';
                        menu.style.pointerEvents = 'none';
                    }
                });
            }

            // Inicializar tema al cargar
            const isDark = getCurrentTheme();

            // Aplicar tema después de que el DOM esté completamente cargado
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    hideAllDropdowns();
                    applyTheme(isDark);
                    updateBackgroundsAfterLoad();
                    // Asegurar nuevamente después de un pequeño delay
                    setTimeout(hideAllDropdowns, 100);
                    setTimeout(hideAllDropdowns, 300);
                });
            } else {
                // Si ya está cargado, aplicar inmediatamente
                hideAllDropdowns();
                setTimeout(function() {
                    applyTheme(isDark);
                    updateBackgroundsAfterLoad();
                    hideAllDropdowns();
                }, 0);
                setTimeout(hideAllDropdowns, 100);
                setTimeout(hideAllDropdowns, 300);
            }

            // Observar cambios en el DOM para actualizar nuevos elementos (con debounce)
            let updateTimeout;
            const observer = new MutationObserver(() => {
                clearTimeout(updateTimeout);
                updateTimeout = setTimeout(() => {
                    if (htmlRoot.classList.contains('dark')) {
                        updateBackgroundsAfterLoad();
                    }
                }, 300);
            });
            observer.observe(document.body, { childList: true, subtree: true });

            // Toggle al hacer clic
            toggleButton.addEventListener('click', function() {
                const currentIsDark = htmlRoot.classList.contains('dark');
                applyTheme(!currentIsDark);
                updateBackgroundsAfterLoad();
            });

            // Escuchar cambios en la preferencia del sistema
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
                if (localStorage.getItem('darkMode') === null) {
                    applyTheme(e.matches);
                }
            });
        })();
    </script>

    <style>
        /* Forzar texto blanco en hover del menú - sobrescribe !important */
        .group:hover span {
            color: #ffffff !important;
        }
        .group:hover svg {
            color: #ffffff !important;
        }
        /* Asegurar que el fondo oscuro se aplique en hover */
        .group:hover {
            background-color: #1f2937 !important;
        }
    </style>
</body>
</html>
