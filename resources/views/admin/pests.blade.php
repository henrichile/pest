@extends('layouts.app')

@section('title', 'Catálogo de Plagas')

@section('content')
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
                    Catálogo de Plagas
                </h2>
            </div>

            <!-- Iconos Header Móvil -->
            <div class="flex items-center gap-3">
                <!-- Botón Nueva Plaga (Móvil) -->
                <a href="{{ route('admin.pests.create') }}" class="flex items-center justify-center h-9 w-9 rounded-full bg-green-600 text-white shadow-sm hover:bg-green-700 transition-colors" aria-label="Crear Nueva Plaga">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                </a>

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
                <h2 class="text-2xl sm:text-3xl font-bold leading-7 text-gray-900 sm:truncate sm:tracking-tight" style="color: #111827; font-weight: 700;">
                    Catálogo de Plagas
                </h2>
                <p class="mt-1 text-xs sm:text-sm" style="color: #6b7280;">
                    Información sobre plagas comunes
                </p>
            </div>
            <div class="mt-3 sm:mt-4 md:mt-0 md:ml-4">
                <a href="{{ route('admin.pests.create') }}" class="inline-flex items-center justify-center w-full sm:w-auto px-3 sm:px-4 py-2 border border-transparent rounded-lg shadow-sm text-xs sm:text-sm font-medium text-white transition-colors" style="background: #22c55e; hover:background: #16a34a;">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1.5 sm:mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span class="hidden sm:inline">Crear Nueva Plaga</span>
                    <span class="sm:hidden">Nueva Plaga</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="mb-4 sm:mb-6">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                <svg class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="color: #6b7280;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>
            </div>
            <input type="text" id="search-input" class="block w-full pr-3 py-2 sm:py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" style="border: 1px solid #e5e7eb !important; color: #111827; padding-left: 2.75rem !important;" placeholder="Buscar por nombre..." value="{{ request('search') }}">
        </div>
    </div>

    <!-- Pests Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        @forelse($pests as $pest)
            <div class="bg-white border dark:border-gray-700 rounded-lg overflow-hidden transition-shadow hover:shadow-md cursor-pointer pest-card" style="border: 1px solid #e5e7eb !important;" data-pest-id="{{ $pest->id }}">
                <div class="p-5">
                    <!-- Pest Name -->
                    <div class="mb-3">
                        <h3 class="text-lg font-bold mb-1" style="color: #111827;">{{ $pest->name ?? 'Sin nombre' }}</h3>
                    </div>

                    <!-- Category Tag -->
                    <div class="mb-3">
                        @php
                            $categoryColors = [
                                'Roedores' => ['bg' => '#fef3c7', 'text' => '#92400e'],
                                'Cucarachas' => ['bg' => '#dbeafe', 'text' => '#1e40af'],
                                'Moscas' => ['bg' => '#e0e7ff', 'text' => '#3730a3'],
                                'Termitas' => ['bg' => '#fce7f3', 'text' => '#9f1239'],
                                'Hormigas' => ['bg' => '#f3e8ff', 'text' => '#6b21a8'],
                                'Aves' => ['bg' => '#f0fdf4', 'text' => '#166534'],
                                'Arañas' => ['bg' => '#fce7f3', 'text' => '#9f1239'],
                                'Otros' => ['bg' => '#f3f4f6', 'text' => '#374151'],
                            ];
                            $color = $categoryColors[$pest->category] ?? ['bg' => '#f3f4f6', 'text' => '#374151'];
                        @endphp
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-medium" style="background: {{ $color['bg'] }}; color: {{ $color['text'] }};">
                            {{ ucfirst($pest->category ?? 'otro') }}
                        </span>
                    </div>

                    <!-- Description / Technical Notes -->
                    @if($pest->technical_notes)
                        <p class="text-sm mb-4" style="color: #6b7280; line-height: 1.5;">
                            {{ Str::limit($pest->technical_notes, 120) }}
                        </p>
                    @endif

                    <!-- Control Methods / Treatment -->
                    @if($pest->control_methods)
                        <div>
                            <p class="text-sm font-semibold mb-2" style="color: #22c55e;">Tratamiento:</p>
                            <ul class="list-disc list-inside text-sm space-y-1" style="color: #6b7280;">
                                @if(is_array($pest->control_methods))
                                    @foreach(array_slice($pest->control_methods, 0, 3) as $method)
                                        <li>{{ $method }}</li>
                                    @endforeach
                                    @if(count($pest->control_methods) > 3)
                                        <li class="text-xs italic" style="color: #9ca3af;">+{{ count($pest->control_methods) - 3 }} más...</li>
                                    @endif
                                @else
                                    <li>{{ $pest->control_methods }}</li>
                                @endif
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="bg-white border dark:border-gray-700 rounded-lg p-8 text-center" style="border: 1px solid #e5e7eb !important;">
                    <svg class="mx-auto h-12 w-12 mb-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="color: #9ca3af;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                    <p class="text-sm" style="color: #6b7280;">No se encontraron plagas</p>
                </div>
            </div>
        @endforelse
    </div>
