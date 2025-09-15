@extends("layouts.app")

@section("content")
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Tipos de Servicios</h1>
        <a href="{{ route("admin.service-types.create") }}" 
           class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors">
            <i class="fas fa-plus mr-2"></i>Nuevo Tipo de Servicio
        </a>
    </div>

    @if(session("success"))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session("success") }}
        </div>
    @endif

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Servicios</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($serviceTypes as $serviceType)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $serviceType->name }}</div>
                        <div class="text-sm text-gray-500">{{ $serviceType->slug }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">{{ $serviceType->description ?? "Sin descripción" }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $serviceType->is_active ? "bg-green-100 text-green-800" : "bg-red-100 text-red-800" }}">
                            {{ $serviceType->is_active ? "Activo" : "Inactivo" }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $serviceType->services->count() }} servicios
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route("admin.service-types.edit", $serviceType) }}" 
                           class="text-indigo-600 hover:text-indigo-900 mr-3">Editar</a>
                        <form method="POST" action="{{ route("admin.service-types.destroy", $serviceType) }}" 
                              class="inline" onsubmit="return confirm("¿Estás seguro de eliminar este tipo de servicio?")">
                            @csrf
                            @method("DELETE")
                            <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                        No hay tipos de servicios registrados
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
