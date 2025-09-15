@extends("layouts.app")

@section("title", "Detalle del Cliente - Pest Controller SAT")
@section("page-title", "Detalle del Cliente")

@section("content")
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Client Header -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">{{ $client->name }}</h2>
                <p class="text-gray-600">{{ $client->address }}</p>
            </div>
            <div class="text-right">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                    Cliente Activo
                </span>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <h3 class="text-sm font-medium text-gray-500">RUT</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $client->rut }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Tipo de Negocio</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $client->business_type ?? "No especificado" }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Servicios Totales</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $client->services->count() }}</p>
            </div>
        </div>
    </div>

    <!-- Client Details -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Contact Information -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Información de Contacto</h3>
            <div class="space-y-3">
                <div>
                    <span class="text-sm font-medium text-gray-500">Email:</span>
                    <p class="text-gray-900">{{ $client->email }}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Teléfono:</span>
                    <p class="text-gray-900">{{ $client->phone }}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Dirección:</span>
                    <p class="text-gray-900">{{ $client->address }}</p>
                </div>
                @if($client->contact_person)
                <div>
                    <span class="text-sm font-medium text-gray-500">Persona de Contacto:</span>
                    <p class="text-gray-900">{{ $client->contact_person }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Service Statistics -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Estadísticas de Servicios</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-500">Total Servicios:</span>
                    <span class="text-gray-900 font-semibold">{{ $client->services->count() }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-500">Servicios Completados:</span>
                    <span class="text-gray-900 font-semibold">{{ $client->services->where("status", "finalizado")->count() }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-500">Servicios Pendientes:</span>
                    <span class="text-gray-900 font-semibold">{{ $client->services->where("status", "pendiente")->count() }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-500">Servicios en Progreso:</span>
                    <span class="text-gray-900 font-semibold">{{ $client->services->where("status", "en_progreso")->count() }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Services History -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Historial de Servicios</h3>
            @can("create-services")
            <a href="{{ route("services.create") }}?client_id={{ $client->id }}" 
               class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"></path>
                </svg>
                <span>Nuevo Servicio</span>
            </a>
            @endcan
        </div>
        
        @if($client->services->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prioridad</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($client->services->sortByDesc("created_at") as $service)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($service->service_type == "desratizacion") bg-red-100 text-red-800
                                @elseif($service->service_type == "desinsectacion") bg-yellow-100 text-yellow-800
                                @else bg-blue-100 text-blue-800
                                @endif">
                                {{ ucfirst($service->service_type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $service->scheduled_date->format("d/m/Y H:i") }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($service->status == "pendiente") bg-gray-100 text-gray-800
                                @elseif($service->status == "en_progreso") bg-blue-100 text-blue-800
                                @elseif($service->status == "vencido") bg-red-100 text-red-800
                                @else bg-green-100 text-green-800
                                @endif">
                                {{ ucfirst(str_replace("_", " ", $service->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($service->priority == "alta") bg-red-100 text-red-800
                                @elseif($service->priority == "media") bg-yellow-100 text-yellow-800
                                @else bg-green-100 text-green-800
                                @endif">
                                {{ ucfirst($service->priority) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route("services.show", $service) }}" 
                               class="text-green-600 hover:text-green-900">Ver</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-gray-500 text-center py-4">No hay servicios registrados para este cliente</p>
        @endif
    </div>

    <!-- Actions -->
    <div class="flex justify-between items-center">
        <a href="{{ route("clients.index") }}" 
           class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
            Volver a Clientes
        </a>
        
        <div class="flex space-x-4">
            @can("edit-clients")
            <a href="{{ route("clients.edit", $client) }}" 
               class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                Editar Cliente
            </a>
            @endcan
            
            @can("create-services")
            <a href="{{ route("services.create") }}?client_id={{ $client->id }}" 
               class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                Nuevo Servicio
            </a>
            @endcan
        </div>
    </div>
</div>
@endsection
