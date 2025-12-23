@php
use Illuminate\Support\Facades\DB;
// Helper para determinar si está en modo técnico
$isViewingAsTechnician = session('view_as_technician', false) && auth()->check() && auth()->user()->hasRole('super-admin');

// Compartir contador de notificaciones no leídas globalmente
if (auth()->check()) {
    try {
        $user = auth()->user();
        // Usar el método unreadNotifications() de Laravel
        $unreadCount = $user->unreadNotifications()->count();
        // Obtener notificaciones recientes (incluyendo leídas para el dropdown)
        $recentNotifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    } catch (\Exception $e) {
        // Fallback: consultar directamente la tabla si hay error
        \Log::error('Error obteniendo notificaciones: ' . $e->getMessage());
        try {
            $unreadCount = DB::table('notifications')
                ->where('notifiable_type', 'App\Models\User')
                ->where('notifiable_id', auth()->id())
                ->whereNull('read_at')
                ->count();
            $recentNotifications = collect();
        } catch (\Exception $e2) {
            $unreadCount = 0;
            $recentNotifications = collect();
        }
    }
} else {
    $unreadCount = 0;
    $recentNotifications = collect();
}

// Helper function para obtener la ruta correcta según el modo
function getTechnicianRoute($routeName, ...$params)
{
    $isViewingAsTechnician = session('view_as_technician', false) && auth()->check() && auth()->user()->hasRole('super-admin');

    if ($isViewingAsTechnician) {
        // Mapear rutas de technician a technician-view
        $routeMap = [
            'technician.service.detail' => 'admin.technician-view.service.detail',
            'technician.service.checklist' => 'admin.technician-view.service.checklist',
            'technician.service.checklist.stage' => 'admin.technician-view.service.checklist.stage',
            'technician.service.checklist.location' => 'admin.technician-view.service.checklist.location',
            'technician.service.checklist.process-location' => 'admin.technician-view.service.checklist.process-location',
            'technician.service.checklist.submit' => 'admin.technician-view.service.checklist.submit',
            'technician.service.pdf' => 'admin.technician-view.service.pdf',
            'technician.service.checklist-details' => 'admin.technician-view.service.checklist-details',
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
        
        /* Prevenir desplazamiento durante carga */
        #app {
            display: flex !important;
            flex-direction: row !important;
            width: 100% !important;
            overflow-x: hidden !important;
        }
        
        #app > div.flex-1 {
            width: 100% !important;
            margin-left: 0 !important;
            flex: 1 !important;
        }
        
        /* Optimizar carga en móvil */
        @media (max-width: 767px) {
            body {
                overflow-x: hidden !important;
            }
            
            /* Asegurar que el sidebar esté oculto por defecto en móvil */
            #sidebar {
                transform: translateX(-100%) !important;
                position: fixed !important;
            }
            
            /* Asegurar que los menús no se muestren accidentalmente */
            .notification-menu:not(.show),
            .user-menu:not(.show),
            #notification-menu-mobile:not(.show),
            #user-menu-mobile:not(.show) {
                display: none !important;
            }
        }
        
        /* Desktop: sidebar visible desde el inicio */
        @media (min-width: 768px) {
            #sidebar {
                transform: translateX(0) !important;
                position: static !important;
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
        <aside id="sidebar" class="fixed md:static flex-shrink-0 w-72 flex flex-col bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 z-50 h-full overflow-y-auto" style="transform: translateX(-100%); transition: transform 0.3s ease-in-out; padding-bottom: 300px;">
            <style>
                @media (min-width: 768px) {
                    #sidebar {
                        transform: translateX(0) !important;
                        position: static !important;
                    }
                }
                @media (max-width: 767px) {
                    #sidebar {
                        transform: translateX(-100%) !important;
                        position: fixed !important;
                        overflow-y: auto !important;
                        -webkit-overflow-scrolling: touch !important;
                        padding-bottom: 300px !important;
                    }
                }
            </style>
            <div class="flex grow flex-col gap-y-3 px-5 pb-40">
                <div class="flex h-16 shrink-0 items-center justify-center pt-6 pb-4">
                    @php
