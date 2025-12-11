@extends("layouts.app")

@section("title", "Gestión de Usuarios")

@section("content")
<div class="space-y-6 pt-3 md:pt-0">
    <!-- Header con hamburguesa y título -->
    <div class="mb-4 sm:mb-6">
        <!-- Primera fila: Hamburguesa + Título (móvil) -->
        <div class="flex items-center gap-3 mb-4 md:hidden" style="padding-top: 2.5rem;">
            <!-- Hamburguesa (solo móvil) -->
            <button id="page-mobile-menu-button" class="flex-shrink-0 p-2 rounded-lg bg-white border border-gray-300 shadow-md hover:bg-gray-50 transition-colors cursor-pointer" style="z-index: 100;">
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
                    Gestión de Usuarios
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
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="hidden md:flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Gestión de Usuarios</h1>
            <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                <svg class="w-5 h-5 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"/>
                </svg>
                Nuevo Usuario
            </button>
        </div>
        
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <h3 class="text-lg font-semibold text-blue-800 mb-2">Gestión de Usuarios</h3>
            <p class="text-blue-700">Aquí podrás crear, editar y administrar usuarios del sistema, asignando roles y permisos específicos.</p>
            <ul class="mt-3 text-blue-700 space-y-1">
                <li>• Crear nuevos usuarios con diferentes roles</li>
                <li>• Asignar permisos específicos</li>
                <li>• Gestionar estado activo/inactivo</li>
                <li>• Cambiar contraseñas y datos de usuario</li>
            </ul>
        </div>
        
        <div class="text-center py-8">
            <p class="text-gray-500">Funcionalidad en desarrollo...</p>
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
                            max-height: 100vh !important;
                            overflow-y: auto !important;
                            -webkit-overflow-scrolling: touch !important;
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
