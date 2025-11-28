@extends('layouts.app')

@section('title', 'Centro de Notificaciones')

@section('content')
<div class="space-y-4 sm:space-y-6 pt-12 md:pt-0">
    <!-- Header -->
    <div class="md:flex md:items-center md:justify-between mb-6">
        <div class="min-w-0 flex-1">
            <h2 class="text-3xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight" style="color: #111827; font-weight: 700;">
                Centro de Notificaciones
            </h2>
            <p class="mt-1 text-sm" style="color: #6b7280;">
                Administra y envía notificaciones a los usuarios del sistema
            </p>
        </div>
        <div class="mt-4 md:mt-0 md:ml-4 flex flex-col sm:flex-row gap-2">
            @if($unreadNotifications > 0)
            <form action="{{ route('admin.notifications.mark-all-read') }}" method="POST" class="inline">
                @csrf
                @method('PATCH')
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                    <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Marcar Todas como Leídas
                </button>
            </form>
            @endif
            <button type="button" onclick="openSendModal()" class="inline-flex items-center px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
                <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Enviar Notificación
            </button>
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
        <div class="bg-white border dark:border-gray-700 rounded-lg p-4" style="border: 1px solid #e5e7eb !important;">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="color: #6b7280;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium" style="color: #6b7280;">Total</p>
                    <p class="text-2xl font-bold" style="color: #111827;">{{ $totalNotifications }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white border dark:border-gray-700 rounded-lg p-4" style="border: 1px solid #e5e7eb !important;">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="color: #ef4444;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium" style="color: #6b7280;">No Leídas</p>
                    <p class="text-2xl font-bold" style="color: #111827;">{{ $unreadNotifications }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white border dark:border-gray-700 rounded-lg p-4" style="border: 1px solid #e5e7eb !important;">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="color: #22c55e;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium" style="color: #6b7280;">Hoy</p>
                    <p class="text-2xl font-bold" style="color: #111827;">{{ $todayNotifications }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white border dark:border-gray-700 rounded-lg p-4" style="border: 1px solid #e5e7eb !important;">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="color: #3b82f6;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium" style="color: #6b7280;">Tipos</p>
                    <p class="text-2xl font-bold" style="color: #111827;">{{ count($notificationsByType) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white border dark:border-gray-700 rounded-lg p-4 mb-6" style="border: 1px solid #e5e7eb !important;">
        <form method="GET" action="{{ route('admin.notification-center') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium mb-2" style="color: #374151;">Buscar</label>
                <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Buscar en notificaciones..."
                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                       style="border: 1px solid #e5e7eb !important; color: #111827;">
            </div>

            <div>
                <label for="type" class="block text-sm font-medium mb-2" style="color: #374151;">Tipo</label>
                <select id="type" name="type" class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" style="border: 1px solid #e5e7eb !important; color: #111827;">
                    <option value="">Todos</option>
                    @foreach($notificationsByType as $type => $count)
                        <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="read_status" class="block text-sm font-medium mb-2" style="color: #374151;">Estado</label>
                <select id="read_status" name="read_status" class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" style="border: 1px solid #e5e7eb !important; color: #111827;">
                    <option value="">Todos</option>
                    <option value="unread" {{ request('read_status') == 'unread' ? 'selected' : '' }}>No Leídas</option>
                    <option value="read" {{ request('read_status') == 'read' ? 'selected' : '' }}>Leídas</option>
                </select>
            </div>

            <div>
                <label for="start_date" class="block text-sm font-medium mb-2" style="color: #374151;">Desde</label>
                <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}"
                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                       style="border: 1px solid #e5e7eb !important; color: #111827;">
            </div>

            <div>
                <label for="end_date" class="block text-sm font-medium mb-2" style="color: #374151;">Hasta</label>
                <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}"
                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                       style="border: 1px solid #e5e7eb !important; color: #111827;">
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
    <div class="bg-white border dark:border-gray-700 rounded-lg overflow-hidden" style="border: 1px solid #e5e7eb !important;">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: #6b7280; width: 13%;">Usuario</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: #6b7280; width: 6%;">Tipo</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: #6b7280; width: 18%;">Título</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: #6b7280; width: 33%;">Mensaje</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: #6b7280; width: 8%;">Estado</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: #6b7280; width: 10%;">Fecha</th>
                        <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider" style="color: #6b7280; width: 12%;">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($notifications as $notification)
                        @php
                            $data = json_decode($notification->data, true);
                            $user = \App\Models\User::find($notification->notifiable_id);
                            // Extraer nombre corto del tipo de notificación
                            $typeName = $notification->type;
                            if (strpos($typeName, '\\') !== false) {
                                $typeParts = explode('\\', $typeName);
                                $typeName = end($typeParts);
                                // Remover "Notification" del final si existe
                                $typeName = str_replace('Notification', '', $typeName);
                            }
                            // Mapear tipos comunes a nombres más legibles
                            $typeLabels = [
                                'ServiceAssigned' => 'Servicio Asignado',
                                'ServiceCompleted' => 'Servicio Completado',
                                'ServiceUpdated' => 'Servicio Actualizado',
                                'Generic' => 'General',
                            ];
                            $typeLabel = $typeLabels[$typeName] ?? $typeName;
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-4 whitespace-nowrap text-sm" style="color: #111827;">
                                {{ $user->name ?? 'Usuario eliminado' }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium rounded-full whitespace-nowrap" style="background: #e0e7ff; color: #3730a3; display: inline-block;">
                                    {{ $typeLabel }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-sm" style="color: #111827;">
                                {{ $data['title'] ?? 'Sin título' }}
                            </td>
                            <td class="px-4 py-4 text-sm" style="color: #6b7280;">
                                {{ Str::limit($data['message'] ?? '', 60) }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                @if($notification->read_at)
                                    <span class="px-2 py-1 text-xs font-medium rounded-full" style="background: #d1fae5; color: #065f46;">
                                        Leída
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium rounded-full" style="background: #fee2e2; color: #991b1b;">
                                        No Leída
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm" style="color: #6b7280;">
                                {{ \Carbon\Carbon::parse($notification->created_at)->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium">
                                @if(!$notification->read_at)
                                    <form action="{{ route('admin.notifications.mark-read', $notification->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-green-600 hover:text-green-900 mr-2 text-xs">Marcar como leída</button>
                                    </form>
                                @endif
                                <form action="{{ route('admin.notifications.destroy', $notification->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar esta notificación?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 text-xs">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-sm" style="color: #6b7280;">
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

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full" style="border: 1px solid #e5e7eb !important;">
            <form action="{{ route('admin.notifications.send') }}" method="POST">
                @csrf
                <div class="bg-white px-6 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="flex items-start justify-between mb-4">
                        <h3 class="text-2xl font-bold" style="color: #111827;">Enviar Notificación</h3>
                        <button type="button" onclick="closeSendModal()" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label for="user_ids" class="block text-sm font-medium mb-2" style="color: #374151;">Usuarios *</label>
                            <select id="user_ids" name="user_ids[]" multiple required class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" style="border: 1px solid #e5e7eb !important; color: #111827; min-height: 100px;">
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs" style="color: #6b7280;">Mantén presionado Ctrl (Cmd en Mac) para seleccionar múltiples usuarios</p>
                        </div>

                        <div>
                            <label for="title" class="block text-sm font-medium mb-2" style="color: #374151;">Título *</label>
                            <input type="text" id="title" name="title" required
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                                   style="border: 1px solid #e5e7eb !important; color: #111827;">
                        </div>

                        <div>
                            <label for="message" class="block text-sm font-medium mb-2" style="color: #374151;">Mensaje *</label>
                            <textarea id="message" name="message" rows="4" required
                                      class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                                      style="border: 1px solid #e5e7eb !important; color: #111827;"></textarea>
                        </div>

                        <div>
                            <label for="type" class="block text-sm font-medium mb-2" style="color: #374151;">Tipo *</label>
                            <select id="type" name="type" required class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" style="border: 1px solid #e5e7eb !important; color: #111827;">
                                <option value="info">Info</option>
                                <option value="success">Éxito</option>
                                <option value="warning">Advertencia</option>
                                <option value="error">Error</option>
                            </select>
                        </div>

                        <div>
                            <label for="url" class="block text-sm font-medium mb-2" style="color: #374151;">URL (opcional)</label>
                            <input type="url" id="url" name="url"
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
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
    function openSendModal() {
        document.getElementById('send-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeSendModal() {
        document.getElementById('send-modal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeSendModal();
        }
    });
</script>
@endpush
@endsection


