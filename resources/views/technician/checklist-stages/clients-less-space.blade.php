@extends("layouts.app")

@section("title", "Clientes - Pest Controller SAT")
@section("page-title", "Gestión de Clientes")

@section("content")
<style>
    /* Reducir espacio entre header e iconos y título en móvil */
    @media (max-width: 767px) {
        main {
            padding-top: 80px !important;
        }
        /* Reducir margen del header móvil solo en esta página */
        .md\:hidden.mb-2 {
            margin-bottom: 0.25rem !important; /* Reducir a 4px */
        }
    }
</style>
<div class="space-y-4 sm:space-y-6">
    @include('admin.partials.header', [
        'title' => 'Clientes',
        'subtitle' => 'Gestiona la información de todos los clientes',
        'searchPlaceholder' => 'Buscar clientes...',
        'pageId' => 'clients'
    ])

    @can("create-clients")
    <div class="flex justify-end mb-4">
        <a href="{{ route("admin.clients.create") }}" 
           class="inline-flex items-center justify-center px-5 py-3 border border-transparent rounded-lg shadow-sm text-base font-medium text-white bg-green-600 hover:bg-green-700 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"></path>
            </svg>
            <span>Nuevo Cliente</span>
        </a>
    </div>
    @endcan

    <!-- Clients Table -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">RUT</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contacto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo de Negocio</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Servicios</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-white divide-y divide-gray-200">
                    @forelse($clients as $client)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $client->name }}</div>
                            <div class="text-sm text-gray-500">{{ $client->address }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $client->rut }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $client->email }}</div>
                            <div class="text-sm text-gray-500">{{ $client->phone }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $client->business_type ?? "No especificado" }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $client->services_count ?? 0 }} servicios
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                            <a href="{{ route("admin.clients.show", $client) }}" 
                               class="text-green-600 hover:text-green-900">Ver</a>
                            @can("edit-clients")
                            <a href="{{ route("admin.clients.edit", $client) }}" 
                               class="text-blue-600 hover:text-blue-900">Editar</a>
                            @endcan
                            @can("delete-clients")
                            <form method="POST" action="{{ route("admin.clients.destroy", $client) }}" class="inline">
                                @csrf
                                @method("DELETE")
                                <button type="submit" class="text-red-600 hover:text-red-900" 
                                        onclick="return confirm("¿Está seguro de eliminar este cliente?")">Eliminar</button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            No hay clientes registrados
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($clients->hasPages())
        <div class="px-6 py-3 border-t border-gray-200">
            {{ $clients->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
