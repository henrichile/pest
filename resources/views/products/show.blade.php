@extends("layouts.app")

@section("title", "Detalle del Producto - Pest Controller SAT")
@section("page-title", "Detalle del Producto")

@section("content")
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Product Header -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">{{ $product->name }}</h2>
                <p class="text-gray-600">{{ $product->active_ingredient }}</p>
            </div>
            <div class="text-right">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    @if($product->stock < 10) bg-red-100 text-red-800
                    @elseif($product->stock < 50) bg-yellow-100 text-yellow-800
                    @else bg-green-100 text-green-800
                    @endif">
                    Stock: {{ $product->stock }} {{ $product->unit }}
                </span>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <h3 class="text-sm font-medium text-gray-500">Tipo de Servicio</h3>
                <p class="text-lg font-semibold text-gray-900">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        @if($product->service_type == "desratizacion") bg-red-100 text-red-800
                        @elseif($product->service_type == "desinsectacion") bg-yellow-100 text-yellow-800
                        @else bg-blue-100 text-blue-800
                        @endif">
                        {{ ucfirst($product->service_type) }}
                    </span>
                </p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Stock Actual</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $product->stock }} {{ $product->unit }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Servicios Utilizados</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $product->services->count() }}</p>
            </div>
        </div>
    </div>

    <!-- Product Details -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Product Information -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Información del Producto</h3>
            <div class="space-y-3">
                <div>
                    <span class="text-sm font-medium text-gray-500">Nombre Comercial:</span>
                    <p class="text-gray-900">{{ $product->name }}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Ingrediente Activo:</span>
                    <p class="text-gray-900">{{ $product->active_ingredient }}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Tipo de Servicio:</span>
                    <p class="text-gray-900">{{ ucfirst($product->service_type) }}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Unidad:</span>
                    <p class="text-gray-900">{{ $product->unit }}</p>
                </div>
                @if($product->sag_registration)
                <div>
                    <span class="text-sm font-medium text-gray-500">Registro SAG:</span>
                    <p class="text-gray-900">{{ $product->sag_registration }}</p>
                </div>
                @endif
                @if($product->isp_registration)
                <div>
                    <span class="text-sm font-medium text-gray-500">Registro ISP:</span>
                    <p class="text-gray-900">{{ $product->isp_registration }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Stock Management -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Gestión de Stock</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Stock Actual</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $product->stock }} {{ $product->unit }}</p>
                    </div>
                    <div class="text-right">
                        @if($product->stock < 10)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            Stock Bajo
                        </span>
                        @elseif($product->stock < 50)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            Stock Medio
                        </span>
                        @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Stock Alto
                        </span>
                        @endif
                    </div>
                </div>
                
                @can("edit-products")
                <form method="POST" action="{{ route("products.update-stock", $product) }}" class="space-y-3">
                    @csrf
                    <div>
                        <label for="stock" class="block text-sm font-medium text-gray-700 mb-2">Actualizar Stock</label>
                        <div class="flex space-x-2">
                            <input type="number" id="stock" name="stock" required min="0"
                                   value="{{ $product->stock }}"
                                   class="flex-1 border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                            <button type="submit" 
                                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                                Actualizar
                            </button>
                        </div>
                    </div>
                </form>
                @endcan
            </div>
        </div>
    </div>

    <!-- Description -->
    @if($product->description)
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Descripción</h3>
        <p class="text-gray-700">{{ $product->description }}</p>
    </div>
    @endif

    <!-- Services Using This Product -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Servicios que Utilizan este Producto</h3>
        @if($product->services->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($product->services->sortByDesc("created_at") as $service)
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
                            {{ $service->scheduled_date->format("d/m/Y") }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $service->pivot->quantity }} {{ $product->unit }}
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
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-gray-500 text-center py-4">Este producto no ha sido utilizado en ningún servicio</p>
        @endif
    </div>

    <!-- Actions -->
    <div class="flex justify-between items-center">
        <a href="{{ route("admin.products.index") }}" 
           class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
            Volver a Productos
        </a>
        
        <div class="flex space-x-4">
            @can("edit-products")
            <a href="{{ route("admin.products.edit", $product) }}" 
               class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                Editar Producto
            </a>
            @endcan
        </div>
    </div>
</div>
@endsection
