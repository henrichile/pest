@extends('layouts.app')

@section('title', 'Clientes')

@section('content')
<div class="space-y-4 sm:space-y-6 pt-3 md:pt-0">
    <!-- Header con hamburguesa y título -->
    <div class="mb-4 sm:mb-6">
        <!-- Primera fila: Hamburguesa + Título (móvil) -->
        <div class="flex items-center gap-3 mb-4 md:hidden" style="padding-top: 2.5rem;">
            <!-- Hamburguesa (solo móvil) -->
            <button id="page-mobile-menu-button" class="flex-shrink-0 p-2 rounded-lg bg-white border border-gray-300 shadow-md hover:bg-gray-50 transition-colors" style="z-index: 50;">
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
                    Clientes x
                </h2>
            </div>
        </div>
        
        <!-- Botón Nuevo Cliente (móvil) -->
        <div class="mb-4 md:hidden">
            <a href="{{ route('admin.clients.create') ?? '#' }}" class="inline-flex items-center justify-center w-full px-4 py-2.5 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>Nuevo Cliente</span>
            </a>
        </div>
        
        <!-- Segunda fila: Título completo (desktop) -->
        <div class="hidden md:flex md:items-center md:justify-between">
            <div class="min-w-0 flex-1">
                <h2 class="text-2xl sm:text-3xl font-bold leading-7 text-gray-900 sm:truncate sm:tracking-tight text-gray-900 dark:text-white" class="font-bold">
                    Clientes
                </h2>
                <p class="mt-1 text-xs sm:text-sm text-gray-600 dark:text-white">
                    Gestiona la información de todos los clientes
                </p>
            </div>
            <div class="mt-3 sm:mt-4 md:mt-0 md:ml-4">
                <a href="{{ route('admin.clients.create') ?? '#' }}" class="inline-flex items-center justify-center w-full sm:w-auto px-3 sm:px-4 py-2 border border-transparent rounded-lg shadow-sm text-xs sm:text-sm font-medium text-white transition-colors">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1.5 sm:mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span class="hidden sm:inline">Nuevo Cliente</span>
                    <span class="sm:hidden">Nuevo</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="mb-4 sm:mb-6">
        <form method="GET" action="{{ route('admin.clients.index') ?? route('clients.index') ?? '#' }}" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1 relative" style="max-width: 100%;">
                <div class="absolute inset-y-0 left-0 pl-3 dark:white flex items-center pointer-events-none z-10">
                    <svg class="h-5 w-5" style="left: 10px; top: 50%; transform: translateY(-50%); width: 18px; height: 18px; color: #9ca3af; pointer-events: none; z-index: 1;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" name="search" id="search-input" class="block w-full pr-3 py-3.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all dark:text-white dark:bg-gray-700 dark:border-gray-600" style="border: 1px solid #e5e7eb !important; color: #111827; background: white; padding-left: 40px !important;" placeholder="Buscar por nombre, RUT..." value="{{ request('search') }}">
            </div>
            <button type="submit" class="px-4 py-3.5 bg-gray-700 text-white rounded-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors text-sm font-medium whitespace-nowrap">
                Buscar
            </button>
        </form>
    </div>

    <!-- Clients Table -->
    <div class="bg-white dark:bg-gray-800 border dark:border-gray-700 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-600 dark:text-white">CLIENTE</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-600 dark:text-white">RUT</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-600 dark:text-white">CONTACTO</th>
                        <th class="px-4 sm:px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-600 dark:text-white">ACCIONES</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200">
                    @forelse($clients as $client)
                        <tr>
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $client->name ?? 'Sin nombre' }}</div>
                                    @if($client->address)
                                        <div class="text-xs text-gray-600 dark:text-white">{{ Str::limit($client->address, 30) }}</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-700 dark:text-white">{{ $client->rut ?? 'N/A' }}</div>
                            </td>
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                <div>
                                    @if($client->email)
                                        <div class="text-sm text-gray-700 dark:text-white">{{ Str::limit($client->email, 20) }}</div>
                                    @endif
                                    @if($client->phone)
                                        <div class="text-xs text-gray-600 dark:text-white">{{ Str::limit($client->phone, 15) }}</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.clients.show', $client) ?? route('clients.show', $client) ?? '#' }}" class="text-blue-600 hover:text-blue-900" title="Ver">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.clients.edit', $client) ?? route('clients.edit', $client) ?? '#' }}" class="text-green-600 hover:text-green-900" title="Editar">
                                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l2.257-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.clients.destroy', $client) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar este cliente? Esta acción no se puede deshacer.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Eliminar">
                                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 sm:px-6 py-8 text-center text-sm text-gray-600 dark:text-white">
                                No se encontraron clientes
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($clients->hasPages())
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6" style="border-top: 1px solid #e5e7eb !important;">
            {{ $clients->links() }}
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    // Page Mobile Menu Button
    (function() {
        function initPageMenu() {
            const pageMenuButton = document.getElementById('page-mobile-menu-button');
            const sidebar = document.getElementById('sidebar');
            const mobileOverlay = document.getElementById('mobile-overlay');
            
            if (!pageMenuButton) {
                console.warn('Botón de menú móvil de página no encontrado');
                setTimeout(initPageMenu, 100);
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
                    const menuIcon = document.getElementById('page-menu-icon');
                    const closeIcon = document.getElementById('page-close-icon');
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
                    const menuIcon = document.getElementById('page-menu-icon');
                    const closeIcon = document.getElementById('page-close-icon');
                    if (menuIcon) menuIcon.classList.add('hidden');
                    if (closeIcon) closeIcon.classList.remove('hidden');
                    
                    // Bloquear scroll del body
                    document.body.style.overflow = 'hidden';
                }
            }
            
            // Event listener para el botón
            pageMenuButton.addEventListener('click', function(e) {
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
            document.addEventListener('DOMContentLoaded', initPageMenu);
        } else {
            // Si el DOM ya está listo, esperar un poco para asegurar que el layout haya inicializado
            setTimeout(initPageMenu, 50);
        }
    })();
</script>
@endpush
@endsection

