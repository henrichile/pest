@extends('layouts.app')

@section('title', 'Configuraciones')

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
                <h2 class="text-2xl font-bold" style="color: #111827; font-weight: 700;">
                    Configuraciones
                </h2>
            </div>
        </div>
        
        <!-- Segunda fila: Título completo (desktop) -->
        <div class="hidden md:block">
            <div class="min-w-0 flex-1">
                <h2 class="text-3xl font-bold leading-7 text-gray-900 sm:truncate sm:tracking-tight" style="color: #111827; font-weight: 700;">
                    Configuraciones
                </h2>
                <p class="mt-1 text-sm" style="color: #6b7280;">
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
    <div class="bg-white border dark:border-gray-700 rounded-lg p-6" style="border: 1px solid #e5e7eb !important;">
        <h3 class="text-lg font-semibold mb-4" style="color: #111827;">Datos de la Empresa</h3>
        
        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Company Name -->
                <div>
                    <label for="company_name" class="block text-sm font-medium mb-2" style="color: #374151;">Nombre de la Empresa *</label>
                    <input type="text" id="company_name" name="company_name" value="{{ old('company_name', $settings['company_name'] ?? config('app.name')) }}" required
                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                           style="border: 1px solid #e5e7eb !important; color: #111827;">
                </div>

                <!-- Company RUT -->
                <div>
                    <label for="company_rut" class="block text-sm font-medium mb-2" style="color: #374151;">RUT</label>
                    <input type="text" id="company_rut" name="company_rut" value="{{ old('company_rut', $settings['company_rut'] ?? '') }}"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                           style="border: 1px solid #e5e7eb !important; color: #111827;">
                </div>

                <!-- Company Address -->
                <div>
                    <label for="company_address" class="block text-sm font-medium mb-2" style="color: #374151;">Dirección</label>
                    <input type="text" id="company_address" name="company_address" value="{{ old('company_address', $settings['company_address'] ?? '') }}"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                           style="border: 1px solid #e5e7eb !important; color: #111827;">
                </div>

                <!-- Company Phone -->
                <div>
                    <label for="company_phone" class="block text-sm font-medium mb-2" style="color: #374151;">Teléfono</label>
                    <input type="text" id="company_phone" name="company_phone" value="{{ old('company_phone', $settings['company_phone'] ?? '') }}"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                           style="border: 1px solid #e5e7eb !important; color: #111827;">
                </div>

                <!-- Company Email -->
                <div>
                    <label for="company_email" class="block text-sm font-medium mb-2" style="color: #374151;">Email</label>
                    <input type="email" id="company_email" name="company_email" value="{{ old('company_email', $settings['company_email'] ?? '') }}"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                           style="border: 1px solid #e5e7eb !important; color: #111827;">
                </div>

                <!-- Company Logo -->
                <div>
                    <label for="company_logo" class="block text-sm font-medium mb-2" style="color: #374151;">Logo</label>
                    <input type="file" id="company_logo" name="company_logo" accept="image/jpeg,image/png,image/jpg,image/svg+xml"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                           style="border: 1px solid #e5e7eb !important; color: #111827;">
                    <p class="mt-1 text-xs" style="color: #6b7280;">Formatos permitidos: JPEG, PNG, JPG, SVG (máx. 2MB)</p>
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
    <div class="bg-white border dark:border-gray-700 rounded-lg p-6" style="border: 1px solid #e5e7eb !important;">
        <h3 class="text-lg font-semibold mb-4" style="color: #111827;">Configuración SMTP</h3>
        
        <form action="{{ route('admin.settings.smtp') }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- SMTP Host -->
                <div>
                    <label for="smtp_host" class="block text-sm font-medium mb-2" style="color: #374151;">Servidor SMTP *</label>
                    <input type="text" id="smtp_host" name="smtp_host" value="{{ old('smtp_host', config('mail.mailers.smtp.host')) }}" required
                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                           style="border: 1px solid #e5e7eb !important; color: #111827;">
                </div>

                <!-- SMTP Port -->
                <div>
                    <label for="smtp_port" class="block text-sm font-medium mb-2" style="color: #374151;">Puerto *</label>
                    <input type="number" id="smtp_port" name="smtp_port" value="{{ old('smtp_port', config('mail.mailers.smtp.port')) }}" required min="1" max="65535"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                           style="border: 1px solid #e5e7eb !important; color: #111827;">
                </div>

                <!-- SMTP Username -->
                <div>
                    <label for="smtp_username" class="block text-sm font-medium mb-2" style="color: #374151;">Usuario *</label>
                    <input type="text" id="smtp_username" name="smtp_username" value="{{ old('smtp_username', config('mail.mailers.smtp.username')) }}" required
                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                           style="border: 1px solid #e5e7eb !important; color: #111827;">
                </div>

                <!-- SMTP Password -->
                <div>
                    <label for="smtp_password" class="block text-sm font-medium mb-2" style="color: #374151;">Contraseña</label>
                    <input type="password" id="smtp_password" name="smtp_password" value="{{ old('smtp_password') }}"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                           style="border: 1px solid #e5e7eb !important; color: #111827;">
                    <p class="mt-1 text-xs" style="color: #6b7280;">Dejar en blanco para mantener la contraseña actual</p>
                </div>

                <!-- SMTP Encryption -->
                <div>
                    <label for="smtp_encryption" class="block text-sm font-medium mb-2" style="color: #374151;">Encriptación *</label>
                    <select id="smtp_encryption" name="smtp_encryption" required
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                            style="border: 1px solid #e5e7eb !important; color: #111827;">
                        <option value="" {{ old('smtp_encryption', config('mail.mailers.smtp.encryption')) == '' ? 'selected' : '' }}>Ninguna</option>
                        <option value="tls" {{ old('smtp_encryption', config('mail.mailers.smtp.encryption')) == 'tls' ? 'selected' : '' }}>TLS</option>
                        <option value="ssl" {{ old('smtp_encryption', config('mail.mailers.smtp.encryption')) == 'ssl' ? 'selected' : '' }}>SSL</option>
                    </select>
                </div>

                <!-- SMTP From Address -->
                <div>
                    <label for="smtp_from_address" class="block text-sm font-medium mb-2" style="color: #374151;">Email Remitente *</label>
                    <input type="email" id="smtp_from_address" name="smtp_from_address" value="{{ old('smtp_from_address', config('mail.from.address')) }}" required
                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                           style="border: 1px solid #e5e7eb !important; color: #111827;">
                </div>

                <!-- SMTP From Name -->
                <div>
                    <label for="smtp_from_name" class="block text-sm font-medium mb-2" style="color: #374151;">Nombre Remitente</label>
                    <input type="text" id="smtp_from_name" name="smtp_from_name" value="{{ old('smtp_from_name', config('mail.from.name')) }}"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
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

