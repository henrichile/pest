@extends('layouts.app')

@section('title', 'Configuraciones')

@section('content')
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
                    Configuraciones
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
        <div class="hidden md:block">
            <div class="min-w-0 flex-1">
                <h2 class="text-3xl font-bold leading-7 text-gray-900 sm:truncate sm:tracking-tight" class="text-gray-900 dark:text-white" style="font-weight: 700;">
                    Configuraciones
                </h2>
                <p class="mt-1 text-sm" class="text-gray-600 dark:text-gray-300">
                    Administra los datos de la empresa, logo y configuración SMTP
                </p>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-4" style="border: 1px solid #22c55e !important;">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-4" style="border: 1px solid #ef4444 !important;">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Company Settings -->
    <div class="bg-white dark:bg-gray-800 border dark:border-gray-700 rounded-lg p-6" style="border: 1px solid #e5e7eb !important;">
        <h3 class="text-lg font-semibold mb-4" class="text-gray-900 dark:text-white">Datos de la Empresa</h3>
        
        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Company Name -->
                <div>
                    <label for="company_name" class="block text-sm font-medium mb-2" class="text-gray-700 dark:text-gray-300">Nombre de la Empresa *</label>
                    <input type="text" id="company_name" name="company_name" value="{{ old('company_name', $settings['company_name'] ?? config('app.name')) }}" required
                           class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                           style="border: 1px solid #e5e7eb !important; color: #111827;">
                </div>

                <!-- Company RUT -->
                <div>
                    <label for="company_rut" class="block text-sm font-medium mb-2" class="text-gray-700 dark:text-gray-300">RUT</label>
                    <input type="text" id="company_rut" name="company_rut" value="{{ old('company_rut', $settings['company_rut'] ?? '') }}"
                           class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                           style="border: 1px solid #e5e7eb !important; color: #111827;">
                </div>

                <!-- Company Address -->
                <div>
                    <label for="company_address" class="block text-sm font-medium mb-2" class="text-gray-700 dark:text-gray-300">Dirección</label>
                    <input type="text" id="company_address" name="company_address" value="{{ old('company_address', $settings['company_address'] ?? '') }}"
                           class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                           style="border: 1px solid #e5e7eb !important; color: #111827;">
                </div>

                <!-- Company Phone -->
                <div>
                    <label for="company_phone" class="block text-sm font-medium mb-2" class="text-gray-700 dark:text-gray-300">Teléfono</label>
                    <input type="text" id="company_phone" name="company_phone" value="{{ old('company_phone', $settings['company_phone'] ?? '') }}"
                           class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                           style="border: 1px solid #e5e7eb !important; color: #111827;">
                </div>

                <!-- Company Email -->
                <div>
                    <label for="company_email" class="block text-sm font-medium mb-2" class="text-gray-700 dark:text-gray-300">Email</label>
                    <input type="email" id="company_email" name="company_email" value="{{ old('company_email', $settings['company_email'] ?? '') }}"
                           class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                           style="border: 1px solid #e5e7eb !important; color: #111827;">
                </div>

                <!-- Company Logo -->
                <div>
                    <label for="company_logo" class="block text-sm font-medium mb-2" class="text-gray-700 dark:text-gray-300">Logo</label>
                    <input type="file" id="company_logo" name="company_logo" accept="image/jpeg,image/png,image/jpg,image/svg+xml"
                           class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                           style="border: 1px solid #e5e7eb !important; color: #111827;">
                    <p class="mt-1 text-xs" class="text-gray-600 dark:text-gray-300">Formatos permitidos: JPEG, PNG, JPG, SVG (máx. 2MB)</p>
                    @if(file_exists(public_path('logo.png')) || file_exists(public_path('logo.jpg')) || file_exists(public_path('logo.svg')))
                        <p class="mt-2 text-sm" style="color: #22c55e;">Logo actual: 
                            @if(file_exists(public_path('logo.png')))
                                <img src="{{ asset('logo.png') }}" alt="Logo" class="inline-block h-8 mt-1">
                            @elseif(file_exists(public_path('logo.jpg')))
                                <img src="{{ asset('logo.jpg') }}" alt="Logo" class="inline-block h-8 mt-1">
                            @elseif(file_exists(public_path('logo.svg')))
                                <img src="{{ asset('logo.svg') }}" alt="Logo" class="inline-block h-8 mt-1">
                            @endif
                        </p>
                    @endif
                </div>
            </div>

            <div class="mt-6">
                <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
                    Guardar Datos de la Empresa
                </button>
            </div>
        </form>
    </div>

    <!-- SMTP Settings -->
    <div class="bg-white dark:bg-gray-800 border dark:border-gray-700 rounded-lg p-6" style="border: 1px solid #e5e7eb !important;">
        <h3 class="text-lg font-semibold mb-4" class="text-gray-900 dark:text-white">Configuración SMTP</h3>
        
        <form action="{{ route('admin.settings.smtp') }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- SMTP Host -->
                <div>
                    <label for="smtp_host" class="block text-sm font-medium mb-2" class="text-gray-700 dark:text-gray-300">Servidor SMTP *</label>
                    <input type="text" id="smtp_host" name="smtp_host" value="{{ old('smtp_host', config('mail.mailers.smtp.host')) }}" required
                           class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                           style="border: 1px solid #e5e7eb !important; color: #111827;">
                </div>

                <!-- SMTP Port -->
                <div>
                    <label for="smtp_port" class="block text-sm font-medium mb-2" class="text-gray-700 dark:text-gray-300">Puerto *</label>
                    <input type="number" id="smtp_port" name="smtp_port" value="{{ old('smtp_port', config('mail.mailers.smtp.port')) }}" required min="1" max="65535"
                           class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                           style="border: 1px solid #e5e7eb !important; color: #111827;">
                </div>

                <!-- SMTP Username -->
                <div>
                    <label for="smtp_username" class="block text-sm font-medium mb-2" class="text-gray-700 dark:text-gray-300">Usuario *</label>
                    <input type="text" id="smtp_username" name="smtp_username" value="{{ old('smtp_username', config('mail.mailers.smtp.username')) }}" required
                           class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                           style="border: 1px solid #e5e7eb !important; color: #111827;">
                </div>

                <!-- SMTP Password -->
                <div>
                    <label for="smtp_password" class="block text-sm font-medium mb-2" class="text-gray-700 dark:text-gray-300">Contraseña</label>
                    <input type="password" id="smtp_password" name="smtp_password" value="{{ old('smtp_password') }}"
                           class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                           style="border: 1px solid #e5e7eb !important; color: #111827;">
                    <p class="mt-1 text-xs" class="text-gray-600 dark:text-gray-300">Dejar en blanco para mantener la contraseña actual</p>
                </div>

                <!-- SMTP Encryption -->
                <div>
                    <label for="smtp_encryption" class="block text-sm font-medium mb-2" class="text-gray-700 dark:text-gray-300">Encriptación *</label>
                    <select id="smtp_encryption" name="smtp_encryption" required
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                            style="border: 1px solid #e5e7eb !important; color: #111827;">
                        <option value="" {{ old('smtp_encryption', config('mail.mailers.smtp.encryption')) == '' ? 'selected' : '' }}>Ninguna</option>
                        <option value="tls" {{ old('smtp_encryption', config('mail.mailers.smtp.encryption')) == 'tls' ? 'selected' : '' }}>TLS</option>
                        <option value="ssl" {{ old('smtp_encryption', config('mail.mailers.smtp.encryption')) == 'ssl' ? 'selected' : '' }}>SSL</option>
                    </select>
                </div>

                <!-- SMTP From Address -->
                <div>
                    <label for="smtp_from_address" class="block text-sm font-medium mb-2" class="text-gray-700 dark:text-gray-300">Email Remitente *</label>
                    <input type="email" id="smtp_from_address" name="smtp_from_address" value="{{ old('smtp_from_address', config('mail.from.address')) }}" required
                           class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                           style="border: 1px solid #e5e7eb !important; color: #111827;">
                </div>

                <!-- SMTP From Name -->
                <div>
                    <label for="smtp_from_name" class="block text-sm font-medium mb-2" class="text-gray-700 dark:text-gray-300">Nombre Remitente</label>
                    <input type="text" id="smtp_from_name" name="smtp_from_name" value="{{ old('smtp_from_name', config('mail.from.name')) }}"
                           class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                           style="border: 1px solid #e5e7eb !important; color: #111827;">
                </div>
            </div>

            <div class="mt-6">
                <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
                    Guardar Configuración SMTP
                </button>
            </div>
        </form>
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
