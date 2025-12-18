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
                <select name="status" id="status" class="border border-gray-300 rounded-lg px-4 py-3.5 text-base focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 w-full dark:text-white dark:bg-gray-700 dark:border-gray-600">
                    <option value="">Todos los estados</option>
                    <option value="pendiente" {{ request('status') === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="en_progreso" {{ request('status') === 'en_progreso' ? 'selected' : '' }}>En Progreso</option>
                    <option value="completado" {{ request('status') === 'completado' ? 'selected' : '' }}>Completado</option>
                    <option value="cancelado" {{ request('status') === 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                </select>
            </div>
            <div class="flex flex-col space-y-2">
                <label for="type" class="text-sm font-medium text-gray-700">Tipo</label>
                <select name="type" id="type" class="border border-gray-300 rounded-lg px-4 py-3.5 text-base focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 w-full dark:text-white dark:bg-gray-700 dark:border-gray-600">
                    <option value="">Todos los tipos</option>
                    <option value="desratizacion" {{ request('type') === 'desratizacion' ? 'selected' : '' }}>Desratización</option>
                    <option value="desinsectacion" {{ request('type') === 'desinsectacion' ? 'selected' : '' }}>Desinsectación</option>
                    <option value="sanitizacion" {{ request('type') === 'sanitizacion' ? 'selected' : '' }}>Sanitización</option>
                </select>
            </div>
            <div class="flex flex-col space-y-2">
                <label for="priority" class="text-sm font-medium text-gray-700">Prioridad</label>
                <select name="priority" id="priority" class="border border-gray-300 rounded-lg px-4 py-3.5 text-base focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 w-full dark:text-white dark:bg-gray-700 dark:border-gray-600">
                    <option value="">Todas las prioridades</option>
                    <option value="baja" {{ request('priority') === 'baja' ? 'selected' : '' }}>Baja</option>
                    <option value="media" {{ request('priority') === 'media' ? 'selected' : '' }}>Media</option>
                    <option value="alta" {{ request('priority') === 'alta' ? 'selected' : '' }}>Alta</option>
                </select>
            </div>
            <div class="flex items-end col-span-full lg:col-span-3">
                <button type="submit" class="w-full lg:w-auto px-6 py-3.5 bg-gray-700 hover:bg-gray-800 text-white rounded-lg transition-colors text-base font-medium">
                    Filtrar
                </button>
            </div>
        </form>
    </div>

    <!-- Services List -->
    <div class="bg-white dark:bg-gray-800 border dark:border-gray-700 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
        <div class="overflow-x-auto">
            @forelse($services as $service)
                <div class="p-4 sm:p-6 border-b border-gray-200 hover:bg-gray-50" style="border-bottom: 1px solid #e5e7eb;">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="flex-1">
                            <div class="mb-2">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ $service->client->name ?? 'Cliente no encontrado' }}
                                </h3>
                                @if($service->address)
                                    <p class="text-sm text-gray-600 dark:text-white">{{ $service->address }}</p>
                                @endif
                            </div>
                            <div class="flex flex-wrap gap-2">
                                @if($service->status)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($service->status == 'pendiente') bg-yellow-100 text-yellow-800
                                        @elseif($service->status == 'en_progreso') bg-blue-100 text-blue-800
                                        @elseif($service->status == 'completado') bg-green-100 text-green-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst(str_replace('_', ' ', $service->status)) }}
                                    </span>
                                @endif
                                @if($service->serviceType)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $service->serviceType->name ?? 'N/A' }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.services.show', $service) ?? route('services.show', $service) ?? '#' }}" class="text-blue-600 hover:text-blue-900" title="Ver">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center">
                    <p class="text-sm text-gray-600 dark:text-white">No se encontraron servicios</p>
                </div>
            @endforelse
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

