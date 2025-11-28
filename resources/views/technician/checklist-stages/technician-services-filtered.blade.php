@extends("layouts.app")

@section("title", "Mis Servicios - Pest Controller SAT")
@section("page-title", "Mis Servicios")

@section("content")
<div class="max-w-7xl mx-auto space-y-6">

    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex items-center space-x-4">
            <div class="flex items-center space-x-2">
                <label class="text-sm font-medium text-gray-700">Estado:</label>
                <select id="filter-estado" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos</option>
                    <option value="pendiente">Pendientes</option>
                    <option value="en_progreso">En Progreso</option>
                    <option value="finalizado">Finalizados</option>
                    <option value="vencido">Vencidos</option>
                </select>
            </div>
            <div class="flex items-center space-x-2">
                <label class="text-sm font-medium text-gray-700">Tipo:</label>
                <select id="filter-tipo" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos</option>
                    <option value="desratizacion">Desratización</option>
                    <option value="desinsectacion">Desinsectación</option>
                    <option value="sanitizacion">Sanitización</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Lista de Servicios -->
    <div class="bg-white rounded-lg shadow-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Servicios Asignados</h3>
        </div>
        
        <div class="overflow-x-auto">
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
                <tbody class="bg-white divide-y divide-gray-200" id="services-table-body">
                    @foreach($services as $service)
                    <tr class="hover:bg-gray-50 service-row" 
                        data-status="{{ $service->status }}" 
                        data-service-type="{{ $service->service_type }}">
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
                                    
                                    // Generar URLs correctas
                                    if ($isTechView) {
                                        $startUrl = url('/admin/technician-view/services/' . $service->id . '/start');
                                        $detailUrl = url('/admin/technician-view/services/' . $service->id . '/detail');
                                        $pdfUrl = url('/admin/technician-view/services/' . $service->id . '/pdf');
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
            
            <!-- Paginación -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $services->links() }}
            </div>
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
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Filtrado de servicios
        const estadoFilter = document.getElementById('filter-estado');
        const tipoFilter = document.getElementById('filter-tipo');
        const serviceRows = document.querySelectorAll('.service-row');
        
        function filterServices() {
            const estadoValue = estadoFilter ? estadoFilter.value : '';
            const tipoValue = tipoFilter ? tipoFilter.value : '';
            
            let visibleCount = 0;
            
            serviceRows.forEach(function(row) {
                const rowStatus = row.getAttribute('data-status') || '';
                const rowTipo = row.getAttribute('data-service-type') || '';
                
                let showRow = true;
                
                // Filtrar por estado
                if (estadoValue && rowStatus !== estadoValue) {
                    showRow = false;
                }
                
                // Filtrar por tipo
                if (tipoValue && rowTipo !== tipoValue) {
                    showRow = false;
                }
                
                if (showRow) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Mostrar mensaje si no hay resultados
            const tableBody = document.getElementById('services-table-body');
            const noResultsMsg = document.getElementById('no-results-message');
            
            if (visibleCount === 0) {
                if (!noResultsMsg && tableBody) {
                    const msg = document.createElement('tr');
                    msg.id = 'no-results-message';
                    msg.innerHTML = '<td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">No se encontraron servicios con los filtros seleccionados</td>';
                    tableBody.appendChild(msg);
                }
            } else {
                if (noResultsMsg) {
                    noResultsMsg.remove();
                }
            }
        }
        
        // Agregar event listeners a los filtros
        if (estadoFilter) {
            estadoFilter.addEventListener('change', filterServices);
        }
        
        if (tipoFilter) {
            tipoFilter.addEventListener('change', filterServices);
        }
        
        // Aplicar filtros iniciales si hay valores en la URL
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('estado') && estadoFilter) {
            estadoFilter.value = urlParams.get('estado');
        }
        if (urlParams.get('tipo') && tipoFilter) {
            tipoFilter.value = urlParams.get('tipo');
        }
        
        // Aplicar filtros al cargar la página
        filterServices();
        
        // Código existente para formularios
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
                const serviceId = formId.replace('start-form-', '');
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


