@extends("layouts.app")

@section("title", "Detalle del Servicio - Pest Controller SAT")
@section("page-title", "Detalle del Servicio")

@section("content")
<div class="max-w-6xl mx-auto space-y-4 sm:space-y-6 pt-12 md:pt-0">
    <!-- Service Header -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">{{ $service->client->name ?? "Cliente no encontrado" }}</h2>
                <p class="text-gray-600">{{ $service->address }}</p>
            </div>
            <div class="text-right">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    @if($service->status == "pendiente") bg-gray-100 text-gray-800
                    @elseif($service->status == "en_progreso") bg-blue-100 text-blue-800
                    @elseif($service->status == "vencido") bg-red-100 text-red-800
                    @else bg-green-100 text-green-800
                    @endif">
                    {{ ucfirst(str_replace("_", " ", $service->status)) }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <h3 class="text-sm font-medium text-gray-500">Tipo de Servicio</h3>
                <p class="text-lg font-semibold text-gray-900">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        @if($service->service_type == "desratizacion") bg-red-100 text-red-800
                        @elseif($service->service_type == "desinsectacion") bg-yellow-100 text-yellow-800
                        @else bg-blue-100 text-blue-800
                        @endif">
                        {{ ucfirst($service->service_type) }}
                    </span>
                </p>

                @if($service->service_type === 'servicios-especiales' && $service->special_service_title)
                    <div class="mt-2">
                        <p class="text-sm text-gray-600 flex items-center">
                            <span class="mr-1">🏷️</span>
                            <span class="font-semibold text-green-700">{{ $service->special_service_title }}</span>
                        </p>
                    </div>
                @endif
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Prioridad</h3>
                <p class="text-lg font-semibold text-gray-900">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        @if($service->priority == "alta") bg-red-100 text-red-800
                        @elseif($service->priority == "media") bg-yellow-100 text-yellow-800
                        @else bg-green-100 text-green-800
                        @endif">
                        {{ ucfirst($service->priority) }}
                    </span>
                </p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Fecha Programada</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $service->scheduled_date->format("d/m/Y H:i") }}</p>
            </div>
        </div>
    </div>

    <!-- Service Details -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Client Information -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Información del Cliente</h3>
            <div class="space-y-3">
                <div>
                    <span class="text-sm font-medium text-gray-500">Razón Social:</span>
                    <p class="text-gray-900">{{ $service->client->name ?? "N/A" }}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">RUT:</span>
                    <p class="text-gray-900">{{ $service->client->rut ?? "N/A" }}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Email:</span>
                    <p class="text-gray-900">{{ $service->client->email ?? "N/A" }}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Teléfono:</span>
                    <p class="text-gray-900">{{ $service->client->phone ?? "N/A" }}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Tipo de Negocio:</span>
                    <p class="text-gray-900">{{ $service->client->business_type ?? "No especificado" }}</p>
                </div>
            </div>
        </div>

        <!-- Service Information -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Detalles del Servicio</h3>
            <div class="space-y-3">
                <div>
                    <span class="text-sm font-medium text-gray-500">Descripción:</span>
                    <p class="text-gray-900">{{ $service->description ?? "Sin descripción" }}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Técnico Asignado:</span>
                    <p class="text-gray-900">{{ $service->assignedUser->name ?? "Sin asignar" }}</p>
                </div>
                @if(auth()->check() && auth()->user()->hasRole('super-admin') && $service->price)
                <div>
                    <span class="text-sm font-medium text-gray-500">Precio de Venta:</span>
                    <p class="text-gray-900 font-semibold text-green-600">${{ number_format($service->price, 2, ',', '.') }}</p>
                </div>
                @endif
                @if($service->started_at)
                <div>
                    <span class="text-sm font-medium text-gray-500">Iniciado:</span>
                    <p class="text-gray-900">{{ $service->started_at->format("d/m/Y H:i") }}</p>
                </div>
                @endif
                @if($service->completed_at)
                <div>
                    <span class="text-sm font-medium text-gray-500">Completado:</span>
                    <p class="text-gray-900">{{ $service->completed_at->format("d/m/Y H:i") }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Products Used -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Productos Utilizados</h3>
        @if($service->products->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ingrediente Activo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha de Uso</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-white divide-y divide-gray-200">
                    @foreach($service->products as $product)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $product->active_ingredient }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $product->pivot->quantity }} {{ $product->unit }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $product->pivot->used_at ? \Carbon\Carbon::parse($product->pivot->used_at)->format("d/m/Y H:i") : "No registrado" }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-gray-500 text-center py-4">No se han registrado productos para este servicio</p>
        @endif
    </div>

    <!-- Checklist Data for non-monitoring services -->
    @if($service->checklist_data && ($service->status === 'finalizado' || $service->status === 'completado'))
    @php
        $checklistData = $service->checklist_data;
        $isMonitoreoCebaderas = $service->service_type === 'monitoreo-cebaderas';
    @endphp

    @if(!$isMonitoreoCebaderas)
        <!-- Products Applied from Checklist -->
        @if(isset($checklistData['products']))
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Producto Aplicado (Checklist)</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @if(isset($checklistData['products']['applied_product']))
                <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                    <span class="text-sm font-medium text-blue-700">Producto:</span>
                    <p class="text-lg font-semibold text-gray-900 mt-1">{{ $checklistData['products']['applied_product'] }}</p>
                </div>
                @endif

                @if(isset($checklistData['products']['quantity']))
                <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                    <span class="text-sm font-medium text-green-700">Cantidad:</span>
                    <p class="text-lg font-semibold text-gray-900 mt-1">{{ $checklistData['products']['quantity'] }}</p>
                </div>
                @endif

                @if(isset($checklistData['products']['dosis']))
                <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
                    <span class="text-sm font-medium text-purple-700">Dosis:</span>
                    <p class="text-lg font-semibold text-gray-900 mt-1">{{ $checklistData['products']['dosis'] }}</p>
                </div>
                @endif

                @if(isset($checklistData['products']['agua']))
                <div class="bg-cyan-50 rounded-lg p-4 border border-cyan-200">
                    <span class="text-sm font-medium text-cyan-700">Agua:</span>
                    <p class="text-lg font-semibold text-gray-900 mt-1">{{ $checklistData['products']['agua'] }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Observations from Checklist -->
        @if(isset($checklistData['observations']) && count($checklistData['observations']) > 0)
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Observaciones ({{ count($checklistData['observations']) }})</h3>
            <div class="space-y-3">
                @foreach($checklistData['observations'] as $index => $observation)
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <div class="flex items-start justify-between">
                        <span class="font-semibold text-gray-900">
                            @if(isset($observation['cebadera_code']))
                                Cebadera {{ $observation['cebadera_code'] }}
                            @else
                                Observación #{{ $index + 1 }}
                            @endif
                        </span>
                        @if(isset($observation['created_at']))
                        <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($observation['created_at'])->format('d/m/Y H:i') }}</span>
                        @endif
                    </div>
                    @if(isset($observation['detail']) && !empty($observation['detail']))
                    <p class="text-gray-700 mt-2">{{ $observation['detail'] }}</p>
                    @endif
                    @if(isset($observation['complementary']) && !empty($observation['complementary']))
                    <p class="text-sm text-gray-600 mt-1 italic">{{ $observation['complementary'] }}</p>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Sites Treated from Checklist -->
        @if(isset($checklistData['sites']['treated_sites']))
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Sitios Tratados</h3>
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <p class="text-gray-700 whitespace-pre-wrap">{{ $checklistData['sites']['treated_sites'] }}</p>
            </div>
        </div>
        @endif

        <!-- Description and Suggestions from Checklist -->
        @if(isset($checklistData['description']))
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Descripción y Sugerencias</h3>
            
            @if(isset($checklistData['description']['service_description']) && !empty($checklistData['description']['service_description']))
            <div class="mb-4">
                <h4 class="text-sm font-medium text-gray-500 mb-2">Descripción del Servicio:</h4>
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <p class="text-gray-700 whitespace-pre-wrap">{{ $checklistData['description']['service_description'] }}</p>
                </div>
            </div>
            @endif

            @if(isset($checklistData['description']['service_sugerencia']) && !empty($checklistData['description']['service_sugerencia']))
            <div class="mb-4">
                <h4 class="text-sm font-medium text-gray-500 mb-2">Sugerencias:</h4>
                <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                    <p class="text-gray-700 whitespace-pre-wrap">{{ $checklistData['description']['service_sugerencia'] }}</p>
                </div>
            </div>
            @endif
        </div>
        @endif
    @endif
    @endif

    <!-- Signatures -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Firmas de Confirmación</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Technician Signature -->
            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-2">Firma del Técnico</h4>
                @php
                    $techSignature = null;
                    // Check in all possible locations for the technician signature
                    if (isset($service->checklist_data['monitoreo_firma']['technician_signature'])) {
                        $techSignature = $service->checklist_data['monitoreo_firma']['technician_signature'];
                    } elseif (isset($service->checklist_data['description']['technician_signature'])) {
                        $techSignature = $service->checklist_data['description']['technician_signature'];
                    } elseif (isset($service->checklist_data['technician_signature'])) {
                        $techSignature = $service->checklist_data['technician_signature'];
                    }
                @endphp

                @if($techSignature)
                    <div class="border rounded-lg p-2 inline-block bg-gray-50">
                        @if(str_starts_with($techSignature, 'data:image'))
                            {{-- It's a base64 data URI, render directly --}}
                            <img src="{{ $techSignature }}" alt="Firma Técnico" class="max-h-24">
                        @else
                            {{-- It's a file path, use asset() --}}
                            <img src="{{ asset($techSignature) }}" alt="Firma Técnico" class="max-h-24">
                        @endif
                    </div>
                    <p class="text-sm text-gray-600 mt-1">{{ $service->assignedUser->name ?? 'Técnico' }}</p>
                @else
                    <p class="text-gray-500 italic">No registrada</p>
                @endif
            </div>

            <!-- Client Signature -->
            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-2">Firma del Cliente</h4>
                @php
                    $clientSignature = null;
                    // Check in all possible locations for the client signature
                    if (isset($service->checklist_data['monitoreo_firma']['client_signature'])) {
                        $clientSignature = $service->checklist_data['monitoreo_firma']['client_signature'];
                    } elseif (isset($service->checklist_data['description']['client_signature'])) {
                        $clientSignature = $service->checklist_data['description']['client_signature'];
                    } elseif (isset($service->checklist_data['client_signature'])) {
                        $clientSignature = $service->checklist_data['client_signature'];
                    }
                @endphp

                @if($clientSignature)
                    <div class="border rounded-lg p-2 inline-block bg-gray-50">
                        @if(str_starts_with($clientSignature, 'data:image'))
                            {{-- It's a base64 data URI, render directly --}}
                            <img src="{{ $clientSignature }}" alt="Firma Cliente" class="max-h-24">
                        @else
                            {{-- It's a file path, use asset() --}}
                            <img src="{{ asset($clientSignature) }}" alt="Firma Cliente" class="max-h-24">
                        @endif
                    </div>
                    <p class="text-sm text-gray-600 mt-1">
                        {{ $service->checklist_data['monitoreo_firma']['signer_name'] ?? ($service->client->name ?? 'Cliente') }}
                        @if(isset($service->checklist_data['monitoreo_firma']['signer_position']))
                            <span class="text-xs text-gray-500">({{ ucfirst($service->checklist_data['monitoreo_firma']['signer_position']) }})</span>
                        @endif
                    </p>
                @else
                    <p class="text-gray-500 italic">No registrada</p>
                @endif
            </div>
        </div>
    </div>

    <!-- PDF Download Button for completed services -->
    @if($service->status === 'finalizado' || $service->status === 'completado')
    <div class="bg-green-50 border border-green-200 rounded-lg p-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-3">
                <svg class="w-8 h-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <h3 class="text-lg font-semibold text-green-800">Servicio Finalizado</h3>
                    <p class="text-sm text-green-700">Puedes descargar el informe completo en PDF.</p>
                </div>
            </div>
            <a href="{{ url('/admin/technician-view/services/' . $service->id . '/pdf') }}" target="_blank" 
               class="inline-flex items-center px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                </svg>
                Descargar PDF
            </a>
        </div>
    </div>
    @endif

    <!-- Actions -->
    <div class="flex justify-between items-center">
        <a href="{{ route("admin.services.index") }}"
           class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
            Volver a Servicios
        </a>

        <div class="flex space-x-4">
            @can("edit-services")
            <a href="{{ route("admin.services.edit", $service) }}"
               class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                Editar Servicio
            </a>
            @endcan


        </div>
    </div>
</div>
@endsection
