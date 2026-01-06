@extends("layouts.app")

@section("title", "Mis Servicios - Pest Controller SAT")
@section("page-title", "Mis Servicios")

@section("content")
<div class="max-w-7xl mx-auto space-y-6 pt-3 md:pt-0">
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
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white" class="font-bold">
                    Mis Servicios
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
        
        <!-- Header original (desktop) -->
        <div class="hidden md:block">
            @include('technician.partials.header', [
                'title' => 'Mis Servicios',
                'searchPlaceholder' => 'Buscar servicios...',
                'pageId' => 'services'
            ])
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow-lg p-4 md:p-6">
        <!-- Título del filtro (solo móvil) -->
        <div class="flex items-center justify-between mb-3 md:hidden">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Filtrar Servicios</h3>
            <button type="button" id="toggle-filters" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                <span id="filter-toggle-text">Ocultar</span>
            </button>
        </div>

        <form method="GET" action="{{ request()->url() }}" id="filter-form" class="space-y-4 md:space-y-0 md:flex md:items-center md:space-x-4">
            <div id="filter-content" class="space-y-4 md:space-y-0 md:flex md:items-center md:space-x-4 md:flex-1">
                <!-- Filtro Estado -->
                <div class="flex flex-col md:flex-row md:items-center md:space-x-2 w-full md:w-auto">
                    <label class="text-sm font-medium text-gray-700 mb-1.5 md:mb-0 dark:text-white">Estado:</label>
                    <select name="estado" id="filter-estado" class="w-full md:w-auto border border-gray-300 rounded-lg px-4 py-2.5 md:px-3 md:py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:text-white dark:bg-gray-700 dark:border-gray-600">
                        <option value="">Todos</option>
                        <option value="pendiente" {{ request('estado') === 'pendiente' ? 'selected' : '' }}>Pendientes</option>
                        <option value="en_progreso" {{ request('estado') === 'en_progreso' ? 'selected' : '' }}>En Progreso</option>
                        <option value="finalizado" {{ request('estado') === 'finalizado' ? 'selected' : '' }}>Finalizados</option>
                        <option value="vencido" {{ request('estado') === 'vencido' ? 'selected' : '' }}>Vencidos</option>
                    </select>
                </div>

                <!-- Filtro Tipo -->
                <div class="flex flex-col md:flex-row md:items-center md:space-x-2 w-full md:w-auto">
                    <label class="text-sm font-medium text-gray-700 mb-1.5 md:mb-0 dark:text-white">Tipo:</label>
                    <select name="tipo" id="filter-tipo" class="w-full md:w-auto border border-gray-300 rounded-lg px-4 py-2.5 md:px-3 md:py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:text-white dark:bg-gray-700 dark:border-gray-600">
                        <option value="">Todos</option>
                        <option value="desratizacion" {{ request('tipo') === 'desratizacion' ? 'selected' : '' }}>Desratización</option>
                        <option value="desinsectacion" {{ request('tipo') === 'desinsectacion' ? 'selected' : '' }}>Desinsectación</option>
                        <option value="sanitizacion" {{ request('tipo') === 'sanitizacion' ? 'selected' : '' }}>Sanitización</option>
                    </select>
                </div>

                <!-- Botones de acción -->
                <div class="flex flex-col md:flex-row gap-2 md:gap-2 w-full md:w-auto pt-2 md:pt-0">
                    <button type="submit" class="w-full md:w-auto px-4 py-2.5 md:py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors shadow-sm hover:shadow-md">
                        <span class="flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            Filtrar
                        </span>
                    </button>
                    <a href="{{ request()->url() }}" class="w-full md:w-auto px-4 py-2.5 md:py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-sm font-medium transition-colors text-center shadow-sm hover:shadow-md">
                        <span class="flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Limpiar
                        </span>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Lista de Servicios -->
    <div class="bg-white rounded-lg shadow-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Servicios Asignados</h3>
        </div>
        
        @if($services->count() > 0)

        <!-- Vista Móvil (Cards) -->
        <div class="md:hidden space-y-4 p-4">
            @foreach($services as $service)
            @php
                $iconBg = '#dbeafe'; // blue-100 default
                $iconColor = '#1e40af'; // blue-800 default
                $iconPath = 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z'; // Shield check default

                if($service->service_type == 'desratizacion') {
                    $iconBg = '#fee2e2'; // red-100
                    $iconColor = '#991b1b'; // red-800
                    $iconPath = 'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z';
                } elseif($service->service_type == 'desinsectacion') {
                    $iconBg = '#fef9c3'; // yellow-100
                    $iconColor = '#854d0e'; // yellow-800
                    $iconPath = 'M12 7.462c-2.502 0-4.853 1.558-5.738 3.808A2.999 2.999 0 014 13.5v3.75a3 3 0 003 3h10a3 3 0 003-3v-3.75a2.999 2.999 0 01-2.262-2.23c-.885-2.25-3.236-3.808-5.738-3.808zM6.75 8.25a.75.75 0 01.75-.75h9a.75.75 0 010 1.5h-9a.75.75 0 01-.75-.75zM6.75 18.75a.75.75 0 01.75-.75h9a.75.75 0 010 1.5h-9a.75.75 0 01-.75-.75zM3.75 15a.75.75 0 01.75-.75h15a.75.75 0 010 1.5h-15a.75.75 0 01-.75-.75zM3 12.75a.75.75 0 01.75-.75h16.5a.75.75 0 010 1.5H3.75a.75.75 0 01-.75-.75z';
                } elseif($service->service_type == 'sanitizacion') {
                     // Keep default blue
                }
            @endphp
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 space-y-3">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ optional($service->client)->name ?? "N/A" }}</h3>
                        @if($service->address)
                        <p class="text-sm text-gray-500 dark:text-white">{{ Str::limit($service->address, 30) }}</p>
                        @endif
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-full" style="background-color: {{ $iconBg }}; color: {{ $iconColor }};">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $iconPath }}" />
                        </svg>
                        {{ ucfirst($service->service_type) }}
                    </span>
                </div>
                
                <div class="grid grid-cols-2 gap-2 text-sm">
                    <div>
                        <span class="text-gray-500 block text-xs dark:text-white">Fecha</span>
                        <span class="font-medium dark:text-white">{{ $service->scheduled_date->format("d/m/Y H:i") }}</span>
                        @if($service->scheduled_date < now() && $service->status == "pendiente")
                        <span class="text-xs text-red-600 font-medium block dark:text-red-600">Vencido</span>
                        @endif
                    </div>
                    <div>
                        <span class="text-gray-500 block text-xs dark:text-white">Estado</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                            @if($service->status == 'pendiente') bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                            @elseif($service->status == 'en_progreso') bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300
                            @elseif($service->status == 'vencido') bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300
                            @else bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300
                            @endif">
                            {{ ucfirst(str_replace("_", " ", $service->status)) }}
                        </span>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                    @php
                        // Lógica de URLs (reutilizada)
                        $isTechView = false;
                        if (auth()->check() && auth()->user()->hasRole('super-admin')) {
                            $viewAsTechnician = session('view_as_technician', false);
                            if (!$viewAsTechnician && request()->hasSession()) {
                                $viewAsTechnician = request()->session()->get('view_as_technician', false);
                            }
                            if ($viewAsTechnician) $isTechView = true;
                        }
                        if (!$isTechView && (request()->is('admin/technician-view/*') || request()->routeIs('technician-view.*'))) $isTechView = true;
                        if (!$isTechView && isset($isTechnicianView) && $isTechnicianView) $isTechView = true;
                        
                        if ($isTechView) {
                            $startUrl = url('/admin/technician-view/services/' . $service->id . '/start');
                            $detailUrl = url('/admin/technician-view/services/' . $service->id . '/detail');
                            $pdfUrl = url('/admin/technician-view/services/' . $service->id . '/pdf');
                        } else {
                            try {
                                $startUrl = route("technician.service.start", $service);
                                $detailUrl = route("technician.service.detail", $service);
                                $pdfUrl = route("technician.service.pdf", $service);
                            } catch (\Exception $e) {
                                $startUrl = url('/technician/services/' . $service->id . '/start');
                                $detailUrl = url('/technician/services/' . $service->id . '/detail');
                                $pdfUrl = url('/technician/services/' . $service->id . '/pdf');
                            }
                        }
                    @endphp

                    @if($service->status == "pendiente")
                    <form method="POST" action="{{ $startUrl }}" class="inline" id="mobile-start-form-{{ $service->id }}">
                        @csrf
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white dark:bg-blue-500 dark:hover:bg-blue-600 px-3 py-1.5 rounded-md text-sm font-medium transition-colors">
                            Iniciar
                        </button>
                    </form>
                    @elseif($service->status == "en_progreso")
                    <a href="{{ $detailUrl }}" class="bg-green-600 hover:bg-green-700 text-white dark:bg-green-500 dark:hover:bg-green-600 px-3 py-1.5 rounded-md text-sm font-medium transition-colors">
                        Completar
                    </a>
                    @elseif($service->status == "finalizado")
                    <a href="{{ $pdfUrl }}" class="bg-blue-600 hover:bg-blue-700 text-white dark:bg-blue-500 dark:hover:bg-blue-600 px-3 py-1.5 rounded-md text-sm font-medium transition-colors">
                        PDF
                    </a>
                    @endif
                    <a href="{{ $detailUrl }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-white px-3 py-1.5 rounded-md text-sm font-medium transition-colors">
                        Ver Detalle
                    </a>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Vista Desktop (Tabla) -->
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Programada</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prioridad</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="services-table-body">
                    @foreach($services as $service)
                    @php
                        $iconBg = '#dbeafe'; // blue-100 default
                        $iconColor = '#1e40af'; // blue-800 default
                        $iconPath = 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z'; // Shield check default

                        if($service->service_type == 'desratizacion') {
                            $iconBg = '#fee2e2'; // red-100
                            $iconColor = '#991b1b'; // red-800
                            $iconPath = 'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z';
                        } elseif($service->service_type == 'desinsectacion') {
                            $iconBg = '#fef9c3'; // yellow-100
                            $iconColor = '#854d0e'; // yellow-800
                            $iconPath = 'M12 7.462c-2.502 0-4.853 1.558-5.738 3.808A2.999 2.999 0 014 13.5v3.75a3 3 0 003 3h10a3 3 0 003-3v-3.75a2.999 2.999 0 01-2.262-2.23c-.885-2.25-3.236-3.808-5.738-3.808zM6.75 8.25a.75.75 0 01.75-.75h9a.75.75 0 010 1.5h-9a.75.75 0 01-.75-.75zM6.75 18.75a.75.75 0 01.75-.75h9a.75.75 0 010 1.5h-9a.75.75 0 01-.75-.75zM3.75 15a.75.75 0 01.75-.75h15a.75.75 0 010 1.5h-15a.75.75 0 01-.75-.75zM3 12.75a.75.75 0 01.75-.75h16.5a.75.75 0 010 1.5H3.75a.75.75 0 01-.75-.75z';
                        } elseif($service->service_type == 'sanitizacion') {
                             // Keep default blue
                        }
                    @endphp
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 service-row" 
                        data-status="{{ $service->status }}" 
                        data-service-type="{{ $service->service_type }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ optional($service->client)->name ?? "N/A" }}</div>
                            @if($service->address)
                            <div class="text-sm text-gray-500 dark:text-white">{{ Str::limit($service->address, 30) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium" style="background-color: {{ $iconBg }}; color: {{ $iconColor }};">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $iconPath }}" />
                                </svg>
                                {{ ucfirst($service->service_type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            {{ $service->scheduled_date->format("d/m/Y H:i") }}
                            @if($service->scheduled_date < now() && $service->status == "pendiente")
                            <div class="text-xs text-red-600 font-medium dark:text-red-600">Vencido</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($service->status == "pendiente") bg-gray-100 text-gray-800
                                @elseif($service->status == "en_progreso") bg-blue-100 text-blue-800
                                @elseif($service->status == "vencido") bg-red-100 text-red-800
                                @else bg-green-100 text-green-800
                                @endif">
                                {{ ucfirst(str_replace("_", " ", $service->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($service->priority == "alta") bg-red-100 text-red-800
                                @elseif($service->priority == "media") bg-yellow-100 text-yellow-800
                                @else bg-green-100 text-green-800
                                @endif">
                                {{ ucfirst($service->priority) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium dark:text-white dark:bg-gray-800">
                            <div class="flex items-center space-x-2">
                                @php
                                    // Lógica de URLs (reutilizada)
                                    $isTechView = false;
                                    if (auth()->check() && auth()->user()->hasRole('super-admin')) {
                                        $viewAsTechnician = session('view_as_technician', false);
                                        if (!$viewAsTechnician && request()->hasSession()) {
                                            $viewAsTechnician = request()->session()->get('view_as_technician', false);
                                        }
                                        if ($viewAsTechnician) $isTechView = true;
                                    }
                                    if (!$isTechView && (request()->is('admin/technician-view/*') || request()->routeIs('technician-view.*'))) $isTechView = true;
                                    if (!$isTechView && isset($isTechnicianView) && $isTechnicianView) $isTechView = true;
                                    
                                    if ($isTechView) {
                                        $startUrl = url('/admin/technician-view/services/' . $service->id . '/start');
                                        $detailUrl = url('/admin/technician-view/services/' . $service->id . '/detail');
                                        $pdfUrl = url('/admin/technician-view/services/' . $service->id . '/pdf');
                                    } else {
                                        try {
                                            $startUrl = route("technician.service.start", $service);
                                            $detailUrl = route("technician.service.detail", $service);
                                            $pdfUrl = route("technician.service.pdf", $service);
                                        } catch (\Exception $e) {
                                            $startUrl = url('/technician/services/' . $service->id . '/start');
                                            $detailUrl = url('/technician/services/' . $service->id . '/detail');
                                            $pdfUrl = url('/technician/services/' . $service->id . '/pdf');
                                        }
                                    }
                                @endphp
                                @if($service->status == "pendiente")
                                <form method="POST" action="{{ $startUrl }}" class="inline" id="start-form-{{ $service->id }}">
                                    @csrf
                                    <button type="submit" class="text-blue-600 hover:text-blue-900 font-medium dark:text-blue-400 dark:hover:text-blue-600">
                                        Iniciar
                                    </button>
                                </form>
                                @elseif($service->status == "en_progreso")
                                <a href="{{ $detailUrl }}" class="text-green-600 hover:text-green-900 font-medium dark:text-green-400 dark:hover:text-green-600">
                                    Completar
                                </a>
                                @elseif($service->status == "finalizado")
                                <a href="{{ $pdfUrl }}" class="text-blue-600 hover:text-blue-900 font-medium dark:text-blue-400 dark:hover:text-blue-600">
                                    📄 Descargar PDF
                                </a>
                                @endif
                                <a href="{{ $detailUrl }}" class="text-gray-600 hover:text-gray-900 font-medium dark:text-gray-400 dark:hover:text-gray-600">
                                    Ver
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Paginación - Fuera del overflow-x-auto para que sea visible en móvil -->
        @if($services->hasPages())
        <div class="px-2 sm:px-6 py-3 border-t border-gray-200 bg-white">
            <!-- Información de resultados - Solo en desktop -->
            <div class="hidden sm:block text-sm text-gray-700 mb-3 dark:text-gray-400">
                Mostrando
                <span class="font-medium">{{ $services->firstItem() }}</span>
                a       
                <span class="font-medium">{{ $services->lastItem() }}</span>
                de
                <span class="font-medium">{{ $services->total() }}</span>
                resultados
            </div>
            
            <!-- Números de página - Visible en móvil y desktop -->
            <div class="flex items-center justify-center gap-1 overflow-x-auto w-full">
                @if($services->onFirstPage())
                    <span class="px-2 py-1.5 text-xs sm:text-sm font-medium text-gray-400 cursor-not-allowed whitespace-nowrap dark:text-gray-600">« Anterior</span>
                @else
                    <a href="{{ $services->previousPageUrl() }}" class="px-2 py-1.5 text-xs sm:text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 whitespace-nowrap dark:text-gray-600">« Anterior</a>
                @endif
                
                @php
                    $currentPage = $services->currentPage();
                    $lastPage = $services->lastPage();
                    $startPage = max(1, $currentPage - 1);
                    $endPage = min($lastPage, $currentPage + 1);
                    
                    // Si estamos cerca del inicio, mostrar más páginas al final
                    if ($currentPage <= 2) {
                        $endPage = min($lastPage, 4);
                    }
                    // Si estamos cerca del final, mostrar más páginas al inicio
                    if ($currentPage >= $lastPage - 1) {
                        $startPage = max(1, $lastPage - 3);
                    }
                @endphp
                
                @if($startPage > 1)
                    <a href="{{ $services->url(1) }}" class="px-2 py-1.5 text-xs sm:text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">1</a>
                    @if($startPage > 2)
                        <span class="px-1 text-gray-400">...</span>
                    @endif
                @endif
                
                @foreach($services->getUrlRange($startPage, $endPage) as $page => $url)
                    @if($page == $services->currentPage())
                        <span class="px-2.5 py-1.5 text-xs sm:text-sm font-medium text-white bg-green-600 border border-green-600 rounded-md whitespace-nowrap">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="px-2.5 py-1.5 text-xs sm:text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 whitespace-nowrap dark:text-gray-600">{{ $page }}</a>
                    @endif
                @endforeach
                
                @if($endPage < $lastPage)
                    @if($endPage < $lastPage - 1)
                        <span class="px-1 text-gray-400">...</span>
                    @endif
                    <a href="{{ $services->url($lastPage) }}" class="px-2 py-1.5 text-xs sm:text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 whitespace-nowrap dark:text-gray-600">{{ $lastPage }}</a>
                @endif
                
                @if($services->hasMorePages())
                    <a href="{{ $services->nextPageUrl() }}" class="px-2 py-1.5 text-xs sm:text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 whitespace-nowrap dark:text-gray-600">Siguiente »</a>
                @else
                    <span class="px-2 py-1.5 text-xs sm:text-sm font-medium text-gray-400 cursor-not-allowed whitespace-nowrap dark:text-gray-600">Siguiente »</span>
                @endif
            </div>
            
            <!-- Información de resultados - Solo en móvil -->
            <div class="sm:hidden text-xs text-gray-600 text-center mt-2 dark:text-gray-600">
                Página {{ $services->currentPage() }} de {{ $services->lastPage() }}
            </div>
        </div>
        @endif
        @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-600">No hay servicios asignados</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-600">No tienes servicios asignados en este momento.</p>
        </div>
        @endif
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

