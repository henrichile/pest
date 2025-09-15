@extends("layouts.app")

@section("title", "Detalle del Usuario - Pest Controller SAT")
@section("page-title", "Detalle del Usuario")

@section("content")
<div class="max-w-6xl mx-auto space-y-6">
    <!-- User Header -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-start mb-4">
            <div class="flex items-center">
                <div class="w-16 h-16 bg-green-600 rounded-full flex items-center justify-center mr-4">
                    <span class="text-white text-xl font-medium">{{ substr($user->name, 0, 1) }}</span>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h2>
                    <p class="text-gray-600">{{ $user->email }}</p>
                </div>
            </div>
            <div class="text-right">
                @if($user->email_verified_at)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                    Usuario Activo
                </span>
                @else
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                    Usuario Inactivo
                </span>
                @endif
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <h3 class="text-sm font-medium text-gray-500">Rol</h3>
                <p class="text-lg font-semibold text-gray-900">
                    @if($user->roles->count() > 0)
                        @foreach($user->roles as $role)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($role->name == "super-admin") bg-purple-100 text-purple-800
                            @else bg-blue-100 text-blue-800
                            @endif">
                            {{ ucfirst(str_replace("-", " ", $role->name)) }}
                        </span>
                        @endforeach
                    @else
                        <span class="text-gray-500">Sin rol asignado</span>
                    @endif
                </p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Miembro desde</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $user->created_at->format("d/m/Y") }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Última actualización</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $user->updated_at->format("d/m/Y H:i") }}</p>
            </div>
        </div>
    </div>

    <!-- User Details -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- User Information -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Información del Usuario</h3>
            <div class="space-y-3">
                <div>
                    <span class="text-sm font-medium text-gray-500">Nombre Completo:</span>
                    <p class="text-gray-900">{{ $user->name }}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Email:</span>
                    <p class="text-gray-900">{{ $user->email }}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Estado:</span>
                    <p class="text-gray-900">
                        @if($user->email_verified_at)
                            <span class="text-green-600 font-medium">Activo</span>
                        @else
                            <span class="text-red-600 font-medium">Inactivo</span>
                        @endif
                    </p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Rol:</span>
                    <p class="text-gray-900">
                        @if($user->roles->count() > 0)
                            {{ ucfirst(str_replace("-", " ", $user->roles->first()->name)) }}
                        @else
                            Sin rol asignado
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- User Statistics -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Estadísticas</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-500">Servicios Asignados:</span>
                    <span class="text-gray-900 font-semibold">{{ $user->services()->count() }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-500">Servicios Completados:</span>
                    <span class="text-gray-900 font-semibold">{{ $user->services()->where("status", "finalizado")->count() }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-500">Servicios Pendientes:</span>
                    <span class="text-gray-900 font-semibold">{{ $user->services()->where("status", "pendiente")->count() }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-500">Servicios en Progreso:</span>
                    <span class="text-gray-900 font-semibold">{{ $user->services()->where("status", "en_progreso")->count() }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Services Assigned -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Servicios Asignados</h3>
        </div>
        
        @if($user->services()->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prioridad</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($user->services()->latest()->take(10)->get() as $service)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $service->client->name ?? "N/A" }}</div>
                        </td>
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
        <p class="text-gray-500 text-center py-4">No hay servicios asignados a este usuario</p>
        @endif
    </div>

    <!-- Actions -->
    <div class="flex justify-between items-center">
        <a href="{{ route("admin.users.index") }}" 
           class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
            Volver a Usuarios
        </a>
        
        <div class="flex space-x-4">
            <a href="{{ route("admin.users.edit", $user) }}" 
               class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                Editar Usuario
            </a>
            
            @if($user->id !== auth()->id())
            <form method="POST" action="{{ route("admin.users.toggle-status", $user) }}" class="inline">
                @csrf
                <button type="submit" 
                        class="px-6 py-2 {{ $user->email_verified_at ? "bg-yellow-600 hover:bg-yellow-700" : "bg-green-600 hover:bg-green-700" }} text-white rounded-lg transition-colors">
                    {{ $user->email_verified_at ? "Desactivar" : "Activar" }}
                </button>
            </form>
            @endif
        </div>
    </div>
</div>
@endsection
