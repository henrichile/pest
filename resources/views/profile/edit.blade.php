@extends("layouts.app")

@section("content")
<div class="max-w-2xl mx-auto pt-3 md:pt-0">
    <!-- Header con hamburguesa y título (Móvil) -->
    <div class="mb-4 md:hidden px-4" style="padding-top: 2.5rem;">
        <div class="flex items-center gap-3">
            <!-- Hamburguesa -->
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
                <h2 class="text-xl font-bold" style="color: #111827; font-weight: 700;">
                    Editar Perfil
                </h2>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
            <h1 class="text-2xl font-bold text-white">Editar Perfil</h1>
        </div>

        <!-- Content -->
        <div class="p-6">
            <form method="POST" action="{{ route("profile.update") }}">
                @csrf
                @method("PUT")

                <!-- Información Personal -->
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Información Personal</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nombre</label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old("name", $user->name) }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 @error("name") border-red-500 @enderror"
                                   required>
                            @error("name")
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old("email", $user->email) }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 @error("email") border-red-500 @enderror"
                                   required>
                            @error("email")
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Información de Roles -->
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Información de Roles</h2>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">Roles Asignados</p>
                                <div class="mt-1">
                                    @forelse($user->roles as $role)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mr-2">
                                            {{ $role->name }}
                                        </span>
                                    @empty
                                        <span class="text-gray-500 text-sm">Sin roles asignados</span>
                                    @endforelse
                                </div>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Miembro desde</p>
                                <p class="text-sm font-medium text-gray-900">{{ $user->created_at->format("d/m/Y") }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cambiar Contraseña -->
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Cambiar Contraseña</h2>
                    <p class="text-sm text-gray-600 mb-4">Deja estos campos vacíos si no quieres cambiar la contraseña.</p>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Contraseña Actual</label>
                            <input type="password" 
                                   id="current_password" 
                                   name="current_password"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 @error("current_password") border-red-500 @enderror">
                            @error("current_password")
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Nueva Contraseña</label>
                                <input type="password" 
                                       id="password" 
                                       name="password"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 @error("password") border-red-500 @enderror">
                                @error("password")
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirmar Nueva Contraseña</label>
                                <input type="password" 
                                       id="password_confirmation" 
                                       name="password_confirmation"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                    <a href="{{ url()->previous() }}" 
                       class="inline-flex items-center px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Cancelar
                    </a>
                    
                    <button type="submit" 
                            class="inline-flex items-center px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Actualizar Perfil
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

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

