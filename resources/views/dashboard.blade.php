@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
<style>
    /* Asegurar que los iconos tengan el tamaño correcto desde el inicio - CRÍTICO */
    #notification-button,
    #user-menu-button {
        width: 40px !important;
        height: 40px !important;
        min-width: 40px !important;
        min-height: 40px !important;
        max-width: 40px !important;
        max-height: 40px !important;
        padding: 8px !important;
        box-sizing: border-box !important;
        flex-shrink: 0 !important;
        overflow: visible !important;
    }
    
    #notification-button {
        padding: 8px !important;
    }
    
    #user-menu-button {
        padding: 0 !important;
    }
    
    #notification-button svg {
        width: 24px !important;
        height: 24px !important;
        min-width: 24px !important;
        min-height: 24px !important;
        max-width: 24px !important;
        max-height: 24px !important;
        display: block !important;
        flex-shrink: 0 !important;
    }
    
    #user-menu-button > div {
        width: 40px !important;
        height: 40px !important;
        min-width: 40px !important;
        min-height: 40px !important;
        max-width: 40px !important;
        max-height: 40px !important;
        box-sizing: border-box !important;
    }
    
    #user-menu-button > div > span {
        font-size: 14px !important;
        line-height: 1 !important;
    }
    
    /* Prevenir cualquier tamaño grande que pueda venir de otros estilos */
    .notification-dropdown button,
    .user-menu-dropdown button {
        width: 40px !important;
        height: 40px !important;
        min-width: 40px !important;
        min-height: 40px !important;
        max-width: 40px !important;
        max-height: 40px !important;
    }
    
    /* Iconos dentro del menú de usuario - CRÍTICO */
    .user-menu-icon {
        width: 20px !important;
        height: 20px !important;
        min-width: 20px !important;
        min-height: 20px !important;
        max-width: 20px !important;
        max-height: 20px !important;
        flex-shrink: 0 !important;
    }
    
    /* Avatar del menú de usuario */
    .user-menu-avatar {
        width: 48px !important;
        height: 48px !important;
        min-width: 48px !important;
        min-height: 48px !important;
        max-width: 48px !important;
        max-height: 48px !important;
        object-fit: cover !important;
        border-radius: 50% !important;
    }
    
    /* Todos los SVG dentro del menú de usuario */
    .user-menu svg {
        width: 20px !important;
        height: 20px !important;
        min-width: 20px !important;
        min-height: 20px !important;
        max-width: 20px !important;
        max-height: 20px !important;
    }
    
    /* Asegurar que el layout del título y buscador funcione correctamente */
    @media (min-width: 768px) {
        .dashboard-header-row {
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            justify-content: space-between !important;
            width: 100% !important;
            max-width: 100% !important;
        }
        
        .dashboard-title-container {
            flex-shrink: 0 !important;
            flex-grow: 0 !important;
        }
        
        .dashboard-search-container {
            flex-shrink: 0 !important;
            flex-grow: 0 !important;
        }
        
        /* Asegurar que el input del buscador tenga un ancho fijo */
        #global-search-input-desktop {
            width: 224px !important;
            min-width: 224px !important;
            max-width: 224px !important;
            padding-left: 35
            px !important;
        }
    }
</style>
@endpush

@section('content')
<!-- Script crítico para prevenir FOUC - se ejecuta ANTES del contenido -->
<script>
    (function() {
        // Aplicar estilos críticos inmediatamente, incluso antes de DOMContentLoaded
        function forceIconSizes() {
            // Aplicar múltiples veces para asegurar
            for (let i = 0; i < 10; i++) {
                setTimeout(function() {
                    const nb = document.getElementById('notification-button');
                    const umb = document.getElementById('user-menu-button');
                    
                    if (nb) {
                        nb.style.setProperty('width', '40px', 'important');
                        nb.style.setProperty('height', '40px', 'important');
                        nb.style.setProperty('min-width', '40px', 'important');
                        nb.style.setProperty('min-height', '40px', 'important');
                        nb.style.setProperty('max-width', '40px', 'important');
                        nb.style.setProperty('max-height', '40px', 'important');
                        nb.style.setProperty('padding', '8px', 'important');
                        nb.style.setProperty('box-sizing', 'border-box', 'important');
                        
                        const svg = nb.querySelector('svg');
                        if (svg) {
                            svg.style.setProperty('width', '24px', 'important');
                            svg.style.setProperty('height', '24px', 'important');
                            svg.style.setProperty('min-width', '24px', 'important');
                            svg.style.setProperty('min-height', '24px', 'important');
                            svg.style.setProperty('max-width', '24px', 'important');
                            svg.style.setProperty('max-height', '24px', 'important');
                        }
                    }
                    
                    if (umb) {
                        umb.style.setProperty('width', '40px', 'important');
                        umb.style.setProperty('height', '40px', 'important');
                        umb.style.setProperty('min-width', '40px', 'important');
                        umb.style.setProperty('min-height', '40px', 'important');
                        umb.style.setProperty('max-width', '40px', 'important');
                        umb.style.setProperty('max-height', '40px', 'important');
                        umb.style.setProperty('padding', '0', 'important');
                        umb.style.setProperty('box-sizing', 'border-box', 'important');
                        
                        const div = umb.querySelector('div');
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
                    
                    // Corregir iconos dentro del menú de usuario
                    const userMenuIcons = document.querySelectorAll('.user-menu-icon');
                    userMenuIcons.forEach(function(icon) {
                        icon.style.setProperty('width', '20px', 'important');
                        icon.style.setProperty('height', '20px', 'important');
                        icon.style.setProperty('min-width', '20px', 'important');
                        icon.style.setProperty('min-height', '20px', 'important');
                        icon.style.setProperty('max-width', '20px', 'important');
                        icon.style.setProperty('max-height', '20px', 'important');
                    });
                    
                    // Corregir avatar del menú de usuario
                    const userMenuAvatar = document.querySelector('.user-menu-avatar');
                    if (userMenuAvatar) {
                        userMenuAvatar.style.setProperty('width', '48px', 'important');
                        userMenuAvatar.style.setProperty('height', '48px', 'important');
                        userMenuAvatar.style.setProperty('min-width', '48px', 'important');
                        userMenuAvatar.style.setProperty('min-height', '48px', 'important');
                        userMenuAvatar.style.setProperty('max-width', '48px', 'important');
                        userMenuAvatar.style.setProperty('max-height', '48px', 'important');
                    }
                }, i * 10);
            }
        }
        
        // Ejecutar inmediatamente
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', forceIconSizes);
        } else {
            forceIconSizes();
        }
        
        // También usar MutationObserver (solo si document.body existe)
        if (typeof MutationObserver !== 'undefined' && document.body) {
            try {
                const observer = new MutationObserver(function() {
                    forceIconSizes();
                });
                observer.observe(document.body, { childList: true, subtree: true });
            } catch (e) {
                console.warn('MutationObserver error:', e);
            }
        }
    })();
