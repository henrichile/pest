@extends("layouts.app")

@section("title", "Mis Servicios - Pest Controller SAT")
@section("page-title", "Mis Servicios")

@section("content")
<div class="max-w-7xl mx-auto space-y-4 md:space-y-6 px-4 md:px-0 pt-12 md:pt-0">

    @include('technician.partials.header', [
        'title' => 'Mis Servicios',
        'searchPlaceholder' => 'Buscar servicios...',
        'pageId' => 'services'
    ])

    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow-lg p-4 md:p-6">
        <div class="flex flex-col md:flex-row md:items-center space-y-3 md:space-y-0 md:space-x-4">
            <div class="flex flex-col md:flex-row md:items-center space-y-2 md:space-y-0 md:space-x-2 w-full md:w-auto">
                <label class="text-sm font-medium text-gray-700">Estado:</label>
                <select id="filter-estado" class="border border-gray-300 rounded-lg px-3 py-2.5 md:py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-full md:w-auto">
                    <option value="">Todos</option>
                    <option value="pendiente">Pendientes</option>
                    <option value="en_progreso">En Progreso</option>
                    <option value="finalizado">Finalizados</option>
                    <option value="vencido">Vencidos</option>
                </select>
            </div>
            <div class="flex flex-col md:flex-row md:items-center space-y-2 md:space-y-0 md:space-x-2 w-full md:w-auto">
                <label class="text-sm font-medium text-gray-700">Tipo:</label>
                <select id="filter-tipo" class="border border-gray-300 rounded-lg px-3 py-2.5 md:py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-full md:w-auto">
                    <option value="">Todos</option>
                    <option value="monitoreo-cebaderas">Monitoreo-cebaderas</option>
                    <option value="desratizacion">Desratización</option>
                    <option value="desinsectacion">Desinsectación</option>
                    <option value="sanitizacion">Sanitización</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Lista de Servicios -->
    <div class="bg-white rounded-lg shadow-lg">
        <div class="px-4 md:px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Servicios Asignados</h3>
        </div>

        <!-- Vista Desktop -->
        <div class="hidden md:block overflow-x-auto">
            @if($services->count() > 0)
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Programada</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prioridad</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($services as $service)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $service->client->name ?? "N/A" }}</div>
                            @if($service->address)
                            <div class="text-sm text-gray-500">{{ Str::limit($service->address, 30) }}</div>
                            @endif
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
                            @if($service->scheduled_date < now() && $service->status == "pendiente")
                            <div class="text-xs text-red-600 font-medium">Vencido</div>
                            @endif
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
                            <div class="flex items-center space-x-2">
                                @php
                                    // PRIORIDAD 1: Verificar sesión PRIMERO (más confiable)
                                    $isTechView = false;
                                    if (auth()->check() && auth()->user()->hasRole('super-admin')) {
                                        $viewAsTechnician = session('view_as_technician', false);
                                        // También verificar en request()->session() por si acaso
                                        if (!$viewAsTechnician && request()->hasSession()) {
                                            $viewAsTechnician = request()->session()->get('view_as_technician', false);
                                        }
                                        if ($viewAsTechnician) {
                                            $isTechView = true;
                                        }
                                    }

                                    // PRIORIDAD 2: Verificar URL actual
                                    if (!$isTechView) {
                                        if (request()->is('admin/technician-view/*') || request()->routeIs('technician-view.*')) {
                                            $isTechView = true;
                                        }
                                    }

                                    // PRIORIDAD 3: Usar variable del controlador si está disponible
                                    if (!$isTechView && isset($isTechnicianView) && $isTechnicianView) {
                                        $isTechView = true;
                                    }

                                    // Generar URLs correctas usando rutas nombradas
                                    if ($isTechView) {
                                        $startUrl = route('technician-view.service.start', $service);
                                        $detailUrl = route('technician-view.service.detail', $service);
                                        $pdfUrl = route('technician-view.service.pdf', $service);
                                    } else {
                                        try {
                                            $startUrl = route("technician.service.start", $service);
                                            $detailUrl = route("technician.service.detail", $service);
                                            $pdfUrl = route("technician.service.pdf", $service);
                                        } catch (\Exception $e) {
                                            $startUrl = url('/technician/services/' . $service->id . '/start');
                                            $detailUrl = url('/technician/services/' . $service->id . '/detail');
                                            $pdfUrl = url('/technician/services/' . $service->id . '/pdf');
                                        }
                                    }
                                @endphp
                                @if($service->status == "pendiente")
                                <form method="POST" action="{{ $startUrl }}" class="inline" id="start-form-{{ $service->id }}">
                                    @csrf
                                    <button type="submit" class="text-blue-600 hover:text-blue-900 font-medium">
                                        Iniciar
                                    </button>
                                </form>
                                @elseif($service->status == "en_progreso")
                                <a href="{{ $detailUrl }}" class="text-green-600 hover:text-green-900 font-medium">
                                    Completar
                                </a>
                                @elseif($service->status == "finalizado")
                                <a href="{{ $pdfUrl }}" class="text-blue-600 hover:text-blue-900 font-medium">
                                    📄 Descargar PDF
                                </a>
                                @endif
                                <a href="{{ $detailUrl }}" class="text-gray-600 hover:text-gray-900 font-medium">
                                    Ver
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No hay servicios asignados</h3>
                <p class="mt-1 text-sm text-gray-500">No tienes servicios asignados en este momento.</p>
            </div>
            @endif
        </div>

        <!-- Vista Mobile - Tarjetas -->
        <div class="md:hidden divide-y divide-gray-200">
            @if($services->count() > 0)
                @foreach($services as $service)
                <div class="p-4 hover:bg-gray-50 transition-colors">
                    <!-- Cliente -->
                    <div class="mb-3">
                        <div class="text-base font-semibold text-gray-900">{{ $service->client->name ?? "N/A" }}</div>
                        @if($service->address)
                        <div class="text-sm text-gray-600 mt-1">{{ $service->address }}</div>
                        @endif
                    </div>

                    <!-- Badges row -->
                    <div class="flex flex-wrap gap-2 mb-3">
                        <!-- Tipo -->
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                            @if($service->service_type == "desratizacion") bg-red-100 text-red-800
                            @elseif($service->service_type == "desinsectacion") bg-yellow-100 text-yellow-800
                            @else bg-blue-100 text-blue-800
                            @endif">
                            {{ ucfirst($service->service_type) }}
                        </span>

                        <!-- Estado -->
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                            @if($service->status == "pendiente") bg-gray-100 text-gray-800
                            @elseif($service->status == "en_progreso") bg-blue-100 text-blue-800
                            @elseif($service->status == "vencido") bg-red-100 text-red-800
                            @else bg-green-100 text-green-800
                            @endif">
                            {{ ucfirst(str_replace("_", " ", $service->status)) }}
                        </span>

                        <!-- Prioridad -->
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                            @if($service->priority == "alta") bg-red-100 text-red-800
                            @elseif($service->priority == "media") bg-yellow-100 text-yellow-800
                            @else bg-green-100 text-green-800
                            @endif">
                            {{ ucfirst($service->priority) }}
                        </span>
                    </div>

                    <!-- Fecha -->
                    <div class="text-sm text-gray-700 mb-3">
                        <svg class="inline h-4 w-4 mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        {{ $service->scheduled_date->format("d/m/Y H:i") }}
                        @if($service->scheduled_date < now() && $service->status == "pendiente")
                        <span class="ml-2 text-xs text-red-600 font-semibold">⚠ Vencido</span>
                        @endif
                    </div>

                    <!-- Botones de acción -->
                    <div class="flex flex-col sm:flex-row gap-2">
                        @php
                            // PRIORIDAD 1: Verificar sesión PRIMERO (más confiable)
                            $isTechView = false;
                            if (auth()->check() && auth()->user()->hasRole('super-admin')) {
                                $isTechView = session('view_as_technician', false);
                            }

                            // PRIORIDAD 2: Verificar URL actual
                            if (!$isTechView) {
                                $currentUrl = request()->url();
                                $currentPath = request()->path();
                                $isTechView = str_contains($currentUrl, '/admin/technician-view/') ||
                                            str_contains($currentPath, 'admin/technician-view');
                            }

                            // PRIORIDAD 3: Usar variable del controlador si está disponible
                            if (!$isTechView && isset($isTechnicianView) && $isTechnicianView) {
                                $isTechView = true;
                            }

                            // Generar URLs correctas usando rutas nombradas
                            if ($isTechView) {
                                $startUrl = route('technician-view.service.start', $service);
                                $detailUrl = route('technician-view.service.detail', $service);
                                $pdfUrl = route('technician-view.service.pdf', $service);
                            } else {
                                try {
                                    $startUrl = route("technician.service.start", $service);
                                    $detailUrl = route("technician.service.detail", $service);
                                    $pdfUrl = route("technician.service.pdf", $service);
                                } catch (\Exception $e) {
                                    $startUrl = url('/technician/services/' . $service->id . '/start');
                                    $detailUrl = url('/technician/services/' . $service->id . '/detail');
                                    $pdfUrl = url('/technician/services/' . $service->id . '/pdf');
                                }
                            }
                        @endphp

                        @if($service->status == "pendiente")
                        <form method="POST" action="{{ $startUrl }}" class="flex-1" id="start-form-mobile-{{ $service->id }}">
                            @csrf
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition-colors text-sm">
                                🚀 Iniciar Servicio
                            </button>
                        </form>
                        @elseif($service->status == "en_progreso")
                        <a href="{{ $detailUrl }}" class="flex-1 bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-4 rounded-lg transition-colors text-sm text-center">
                            ✓ Completar Servicio
                        </a>
                        @elseif($service->status == "finalizado")
                        <a href="{{ $pdfUrl }}" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition-colors text-sm text-center">
                            📄 Descargar PDF
                        </a>
                        @endif

                        <a href="{{ $detailUrl }}" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-3 px-4 rounded-lg transition-colors text-sm text-center">
                            👁 Ver Detalles
                        </a>
                    </div>
                </div>
                @endforeach
            @else
            <div class="text-center py-12 px-4">
                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <h3 class="mt-4 text-base font-semibold text-gray-900">No hay servicios asignados</h3>
                <p class="mt-2 text-sm text-gray-500">No tienes servicios asignados en este momento.</p>
            </div>
            @endif
        </div>

        <!-- Paginación -->
        @if($services->count() > 0)
        <div class="px-4 md:px-6 py-4 border-t border-gray-200">
            {{ $services->links() }}
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Filtros de servicios
        const filterEstado = document.getElementById('filter-estado');
        const filterTipo = document.getElementById('filter-tipo');
        const serviceRows = document.querySelectorAll('tbody tr');

        function applyFilters() {
            const estadoValue = filterEstado.value.toLowerCase();
            const tipoValue = filterTipo.value.toLowerCase();

            serviceRows.forEach(function(row) {
                let showRow = true;

                // Filtro por estado
                if (estadoValue) {
                    const estadoCell = row.querySelector('td:nth-child(4)'); // Columna ESTADO
                    if (estadoCell) {
                        const estadoText = estadoCell.textContent.trim().toLowerCase();
                        const estadoMatch = 
                            (estadoValue === 'pendiente' && estadoText === 'pendiente') ||
                            (estadoValue === 'en_progreso' && estadoText === 'en progreso') ||
                            (estadoValue === 'finalizado' && estadoText === 'finalizado') ||
                            (estadoValue === 'vencido' && estadoText === 'vencido');
                        
                        if (!estadoMatch) {
                            showRow = false;
                        }
                    }
                }

                // Filtro por tipo
                if (tipoValue && showRow) {
                    const tipoCell = row.querySelector('td:nth-child(2)'); // Columna TIPO
                    if (tipoCell) {
                        const tipoText = tipoCell.textContent.trim().toLowerCase();
                        if (!tipoText.includes(tipoValue)) {
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

        if (filterEstado && filterTipo) {
            filterEstado.addEventListener('change', applyFilters);
            filterTipo.addEventListener('change', applyFilters);
        }

        // PRIORIDAD 1: Verificar atributo del body (viene del servidor basado en sesión)
        let isTechnicianView = false;
        const body = document.body;
        if (body) {
            const hasAttribute = body.getAttribute('data-technician-view') === 'true';
            const hasClass = body.classList.contains('technician-view-mode');
            if (hasAttribute || hasClass) {
                isTechnicianView = true;
            }
        }

        // PRIORIDAD 2: Verificar URL actual
        if (!isTechnicianView) {
            isTechnicianView = window.location.href.includes('/admin/technician-view/') ||
                             window.location.pathname.includes('/admin/technician-view/');
        }

        // Agregar atributo al body para detección si no está presente
        if (isTechnicianView && body) {
            body.setAttribute('data-technician-view', 'true');
            body.classList.add('technician-view-mode');
        }

        // Manejar todos los formularios de inicio de servicio
        document.querySelectorAll('form[id^="start-form-"]').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const formId = form.id;
                // Extraer el ID del servicio correctamente (puede ser start-form-89 o start-form-mobile-89)
                const serviceId = formId.replace('start-form-mobile-', '').replace('start-form-', '');
                const submitBtn = form.querySelector('button[type="submit"]');

                // Deshabilitar botón
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Iniciando...';
                }

                // FORZAR URL correcta si estamos en technician-view
                let submitUrl = form.action;
                if (isTechnicianView) {
                    submitUrl = '/admin/technician-view/services/' + serviceId + '/start';
                    form.action = submitUrl;
                    console.log('✅ URL FORZADA a technician-view:', submitUrl);
                } else if (!isTechnicianView && submitUrl.includes('/admin/technician-view/')) {
                    // Si NO estamos en technician-view pero la URL lo indica, corregir
                    submitUrl = '/technician/services/' + serviceId + '/start';
                    form.action = submitUrl;
                    console.log('⚠️ URL corregida a technician normal:', submitUrl);
                }

                console.log('Enviando formulario a:', submitUrl);

                const formData = new FormData(form);

                fetch(submitUrl, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                })
                .then(function(response) {
                    console.log('Respuesta recibida:', response.status, response.url);

                    if (response.redirected) {
                        window.location.href = response.url;
                    } else if (response.ok) {
                        return response.text().then(function(text) {
                            const match = text.match(/window\.location\.href\s*=\s*['"]([^'"]+)['"]/);
                            if (match) {
                                window.location.href = match[1];
                            } else {
                                window.location.reload();
                            }
                        });
                    } else {
                        throw new Error('HTTP ' + response.status);
                    }
                })
                .catch(function(error) {
                    console.error('Error:', error);
                    alert('Error al iniciar el servicio: ' + error.message);
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.textContent = 'Iniciar';
                    }
                });
            });
        });
    });
</script>
@endpush
@endsection


