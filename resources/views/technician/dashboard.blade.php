@extends('layouts.app')

@section('title', 'Dashboard')

@php
    $finalizedServices = $finalizedServices ?? 0;
@endphp

@section('content')
<div class="space-y-4 sm:space-y-6 mx-4 sm:mx-6 lg:mx-8 pt-3 md:pt-6">
    <!-- Header con hamburguesa y título -->
    <div class="mb-4 sm:mb-6">
        <!-- Primera fila: Hamburguesa + Título (móvil) -->
        <div class="flex items-center gap-3 mb-4 md:hidden" style="padding-top: 2.5rem;">
            <!-- Hamburguesa (solo móvil) -->
            <button id="page-mobile-menu-button" class="flex-shrink-0 p-2 rounded-lg bg-white border border-gray-300 shadow-md hover:bg-gray-50 transition-colors" style="z-index: 1000; position: relative;">
                <svg id="page-menu-icon" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="text-gray-900 dark:text-white">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
                <svg id="page-close-icon" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="text-gray-900 dark:text-white">
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
                <a href="{{ route('technician.notifications.index') }}" class="text-gray-500 hover:text-gray-700 relative">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                    </svg>
                    @php
                        $unreadCount = auth()->check() ? auth()->user()->unreadNotifications()->count() : 0;
                    @endphp
                    @if($unreadCount > 0)
                    <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-500 ring-2 ring-white transform translate-x-1/4 -translate-y-1/4"></span>
                    @endif
                </a>

                <!-- Perfil -->
                <a href="{{ route('technician.profile') }}" class="flex-shrink-0">
                    <div class="h-10 w-10 rounded-full bg-green-600 flex items-center justify-center shadow-sm flex-shrink-0">
                        <span class="text-white font-medium text-base">{{ substr(auth()->user()->name ?? 'U', 0, 1) }}</span>
                    </div>
                </a>
                <!-- Logout -->
                <form method="POST" action="{{ route('logout') }}" class="flex-shrink-0">
                    @csrf
                    <button type="submit" class="text-gray-500 hover:text-red-600 transition-colors" title="Cerrar Sesión">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Header original (desktop) -->
        <div class="hidden md:block">
            @include('technician.partials.header', [
                'title' => 'Dashboard',
                'searchPlaceholder' => 'Buscar servicios...',
                'pageId' => 'dashboard',
                'showDate' => true
            ])
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-6">
        <!-- Clientes -->
        <div class="overflow-hidden rounded-lg bg-white border dark:border-gray-700 border border-gray-200 dark:border-gray-700">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-14 h-14 rounded-lg flex items-center justify-center">
                            <svg class="w-7 h-7 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-sm font-medium mb-1 text-gray-700 dark:text-white">Clientes</p>
                        <p class="text-3xl font-bold">{{ $clientsCount ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Este Mes -->
        <div class="overflow-hidden rounded-lg bg-white border dark:border-gray-700 border border-gray-200 dark:border-gray-700">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-14 h-14 rounded-lg flex items-center justify-center">
                            <svg class="w-7 h-7 text-purple-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-sm font-medium mb-1 text-gray-700 dark:text-white">Este Mes</p>
                        <p class="text-3xl font-bold">{{ $monthlyServices ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Completados -->
        <div class="overflow-hidden rounded-lg bg-white border dark:border-gray-700 border border-gray-200 dark:border-gray-700">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-14 h-14 rounded-lg flex items-center justify-center bg-green-500">
                            <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-sm font-medium mb-1 text-gray-700 dark:text-white">Completados</p>
                        <p class="text-3xl font-bold">{{ $finalizedServices ?? 0 }}</p>
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
                        <p class="text-sm font-medium mb-1 text-gray-700 dark:text-white">Pendientes</p>
                        <p class="text-3xl font-bold">{{ $pendingServices ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Servicios Asignados -->
    <div class="overflow-hidden rounded-lg bg-white border dark:border-gray-700 mb-6 border border-gray-200 dark:border-gray-700">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Servicios Asignados</h3>
                    <p class="text-sm mt-1 text-gray-700 dark:text-white">Próximos servicios a realizar</p>
                </div>
                <a href="{{ route('technician.services') }}" class="text-sm font-medium">Ver todos</a>
            </div>
            <div class="space-y-3">
                @forelse($assignedServices ?? [] as $service)
                @php
                    $iconBg = '#f3f4f6';
                    $iconColor = '#374151';
                    $iconPath = 'M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z'; // Clock default

                    if($service->service_type == 'desratizacion') {
                        $iconBg = '#fee2e2'; // red-100
                        $iconColor = '#991b1b'; // red-800
                        // Alert triangle
                        $iconPath = 'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z';
                    } elseif($service->service_type == 'desinsectacion') {
                        $iconBg = '#fef9c3'; // yellow-100
                        $iconColor = '#854d0e'; // yellow-800
                        // Bug icon
                        $iconPath = 'M12 7.462c-2.502 0-4.853 1.558-5.738 3.808A2.999 2.999 0 014 13.5v3.75a3 3 0 003 3h10a3 3 0 003-3v-3.75a2.999 2.999 0 01-2.262-2.23c-.885-2.25-3.236-3.808-5.738-3.808zM6.75 8.25a.75.75 0 01.75-.75h9a.75.75 0 010 1.5h-9a.75.75 0 01-.75-.75zM6.75 18.75a.75.75 0 01.75-.75h9a.75.75 0 010 1.5h-9a.75.75 0 01-.75-.75zM3.75 15a.75.75 0 01.75-.75h15a.75.75 0 010 1.5h-15a.75.75 0 01-.75-.75zM3 12.75a.75.75 0 01.75-.75h16.5a.75.75 0 010 1.5H3.75a.75.75 0 01-.75-.75z';
                    } elseif($service->service_type == 'sanitizacion') {
                        $iconBg = '#dbeafe'; // blue-100
                        $iconColor = '#1e40af'; // blue-800
                        // Shield Check
                        $iconPath = 'M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z';
                    }
                @endphp
                <div class="flex items-center justify-between p-4 border-b border-gray-200 last:border-b-0">
                    <div class="flex items-center gap-4 flex-1">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: {{ $iconBg }};">
                                <svg class="w-5 h-5" style="color: {{ $iconColor }};" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $iconPath }}" />
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ optional($service->client)->name ?? 'N/A' }} - {{ ucfirst(str_replace('-', ' ', $service->service_type ?? 'N/A')) }}</p>
                            <p class="text-xs text-gray-700 dark:text-white">{{ $service->scheduled_date ? $service->scheduled_date->format('d/m/Y H:i') : ($service->created_at->format('d/m/Y H:i')) }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-1 text-xs font-medium rounded-full">
                            {{ ucfirst(str_replace('_', ' ', $service->status ?? 'Pendiente')) }}
                        </span>
                        <a href="{{ route('technician.service.detail', $service) }}" class="px-3 py-1.5 text-xs font-medium rounded-md text-white bg-green-500">Ver Detalle</a>
                    </div>
                </div>
                @empty
                <div class="text-center py-8">
                    <p class="text-sm text-gray-700 dark:text-white">No hay servicios asignados</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Acciones Rápidas -->
    <div class="overflow-hidden rounded-lg bg-white border dark:border-gray-700 border border-gray-200 dark:border-gray-700">
        <div class="p-6">
            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Acciones Rápidas</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <a href="{{ route('technician.services') }}" class="flex items-center gap-4 p-4 rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors">
                    <div class="w-12 h-12 rounded-lg flex items-center justify-center bg-green-500">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Ver Mis Servicios</p>
                        <p class="text-xs text-gray-700 dark:text-white">Gestiona todos tus servicios</p>
                    </div>
                </a>
                <a href="{{ route('technician.profile') }}" class="flex items-center gap-4 p-4 rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors">
                    <div class="w-12 h-12 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-gray-900 dark:text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Mi Perfil</p>
                        <p class="text-xs text-gray-700 dark:text-white">Actualiza tu información</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function() {
    function initPageMenuButton() {
        const pageMenuButton = document.getElementById('page-mobile-menu-button');
        
        if (!pageMenuButton) {
            console.warn('[PAGE MENU] Botón page-mobile-menu-button no encontrado, reintentando...');
            setTimeout(initPageMenuButton, 100);
            return;
        }
        
        console.log('[PAGE MENU] Botón encontrado, configurando listener...');
        
        pageMenuButton.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('[PAGE MENU] Click detectado, llamando a window.openMobileMenu()');
            
            if (typeof window.openMobileMenu === 'function') {
                window.openMobileMenu();
            } else {
                console.error('[PAGE MENU] window.openMobileMenu no está definida!');
            }
        });
        
        console.log('[PAGE MENU] Listener configurado correctamente');
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPageMenuButton);
    } else {
        initPageMenuButton();
    }
})();
</script>
@endpush

