@extends('layouts.app')

@section('title', 'Notificaciones')

@section('content')
    <div class="px-4 sm:px-6 lg:px-8 pt-12 md:pt-0">
        <div class="flex items-center justify-between mb-6">
            <div class="flex-1">
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Notificaciones</h1>
                <p class="mt-2 text-sm text-gray-700 dark:text-white">
                    Gestiona todas tus notificaciones del sistema.
                </p>
            </div>
            <div class="ml-4 flex-shrink-0">
                @if($unreadNotifications > 0)
                    <form
                        action="{{ auth()->check() && auth()->user()->hasRole('technician') && !auth()->user()->hasRole('super-admin') ? route('technician.notifications.mark-all-read') : route('notifications.mark-all-read') }}"
                        method="POST" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                            class="inline-flex items-center justify-center rounded-md px-4 py-2 text-sm font-medium text-white shadow-sm hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors bg-green-500">
                            Marcar todas como leídas
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
            <div class="bg-white overflow-hidden shadow rounded-lg border border border-gray-200 dark:border-gray-700">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 rounded-lg flex items-center justify-center">
                                <svg class="h-6 w-6 text-gray-600 dark:text-white" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="text-sm font-medium mb-1 text-gray-700 dark:text-white">Total de notificaciones</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalNotifications }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg border border border-gray-200 dark:border-gray-700">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div
                                class="w-12 h-12 rounded-lg flex items-center justify-center text-gray-900 dark:text-white">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="text-sm font-medium mb-1 text-gray-700 dark:text-white">No leídas</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $unreadNotifications }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg border border border-gray-200 dark:border-gray-700">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div
                                class="w-12 h-12 rounded-lg flex items-center justify-center text-gray-900 dark:text-white">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5a2.25 2.25 0 002.25-2.25m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5a2.25 2.25 0 012.25 2.25v7.5" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="text-sm font-medium mb-1 text-gray-700 dark:text-white">Hoy</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $todayNotifications }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de notificaciones -->
        <div class="mt-8 flow-root">
            <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                        @if($notifications->count() > 0)
                            <ul class="divide-y divide-gray-200">
                                @foreach($notifications as $notification)
                                    @php
                                        $data = is_array($notification->data) ? $notification->data : json_decode($notification->data, true);
                                        $title = $data['title'] ?? 'Notificación';
                                        $message = $data['message'] ?? 'Sin mensaje';
                                        $serviceId = $data['service_id'] ?? null;

                                        // Si no hay service_id en los datos, intentar buscarlo desde el mensaje
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
                                            // Buscar después de dos puntos ": [cliente]" como último recurso
                                            elseif (preg_match('/:\s*([^:]+?)(?:\s+el|\s*$)/i', $message, $clientMatches)) {
                                                $clientName = trim($clientMatches[1]);
                                            }

                                            if ($clientName) {
                                                // Limpiar el nombre del cliente (puede tener espacios extra)
                                                $clientName = trim($clientName);

                                                // Buscar el servicio más reciente asignado a este técnico para ese cliente
                                                // Buscar servicios creados antes o en la fecha de la notificación
                                                $service = \App\Models\Service::where('assigned_to', auth()->id())
                                                    ->where('created_at', '<=', $notification->created_at->copy()->addDay())
                                                    ->whereHas('client', function ($q) use ($clientName) {
                                                        $q->where('name', 'like', '%' . $clientName . '%');
                                                    })
                                                    ->orderBy('created_at', 'desc')
                                                    ->first();

                                                if ($service) {
                                                    $serviceId = $service->id;
                                                }
                                            }

                                            // Si aún no encontramos el servicio, buscar el más reciente del técnico cerca de la fecha de la notificación
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

                                        // Determinar la URL de destino
                                        $notificationUrl = null;
                                        if ($serviceId && auth()->check() && auth()->user()->hasRole('technician') && !auth()->user()->hasRole('super-admin')) {
                                            try {
                                                $notificationUrl = route('technician.service.detail', $serviceId);
                                            } catch (\Exception $e) {
                                                // Si la ruta falla, no establecer URL
                                            }
                                        } elseif ($data['url'] ?? null) {
                                            $notificationUrl = $data['url'];
                                        }
                                    @endphp
                                    <li class="px-6 py-4 {{ $notification->read_at ? 'bg-white text-gray-900 dark:text-white dark:bg-gray-800' : 'bg-blue-50 text-gray-900 dark:text-white dark:bg-gray-800' }} hover:bg-gray-50 transition-colors {{ $notificationUrl ? 'cursor-pointer' : '' }}"
                                        @if($notificationUrl) data-url="{{ $notificationUrl }}" @endif>
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center flex-1" @if($notificationUrl) style="cursor: pointer;"
                                            @endif>
                                                <div class="flex-shrink-0">
                                                    @if($notification->read_at)
                                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                            stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                    @else
                                                        <div class="h-2 w-2 rounded-full bg-red-500"></div>
                                                    @endif
                                                </div>
                                                <div class="ml-4 flex-1">
                                                    <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                                        {{ $title }}
                                                    </div>
                                                    <div class="text-sm mt-1 text-gray-700 dark:text-white">
                                                        {{ $message }}
                                                    </div>
                                                    <div class="text-xs mt-1 text-gray-600 dark:text-white">
                                                        {{ $notification->created_at->diffForHumans() }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex items-center space-x-2 ml-4">
                                                @if(!$notification->read_at)
                                                    <form
                                                        action="{{ auth()->check() && auth()->user()->hasRole('technician') && !auth()->user()->hasRole('super-admin') ? route('technician.notifications.mark-read', $notification->id) : route('notifications.mark-read', $notification->id) }}"
                                                        method="POST" class="inline" onclick="event.stopPropagation();">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit"
                                                            class="text-sm font-medium px-3 py-1.5 rounded-md text-white transition-colors hover:opacity-90 bg-blue-500">
                                                            Marcar como leída
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>

                            <!-- Paginación -->
                            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                                {{ $notifications->links() }}
                            </div>
                        @else
                            <div class="text-center py-12">
                                <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No hay notificaciones</h3>
                                <p class="mt-1 text-sm text-gray-600 dark:text-white">No tienes notificaciones pendientes.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Manejar clicks en las notificaciones
                const notificationItems = document.querySelectorAll('li[data-url]');
                notificationItems.forEach(item => {
                    item.addEventListener('click', function (e) {
                        // No hacer nada si se hace click en el botón o formulario
                        if (e.target.tagName === 'BUTTON' || e.target.closest('button') || e.target.closest('form')) {
                            return;
                        }

                        const url = this.getAttribute('data-url');
                        if (url) {
                            window.location.href = url;
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection