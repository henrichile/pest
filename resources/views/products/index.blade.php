@extends("layouts.app")

@section("title", "Productos - Pest Controller SAT")
@section("page-title", "Gestión de Productos")

@section("content")
<div class="space-y-4 sm:space-y-6" style="margin-top: -50px;">
    @include('admin.partials.header', [
        'title' => 'Productos',
        'subtitle' => 'Gestiona el inventario de productos',
        'searchPlaceholder' => 'Buscar productos...',
        'pageId' => 'products'
    ])

    <div class="flex justify-end mb-4">
        <a href="{{ route("admin.products.create") }}" 
           class="inline-flex items-center justify-center px-5 py-3 border border-transparent rounded-lg shadow-sm text-base font-medium text-white bg-green-600 hover:bg-green-700 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"></path>
            </svg>
            <span>Nuevo Producto</span>
        </a>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ingrediente Activo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo Servicio</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registro</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($products as $product)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $product->active_ingredient }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ ucfirst($product->service_type) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $product->stock }} {{ $product->unit }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div>SAG: {{ $product->sag_registration ?? 'N/A' }}</div>
                            <div>ISP: {{ $product->isp_registration ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                            <a href="{{ route("admin.products.show", $product) }}" 
                               class="text-green-600 hover:text-green-900">Ver</a>
                            <a href="{{ route("admin.products.edit", $product) }}" 
                               class="text-blue-600 hover:text-blue-900">Editar</a>
                            <form method="POST" action="{{ route("admin.products.destroy", $product) }}" class="inline">
                                @csrf
                                @method("DELETE")
                                <button type="submit" class="text-red-600 hover:text-red-900" 
                                        onclick="return confirm('¿Está seguro de eliminar este producto?')">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            No hay productos registrados
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($products->hasPages())
        <div class="px-6 py-3 border-t border-gray-200">
            {{ $products->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
