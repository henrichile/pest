@extends("layouts.app")

@section("title", "Productos - Pest Controller SAT")
@section("page-title", "Gestión de Productos")

@section("content")
<div class="space-y-4 sm:space-y-6 pt-3 md:pt-0">
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
                <h2 class="text-2xl font-bold" class="text-gray-900 dark:text-white" style="font-weight: 700;">
                    Productos
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
        
        <!-- Botón Nuevo Producto (móvil) -->
        <div class="mb-4 md:hidden">
            <a href="{{ route("admin.products.create") }}" class="inline-flex items-center justify-center w-full px-4 py-2.5 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white transition-colors" style="background: #22c55e; hover:background: #16a34a;">
                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>Nuevo Producto</span>
            </a>
        </div>
        
        <!-- Segunda fila: Título completo (desktop) -->
        <div class="hidden md:flex md:items-center md:justify-between">
            <div class="min-w-0 flex-1">
                <h2 class="text-2xl sm:text-3xl font-bold leading-7 text-gray-900 sm:truncate sm:tracking-tight" class="text-gray-900 dark:text-white" style="font-weight: 700;">
                    Productos
                </h2>
                <p class="mt-1 text-xs sm:text-sm" class="text-gray-600 dark:text-gray-300">
                    Gestiona el inventario de productos
                </p>
            </div>
            <div class="mt-3 sm:mt-4 md:mt-0 md:ml-4">
                <a href="{{ route("admin.products.create") }}" class="inline-flex items-center justify-center w-full sm:w-auto px-3 sm:px-4 py-2 border border-transparent rounded-lg shadow-sm text-xs sm:text-sm font-medium text-white transition-colors" style="background: #22c55e; hover:background: #16a34a;">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1.5 sm:mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span class="hidden sm:inline">Nuevo Producto</span>
                    <span class="sm:hidden">Nuevo</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Mobile View (Cards) -->
    <div class="md:hidden space-y-4">
        @forelse($products as $product)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 space-y-3">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">{{ $product->name }}</h3>
                        <p class="text-sm text-gray-500">{{ $product->active_ingredient }}</p>
                    </div>
                    <span class="px-2 py-1 text-xs font-medium rounded bg-blue-100 text-blue-800">
                        {{ ucfirst($product->service_type) }}
                    </span>
                </div>
                
                <div class="grid grid-cols-2 gap-2 text-sm">
                    <div>
                        <span class="text-gray-500 block text-xs">Stock</span>
                        <span class="font-medium">{{ $product->stock }} {{ $product->unit }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 block text-xs">Registro</span>
                        <span class="font-medium text-xs block">SAG: {{ $product->sag_registration ?? 'N/A' }}</span>
                        <span class="font-medium text-xs block">ISP: {{ $product->isp_registration ?? 'N/A' }}</span>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-3 border-t border-gray-100">
                    <a href="{{ route("admin.products.show", $product) }}" class="text-green-600 hover:text-green-800 text-sm font-medium">Ver</a>
                    <a href="{{ route("admin.products.edit", $product) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Editar</a>
                    <form method="POST" action="{{ route("admin.products.destroy", $product) }}" class="inline">
                        @csrf
                        @method("DELETE")
                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium" onclick="return confirm('¿Está seguro de eliminar este producto?')">Eliminar</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="text-center py-8 text-gray-500 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                No hay productos registrados
            </div>
        @endforelse
    </div>

    <!-- Desktop View (Table) -->
    <div class="hidden md:block bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ingrediente Activo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo Servicio</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registro</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-white divide-y divide-gray-200">
                    @forelse($products as $product)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $product->active_ingredient }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ ucfirst($product->service_type) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $product->stock }} {{ $product->unit }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div>SAG: {{ $product->sag_registration ?? 'N/A' }}</div>
                            <div>ISP: {{ $product->isp_registration ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                            <a href="{{ route("admin.products.show", $product) }}" 
                               class="text-green-600 hover:text-green-900">Ver</a>
                            <a href="{{ route("admin.products.edit", $product) }}" 
                               class="text-blue-600 hover:text-blue-900">Editar</a>
                            <form method="POST" action="{{ route("admin.products.destroy", $product) }}" class="inline">
                                @csrf
                                @method("DELETE")
                                <button type="submit" class="text-red-600 hover:text-red-900" 
                                        onclick="return confirm('¿Está seguro de eliminar este producto?')">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            No hay productos registrados
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($products->hasPages())
        <div class="px-6 py-3 border-t border-gray-200">
            {{ $products->links() }}
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