// Determinar la ruta del dashboard según el rol
$dashboardRoute = route('admin.dashboard');
if (auth()->check()) {
    if (auth()->user()->hasRole('technician') && !auth()->user()->hasRole('super-admin')) {
        // Usuario es técnico (no admin)
        $dashboardRoute = route('technician.dashboard');
    } elseif (session('view_as_technician') && auth()->user()->hasRole('super-admin')) {
        // Admin viendo como técnico
        $dashboardRoute = route('admin.technician-view.dashboard');
    } else {
        // Admin normal
        $dashboardRoute = route('admin.dashboard');
    }
}
                    @endphp
                    <a href="{{ $dashboardRoute }}" class="flex items-center justify-center">
                        <img src="https://pestcontroller.cl/wp-content/uploads/2022/07/pestcontroller-logo.png" alt="PestController Logo" class="h-14 w-auto object-contain max-w-full">
                    </a>
                </div>
                
                <!-- Separator -->
                <div class="border-t border-gray-200 my-3"></div>
                
                <!-- Quick Actions - Modo Oscuro/Claro Switch -->
                <div class="mb-3">
                    <div id="dark-mode-container" class="w-full flex items-center justify-between gap-x-3 rounded-lg py-2.5 px-3 text-xs transition-colors duration-200 bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center gap-x-2">
                            <svg id="dark-mode-icon" class="h-4 w-4 transition-colors duration-200 text-gray-900 dark:text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                            </svg>
                            <span id="dark-mode-text" class="text-xs font-medium transition-colors duration-200 text-gray-900 dark:text-white" style="font-size: 11px;">Modo Claro</span>
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
                                <button type="submit" class="w-full flex items-center gap-x-2 rounded-lg py-2 px-2.5 text-xs hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors text-gray-900 dark:text-white">
                                    <svg class="h-5 w-5 text-gray-900 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="text-xs font-medium text-gray-900 dark:text-white">Salir de Vista Técnico</span>
                                </button>
                            </form>
                        @else
                            <form action="{{ route('admin.view-as-technician') }}" method="POST" class="mb-2.5">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-x-2 rounded-lg py-2 px-2.5 text-xs hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors text-gray-900 dark:text-white">
                                    <svg class="h-5 w-5 text-gray-900 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span class="text-xs font-medium text-gray-900 dark:text-white">Ver como Técnico</span>
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
                                <div class="text-xs font-semibold uppercase mb-3 dark:text-white">Menú Principal</div>
                                <ul role="list" class="-mx-2 space-y-1.5">
                                <li>
                                    <a href="{{ route('admin.technician-view.dashboard') }}" class="group flex items-center gap-x-6 rounded-md px-3 py-3 text-sm leading-5 font-medium {{ request()->routeIs('admin.technician-view.dashboard') ? 'bg-green-500 text-white' : 'text-gray-900 dark:text-white hover:bg-gray-800 hover:text-white dark:hover:bg-gray-800' }}" >
                                        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('admin.technician-view.dashboard') ? 'text-white' : 'text-gray-900 dark:text-gray-400 group-hover:text-white' }}"  fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                                        </svg>
                                        <span class="{{ request()->routeIs('admin.technician-view.dashboard') ? 'text-white' : 'text-gray-900 dark:text-white group-hover:text-white' }}" >Dashboard</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.technician-view.services') }}" class="group flex items-center gap-x-6 rounded-md px-3 py-3 text-sm leading-5 font-medium {{ request()->routeIs('admin.technician-view.services') || request()->routeIs('admin.technician-view.service.*') ? 'bg-green-500 text-white' : 'text-gray-900 dark:text-white hover:bg-gray-800 hover:text-white dark:hover:bg-gray-800' }}" >
                                        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('admin.technician-view.services') || request()->routeIs('admin.technician-view.service.*') ? 'text-white' : 'text-gray-900 dark:text-gray-400 group-hover:text-white' }}"  fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5a2.25 2.25 0 002.25-2.25m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5a2.25 2.25 0 012.25 2.25v7.5" />
                                        </svg>
                                        <span class="{{ request()->routeIs('admin.technician-view.services') || request()->routeIs('admin.technician-view.service.*') ? 'text-white' : 'text-gray-900 dark:text-white group-hover:text-white' }}" >Mis Servicios</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.technician-view.profile') }}" class="group flex items-center gap-x-6 rounded-md px-3 py-3 text-sm leading-5 font-medium {{ request()->routeIs('admin.technician-view.profile') ? 'bg-green-500 text-white' : 'text-gray-900 dark:text-white hover:bg-gray-800 hover:text-white dark:hover:bg-gray-800' }}" >
                                        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('admin.technician-view.profile') ? 'text-white' : 'text-gray-900 dark:text-gray-400 group-hover:text-white' }}"  fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                        </svg>
                                        <span class="{{ request()->routeIs('admin.technician-view.profile') ? 'text-white' : 'text-gray-900 dark:text-white group-hover:text-white' }}" >Mi Perfil</span>
                                    </a>
                                </li>
                                </ul>
                            </li>
                        @else
                            <!-- MENÚ SEGÚN ROL -->
                            @if(auth()->user()->hasRole('super-admin'))
                                                                <!-- MENÚ COMPLETO DE ADMIN -->
                                                                <li>
                                                                    <div class="text-xs font-semibold uppercase mb-3 dark:text-white">Menú Principal</div>
                                                                    <ul role="list" class="-mx-2 space-y-1.5">
                                                                        <li>
                                                                            <a href="{{ route('admin.dashboard') }}" class="group flex items-center gap-x-6 rounded-md px-3 py-3 text-sm leading-5 font-medium {{ request()->routeIs('dashboard') || request()->routeIs('admin.dashboard') ? 'bg-green-500 text-white' : 'text-gray-900 dark:text-white hover:bg-gray-800 hover:text-white dark:hover:bg-gray-800' }}" >
                                                                                <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('dashboard') || request()->routeIs('admin.dashboard') ? 'text-white' : 'text-gray-900 dark:text-gray-400 group-hover:text-white' }}"  fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                                                                                </svg>
                                                                                <span class="{{ request()->routeIs('dashboard') || request()->routeIs('admin.dashboard') ? 'text-white' : 'text-gray-900 dark:text-white group-hover:text-white' }}" >Dashboard</span>
                                                                            </a>
                                                                        </li>
                                                                        <li>
                                                                            <a href="{{ route('admin.clients.index') ?? route('clients.index') ?? '#' }}" class="group flex items-center gap-x-6 rounded-md px-3 py-3 text-sm leading-5 font-medium {{ request()->routeIs('clients.*') || request()->routeIs('admin.clients.*') ? 'bg-green-500 text-white' : 'text-gray-900 dark:text-white hover:bg-gray-800 hover:text-white dark:hover:bg-gray-800' }}" >
                                                                                <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('clients.*') || request()->routeIs('admin.clients.*') ? 'text-white' : 'text-gray-900 dark:text-gray-400 group-hover:text-white' }}"  fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                                                                </svg>
                                                                                <span class="{{ request()->routeIs('clients.*') || request()->routeIs('admin.clients.*') ? 'text-white' : 'text-gray-900 dark:text-white group-hover:text-white' }}" >Clientes</span>
                                                                            </a>
                                                                        </li>
                                                                        <li>
                                                                            <a href="{{ route('admin.services.index') ?? route('services.index') ?? '#' }}" class="group flex items-center gap-x-6 rounded-md px-3 py-3 text-sm leading-5 font-medium {{ request()->routeIs('services.*') || request()->routeIs('admin.services.*') ? 'bg-green-500 text-white' : 'text-gray-900 dark:text-white hover:bg-gray-800 hover:text-white dark:hover:bg-gray-800' }}" >
                                                                                <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('services.*') || request()->routeIs('admin.services.*') ? 'text-white' : 'text-gray-900 dark:text-gray-400 group-hover:text-white' }}"  fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5a2.25 2.25 0 002.25-2.25m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5a2.25 2.25 0 012.25 2.25v7.5" />
                                                                                </svg>
                                                                                <span class="{{ request()->routeIs('services.*') || request()->routeIs('admin.services.*') ? 'text-white' : 'text-gray-900 dark:text-white group-hover:text-white' }}" >Servicios</span>
                                                                            </a>
                                                                        </li>
                                                                        <li>
                                                                            <a href="{{ route('admin.reports.index') ?? '#' }}" class="group flex items-center gap-x-6 rounded-md px-3 py-3 text-sm leading-5 font-medium {{ request()->routeIs('admin.reports.*') || request()->routeIs('reports.*') ? 'bg-green-500 text-white' : 'text-gray-900 dark:text-white hover:bg-gray-800 hover:text-white dark:hover:bg-gray-800' }}" >
                                                                                <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('admin.reports.*') || request()->routeIs('reports.*') ? 'text-white' : 'text-gray-900 dark:text-gray-400 group-hover:text-white' }}"  fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                                                                </svg>
                                                                                <span class="{{ request()->routeIs('admin.reports.*') || request()->routeIs('reports.*') ? 'text-white' : 'text-gray-900 dark:text-white group-hover:text-white' }}" >Reportes</span>
                                                                            </a>
                                                                        </li>
                                                                        <li>
                                                                            <a href="{{ route('admin.users.index', ['role' => 'technician']) }}" class="group flex items-center gap-x-6 rounded-md px-3 py-3 text-sm leading-5 font-medium {{ request()->routeIs('admin.users.index') && request()->get('role') === 'technician' ? 'bg-green-500 text-white' : 'text-gray-900 dark:text-white hover:bg-gray-800 hover:text-white dark:hover:bg-gray-800' }}" >
                                                                                <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('admin.users.index') && request()->get('role') === 'technician' ? 'text-white' : 'text-gray-900 dark:text-gray-400 group-hover:text-white' }}"  fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                                                                </svg>
                                                                                <span class="{{ request()->routeIs('admin.users.index') && request()->get('role') === 'technician' ? 'text-white' : 'text-gray-900 dark:text-white group-hover:text-white' }}" >Técnicos</span>
                                                                            </a>
                                                                        </li>
                                                                        <li>
                                                                            <a href="{{ route('admin.pests') ?? '#' }}" class="group flex items-center gap-x-6 rounded-md px-3 py-3 text-sm leading-5 font-medium {{ request()->routeIs('admin.pests') ? 'bg-green-500 text-white' : 'text-gray-900 dark:text-white hover:bg-gray-800 hover:text-white dark:hover:bg-gray-800' }}" >
                                                                                <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('admin.pests') ? 'text-white' : 'text-gray-900 dark:text-gray-400 group-hover:text-white' }}" fill="currentColor" viewBox="0 0 640 512">
                                                                                    <path d="M196.5 107.2c-15.1-6.9-32.9-.6-41.3 13.7l-48 81.5c-5.8 9.9-6.8 22-2.7 32.8l27.4 71.6-80.6 9.2c-46.3 5.3-80.2 44.7-80.2 91.4v4.4c0 51.2 41.5 92.7 92.7 92.7h19.6c28.8 0 56.5-10.6 77.9-29.8L231.3 410l-46.1-61.4c-9.4-12.6-15.9-27.2-18.9-42.9l-12.1-64c-2.9-15.4 .7-31.2 10.1-43.7l59.1-78.8-26.9-12.1zM552.9 416.5l69.9-64.5c21.4 19.2 49.1 29.8 77.9 29.8h19.6c51.2 0 92.7-41.5 92.7-92.7v-4.4c0-46.6-33.9-86-80.2-91.3l-80.6-9.2 27.4-71.6c4.1-10.8 3.1-22.8-2.7-32.8l-48-81.5c-8.4-14.2-26.2-20.6-41.3-13.7l-26.9 12.1 59.1 78.8c9.4 12.5 12.9 28.3 10 43.7l-12.1 64c-3 15.6-9.5 30.3-18.9 42.9l-46 61.4 69.8 64.5zM406.5 308.2L320 192.9l-86.5 115.3L320 437.3l86.5-129.1zM170.1 536.2l83.8-125.1 50.8 67.7c8.4 11.2 24.2 11.2 32.6 0l50.8-67.7 83.8 125.1c8.4 12.5 23.2 18.4 37.4 14.8l69.1-17.3c20.8-5.2 33.4-26.3 28.2-47.1l-5.1-20.5c-3.6-14.3-14-26-28-31.2l-66.1-24.8 63.9-95.5c6.3-9.4 8.9-20.8 7.1-32l-16.8-107.4c-3.4-21.7-23.8-36.5-45.4-33.1l-.3 0-31.2 4.9L320 291.2 222.1 146.6 190.9 142c-.1 0-.2 0-.3 0c-21.7-3.4-42 11.4-45.4 33.1L128.5 282.5c-1.8 11.2 .8 22.6 7.1 32L199.5 410l-66.1 24.8c-14 5.3-24.4 16.9-28 31.2l-5.1 20.5c-5.2 20.8 7.4 41.9 28.2 47.1l69.1 17.3c14.2 3.6 29-.2 37.4-14.8l-59 .1z"/>
                                                                                </svg>
                                                                                <span class="{{ request()->routeIs('admin.pests') ? 'text-white' : 'text-gray-900 dark:text-white group-hover:text-white' }}" >Plagas</span>
                                                                            </a>
                                                                        </li>
                                                                        <li>
                                                                            <a href="{{ route('admin.products.index') ?? '#' }}" class="group flex items-center gap-x-6 rounded-md px-3 py-3 text-sm leading-5 font-medium {{ request()->routeIs('admin.products.*') ? 'bg-green-500 text-white' : 'text-gray-900 dark:text-white hover:bg-gray-800 hover:text-white dark:hover:bg-gray-800' }}" >
                                                                                <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('admin.products.*') ? 'text-white' : 'text-gray-900 dark:text-gray-400 group-hover:text-white' }}"  fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                                                                </svg>
                                                                                <span class="{{ request()->routeIs('admin.products.*') ? 'text-white' : 'text-gray-900 dark:text-white group-hover:text-white' }}" >Productos</span>
                                                                            </a>
                                                                        </li>
                                                                    </ul>
                                                                </li>
                            @else
                                <!-- MENÚ LIMITADO PARA TÉCNICOS -->
                                <li>
                                    <div class="text-xs font-semibold uppercase mb-3 dark:text-white">Menú Principal</div>
                                    <ul role="list" class="-mx-2 space-y-1.5">
                                        <li>
                                            <a href="{{ route('technician.dashboard') }}" class="group flex items-center gap-x-6 rounded-md px-3 py-3 text-sm leading-5 font-medium {{ request()->routeIs('technician.dashboard') ? 'bg-green-500 text-white' : 'text-gray-900 dark:text-white hover:bg-gray-800 hover:text-white dark:hover:bg-gray-800' }}" >
                                                <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('technician.dashboard') ? 'text-white' : 'text-gray-900 dark:text-gray-400 group-hover:text-white' }}"  fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                                                </svg>
                                                <span class="{{ request()->routeIs('technician.dashboard') ? 'text-white' : 'text-gray-900 dark:text-white group-hover:text-white' }}" >Dashboard</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('technician.services') }}" class="group flex items-center gap-x-6 rounded-md px-3 py-3 text-sm leading-5 font-medium {{ request()->routeIs('technician.services*') ? 'bg-green-500 text-white' : 'text-gray-900 dark:text-white hover:bg-gray-800 hover:text-white dark:hover:bg-gray-800' }}" >
                                                <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('technician.services*') ? 'text-white' : 'text-gray-900 dark:text-gray-400 group-hover:text-white' }}"  fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5a2.25 2.25 0 002.25-2.25m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5a2.25 2.25 0 012.25 2.25v7.5" />
                                                </svg>
                                                <span class="{{ request()->routeIs('technician.services*') ? 'text-white' : 'text-gray-900 dark:text-white group-hover:text-white' }}" >Mis Servicios</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('technician.profile') }}" class="group flex items-center gap-x-6 rounded-md px-3 py-3 text-sm leading-5 font-medium {{ request()->routeIs('technician.profile') ? 'bg-green-500 text-white' : 'text-gray-900 dark:text-white hover:bg-gray-800 hover:text-white dark:hover:bg-gray-800' }}" >
                                                <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('technician.profile') ? 'text-white' : 'text-gray-900 dark:text-gray-400 group-hover:text-white' }}"  fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                                </svg>
                                                <span class="{{ request()->routeIs('technician.profile') ? 'text-white' : 'text-gray-900 dark:text-white group-hover:text-white' }}" >Mi Perfil</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            @endif
                        @endif
                        
                        @if(auth()->user()->hasRole('super-admin') && !$isViewingAsTechnician)
                        <li>
                            <div class="text-xs font-semibold uppercase mb-3 dark:text-white">Administración</div>
                            <ul role="list" class="-mx-2 space-y-1.5">
                                <li>
                                    <a href="{{ route('admin.users.index') }}" class="group flex items-center gap-x-6 rounded-md px-3 py-3 text-sm leading-5 font-medium {{ request()->routeIs('admin.users.*') ? 'bg-green-500 text-white' : 'text-gray-900 dark:text-white hover:bg-gray-800 hover:text-white dark:hover:bg-gray-800' }}">
                                        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('admin.users.*') ? 'text-white' : 'text-gray-900 dark:text-gray-400 group-hover:text-white' }}"  fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                        </svg>
                                        <span class="{{ request()->routeIs('admin.users.*') ? 'text-white' : 'text-gray-900 dark:text-white group-hover:text-white' }}" >Usuarios</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.notification-center') ?? '#' }}" class="group flex items-center gap-x-6 rounded-md px-3 py-3 text-sm leading-5 font-medium {{ request()->routeIs('admin.notification-center') ? 'bg-green-500 text-white' : 'text-gray-900 dark:text-white hover:bg-gray-800 hover:text-white dark:hover:bg-gray-800' }}" >
                                        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('admin.notification-center') ? 'text-white' : 'text-gray-900 dark:text-gray-400 group-hover:text-white' }}"  fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                                        </svg>
                                        <span class="{{ request()->routeIs('admin.notification-center') ? 'text-white' : 'text-gray-900 dark:text-white group-hover:text-white' }}" >Notificaciones</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ Route::has('admin.settings') ? route('admin.settings') : '#' }}" class="group flex items-center gap-x-6 rounded-md px-3 py-3 text-sm leading-5 font-medium {{ request()->routeIs('admin.settings.*') || request()->routeIs('admin.settings') ? 'bg-green-500 text-white' : 'text-gray-900 dark:text-white hover:bg-gray-800 hover:text-white dark:hover:bg-gray-800' }}" >
                                        <svg class="h-5 w-5 shrink-0 {{ (request()->routeIs('admin.settings.*') || request()->routeIs('admin.settings')) ? 'text-white' : 'text-gray-900 dark:text-gray-400 group-hover:text-white' }}"  fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span class="{{ (request()->routeIs('admin.settings.*') || request()->routeIs('admin.settings')) ? 'text-white' : 'text-gray-900 dark:text-white group-hover:text-white' }}" >Configuraciones</span>
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

                <!-- Logout -->
                <div class="mt-auto pt-4 border-t border-gray-200">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="group flex w-full items-center gap-x-6 rounded-md px-3 py-3 text-sm leading-5 font-medium text-gray-900 dark:text-white hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-900/20 dark:hover:text-red-400 transition-colors">
                            <svg class="h-5 w-5 shrink-0 text-gray-900 dark:text-gray-400 group-hover:text-red-600 dark:group-hover:text-red-400"  fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                            </svg>
                            <span class="text-gray-900 dark:text-white group-hover:text-red-600 dark:group-hover:text-red-400" >Cerrar Sesión</span>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main content -->
        <div class="flex-1 flex flex-col min-w-0 overflow-x-hidden" style="margin-left: 0;">
            <style>
                @media (min-width: 768px) {
                    #app > div.flex-1 {
                        margin-left: 0 !important;
                    }
                }
            </style>

            <!-- Page content -->
            <main class="p-3 md:p-3 flex-1">
                <!-- Banner de advertencia cuando está en modo técnico -->
                @if(session('view_as_technician') && auth()->check() && auth()->user()->hasRole('super-admin'))
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-3 sm:p-4 mx-3 sm:mx-4 md:mx-6 lg:mx-8 mb-3 sm:mb-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <svg class="h-5 w-5 text-yellow-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                <div>
                                    <p class="text-sm font-medium ">
                                        Estás viendo el sistema como <strong>Técnico</strong>. Los cambios que realices se aplicarán como si fueras un técnico.
                                    </p>
                                </div>
                            </div>
                            <form action="{{ route('admin.stop-viewing-as-technician') }}" method="POST" class="ml-4">
                                @csrf
                                <button type="submit" class="text-sm font-medium underline">
                                    Salir de Vista Técnico
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
                
                <div class="px-3 sm:px-4 md:px-6 lg:px-8 max-w-full">
                    @if(session('success'))
                        <div class="mb-4 rounded-md p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium">{{ session('success') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-4 rounded-md bg-red-50 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
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
                        max-height: 100vh !important;
                        overflow-y: auto !important;
                        -webkit-overflow-scrolling: touch !important;
                        padding-bottom: 300px !important;
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
                    max-height: 100vh !important;
                    overflow-y: auto !important;
                    -webkit-overflow-scrolling: touch !important;
                    padding-bottom: 300px !important;
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
                
                // Cambiar iconos (layout, dashboard y páginas)
                const menuIcon = document.getElementById('menu-icon');
                const closeIcon = document.getElementById('close-icon');
                const dashboardMenuIcon = document.getElementById('dashboard-menu-icon');
                const dashboardCloseIcon = document.getElementById('dashboard-close-icon');
                const pageMenuIcon = document.getElementById('page-menu-icon');
                const pageCloseIcon = document.getElementById('page-close-icon');
                if (menuIcon) menuIcon.classList.add('hidden');
                if (closeIcon) closeIcon.classList.remove('hidden');
                if (dashboardMenuIcon) dashboardMenuIcon.classList.add('hidden');
                if (dashboardCloseIcon) dashboardCloseIcon.classList.remove('hidden');
                if (pageMenuIcon) pageMenuIcon.classList.add('hidden');
                if (pageCloseIcon) pageCloseIcon.classList.remove('hidden');
                
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
                // Cambiar iconos (layout, dashboard y páginas)
                const menuIcon = document.getElementById('menu-icon');
                const closeIcon = document.getElementById('close-icon');
                const dashboardMenuIcon = document.getElementById('dashboard-menu-icon');
                const dashboardCloseIcon = document.getElementById('dashboard-close-icon');
                const pageMenuIcon = document.getElementById('page-menu-icon');
                const pageCloseIcon = document.getElementById('page-close-icon');
                if (menuIcon) menuIcon.classList.remove('hidden');
                if (closeIcon) closeIcon.classList.add('hidden');
                if (dashboardMenuIcon) dashboardMenuIcon.classList.remove('hidden');
                if (dashboardCloseIcon) dashboardCloseIcon.classList.add('hidden');
                if (pageMenuIcon) pageMenuIcon.classList.remove('hidden');
                if (pageCloseIcon) pageCloseIcon.classList.add('hidden');
            }
            
            if (mobileOverlay) {
                mobileOverlay.addEventListener('click', function() {
                    closeMobileMenu();
                });
            }
            
            // Cerrar menú al hacer clic fuera del sidebar (solo en móvil)
            document.addEventListener('click', function(event) {
                // Solo en móvil
                if (window.innerWidth >= 768) return;
                
                // Verificar si el menú está abierto
                if (!sidebar || sidebar.classList.contains('-translate-x-full')) return;
                
                // Verificar si el clic fue fuera del sidebar
                if (!sidebar.contains(event.target)) {
                    // Verificar que no sea un botón de menú
                    const menuButtons = document.querySelectorAll('[onclick*="openMobileMenu"]');
                    let isMenuButton = false;
                    menuButtons.forEach(button => {
                        if (button.contains(event.target)) {
                            isMenuButton = true;
                        }
                    });
                    
                    if (!isMenuButton) {
                        closeMobileMenu();
                    }
                }
            });
            
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
                        sidebar.style.transform = 'translateX(0)';
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
                        sidebar.style.transform = 'translateX(-100%)';
                        sidebar.style.position = 'fixed';
                        sidebar.style.zIndex = '9999';
                        sidebar.style.left = '0';
                        sidebar.style.top = '0';
                    }
                }
            }
            
            // Ejecutar inmediatamente antes de que se renderice
            if (document.readyState === 'loading') {
                ensureDesktopSidebar();
            }
            
            // Ejecutar al cargar y al redimensionar
            document.addEventListener('DOMContentLoaded', ensureDesktopSidebar);
            ensureDesktopSidebar();
            
            // Cerrar menú al redimensionar a desktop
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 768) {
                    closeMobileMenu();
                    ensureDesktopSidebar();
                }
            });
            
            // Funciones helper para obtener estilos correctos del sidebar
            window.getSidebarOpenStyles = function() {
                return {
                    css: `
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
                            max-height: 100vh !important;
                            overflow-y: auto !important;
                            -webkit-overflow-scrolling: touch !important;
                        }
                    `,
                    inline: `
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
                        max-height: 100vh !important;
                        overflow-y: auto !important;
                        -webkit-overflow-scrolling: touch !important;
                        padding-bottom: 300px !important;
                    `
                };
            };
            
            // Exponer funciones globalmente para uso en páginas
            window.openMobileMenu = openMobileMenu;
            window.closeMobileMenu = closeMobileMenu;
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
                        }
                    });
                    
                    // Actualizar tablas
                    const tableBodies = document.querySelectorAll('tbody');
                    tableBodies.forEach(tbody => {
                        tbody.style.backgroundColor = '#1f2937';
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
                            input.style.borderColor = '#374151';
                        }
                    });
                    
                    // Actualizar botones de menú móvil en dark mode
                    const mobileMenuButtons = document.querySelectorAll('#page-mobile-menu-button, #mobile-menu-button, #dashboard-mobile-menu-button');
                    mobileMenuButtons.forEach(btn => {
                        btn.style.backgroundColor = '#1f2937';
                        btn.style.borderColor = '#374151';
                        // Hacer las líneas del SVG blancas
                        const svg = btn.querySelector('svg');
                        if (svg) {
                            svg.style.color = '#ffffff';
                            svg.setAttribute('stroke', '#ffffff');
                        }
                    });
                } else {
                    // Aplicar modo claro
                    htmlRoot.classList.remove('dark');
                    
                    // Actualizar switch
                    toggleButton.style.backgroundColor = '#d1d5db';
                    switchThumb.style.transform = 'translateX(0.125rem)';
                    switchThumb.style.transition = 'transform 300ms cubic-bezier(0.4, 0, 0.2, 1)';
                    modeText.textContent = 'Modo Claro';
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
                            input.style.borderColor = '#e5e7eb';
                        }
                    });
                    
                    // Restaurar botones de menú móvil en modo claro
                    const mobileMenuButtons = document.querySelectorAll('#page-mobile-menu-button, #mobile-menu-button, #dashboard-mobile-menu-button');
                    mobileMenuButtons.forEach(btn => {
                        btn.style.backgroundColor = '#ffffff';
                        btn.style.borderColor = '#d1d5db';
                        // Restaurar las líneas del SVG a color oscuro
                        const svg = btn.querySelector('svg');
                        if (svg) {
                            svg.style.color = '#374151';
                            svg.setAttribute('stroke', '#374151');
                        }
                    });
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
                        });
                        
                        const tableHeaders = document.querySelectorAll('thead');
                        tableHeaders.forEach(thead => {
                            thead.style.backgroundColor = '#111827';
                        });
                        
                        // Actualizar inputs y selects
                        const inputs = document.querySelectorAll('input:not([type="checkbox"]):not([type="radio"]), select, textarea');
                        inputs.forEach(input => {
                            input.style.backgroundColor = '#1f2937';
                            input.style.borderColor = '#374151';
                        });
                        
                        // Actualizar botones de menú móvil en dark mode
                        const mobileMenuButtons = document.querySelectorAll('#page-mobile-menu-button, #mobile-menu-button, #dashboard-mobile-menu-button');
                        mobileMenuButtons.forEach(btn => {
                            btn.style.backgroundColor = '#1f2937';
                            btn.style.borderColor = '#374151';
                            const svg = btn.querySelector('svg');
                            if (svg) {
                                svg.style.color = '#ffffff';
                                svg.setAttribute('stroke', '#ffffff');
                            }
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
                        
                        // Restaurar botones de menú móvil en modo claro
                        const mobileMenuButtons = document.querySelectorAll('#page-mobile-menu-button, #mobile-menu-button, #dashboard-mobile-menu-button');
                        mobileMenuButtons.forEach(btn => {
                            btn.style.backgroundColor = '#ffffff';
                            btn.style.borderColor = '#d1d5db';
                            const svg = btn.querySelector('svg');
                            if (svg) {
                                svg.style.color = '#374151';
                                svg.setAttribute('stroke', '#374151');
                            }
                        });
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