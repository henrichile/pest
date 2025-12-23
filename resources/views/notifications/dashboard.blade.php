@extends('layouts.app')

@section('title', 'Notificaciones')

@section('content')
    <div class="px-4 sm:px-6 lg:px-8 pt-3 md:pt-0">
        <!-- Header con hamburguesa y título -->
        <div class="mb-4 sm:mb-6">
            <!-- Primera fila: Hamburguesa + Título (móvil) -->
            <div class="flex items-center gap-3 mb-4 md:hidden" style="padding-top: 2.5rem;">
                <!-- Hamburguesa (solo móvil) -->
                <button id="page-mobile-menu-button"
                    class="flex-shrink-0 p-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 shadow-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                    style="z-index: 1000; position: relative;">
                    <svg id="page-menu-icon" class="h-5 w-5 text-gray-900 dark:text-white" fill="none" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                    <svg id="page-close-icon" class="h-5 w-5 hidden text-gray-900 dark:text-white" fill="none"
                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <!-- Título -->
                <div class="flex-1">
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Notificaciones</h1>
                </div>

                <!-- Iconos Header Móvil -->
                <div class="flex items-center gap-4">
                    <!-- Perfil -->
                    @if(auth()->check() && auth()->user()->hasRole('technician') && !auth()->user()->hasRole('super-admin'))
                        <a href="{{ route('technician.profile') }}" class="flex-shrink-0">
                            <div
                                class="h-10 w-10 rounded-full bg-green-600 flex items-center justify-center shadow-sm flex-shrink-0">
                                <span
                                    class="text-white font-medium text-base">{{ substr(auth()->user()->name ?? 'U', 0, 1) }}</span>
                            </div>
                        </a>
                    @endif
                    <!-- Logout -->
                    <form method="POST" action="{{ route('logout') }}" class="flex-shrink-0">
                        @csrf
                        <button type="submit" class="text-gray-500 hover:text-red-600 transition-colors"
                            title="Cerrar Sesión">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Header original (desktop) -->
            <div class="hidden md:flex items-center justify-between">
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

            <!-- Botón marcar como leídas (móvil) -->
            <div class="md:hidden">
                <p class="text-sm text-gray-700 dark:text-white mb-3">
                    Gestiona todas tus notificaciones del sistema.
                </p>
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
                                <svg class="h-6 w-6 text-gray-600 dark:text-white" fill="currentColor"
                                    viewBox="0 0 640 640">
                                    <path
                                        d="M73 39.1C63.6 29.7 48.4 29.7 39.1 39.1C29.8 48.5 29.7 63.7 39 73.1L567 601.1C576.4 610.5 591.6 610.5 600.9 601.1C610.2 591.7 610.3 576.5 600.9 567.2L504.5 470.8C507.2 468.4 509.9 466 512.5 463.6C559.3 420.1 590.6 368.2 605.5 332.5C608.8 324.6 608.8 315.8 605.5 307.9C590.6 272.2 559.3 220.2 512.5 176.8C465.4 133.1 400.7 96.2 319.9 96.2C263.1 96.2 214.3 114.4 173.9 140.4L73 39.1zM236.5 202.7C260 185.9 288.9 176 320 176C399.5 176 464 240.5 464 320C464 351.1 454.1 379.9 437.3 403.5L402.6 368.8C415.3 347.4 419.6 321.1 412.7 295.1C399 243.9 346.3 213.5 295.1 227.2C286.5 229.5 278.4 232.9 271.1 237.2L236.4 202.5zM357.3 459.1C345.4 462.3 332.9 464 320 464C240.5 464 176 399.5 176 320C176 307.1 177.7 294.6 180.9 282.7L101.4 203.2C68.8 240 46.4 279 34.5 307.7C31.2 315.6 31.2 324.4 34.5 332.3C49.4 368 80.7 420 127.5 463.4C174.6 507.1 239.3 544 320.1 544C357.4 544 391.3 536.1 421.6 523.4L357.4 459.2z" />
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

            // Inicializar botón de menú móvil
            (function () {
                function initPageMenuButton() {
                    const pageMenuButton = document.getElementById('page-mobile-menu-button');

                    if (!pageMenuButton) {
                        console.warn('[PAGE MENU] Botón page-mobile-menu-button no encontrado, reintentando...');
                        setTimeout(initPageMenuButton, 100);
                        return;
                    }

                    console.log('[PAGE MENU] Botón encontrado, configurando listener...');

                    pageMenuButton.addEventListener('click', function (e) {
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