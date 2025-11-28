@extends('layouts.app')

@section('title', 'Servicios')

@section('content')
<div class="space-y-4 sm:space-y-6 pt-12 md:pt-0">
    @include('admin.partials.header', [
        'title' => 'Servicios',
        'subtitle' => 'Gestiona todos los servicios de control de plagas',
        'searchPlaceholder' => 'Buscar servicios...',
        'pageId' => 'services'
    ])

    <!-- Botón Nuevo Servicio -->
    <div class="flex justify-end mb-4">
        <a href="{{ route('admin.services.create') ?? route('services.create') ?? '#' }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent rounded-lg shadow-sm text-base font-medium text-white bg-green-600 hover:bg-green-700 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Nuevo Servicio
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6 mb-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-4">Filtros</h3>
        <form method="GET" action="{{ route('admin.services.index') ?? route('services.index') ?? '#' }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="flex flex-col space-y-2">
                <label for="status" class="text-sm font-medium text-gray-700">Estado</label>
                <select name="status" id="status" class="border border-gray-300 rounded-lg px-4 py-3.5 text-base focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 w-full">
                    <option value="">Todos los estados</option>
                    <option value="pendiente" {{ request('status') === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="en_progreso" {{ request('status') === 'en_progreso' ? 'selected' : '' }}>En Progreso</option>
                    <option value="finalizado" {{ request('status') === 'finalizado' ? 'selected' : '' }}>Finalizado</option>
                    <option value="cancelado" {{ request('status') === 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                </select>
            </div>
            <div class="flex flex-col space-y-2">
                <label for="type" class="text-sm font-medium text-gray-700">Tipo</label>
                <select name="type" id="type" class="border border-gray-300 rounded-lg px-4 py-3.5 text-base focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 w-full">
                    <option value="">Todos los tipos</option>
                    <option value="desratizacion" {{ request('type') === 'desratizacion' ? 'selected' : '' }}>Desratización</option>
                    <option value="desinsectacion" {{ request('type') === 'desinsectacion' ? 'selected' : '' }}>Desinsectación</option>
                    <option value="sanitizacion" {{ request('type') === 'sanitizacion' ? 'selected' : '' }}>Sanitización</option>
                </select>
            </div>
            <div class="flex flex-col space-y-2">
                <label for="priority" class="text-sm font-medium text-gray-700">Prioridad</label>
                <select name="priority" id="priority" class="border border-gray-300 rounded-lg px-4 py-3.5 text-base focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 w-full">
                    <option value="">Todas las prioridades</option>
                    <option value="baja" {{ request('priority') === 'baja' ? 'selected' : '' }}>Baja</option>
                    <option value="media" {{ request('priority') === 'media' ? 'selected' : '' }}>Media</option>
                    <option value="alta" {{ request('priority') === 'alta' ? 'selected' : '' }}>Alta</option>
                </select>
            </div>
            <div class="col-span-full lg:col-span-3 mt-4">
                <div class="flex items-center gap-3">
                    <button type="submit" id="filter-submit-btn" class="px-6 py-3 bg-gray-700 hover:bg-gray-800 text-white font-medium rounded-lg shadow-sm transition-colors">
                        Filtrar
                    </button>
                    <a href="{{ route('admin.services.index') }}" class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg shadow-sm transition-colors text-center no-underline">
                        Limpiar
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Services Table -->
    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm">
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
                    @forelse($services as $service)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ $service->client->name ?? 'Cliente no encontrado' }}</div>
                            @if($service->address)
                                <div class="text-sm text-gray-500">{{ $service->address }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($service->serviceType)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $service->serviceType->name ?? 'N/A' }}
                                </span>
                            @else
                                <span class="text-sm text-gray-400">Sin tipo</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $service->scheduled_date ? \Carbon\Carbon::parse($service->scheduled_date)->format('d/m/Y H:i') : 'Sin fecha' }}
                        </td>
                        <td class="px-6 py-4">
                            @if($service->status)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                    @if($service->status == 'pendiente') bg-yellow-100 text-yellow-800
                                    @elseif($service->status == 'en_progreso') bg-blue-100 text-blue-800
                                    @elseif($service->status == 'finalizado') bg-green-100 text-green-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $service->status)) }}
                                </span>
                            @else
                                <span class="text-sm text-gray-400">Sin estado</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($service->priority)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                    @if($service->priority == 'alta') bg-red-100 text-red-800
                                    @elseif($service->priority == 'media') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($service->priority) }}
                                </span>
                            @else
                                <span class="text-sm text-gray-400">Sin prioridad</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('admin.services.show', $service) ?? route('services.show', $service) ?? '#' }}" class="text-green-600 hover:text-green-700 font-medium" title="Ver">
                                    Ver
                                </a>
                                <a href="{{ route('admin.services.edit', $service) ?? '#' }}" class="text-blue-600 hover:text-blue-700 font-medium" title="Editar">
                                    Editar
                                </a>
                                <button class="text-red-600 hover:text-red-700 font-medium" title="Eliminar">
                                    Eliminar
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">
                            No se encontraron servicios
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($services->hasPages())
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6" style="border-top: 1px solid #e5e7eb !important;">
            {{ $services->links() }}
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    // Prevenir envío automático del formulario
    (function() {
        const filterForm = document.querySelector('form[method="GET"]');
        const statusSelect = document.getElementById('status');
        const typeSelect = document.getElementById('type');
        const prioritySelect = document.getElementById('priority');
        const filterSubmitBtn = document.getElementById('filter-submit-btn');

        // Prevenir envío automático cuando se cambia un select
        if (statusSelect) {
            statusSelect.addEventListener('change', function(e) {
                e.preventDefault();
                // No hacer nada, solo esperar a que se haga clic en el botón
            });
        }

        if (typeSelect) {
            typeSelect.addEventListener('change', function(e) {
                e.preventDefault();
                // No hacer nada, solo esperar a que se haga clic en el botón
            });
        }

        if (prioritySelect) {
            prioritySelect.addEventListener('change', function(e) {
                e.preventDefault();
                // No hacer nada, solo esperar a que se haga clic en el botón
            });
        }

        // Prevenir envío cuando se presiona Enter en un select
        if (filterForm) {
            filterForm.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && (e.target.tagName === 'SELECT')) {
                    e.preventDefault();
                    // Solo permitir envío si se presiona Enter en el botón
                }
            });
        }

        // Permitir envío solo cuando se hace clic en el botón
        if (filterSubmitBtn) {
            filterSubmitBtn.addEventListener('click', function(e) {
                // Permitir el envío normal del formulario
            });
        }
    })();

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
</script>
@endpush
@endsection

