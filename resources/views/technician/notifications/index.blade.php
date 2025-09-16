@extends("layouts.app")

@section("title", "Centro de Notificaciones - Pest Controller SAT")
@section("page-title", "Centro de Notificaciones")

@section("content")
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Centro de Notificaciones</h2>
            <p class="text-gray-600">Gestiona tus notificaciones y alertas</p>
        </div>
        <div class="flex items-center space-x-4">
            @if($stats['unread'] > 0)
            <form method="POST" action="{{ route('technician.notifications.mark-all-read') }}" class="inline">
                @csrf
                @method('PATCH')
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Marcar todas como leídas
                </button>
            </form>
            @endif
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM9 17H4l5 5v-5zM12 3v18"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-orange-100 rounded-lg">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">No leídas</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['unread'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Leídas</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['read'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-red-100 rounded-lg">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Urgentes</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['urgent'] + $stats['high'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" action="{{ route('technician.notifications.index') }}" class="flex flex-wrap gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <select name="status" class="border border-gray-300 rounded-lg px-3 py-2">
                    <option value="">Todos</option>
                    <option value="unread" {{ request('status') == 'unread' ? 'selected' : '' }}>No leídas</option>
                    <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>Leídas</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                <select name="type" class="border border-gray-300 rounded-lg px-3 py-2">
                    <option value="">Todos</option>
                    <option value="info" {{ request('type') == 'info' ? 'selected' : '' }}>Información</option>
                    <option value="success" {{ request('type') == 'success' ? 'selected' : '' }}>Éxito</option>
                    <option value="warning" {{ request('type') == 'warning' ? 'selected' : '' }}>Advertencia</option>
                    <option value="error" {{ request('type') == 'error' ? 'selected' : '' }}>Error</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Prioridad</label>
                <select name="priority" class="border border-gray-300 rounded-lg px-3 py-2">
                    <option value="">Todas</option>
                    <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgente</option>
                    <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>Alta</option>
                    <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Media</option>
                    <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Baja</option>
                </select>
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    Filtrar
                </button>
            </div>
        </form>
    </div>

    <!-- Lista de Notificaciones -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($notifications->count() > 0)
            @foreach($notifications as $notification)
            <div class="border-b border-gray-200 last:border-b-0 {{ $notification->is_read ? 'bg-gray-50' : 'bg-white' }}">
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-2">
                                <!-- Icono de tipo -->
                                @if($notification->type == 'info')
                                    <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                @elseif($notification->type == 'success')
                                    <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                @elseif($notification->type == 'warning')
                                    <div class="w-2 h-2 bg-yellow-500 rounded-full"></div>
                                @elseif($notification->type == 'error')
                                    <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                                @endif
                                
                                <!-- Badge de prioridad -->
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($notification->priority == 'urgent') bg-red-100 text-red-800
                                    @elseif($notification->priority == 'high') bg-orange-100 text-orange-800
                                    @elseif($notification->priority == 'medium') bg-yellow-100 text-yellow-800
                                    @else bg-green-100 text-green-800
                                    @endif">
                                    {{ ucfirst($notification->priority) }}
                                </span>
                                
                                @if(!$notification->is_read)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Nueva
                                </span>
                                @endif
                            </div>
                            
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $notification->title }}</h3>
                            <p class="text-gray-700 mb-3">{{ $notification->message }}</p>
                            
                            @if($notification->service)
                            <div class="bg-gray-100 rounded-lg p-3 mb-3">
                                <h4 class="font-medium text-gray-900">Servicio relacionado:</h4>
                                <p class="text-sm text-gray-600">
                                    Cliente: {{ $notification->service->client->name ?? 'N/A' }} | 
                                    Fecha: {{ $notification->service->scheduled_date ? $notification->service->scheduled_date->format('d/m/Y H:i') : 'N/A' }}
                                </p>
                                <a href="{{ route('technician.service.detail', $notification->service) }}" 
                                   class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    Ver servicio →
                                </a>
                            </div>
                            @endif
                            
                            <p class="text-sm text-gray-500">
                                {{ $notification->created_at->diffForHumans() }}
                                @if($notification->is_read)
                                | Leída {{ $notification->read_at->diffForHumans() }}
                                @endif
                            </p>
                        </div>
                        
                        <div class="flex items-center space-x-2 ml-4">
                            @if(!$notification->is_read)
                            <form method="POST" action="{{ route('technician.notifications.mark-read', $notification) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-blue-600 hover:text-blue-800 text-sm">
                                    Marcar como leída
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
            
            <!-- Paginación -->
            <div class="px-6 py-4 bg-gray-50">
                {{ $notifications->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM9 17H4l5 5v-5zM12 3v18"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No hay notificaciones</h3>
                <p class="mt-1 text-sm text-gray-500">No tienes notificaciones en este momento.</p>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
// Auto-actualizar el contador de notificaciones cada 30 segundos
setInterval(function() {
    fetch('{{ route("technician.notifications.unread-count") }}')
        .then(response => response.json())
        .then(data => {
            const badge = document.querySelector('.notification-badge');
            if (badge) {
                if (data.count > 0) {
                    badge.textContent = data.count;
                    badge.style.display = 'inline';
                } else {
                    badge.style.display = 'none';
                }
            }
        })
        .catch(error => console.log('Error actualizando notificaciones:', error));
}, 30000);
</script>
@endpush

@endsection
