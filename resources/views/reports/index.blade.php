@extends('layouts.app')

@section('title', 'Reportes')

@section('content')
<div class="space-y-4 sm:space-y-6 pt-3 md:pt-0">
    <!-- Header con hamburguesa y título -->
    <div class="mb-4 sm:mb-6">
        <!-- Primera fila: Hamburguesa + Título (móvil) -->
        <div class="flex items-center gap-3 mb-4 md:hidden" style="padding-top: 2.5rem;">
            <!-- Hamburguesa (solo móvil) -->
            <button id="page-mobile-menu-button" class="flex-shrink-0 p-2 rounded-lg bg-white border border-gray-300 shadow-md hover:bg-gray-50 transition-colors cursor-pointer" style="z-index: 100;">
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
            </div>
        </div>
        
        <!-- Segunda fila: Título completo (desktop) -->
        <div class="hidden md:flex md:items-center md:justify-between">
            <div class="min-w-0 flex-1">
                <h2 class="text-2xl sm:text-3xl font-bold leading-7 text-gray-900 sm:truncate sm:tracking-tight" style="color: #111827; font-weight: 700;">
                    Reportes
                </h2>
                <p class="mt-1 text-xs sm:text-sm" style="color: #6b7280;">
                    Genera, exporta y programa reportes personalizados
                </p>
            </div>
        </div>
    </div>

    <!-- Filtros de Reporte -->
    <div class="bg-white rounded-lg shadow-md border mb-6" style="border: 1px solid #e5e7eb;">
        <div class="p-6">
            <div class="flex items-center gap-2 mb-4">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="color: #22c55e;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z" />
                </svg>
                <h2 class="text-lg font-semibold" style="color: #22c55e;">Filtros de Reporte</h2>
            </div>

            <form method="GET" action="{{ route('admin.reports.index') }}" id="reportFiltersForm">
                <!-- Rangos Rápidos -->
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2" style="color: #6b7280;">Rangos Rápidos</label>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" class="quick-range-btn px-4 py-2 rounded-full text-sm font-medium transition-colors" data-range="this-month" style="background: #f3f4f6; color: #6b7280; border: 1px solid #e5e7eb;">
                            <svg class="w-4 h-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                            </svg>
                            Este Mes
                        </button>
                        <button type="button" class="quick-range-btn px-4 py-2 rounded-full text-sm font-medium transition-colors" data-range="last-month" style="background: #f3f4f6; color: #6b7280; border: 1px solid #e5e7eb;">
                            <svg class="w-4 h-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                            </svg>
                            Último Mes
                        </button>
                        <button type="button" class="quick-range-btn px-4 py-2 rounded-full text-sm font-medium transition-colors" data-range="last-3-months" style="background: #f3f4f6; color: #6b7280; border: 1px solid #e5e7eb;">
                            <svg class="w-4 h-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                            </svg>
                            Últimos 3 Meses
                        </button>
                        <button type="button" class="quick-range-btn px-4 py-2 rounded-full text-sm font-medium transition-colors" data-range="this-year" style="background: #f3f4f6; color: #6b7280; border: 1px solid #e5e7eb;">
                            <svg class="w-4 h-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                            </svg>
                            Este Año
                        </button>
                    </div>
                </div>

                <!-- Filtros de Fecha y Dropdowns -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
                    <!-- Fecha Inicio -->
                    <div>
                        <label for="start_date" class="block text-sm font-medium mb-2" style="color: #6b7280;">Fecha Inicio</label>
                        <div class="relative">
                            <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 pl-10 focus:outline-none focus:ring-2 focus:ring-green-500">
                            <svg class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                            </svg>
                        </div>
                    </div>

                    <!-- Fecha Fin -->
                    <div>
                        <label for="end_date" class="block text-sm font-medium mb-2" style="color: #6b7280;">Fecha Fin</label>
                        <div class="relative">
                            <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 pl-10 focus:outline-none focus:ring-2 focus:ring-green-500">
                            <svg class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                            </svg>
                        </div>
                    </div>

                    <!-- Tipo de Servicio -->
                    <div>
                        <label for="service_type" class="block text-sm font-medium mb-2" style="color: #6b7280;">Tipo de Servicio</label>
                        <select name="service_type" id="service_type" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="all" {{ $serviceType === 'all' ? 'selected' : '' }}>Todos</option>
                            @foreach($serviceTypes as $type)
                                <option value="{{ $type['value'] }}" {{ $serviceType === $type['value'] ? 'selected' : '' }}>{{ $type['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Cliente -->
                    <div>
                        <label for="client_id" class="block text-sm font-medium mb-2" style="color: #6b7280;">Cliente</label>
                        <select name="client_id" id="client_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
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
                        <label for="technician_id" class="block text-sm font-medium mb-2" style="color: #6b7280;">Técnico</label>
                        <select name="technician_id" id="technician_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="all" {{ $technicianId === 'all' ? 'selected' : '' }}>Todos</option>
                            @foreach($allTechnicians as $technician)
                                <option value="{{ $technician->id }}" {{ $technicianId == $technician->id ? 'selected' : '' }}>{{ $technician->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Estado -->
                    <div>
                        <label for="status" class="block text-sm font-medium mb-2" style="color: #6b7280;">Estado</label>
                        <select name="status" id="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
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
                    <button type="button" id="resetFilters" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors" style="background: #f3f4f6; color: #6b7280; border: 1px solid #e5e7eb; hover:bg-gray-100;">
                        <svg class="w-4 h-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                        </svg>
                        Resetear Filtros
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Acciones -->
    <div class="flex gap-3 mb-6">
        <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2" style="background: #f3f4f6; color: #6b7280; border: 1px solid #e5e7eb;">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
            </svg>
            Dashboard
        </a>
        <div class="relative">
            <button type="button" id="exportBtn" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2 hover:bg-gray-100" style="background: #f3f4f6; color: #6b7280; border: 1px solid #e5e7eb;">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                </svg>
                Exportar
            </button>
            <!-- Dropdown de exportación -->
            <div id="exportDropdown" class="hidden absolute top-full left-0 mt-2 bg-white rounded-lg shadow-lg border z-50" style="border: 1px solid #e5e7eb; min-width: 200px;">
                <a href="{{ route('admin.reports.export', array_merge(request()->all(), ['format' => 'csv'])) }}" class="block px-4 py-2 text-sm hover:bg-gray-50 transition-colors" style="color: #111827;">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        Exportar CSV
                    </div>
                </a>
                <a href="{{ route('admin.reports.export', array_merge(request()->all(), ['format' => 'pdf'])) }}" class="block px-4 py-2 text-sm hover:bg-gray-50 transition-colors" style="color: #111827;">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                        Exportar PDF
                    </div>
                </a>
            </div>
        </div>
        <a href="{{ route('admin.reports.scheduled') }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2 hover:bg-gray-100" style="background: #f3f4f6; color: #6b7280; border: 1px solid #e5e7eb;">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5a2.25 2.25 0 002.25-2.25m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5a2.25 2.25 0 012.25 2.25v7.5" />
            </svg>
            Programados
        </a>
        <a href="{{ route('admin.reports.config') }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2 hover:bg-gray-100" style="background: #f3f4f6; color: #6b7280; border: 1px solid #e5e7eb;">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.355-.183-.75-.256-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            Config
        </a>
    </div>

    <!-- Tarjetas de Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <!-- Total Servicios -->
        <div class="bg-white rounded-lg shadow-md border p-5" style="border: 1px solid #e5e7eb;">
            <div class="flex items-center justify-between mb-2">
                <div class="w-12 h-12 rounded-lg flex items-center justify-center" style="background: #3b82f6;">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold mb-1" style="color: #111827;">TOTAL</p>
            <p class="text-lg font-semibold mb-1" style="color: #111827;">{{ $totalServices }}</p>
            <p class="text-sm" style="color: #6b7280;">Servicios registrados</p>
        </div>

        <!-- Completados -->
        <div class="bg-white rounded-lg shadow-md border p-5" style="border: 1px solid #e5e7eb; background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%);">
            <div class="flex items-center justify-between mb-2">
                <div class="w-12 h-12 rounded-lg flex items-center justify-center" style="background: #22c55e;">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold mb-1" style="color: #111827;">COMPLETADOS</p>
            <p class="text-lg font-semibold mb-1" style="color: #111827;">{{ $completedServices }}</p>
            <p class="text-sm" style="color: #6b7280;">{{ $completedPercentage }}% del total</p>
        </div>

        <!-- Ingresos -->
        <div class="bg-white rounded-lg shadow-md border p-5" style="border: 1px solid #e5e7eb; background: linear-gradient(135deg, #eff6ff 0%, #ffffff 100%);">
            <div class="flex items-center justify-between mb-2">
                <div class="w-12 h-12 rounded-lg flex items-center justify-center" style="background: #3b82f6;">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659a1.5 1.5 0 001.06.39h1.5m-1.5-4.5h-3m-3 0l-.879.659A1.5 1.5 0 003 8.818v1.5m0 0v1.5m0-1.5h3m-3 0h-3M9 6.75h3m-3 0h-3m3 0v1.5m0-1.5V6.75m0 0H9m3 0h3M15 6.75h3m-3 0h-3m3 0v1.5m0-1.5V6.75m0 0H15m3 0h3" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold mb-1" style="color: #111827;">${{ number_format($periodIncome, 0, ',', '.') }}</p>
            <p class="text-sm" style="color: #6b7280;">Período seleccionado</p>
        </div>

        <!-- Clientes -->
        <div class="bg-white rounded-lg shadow-md border p-5" style="border: 1px solid #e5e7eb; background: linear-gradient(135deg, #faf5ff 0%, #ffffff 100%);">
            <div class="flex items-center justify-between mb-2">
                <div class="w-12 h-12 rounded-lg flex items-center justify-center" style="background: #8b5cf6;">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold mb-1" style="color: #111827;">CLIENTES</p>
            <p class="text-lg font-semibold mb-1" style="color: #111827;">{{ $uniqueClients }}</p>
            <p class="text-sm" style="color: #6b7280;">Únicos activos</p>
        </div>

        <!-- Técnicos -->
        <div class="bg-white rounded-lg shadow-md border p-5" style="border: 1px solid #e5e7eb; background: linear-gradient(135deg, #fff7ed 0%, #ffffff 100%);">
            <div class="flex items-center justify-between mb-2">
                <div class="w-12 h-12 rounded-lg flex items-center justify-center" style="background: #f59e0b;">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold mb-1" style="color: #111827;">TÉCNICOS</p>
            <p class="text-lg font-semibold mb-1" style="color: #111827;">{{ $activeTechnicians }}</p>
            <p class="text-sm" style="color: #6b7280;">Activos en período</p>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Servicios por Estado -->
        <div class="bg-white rounded-lg shadow-md border p-6" style="border: 1px solid #e5e7eb;">
            <h3 class="text-lg font-semibold mb-4" style="color: #111827;">Servicios por Estado</h3>
            <div style="height: 300px; position: relative;">
                <canvas id="statusChart"></canvas>
            </div>
        </div>

        <!-- Distribución por Tipo -->
        <div class="bg-white rounded-lg shadow-md border p-6" style="border: 1px solid #e5e7eb;">
            <h3 class="text-lg font-semibold mb-4" style="color: #111827;">Distribución por Tipo</h3>
            <div style="height: 300px; position: relative;">
                <canvas id="typeChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Evolución Temporal -->
    <div class="bg-white rounded-lg shadow-md border p-6 mb-6" style="border: 1px solid #e5e7eb;">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold" style="color: #111827;">Evolución Temporal</h3>
            <div class="flex gap-2">
                <button class="px-3 py-1 rounded text-sm font-medium transition-colors" style="background: #f3f4f6; color: #6b7280;">PROGRAMADO</button>
                <button class="px-3 py-1 rounded text-sm font-medium transition-colors bg-green-500 text-white">COMPLETADO</button>
            </div>
        </div>
        <div style="height: 350px; position: relative;">
            <canvas id="temporalChart"></canvas>
        </div>
        <div class="flex justify-center gap-6 mt-4">
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-full bg-green-500"></div>
                <span class="text-sm" style="color: #6b7280;">Completados</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-full" style="background: #f59e0b;"></div>
                <span class="text-sm" style="color: #6b7280;">Pendientes</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-full bg-black"></div>
                <span class="text-sm" style="color: #6b7280;">Total</span>
            </div>
        </div>
    </div>

    <!-- Top 5 -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top 5 Clientes -->
        <div class="bg-white rounded-lg shadow-md border p-6" style="border: 1px solid #e5e7eb;">
            <h3 class="text-lg font-semibold mb-4" style="color: #111827;">Top 5 Clientes</h3>
            <div class="space-y-3">
                @forelse($topClients as $index => $item)
                    <div class="flex items-center justify-between p-3 rounded-lg" style="background: #f9fafb;">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm {{ $index === 0 ? 'bg-orange-500 text-white' : 'bg-gray-200 text-gray-600' }}">
                                {{ $index + 1 }}
                            </div>
                            <div>
                                <p class="text-sm font-medium" style="color: #111827;">{{ $item['client']->business_name ?? $item['client']->name ?? 'Cliente #' . $item['client']->id }}</p>
                            </div>
                        </div>
                        <p class="text-sm font-semibold" style="color: #111827;">{{ $item['count'] }} servicios</p>
                    </div>
                @empty
                    <p class="text-sm text-center py-4" style="color: #6b7280;">No hay datos disponibles</p>
                @endforelse
            </div>
        </div>

        <!-- Top 5 Técnicos -->
        <div class="bg-white rounded-lg shadow-md border p-6" style="border: 1px solid #e5e7eb;">
            <h3 class="text-lg font-semibold mb-4" style="color: #111827;">Top 5 Técnicos</h3>
            <div class="space-y-3">
                @forelse($topTechnicians as $index => $item)
                    <div class="flex items-center justify-between p-3 rounded-lg" style="background: #f9fafb;">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm {{ $index === 0 ? 'bg-orange-500 text-white' : 'bg-gray-200 text-gray-600' }}">
                                {{ $index + 1 }}
                            </div>
                            <div>
                                <p class="text-sm font-medium" style="color: #111827;">{{ $item['technician']->name }}</p>
                            </div>
                        </div>
                        <p class="text-sm font-semibold" style="color: #111827;">{{ $item['count'] }} servicios</p>
                    </div>
                @empty
                    <p class="text-sm text-center py-4" style="color: #6b7280;">No hay datos disponibles</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Dropdown de exportación
    const exportBtn = document.getElementById('exportBtn');
    const exportDropdown = document.getElementById('exportDropdown');
    
    if (exportBtn && exportDropdown) {
        exportBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            exportDropdown.classList.toggle('hidden');
        });
        
        document.addEventListener('click', function(e) {
            if (!exportBtn.contains(e.target) && !exportDropdown.contains(e.target)) {
                exportDropdown.classList.add('hidden');
            }
        });
    }
    
    // Rangos rápidos
    const quickRangeButtons = document.querySelectorAll('.quick-range-btn');
    quickRangeButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const range = this.dataset.range;
            const today = new Date();
            let startDate, endDate;

            switch(range) {
                case 'this-month':
                    startDate = new Date(today.getFullYear(), today.getMonth(), 1);
                    endDate = today;
                    break;
                case 'last-month':
                    startDate = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                    endDate = new Date(today.getFullYear(), today.getMonth(), 0);
                    break;
                case 'last-3-months':
                    startDate = new Date(today.getFullYear(), today.getMonth() - 3, 1);
                    endDate = today;
                    break;
                case 'this-year':
                    startDate = new Date(today.getFullYear(), 0, 1);
                    endDate = today;
                    break;
            }

            document.getElementById('start_date').value = startDate.toISOString().split('T')[0];
            document.getElementById('end_date').value = endDate.toISOString().split('T')[0];
            
            // Remover selección de otros botones
            quickRangeButtons.forEach(b => {
                b.style.background = '#f3f4f6';
                b.style.color = '#6b7280';
            });
            // Seleccionar este botón
            this.style.background = '#22c55e';
            this.style.color = '#ffffff';
        });
    });

    // Resetear filtros
    document.getElementById('resetFilters')?.addEventListener('click', function() {
        document.getElementById('reportFiltersForm').reset();
        document.getElementById('start_date').value = '{{ Carbon\Carbon::now()->startOfMonth()->format("Y-m-d") }}';
        document.getElementById('end_date').value = '{{ Carbon\Carbon::now()->format("Y-m-d") }}';
        quickRangeButtons.forEach(b => {
            b.style.background = '#f3f4f6';
            b.style.color = '#6b7280';
        });
        document.getElementById('reportFiltersForm').submit();
    });

    // Auto-submit al cambiar filtros
    const filterInputs = document.querySelectorAll('#reportFiltersForm select, #reportFiltersForm input[type="date"]');
    filterInputs.forEach(input => {
        input.addEventListener('change', function() {
            document.getElementById('reportFiltersForm').submit();
        });
    });

    // Gráfico de Estado (Barras)
    const statusCtx = document.getElementById('statusChart');
    if (statusCtx) {
        new Chart(statusCtx, {
            type: 'bar',
            data: {
                labels: @json($statusLabels),
                datasets: [{
                    label: 'Servicios',
                    data: @json($statusData),
                    backgroundColor: '#3b82f6',
                    borderColor: '#2563eb',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 1.5,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }

    // Gráfico de Tipo (Pastel)
    const typeCtx = document.getElementById('typeChart');
    if (typeCtx) {
        new Chart(typeCtx, {
            type: 'pie',
            data: {
                labels: @json($typeLabels),
                datasets: [{
                    data: @json($typeData),
                    backgroundColor: @json(array_slice($typeColors, 0, count($typeLabels)))
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

    // Gráfico Temporal (Líneas)
    const temporalCtx = document.getElementById('temporalChart');
    if (temporalCtx) {
        const temporalData = @json($temporalData);
        const months = @json($months);
        
        new Chart(temporalCtx, {
            type: 'line',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Completados',
                        data: temporalData.map(d => d.completed),
                        borderColor: '#22c55e',
                        backgroundColor: '#22c55e40',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Pendientes',
                        data: temporalData.map(d => d.pending),
                        borderColor: '#f59e0b',
                        backgroundColor: '#f59e0b40',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Total',
                        data: temporalData.map(d => d.total),
                        borderColor: '#000000',
                        backgroundColor: '#00000040',
                        tension: 0.4,
                        fill: true
                    }
                ]
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
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush
@endsection

