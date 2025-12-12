@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-4 sm:space-y-6 pt-12 md:pt-0">
    <!-- Header -->
    <div class="mb-6">
        <!-- Desktop Header: Título + Buscador + Iconos (todo en la misma línea) -->
        <div class="hidden md:flex md:items-center md:justify-between gap-4">
            <!-- Título Dashboard + Buscador -->
            <div class="flex items-center gap-4">
                <div class="flex-shrink-0">
                    <h2 class="text-2xl sm:text-3xl font-bold leading-7 text-gray-900 dark:text-white sm:truncate sm:tracking-tight text-gray-900 dark:text-white" class="font-bold">
                        Dashboard
                    </h2>
                    <p class="mt-1 text-xs sm:text-sm dark:text-white text-gray-600 dark:text-gray-300">
                        {{ now()->locale('es')->isoFormat('dddd, D [de] MMMM') }}
                    </p>
                </div>

                <!-- Buscador al lado derecho del título -->
                <div class="relative flex-shrink-0" style="min-width: 0;">
                    <div class="relative">
                        <svg class="absolute" style="left: 10px; top: 50%; transform: translateY(-50%); width: 18px; height: 18px; color: #9ca3af; pointer-events: none; z-index: 1;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input
                            type="text"
                            placeholder="Buscar servicios..."
                            class="w-56 pr-3 py-2 sm:py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all text-sm"
                            style="background: white; color: #111827; padding-left: 36px; font-size: 14px;"
                            autocomplete="off"
                        />
                    </div>
                </div>
            </div>

            <!-- Iconos de notificaciones y usuario (desktop) -->
            <div class="flex items-center gap-x-4 flex-shrink-0">
                <!-- Notificaciones -->
                <div class="relative" style="overflow: visible;">
                    <button type="button" class="flex items-center justify-center text-gray-500 hover:text-gray-700 relative" title="Notificaciones" id="tech-notification-button" style="width: 40px !important; height: 40px !important; padding: 8px !important; overflow: visible !important;">
                        <svg style="width: 24px !important; height: 24px !important; display: block !important; flex-shrink: 0 !important;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                        </svg>
                        @php
                            $unreadCount = auth()->check() ? auth()->user()->unreadNotifications()->count() : 0;
                        @endphp
                        @if($unreadCount > 0)
                            <span class="absolute text-white text-xs rounded-full flex items-center justify-center font-semibold">
                                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                            </span>
                        @endif
                    </button>

                    <!-- Notification Dropdown Menu -->
                    <div id="tech-notification-menu" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50" style="max-height: 400px; overflow-y: auto;">
                        <div class="p-3 border-b border-gray-200 flex justify-between items-center">
                            <h3 class="font-semibold text-gray-900">Notificaciones</h3>
                            <a href="{{ route('technician.notifications.index') }}" class="text-sm text-green-600 hover:text-green-700">Ver todas</a>
                        </div>
                        <div class="p-2">
                            @php
                                $recentNotifications = auth()->check() ? auth()->user()->notifications()->take(5)->get() : collect();
                            @endphp
                            @if($recentNotifications->count() > 0)
                                @foreach($recentNotifications as $notification)
                                    @php
                                        $data = is_array($notification->data) ? $notification->data : json_decode($notification->data, true);
                                        $title = $data['title'] ?? 'Notificación';
                                        $message = $data['message'] ?? '';
                                        $isRead = !is_null($notification->read_at);
                                    @endphp
                                    <div class="p-3 hover:bg-gray-50 rounded-lg cursor-pointer {{ !$isRead ? 'bg-green-50' : '' }}">
                                        <div class="flex justify-between items-start">
                                            <h4 class="font-semibold text-sm text-gray-900">{{ $title }}</h4>
                                            <span class="text-xs text-gray-500">{{ $notification->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">{{ Str::limit($message, 80) }}</p>
                                    </div>
                                @endforeach
                            @else
                                <div class="p-6 text-center text-gray-500">
                                    <p>No hay notificaciones</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Usuario -->
                <div class="relative">
                    <button type="button" class="flex items-center justify-center hover:bg-gray-50 rounded-lg transition-colors" id="tech-user-button" title="Menú de usuario" style="width: 40px !important; height: 40px !important; padding: 0 !important;">
                        <div class="bg-green-600 rounded-full flex items-center justify-center" style="width: 32px !important; height: 32px !important;">
                            <span class="text-white font-medium" style="font-size: 13px !important; line-height: 1 !important;">{{ substr(auth()->user()->name ?? 'U', 0, 1) }}</span>
                        </div>
                    </button>

                    <!-- User Dropdown Menu -->
                    <div id="tech-user-menu" class="hidden absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
                        <div class="p-3 border-b border-gray-200">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-full bg-green-500 flex items-center justify-center flex-shrink-0">
                                    <span class="text-sm font-medium text-white">
                                        {{ auth()->check() ? strtoupper(substr(auth()->user()->name, 0, 1)) : 'U' }}
                                    </span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 truncate">{{ auth()->check() ? auth()->user()->name : 'Usuario' }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ auth()->check() ? auth()->user()->email : '' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-2">
                            <a href="{{ route('technician.profile') }}" class="flex items-center gap-3 px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                                <span>Mi Perfil</span>
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                                    </svg>
                                    <span>Cerrar Sesión</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Header (solo título) -->
        <div class="md:hidden">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 text-gray-900 dark:text-white" class="font-bold">
                Dashboard
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                {{ now()->locale('es')->isoFormat('dddd, D [de] MMMM') }}
            </p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 gap-4 sm:gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-6">
        <!-- Completados Hoy -->
        <div class="overflow-hidden rounded-xl bg-white border dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow border border-gray-200 dark:border-gray-700">
            <div class="p-5 sm:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-lg flex items-center justify-center bg-green-500">
                            <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-xs sm:text-sm font-medium mb-1 text-gray-600 dark:text-gray-300">Completados Hoy</p>
                        <p class="text-2xl sm:text-3xl font-bold">{{ $completedToday ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pendientes -->
        <div class="overflow-hidden rounded-xl bg-white border dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow border border-gray-200 dark:border-gray-700">
            <div class="p-5 sm:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-lg flex items-center justify-center bg-amber-500">
                            <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-xs sm:text-sm font-medium mb-1 text-gray-600 dark:text-gray-300">Pendientes</p>
                        <p class="text-2xl sm:text-3xl font-bold">{{ $pendingServices ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- En Progreso -->
        <div class="overflow-hidden rounded-xl bg-white border dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow border border-gray-200 dark:border-gray-700">
            <div class="p-5 sm:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-lg flex items-center justify-center bg-blue-500">
                            <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-xs sm:text-sm font-medium mb-1 text-gray-600 dark:text-gray-300">En Progreso</p>
                        <p class="text-2xl sm:text-3xl font-bold">{{ $inProgressServices ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vencidos -->
        <div class="overflow-hidden rounded-xl bg-white border dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow border border-gray-200 dark:border-gray-700">
            <div class="p-5 sm:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-lg flex items-center justify-center bg-red-500">
                            <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-xs sm:text-sm font-medium mb-1 text-gray-600 dark:text-gray-300">Vencidos</p>
                        <p class="text-2xl sm:text-3xl font-bold">{{ $overdueServices ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Servicios Asignados -->
    <div class="overflow-hidden rounded-lg bg-white border dark:border-gray-700 mb-6 border border-gray-200 dark:border-gray-700">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Servicios Asignados</h3>
                    <p class="text-sm mt-1 text-gray-700 dark:text-gray-300">Próximos servicios a realizar</p>
                </div>
                <a href="{{ route('technician.services') }}" class="text-sm font-medium">Ver todos</a>
            </div>
            <div class="space-y-3">
                @forelse($assignedServices ?? [] as $service)
                <div class="flex items-center justify-between p-4 border-b border-gray-200 last:border-b-0">
                    <div class="flex items-center gap-4 flex-1">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $service->client->name ?? 'N/A' }} - {{ ucfirst(str_replace('-', ' ', $service->service_type ?? 'N/A')) }}</p>
                            <p class="text-xs text-gray-700 dark:text-gray-300">{{ $service->scheduled_date ? $service->scheduled_date->format('d/m/Y H:i') : ($service->created_at->format('d/m/Y H:i')) }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-1 text-xs font-medium rounded-full">
                            {{ ucfirst(str_replace('_',' ',$service->status) ?? 'Pendiente') }}
                        </span>
                        <a href="{{ route('technician.service.detail', $service) }}" class="px-3 py-1.5 text-xs font-medium rounded-md text-white bg-green-500">Ver Detalle</a>
                    </div>
                </div>
                @empty
                <div class="text-center py-8">
                    <p class="text-sm text-gray-700 dark:text-gray-300">No hay servicios asignados</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Acciones Rápidas -->
    <div class="overflow-hidden rounded-lg bg-white border dark:border-gray-700 border border-gray-200 dark:border-gray-700">
        <div class="p-6">
            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Acciones Rápidas</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <a href="{{ route('technician.services') }}" class="flex items-center gap-4 p-4 rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors">
                    <div class="w-12 h-12 rounded-lg flex items-center justify-center bg-green-500">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Ver Mis Servicios</p>
                        <p class="text-xs text-gray-700 dark:text-gray-300">Gestiona todos tus servicios</p>
                    </div>
                </a>
                <a href="{{ route('technician.profile') }}" class="flex items-center gap-4 p-4 rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors">
                    <div class="w-12 h-12 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-gray-900 dark:text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Mi Perfil</p>
                        <p class="text-xs text-gray-700 dark:text-gray-300">Actualiza tu información</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Technician Dashboard Notification and User Menu Dropdowns (Desktop)
    (function() {
        const notificationButton = document.getElementById('tech-notification-button');
        const notificationMenu = document.getElementById('tech-notification-menu');
        const userButton = document.getElementById('tech-user-button');
        const userMenu = document.getElementById('tech-user-menu');

        // Toggle notification menu
        if (notificationButton && notificationMenu) {
            notificationButton.addEventListener('click', function(e) {
                e.stopPropagation();
                notificationMenu.classList.toggle('hidden');
                // Close user menu if open
                if (userMenu) {
                    userMenu.classList.add('hidden');
                }
            });
        }

        // Toggle user menu
        if (userButton && userMenu) {
            userButton.addEventListener('click', function(e) {
                e.stopPropagation();
                userMenu.classList.toggle('hidden');
                // Close notification menu if open
                if (notificationMenu) {
                    notificationMenu.classList.add('hidden');
                }
            });
        }

        // Close menus when clicking outside
        document.addEventListener('click', function(e) {
            if (notificationMenu && !notificationMenu.contains(e.target) && e.target !== notificationButton) {
                notificationMenu.classList.add('hidden');
            }
            if (userMenu && !userMenu.contains(e.target) && e.target !== userButton) {
                userMenu.classList.add('hidden');
            }
        });
    })();
</script>
@endpush
