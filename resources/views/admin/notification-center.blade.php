@extends('layouts.app')

@section('title', 'Centro de Notificaciones')

@section('content')
<div class="space-y-4 sm:space-y-6 pt-3 md:pt-0">
    <!-- Header -->
    <div class="mb-6">
        <!-- Mobile Header -->
        <div class="flex items-center gap-3 mb-4 md:hidden">
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
                <h2 class="text-2xl font-bold text-gray-900">Centro de Notificaciones</h2>
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
                        <span class="dark:text-white font-medium text-base">{{ substr(auth()->user()->name ?? 'U', 0, 1) }}</span>
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

        <!-- Desktop Header -->
        <div class="md:flex md:items-center md:justify-between">
            <div class="min-w-0 flex-1 hidden md:block">
                <h2 class="text-3xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight text-gray-900 dark:text-white" class="font-bold">
                    Centro de Notificaciones
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-white">
                    Administra y envía notificaciones a los usuarios del sistema
                </p>
            </div>
            <div class="mt-4 md:mt-0 md:ml-4 flex flex-col sm:flex-row gap-2">
                @if($unreadNotifications > 0)
                <form action="{{ route('admin.notifications.mark-all-read') }}" method="POST" class="inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors w-full sm:w-auto">
                        <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Marcar Todas como Leídas
                    </button>
                </form>
                @endif
               <!-- <button type="button" onclick="openSendModal()" class="inline-flex items-center justify-center px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors w-full sm:w-auto">
                    <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Enviar Notificación
                </button>-->
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

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white dark:bg-gray-800 border dark:border-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="flex-shrink-0 dark:text-white">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="text-gray-600 dark:text-white">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                    </svg>
                </div>
                <div class="ml-4 dark:text-white">
                    <p class="text-sm font-medium text-gray-600 dark:text-white">Total</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalNotifications }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 border dark:border-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="flex-shrink-0 dark:text-white">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="text-gray-600 dark:text-white">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                    </svg>
                </div>
                <div class="ml-4 dark:text-white">
                    <p class="text-sm font-medium text-gray-600 dark:text-white">No Leídas</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $unreadNotifications }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 border dark:border-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="flex-shrink-0 dark:text-white">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="text-gray-600 dark:text-white">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                    </svg>
                </div>
                <div class="ml-4 dark:text-white">
                    <p class="text-sm font-medium text-gray-600 dark:text-white">Hoy</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $todayNotifications }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 border dark:border-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="flex-shrink-0    dark:text-white">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="text-gray-600 dark:text-white">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                    </svg>
                </div>
                <div class="ml-4 dark:text-white">
                    <p class="text-sm font-medium text-gray-600 dark:text-white">Tipos</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ count($notificationsByType) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 border dark:border-gray-700 rounded-lg p-4 mb-6 border border-gray-200 dark:border-gray-700">
        <form method="GET" action="{{ route('admin.notification-center') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium mb-2 text-gray-700 dark:text-white">Buscar</label>
                <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Buscar en notificaciones..."
                       class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                       style="border: 1px solid #e5e7eb !important; ">
            </div>
            <div>
                <label for="read_status" class="block text-sm font-medium mb-2 text-gray-700 dark:text-white">Estado</label>
                <select id="read_status" name="read_status" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent dark:text-white dark:bg-gray-700 dark:border-gray-600" style="border: 1px solid #e5e7eb !important; ">
                    <option value="">Todos</option>
                    <option value="unread" {{ request('read_status') == 'unread' ? 'selected' : '' }}>No Leídas</option>
                    <option value="read" {{ request('read_status') == 'read' ? 'selected' : '' }}>Leídas</option>
                </select>
            </div>

            <div>
                <label for="start_date" class="block text-sm font-medium mb-2 text-gray-700 dark:text-white">Desde</label>
                <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}"
                       class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                       style="border: 1px solid #e5e7eb !important; ">
            </div>

            <div>
                <label for="end_date" class="block text-sm font-medium mb-2 text-gray-700 dark:text-white">Hasta</label>
                <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}"
                       class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                       style="border: 1px solid #e5e7eb !important; ">
            </div>

            <div class="md:col-span-5 flex gap-2">
                <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
                    Filtrar
                </button>
                <a href="{{ route('admin.notification-center') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
                    Limpiar
                </a>
            </div>
        </form>
    </div>

    <!-- Notifications List -->
    <div class="dark:hover:bg-gray-800 border dark:border-gray-700 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
        <!-- Mobile View (Cards) -->
        <div class="md:hidden space-y-4 p-4">
            @forelse($notifications as $notification)
                @php
                    $data = json_decode($notification->data, true);
                    $user = \App\Models\User::find($notification->notifiable_id);
                    $typeName = $notification->type;
                    if (strpos($typeName, '\\') !== false) {
                        $typeParts = explode('\\', $typeName);
                        $typeName = end($typeParts);
                        $typeName = str_replace('Notification', '', $typeName);
                    }
                    $typeLabels = [
                        'ServiceAssigned' => 'Asignado',
                        'ServiceCompleted' => 'Completado',
                        'ServiceUpdated' => 'Actualizado',
                        'Generic' => 'General',
                    ];
                    $typeLabel = $typeLabels[$typeName] ?? (strlen($typeName) > 12 ? substr($typeName, 0, 12) : $typeName);
                @endphp
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 space-y-3">
                    <!-- Header: Tipo y Estado -->
                    <div class="flex justify-between items-start">
                        <span class="px-2 py-1 text-xs font-medium rounded bg-indigo-100 text-indigo-800">
                            {{ $typeLabel }}
                        </span>
                        @if($notification->read_at)
                            <span class="px-2 py-1 text-xs font-medium rounded bg-green-100 text-green-800">
                                Leída
                            </span>
                        @else
                            <span class="px-2 py-1 text-xs font-medium rounded bg-red-100 text-red-800">
                                No Leída
                            </span>
                        @endif
                    </div>

                    <!-- Content -->
                    <div>
                        <h4 class="text-sm font-bold text-gray-900 mb-1">{{ $data['title'] ?? 'Sin título' }}</h4>
                        <p class="text-sm text-gray-600 mb-2">{{ Str::limit($data['message'] ?? '', 150) }}</p>
                        <div class="flex items-center text-xs text-gray-500 gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                            {{ $user->name ?? 'Usuario eliminado' }}
                        </div>
                    </div>

                    <!-- Footer: Fecha y Acciones -->
                    <div class="flex justify-between items-center pt-3 border-t border-gray-100 mt-2">
                        <span class="text-xs text-gray-400">
                            {{ \Carbon\Carbon::parse($notification->created_at)->format('d/m/Y H:i') }}
                        </span>
                        <div class="flex gap-3">
                            @if(!$notification->read_at)
                                <form action="{{ route('admin.notifications.mark-read', $notification->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-green-600 hover:text-green-800 text-sm font-medium">Marcar leída</button>
                                </form>
                            @endif
                            <form action="{{ route('admin.notifications.destroy', $notification->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar esta notificación?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">Eliminar</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-gray-500 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                    No se encontraron notificaciones
                </div>
            @endforelse
        </div>

        <!-- Desktop View (Table) -->
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" style="table-layout: fixed; width: 100%;">
                <colgroup>
                    <col style="width: 11%;">
                    <col style="width: 7%;">
                    <col style="width: 17%;">
                    <col style="width: 30%;">
                    <col style="width: 8%;">
                    <col style="width: 10%;">
                    <col style="width: 17%;">
                </colgroup>
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-600 dark:text-white">Usuario</th>
                        <th class="px-2 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-600 dark:text-white">Tipo</th>
                        <th class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-600 dark:text-white">Título</th>
                        <th class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-600 dark:text-white">Mensaje</th>
                        <th class="px-2 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-600 dark:text-white">Estado</th>
                        <th class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-600 dark:text-white">Fecha</th>
                        <th class="px-3 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-600 dark:text-white">Acciones</th>
                    </tr>
                </thead>
                <tbody class="dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($notifications as $notification)
                        @php
                            $data = json_decode($notification->data, true);
                            $user = \App\Models\User::find($notification->notifiable_id);
                            $typeName = $notification->type;
                            if (strpos($typeName, '\\') !== false) {
                                $typeParts = explode('\\', $typeName);
                                $typeName = end($typeParts);
                                $typeName = str_replace('Notification', '', $typeName);
                            }
                            $typeLabels = [
                                'ServiceAssigned' => 'Asignado',
                                'ServiceCompleted' => 'Completado',
                                'ServiceUpdated' => 'Actualizado',
                                'Generic' => 'General',
                            ];
                            $typeLabel = $typeLabels[$typeName] ?? (strlen($typeName) > 12 ? substr($typeName, 0, 12) : $typeName);
                        @endphp
                        <tr class="dark:hover:bg-gray-50">
                            <td class="px-3 py-4 whitespace-nowrap text-sm dark:text-white">
                                {{ $user->name ?? 'Usuario eliminado' }}
                            </td>
                            <td class="px-2 py-4" style="overflow: hidden; max-width: 0;">
                                <span class="px-1.5 py-0.5 text-xs font-medium rounded whitespace-nowrap inline-block dark:text-white">
                                    {{ $typeLabel }}
                                </span>
                            </td>
                            <td class="px-3 py-4 text-sm dark:text-white">
                                {{ $data['title'] ?? 'Sin título' }}
                            </td>
                            <td class="px-3 py-4 text-sm dark:text-white">
                                {{ Str::limit($data['message'] ?? '', 100) }}
                            </td>
                            <td class="px-2 py-4 whitespace-nowrap dark:text-white" style="overflow: hidden;">
                                @if($notification->read_at)
                                    <span class="px-1.5 py-0.5 text-xs font-medium rounded">
                                        Leída
                                    </span>
                                @else
                                    <span class="px-1.5 py-0.5 text-xs font-medium rounded">
                                        No Leída
                                    </span>
                                @endif
                            </td>
                            <td class="px-3 py-4 whitespace-nowrap text-sm dark:text-white">
                                {{ \Carbon\Carbon::parse($notification->created_at)->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-3 py-4 text-sm font-medium" style="overflow: visible;">
                                <div class="flex flex-col gap-1 items-end">
                                    @if(!$notification->read_at)
                                        <form action="{{ route('admin.notifications.mark-read', $notification->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-green-600 hover:text-green-900 text-xs whitespace-nowrap">Marcar como leída</button>
                                        </form>
                                    @endif
                                    <form action="{{ route('admin.notifications.destroy', $notification->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar esta notificación?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 text-xs whitespace-nowrap">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-600 dark:text-white">
                                No se encontraron notificaciones
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($notifications->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal for Sending Notification -->
<div id="send-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true" id="modal-overlay" onclick="closeSendModal()"></div>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-gray-200 dark:border-gray-700">
            <form action="{{ route('admin.notifications.send') }}" method="POST">
                @csrf
                <div class="bg-white px-6 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="flex items-start justify-between mb-4">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Enviar Notificación</h3>
                        <button type="button" onclick="closeSendModal()" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label for="user_ids" class="block text-sm font-medium mb-2 text-gray-700 dark:text-white">Usuarios *</label>
                            <select id="user_ids" name="user_ids[]" multiple required class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent dark:text-white dark:bg-gray-700 dark:border-gray-600" style="border: 1px solid #e5e7eb !important; color: #111827; min-height: 100px;">
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-600 dark:text-white">Mantén presionado Ctrl (Cmd en Mac) para seleccionar múltiples usuarios</p>
                        </div>

                        <div>
                            <label for="title" class="block text-sm font-medium mb-2 text-gray-700 dark:text-white">Título *</label>
                            <input type="text" id="title" name="title" required
                                   class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                                   style="border: 1px solid #e5e7eb !important; color: #111827;">
                        </div>

                        <div>
                            <label for="message" class="block text-sm font-medium mb-2 text-gray-700 dark:text-white">Mensaje *</label>
                            <textarea id="message" name="message" rows="4" required
                                      class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                                      style="border: 1px solid #e5e7eb !important; color: #111827;"></textarea>
                        </div>

                        <div>
                            <label for="type" class="block text-sm font-medium mb-2 text-gray-700 dark:text-white">Tipo *</label>
                            <select id="type" name="type" required class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent dark:text-white dark:bg-gray-700 dark:border-gray-600" style="border: 1px solid #e5e7eb !important; color: #111827;">
                                <option value="info">Info</option>
                                <option value="success">Éxito</option>
                                <option value="warning">Advertencia</option>
                                <option value="error">Error</option>
                            </select>
                        </div>

                        <div>
                            <label for="url" class="block text-sm font-medium mb-2 text-gray-700 dark:text-white">URL (opcional)</label>
                            <input type="url" id="url" name="url"
                                   class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                                   style="border: 1px solid #e5e7eb !important; color: #111827;">
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-4 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full sm:w-auto sm:ml-3 px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
                        Enviar Notificación
                    </button>
                    <button type="button" onclick="closeSendModal()" class="mt-3 sm:mt-0 w-full sm:w-auto px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Modal functions for sending notifications
function openSendModal() {
    const modal = document.getElementById('send-modal');
    if (modal) {
        modal.classList.remove('hidden');
    }
}

function closeSendModal() {
    const modal = document.getElementById('send-modal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

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
