@extends('layouts.app')

@section('title', 'Dashboard')

@php
    $headerNotifications = auth()->check() ? auth()->user()->notifications()->orderBy('created_at', 'desc')->limit(10)->get() : collect();
    $headerUnreadCount = auth()->check() ? auth()->user()->unreadNotifications()->count() : 0;
    $completedToday = $completedToday ?? 0;
@endphp

@push('styles')
<style>
    /* Asegurar que los iconos tengan el tamaño correcto desde el inicio - CRÍTICO */
    #notification-button,
    #user-menu-button {
        width: 40px !important;
        height: 40px !important;
        min-width: 40px !important;
        min-height: 40px !important;
        max-width: 40px !important;
        max-height: 40px !important;
        padding: 8px !important;
        box-sizing: border-box !important;
        flex-shrink: 0 !important;
        overflow: visible !important;
    }

    #notification-button {
        padding: 8px !important;
    }

    #user-menu-button {
        padding: 0 !important;
    }

    #notification-button svg {
        width: 24px !important;
        height: 24px !important;
        min-width: 24px !important;
        min-height: 24px !important;
        max-width: 24px !important;
        max-height: 24px !important;
        display: block !important;
        flex-shrink: 0 !important;
    }

    #user-menu-button > div {
        width: 40px !important;
        height: 40px !important;
        min-width: 40px !important;
        min-height: 40px !important;
        max-width: 40px !important;
        max-height: 40px !important;
        box-sizing: border-box !important;
    }

    #user-menu-button > div > span {
        font-size: 14px !important;
        line-height: 1 !important;
    }

    /* Estilos para los menús dropdown */
    .notification-dropdown {
        position: relative;
    }

    .notification-dropdown::before {
        content: '';
        position: absolute;
        top: 100%;
        right: 0;
        left: -50px;
        height: 12px;
        z-index: 999;
    }

    .notification-menu {
        position: absolute;
        top: calc(100% + 12px);
        right: 0;
        width: 380px;
        max-width: calc(100vw - 2rem);
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15), 0 4px 6px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
        z-index: 1000;
        overflow: hidden;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: opacity 0.2s ease, visibility 0.2s ease, transform 0.2s ease;
    }

    .notification-menu.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .notification-menu-header {
        padding: 16px 20px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #f9fafb;
    }

    .notification-menu-content {
        max-height: 400px;
        overflow-y: auto;
        padding: 8px;
    }

    .notification-item {
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 4px;
        cursor: pointer;
        transition: background-color 0.15s ease;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
    }

    .notification-item:hover {
        background-color: #f3f4f6;
    }

    .notification-item.unread {
        background-color: #f0fdf4;
    }

    .notification-item-content {
        flex: 1;
        min-width: 0;
    }

    .notification-item-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 4px;
    }

    .notification-time {
        color: #9ca3af;
        font-size: 12px;
        white-space: nowrap;
        margin-left: 12px;
    }

    .notification-dot {
        width: 8px;
        height: 8px;
        background: #22c55e;
        border-radius: 50%;
        margin-left: 12px;
        margin-top: 6px;
        flex-shrink: 0;
    }

    .notification-empty {
        padding: 40px 20px;
        text-align: center;
    }

    /* Estilos del menú de usuario */
    .user-menu-dropdown {
        position: relative;
    }

    .user-menu-dropdown::before {
        content: '';
        position: absolute;
        top: 100%;
        right: 0;
        left: -50px;
        height: 12px;
        z-index: 999;
    }

    .user-menu {
        position: absolute !important;
        top: calc(100% + 12px) !important;
        right: 0 !important;
        width: 280px !important;
        max-width: calc(100vw - 2rem) !important;
        background: white !important;
        border-radius: 12px !important;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15), 0 4px 6px rgba(0, 0, 0, 0.1) !important;
        border: 1px solid #e5e7eb !important;
        z-index: 1000 !important;
        overflow: hidden !important;
        opacity: 0 !important;
        visibility: hidden !important;
        transform: translateY(-10px) !important;
        transition: opacity 0.2s ease, visibility 0.2s ease, transform 0.2s ease !important;
    }

    .user-menu.show {
        opacity: 1 !important;
        visibility: visible !important;
        transform: translateY(0) !important;
    }

    .user-menu-header {
        padding: 20px !important;
        border-bottom: 1px solid #e5e7eb !important;
        background: #f9fafb !important;
    }

    .user-menu-profile {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .user-menu-info {
        flex: 1;
        min-width: 0;
    }

    .user-menu-name {
        font-size: 16px;
        font-weight: 600;
        color: #111827;
        margin-bottom: 4px;
    }

    .user-menu-email {
        font-size: 14px;
        color: #6b7280;
    }

    .user-menu-content {
        padding: 8px;
    }

    .user-menu-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px;
        border-radius: 8px;
        color: #111827;
        text-decoration: none;
        transition: background-color 0.15s ease;
        font-size: 14px;
        width: 100%;
        border: none;
        background: none;
        cursor: pointer;
    }

    .user-menu-item:hover {
        background-color: #f3f4f6;
    }

    .user-menu-item-danger {
        color: #dc2626;
    }

    .user-menu-item-danger:hover {
        background-color: #fef2f2;
    }

    .user-menu-icon {
        width: 20px !important;
        height: 20px !important;
        flex-shrink: 0;
    }

    .user-menu-divider {
        height: 1px;
        background: #e5e7eb;
        margin: 8px 0;
    }

    .user-menu-form {
        margin: 0;
        padding: 0;
    }