</div>

<!-- Modal for Pest Details -->
<div id="pest-modal" class="fixed hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="z-index: 9999; display: none; top: 0; left: 0; right: 0; bottom: 0;">
    <!-- Background overlay -->
    <div class="fixed bg-gray-900 transition-opacity" id="modal-overlay" style="z-index: 9998; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0, 0, 0, 0.75) !important;"></div>
    
    <!-- Modal container - Centered -->
    <div class="fixed z-50 flex items-center justify-center p-2 sm:p-4" style="z-index: 10000; pointer-events: none; top: 0; left: 0; right: 0; bottom: 0;" id="modal-container">
        <!-- Modal panel -->
        <div class="relative bg-white rounded-lg sm:rounded-xl shadow-2xl w-full sm:max-w-3xl max-h-[95vh] sm:max-h-[85vh] overflow-y-auto" style="border: 1px solid #e5e7eb !important; pointer-events: auto; margin: 0.5rem;" id="modal-content">
            <!-- Header -->
            <div class="sticky top-0 bg-white border-b border-gray-200 px-4 sm:px-6 py-3 sm:py-4 flex items-start justify-between z-10" style="border-bottom: 1px solid #e5e7eb !important;">
                <div class="flex-1 pr-4">
                    <h3 class="text-xl sm:text-2xl font-bold mb-2" style="color: #111827;" id="modal-pest-name">Cargando...</h3>
                    <div class="mt-2">
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-medium" id="modal-category"></span>
                    </div>
                </div>
                <button type="button" class="flex-shrink-0 text-gray-400 hover:text-gray-600 focus:outline-none transition-colors" id="close-modal" style="cursor: pointer;">
                    <svg class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Content -->
            <div class="px-4 sm:px-6 py-4 sm:py-6 space-y-4 sm:space-y-6">
                <!-- Description -->
                <div id="modal-description" class="hidden">
                    <h4 class="text-lg font-semibold mb-3" style="color: #111827;">
                        <svg class="inline-block w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="color: #6b7280;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                        </svg>
                        Descripción
                    </h4>
                    <div class="bg-gray-50 rounded-lg p-4" style="background: #f9fafb;">
                        <p class="text-sm leading-relaxed" style="color: #374151; line-height: 1.7;" id="modal-description-text"></p>
                    </div>
                </div>

                <!-- Treatment / Control Methods -->
                <div id="modal-treatment" class="hidden">
                    <h4 class="text-lg font-semibold mb-3" style="color: #111827;">
                        <svg class="inline-block w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="color: #22c55e;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" />
                        </svg>
                        Métodos de Control
                    </h4>
                    <div class="bg-green-50 rounded-lg p-4" style="background: #f0fdf4;">
                        <ul class="list-disc list-inside text-sm space-y-2" style="color: #374151;" id="modal-treatment-list"></ul>
                    </div>
                </div>

                <!-- Recommendations Section -->
                <div id="modal-recommendations" class="hidden">
                    <h4 class="text-lg font-semibold mb-3" style="color: #111827;">
                        <svg class="inline-block w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="color: #3b82f6;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" />
                        </svg>
                        Recomendaciones
                    </h4>
                    <div class="bg-blue-50 rounded-lg p-4" style="background: #eff6ff;">
                        <ul class="list-disc list-inside text-sm space-y-2" style="color: #374151;" id="modal-recommendations-list"></ul>
                    </div>
                </div>
            </div>
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
