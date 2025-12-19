@extends('layouts.app')

@section('title', 'Servicios')

@section('content')
<div class="space-y-4 sm:space-y-6 pt-3 md:pt-0">
    <!-- Header con hamburguesa y título -->
    <div class="mb-4 sm:mb-6">
        <!-- Primera fila: Hamburguesa + Título (móvil) -->
        <div class="flex items-center gap-3 mb-4 md:hidden" style="padding-top: 2.5rem;">
            <!-- Hamburguesa (solo móvil) -->
            <button id="page-mobile-menu-button" class="flex-shrink-0 p-2 rounded-lg bg-white border border-gray-300 shadow-md hover:bg-gray-50 transition-colors dark:text-white dark:bg-gray-700 dark:hover:bg-gray-600" style="z-index: 1000; position: relative;">
                <svg id="page-menu-icon" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="text-gray-900 dark:text-white">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
                <svg id="page-close-icon" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="text-gray-900 dark:text-white">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            
            <!-- Título -->
            <div class="flex-1" style="flex: 1 1 0% !important; min-width: 0 !important;">
                <h2 class="text-2xl font-bold">
                    Servicios
                </h2>
            </div>

            <!-- Iconos Header Móvil -->
            <div class="flex items-center gap-4">
                <!-- Notificaciones -->
                <a href="{{ route('admin.notification-center') ?? '#' }}" class="text-gray-500 hover:text-gray-700 relative">
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
                <a href="{{ Route::has('admin.profile') ? route('admin.profile') : (Route::has('profile') ? route('profile') : '#') }}" class="flex-shrink-0">
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
        
        <!-- Botón Nuevo Servicio (móvil) -->
        <div class="mb-4 md:hidden">
            <a href="{{ route('admin.services.create') ?? route('services.create') ?? '#' }}" class="inline-flex items-center justify-center w-full px-4 py-2.5 border border-transparent rounded-lg shadow-sm text-sm font-medium transition-colors dark:text-white dark:bg-green-600 dark:hover:bg-green-700">
                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>Nuevo Servicio</span>
            </a>
        </div>
        
        <!-- Segunda fila: Título completo (desktop) -->
        <div class="hidden md:flex md:items-center md:justify-between">
            <div class="min-w-0 flex-1">
                <h2 class="text-2xl sm:text-3xl font-bold leading-7 text-gray-900 sm:truncate sm:tracking-tight text-gray-900 dark:text-white" class="font-bold">
                    Servicios
                </h2>
                <p class="mt-1 text-xs sm:text-sm text-gray-600 dark:text-white">
                    Gestiona todos los servicios de control de plagas
                </p>
            </div>
            <div class="mt-3 sm:mt-4 md:mt-0 md:ml-4">
                <a href="{{ route('admin.services.create') ?? route('services.create') ?? '#' }}" class="inline-flex items-center justify-center w-full sm:w-auto px-3 sm:px-4 py-2 border border-transparent rounded-lg shadow-sm text-xs sm:text-sm font-medium transition-colors dark:text-white dark:bg-green-600 dark:hover:bg-green-700" style="border: 1px solid #e5e7eb !important;">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1.5 sm:mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span class="hidden sm:inline">Nuevo Servicio</span>
                    <span class="sm:hidden">Nuevo</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6 mb-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-4 dark:text-white">Filtros</h3>
        <form method="GET" action="{{ route('admin.services.index') ?? route('services.index') ?? '#' }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="flex flex-col space-y-2">
                <label for="status" class="text-sm font-medium text-gray-700 dark:text-white">Estado</label>
                <select name="status" id="status" class="border border-gray-300 rounded-lg px-4 py-3.5 text-base focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 w-full dark:text-white dark:bg-gray-700 dark:border-gray-600">
                    <option value="">Todos los estados</option>
                    <option value="pendiente" {{ request('status') === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="en_progreso" {{ request('status') === 'en_progreso' ? 'selected' : '' }}>En Progreso</option>
                    <option value="finalizado" {{ request('status') === 'finalizado' ? 'selected' : '' }}>Finalizado</option>
                    <option value="cancelado" {{ request('status') === 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                </select>
            </div>
            <div class="flex flex-col space-y-2">
                <label for="type" class="text-sm font-medium text-gray-700 dark:text-white">Tipo</label>
                <select name="type" id="type" class="border border-gray-300 rounded-lg px-4 py-3.5 text-base focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 w-full dark:text-white dark:bg-gray-700 dark:border-gray-600">
                    <option value="">Todos los tipos</option>
                    <option value="desratizacion" {{ request('type') === 'desratizacion' ? 'selected' : '' }}>Desratización</option>
                    <option value="desinsectacion" {{ request('type') === 'desinsectacion' ? 'selected' : '' }}>Desinsectación</option>
                    <option value="sanitizacion" {{ request('type') === 'sanitizacion' ? 'selected' : '' }}>Sanitización</option>
                </select>
            </div>
            <div class="flex flex-col space-y-2">
                <label for="priority" class="text-sm font-medium text-gray-700 dark:text-white">Prioridad</label>
                <select name="priority" id="priority" class="border border-gray-300 rounded-lg px-4 py-3.5 text-base focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 w-full dark:text-white dark:bg-gray-700 dark:border-gray-600">
                    <option value="">Todas las prioridades</option>
                    <option value="baja" {{ request('priority') === 'baja' ? 'selected' : '' }}>Baja</option>
                    <option value="media" {{ request('priority') === 'media' ? 'selected' : '' }}>Media</option>
                    <option value="alta" {{ request('priority') === 'alta' ? 'selected' : '' }}>Alta</option>
                </select>
            </div>
            <div class="flex flex-col space-y-2">
                <label class="text-sm font-medium text-gray-700 opacity-0 dark:text-white">Acciones</label>
                <div class="flex items-center gap-3 h-full">
                    <button type="submit" id="filter-submit-btn" class="flex-1 px-6 py-3.5 bg-gray-700 hover:bg-gray-800 text-white font-medium rounded-lg shadow-sm transition-colors dark:bg-green-600 dark:hover:bg-green-700">
                        Filtrar
                    </button>
                    <a href="{{ route('admin.services.index') }}" class="flex-1 px-6 py-3.5 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg shadow-sm transition-colors text-center no-underline">
                        Limpiar
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Services List -->
    <div class="bg-white dark:bg-gray-800 border dark:border-gray-700 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
        <div class="overflow-x-auto">
            @forelse($services as $service)
                <div class="p-4 sm:p-6 border-b border-gray-200 hover:bg-gray-50" style="border-bottom: 1px solid #e5e7eb;">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="flex-1">
                            <div class="mb-2">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ $service->client->name ?? 'Cliente no encontrado' }}
                                </h3>
                                @if($service->address)
                                    <p class="text-sm text-gray-600 dark:text-white">{{ $service->address }}</p>
                                @endif
                            </div>
                            <div class="flex flex-wrap gap-2">
                                @if($service->status)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($service->status == 'pendiente') bg-yellow-100 text-yellow-800
                                        @elseif($service->status == 'en_progreso') bg-blue-100 text-blue-800
                                        @elseif($service->status == 'completado') bg-green-100 text-green-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst(str_replace('_', ' ', $service->status)) }}
                                    </span>
                                @endif
                                @if($service->serviceType)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $service->serviceType->name ?? 'N/A' }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.services.show', $service) ?? route('services.show', $service) ?? '#' }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300" title="Ver">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center">
                    <p class="text-sm text-gray-600 dark:text-white">No se encontraron servicios</p>
                </div>
            @endforelse
        </div>
        
        @if($services->hasPages())
        <div class="bg-white px-2 sm:px-6 py-3 border-t border-gray-200" style="border-top: 1px solid #e5e7eb !important;">
            <!-- Información de resultados - Solo en desktop -->
            <div class="hidden sm:block text-sm text-gray-700 mb-3 dark:text-white">
                Mostrando
                <span class="font-medium">{{ $services->firstItem() }}</span>
                a
                <span class="font-medium">{{ $services->lastItem() }}</span>
                de
                <span class="font-medium">{{ $services->total() }}</span>
                resultados
            </div>
            
            <!-- Números de página - Visible en móvil y desktop -->
            <div class="flex items-center justify-center gap-1 overflow-x-auto w-full">
                @if($services->onFirstPage())
                    <span class="px-2 py-1.5 text-xs sm:text-sm font-medium text-gray-400 cursor-not-allowed whitespace-nowrap" style="color: #9CA3AF !important;">« Anterior</span>
                @else
                    <a href="{{ $services->previousPageUrl() }}" class="px-2 py-1.5 text-xs sm:text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 whitespace-nowrap" style="color: #9CA3AF !important;">« Anterior</a>
                @endif
                
                @php
                    $currentPage = $services->currentPage();
                    $lastPage = $services->lastPage();
                    $startPage = max(1, $currentPage - 1);
                    $endPage = min($lastPage, $currentPage + 1);
                    
                    // Si estamos cerca del inicio, mostrar más páginas al final
                    if ($currentPage <= 2) {
                        $endPage = min($lastPage, 4);
                    }
                    // Si estamos cerca del final, mostrar más páginas al inicio
                    if ($currentPage >= $lastPage - 1) {
                        $startPage = max(1, $lastPage - 3);
                    }
                @endphp
                
                @if($startPage > 1)
                    <a href="{{ $services->url(1) }}" class="px-2 py-1.5 text-xs sm:text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 whitespace-nowrap" style="color: #9CA3AF !important;">1</a>
                    @if($startPage > 2)
                        <span class="px-1 text-gray-400">...</span>
                    @endif
                @endif
                
                @foreach($services->getUrlRange($startPage, $endPage) as $page => $url)
                    @if($page == $services->currentPage())
                        <span class="px-2.5 py-1.5 text-xs sm:text-sm font-medium text-white bg-green-600 border border-green-600 rounded-md whitespace-nowrap">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="px-2.5 py-1.5 text-xs sm:text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 whitespace-nowrap" style="color: #9CA3AF !important;">{{ $page }}</a>
                    @endif
                @endforeach
                
                @if($endPage < $lastPage)
                    @if($endPage < $lastPage - 1)
                        <span class="px-1 text-gray-400">...</span>
                    @endif
                    <a href="{{ $services->url($lastPage) }}" class="px-2 py-1.5 text-xs sm:text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 whitespace-nowrap" style="color: #9CA3AF !important;">{{ $lastPage }}</a>
                @endif
                
                @if($services->hasMorePages())
                    <a href="{{ $services->nextPageUrl() }}" class="px-2 py-1.5 text-xs sm:text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 whitespace-nowrap" style="color: #9CA3AF !important;">Siguiente »</a>
                @else
                    <span class="px-2 py-1.5 text-xs sm:text-sm font-medium text-gray-400 cursor-not-allowed whitespace-nowrap" style="color: #9CA3AF !important;">Siguiente »</span>
                @endif
            </div>
            
            <!-- Información de resultados - Solo en móvil -->
            <div class="sm:hidden text-xs text-gray-600 text-center mt-2 dark:text-white">
                Página {{ $services->currentPage() }} de {{ $services->lastPage() }}
            </div>
        </div>
        @endif
    </div>
</div>

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

@endsection