</style>
@endpush

@section('content')
<div class="space-y-4 sm:space-y-6 mx-4 sm:mx-6 lg:mx-8 md:pt-6" style="padding-top: 80px;">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
        <div class="min-w-0 flex-1">
            <h2 class="text-2xl sm:text-3xl font-bold leading-7 text-gray-900 sm:truncate sm:tracking-tight dark:text-white" style="color: #111827; font-weight: 700;">
                Dashboard
            </h2>
            <p class="mt-1 text-sm dark:text-gray-300" style="color: #374151;">
                {{ now()->locale('es')->isoFormat('dddd, D [de] MMMM') }}
            </p>
        </div>
        <!-- Notificaciones y Usuario (solo desktop) -->
        <div class="hidden md:flex items-center gap-x-4 sm:gap-x-6 md:ml-4 md:mt-0 flex-shrink-0">
            <!-- Notifications -->
            <div class="relative notification-dropdown" id="notification-dropdown" style="margin-right: 8px; overflow: visible;">
                <button type="button" class="flex items-center justify-center p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 relative" title="Notificaciones" id="notification-button" style="position: relative; overflow: visible;">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                    </svg>
                    @if($headerUnreadCount > 0)
                    <span class="absolute text-white text-xs rounded-full flex items-center justify-center font-semibold" style="background: #22c55e; min-width: 20px; height: 20px; padding: 0 6px; top: -2px; right: -2px; z-index: 20; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                        {{ $headerUnreadCount > 99 ? '99+' : $headerUnreadCount }}
                    </span>
                    @endif
                </button>

                <!-- Dropdown Menu -->
                <div class="notification-menu" id="notification-menu" style="opacity: 0; visibility: hidden; transform: translateY(-10px);">
                    <div class="notification-menu-header">
                        <h3 style="color: #111827; font-weight: 600; font-size: 16px;">Notificaciones</h3>
                        @if(auth()->check() && auth()->user()->hasRole('technician') && !auth()->user()->hasRole('super-admin'))
                            <a href="{{ route('technician.notifications.index') ?? '#' }}" style="color: #22c55e; font-size: 14px; font-weight: 500; text-decoration: none;">Ver todas</a>
                        @else
                            <a href="{{ route('admin.notification-center') ?? '#' }}" style="color: #22c55e; font-size: 14px; font-weight: 500; text-decoration: none;">Ver todas</a>
                        @endif
                    </div>
                    <div class="notification-menu-content">
                        @if($headerNotifications->count() > 0)
                            @foreach($headerNotifications->take(8) as $notification)
                                @php
                                    $data = is_array($notification->data) ? $notification->data : json_decode($notification->data, true);
                                    $title = $data['title'] ?? 'Notificación';
                                    $message = $data['message'] ?? '';
                                    $type = $data['type'] ?? 'info';
                                    $isRead = !is_null($notification->read_at);
                                    $serviceId = $data['service_id']
                                        ?? ($data['service']['id'] ?? null)
                                        ?? ($data['serviceId'] ?? null)
                                        ?? ($data['metadata']['service_id'] ?? null);

                                    // Si no hay service_id, intentar buscarlo desde el mensaje
                                    if (!$serviceId && auth()->check() && ($title === 'Nuevo Servicio Asignado' || strpos($message, 'servicio') !== false)) {
                                        $clientName = null;

                                        // Buscar formato "servicio: [cliente]" - ejemplo: "Se te ha asignado un nuevo servicio: Venta Renta"
                                        if (preg_match('/servicio[:\s]+([^:]+?)(?:\s+el|\s*$)/i', $message, $clientMatches)) {
                                            $clientName = trim($clientMatches[1]);
                                        }
                                        // Buscar formato "para [cliente] el" - ejemplo: "Se te ha asignado un nuevo servicio para Venta Renta el 07/11/2025"
                                        elseif (preg_match('/para\s+([^:]+?)\s+el/i', $message, $clientMatches)) {
                                            $clientName = trim($clientMatches[1]);
                                        }

                                        if ($clientName) {
                                            $clientName = trim($clientName);

                                            // Buscar el servicio más reciente asignado a este técnico para ese cliente
                                            $service = \App\Models\Service::where('assigned_to', auth()->id())
                                                ->where('created_at', '<=', $notification->created_at->copy()->addDay())
                                                ->whereHas('client', function($q) use ($clientName) {
                                                    $q->where('name', 'like', '%' . $clientName . '%');
                                                })
                                                ->orderBy('created_at', 'desc')
                                                ->first();

                                            if ($service) {
                                                $serviceId = $service->id;
                                            }
                                        }

                                        // Si aún no encontramos el servicio, buscar el más reciente del técnico cerca de la fecha
                                        if (!$serviceId) {
                                            $service = \App\Models\Service::where('assigned_to', auth()->id())
                                                ->whereBetween('created_at', [
                                                    $notification->created_at->copy()->subDays(30),
                                                    $notification->created_at->copy()->addDays(1)
                                                ])
                                                ->orderBy('created_at', 'desc')
                                                ->first();

                                            if ($service) {
                                                $serviceId = $service->id;
                                            }
                                        }
                                    }

                                    $notificationUrl = null;
                                    if ($serviceId && auth()->check() && auth()->user()->hasRole('technician') && !auth()->user()->hasRole('super-admin')) {
                                        // Verificar que el servicio existe y está asignado al técnico antes de generar la URL
                                        $service = \App\Models\Service::where('id', $serviceId)
                                            ->where('assigned_to', auth()->id())
                                            ->first();

                                        if ($service) {
                                            try {
                                                $notificationUrl = route('technician.service.detail', $serviceId);
                                            } catch (\Exception $e) {
                                                $notificationUrl = null;
                                            }
                                        }
                                    }

                                    if (!$notificationUrl && !empty($data['url'])) {
                                        $notificationUrl = $data['url'];
                                    }

                                    if (!$notificationUrl && auth()->check() && auth()->user()->hasRole('technician') && !auth()->user()->hasRole('super-admin')) {
                                        $notificationUrl = route('technician.notifications.index');
                                    } elseif (!$notificationUrl) {
                                        $notificationUrl = route('admin.notification-center');
                                    }
                                @endphp
                                <div class="notification-item {{ !$isRead ? 'unread' : '' }}" data-notification-id="{{ $notification->id }}" data-url="{{ $notificationUrl }}">
                                    <div class="notification-item-content">
                                        <div class="notification-item-header">
                                            <h4 style="color: #111827; font-weight: 600; font-size: 14px; margin: 0 0 4px 0;">{{ $title }}</h4>
                                            <span class="notification-time">{{ $notification->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p style="color: #6b7280; font-size: 13px; margin: 0; line-height: 1.4;">{{ Str::limit($message, 80) }}</p>
                                    </div>
                                    @if(!$isRead)
                                    <div class="notification-dot"></div>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <div class="notification-empty">
                                <p style="color: #6b7280; font-size: 14px; text-align: center; padding: 20px;">No hay notificaciones</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Profile dropdown -->
            <div class="relative user-menu-dropdown" id="user-menu-dropdown" style="margin-left: 8px;">
                <button type="button" class="flex items-center justify-center p-2 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition-colors" id="user-menu-button" title="Menú de usuario">
                    <div class="w-10 h-10 bg-green-600 rounded-full flex items-center justify-center">
                        <span class="text-white text-sm font-medium">{{ substr(auth()->user()->name ?? 'U', 0, 1) }}</span>
                    </div>
                </button>

                <!-- User Menu -->
                <div class="user-menu" id="user-menu" style="opacity: 0; visibility: hidden; transform: translateY(-10px);">
                    <div class="user-menu-header">
                        <div class="user-menu-profile">
                            <div class="w-12 h-12 bg-green-600 rounded-full flex items-center justify-center">
                                <span class="text-white text-lg font-medium">{{ substr(auth()->user()->name ?? 'U', 0, 1) }}</span>
                            </div>
                            <div class="user-menu-info">
                                <div class="user-menu-name">{{ auth()->user()->name ?? 'Usuario' }}</div>
                                <div class="user-menu-email">{{ auth()->user()->email ?? '' }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="user-menu-content">
                        <a href="{{ route('technician.profile') }}" class="user-menu-item">
                            <svg class="user-menu-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                            <span>Mi Perfil</span>
                        </a>
                        <div class="user-menu-divider"></div>
                        <form method="POST" action="{{ route('logout') }}" class="user-menu-form">
                            @csrf
                            <button type="submit" class="user-menu-item user-menu-item-danger">
                                <svg class="user-menu-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
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

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 gap-4 sm:gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-6">
        <!-- Completados Hoy -->
        <div class="overflow-hidden rounded-xl bg-white border dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow" style="border: 1px solid #e5e7eb !important;">
            <div class="p-5 sm:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-lg flex items-center justify-center" style="background: #22c55e;">
                            <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-xs sm:text-sm font-medium mb-1" style="color: #6b7280;">Completados Hoy</p>
                        <p class="text-2xl sm:text-3xl font-bold" style="color: #000000;">{{ $completedToday ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pendientes -->
        <div class="overflow-hidden rounded-xl bg-white border dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow" style="border: 1px solid #e5e7eb !important;">
            <div class="p-5 sm:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-lg flex items-center justify-center" style="background: #f59e0b;">
                            <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-xs sm:text-sm font-medium mb-1" style="color: #6b7280;">Pendientes</p>
                        <p class="text-2xl sm:text-3xl font-bold" style="color: #000000;">{{ $pendingServices ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- En Progreso -->
        <div class="overflow-hidden rounded-xl bg-white border dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow" style="border: 1px solid #e5e7eb !important;">
            <div class="p-5 sm:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-lg flex items-center justify-center" style="background: #3b82f6;">
                            <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-xs sm:text-sm font-medium mb-1" style="color: #6b7280;">En Progreso</p>
                        <p class="text-2xl sm:text-3xl font-bold" style="color: #000000;">{{ $inProgressServices ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vencidos -->
        <div class="overflow-hidden rounded-xl bg-white border dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow" style="border: 1px solid #e5e7eb !important;">
            <div class="p-5 sm:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-lg flex items-center justify-center" style="background: #ef4444;">
                            <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-xs sm:text-sm font-medium mb-1" style="color: #6b7280;">Vencidos</p>
                        <p class="text-2xl sm:text-3xl font-bold" style="color: #000000;">{{ $overdueServices ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Servicios Asignados -->
    <div class="overflow-hidden rounded-lg bg-white border dark:border-gray-700 mb-6" style="border: 1px solid #e5e7eb !important;">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-semibold" style="color: #111827;">Servicios Asignados</h3>
                    <p class="text-sm mt-1" style="color: #374151;">Próximos servicios a realizar</p>
                </div>
                <a href="{{ route('technician.services') }}" class="text-sm font-medium" style="color: #22c55e;">Ver todos</a>
            </div>
            <div class="space-y-3">
                @forelse($assignedServices ?? [] as $service)
                <div class="flex items-center justify-between p-4 border-b border-gray-200 last:border-b-0">
                    <div class="flex items-center gap-4 flex-1">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: #fef3c7;">
                                <svg class="w-5 h-5" style="color: #f59e0b;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium" style="color: #111827;">{{ $service->client->name ?? 'N/A' }} - {{ ucfirst(str_replace('-', ' ', $service->service_type ?? 'N/A')) }}</p>
                            <p class="text-xs" style="color: #374151;">{{ $service->scheduled_date ? $service->scheduled_date->format('d/m/Y H:i') : ($service->created_at->format('d/m/Y H:i')) }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-1 text-xs font-medium rounded-full" style="background: #fef3c7; color: #92400e;">
                            {{ ucfirst(str_replace('-',' ',$service->status) ?? 'Pendiente') }}
                        </span>
                        <a href="{{ route('technician.service.detail', $service) }}" class="px-3 py-1.5 text-xs font-medium rounded-md text-white" style="background: #22c55e;">Ver Detalle</a>
                    </div>
                </div>
                @empty
                <div class="text-center py-8">
                    <p class="text-sm" style="color: #374151;">No hay servicios asignados</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Acciones Rápidas -->
    <div class="overflow-hidden rounded-lg bg-white border dark:border-gray-700" style="border: 1px solid #e5e7eb !important;">
        <div class="p-6">
            <h3 class="text-lg font-semibold mb-4" style="color: #111827;">Acciones Rápidas</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <a href="{{ route('technician.services') }}" class="flex items-center gap-4 p-4 rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors">
                    <div class="w-12 h-12 rounded-lg flex items-center justify-center" style="background: #22c55e;">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium" style="color: #111827;">Ver Mis Servicios</p>
                        <p class="text-xs" style="color: #374151;">Gestiona todos tus servicios</p>
                    </div>
                </a>
                <a href="{{ route('technician.profile') }}" class="flex items-center gap-4 p-4 rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors">
                    <div class="w-12 h-12 rounded-lg flex items-center justify-center" style="background: #f3f4f6;">
                        <svg class="w-6 h-6" style="color: #111827;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium" style="color: #111827;">Mi Perfil</p>
                        <p class="text-xs" style="color: #374151;">Actualiza tu información</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Dashboard script loaded');

        // Setup notification dropdown
        const notificationDropdown = document.getElementById('notification-dropdown');
        const notificationButton = document.getElementById('notification-button');
        const notificationMenu = document.getElementById('notification-menu');

        console.log('Notification elements:', { notificationDropdown, notificationButton, notificationMenu });

        if (notificationDropdown && notificationButton && notificationMenu) {
            let hideTimeout = null;
            let showTimeout = null;

            function showNotificationMenu() {
                console.log('Showing notification menu');
                if (hideTimeout) {
                    clearTimeout(hideTimeout);
                    hideTimeout = null;
                }
                if (showTimeout) {
                    clearTimeout(showTimeout);
                }
                showTimeout = setTimeout(function() {
                    notificationMenu.style.display = 'block';
                    notificationMenu.style.opacity = '0';
                    notificationMenu.style.visibility = 'visible';
                    notificationMenu.offsetHeight; // Force reflow
                    requestAnimationFrame(function() {
                        notificationMenu.classList.add('show');
                        notificationMenu.style.opacity = '1';
                        notificationMenu.style.transform = 'translateY(0)';
                    });
                    showTimeout = null;
                }, 10);
            }

            function hideNotificationMenu() {
                console.log('Hiding notification menu');
                if (showTimeout) {
                    clearTimeout(showTimeout);
                    showTimeout = null;
                }
                if (hideTimeout) {
                    clearTimeout(hideTimeout);
                }
                hideTimeout = setTimeout(function() {
                    notificationMenu.classList.remove('show');
                    notificationMenu.style.opacity = '0';
                    notificationMenu.style.transform = 'translateY(-10px)';
                    setTimeout(function() {
                        notificationMenu.style.display = 'none';
                        notificationMenu.style.visibility = 'hidden';
                    }, 200);
                    hideTimeout = null;
                }, 200);
            }

            notificationDropdown.addEventListener('mouseenter', showNotificationMenu);
            notificationButton.addEventListener('mouseenter', showNotificationMenu);
            notificationMenu.addEventListener('mouseenter', showNotificationMenu);
            notificationDropdown.addEventListener('mouseleave', function(e) {
                if (!notificationMenu.contains(e.relatedTarget) && !notificationButton.contains(e.relatedTarget)) {
                    hideNotificationMenu();
                }
            });
            notificationMenu.addEventListener('mouseleave', function(e) {
                if (!notificationDropdown.contains(e.relatedTarget) && !notificationButton.contains(e.relatedTarget)) {
                    hideNotificationMenu();
                }
            });

            // Mark as read on click
            const notificationItems = document.querySelectorAll('.notification-item');
            notificationItems.forEach(item => {
                item.addEventListener('click', function() {
                    const notificationId = this.getAttribute('data-notification-id');
                    if (notificationId) {
                        fetch(`/notifications/${notificationId}/mark-read`, {
                            method: 'PATCH',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                            },
                        }).then(response => {
                            if (response.ok) {
                                this.classList.remove('unread');
                                const dot = this.querySelector('.notification-dot');
                                if (dot) dot.remove();
                                const badge = document.querySelector('.notification-dropdown span');
                                if (badge) {
                                    const currentCount = parseInt(badge.textContent) || 0;
                                    const newCount = currentCount - 1;
                                    if (newCount > 0) {
                                        badge.textContent = newCount > 99 ? '99+' : newCount;
                                    } else {
                                        badge.remove();
                                    }
                                }
                            }
                        }).catch(error => {
                            console.error('Error marking notification as read:', error);
                        });
                    }
                });
            });
        }

        // Setup user menu dropdown
        const userDropdown = document.getElementById('user-menu-dropdown');
        const userButton = document.getElementById('user-menu-button');
        const userMenu = document.getElementById('user-menu');

        console.log('User menu elements:', { userDropdown, userButton, userMenu });

        if (userDropdown && userButton && userMenu) {
            let hideTimeout = null;
            let showTimeout = null;

            function showUserMenu() {
                console.log('Showing user menu');
                if (hideTimeout) {
                    clearTimeout(hideTimeout);
                    hideTimeout = null;
                }
                if (showTimeout) {
                    clearTimeout(showTimeout);
                }
                showTimeout = setTimeout(function() {
                    userMenu.style.display = 'block';
                    userMenu.style.opacity = '0';
                    userMenu.style.visibility = 'visible';
                    userMenu.offsetHeight; // Force reflow
                    requestAnimationFrame(function() {
                        userMenu.classList.add('show');
                        userMenu.style.opacity = '1';
                        userMenu.style.transform = 'translateY(0)';
                    });
                    showTimeout = null;
                }, 10);
            }

            function hideUserMenu() {
                console.log('Hiding user menu');
                if (showTimeout) {
                    clearTimeout(showTimeout);
                    showTimeout = null;
                }
                if (hideTimeout) {
                    clearTimeout(hideTimeout);
                }
                hideTimeout = setTimeout(function() {
                    userMenu.classList.remove('show');
                    userMenu.style.opacity = '0';
                    userMenu.style.transform = 'translateY(-10px)';
                    setTimeout(function() {
                        userMenu.style.display = 'none';
                        userMenu.style.visibility = 'hidden';
                    }, 200);
                    hideTimeout = null;
                }, 200);
            }

            userDropdown.addEventListener('mouseenter', showUserMenu);
            userButton.addEventListener('mouseenter', showUserMenu);
            userMenu.addEventListener('mouseenter', showUserMenu);
            userDropdown.addEventListener('mouseleave', function(e) {
                if (!userMenu.contains(e.relatedTarget) && !userButton.contains(e.relatedTarget)) {
                    hideUserMenu();
                }
            });
            userMenu.addEventListener('mouseleave', function(e) {
                if (!userDropdown.contains(e.relatedTarget) && !userButton.contains(e.relatedTarget)) {
                    hideUserMenu();
                }
            });
        }
    });
</script>
@endpush
@endsection
