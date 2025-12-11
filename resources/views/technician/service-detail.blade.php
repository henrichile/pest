@extends("layouts.app")

@section("content")
@php
    // PRIORIDAD 1: Verificar sesión PRIMERO (más confiable)
    $isTechnicianViewMode = false;
    if (auth()->check() && auth()->user()->hasRole('super-admin')) {
        $viewAsTechnician = session('view_as_technician', false);
        // También verificar en request()->session() por si acaso
        if (!$viewAsTechnician && request()->hasSession()) {
            $viewAsTechnician = request()->session()->get('view_as_technician', false);
        }
        if ($viewAsTechnician) {
            $isTechnicianViewMode = true;
        }
    }

    // PRIORIDAD 2: Verificar URL actual
    if (!$isTechnicianViewMode) {
        if (request()->is('admin/technician-view/*') || request()->routeIs('technician-view.*')) {
            $isTechnicianViewMode = true;
        }
    }

    // PRIORIDAD 3: Verificar ruta actual por nombre
    if (!$isTechnicianViewMode) {
        try {
            $routeName = request()->route()->getName();
            if ($routeName && (strpos($routeName, 'technician-view') !== false || strpos($routeName, 'admin.technician-view') !== false)) {
                $isTechnicianViewMode = true;
            }
        } catch (\Exception $e) {
            // Continuar
        }
    }

    // PRIORIDAD 4: Verificar HTTP_REFERER
    if (!$isTechnicianViewMode && isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], '/admin/technician-view/') !== false) {
        $isTechnicianViewMode = true;
    }

    // PRIORIDAD 5: Usar variable del controlador si está disponible
    if (!$isTechnicianViewMode && isset($isTechnicianView) && $isTechnicianView) {
        $isTechnicianViewMode = true;
    }

    // Log para debug (solo en desarrollo)
    if (config('app.debug')) {
        \Log::info('Service Detail - Technician View Detection', [
            'isTechnicianViewMode' => $isTechnicianViewMode,
            'session_view_as_technician' => session('view_as_technician', false),
            'request_session_view_as_technician' => request()->hasSession() ? request()->session()->get('view_as_technician', false) : 'no_session',
            'current_url' => request()->url(),
            'current_path' => request()->path(),
            'route_name' => request()->route() ? request()->route()->getName() : 'no_route',
            'is_super_admin' => auth()->check() ? auth()->user()->hasRole('super-admin') : false,
        ]);
    }
@endphp

<div class="space-y-6 pt-3 md:pt-0">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-4 md:hidden" style="padding-top: 2.5rem;">
            <button id="page-mobile-menu-button" class="flex-shrink-0 p-2 rounded-lg bg-white border border-gray-300 shadow-md hover:bg-gray-50 transition-colors" style="z-index: 1000; position: relative;">
                <svg id="page-menu-icon" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="text-gray-900 dark:text-white">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </button>
            <div class="flex-1">
                <h2 class="text-2xl font-bold" class="text-gray-900 dark:text-white" style="font-weight: 700;">Detalle del Servicio</h2>
            </div>
        </div>
        <div class="hidden md:block">
            <h2 class="text-2xl sm:text-3xl font-bold" class="text-gray-900 dark:text-white" style="font-weight: 700;">Detalle del Servicio</h2>
        </div>
    </div>

    <!-- Service Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h3 class="text-xl font-bold text-gray-900">{{ $service->client->name ?? 'Cliente no encontrado' }}</h3>
                <p class="text-gray-600 mt-1">{{ $service->address }}</p>
            </div>
            <div class="text-right">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    @if($service->status == 'pendiente') bg-gray-100 text-gray-800
                    @elseif($service->status == 'en_progreso') bg-blue-100 text-blue-800
                    @elseif($service->status == 'vencido') bg-red-100 text-red-800
                    @else bg-green-100 text-green-800
                    @endif">
                    {{ ucfirst(str_replace('_', ' ', $service->status)) }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
            <div>
                <h4 class="text-sm font-medium text-gray-500">Tipo de Servicio</h4>
                <p class="text-lg font-semibold text-gray-900 mt-1">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        @if($service->service_type == 'desratizacion') bg-red-100 text-red-800
                        @elseif($service->service_type == 'desinsectacion') bg-yellow-100 text-yellow-800
                        @else bg-blue-100 text-blue-800
                        @endif">
                        {{ ucfirst($service->service_type) }}
                    </span>
                </p>
                @if($service->service_type === 'servicios-especiales' && $service->special_service_title)
                    <p class="text-green-700 font-semibold text-sm mt-2">📋 {{ $service->special_service_title }}</p>
                @endif
            </div>
            <div>
                <h4 class="text-sm font-medium text-gray-500">Prioridad</h4>
                <p class="text-lg font-semibold text-gray-900 mt-1">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        @if($service->priority == 'alta') bg-red-100 text-red-800
                        @elseif($service->priority == 'media') bg-yellow-100 text-yellow-800
                        @else bg-green-100 text-green-800
                        @endif">
                        {{ ucfirst($service->priority) }}
                    </span>
                </p>
            </div>
            <div>
                <h4 class="text-sm font-medium text-gray-500">Fecha Programada</h4>
                <p class="text-lg font-semibold text-gray-900 mt-1">{{ $service->scheduled_date->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>

    <!-- Service Details -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Client Information -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Información del Cliente</h3>
            <div class="space-y-3">
                <div>
                    <span class="text-sm font-medium text-gray-500">Razón Social:</span>
                    <p class="text-gray-900">{{ $service->client->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">RUT:</span>
                    <p class="text-gray-900">{{ $service->client->rut ?? 'N/A' }}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Email:</span>
                    <p class="text-gray-900">{{ $service->client->email ?? 'N/A' }}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Teléfono:</span>
                    <p class="text-gray-900">{{ $service->client->phone ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <!-- Service Information -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Detalles del Servicio</h3>
            <div class="space-y-3">
                <div>
                    <span class="text-sm font-medium text-gray-500">Descripción:</span>
                    <p class="text-gray-900">{{ $service->description ?? 'Sin descripción' }}</p>
                </div>
                @if($service->started_at)
                <div>
                    <span class="text-sm font-medium text-gray-500">Iniciado:</span>
                    <p class="text-gray-900">{{ $service->started_at->format('d/m/Y H:i') }}</p>
                </div>
                @endif
                @if($service->completed_at)
                <div>
                    <span class="text-sm font-medium text-gray-500">Completado:</span>
                    <p class="text-gray-900">{{ $service->completed_at->format('d/m/Y H:i') }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Actions -->
    @if($service->status !== 'completado')
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex flex-wrap gap-4">
            @if($service->status === 'pendiente')
                <form action="{{ route('technician.service.start', $service) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        Iniciar Servicio
                    </button>
                </form>
            @endif
            
            <a href="{{ route('technician.service.checklist', $service) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors inline-block">
                Ver/Completar Checklist
            </a>
            
            @if($service->checklist_data)
                <a href="{{ route('technician.service.checklist-details', $service) }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors inline-block">
                    Ver Detalles del Checklist
                </a>
            @endif
        </div>
    </div>
    @endif
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
