@extends('layouts.app')

@section('title', 'Perfil')

@section('content')
<div class="container mx-auto px-4 py-8 pt-3 md:pt-0">
    <div class="max-w-2xl mx-auto">
        <!-- Header con hamburguesa y título (Móvil) -->
        <div class="mb-4 md:hidden" style="padding-top: 2.5rem;">
            <div class="flex items-center gap-3">
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
                    <h2 class="text-xl font-bold" style="color: #111827; font-weight: 700;">
                        Mi Perfil
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
        </div>

        <div class="hidden md:block">
            @include('technician.partials.header', [
                'title' => 'Mi Perfil',
                'searchPlaceholder' => 'Buscar...',
                'pageId' => 'profile'
            ])
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nombre</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $user->name }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $user->email }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Rol</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $user->roles->first()->name ?? 'Sin rol asignado' }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Fecha de registro</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('d/m/Y') }}</p>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <a href="{{ route('technician.dashboard') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Volver al Dashboard
                </a>
            </div>
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
                const currentTransform = sidebar.style.transform || '';
                // Asumimos cerrado si tiene -100% o si no tiene la clase translate-x-0
                const isClosed = currentTransform.includes('-100%') || !sidebar.classList.contains('translate-x-0');
                
                if (isClosed) {
                    // Abrir
                    sidebar.classList.remove('-translate-x-full');
                    sidebar.classList.add('translate-x-0');
                    sidebar.style.transform = 'translateX(0)';
                    
                    // Forzar estilos críticos
                    let styleTag = document.getElementById('mobile-menu-override-style');
                    if (!styleTag) {
                        styleTag = document.createElement('style');
                        styleTag.id = 'mobile-menu-override-style';
                        document.head.appendChild(styleTag);
                    }
                    styleTag.textContent = `#sidebar { transform: translateX(0) !important; display: flex !important; z-index: 9999 !important; position: fixed !important; left: 0 !important; top: 0 !important; height: 100vh !important; }`;
                    
                    if (mobileOverlay) {
                        mobileOverlay.classList.remove('hidden');
                        mobileOverlay.style.display = 'block';
                    }
                    
                    const menuIcon = document.getElementById('page-menu-icon');
                    const closeIcon = document.getElementById('page-close-icon');
                    if (menuIcon) menuIcon.classList.add('hidden');
                    if (closeIcon) closeIcon.classList.remove('hidden');
                    
                    document.body.style.overflow = 'hidden';
                } else {
                    // Cerrar
                    sidebar.classList.remove('translate-x-0');
                    sidebar.classList.add('-translate-x-full');
                    sidebar.style.transform = 'translateX(-100%)';
                    
                    const styleTag = document.getElementById('mobile-menu-override-style');
                    if (styleTag) styleTag.remove();
                    
                    if (mobileOverlay) {
                        mobileOverlay.classList.add('hidden');
                        mobileOverlay.style.display = 'none';
                    }
                    
                    const menuIcon = document.getElementById('page-menu-icon');
                    const closeIcon = document.getElementById('page-close-icon');
                    if (menuIcon) menuIcon.classList.remove('hidden');
                    if (closeIcon) closeIcon.classList.add('hidden');
                    
                    document.body.style.overflow = '';
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
            initPageMenu();
        }
    })();
</script>
@endpush

