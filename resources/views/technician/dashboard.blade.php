@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-4 sm:space-y-6 pt-12 md:pt-0">
    <!-- Header -->
    <div class="md:flex md:items-center md:justify-between mb-6">
        <div class="min-w-0 flex-1">
            <h2 class="text-2xl sm:text-3xl font-bold leading-7 text-gray-900 sm:truncate sm:tracking-tight dark:text-white" style="color: #111827; font-weight: 700;">
                Dashboard
            </h2>
            <p class="mt-1 text-sm dark:text-gray-300" style="color: #6b7280;">
                {{ now()->locale('es')->isoFormat('dddd, D [de] MMMM') }}
            </p>
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
                            {{ ucfirst(str_replace('_',' ',$service->status) ?? 'Pendiente') }}
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

@endsection
