@extends("layouts.app")

@section("title", "Productos - Pest Controller SAT")
@section("page-title", "Gestión de Productos")

@section("content")
<div class="space-y-4 sm:space-y-6 pt-12 md:pt-0">
    @include('admin.partials.header', [
        'title' => 'Productos',
        'subtitle' => 'Gestiona el catálogo de productos para control de plagas',
        'searchPlaceholder' => 'Buscar productos...',
        'pageId' => 'products'
    ])

    <!-- Botón Nuevo Producto -->
    @can("create-products")
    <div class="flex justify-end mb-4">
        <a href="{{ route("admin.products.create") }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent rounded-lg shadow-sm text-base font-medium text-white bg-green-600 hover:bg-green-700 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Nuevo Producto
        </a>
    </div>
    @endcan

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6 mb-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-4">Filtros</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="flex flex-col space-y-2">
                <label class="text-sm font-medium text-gray-700">Tipo de Servicio</label>
                <select id="filter-tipo-servicio" class="border border-gray-300 rounded-lg px-4 py-3.5 text-base focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 w-full">
                    <option value="">Todos los tipos</option>
                    <option value="desratizacion">Desratización</option>
                    <option value="desinsectacion">Desinsectación</option>
                    <option value="sanitizacion">Sanitización</option>
                    <option value="monitoreo">Monitoreo</option>
                </select>
            </div>
            <div class="flex flex-col space-y-2">
                <label class="text-sm font-medium text-gray-700">Stock</label>
                <select id="filter-stock" class="border border-gray-300 rounded-lg px-4 py-3.5 text-base focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 w-full">
                    <option value="">Todos los niveles</option>
                    <option value="low">Stock Bajo (< 10)</option>
                    <option value="medium">Stock Medio (10-50)</option>
                    <option value="high">Stock Alto (> 50)</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ingrediente Activo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registro</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-white divide-y divide-gray-200">
                    @forelse($products as $product)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                            <div class="text-sm text-gray-500">{{ $product->description }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $product->active_ingredient }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($product->service_type == "desratizacion") bg-red-100 text-red-800
                                @elseif($product->service_type == "desinsectacion") bg-yellow-100 text-yellow-800
                                @else bg-blue-100 text-blue-800
                                @endif">
                                {{ ucfirst($product->service_type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <span class="text-sm font-medium text-gray-900">{{ $product->stock }}</span>
                                <span class="text-sm text-gray-500 ml-1">{{ $product->unit }}</span>
                                @if($product->stock < 10)
                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                    Bajo Stock
                                </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div class="text-xs">
                                @if($product->sag_registration)
                                <div>SAG: {{ $product->sag_registration }}</div>
                                @endif
                                @if($product->isp_registration)
                                <div>ISP: {{ $product->isp_registration }}</div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                            <a href="{{ route("admin.products.show", $product) }}"
                               class="text-green-600 hover:text-green-900">Ver</a>
                            @can("edit-products")
                            <a href="{{ route("admin.products.edit", $product) }}"
                               class="text-blue-600 hover:text-blue-900">Editar</a>
                            @endcan
                            @can("delete-products")
                            <form method="POST" action="{{ route("admin.products.destroy", $product) }}" class="inline">
                                @csrf
                                @method("DELETE")
                                <button type="submit" class="text-red-600 hover:text-red-900"
                                        onclick="return confirm("¿Está seguro de eliminar este producto?")">Eliminar</button>
                            </form>
                            @endcan
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

@push('scripts')
<script>
    // Page Mobile Menu Button
    (function() {
        const pageMenuButton = document.getElementById('page-mobile-menu-button');
        const mainMenuButton = document.getElementById('mobile-menu-button');
        const sidebar = document.getElementById('sidebar');
        const mobileOverlay = document.getElementById('mobile-overlay');

        function toggleMobileMenu() {
            if (mainMenuButton) {
                mainMenuButton.click();
            } else {
                const menuIcon = document.getElementById('page-menu-icon');
                const closeIcon = document.getElementById('page-close-icon');

                if (sidebar && sidebar.classList.contains('-translate-x-full')) {
                    sidebar.classList.remove('-translate-x-full');
                    sidebar.classList.add('translate-x-0');
                    if (mobileOverlay) mobileOverlay.classList.remove('hidden');
                    if (menuIcon) menuIcon.classList.add('hidden');
                    if (closeIcon) closeIcon.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                } else {
                    sidebar.classList.remove('translate-x-0');
                    sidebar.classList.add('-translate-x-full');
                    if (mobileOverlay) mobileOverlay.classList.add('hidden');
                    if (menuIcon) menuIcon.classList.remove('hidden');
                    if (closeIcon) closeIcon.classList.add('hidden');
                    document.body.style.overflow = '';
                }
            }
        }

        if (pageMenuButton) {
            pageMenuButton.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                toggleMobileMenu();
            });
        }
    })();

    // Filtros de productos
    (function() {
        const filterTipo = document.getElementById('filter-tipo-servicio');
        const filterStock = document.getElementById('filter-stock');
        const productRows = document.querySelectorAll('tbody tr');

        function applyFilters() {
            const tipoValue = filterTipo.value.toLowerCase();
            const stockValue = filterStock.value.toLowerCase();

            productRows.forEach(function(row) {
                let showRow = true;

                // Filtro por tipo de servicio
                if (tipoValue) {
                    const tipoCell = row.querySelector('td:nth-child(3)'); // Columna TIPO
                    if (tipoCell) {
                        const tipoText = tipoCell.textContent.trim().toLowerCase();
                        if (!tipoText.includes(tipoValue)) {
                            showRow = false;
                        }
                    }
                }

                // Filtro por stock
                if (stockValue && showRow) {
                    const stockCell = row.querySelector('td:nth-child(4)'); // Columna STOCK
                    if (stockCell) {
                        const stockText = stockCell.textContent.trim();
                        const stockMatch = stockText.match(/(\d+)/);
                        const stockNumber = stockMatch ? parseInt(stockMatch[1]) : 0;

                        if (stockValue === 'low' && stockNumber >= 10) {
                            showRow = false;
                        } else if (stockValue === 'medium' && (stockNumber < 10 || stockNumber > 50)) {
                            showRow = false;
                        } else if (stockValue === 'high' && stockNumber <= 50) {
                            showRow = false;
                        }
                    }
                }

                // Mostrar u ocultar fila
                if (showRow) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        if (filterTipo && filterStock) {
            filterTipo.addEventListener('change', applyFilters);
            filterStock.addEventListener('change', applyFilters);
        }
    })();
</script>
@endpush
@endsection
