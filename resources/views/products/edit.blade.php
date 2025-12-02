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
            <button id="page-mobile-menu-button" class="flex-shrink-0 p-2 rounded-lg bg-white border border-gray-300 shadow-md hover:bg-gray-50 transition-colors" style="z-index: 50;">
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