</script>
<script>
    // Ocultar hamburguesa del layout en dashboard
    document.addEventListener('DOMContentLoaded', function() {
        const mainMenuButton = document.getElementById('mobile-menu-button');
        if (mainMenuButton) {
            mainMenuButton.style.display = 'none';
        }
        
        // Asegurar que los iconos tengan el tamaño correcto inmediatamente
        const notificationButton = document.getElementById('notification-button');
        const userMenuButton = document.getElementById('user-menu-button');
        
        if (notificationButton) {
            notificationButton.style.width = '40px';
            notificationButton.style.height = '40px';
            notificationButton.style.minWidth = '40px';
            notificationButton.style.minHeight = '40px';
            notificationButton.style.maxWidth = '40px';
            notificationButton.style.maxHeight = '40px';
            notificationButton.style.padding = '8px';
            
            const notificationSvg = notificationButton.querySelector('svg');
            if (notificationSvg) {
                notificationSvg.style.width = '24px';
                notificationSvg.style.height = '24px';
                notificationSvg.style.minWidth = '24px';
                notificationSvg.style.minHeight = '24px';
                notificationSvg.style.maxWidth = '24px';
                notificationSvg.style.maxHeight = '24px';
            }
        }
        
        if (userMenuButton) {
            userMenuButton.style.width = '40px';
            userMenuButton.style.height = '40px';
            userMenuButton.style.minWidth = '40px';
            userMenuButton.style.minHeight = '40px';
            userMenuButton.style.maxWidth = '40px';
            userMenuButton.style.maxHeight = '40px';
            userMenuButton.style.padding = '0';
            
            const userMenuDiv = userMenuButton.querySelector('div');
            if (userMenuDiv) {
                userMenuDiv.style.width = '40px';
                userMenuDiv.style.height = '40px';
                userMenuDiv.style.minWidth = '40px';
                userMenuDiv.style.minHeight = '40px';
                userMenuDiv.style.maxWidth = '40px';
                userMenuDiv.style.maxHeight = '40px';
            }
        }
    });
