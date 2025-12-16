@extends('layouts.app')

@section('title', 'Reportes')

@section('content')
<div class="space-y-4 sm:space-y-6 pt-3 md:pt-0">
    <!-- Header con hamburguesa y título -->
    <div class="mb-4 sm:mb-6">
        <!-- Primera fila: Hamburguesa + Título (móvil) -->
        <div class="flex items-center gap-3 mb-4 md:hidden">
            <!-- Hamburguesa (solo móvil) -->
            <button id="page-mobile-menu-button" class="flex-shrink-0 p-2 rounded-lg bg-white border border-gray-300 shadow-md hover:bg-gray-50 transition-colors">
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
                    Reportes
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
                <h2 class="text-2xl sm:text-3xl font-bold leading-7 text-gray-900 sm:truncate sm:tracking-tight text-gray-900 dark:text-white" class="font-bold">
                    Reportes
                </h2>
                <p class="mt-1 text-xs sm:text-sm text-gray-600 dark:text-gray-300">
                    Genera, exporta y programa reportes personalizados
                </p>
            </div>
        </div>
    </div>

    <!-- Filtros de Reporte -->
    <div class="bg-white rounded-lg shadow-md border mb-6" class="border border-gray-200 dark:border-gray-700">
        <div class="p-6">
            <div class="flex items-center gap-2 mb-4">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="text-green-500">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z" />
                </svg>
                <h2 class="text-lg font-semibold" class="text-green-500">Filtros de Reporte</h2>
            </div>

            <form method="GET" action="{{ route('admin.reports.index') }}" id="reportFiltersForm">
                <!-- Rangos Rápidos -->
                <!-- Filtros de Fecha y Dropdowns -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
                    <!-- Fecha Inicio -->
                    <div>
                        <label for="start_date" class="block text-sm font-medium mb-2 text-gray-600 dark:text-gray-300">Fecha Inicio</label>
                        <div class="relative">
                            <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 pl-10 focus:outline-none focus:ring-2 focus:ring-green-500 dark:text-white dark:bg-gray-700 dark:border-gray-600">
                        </div>
                    </div>

                    <!-- Fecha Fin -->
                    <div>
                        <label for="end_date" class="block text-sm font-medium mb-2 text-gray-600 dark:text-gray-300">Fecha Fin</label>
                        <div class="relative">
                            <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 pl-10 focus:outline-none focus:ring-2 focus:ring-green-500 dark:text-white dark:bg-gray-700 dark:border-gray-600">
                        </div>
                    </div>

                    <!-- Tipo de Servicio -->
                    <div>
                        <label for="service_type" class="block text-sm font-medium mb-2 text-gray-600 dark:text-gray-300">Tipo de Servicio</label>
                        <select name="service_type" id="service_type" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 dark:text-white dark:bg-gray-700 dark:border-gray-600">
                            <option value="all" {{ $serviceType === 'all' ? 'selected' : '' }}>Todos</option>
                            @foreach($serviceTypes as $type)
                                <option value="{{ $type['value'] }}" {{ $serviceType === $type['value'] ? 'selected' : '' }}>{{ $type['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Cliente -->
                    <div>
                        <label for="client_id" class="block text-sm font-medium mb-2 text-gray-600 dark:text-gray-300">Cliente</label>
                        <select name="client_id" id="client_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 dark:text-white dark:bg-gray-700 dark:border-gray-600">
                            <option value="all" {{ $clientId === 'all' ? 'selected' : '' }}>Todos</option>
                            @foreach($allClients as $client)
                                <option value="{{ $client->id }}" {{ $clientId == $client->id ? 'selected' : '' }}>
                                    {{ $client->business_name ?? $client->name ?? 'Cliente #' . $client->id }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Técnico -->
                    <div>
                        <label for="technician_id" class="block text-sm font-medium mb-2 text-gray-600 dark:text-gray-300">Técnico</label>
                        <select name="technician_id" id="technician_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 dark:text-white dark:bg-gray-700 dark:border-gray-600">
                            <option value="all" {{ $technicianId === 'all' ? 'selected' : '' }}>Todos</option>
                            @foreach($allTechnicians as $technician)
                                <option value="{{ $technician->id }}" {{ $technicianId == $technician->id ? 'selected' : '' }}>{{ $technician->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Estado -->
                    <div>
                        <label for="status" class="block text-sm font-medium mb-2 text-gray-600 dark:text-gray-300">Estado</label>
                        <select name="status" id="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 dark:text-white dark:bg-gray-700 dark:border-gray-600">
                            <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Todos</option>
                            <option value="pendiente" {{ $status === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="in_progress" {{ $status === 'in_progress' ? 'selected' : '' }}>En Progreso</option>
                            <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Completado</option>
                        </select>
                    </div>
                </div>

                <!-- Botón Resetear -->
                <div class="flex justify-end">
                    <button type="button" id="resetFilters" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        <svg class="w-4 h-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                        </svg>
                        Resetear Filtros
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tarjetas de Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <!-- Total Servicios -->
        <div class="bg-white rounded-lg shadow-md border p-5" class="border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-2">
                <div class="w-12 h-12 rounded-lg flex items-center justify-center bg-blue-500">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold mb-1 text-gray-900 dark:text-white">TOTAL</p>
            <p class="text-lg font-semibold mb-1 text-gray-900 dark:text-white">{{ $totalServices }}</p>
            <p class="text-sm text-gray-600 dark:text-gray-300">Servicios registrados</p>
        </div>

        <!-- Completados -->
        <div class="bg-white rounded-lg shadow-md border p-5" style="border: 1px solid #e5e7eb; background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%);">
            <div class="flex items-center justify-between mb-2">
                <div class="w-12 h-12 rounded-lg flex items-center justify-center bg-green-500">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold mb-1 text-gray-900 dark:text-white">COMPLETADOS</p>
            <p class="text-lg font-semibold mb-1 text-gray-900 dark:text-white">{{ $completedServices }}</p>
            <p class="text-sm text-gray-600 dark:text-gray-300">{{ $completedPercentage }}% del total</p>
        </div>

        <!-- Ingresos -->
        <div class="bg-white rounded-lg shadow-md border p-5" style="border: 1px solid #e5e7eb; background: linear-gradient(135deg, #eff6ff 0%, #ffffff 100%);">
            <div class="flex items-center justify-between mb-2">
                <div class="w-12 h-12 rounded-lg flex items-center justify-center bg-blue-500">
<svg class="w-6 h-6 text-white" fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><!--!Font Awesome Free v7.1.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#ffffff" d="M296 88C296 74.7 306.7 64 320 64C333.3 64 344 74.7 344 88L344 128L400 128C417.7 128 432 142.3 432 160C432 177.7 417.7 192 400 192L285.1 192C260.2 192 240 212.2 240 237.1C240 259.6 256.5 278.6 278.7 281.8L370.3 294.9C424.1 302.6 464 348.6 464 402.9C464 463.2 415.1 512 354.9 512L344 512L344 552C344 565.3 333.3 576 320 576C306.7 576 296 565.3 296 552L296 512L224 512C206.3 512 192 497.7 192 480C192 462.3 206.3 448 224 448L354.9 448C379.8 448 400 427.8 400 402.9C400 380.4 383.5 361.4 361.3 358.2L269.7 345.1C215.9 337.5 176 291.4 176 237.1C176 176.9 224.9 128 285.1 128L296 128L296 88z"/></svg>
                </div>
            </div>
            <p class="text-2xl font-bold mb-1 text-gray-900 dark:text-white">${{ number_format($periodIncome, 0, ',', '.') }}</p>
            <p class="text-sm text-gray-600 dark:text-gray-300">Período seleccionado</p>
        </div>

        <!-- Clientes -->
        <div class="bg-white rounded-lg shadow-md border p-5" style="border: 1px solid #e5e7eb; background: linear-gradient(135deg, #faf5ff 0%, #ffffff 100%);">
            <div class="flex items-center justify-between mb-2">
                <div class="w-12 h-12 rounded-lg flex items-center justify-center bg-purple-500">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold mb-1 text-gray-900 dark:text-white">CLIENTES</p>
            <p class="text-lg font-semibold mb-1 text-gray-900 dark:text-white">{{ $uniqueClients }}</p>
            <p class="text-sm text-gray-600 dark:text-gray-300">Únicos activos</p>
        </div>

        <!-- Técnicos -->
        <div class="bg-white rounded-lg shadow-md border p-5" style="border: 1px solid #e5e7eb; background: linear-gradient(135deg, #fff7ed 0%, #ffffff 100%);">
            <div class="flex items-center justify-between mb-2">
                <div class="w-12 h-12 rounded-lg flex items-center justify-center bg-amber-500">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold mb-1 text-gray-900 dark:text-white">TÉCNICOS</p>
            <p class="text-lg font-semibold mb-1 text-gray-900 dark:text-white">{{ $activeTechnicians }}</p>
            <p class="text-sm text-gray-600 dark:text-gray-300">Activos en período</p>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Servicios por Estado -->
        <div class="bg-white rounded-lg shadow-md border p-6" class="border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Servicios por Estado</h3>
            <div class="h-72">
                <canvas id="statusChart"></canvas>
            </div>
        </div>

        <!-- Distribución por Tipo -->
        <div class="bg-white rounded-lg shadow-md border p-6" class="border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Distribución por Tipo</h3>
            <div class="h-72">
                <canvas id="typeChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Evolución Temporal -->
    <div class="bg-white rounded-lg shadow-md border p-6 mb-6" class="border border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Evolución Temporal</h3>
            <div class="flex gap-2">
                <button class="px-3 py-1 rounded text-sm font-medium transition-colors">PROGRAMADO</button>
                <button class="px-3 py-1 rounded text-sm font-medium transition-colors bg-green-500 text-white">COMPLETADO</button>
            </div>
        </div>
        <div class="h-80">
            <canvas id="temporalChart"></canvas>
        </div>
        <div class="flex justify-center gap-6 mt-4">
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-full bg-green-500"></div>
                <span class="text-sm text-gray-600 dark:text-gray-300">Completados</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-full bg-amber-500"></div>
                <span class="text-sm text-gray-600 dark:text-gray-300">Pendientes</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-full bg-black"></div>
                <span class="text-sm text-gray-600 dark:text-gray-300">Total</span>
            </div>
        </div>
    </div>

    <!-- Top 5 -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top 5 Clientes -->
        <div class="bg-white rounded-lg shadow-md border p-6" class="border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Top 5 Clientes</h3>
            <div class="space-y-3">
                @forelse($topClients as $index => $item)
                    <div class="flex items-center justify-between p-3 rounded-lg" class="bg-gray-50 dark:bg-gray-800">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm {{ $index === 0 ? 'bg-orange-500 text-white' : 'bg-gray-200 text-gray-600' }}">
                                {{ $index + 1 }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $item['client']->business_name ?? $item['client']->name ?? 'Cliente #' . $item['client']->id }}</p>
                            </div>
                        </div>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $item['count'] }} servicios</p>
                    </div>
                @empty
                    <p class="text-sm text-center py-4 text-gray-600 dark:text-gray-300">No hay datos disponibles</p>
                @endforelse
            </div>
        </div>

        <!-- Top 5 Técnicos -->
        <div class="bg-white rounded-lg shadow-md border p-6" class="border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Top 5 Técnicos</h3>
            <div class="space-y-3">
                @forelse($topTechnicians as $index => $item)
                    <div class="flex items-center justify-between p-3 rounded-lg" class="bg-gray-50 dark:bg-gray-800">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm {{ $index === 0 ? 'bg-orange-500 text-white' : 'bg-gray-200 text-gray-600' }}">
                                {{ $index + 1 }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $item['technician']->name }}</p>
                            </div>
                        </div>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $item['count'] }} servicios</p>
                    </div>
                @empty
                    <p class="text-sm text-center py-4 text-gray-600 dark:text-gray-300">No hay datos disponibles</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
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
    
    // Inicializar gráficos cuando el DOM esté listo
    document.addEventListener('DOMContentLoaded', function() {
        // Gráfico de Estado
        const statusCtx = document.getElementById('statusChart');
        if (statusCtx) {
            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Completados', 'En Progreso', 'Pendientes'],
                    datasets: [{
                        data: [
                            {{ $completedServices ?? 0 }},
                            {{ $inProgressServices ?? 0 }},
                            {{ $pendingServices ?? 0 }}
                        ],
                        backgroundColor: ['#22c55e', '#3b82f6', '#f59e0b'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }
        
        // Gráfico de Tipo
        const typeCtx = document.getElementById('typeChart');
        if (typeCtx) {
            new Chart(typeCtx, {
                type: 'pie',
                data: {
                    labels: @json($serviceTypeLabels ?? []),
                    datasets: [{
                        data: @json($serviceTypeCounts ?? []),
                        backgroundColor: ['#ef4444', '#f59e0b', '#8b5cf6', '#ec4899', '#22c55e'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }
        
        // Gráfico Temporal
        const temporalCtx = document.getElementById('temporalChart');
        if (temporalCtx) {
            new Chart(temporalCtx, {
                type: 'line',
                data: {
                    labels: @json($temporalLabels ?? []),
                    datasets: [{
                        label: 'Completados',
                        data: @json($temporalCompleted ?? []),
                        borderColor: '#22c55e',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        tension: 0.4
                    }, {
                        label: 'Pendientes',
                        data: @json($temporalPending ?? []),
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245, 158, 11, 0.1)',
                        tension: 0.4
                    }, {
                        label: 'Total',
                        data: @json($temporalTotal ?? []),
                        borderColor: '#000000',
                        backgroundColor: 'rgba(0, 0, 0, 0.1)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    });
})();
</script>
@endpush

@endsection
