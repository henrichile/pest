@extends('layouts.app')

@section('title', 'Mi Perfil')

@section('content')
<div class="space-y-6 pt-3 md:pt-0">
    <!-- Header -->
    <div class="mb-6">
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
        
        <!-- Segunda fila: Título completo (desktop) -->
        <div class="hidden md:flex md:items-center md:justify-between">
            <div class="min-w-0 flex-1">
                <h2 class="text-3xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight" style="color: #111827; font-weight: 700;">
                    Mi Perfil
                </h2>
                <p class="mt-1 text-sm" style="color: #6b7280;">
                    Gestiona tu información personal y configuración de cuenta
                </p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-md bg-green-50 p-4 mb-6" style="background: #f0fdf4; border: 1px solid #22c55e;">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" style="color: #22c55e;" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.236 4.53L7.53 10.53a.75.75 0 00-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium" style="color: #166534;">
                        {{ session('success') }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-md bg-red-50 p-4 mb-6" style="background: #fef2f2; border: 1px solid #ef4444;">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" style="color: #ef4444;" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium" style="color: #991b1b;">
                        {{ session('error') }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Profile Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Información Personal -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm" style="border: 1px solid #e5e7eb; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);">
                <div class="px-6 py-4 border-b border-gray-200" style="border-bottom: 1px solid #e5e7eb;">
                    <h3 class="text-lg font-semibold" style="color: #111827;">Información Personal</h3>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label for="name" class="block text-sm font-medium mb-2" style="color: #111827;">Nombre</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500" 
                                    style="border: 1px solid #d1d5db; border-radius: 8px;">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium mb-2" style="color: #111827;">Correo Electrónico</label>
                                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                    style="border: 1px solid #d1d5db; border-radius: 8px;">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-medium mb-2" style="color: #111827;">Teléfono</label>
                                <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                    style="border: 1px solid #d1d5db; border-radius: 8px;">
                                @error('phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="timezone" class="block text-sm font-medium mb-2" style="color: #111827;">Zona Horaria</label>
                                <select name="timezone" id="timezone"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                    style="border: 1px solid #d1d5db; border-radius: 8px;">
                                    <option value="America/Santiago" {{ $user->timezone === 'America/Santiago' ? 'selected' : '' }}>Santiago (GMT-3)</option>
                                    <option value="America/Lima" {{ $user->timezone === 'America/Lima' ? 'selected' : '' }}>Lima (GMT-5)</option>
                                    <option value="America/Mexico_City" {{ $user->timezone === 'America/Mexico_City' ? 'selected' : '' }}>Ciudad de México (GMT-6)</option>
                                    <option value="America/Bogota" {{ $user->timezone === 'America/Bogota' ? 'selected' : '' }}>Bogotá (GMT-5)</option>
                                    <option value="America/Buenos_Aires" {{ $user->timezone === 'America/Buenos_Aires' ? 'selected' : '' }}>Buenos Aires (GMT-3)</option>
                                </select>
                                @error('timezone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="submit" 
                                class="px-6 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors"
                                style="background: #22c55e; color: white; border-radius: 8px;">
                                Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Cambiar Contraseña -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm" style="border: 1px solid #e5e7eb; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);">
                <div class="px-6 py-4 border-b border-gray-200" style="border-bottom: 1px solid #e5e7eb;">
                    <h3 class="text-lg font-semibold" style="color: #111827;">Cambiar Contraseña</h3>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('profile.change-password') }}">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label for="current_password" class="block text-sm font-medium mb-2" style="color: #111827;">Contraseña Actual</label>
                                <input type="password" name="current_password" id="current_password" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                    style="border: 1px solid #d1d5db; border-radius: 8px;">
                                @error('current_password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="new_password" class="block text-sm font-medium mb-2" style="color: #111827;">Nueva Contraseña</label>
                                <input type="password" name="new_password" id="new_password" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                    style="border: 1px solid #d1d5db; border-radius: 8px;">
                                @error('new_password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="new_password_confirmation" class="block text-sm font-medium mb-2" style="color: #111827;">Confirmar Nueva Contraseña</label>
                                <input type="password" name="new_password_confirmation" id="new_password_confirmation" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                    style="border: 1px solid #d1d5db; border-radius: 8px;">
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="submit" 
                                class="px-6 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors"
                                style="background: #22c55e; color: white; border-radius: 8px;">
                                Cambiar Contraseña
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Column: Stats and Info -->
        <div class="space-y-6">
            <!-- Profile Card -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm" style="border: 1px solid #e5e7eb; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);">
                <div class="p-6 text-center">
                    <img class="h-24 w-24 rounded-full mx-auto mb-4 border-4 border-gray-200" 
                         src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQ?:jEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" 
                         alt="{{ $user->name }}">
                    <h3 class="text-xl font-semibold mb-1" style="color: #111827;">{{ $user->name }}</h3>
                    <p class="text-sm mb-4" style="color: #6b7280;">{{ $user->email }}</p>
                    
                    @if($roles->count() > 0)
                        <div class="flex flex-wrap justify-center gap-2 mb-4">
                            @foreach($roles as $role)
                                <span class="px-3 py-1 text-xs font-semibold rounded-full" 
                                      style="background: #dcfce7; color: #166534;">
                                    {{ ucfirst($role->name) }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Statistics -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm" style="border: 1px solid #e5e7eb; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);">
                <div class="px-6 py-4 border-b border-gray-200" style="border-bottom: 1px solid #e5e7eb;">
                    <h3 class="text-lg font-semibold" style="color: #111827;">Estadísticas</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm" style="color: #6b7280;">Órdenes de Trabajo</span>
                        <span class="text-lg font-semibold" style="color: #111827;">{{ $stats['total_work_orders'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm" style="color: #6b7280;">Completadas</span>
                        <span class="text-lg font-semibold" style="color: #22c55e;">{{ $stats['completed_work_orders'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm" style="color: #6b7280;">Sesiones</span>
                        <span class="text-lg font-semibold" style="color: #111827;">{{ $stats['total_sessions'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm" style="color: #6b7280;">Tratamientos</span>
                        <span class="text-lg font-semibold" style="color: #111827;">{{ $stats['total_treatments'] ?? 0 }}</span>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            @if($recentActivities->count() > 0)
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm" style="border: 1px solid #e5e7eb; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);">
                <div class="px-6 py-4 border-b border-gray-200" style="border-bottom: 1px solid #e5e7eb;">
                    <h3 class="text-lg font-semibold" style="color: #111827;">Actividad Reciente</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($recentActivities->take(5) as $activity)
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0 w-2 h-2 rounded-full mt-2" style="background: #22c55e;"></div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm" style="color: #111827;">{{ $activity->description ?? 'Actividad' }}</p>
                                    <p class="text-xs mt-1" style="color: #9ca3af;">{{ $activity->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

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

