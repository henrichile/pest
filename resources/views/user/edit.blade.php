@extends('layouts.app')

@section('title', 'Editar Usuario')

@section('content')
<div class="space-y-4 sm:space-y-6 pt-12 md:pt-0">
    <!-- Header con hamburguesa y título -->
    <div class="mb-4 sm:mb-6">
        <!-- Primera fila: Hamburguesa + Título (móvil) -->
        <div class="flex items-center gap-3 mb-4 md:hidden">
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
                <h2 class="text-xl font-bold" style="color: #111827; font-weight: 700;">
                    Editar Usuario
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

        <div class="hidden md:flex md:items-center md:justify-between">
            <div class="min-w-0 flex-1">
                <h2 class="text-3xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight" style="color: #111827; font-weight: 700;">
                    Editar Usuario: {{ $user->name }}
                </h2>
                <p class="mt-1 text-sm" style="color: #6b7280;">
                    Modifica la información del usuario
                </p>
            </div>
            <div class="mt-4 md:mt-0 md:ml-4">
                <a href="{{ route('admin.users.show', $user) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium transition-colors" style="color: #374151; border-color: #d1d5db; hover:background: #f9fafb;">
                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    Volver al Usuario
                </a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white border dark:border-gray-700 rounded-lg p-6" style="border: 1px solid #e5e7eb !important;">
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf
            @method('PUT')

            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="mb-4 p-4 rounded-lg" style="background: #d1fae5; color: #065f46;">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 rounded-lg" style="background: #fee2e2; color: #991b1b;">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 p-4 rounded-lg" style="background: #fee2e2; color: #991b1b;">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nombre -->
                <div>
                    <label for="name" class="block text-sm font-medium mb-1" style="color: #374151;">
                        Nombre <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                           style="border: 1px solid #e5e7eb !important; color: #111827;">
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium mb-1" style="color: #374151;">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                           style="border: 1px solid #e5e7eb !important; color: #111827;">
                </div>

                <!-- Teléfono -->
                <div>
                    <label for="phone" class="block text-sm font-medium mb-1" style="color: #374151;">
                        Teléfono
                    </label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                           style="border: 1px solid #e5e7eb !important; color: #111827;">
                </div>

                <!-- Estado -->
                <div>
                    <label for="is_active" class="block text-sm font-medium mb-1" style="color: #374151;">
                        Estado <span class="text-red-500">*</span>
                    </label>
                    <select name="is_active" id="is_active" required
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                            style="border: 1px solid #e5e7eb !important; color: #111827;">
                        <option value="1" {{ old('is_active', $user->is_active) == 1 ? 'selected' : '' }}>Activo</option>
                        <option value="0" {{ old('is_active', $user->is_active) == 0 ? 'selected' : '' }}>Inactivo</option>
                    </select>
                </div>

                <!-- Contraseña (Opcional) -->
                <div>
                    <label for="password" class="block text-sm font-medium mb-1" style="color: #374151;">
                        Nueva Contraseña (opcional)
                    </label>
                    <input type="password" name="password" id="password" minlength="8"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                           style="border: 1px solid #e5e7eb !important; color: #111827;">
                    <p class="mt-1 text-xs" style="color: #6b7280;">Dejar en blanco para mantener la contraseña actual. Mínimo 8 caracteres si se cambia.</p>
                </div>

                <!-- Confirmar Contraseña -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium mb-1" style="color: #374151;">
                        Confirmar Nueva Contraseña
                    </label>
                    <input type="password" name="password_confirmation" id="password_confirmation" minlength="8"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                           style="border: 1px solid #e5e7eb !important; color: #111827;">
                </div>

                <!-- Dirección -->
                <div class="md:col-span-2">
                    <label for="address" class="block text-sm font-medium mb-1" style="color: #374151;">
                        Dirección
                    </label>
                    <textarea name="address" id="address" rows="3"
                              class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                              style="border: 1px solid #e5e7eb !important; color: #111827;">{{ old('address', $user->address) }}</textarea>
                </div>

                <!-- Roles -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-2" style="color: #374151;">
                        Roles
                    </label>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        @foreach($roles as $role)
                            <label class="flex items-center">
                                <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                                       {{ in_array($role->name, old('roles', $user->roles->pluck('name')->toArray())) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                <span class="ml-2 text-sm" style="color: #374151;">
                                    @if($role->name === 'technician')
                                        Técnico
                                    @elseif($role->name === 'super-admin')
                                        Super Admin
                                    @else
                                        {{ ucfirst($role->name) }}
                                    @endif
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Permisos (Opcional) -->
                @if($permissions->count() > 0)
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-2" style="color: #374151;">
                        Permisos (Opcional)
                    </label>
                    <div class="max-h-48 overflow-y-auto border border-gray-200 rounded-lg p-3" style="border: 1px solid #e5e7eb !important;">
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                            @foreach($permissions as $permission)
                                <label class="flex items-center">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                           {{ in_array($permission->name, old('permissions', $user->permissions->pluck('name')->toArray())) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                    <span class="ml-2 text-xs" style="color: #374151;">{{ $permission->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Submit Buttons -->
            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('admin.users.show', $user) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium transition-colors" style="color: #374151; border-color: #d1d5db; hover:background: #f9fafb;">
                    Cancelar
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white transition-colors" style="background: #22c55e; hover:background: #16a34a;">
                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                    Guardar Cambios
                </button>
            </div>
        </form>
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