</script>
<div class="space-y-4 sm:space-y-6 pt-3 md:pt-0">
    <!-- Header con hamburguesa y título -->
    <div class="mb-4 sm:mb-6">
        <!-- Primera fila: Hamburguesa + Título (móvil) -->
        <div class="flex items-center gap-3 mb-4 md:hidden">
            <!-- Hamburguesa (solo móvil) -->
            <button id="dashboard-mobile-menu-button" class="flex-shrink-0 p-2 rounded-lg bg-white border border-gray-300 shadow-md hover:bg-gray-50 transition-colors">
                <svg id="dashboard-menu-icon" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="text-gray-900 dark:text-white">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
                <svg id="dashboard-close-icon" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="text-gray-900 dark:text-white">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            
            <!-- Título -->
            <div class="flex-1">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white" class="font-bold">
                    Dashboard
                </h2>
            </div>

            <!-- Iconos Header Móvil -->
            <div class="flex items-center gap-4">
                <!-- Notificaciones -->
                <a href="{{ route('admin.notification-center') ?? '#' }}" class="text-gray-500 hover:text-gray-700 relative">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                    </svg>
                    @if(isset($unreadCount) && $unreadCount > 0)
                    <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-500 ring-2 ring-white transform translate-x-1/4 -translate-y-1/4"></span>
                    @endif
                </a>

                <a href="{{ Route::has('admin.profile') ? route('admin.profile') : (Route::has('profile') ? route('profile') : '#') }}" class="flex-shrink-0">  
                    <div class="h-10 w-10 rounded-full bg-green-600 flex items-center justify-center shadow-sm flex-shrink-0">                                  
                        <span class="text-white font-medium text-base">{{ substr(auth()->user()->name ?? 'U', 0, 1) }}</span>                                   
                    </div>
                </a>
            </div>
        </div>
        
        <!-- Segunda fila: Título completo (desktop) -->
        <div class="hidden md:flex md:items-center md:justify-between gap-4">
            <div class="min-w-0 flex-1">
                <h2 class="text-2xl sm:text-3xl font-bold leading-7 text-gray-900 sm:truncate sm:tracking-tight text-gray-900 dark:text-white" class="font-bold">
                    Dashboard
                </h2>
                <p class="mt-1 text-xs sm:text-sm dark:text-white" >
                    {{ now()->locale('es')->isoFormat('dddd, D [de] MMMM') }}
                </p>
            </div>
            
            <!-- Buscador al lado derecho del título -->
            <div class="relative global-search-container flex-shrink-0">
                <div class="relative">
                    <svg class="absolute" style="left: 10px; top: 50%; transform: translateY(-50%); width: 18px; height: 18px; color: #9ca3af; pointer-events: none; z-index: 1;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input 
                        type="text" 
                        id="global-search-input-desktop" 
                        placeholder="Buscar servicios, clientes..." 
                        class="w-56 pr-3 py-2 sm:py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all text-sm dark:text-white"
                       
                        autocomplete="off"
                    />
                </div>
                
                <!-- Search Results Dropdown -->
                <div id="search-results-desktop" class="absolute top-full left-0 right-0 mt-2 bg-white border border-gray-200 rounded-lg shadow-lg z-50 max-h-96 overflow-y-auto hidden">
                    <div id="search-results-content-desktop" class="p-2">
                        <!-- Results will be inserted here -->
                    </div>
                </div>
            </div>
            
            <!-- Iconos de notificaciones y usuario (desktop) -->
            <div class="flex items-center gap-x-2 sm:gap-x-4 flex-shrink-0">
                <!-- Notifications -->
                <div class="relative notification-dropdown" id="notification-dropdown">
                    <button type="button" class="flex items-center justify-center text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 relative" title="Notificaciones" id="notification-button">
                        <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                        </svg>
                        @if(isset($unreadCount) && $unreadCount > 0)
                        <span class="absolute text-white text-xs rounded-full flex items-center justify-center font-semibold">
                            {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                        </span>
                        @endif
                    </button>
                    
                    <!-- Dropdown Menu -->
                    <div class="notification-menu" id="notification-menu">
                        <div class="notification-menu-header">
                            <h3 class="text-gray-900 dark:text-white font-semibold text-base">Notificaciones</h3>
                            <a href="{{ route('admin.notification-center') ?? '#' }}" class="text-green-500 text-sm font-medium no-underline">Ver todas</a>
                        </div>
                        <div class="notification-menu-content">
                            @if(isset($recentNotifications) && $recentNotifications->count() > 0)
                                @foreach($recentNotifications->take(8) as $notification)
                                    @php
                                        $data = is_array($notification->data) ? $notification->data : json_decode($notification->data, true);
                                        $title = $data['title'] ?? 'Notificación';
                                        $message = $data['message'] ?? '';
                                        $type = $data['type'] ?? 'info';
                                        $isRead = !is_null($notification->read_at);
                                    @endphp
                                    <div class="notification-item {{ !$isRead ? 'unread' : '' }}" data-notification-id="{{ $notification->id }}">
                                        <div class="notification-item-content">
                                            <div class="notification-item-header">
                                                <h4 class="text-gray-900 dark:text-white font-semibold text-sm mb-1">{{ $title }}</h4>
                                                <span class="notification-time">{{ $notification->created_at->diffForHumans() }}</span>
                                            </div>
                                            <p class="text-gray-600 dark:text-gray-300 text-sm">{{ Str::limit($message, 80) }}</p>
                                        </div>
                                        @if(!$isRead)
                                        <div class="notification-dot"></div>
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                <div class="notification-empty">
                                    <p class="text-gray-600 dark:text-gray-300 text-sm text-center p-5">No hay notificaciones</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Profile dropdown -->
                <div class="relative user-menu-dropdown" id="user-menu-dropdown">
                    <button type="button" class="flex items-center justify-center hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition-colors" id="user-menu-button" title="Menú de usuario">
                        <div class="bg-green-600 rounded-full flex items-center justify-center">
                            <span class="text-white font-medium">{{ substr(auth()->user()->name ?? 'U', 0, 1) }}</span>
                        </div>
                    </button>
                    
                    <!-- User Menu -->
                    <div class="user-menu" id="user-menu">
                        <div class="user-menu-header">
                            <div class="user-menu-profile">
                                <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name ?? 'Usuario') . '&background=22c55e&color=fff' }}" alt="{{ auth()->user()->name }}" class="user-menu-avatar">
                                <div class="user-menu-info">
                                    <div class="user-menu-name">{{ auth()->user()->name ?? 'Usuario' }}</div>
                                    <div class="user-menu-email">{{ auth()->user()->email ?? '' }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="user-menu-content">
                            <a href="{{ Route::has('admin.profile') ? route('admin.profile') : (Route::has('profile') ? route('profile') : '#') }}" class="user-menu-item">
                                <svg class="user-menu-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                                <span>Mi Perfil</span>
                            </a>
                            <a href="{{ Route::has('admin.settings') ? route('admin.settings') : (Route::has('settings') ? route('settings') : '#') }}" class="user-menu-item">
                                <svg class="user-menu-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>Configuración</span>
                            </a>
                            <div class="user-menu-divider"></div>
                            <form method="POST" action="{{ route('logout') }}" class="user-menu-form">
                                @csrf
                                <button type="submit" class="user-menu-item user-menu-item-danger">
                                    <svg class="user-menu-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
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
        
        <!-- Fila móvil: Buscador (debajo del título) -->
        <div class="md:hidden flex flex-col gap-4 mb-4">
            <!-- Buscador móvil (debajo del título) -->
            <div class="relative global-search-container w-full">
                <div class="relative">
                    <svg class="absolute" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input 
                        type="text" 
                        id="global-search-input-mobile" 
                        placeholder="Buscar servicios, clientes..." 
                        class="w-full pr-3 py-2 sm:py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all text-sm sm:text-base"
                       
                        autocomplete="off"
                    />
                </div>
                
                <!-- Search Results Dropdown -->
                <div id="search-results-mobile" class="absolute top-full left-0 right-0 mt-2 bg-white border border-gray-200 rounded-lg shadow-lg z-50 max-h-96 overflow-y-auto hidden">
                    <div id="search-results-content-mobile" class="p-2">
                        <!-- Results will be inserted here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <style>
                .notification-dropdown {
                    position: relative;
                }
                
                /* Área invisible de transición para facilitar el movimiento del mouse */
                .notification-dropdown::before {
                    content: '';
                    position: absolute;
                    top: 100%;
                    right: 0;
                    left: -50px;
                    height: 12px;
                    z-index: 999;
                }
                
                .notification-menu {
                    position: absolute;
                    top: calc(100% + 12px);
                    right: 0;
                    width: 380px;
                    max-width: calc(100vw - 2rem);
                    background: white;
                    border-radius: 12px;
                    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15), 0 4px 6px rgba(0, 0, 0, 0.1);
                    border: 1px solid #e5e7eb;
                    z-index: 1000;
                    overflow: hidden;
                    opacity: 0;
                    visibility: hidden;
                    transform: translateY(-10px);
                    transition: opacity 0.2s ease, visibility 0.2s ease, transform 0.2s ease;
                }
                
                @media (max-width: 640px) {
                    .notification-menu {
                        width: calc(100vw - 1rem);
                        right: 0.5rem;
                    }
                }
                
                .notification-menu.show {
                    opacity: 1;
                    visibility: visible;
                    transform: translateY(0);
                }
                
                .notification-menu-header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: 16px 20px;
                    border-bottom: 1px solid #e5e7eb;
                    background: #f9fafb;
                }
                
                .notification-menu-content {
                    max-height: 400px;
                    overflow-y: auto;
                }
                
                .notification-item {
                    display: flex;
                    align-items: flex-start;
                    padding: 16px 20px;
                    border-bottom: 1px solid #f3f4f6;
                    transition: background-color 0.2s ease;
                    position: relative;
                    cursor: pointer;
                }
                
                .notification-item:hover {
                    background: #f9fafb;
                }
                
                .notification-item.unread {
                    background: #f0fdf4;
                }
                
                .notification-item.unread:hover {
                    background: #dcfce7;
                }
                
                .notification-item-content {
                    flex: 1;
                    min-width: 0;
                }
                
                .notification-item-header {
                    display: flex;
                    justify-content: space-between;
                    align-items: flex-start;
                    margin-bottom: 4px;
                }
                
                .notification-time {
                    color: #9ca3af;
                    font-size: 12px;
                    white-space: nowrap;
                    margin-left: 12px;
                }
                
                .notification-dot {
                    width: 8px;
                    height: 8px;
                    background: #22c55e;
                    border-radius: 50%;
                    margin-left: 12px;
                    margin-top: 6px;
                    flex-shrink: 0;
                }
                
                .notification-empty {
                    padding: 40px 20px;
                    text-align: center;
                }
                
                /* Scrollbar styling */
                .notification-menu-content::-webkit-scrollbar {
                    width: 6px;
                }
                
                .notification-menu-content::-webkit-scrollbar-track {
                    background: #f1f1f1;
                }
                
                .notification-menu-content::-webkit-scrollbar-thumb {
                    background: #cbd5e1;
                    border-radius: 3px;
                }
                
                .notification-menu-content::-webkit-scrollbar-thumb:hover {
                    background: #94a3b8;
                }
                
                /* Estilos del menú de usuario */
                .user-menu-dropdown {
                    position: relative;
                }
                
                /* Área invisible de transición para facilitar el movimiento del mouse */
                .user-menu-dropdown::before {
                    content: '';
                    position: absolute;
                    top: 100%;
                    right: 0;
                    left: -50px;
                    height: 12px;
                    z-index: 999;
                }
                
                .user-menu {
                    position: absolute !important;
                    top: calc(100% + 12px) !important;
                    right: 0 !important;
                    width: 280px !important;
                    max-width: calc(100vw - 2rem) !important;
                    background: white !important;
                    border-radius: 12px !important;
                    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15), 0 4px 6px rgba(0, 0, 0, 0.1) !important;
                    border: 1px solid #e5e7eb !important;
                    z-index: 1000 !important;
                    overflow: hidden !important;
                    opacity: 0 !important;
                    visibility: hidden !important;
                    transform: translateY(-10px) !important;
                    transition: opacity 0.2s ease, visibility 0.2s ease, transform 0.2s ease !important;
                }
                
                @media (max-width: 640px) {
                    .user-menu {
                        width: calc(100vw - 1rem) !important;
                        right: 0.5rem !important;
                    }
                }
                
                .user-menu.show {
                    opacity: 1 !important;
                    visibility: visible !important;
                    transform: translateY(0) !important;
                }
                
                .user-menu-header {
                    padding: 20px !important;
                    border-bottom: 1px solid #e5e7eb !important;
                    background: #f9fafb !important;
                }
                
                .user-menu-profile {
                    display: flex !important;
                    align-items: center !important;
                    gap: 12px !important;
                }
                
                .user-menu-avatar {
                    width: 48px !important;
                    height: 48px !important;
                    min-width: 48px !important;
                    min-height: 48px !important;
                    max-width: 48px !important;
                    max-height: 48px !important;
                    object-fit: cover !important;
                    border-radius: 50% !important;
                    flex-shrink: 0 !important;
                }
                
                .user-menu-info {
                    flex: 1 !important;
                    min-width: 0 !important;
                }
                
                .user-menu-name {
                    font-size: 15px !important;
                    font-weight: 600 !important;
                    color: #111827 !important;
                    margin-bottom: 4px !important;
                    white-space: nowrap !important;
                    overflow: hidden !important;
                    text-overflow: ellipsis !important;
                }
                
                .user-menu-email {
                    font-size: 13px !important;
                    color: #6b7280 !important;
                    white-space: nowrap !important;
                    overflow: hidden !important;
                    text-overflow: ellipsis !important;
                }
                
                .user-menu-content {
                    padding: 8px 0 !important;
                }
                
                .user-menu-item {
                    display: flex !important;
                    align-items: center !important;
                    gap: 12px !important;
                    padding: 12px 20px !important;
                    color: #111827 !important;
                    text-decoration: none !important;
                    font-size: 14px !important;
                    font-weight: 500 !important;
                    transition: background-color 0.2s ease !important;
                    cursor: pointer !important;
                    border: none !important;
                    background: none !important;
                    width: 100% !important;
                    text-align: left !important;
                }
                
                .user-menu-item:hover {
                    background: #f9fafb !important;
                }
                
                .user-menu-item-danger {
                    color: #ef4444 !important;
                }
                
                .user-menu-item-danger:hover {
                    background: #fef2f2 !important;
                    color: #dc2626 !important;
                }
                
                .user-menu-icon {
                    width: 20px !important;
                    height: 20px !important;
                    min-width: 20px !important;
                    min-height: 20px !important;
                    max-width: 20px !important;
                    max-height: 20px !important;
                    flex-shrink: 0 !important;
                    color: currentColor !important;
                }
            </style>
            
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const dropdown = document.getElementById('notification-dropdown');
                    const button = document.getElementById('notification-button');
                    const menu = document.getElementById('notification-menu');
                    
                    if (dropdown && button && menu) {
                        let hideTimeout = null;
                        let showTimeout = null;
                        
                        // Función para mostrar el menú
                        function showMenu() {
                            if (hideTimeout) {
                                clearTimeout(hideTimeout);
                                hideTimeout = null;
                            }
                            
                            if (showTimeout) {
                                clearTimeout(showTimeout);
                            }
                            
                            showTimeout = setTimeout(function() {
                                menu.classList.add('show');
                                showTimeout = null;
                            }, 50); // Pequeño delay para suavizar la transición
                        }
                        
                        // Función para ocultar el menú
                        function hideMenu() {
                            if (showTimeout) {
                                clearTimeout(showTimeout);
                                showTimeout = null;
                            }
                            
                            if (hideTimeout) {
                                clearTimeout(hideTimeout);
                            }
                            
                            // Delay antes de ocultar para permitir movimiento del mouse
                            hideTimeout = setTimeout(function() {
                                menu.classList.remove('show');
                                hideTimeout = null;
                            }, 200); // 200ms de delay
                        }
                        
                        // Mostrar cuando el mouse entra en el dropdown o el menú
                        dropdown.addEventListener('mouseenter', showMenu);
                        menu.addEventListener('mouseenter', showMenu);
                        
                        // Ocultar cuando el mouse sale del dropdown o el menú
                        dropdown.addEventListener('mouseleave', function(e) {
                            // Solo ocultar si el mouse no va hacia el menú
                            if (!menu.contains(e.relatedTarget)) {
                                hideMenu();
                            }
                        });
                        
                        menu.addEventListener('mouseleave', function(e) {
                            // Solo ocultar si el mouse no va hacia el botón
                            if (!dropdown.contains(e.relatedTarget)) {
                                hideMenu();
                            }
                        });
                        
                        // Mark as read on click
                        const notificationItems = document.querySelectorAll('.notification-item');
                        notificationItems.forEach(item => {
                            item.addEventListener('click', function() {
                                const notificationId = this.getAttribute('data-notification-id');
                                if (notificationId) {
                                    // Mark as read via AJAX
                                    fetch(`/notifications/${notificationId}/mark-read`, {
                                        method: 'PATCH',
                                        headers: {
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                            'Content-Type': 'application/json',
                                            'Accept': 'application/json',
                                        },
                                    }).then(response => {
                                        if (response.ok) {
                                            this.classList.remove('unread');
                                            const dot = this.querySelector('.notification-dot');
                                            if (dot) dot.remove();
                                            // Update badge count
                                            const badge = document.querySelector('.notification-dropdown span');
                                            if (badge) {
                                                const currentCount = parseInt(badge.textContent) || 0;
                                                const newCount = currentCount - 1;
                                                if (newCount > 0) {
                                                    badge.textContent = newCount > 99 ? '99+' : newCount;
                                                } else {
                                                    badge.remove();
                                                }
                                            }
                                        }
                                    }).catch(error => {
                                        console.error('Error marking notification as read:', error);
                                    });
                                }
                            });
                        });
                    }
                });
            </script>
            
            <script>
                // Menú de usuario
                document.addEventListener('DOMContentLoaded', function() {
                    const userDropdown = document.getElementById('user-menu-dropdown');
                    const userButton = document.getElementById('user-menu-button');
                    const userMenu = document.getElementById('user-menu');
                    
                    if (userDropdown && userButton && userMenu) {
                        let hideTimeout = null;
                        let showTimeout = null;
                        
                        // Función para mostrar el menú
                        function showUserMenu() {
                            if (hideTimeout) {
                                clearTimeout(hideTimeout);
                                hideTimeout = null;
                            }
                            
                            if (showTimeout) {
                                clearTimeout(showTimeout);
                            }
                            
                            showTimeout = setTimeout(function() {
                                userMenu.classList.add('show');
                                showTimeout = null;
                            }, 50);
                        }
                        
                        // Función para ocultar el menú
                        function hideUserMenu() {
                            if (showTimeout) {
                                clearTimeout(showTimeout);
                                showTimeout = null;
                            }
                            
                            if (hideTimeout) {
                                clearTimeout(hideTimeout);
                            }
                            
                            hideTimeout = setTimeout(function() {
                                userMenu.classList.remove('show');
                                hideTimeout = null;
                            }, 200);
                        }
                        
                        // Mostrar cuando el mouse entra en el dropdown o el menú
                        userDropdown.addEventListener('mouseenter', showUserMenu);
                        userMenu.addEventListener('mouseenter', showUserMenu);
                        
                        // Ocultar cuando el mouse sale del dropdown o el menú
                        userDropdown.addEventListener('mouseleave', function(e) {
                            if (!userMenu.contains(e.relatedTarget)) {
                                hideUserMenu();
                            }
                        });
                        
                        userMenu.addEventListener('mouseleave', function(e) {
                            if (!userDropdown.contains(e.relatedTarget)) {
                                hideUserMenu();
                            }
                        });
                    }
                });
            </script>
            
            <script>
                // Funcionalidad para botones móviles de notificaciones y usuario
                document.addEventListener('DOMContentLoaded', function() {
                    // Notificaciones móvil
                    const mobileNotificationDropdown = document.getElementById('notification-dropdown-mobile');
                    const mobileNotificationButton = document.getElementById('notification-button-mobile');
                    const mobileNotificationMenu = document.getElementById('notification-menu-mobile');
                    
                    if (mobileNotificationDropdown && mobileNotificationButton && mobileNotificationMenu) {
                        let hideTimeout = null;
                        let showTimeout = null;
                        
                        function showMobileNotificationMenu() {
                            if (hideTimeout) {
                                clearTimeout(hideTimeout);
                                hideTimeout = null;
                            }
                            if (showTimeout) {
                                clearTimeout(showTimeout);
                            }
                            showTimeout = setTimeout(function() {
                                mobileNotificationMenu.classList.add('show');
                                showTimeout = null;
                            }, 50);
                        }
                        
                        function hideMobileNotificationMenu() {
                            if (showTimeout) {
                                clearTimeout(showTimeout);
                                showTimeout = null;
                            }
                            if (hideTimeout) {
                                clearTimeout(hideTimeout);
                            }
                            hideTimeout = setTimeout(function() {
                                mobileNotificationMenu.classList.remove('show');
                                hideTimeout = null;
                            }, 200);
                        }
                        
                        mobileNotificationButton.addEventListener('click', function(e) {
                            e.stopPropagation();
                            if (mobileNotificationMenu.classList.contains('show')) {
                                hideMobileNotificationMenu();
                            } else {
                                showMobileNotificationMenu();
                            }
                        });
                        
                        document.addEventListener('click', function(e) {
                            if (!mobileNotificationDropdown.contains(e.target)) {
                                hideMobileNotificationMenu();
                            }
                        });
                    }
                    
                    // Usuario móvil
                    const mobileUserDropdown = document.getElementById('user-menu-dropdown-mobile');
                    const mobileUserButton = document.getElementById('user-menu-button-mobile');
                    const mobileUserMenu = document.getElementById('user-menu-mobile');
                    
                    if (mobileUserDropdown && mobileUserButton && mobileUserMenu) {
                        let hideTimeout = null;
                        let showTimeout = null;
                        
                        function showMobileUserMenu() {
                            if (hideTimeout) {
                                clearTimeout(hideTimeout);
                                hideTimeout = null;
                            }
                            if (showTimeout) {
                                clearTimeout(showTimeout);
                            }
                            showTimeout = setTimeout(function() {
                                mobileUserMenu.classList.add('show');
                                showTimeout = null;
                            }, 50);
                        }
                        
                        function hideMobileUserMenu() {
                            if (showTimeout) {
                                clearTimeout(showTimeout);
                                showTimeout = null;
                            }
                            if (hideTimeout) {
                                clearTimeout(hideTimeout);
                            }
                            hideTimeout = setTimeout(function() {
                                mobileUserMenu.classList.remove('show');
                                hideTimeout = null;
                            }, 200);
                        }
                        
                        mobileUserButton.addEventListener('click', function(e) {
                            e.stopPropagation();
                            if (mobileUserMenu.classList.contains('show')) {
                                hideMobileUserMenu();
                            } else {
                                showMobileUserMenu();
                            }
                        });
                        
                        document.addEventListener('click', function(e) {
                            if (!mobileUserDropdown.contains(e.target)) {
                                hideMobileUserMenu();
                            }
                        });
                    }
                });
            </script>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-6">
        <!-- Clientes -->
        <div class="overflow-hidden rounded-lg bg-white border dark:border-gray-700 border border-gray-200 dark:border-gray-700">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-14 h-14 rounded-lg flex items-center justify-center bg-blue-500">
                            <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-sm font-medium mb-1 dark:text-white">Clientes</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['clients'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Este Mes -->
        <div class="overflow-hidden rounded-lg bg-white border dark:border-gray-700 border border-gray-200 dark:border-gray-700">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-14 h-14 rounded-lg flex items-center justify-center bg-green-500">
                            <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5a2.25 2.25 0 002.25-2.25m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5a2.25 2.25 0 012.25 2.25v7.5" />
                        </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-sm font-medium mb-1 dark:text-white">Este Mes</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['this_month'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Completados -->
        <div class="overflow-hidden rounded-lg bg-white border dark:border-gray-700 border border-gray-200 dark:border-gray-700">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-14 h-14 rounded-lg flex items-center justify-center bg-purple-500">
                            <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-sm font-medium mb-1 dark:text-white">Completados</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['completed'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pendientes -->
        <div class="overflow-hidden rounded-lg bg-white border dark:border-gray-700 border border-gray-200 dark:border-gray-700">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-14 h-14 rounded-lg flex items-center justify-center bg-amber-500">
                            <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-sm font-medium mb-1 dark:text-white">Pendientes</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['pending'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas y Paneles -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Estadísticas -->
        <div class="lg:col-span-2 overflow-hidden rounded-lg bg-white border dark:border-gray-700" style="border: 1px solid #e5e7eb !important; min-width: 0; grid-column: span 2 / span 2;">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold flex items-center gap-2 dark:text-white" id="statistics-title" class="text-gray-900 dark:text-white">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" />
                        </svg>
                        Estadísticas
                    </h3>
                </div>
                <div class="mb-4 flex items-center justify-between flex-wrap gap-3">
                    <div class="flex items-center gap-4 flex-wrap">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-blue-500"></div>
                            <span class="text-sm statistics-text dark:text-white">Total Servicios</span>
                            <span class="text-sm font-semibold statistics-number text-gray-900 dark:text-white">{{ $stats['total_services'] ?? 0 }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-green-500"></div>
                            <span class="text-sm statistics-text dark:text-white">Completados</span>
                            <span class="text-sm font-semibold statistics-number text-gray-900 dark:text-white">{{ $stats['completed'] ?? 0 }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-red-500"></div>
                            <span class="text-sm statistics-text dark:text-white">Pendientes</span>
                            <span class="text-sm font-semibold statistics-number text-gray-900 dark:text-white">{{ $stats['pending'] ?? 0 }}</span>
            </div>
                    </div>
                    <select id="periodFilter" class="text-sm border dark:border-gray-700 rounded-md px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-green-500 dark:text-white dark:bg-gray-800" class="text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 cursor-pointer">
                        <option value="this_month" {{ request('period', 'this_month') == 'this_month' ? 'selected' : '' }}>Este Mes</option>
                        <option value="last_month" {{ request('period') == 'last_month' ? 'selected' : '' }}>Último Mes</option>
                        <option value="last_3_months" {{ request('period') == 'last_3_months' ? 'selected' : '' }}>Últimos 3 Meses</option>
                        <option value="last_6_months" {{ request('period') == 'last_6_months' ? 'selected' : '' }}>Últimos 6 Meses</option>
                        <option value="this_year" {{ request('period') == 'this_year' ? 'selected' : '' }}>Este Año</option>
                        <option value="last_year" {{ request('period') == 'last_year' ? 'selected' : '' }}>Año Pasado</option>
                        <option value="all_time" {{ request('period') == 'all_time' ? 'selected' : '' }}>Todo el Tiempo</option>
                    </select>
                </div>
                <div id="chart-container" class="w-full border dark:border-gray-700 rounded-lg overflow-hidden">
                    <canvas id="statisticsChart"></canvas>
        </div>

                <!-- Legend -->
                <div class="mt-4 flex flex-wrap items-center gap-4 justify-center">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-red-500"></div>
                        <span class="text-xs dark:text-white">Fumigación</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-amber-500"></div>
                        <span class="text-xs dark:text-white">Desratización</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-purple-500"></div>
                        <span class="text-xs dark:text-white">Sanitización</span>
                                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-pink-500"></div>
                        <span class="text-xs dark:text-white">Monitoreo Cebaderas</span>
                                        </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-green-500"></div>
                        <span class="text-xs dark:text-white">Otros Servicios</span>
                                        </div>
                                    </div>
                
                <!-- Resumen por Tipo de Servicio -->
                <div class="mt-6">
                    <h4 class="text-base font-semibold mb-4 text-gray-900 dark:text-white">RESUMEN POR TIPO DE SERVICIO</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                        @php
                            $serviceTypesCards = [
                                'fumigacion' => ['name' => 'FUMIGACIÓN', 'icon' => 'M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z', 'color' => '#ef4444'],
                                'desratizacion' => ['name' => 'DESRATIZACIÓN', 'icon' => 'M15.182 15.182a4.5 4.5 0 01-6.364 0M21 12a9 9 0 11-18 0 9 9 0 0118 0zM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75zm-.375 0h.008v.015h-.008V9.75zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75zm-.375 0h.008v.015h-.008V9.75z', 'color' => '#f59e0b'],
                                'sanitizacion' => ['name' => 'SANITIZACIÓN', 'icon' => 'M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z', 'color' => '#8b5cf6'],
                                'monitoreo-cebaderas' => ['name' => 'MONITOREO CEBADERAS', 'icon' => 'M15 10.5a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 21.75h16.5a.75.75 0 00.75-.75v-3.75a.75.75 0 00-.75-.75H4.5a.75.75 0 00-.75.75v3.75a.75.75 0 00.75.75z', 'color' => '#ec4899'],
                            ];
                        @endphp
                        @foreach($serviceTypesCards as $typeSlug => $typeInfo)
                            @php
                                $typeData = isset($stats['service_type_summary']) && isset($stats['service_type_summary'][$typeSlug]) ? $stats['service_type_summary'][$typeSlug] : null;
                                $count = $typeData && isset($typeData->total) ? $typeData->total : 0;
                                $totalForPercentage = $stats['total_services_percentage'] ?? 0;
                                $percentage = $totalForPercentage > 0 ? round(($count / $totalForPercentage) * 100) : 0;
                            @endphp
                            <div class="bg-white border dark:border-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                                <div class="flex items-center gap-2 mb-2">
                                    <svg class="w-5 h-5 text-gray-900 dark:text-gray-400 group-hover:text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" >
                                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $typeInfo['icon'] }}" />
                                    </svg>
                                    <div class="w-2 h-2 rounded-full" style="background: {{ $typeInfo['color'] }};"></div>
                                </div>
                                <p class="text-xs font-semibold mb-1 text-gray-900 dark:text-white">{{ $typeInfo['name'] }}</p>
                                <p class="text-2xl font-bold mb-1 text-gray-900 dark:text-white">{{ $count }}</p>
                                @if($count > 0 && $percentage > 0)
                                    <p class="text-xs dark:text-white">{{ $percentage }}%</p>
                                @else
                                    <p class="text-xs dark:text-white">Sin actividad</p>
                                @endif
                            </div>
                        @endforeach
                        @php
                            $otherCount = 0;
                            $otherPercentage = 0;
                            if (isset($stats['service_type_summary']) && is_object($stats['service_type_summary'])) {
                                $otherCount = $stats['service_type_summary']->whereNotIn('service_type', array_keys($serviceTypesCards))->sum('total');
                                $totalForPercentage = $stats['total_services_percentage'] ?? 0;
                                $otherPercentage = $totalForPercentage > 0 ? round(($otherCount / $totalForPercentage) * 100) : 0;
                            }
                        @endphp
                        <div class="bg-white border dark:border-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-5 h-5 text-gray-900 dark:text-gray-400 group-hover:text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                                </svg>
                                <div class="w-2 h-2 rounded-full bg-green-500"></div>
                            </div>
                            <p class="text-xs font-semibold mb-1 text-gray-900 dark:text-white">OTROS SERVICIOS</p>
                            <p class="text-2xl font-bold mb-1 text-gray-900 dark:text-white">{{ $otherCount }}</p>
                            @if($otherCount > 0 && $otherPercentage > 0)
                                <p class="text-xs dark:text-white">{{ $otherPercentage }}%</p>
                            @else
                                <p class="text-xs dark:text-white">Sin actividad</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Paneles Laterales -->
        <div class="space-y-6" style="grid-column: span 1 / span 1;">
            <!-- Ingresos del Mes -->
            <div class="overflow-hidden rounded-lg bg-white border dark:border-gray-700 border border-gray-200 dark:border-gray-700">
                <div class="p-5">
                    <h3 class="text-base font-semibold mb-3 text-gray-900 dark:text-white">Ingresos del Mes</h3>
                    <p class="text-3xl font-bold mb-2 text-gray-900 dark:text-white">${{ number_format($stats['monthly_income'] ?? 0, 0, ',', '.') }}</p>
                    <p class="text-sm mb-3 dark:text-white">{{ $stats['this_month_completed'] ?? $stats['completed'] ?? 0 }} servicios completados</p>
                    <div class="flex items-center justify-between pt-2 border-t border-gray-200 dark:border-gray-700">
                        <span class="text-sm dark:text-white">Promedio:</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">${{ number_format(($stats['monthly_income'] ?? 0) / max($stats['this_month_completed'] ?? $stats['completed'] ?? 1, 1), 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

            <!-- Alertas de Stock -->
            <div class="overflow-hidden rounded-lg bg-white border dark:border-gray-700 border border-gray-200 dark:border-gray-700">
                <div class="p-5">
                    <h3 class="text-base font-semibold mb-3 flex items-center gap-2 text-gray-900 dark:text-white">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                        Alertas de Stock
                    </h3>
                    <p class="text-4xl font-bold mb-2 text-gray-900 dark:text-white">{{ $stats['low_stock_alerts'] ?? 0 }}</p>
                    <p class="text-sm mb-2 dark:text-white">Productos con stock bajo</p>
                    @if(($stats['low_stock_alerts'] ?? 0) > 0)
                        <p class="text-xs flex items-center gap-1 dark:text-white">
                            <svg class="w-4 h-4 text-gray-900 dark:text-gray-400 group-hover:text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                            Requiere atención
                        </p>
                    @endif
                    </div>
                    </div>

            <!-- Acciones Rápidas -->
            @if(auth()->user()->hasAnyRole(['super-admin', 'supervisor']))
            <div class="overflow-hidden rounded-lg bg-white border dark:border-gray-700 border border-gray-200 dark:border-gray-700">
                <div class="p-5">
                    <h3 class="text-base font-semibold mb-4 text-gray-900 dark:text-white">Acciones Rápidas</h3>
                    <div class="space-y-3">
                        <a href="{{ route('admin.services.create') ?? '#' }}" class="relative rounded-lg bg-white border dark:border-gray-700 px-4 py-3 flex items-center space-x-3 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors border border-gray-200 dark:border-gray-700">
                    <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-red-500">
                                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                                </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="absolute inset-0" aria-hidden="true"></span>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">Nuevo Servicio</p>
                                <p class="text-xs dark:text-white">Crear orden de trabajo</p>
                    </div>
                </a>

                        <a href="{{ route('admin.clients.create') ?? route('clients.create') }}" class="relative rounded-lg bg-white border dark:border-gray-700 px-4 py-3 flex items-center space-x-3 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors border border-gray-200 dark:border-gray-700">
                    <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-green-500">
                                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM3 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 019.374 21c-2.331 0-4.512-.645-6.374-1.766z" />
                        </svg>
                                </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="absolute inset-0" aria-hidden="true"></span>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">Nuevo Cliente</p>
                                <p class="text-xs dark:text-white">Registrar cliente</p>
                    </div>
                </a>

                        <a href="{{ route('admin.statistics') ?? '#' }}" class="relative rounded-lg bg-white border dark:border-gray-700 px-4 py-3 flex items-center space-x-3 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors border border-gray-200 dark:border-gray-700">
                    <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-blue-500">
                                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                        </svg>
                                </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="absolute inset-0" aria-hidden="true"></span>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">Ver Estadísticas</p>
                                <p class="text-xs dark:text-white">Reportes y gráficos</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
    @endif
        </div>
    </div>

    <!-- Servicios Recientes -->
    <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Servicios Recientes</h3>
            <a href="{{ route('admin.services.index') ?? '#' }}" class="text-sm font-medium">Ver todos</a>
        </div>
        <div class="overflow-hidden rounded-lg bg-white border dark:border-gray-700 border border-gray-200 dark:border-gray-700">
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider dark:text-white">CLIENTE</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider dark:text-white">TIPO</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider dark:text-white">FECHA</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider dark:text-white">ESTADO</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider dark:text-white">PRIORIDAD</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-white divide-y divide-gray-200">
                            @forelse($recentServices ?? [] as $service)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $service->client->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ ucfirst(str_replace('-', ' ', $service->service_type ?? 'N/A')) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $service->created_at->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                        @if($service->status === 'completed') bg-green-100 text-green-800
                                        @elseif($service->status === 'in_progress') bg-blue-100 text-blue-800
                                        @else bg-yellow-100 text-yellow-800
                                        @endif">
                                        {{ ucfirst($service->status ?? 'pending') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                        @if(strtolower($service->priority ?? 'media') === 'alta') bg-red-100 text-red-800
                                        @elseif(strtolower($service->priority ?? 'media') === 'media') bg-yellow-100 text-yellow-800
                                        @else bg-blue-100 text-blue-800
                                        @endif">
                                        {{ ucfirst($service->priority ?? 'Media') }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-sm dark:text-white">
                                    No hay servicios registrados aún
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Statistics Chart
    const ctx = document.getElementById('statisticsChart');
    if (ctx) {
        // Get chart data from PHP
        const chartLabels = @json($stats['chart_labels'] ?? []);
        const chartDatasets = @json($stats['chart_datasets'] ?? []);
        
        // Ensure canvas fills container - wait for DOM to be ready
        setTimeout(() => {
            const container = ctx.parentElement;
            if (container) {
                const containerWidth = container.offsetWidth;
                const containerHeight = container.offsetHeight;
                ctx.style.width = containerWidth + 'px';
                ctx.style.height = containerHeight + 'px';
            }
        }, 100);
        
        // Calculate max value for Y axis
        let maxValue = 1;
        chartDatasets.forEach(dataset => {
            const datasetMax = Math.max(...dataset.data, 0);
            if (datasetMax > maxValue) maxValue = datasetMax;
        });
        const yAxisMax = Math.ceil(maxValue * 1.1);
        
        // Get current theme
        const isDark = document.documentElement.classList.contains('dark');
        const gridColor = isDark ? '#374151' : '#e5e7eb';
        const tickColor = isDark ? '#9ca3af' : '#6b7280';
        const chartBgColor = isDark ? '#1f2937' : '#f9fafb';
        
        // Update chart container background
        const chartContainer = document.getElementById('chart-container');
        if (chartContainer) {
            chartContainer.style.background = chartBgColor;
        }
        
        // Update colors on theme change
        const darkModeObserver = new MutationObserver(() => {
            const newIsDark = document.documentElement.classList.contains('dark');
            const newGridColor = newIsDark ? '#374151' : '#e5e7eb';
            const newTickColor = newIsDark ? '#9ca3af' : '#6b7280';
            const newChartBgColor = newIsDark ? '#1f2937' : '#f9fafb';
            const newTextColor = newIsDark ? '#e5e7eb' : '#111827';
            
            if (chartContainer) {
                chartContainer.style.background = newChartBgColor;
            }
            
            if (window.chartInstance) {
                window.chartInstance.options.scales.x.grid.color = newGridColor;
                window.chartInstance.options.scales.x.ticks.color = newTickColor;
                window.chartInstance.options.scales.y.grid.color = newGridColor;
                window.chartInstance.options.scales.y.ticks.color = newTickColor;
                window.chartInstance.update();
            }
        });
        darkModeObserver.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ['class']
        });
        
        // Manejar cambio de período
        const periodFilter = document.getElementById('periodFilter');
        if (periodFilter) {
            periodFilter.addEventListener('change', function() {
                const period = this.value;
                const url = new URL(window.location.href);
                url.searchParams.set('period', period);
                window.location.href = url.toString();
            });
        }
        
        window.chartInstance = new Chart(ctx, {
        type: 'line',
        data: {
                labels: chartLabels,
                datasets: chartDatasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
                layout: {
                    padding: {
                        top: 10,
                        bottom: 10,
                        left: 10,
                        right: 10
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        enabled: true,
                        mode: 'index',
                        intersect: false
                    }
                },
            scales: {
                    x: {
                        grid: {
                            display: true,
                            color: gridColor
                        },
                        border: {
                            display: false
                        },
                        ticks: {
                            color: tickColor,
                            font: {
                                size: 11
                            },
                            maxRotation: 0,
                            minRotation: 0
                        }
                    },
                    y: {
                        beginAtZero: true,
                        max: yAxisMax,
                        ticks: {
                            stepSize: Math.max(1, Math.ceil(yAxisMax / 5)),
                            color: tickColor,
                            font: {
                                size: 11
                            }
                        },
                        grid: {
                            color: gridColor,
                            drawBorder: false
                        },
                        border: {
                            display: false
                        }
                    }
                },
                elements: {
                    point: {
                        radius: 3,
                        hoverRadius: 5,
                        hoverBorderWidth: 2
                    },
                    line: {
                        borderWidth: 2
                    }
                },
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        enabled: true,
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += context.parsed.y;
                                return label;
                            }
                        }
                    }
                }
            }
        });
    }
    
    // Global Search Functionality
    (function() {
        // Obtener inputs de búsqueda (desktop y móvil)
        const searchInputDesktop = document.getElementById('global-search-input-desktop');
        const searchInputMobile = document.getElementById('global-search-input-mobile');
        const searchInput = searchInputDesktop || searchInputMobile; // Usar el que esté visible
        // Obtener contenedores de resultados (desktop y móvil)
        const searchResultsDesktop = document.getElementById('search-results-desktop');
        const searchResultsMobile = document.getElementById('search-results-mobile');
        const searchResultsContentDesktop = document.getElementById('search-results-content-desktop');
        const searchResultsContentMobile = document.getElementById('search-results-content-mobile');
        
        // Función para obtener los elementos activos según el tamaño de pantalla
        function getActiveSearchElements() {
            if (window.innerWidth >= 768) {
                return {
                    input: searchInputDesktop,
                    results: searchResultsDesktop,
                    content: searchResultsContentDesktop
                };
            } else {
                return {
                    input: searchInputMobile,
                    results: searchResultsMobile,
                    content: searchResultsContentMobile
                };
            }
        }
        
        const searchResults = searchResultsDesktop || searchResultsMobile;
        const searchResultsContent = searchResultsContentDesktop || searchResultsContentMobile;
        let searchTimeout = null;
        let currentSearch = '';
        
        if (!searchInput) return;
        
        // Iconos por tipo
        const typeIcons = {
            'service': '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>',
            'client': '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>',
            'product': '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>',
            'pest': '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>',
            'technician': '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>'
        };
        
        const typeLabels = {
            'service': 'Servicio',
            'client': 'Cliente',
            'product': 'Producto',
            'pest': 'Plaga',
            'technician': 'Técnico'
        };
        
        function renderResults(data, contentContainer) {
            if (!contentContainer) {
                const active = getActiveSearchElements();
                contentContainer = active.content;
            }
            
            if (!data || Object.keys(data).length === 0) {
                if (contentContainer) {
                    contentContainer.innerHTML = '<div class="p-4 text-center text-gray-500">No se encontraron resultados</div>';
                }
                return;
            }
            
            let html = '';
            let hasResults = false;
            
            // Servicios
            if (data.services && data.services.length > 0) {
                hasResults = true;
                html += '<div class="mb-2"><div class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase">Servicios</div>';
                data.services.forEach(item => {
                    html += `<a href="${item.url}" class="flex items-center gap-3 px-3 py-2 hover:bg-gray-50 rounded-lg transition-colors">
                        <div class="text-green-600">${typeIcons.service}</div>
                        <div class="flex-1 min-w-0">
                            <div class="font-medium text-gray-900 truncate">${item.title}</div>
                            <div class="text-sm text-gray-500 truncate">${item.subtitle}</div>
                        </div>
                    </a>`;
                });
                html += '</div>';
            }
            
            // Clientes
            if (data.clients && data.clients.length > 0) {
                hasResults = true;
                html += '<div class="mb-2"><div class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase">Clientes</div>';
                data.clients.forEach(item => {
                    html += `<a href="${item.url}" class="flex items-center gap-3 px-3 py-2 hover:bg-gray-50 rounded-lg transition-colors">
                        <div class="text-blue-600">${typeIcons.client}</div>
                        <div class="flex-1 min-w-0">
                            <div class="font-medium text-gray-900 truncate">${item.title}</div>
                            <div class="text-sm text-gray-500 truncate">${item.subtitle}</div>
                        </div>
                    </a>`;
                });
                html += '</div>';
            }
            
            // Productos
            if (data.products && data.products.length > 0) {
                hasResults = true;
                html += '<div class="mb-2"><div class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase">Productos</div>';
                data.products.forEach(item => {
                    html += `<a href="${item.url}" class="flex items-center gap-3 px-3 py-2 hover:bg-gray-50 rounded-lg transition-colors">
                        <div class="text-purple-600">${typeIcons.product}</div>
                        <div class="flex-1 min-w-0">
                            <div class="font-medium text-gray-900 truncate">${item.title}</div>
                            <div class="text-sm text-gray-500 truncate">${item.subtitle}</div>
                        </div>
                    </a>`;
                });
                html += '</div>';
            }
            
            // Plagas
            if (data.pests && data.pests.length > 0) {
                hasResults = true;
                html += '<div class="mb-2"><div class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase">Plagas</div>';
                data.pests.forEach(item => {
                    html += `<a href="${item.url}" class="flex items-center gap-3 px-3 py-2 hover:bg-gray-50 rounded-lg transition-colors">
                        <div class="text-red-600">${typeIcons.pest}</div>
                        <div class="flex-1 min-w-0">
                            <div class="font-medium text-gray-900 truncate">${item.title}</div>
                            <div class="text-sm text-gray-500 truncate">${item.subtitle}</div>
                        </div>
                    </a>`;
                });
                html += '</div>';
            }
            
            // Técnicos
            if (data.technicians && data.technicians.length > 0) {
                hasResults = true;
                html += '<div class="mb-2"><div class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase">Técnicos</div>';
                data.technicians.forEach(item => {
                    html += `<a href="${item.url}" class="flex items-center gap-3 px-3 py-2 hover:bg-gray-50 rounded-lg transition-colors">
                        <div class="text-indigo-600">${typeIcons.technician}</div>
                        <div class="flex-1 min-w-0">
                            <div class="font-medium text-gray-900 truncate">${item.title}</div>
                            <div class="text-sm text-gray-500 truncate">${item.subtitle}</div>
                        </div>
                    </a>`;
                });
                html += '</div>';
            }
            
            if (!hasResults) {
                html = '<div class="p-4 text-center text-gray-500">No se encontraron resultados</div>';
            }
            
            if (contentContainer) {
                contentContainer.innerHTML = html;
            }
        }
        
        function performSearch(query, resultsContainer, contentContainer) {
            if (!resultsContainer || !contentContainer) {
                const active = getActiveSearchElements();
                resultsContainer = active.results;
                contentContainer = active.content;
            }
            
            if (query.length < 2) {
                if (resultsContainer) {
                    resultsContainer.classList.add('hidden');
                }
                return;
            }
            
            currentSearch = query;
            
            fetch(`{{ route('admin.search') }}?q=${encodeURIComponent(query)}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                // Solo mostrar resultados si la búsqueda no ha cambiado
                if (query === currentSearch && resultsContainer && contentContainer) {
                    renderResults(data, contentContainer);
                    resultsContainer.classList.remove('hidden');
                }
            })
            .catch(error => {
                console.error('Error en búsqueda:', error);
                if (query === currentSearch && resultsContainer && contentContainer) {
                    contentContainer.innerHTML = '<div class="p-4 text-center text-red-500">Error al realizar la búsqueda: ' + error.message + '</div>';
                    resultsContainer.classList.remove('hidden');
                }
            });
        }
        
        // Función para inicializar event listeners en un input
        function initializeSearchListeners(input, results, content) {
            if (!input || !results || !content) return;
            
            input.addEventListener('input', function(e) {
                const query = e.target.value.trim();
                
                clearTimeout(searchTimeout);
                
                if (query.length < 2) {
                    results.classList.add('hidden');
                    return;
                }
                
                searchTimeout = setTimeout(() => {
                    performSearch(query, results, content);
                }, 300);
            });
            
            input.addEventListener('focus', function() {
                if (input.value.trim().length >= 2 && !results.classList.contains('hidden')) {
                    results.classList.remove('hidden');
                }
            });
            
            // Cerrar resultados al hacer clic fuera
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.global-search-container')) {
                    results.classList.add('hidden');
                }
            });
            
            // Manejar tecla Escape
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    results.classList.add('hidden');
                    input.blur();
                }
            });
        }
        
        // Inicializar listeners para desktop y móvil
        if (searchInputDesktop && searchResultsDesktop && searchResultsContentDesktop) {
            initializeSearchListeners(searchInputDesktop, searchResultsDesktop, searchResultsContentDesktop);
        }
        
        if (searchInputMobile && searchResultsMobile && searchResultsContentMobile) {
            initializeSearchListeners(searchInputMobile, searchResultsMobile, searchResultsContentMobile);
        }
        
        // Actualizar listeners al cambiar tamaño de ventana
        let resizeTimeout;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(function() {
                const active = getActiveSearchElements();
                if (active.input && active.results && active.content) {
                    // Los listeners ya están configurados, solo necesitamos actualizar la referencia
                }
            }, 250);
        });
    })();
    
    // Dashboard Mobile Menu Button
    (function() {
        function initDashboardMenu() {
            const dashboardMenuButton = document.getElementById('dashboard-mobile-menu-button');
            const sidebar = document.getElementById('sidebar');
            const mobileOverlay = document.getElementById('mobile-overlay');
            
            if (!dashboardMenuButton) {
                console.warn('Botón de menú móvil del dashboard no encontrado');
                setTimeout(initDashboardMenu, 100);
                return;
            }
            
            if (!sidebar) {
                console.error('Sidebar no encontrado');
                return;
            }
            
            function toggleMobileMenu() {
                // Verificar si el menú está abierto usando múltiples métodos
                const computedStyle = window.getComputedStyle(sidebar);
                const transform = computedStyle.transform;
                const sidebarTransform = sidebar.style.transform || '';
                const isOpen = sidebar.classList.contains('translate-x-0') || 
                              transform === 'matrix(1, 0, 0, 1, 0, 0)' || 
                              transform === 'none' ||
                              sidebarTransform === 'translateX(0)' ||
                              sidebarTransform.includes('translateX(0)') ||
                              sidebarTransform === '';
                
                if (isOpen) {
                    // Cerrar menú
                    sidebar.classList.remove('translate-x-0');
                    sidebar.classList.add('-translate-x-full');
                    
                    // Remover el style tag de override
                    const styleTag = document.getElementById('mobile-menu-override-style');
                    if (styleTag) {
                        styleTag.remove();
                    }
                    
                    // Asegurar que el sidebar esté oculto
                    sidebar.style.transform = 'translateX(-100%)';
                    
                    // Ocultar overlay
                    if (mobileOverlay) {
                        mobileOverlay.classList.add('hidden');
                        mobileOverlay.style.display = 'none';
                    }
                    
                    // Cambiar iconos
                    const menuIcon = document.getElementById('dashboard-menu-icon');
                    const closeIcon = document.getElementById('dashboard-close-icon');
                    if (menuIcon) menuIcon.classList.remove('hidden');
                    if (closeIcon) closeIcon.classList.add('hidden');
                    
                    // Restaurar scroll del body
                    document.body.style.overflow = '';
                } else {
                    // Abrir menú
                    sidebar.classList.remove('-translate-x-full');
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
                    
                    // Cambiar iconos
                    const menuIcon = document.getElementById('dashboard-menu-icon');
                    const closeIcon = document.getElementById('dashboard-close-icon');
                    if (menuIcon) menuIcon.classList.add('hidden');
                    if (closeIcon) closeIcon.classList.remove('hidden');
                    
                    // Bloquear scroll del body
                    document.body.style.overflow = 'hidden';
                }
            }
            
            // Event listener para el botón
            dashboardMenuButton.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                toggleMobileMenu();
            });
            
            // Event listener para el overlay (cerrar al hacer clic fuera)
            if (mobileOverlay) {
                mobileOverlay.addEventListener('click', function() {
                    toggleMobileMenu();
                });
            }
            
            // Cerrar menú al hacer clic en un enlace del sidebar (solo en móvil)
            if (sidebar) {
                const sidebarLinks = sidebar.querySelectorAll('a');
                sidebarLinks.forEach(link => {
                    link.addEventListener('click', function() {
                        if (window.innerWidth < 768) {
                            toggleMobileMenu();
                        }
                    });
                });
            }
        }
        
        // Inicializar cuando el DOM esté listo
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initDashboardMenu);
        } else {
            // Si el DOM ya está listo, esperar un poco para asegurar que el layout haya inicializado
            setTimeout(initDashboardMenu, 50);
        }
    })();
    

</script>
@endpush
@endsection
