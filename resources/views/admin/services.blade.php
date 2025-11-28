@extends('layouts.app')

@section('title', 'Servicios')

@section('content')
<div class="space-y-4 sm:space-y-6 pt-3 md:pt-0">
    <!-- Header con hamburguesa y título -->
    <div class="mb-4 sm:mb-6">
        <!-- Primera fila: Hamburguesa + Título (móvil) -->
        <div class="flex items-center gap-3 mb-4 md:hidden" style="padding-top: 2.5rem;">
            <!-- Hamburguesa (solo móvil) -->
            <button id="page-mobile-menu-button" class="flex-shrink-0 p-2 rounded-lg bg-white border border-gray-300 shadow-md hover:bg-gray-50 transition-colors md:hidden" style="z-index: 50;">
                <svg id="page-menu-icon" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="color: #111827;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
                <svg id="page-close-icon" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="color: #111827;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            
            <!-- Título -->
            <div class="flex-1" style="flex: 1 1 0% !important; min-width: 0 !important;">
                <h2 class="text-2xl font-bold" style="color: #111827; font-weight: 700; margin: 0 !important;">
                    Servicios
                </h2>
            </div>
        </div>
        
        <!-- Botón Nuevo Servicio (móvil) -->
        <div class="mb-4 md:hidden">
            <a href="{{ route('admin.services.create') ?? route('services.create') ?? '#' }}" class="inline-flex items-center justify-center w-full px-4 py-2.5 border border-transparent rounded-lg shadow-sm text-sm font-medium transition-colors" style="background: #22c55e; hover:background: #16a34a; color: #ffffff !important;">
                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="color: #ffffff !important;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span style="color: #ffffff !important;">Nuevo Servicio</span>
            </a>
        </div>
        
        <!-- Segunda fila: Título completo (desktop) -->
        <div class="hidden md:flex md:items-center md:justify-between">
            <div class="min-w-0 flex-1">
                <h2 class="text-2xl sm:text-3xl font-bold leading-7 text-gray-900 sm:truncate sm:tracking-tight" style="color: #111827; font-weight: 700;">
                    Servicios
                </h2>
                <p class="mt-1 text-xs sm:text-sm" style="color: #6b7280;">
                    Gestiona todos los servicios de control de plagas
                </p>
            </div>
            <div class="mt-3 sm:mt-4 md:mt-0 md:ml-4">
                <a href="{{ route('admin.services.create') ?? route('services.create') ?? '#' }}" class="inline-flex items-center justify-center w-full sm:w-auto px-3 sm:px-4 py-2 border border-transparent rounded-lg shadow-sm text-xs sm:text-sm font-medium transition-colors" style="background: #22c55e; hover:background: #16a34a; color: #ffffff !important;">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1.5 sm:mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="color: #ffffff !important;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span class="hidden sm:inline" style="color: #ffffff !important;">Nuevo Servicio</span>
                    <span class="sm:hidden" style="color: #ffffff !important;">Nuevo</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6 mb-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-4">Filtros</h3>
        <form method="GET" action="{{ route('admin.services.index') ?? route('services.index') ?? '#' }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="flex flex-col space-y-2">
                <label for="status" class="text-sm font-medium text-gray-700">Estado</label>
                <select name="status" id="status" class="border border-gray-300 rounded-lg px-4 py-3.5 text-base focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 w-full">
                    <option value="">Todos los estados</option>
                    <option value="pendiente" {{ request('status') === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="en_progreso" {{ request('status') === 'en_progreso' ? 'selected' : '' }}>En Progreso</option>
                    <option value="finalizado" {{ request('status') === 'finalizado' ? 'selected' : '' }}>Finalizado</option>
                    <option value="cancelado" {{ request('status') === 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                </select>
            </div>
            <div class="flex flex-col space-y-2">
                <label for="type" class="text-sm font-medium text-gray-700">Tipo</label>
                <select name="type" id="type" class="border border-gray-300 rounded-lg px-4 py-3.5 text-base focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 w-full">
                    <option value="">Todos los tipos</option>
                    <option value="desratizacion" {{ request('type') === 'desratizacion' ? 'selected' : '' }}>Desratización</option>
                    <option value="desinsectacion" {{ request('type') === 'desinsectacion' ? 'selected' : '' }}>Desinsectación</option>
                    <option value="sanitizacion" {{ request('type') === 'sanitizacion' ? 'selected' : '' }}>Sanitización</option>
                </select>
            </div>
            <div class="flex flex-col space-y-2">
                <label for="priority" class="text-sm font-medium text-gray-700">Prioridad</label>
                <select name="priority" id="priority" class="border border-gray-300 rounded-lg px-4 py-3.5 text-base focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 w-full">
                    <option value="">Todas las prioridades</option>
                    <option value="baja" {{ request('priority') === 'baja' ? 'selected' : '' }}>Baja</option>
                    <option value="media" {{ request('priority') === 'media' ? 'selected' : '' }}>Media</option>
                    <option value="alta" {{ request('priority') === 'alta' ? 'selected' : '' }}>Alta</option>
                </select>
            </div>
            <div class="flex flex-col space-y-2">
                <label class="text-sm font-medium text-gray-700 opacity-0">Acciones</label>
                <div class="flex items-center gap-3 h-full">
                    <button type="submit" id="filter-submit-btn" class="flex-1 px-6 py-3.5 bg-gray-700 hover:bg-gray-800 text-white font-medium rounded-lg shadow-sm transition-colors" style="color: #ffffff !important; background-color: #374151 !important;">
                        Filtrar
                    </button>
                    <a href="{{ route('admin.services.index') }}" class="flex-1 px-6 py-3.5 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg shadow-sm transition-colors text-center no-underline" style="color: #374151 !important; background-color: #e5e7eb !important;">
                        Limpiar
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Services List -->
    <div class="bg-white border dark:border-gray-700 rounded-lg overflow-hidden" style="border: 1px solid #e5e7eb !important;">
        <div class="overflow-x-auto">
            @forelse($services as $service)
                <div class="p-4 sm:p-6 border-b border-gray-200 hover:bg-gray-50" style="border-bottom: 1px solid #e5e7eb;">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="flex-1">
                            <div class="mb-2">
                                <h3 class="text-lg font-semibold" style="color: #111827;">
                                    {{ $service->client->name ?? 'Cliente no encontrado' }}
                                </h3>
                                @if($service->address)
                                    <p class="text-sm" style="color: #6b7280;">{{ $service->address }}</p>
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
                            <a href="{{ route('admin.services.show', $service) ?? route('services.show', $service) ?? '#' }}" class="text-blue-600 hover:text-blue-900" title="Ver">
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
                    <p class="text-sm" style="color: #6b7280;">No se encontraron servicios</p>
                </div>
            @endforelse
        </div>
        
        @if($services->hasPages())
        <div class="bg-white px-2 sm:px-6 py-3 border-t border-gray-200" style="border-top: 1px solid #e5e7eb !important;">
            <!-- Información de resultados - Solo en desktop -->
            <div class="hidden sm:block text-sm text-gray-700 mb-3">
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
                    <span class="px-2 py-1.5 text-xs sm:text-sm font-medium text-gray-400 cursor-not-allowed whitespace-nowrap">« Anterior</span>
                @else
                    <a href="{{ $services->previousPageUrl() }}" class="px-2 py-1.5 text-xs sm:text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 whitespace-nowrap">« Anterior</a>
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
                    <a href="{{ $services->url(1) }}" class="px-2 py-1.5 text-xs sm:text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 whitespace-nowrap">1</a>
                    @if($startPage > 2)
                        <span class="px-1 text-gray-400">...</span>
                    @endif
                @endif
                
                @foreach($services->getUrlRange($startPage, $endPage) as $page => $url)
                    @if($page == $services->currentPage())
                        <span class="px-2.5 py-1.5 text-xs sm:text-sm font-medium text-white bg-green-600 border border-green-600 rounded-md whitespace-nowrap">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="px-2.5 py-1.5 text-xs sm:text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 whitespace-nowrap">{{ $page }}</a>
                    @endif
                @endforeach
                
                @if($endPage < $lastPage)
                    @if($endPage < $lastPage - 1)
                        <span class="px-1 text-gray-400">...</span>
                    @endif
                    <a href="{{ $services->url($lastPage) }}" class="px-2 py-1.5 text-xs sm:text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 whitespace-nowrap">{{ $lastPage }}</a>
                @endif
                
                @if($services->hasMorePages())
                    <a href="{{ $services->nextPageUrl() }}" class="px-2 py-1.5 text-xs sm:text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 whitespace-nowrap">Siguiente »</a>
                @else
                    <span class="px-2 py-1.5 text-xs sm:text-sm font-medium text-gray-400 cursor-not-allowed whitespace-nowrap">Siguiente »</span>
                @endif
            </div>
            
            <!-- Información de resultados - Solo en móvil -->
            <div class="sm:hidden text-xs text-gray-600 text-center mt-2">
                Página {{ $services->currentPage() }} de {{ $services->lastPage() }}
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    // Prevenir envío automático del formulario
    (function() {
        const filterForm = document.querySelector('form[method="GET"]');
        const statusSelect = document.getElementById('status');
        const typeSelect = document.getElementById('type');
        const prioritySelect = document.getElementById('priority');
        const filterSubmitBtn = document.getElementById('filter-submit-btn');

        // Prevenir envío automático cuando se cambia un select
        if (statusSelect) {
            statusSelect.addEventListener('change', function(e) {
                e.preventDefault();
                // No hacer nada, solo esperar a que se haga clic en el botón
            });
        }

        if (typeSelect) {
            typeSelect.addEventListener('change', function(e) {
                e.preventDefault();
                // No hacer nada, solo esperar a que se haga clic en el botón
            });
        }

        if (prioritySelect) {
            prioritySelect.addEventListener('change', function(e) {
                e.preventDefault();
                // No hacer nada, solo esperar a que se haga clic en el botón
            });
        }

        // Prevenir envío cuando se presiona Enter en un select
        if (filterForm) {
            filterForm.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && (e.target.tagName === 'SELECT')) {
                    e.preventDefault();
                    // Solo permitir envío si se presiona Enter en el botón
                }
            });
        }

        // Permitir envío solo cuando se hace clic en el botón
        if (filterSubmitBtn) {
            filterSubmitBtn.addEventListener('click', function(e) {
                // Permitir el envío normal del formulario
            });
        }
    })();

    // Page Mobile Menu Button
    (function() {
        function initPageMenu() {
            const pageMenuButton = document.getElementById('page-mobile-menu-button');
            const sidebar = document.getElementById('sidebar');
            const mobileOverlay = document.getElementById('mobile-overlay');
            
            if (!pageMenuButton) {
                setTimeout(initPageMenu, 100);
                return;
            }
            
            if (!sidebar) {
                console.error('Sidebar no encontrado');
                return;
            }
            
            function toggleMobileMenu() {
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
                    sidebar.classList.remove('translate-x-0');
                    sidebar.classList.add('-translate-x-full');
                    const styleTag = document.getElementById('mobile-menu-override-style');
                    if (styleTag) styleTag.remove();
                    sidebar.style.transform = 'translateX(-100%)';
                    if (mobileOverlay) {
                        mobileOverlay.classList.add('hidden');
                        mobileOverlay.style.display = 'none';
                    }
                    const menuIcon = document.getElementById('page-menu-icon');
                    const closeIcon = document.getElementById('page-close-icon');
                    if (menuIcon) menuIcon.classList.remove('hidden');
                    if (closeIcon) closeIcon.classList.add('hidden');
                    document.body.style.overflow = '';
                } else {
                    sidebar.classList.remove('-translate-x-full');
                    sidebar.classList.add('translate-x-0');
                    let styleTag = document.getElementById('mobile-menu-override-style');
                    if (!styleTag) {
                        styleTag = document.createElement('style');
                        styleTag.id = 'mobile-menu-override-style';
                        document.head.appendChild(styleTag);
                    }
                    styleTag.textContent = `#sidebar { transform: translateX(0) !important; display: flex !important; visibility: visible !important; opacity: 1 !important; z-index: 9999 !important; position: fixed !important; left: 0 !important; top: 0 !important; width: 288px !important; height: 100vh !important; }`;
                    sidebar.style.cssText = `display: flex !important; transform: translateX(0) !important; visibility: visible !important; opacity: 1 !important; z-index: 9999 !important; position: fixed !important; left: 0 !important; top: 0 !important; width: 288px !important; height: 100vh !important;`;
                    if (mobileOverlay) {
                        mobileOverlay.classList.remove('hidden');
                        mobileOverlay.style.cssText = `display: block !important; visibility: visible !important; z-index: 9998 !important;`;
                    }
                    const menuIcon = document.getElementById('page-menu-icon');
                    const closeIcon = document.getElementById('page-close-icon');
                    if (menuIcon) menuIcon.classList.add('hidden');
                    if (closeIcon) closeIcon.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                }
            }
            
            pageMenuButton.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                toggleMobileMenu();
            });
            
            if (mobileOverlay) {
                mobileOverlay.addEventListener('click', function() {
                    toggleMobileMenu();
                });
            }
            
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
        
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initPageMenu);
        } else {
            setTimeout(initPageMenu, 50);
        }
    })();
</script>
@endpush
@endsection

