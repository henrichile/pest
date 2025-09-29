@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Detalles Completos del servicio</h1>
                    <p class="text-gray-600 mt-1">Servicio #{{ $service->id }} - {{ $service->client->name ?? 'Cliente' }}</p>
                    <p class="text-gray-600">Tipo de Servicio: <strong>{{ ucfirst($service->service_type) }}</strong></p>
                </div>
                <a href="{{ route('technician.service.detail', $service) }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Volver al Servicio
                </a>
            </div>
        </div>
        @if($service->checklist_data)
            <!-- Etapa 1: Puntos de Control - Oculto para desinsectación -->
            @if($service->service_type !== 'desinsectacion' && $service->service_type !== 'desinfeccion')
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-6 h-6 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    Puntos de Control
                </h2>
                <ul class="space-y-2">
                    @if(isset($service->checklist_data["points"]) && count($service->checklist_data["points"]) > 0)
                        @foreach($service->checklist_data["points"] as $point)
                        <li class="flex items-center text-gray-700">
                            <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>             
                            </svg>
                            {{ $point }}
                        </li>
                        @endforeach
                    @else
                        <li class="text-gray-500 italic">No hay puntos de control registrados</li>
                    @endif
                </ul>
            </div>
            @endif

            <!-- Etapa 2: Productos Aplicados -->
            @if(true)
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-6 h-6 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    Productos Aplicados
                </h2>
                <ul class="space-y-2">
                    @if(isset($service->checklist_data["products"]) && count($service->checklist_data["products"]) > 0)
                        @foreach($service->checklist_data["products"] as $product)
                        <li class="flex items-center text-gray-700">
                            <svg class="w-4 h-4 text-blue-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>             
                            </svg>
                            {{ $product }}
                        </li>
                        @endforeach
                    @else
                        <li class="text-gray-500 italic">No hay productos aplicados registrados</li>
                    @endif
                </ul>
                </ul>
            </div>
            @endif

            <!-- Etapa 3: Resultados Observados -->
            @if(true)
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                @if($service->service_type === 'desinsectacion')
                <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-6 h-6 text-yellow-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    Lámparas Ultravioletas
                </h2>
                <div class="space-y-4">
                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="font-medium text-gray-700">Lámparas UV:</span>
                            <span class="text-gray-900">{{ $service->checklist_data["results"]["uv_lamps"] ?? "N/A" }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="font-medium text-gray-700">TUV:</span>
                            <span class="text-gray-900">{{ $service->checklist_data["results"]["tuv"] ?? "N/A" }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="font-medium text-gray-700">Dispositivos Instalados:</span>
                            <span class="text-gray-900">{{ $service->checklist_data["results"]["devices_installed"] ?? "N/A" }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="font-medium text-gray-700">Dispositivos Existentes:</span>
                            <span class="text-gray-900">{{ $service->checklist_data["results"]["devices_existing"] ?? "N/A" }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg md:col-span-2">
                            <span class="font-medium text-gray-700">Dispositivos Repuestos:</span>
                            <span class="text-gray-900">{{ $service->checklist_data["results"]["devices_replaced"] ?? "N/A" }}</span>
                        </div>
                    </div>
                    
                    @if(isset($service->checklist_data["results"]["observed_results"]) && count($service->checklist_data["results"]["observed_results"]) > 0)
                    <div class="mt-6">
                        <h3 class="font-medium text-gray-900 mb-3">Resultados Observados:</h3>
                        <ul class="space-y-2">
                            @foreach($service->checklist_data["results"]["observed_results"] ?? [] as $result)
                            <li class="flex items-center text-gray-700">
                                <svg class="w-4 h-4 text-yellow-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>             
                                </svg>
                                {{ is_string($result) ? $result : json_encode($result) }}
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
                @else
                <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-6 h-6 text-yellow-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    Resultados Observados
                </h2>
                <ul class="space-y-2">
                    @if(isset($service->checklist_data["results"]["observed_results"]) && count($service->checklist_data["results"]["observed_results"]) > 0)
                        @foreach($service->checklist_data["results"]["observed_results"] ?? [] as $result)
                        <li class="flex items-center text-gray-700">
                            <svg class="w-4 h-4 text-yellow-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>             
                            </svg>
                            {{ is_string($result) ? $result : json_encode($result) }}
                        </li>
                        @endforeach
                    @else
                        <li class="text-gray-500 italic">No hay resultados observados registrados</li>
                    @endif
                </ul>
                
                @if(isset($service->checklist_data["results"]["total_installed_points"]) || isset($service->checklist_data["results"]["total_consumption_activity"]))
                <div class="mt-6 grid md:grid-cols-2 gap-4">
                    @if(isset($service->checklist_data["results"]["total_installed_points"]))
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <span class="font-medium text-gray-700">Puntos Totales:</span>
                        <span class="text-gray-900">{{ $service->checklist_data["results"]["total_installed_points"] }}</span>
                    </div>
                    @endif
                    @if(isset($service->checklist_data["results"]["total_consumption_activity"]))
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <span class="font-medium text-gray-700">Consumo:</span>
                        <span class="text-gray-900">{{ $service->checklist_data["results"]["total_consumption_activity"] }}g</span>
                    </div>
                    @endif
                </div>
                @endif
                @endif
            </div>
            @endif

            <!-- Etapa 4: Observaciones Detalladas -->
            @if(isset($service->checklist_data["observations"]) && count($service->checklist_data["observations"]) > 0)
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-6 h-6 text-purple-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                    </svg>
                    Observaciones Detalladas
                </h2>
                <div class="space-y-4">
                    @foreach($service->checklist_data["observations"] as $index => $observation)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="font-semibold text-gray-900">
                                Observación #{{ $observation['observation_number'] ?? ($index + 1) }}
                                @if(isset($observation['cebadera_code']))
                                    - CE: {{ $observation['cebadera_code'] }}
                                @endif
                            </h3>
                            @if(isset($observation['created_at']))
                            <span class="text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($observation['created_at'])->format('d/m/Y H:i') }}
                            </span>
                            @endif
                        </div>
                        <p class="text-gray-700 mb-3">{{ $observation['detail'] ?? 'No especificado' }}</p>
                        @if(isset($observation['photo']) && $observation['photo'])
                        <div class="mt-3">
                            <img src="{{ asset($observation['photo']) }}" alt="Foto de observación" 
                                 class="max-w-xs rounded-lg border border-gray-200" style="width: 300px; height: 300px; object-fit: cover;">
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Etapa 5: Sitios Tratados -->
            @if(true)
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-6 h-6 text-indigo-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                    </svg>
                    Sitios Tratados
                </h2>
                <p class="text-gray-700">{{ $service->checklist_data["sites"]["treated_sites"] ?? "No especificado" }}</p>
            </div>
            @endif

            <!-- Etapa 6: Descripción del Servicio -->
            @if(isset($service->checklist_data["description"]) && isset($service->checklist_data["description"]["service_description"]))
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-6 h-6 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                    </svg>
                    Descripción del Servicio
                </h2>
                <p class="text-gray-700">{{ $service->checklist_data["description"]["service_description"] }}</p>
            </div>
            @endif

            <!-- Etapa 6.1: Sugerencias del Servicio -->
            @if(isset($service->checklist_data["description"]) && isset($service->checklist_data["description"]["service_sugerencia"]))
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-6 h-6 text-orange-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    Sugerencias del Servicio
                </h2>
                <p class="text-gray-700">{{ $service->checklist_data["description"]["service_sugerencia"] }}</p>
            </div>
            @endif

            <!-- Firmas Digitales -->
            @if(isset($service->checklist_data["description"]["technician_signature"]) || isset($service->checklist_data["description"]["client_signature"]))
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-6 h-6 text-gray-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd"></path>
                    </svg>
                    Firmas de Confirmación
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if(isset($service->checklist_data["description"]["technician_signature"]) && $service->checklist_data["description"]["technician_signature"])
                    <div class="text-center">
                        <h3 class="font-semibold text-gray-900 mb-2">Firma del Técnico</h3>
                        <img src="{{ $service->checklist_data["description"]["technician_signature"] }}" 
                             alt="Firma del Técnico" 
                             class="max-w-xs mx-auto border border-gray-200 rounded-lg">
                        <p class="text-sm text-gray-600 mt-2">{{ $service->assignedUser->name ?? "Técnico" }}</p>
                    </div>
                    @endif
                    
                    @if(isset($service->checklist_data["description"]["client_signature"]) && $service->checklist_data["description"]["client_signature"])
                    <div class="text-center">
                        <h3 class="font-semibold text-gray-900 mb-2">Firma del Cliente</h3>
                        <img src="{{ $service->checklist_data["description"]["client_signature"] }}" 
                             alt="Firma del Cliente" 
                             class="max-w-xs mx-auto border border-gray-200 rounded-lg">
                        <p class="text-sm text-gray-600 mt-2">{{ $service->client->name ?? "Cliente" }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

        @else
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="text-center">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No hay datos del checklist</h3>
                    <p class="text-gray-600">Este servicio no tiene información del checklist disponible.</p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
