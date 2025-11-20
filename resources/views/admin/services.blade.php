@extends('layouts.app')

@section('title', 'Servicios')

@section('content')
<div class="space-y-4 sm:space-y-6 pt-3 md:pt-0" style="padding-top: 80px;">
    <!-- Header con hamburguesa y título -->
    <div class="mb-4 sm:mb-6">
        <!-- Primera fila: Hamburguesa + Título (móvil) -->
        <div class="flex items-center gap-3 mb-4 md:hidden" style="padding-top: 2.5rem; display: flex !important; flex-direction: row !important; align-items: center !important;">
            <!-- Hamburguesa (solo móvil) -->
            <button id="page-mobile-menu-button" class="flex-shrink-0 p-2 rounded-lg bg-white border border-gray-300 shadow-md hover:bg-gray-50 transition-colors" style="z-index: 50; display: flex !important; align-items: center !important; justify-content: center !important;">
                <svg id="page-menu-icon" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="color: #111827;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
                <svg id="page-close-icon" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="color: #111827;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- Título -->
            <div class="flex-1" style="flex: 1 1 0% !important; min-width: 0 !important;">
                <h2 class="text-2xl font-bold" style="color: #111827; font-weight: 700; margin: 0 !important;">
                    Servicios
                </h2>
            </div>
        </div>

        <!-- Segunda fila: Título completo (desktop) -->
        <div class="hidden md:flex md:items-center md:justify-between">
            <div class="min-w-0 flex-1">
                <h2 class="text-2xl sm:text-3xl font-bold leading-7 text-gray-900 sm:truncate sm:tracking-tight" style="color: #111827; font-weight: 700;">
                    Servicios
                </h2>
                <p class="mt-1 text-xs sm:text-sm" style="color: #6b7280;">
                    Gestiona todos los servicios de control de plagas
                </p>
            </div>
            <div class="mt-3 sm:mt-4 md:mt-0 md:ml-4">
                <a href="{{ route('admin.services.create') ?? route('services.create') ?? '#' }}" class="inline-flex items-center justify-center w-full sm:w-auto px-3 sm:px-4 py-2 border border-transparent rounded-lg shadow-sm text-xs sm:text-sm font-medium text-white transition-colors" style="background: #22c55e; hover:background: #16a34a;">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1.5 sm:mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span class="hidden sm:inline">Nuevo Servicio</span>
                    <span class="sm:hidden">Nuevo</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md border mb-6" style="border: 1px solid #e5e7eb;">
        <div class="p-4 sm:p-6">
            <form method="GET" action="{{ route('admin.services.index') ?? route('services.index') ?? '#' }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label for="status" class="block text-sm font-medium mb-2" style="color: #6b7280;">Estado</label>
                    <select name="status" id="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500" style="border: 1px solid #e5e7eb; color: #111827;">
                        <option value="">Todos los estados</option>
                        <option value="pendiente" {{ request('status') === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="en_progreso" {{ request('status') === 'en_progreso' ? 'selected' : '' }}>En Progreso</option>
                        <option value="completado" {{ request('status') === 'completado' ? 'selected' : '' }}>Completado</option>
                        <option value="cancelado" {{ request('status') === 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                    </select>
                </div>
                <div>
                    <label for="type" class="block text-sm font-medium mb-2" style="color: #6b7280;">Tipo</label>
                    <select name="type" id="type" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500" style="border: 1px solid #e5e7eb; color: #111827;">
                        <option value="">Todos los tipos</option>
                        <option value="desratizacion" {{ request('type') === 'desratizacion' ? 'selected' : '' }}>Desratización</option>
                        <option value="desinsectacion" {{ request('type') === 'desinsectacion' ? 'selected' : '' }}>Desinsectación</option>
                        <option value="sanitizacion" {{ request('type') === 'sanitizacion' ? 'selected' : '' }}>Sanitización</option>
                    </select>
                </div>
                <div>
                    <label for="priority" class="block text-sm font-medium mb-2" style="color: #6b7280;">Prioridad</label>
                    <select name="priority" id="priority" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500" style="border: 1px solid #e5e7eb; color: #111827;">
                        <option value="">Todas las prioridades</option>
                        <option value="baja" {{ request('priority') === 'baja' ? 'selected' : '' }}>Baja</option>
                        <option value="media" {{ request('priority') === 'media' ? 'selected' : '' }}>Media</option>
                        <option value="alta" {{ request('priority') === 'alta' ? 'selected' : '' }}>Alta</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-gray-700 hover:bg-gray-800 text-white px-4 py-2 rounded-lg transition-colors text-sm font-medium">
                        Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Services List -->
    <div class="bg-white border dark:border-gray-700 rounded-lg overflow-hidden" style="border: 1px solid #e5e7eb !important;">
        <div class="overflow-x-auto">
            @forelse($services as $service)
                <div class="p-4 sm:p-6 border-b border-gray-200 hover:bg-gray-50" style="border-bottom: 1px solid #e5e7eb;">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="flex-1">
                            <div class="mb-2">
                                <h3 class="text-lg font-semibold" style="color: #111827;">
                                    {{ $service->client->name ?? 'Cliente no encontrado' }}
                                </h3>
                                @if($service->address)
                                    <p class="text-sm" style="color: #6b7280;">{{ $service->address }}</p>
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
                    <p class="text-sm" style="color: #6b7280;">No se encontraron servicios</p>
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

