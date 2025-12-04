@extends("layouts.app")

@section("title", "Editar Producto - Pest Controller SAT")
@section("page-title", "Editar Producto")

@section("content")
<div class="space-y-4 sm:space-y-6 pt-3 md:pt-0">
    <!-- Header con hamburguesa y título -->
    <div class="mb-4 sm:mb-6">
        <!-- Primera fila: Hamburguesa + Título (móvil) -->
        <div class="flex items-center gap-3 mb-4 md:hidden" style="padding-top: 2.5rem;">
            <!-- Hamburguesa (solo móvil) -->
            <button id="page-mobile-menu-button" class="flex-shrink-0 p-2 rounded-lg bg-white border border-gray-300 shadow-md hover:bg-gray-50 transition-colors" style="z-index: 1000; position: relative;">
                <svg id="page-menu-icon" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="color: #111827;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
                <svg id="page-close-icon" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="color: #111827;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            
            <!-- Título -->
            <div class="flex-1">
                <h2 class="text-2xl font-bold" style="color: #111827; font-weight: 700;">
                    Editar Producto
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
            </div>
        </div>
        
        <!-- Segunda fila: Título completo (desktop) -->
        <div class="hidden md:block">
            <h2 class="text-2xl sm:text-3xl font-bold leading-7 text-gray-900 sm:truncate sm:tracking-tight" style="color: #111827; font-weight: 700;">
                Editar Producto
            </h2>
            <p class="mt-1 text-xs sm:text-sm" style="color: #6b7280;">
                Modifica la información del producto
            </p>
        </div>
    </div>

    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-900">Editar Producto</h2>
                <p class="text-gray-600">Modifica la información del producto</p>
            </div>

        <form method="POST" action="{{ route("admin.products.update", $product) }}" class="space-y-6">
            @csrf
            @method("PUT")
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nombre -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nombre Comercial *</label>
                    <input type="text" id="name" name="name" required
                           value="{{ old("name", $product->name) }}"
                           placeholder="Ingrese el nombre comercial"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 @error("name") border-red-500 @enderror">
                    @error("name")
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Ingrediente Activo -->
                <div>
                    <label for="active_ingredient" class="block text-sm font-medium text-gray-700 mb-2">Ingrediente Activo *</label>
                    <input type="text" id="active_ingredient" name="active_ingredient" required
                           value="{{ old("active_ingredient", $product->active_ingredient) }}"
                           placeholder="Ej: Brodifacoum, Deltametrina"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 @error("active_ingredient") border-red-500 @enderror">
                    @error("active_ingredient")
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tipo de Servicio -->
                <div>
                    <label for="service_type" class="block text-sm font-medium text-gray-700 mb-2">Tipo de Servicio *</label>
                    <select id="service_type" name="service_type" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 @error("service_type") border-red-500 @enderror">
                        <option value="">Seleccione el tipo</option>
                        <option value="desratizacion" {{ old("service_type", $product->service_type) == "desratizacion" ? "selected" : "" }}>Desratización</option>
                        <option value="desinsectacion" {{ old("service_type", $product->service_type) == "desinsectacion" ? "selected" : "" }}>Desinsectación</option>
                        <option value="sanitizacion" {{ old("service_type", $product->service_type) == "sanitizacion" ? "selected" : "" }}>Sanitización</option>
                    </select>
                    @error("service_type")
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Stock -->
                <div>
                    <label for="stock" class="block text-sm font-medium text-gray-700 mb-2">Stock Actual *</label>
                    <input type="number" id="stock" name="stock" required min="0"
                           value="{{ old("stock", $product->stock) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 @error("stock") border-red-500 @enderror">
                    @error("stock")
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Unidad -->
                <div>
                    <label for="unit" class="block text-sm font-medium text-gray-700 mb-2">Unidad *</label>
                    <select id="unit" name="unit" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 @error("unit") border-red-500 @enderror">
                        <option value="">Seleccione la unidad</option>
                        <option value="kg" {{ old("unit", $product->unit) == "kg" ? "selected" : "" }}>Kilogramos (kg)</option>
                        <option value="litros" {{ old("unit", $product->unit) == "litros" ? "selected" : "" }}>Litros</option>
                        <option value="unidades" {{ old("unit", $product->unit) == "unidades" ? "selected" : "" }}>Unidades</option>
                        <option value="gramos" {{ old("unit", $product->unit) == "gramos" ? "selected" : "" }}>Gramos (g)</option>
                    </select>
                    @error("unit")
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Registro SAG -->
                <div>
                    <label for="sag_registration" class="block text-sm font-medium text-gray-700 mb-2">Registro SAG</label>
                    <input type="text" id="sag_registration" name="sag_registration"
                           value="{{ old("sag_registration", $product->sag_registration) }}"
                           placeholder="Ej: SAG-12345"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 @error("sag_registration") border-red-500 @enderror">
                    @error("sag_registration")
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Registro ISP -->
                <div>
                    <label for="isp_registration" class="block text-sm font-medium text-gray-700 mb-2">Registro ISP</label>
                    <input type="text" id="isp_registration" name="isp_registration"
                           value="{{ old("isp_registration", $product->isp_registration) }}"
                           placeholder="Ej: ISP-54321"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 @error("isp_registration") border-red-500 @enderror">
                    @error("isp_registration")
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Descripción -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                <textarea id="description" name="description" rows="4"
                          placeholder="Descripción del producto y uso recomendado"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 @error("description") border-red-500 @enderror">{{ old("description", $product->description) }}</textarea>
                @error("description")
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Botones -->
            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route("admin.products.show", $product) }}" 
                   class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancelar
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                    Actualizar Producto
                </button>
            </div>
        </form>
    </div>
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
