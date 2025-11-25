@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <a href="{{ route('technician.service.detail', $service) }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Volver al Servicio
                        </a>
                    <h1 class="text-2xl font-bold text-gray-900">Detalles Completos del servicio</h1>
                    <p class="text-gray-600 mt-1">Servicio #{{ $service->id }} - {{ $service->client->name ?? 'Cliente' }}</p>
                    <p class="text-gray-600">Tipo de Servicio: <strong>{{ ucfirst($service->service_type) }}</strong></p>
                    @if($service->service_type === 'servicios-especiales' && $service->special_service_title)
                    <p class="text-green-700 font-semibold text-lg mt-2">
                        📋 {{ $service->special_service_title }}
                    </p>
                    @endif
                </div>

            </div>
        </div>
        @if($service->checklist_data)
            @php
                $checklistData = $service->checklist_data ?? [];
                $isMonitoreoCebaderas = $service->service_type === 'monitoreo-cebaderas';
            @endphp

            {{-- SECCIÓN ESPECÍFICA PARA MONITOREO DE CEBADERAS --}}
            @if($isMonitoreoCebaderas)
                {{-- 1. DATOS DEL SERVICIO --}}
                @if(isset($checklistData['monitoreo_datos']))
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-6 h-6 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        1. Datos del Servicio
                    </h2>

                    @if(isset($checklistData['monitoreo_datos']['pests_detected_list']) && count($checklistData['monitoreo_datos']['pests_detected_list']) > 0)
                    <div class="mb-4">
                        <strong class="text-gray-900">Plagas Detectadas:</strong>
                        <ul class="mt-2 space-y-1">
                            @foreach($checklistData['monitoreo_datos']['pests_detected_list'] as $pest)
                            <li class="flex items-center text-gray-700">
                                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $pest }}
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    @if(isset($checklistData['monitoreo_datos']['infestation_level']))
                    <div class="mb-4">
                        <strong class="text-gray-900">Nivel de Infestación:</strong>
                        <span class="ml-2 px-3 py-1 rounded-full text-sm font-semibold
                            @if($checklistData['monitoreo_datos']['infestation_level'] === 'critico') bg-red-100 text-red-800
                            @elseif($checklistData['monitoreo_datos']['infestation_level'] === 'alto') bg-orange-100 text-orange-800
                            @elseif($checklistData['monitoreo_datos']['infestation_level'] === 'medio') bg-yellow-100 text-yellow-800
                            @else bg-green-100 text-green-800
                            @endif">
                            {{ ucfirst($checklistData['monitoreo_datos']['infestation_level']) }}
                        </span>
                    </div>
                    @endif

                    @if(isset($checklistData['monitoreo_datos']['technician_observations']))
                    <div class="mb-4">
                        <strong class="text-gray-900">Observaciones del Técnico:</strong>
                        <p class="text-gray-700 mt-2 whitespace-pre-wrap">{{ $checklistData['monitoreo_datos']['technician_observations'] }}</p>
                    </div>
                    @endif

                    @if(isset($checklistData['monitoreo_datos']['client_recommendations']))
                    <div class="mb-4">
                        <strong class="text-gray-900">Recomendaciones al Cliente:</strong>
                        <p class="text-gray-700 mt-2 whitespace-pre-wrap">{{ $checklistData['monitoreo_datos']['client_recommendations'] }}</p>
                    </div>
                    @endif

                    @if(isset($checklistData['monitoreo_datos']['service_photos']) && count($checklistData['monitoreo_datos']['service_photos']) > 0)
                    <div class="mb-4">
                        <strong class="text-gray-900">Fotografías del Servicio:</strong>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-3">
                            @foreach($checklistData['monitoreo_datos']['service_photos'] as $photo)
                            @php
                                $photoPath = $photo;
                                if (strpos($photoPath, 'storage/') === 0) {
                                    $photoPath = '/' . $photoPath;
                                }
                            @endphp
                            <img src="{{ $photoPath }}" alt="Foto del servicio" class="rounded-lg border border-gray-200 w-full h-48 object-cover">
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
                @endif

                {{-- 2. CROQUIS DE CEBADERAS --}}
                @if(isset($checklistData['monitoreo_croquis']))
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-6 h-6 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                        </svg>
                        2. Croquis de Cebaderas
                    </h2>

                    @if(isset($checklistData['monitoreo_croquis']['croquis_notes']))
                    <div class="mb-4">
                        <strong class="text-gray-900">Notas del Croquis:</strong>
                        <p class="text-gray-700 mt-2 whitespace-pre-wrap">{{ $checklistData['monitoreo_croquis']['croquis_notes'] }}</p>
                    </div>
                    @endif

                    @if(isset($checklistData['monitoreo_croquis']['croquis_file']))
                    <div class="mb-4">
                        <strong class="text-gray-900">Croquis:</strong>
                        <div class="mt-3">
                            @php
                                // La ruta guardada es 'storage/services/croquis/filename.ext'
                                // Necesitamos convertirla a '/storage/services/croquis/filename.ext' para asset()
                                $croquisPath = $checklistData['monitoreo_croquis']['croquis_file'];
                                // Si la ruta empieza con 'storage/', agregar / al inicio
                                if (strpos($croquisPath, 'storage/') === 0) {
                                    $croquisPath = '/' . $croquisPath;
                                }
                            @endphp
                            <img src="{{ $croquisPath }}" alt="Croquis de cebaderas" class="rounded-lg border border-gray-200 max-w-full">
                        </div>
                    </div>
                    @endif
                </div>
                @endif

                {{-- 3. MONITOREO COMPLETO --}}
                @if(isset($checklistData['monitoreo_completo']))
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-6 h-6 text-purple-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        3. Monitoreo Completo
                    </h2>

                    @if(isset($checklistData['monitoreo_completo']['monitoring_date']))
                    <div class="mb-4">
                        <strong class="text-gray-900">Fecha de Monitoreo:</strong>
                        <span class="text-gray-700 ml-2">{{ \Carbon\Carbon::parse($checklistData['monitoreo_completo']['monitoring_date'])->format('d/m/Y') }}</span>
                    </div>
                    @endif

                    @if(isset($checklistData['monitoreo_completo']['total_bait_stations']))
                    <div class="mb-4">
                        <strong class="text-gray-900">Total de Cebaderas Instaladas:</strong>
                        <span class="text-gray-700 ml-2">{{ $checklistData['monitoreo_completo']['total_bait_stations'] }}</span>
                    </div>
                    @endif

                    @if(isset($checklistData['monitoreo_completo']['bait_stations']) && count($checklistData['monitoreo_completo']['bait_stations']) > 0)
                    <div class="mb-4">
                        <strong class="text-gray-900">Cebaderas Monitoreadas:</strong>
                        <div class="space-y-4 mt-3">
                            @foreach($checklistData['monitoreo_completo']['bait_stations'] as $index => $station)
                            <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                                <h3 class="font-semibold text-gray-900 mb-3">Cebadera #{{ $index + 1 }}</h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                                    @if(isset($station['code']))
                                    <div>
                                        <span class="text-sm font-medium text-gray-600">Código:</span>
                                        <span class="text-gray-900 ml-2">{{ $station['code'] }}</span>
                                    </div>
                                    @endif

                                    @if(isset($station['location']))
                                    <div>
                                        <span class="text-sm font-medium text-gray-600">Ubicación:</span>
                                        <span class="text-gray-900 ml-2">{{ $station['location'] }}</span>
                                    </div>
                                    @endif

                                    @if(isset($station['product_type']))
                                    <div>
                                        <span class="text-sm font-medium text-gray-600">Tipo de Producto:</span>
                                        <span class="text-gray-900 ml-2">{{ ucfirst($station['product_type']) }}</span>
                                    </div>
                                    @endif

                                    @if(isset($station['quantity']))
                                    <div>
                                        <span class="text-sm font-medium text-gray-600">Cantidad:</span>
                                        <span class="text-gray-900 ml-2">{{ $station['quantity'] }} {{ $station['unit'] ?? 'g' }}</span>
                                    </div>
                                    @endif
                                </div>

                                @if(isset($station['observations']) && is_array($station['observations']) && count($station['observations']) > 0)
                                <div class="mb-3">
                                    <span class="text-sm font-medium text-gray-600">Observaciones:</span>
                                    <ul class="mt-2 space-y-1">
                                        @foreach($station['observations'] as $obs)
                                        <li class="flex items-center text-gray-700 text-sm">
                                            <svg width="20" height="20" class="text-yellow-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ ucfirst(str_replace('_', ' ', $obs)) }}
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif

                                @if(isset($station['photos']) && count($station['photos']) > 0)
                                <div>
                                    <span class="text-sm font-medium text-gray-600">Fotografías:</span>
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mt-2">
                                        @foreach($station['photos'] as $photo)
                                        @php
                                            $photoPath = $photo;
                                            if (strpos($photoPath, 'storage/') === 0) {
                                                $photoPath = '/' . $photoPath;
                                            }
                                        @endphp
                                        <div class="photo-item">
                                            <img src="{{ $photoPath }}"
                                                 alt="Foto de cebadera {{ $station['code'] ?? '' }}"
                                                 class="rounded-lg shadow-sm border border-gray-200 w-full h-32 object-cover cursor-pointer hover:opacity-75 transition-opacity"
                                                 onclick="window.open('{{ $photoPath }}', '_blank')">
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if(isset($checklistData['monitoreo_completo']['traps']) && count($checklistData['monitoreo_completo']['traps']) > 0)
                    <div class="mb-4">
                        <strong class="text-gray-900">Trampas de Captura:</strong>
                        <div class="space-y-4 mt-3">
                            @foreach($checklistData['monitoreo_completo']['traps'] as $index => $trap)
                            <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                                <h3 class="font-semibold text-gray-900 mb-3">Trampa #{{ $index + 1 }}</h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                                    @if(isset($trap['code']))
                                    <div>
                                        <span class="text-sm font-medium text-gray-600">Código:</span>
                                        <span class="text-gray-900 ml-2">{{ $trap['code'] }}</span>
                                    </div>
                                    @endif

                                    @if(isset($trap['location']))
                                    <div>
                                        <span class="text-sm font-medium text-gray-600">Ubicación:</span>
                                        <span class="text-gray-900 ml-2">{{ $trap['location'] }}</span>
                                    </div>
                                    @endif

                                    @if(isset($trap['product_type']))
                                    <div>
                                        <span class="text-sm font-medium text-gray-600">Producto/Material:</span>
                                        <span class="text-gray-900 ml-2">{{ ucfirst($trap['product_type']) }}</span>
                                    </div>
                                    @endif

                                    @if(isset($trap['status']))
                                    <div>
                                        <span class="text-sm font-medium text-gray-600">Estado:</span>
                                        <span class="ml-2 px-2 py-1 rounded text-xs font-semibold
                                            @if($trap['status'] === 'captura') bg-red-100 text-red-800
                                            @elseif($trap['status'] === 'activa') bg-green-100 text-green-800
                                            @elseif($trap['status'] === 'dañada') bg-yellow-100 text-yellow-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ ucfirst($trap['status']) }}
                                        </span>
                                    </div>
                                    @endif
                                </div>

                                @if(isset($trap['notes']))
                                <div class="mb-3">
                                    <span class="text-sm font-medium text-gray-600">Notas:</span>
                                    <p class="text-gray-700 text-sm mt-1">{{ $trap['notes'] }}</p>
                                </div>
                                @endif

                                @if(isset($trap['photos']) && count($trap['photos']) > 0)
                                <div>
                                    <span class="text-sm font-medium text-gray-600">Fotografías:</span>
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mt-2">
                                        @foreach($trap['photos'] as $photo)
                                        @php
                                            $photoPath = $photo;
                                            if (strpos($photoPath, 'storage/') === 0) {
                                                $photoPath = '/' . $photoPath;
                                            }
                                        @endphp
                                        <img src="{{ asset($photoPath) }}" alt="Foto de trampa {{ $trap['code'] ?? ($index + 1) }}" class="rounded-lg border border-gray-200 w-full h-32 object-cover">
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if(isset($checklistData['monitoreo_completo']['general_observations']))
                    <div class="mb-4">
                        <strong class="text-gray-900">Observaciones Generales:</strong>
                        <p class="text-gray-700 mt-2 whitespace-pre-wrap">{{ $checklistData['monitoreo_completo']['general_observations'] }}</p>
                    </div>
                    @endif

                    @if(isset($checklistData['monitoreo_completo']['client_recommendations_monitoring']))
                    <div class="mb-4">
                        <strong class="text-gray-900">Recomendaciones al Cliente:</strong>
                        <p class="text-gray-700 mt-2 whitespace-pre-wrap">{{ $checklistData['monitoreo_completo']['client_recommendations_monitoring'] }}</p>
                    </div>
                    @endif
                </div>
                @endif

                {{-- 4. ESTADÍSTICAS --}}
                @if(isset($checklistData['monitoreo_estadisticas']))
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-6 h-6 text-indigo-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" clip-rule="evenodd"></path>
                        </svg>
                        4. Estadísticas
                    </h2>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                        @if(isset($checklistData['monitoreo_estadisticas']['total_monitored']))
                        <div class="bg-blue-50 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ $checklistData['monitoreo_estadisticas']['total_monitored'] }}</div>
                            <div class="text-xs text-gray-600 mt-1">Monitoreadas</div>
                        </div>
                        @endif

                        @if(isset($checklistData['monitoreo_estadisticas']['total_active']))
                        <div class="bg-green-50 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-green-600">{{ $checklistData['monitoreo_estadisticas']['total_active'] }}</div>
                            <div class="text-xs text-gray-600 mt-1">Activas</div>
                        </div>
                        @endif

                        @if(isset($checklistData['monitoreo_estadisticas']['total_problems']))
                        <div class="bg-red-50 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-red-600">{{ $checklistData['monitoreo_estadisticas']['total_problems'] }}</div>
                            <div class="text-xs text-gray-600 mt-1">Con Problemas</div>
                        </div>
                        @endif

                        @if(isset($checklistData['monitoreo_estadisticas']['average_consumption_percent']))
                        <div class="bg-yellow-50 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-yellow-600">{{ number_format($checklistData['monitoreo_estadisticas']['average_consumption_percent'], 1) }}%</div>
                            <div class="text-xs text-gray-600 mt-1">Consumo Promedio</div>
                        </div>
                        @endif
                    </div>

                    @if(isset($checklistData['monitoreo_estadisticas']['activity_level']))
                    <div class="mb-4">
                        <strong class="text-gray-900">Nivel de Actividad:</strong>
                        <span class="ml-2 px-3 py-1 rounded-full text-sm font-semibold bg-purple-100 text-purple-800">
                            {{ strtoupper($checklistData['monitoreo_estadisticas']['activity_level']) }}
                        </span>
                    </div>
                    @endif

                    @if(isset($checklistData['monitoreo_estadisticas']['executive_summary']))
                    <div class="mb-4">
                        <strong class="text-gray-900">Resumen Ejecutivo:</strong>
                        <p class="text-gray-700 mt-2 whitespace-pre-wrap">{{ $checklistData['monitoreo_estadisticas']['executive_summary'] }}</p>
                    </div>
                    @endif
                </div>
                @endif

                {{-- 5. ANÁLISIS IA --}}
                @if(isset($checklistData['monitoreo_analisis']))
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-6 h-6 text-pink-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"></path>
                        </svg>
                        5. Análisis IA
                    </h2>

                    @if(isset($checklistData['monitoreo_analisis']['ai_analysis_data']) && is_array($checklistData['monitoreo_analisis']['ai_analysis_data']))
                    <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-lg p-4 mb-4">
                        @foreach($checklistData['monitoreo_analisis']['ai_analysis_data'] as $key => $value)
                            @if(is_string($value) || is_numeric($value))
                            <div class="mb-2">
                                <strong class="text-gray-900">{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                <span class="text-gray-700 ml-2">{{ $value }}</span>
                            </div>
                            @endif
                        @endforeach
                    </div>
                    @endif

                    @if(isset($checklistData['monitoreo_analisis']['technician_ai_notes']))
                    <div class="mb-4">
                        <strong class="text-gray-900">Notas del Técnico sobre el Análisis IA:</strong>
                        <p class="text-gray-700 mt-2 whitespace-pre-wrap">{{ $checklistData['monitoreo_analisis']['technician_ai_notes'] }}</p>
                    </div>
                    @endif

                    @if(isset($checklistData['monitoreo_analisis']['ai_analysis_validated']) && $checklistData['monitoreo_analisis']['ai_analysis_validated'])
                    <div class="mb-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Análisis Validado
                        </span>
                    </div>
                    @endif
                </div>
                @endif

                {{-- 6. FIRMA FINAL --}}
                @if(isset($checklistData['monitoreo_firma']))
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-6 h-6 text-gray-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd"></path>
                        </svg>
                        6. Firma Final
                    </h2>

                    @if(isset($checklistData['monitoreo_firma']['signer_name']))
                    <div class="mb-4">
                        <strong class="text-gray-900">Firmante:</strong>
                        <span class="text-gray-700 ml-2">{{ $checklistData['monitoreo_firma']['signer_name'] }}</span>
                    </div>
                    @endif

                    @if(isset($checklistData['monitoreo_firma']['signer_position']))
                    <div class="mb-4">
                        <strong class="text-gray-900">Cargo/Relación:</strong>
                        <span class="text-gray-700 ml-2">{{ ucfirst($checklistData['monitoreo_firma']['signer_position']) }}</span>
                    </div>
                    @endif

                    @if(isset($checklistData['monitoreo_firma']['service_completed']) && $checklistData['monitoreo_firma']['service_completed'])
                    <div class="mb-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Servicio Completado
                        </span>
                    </div>
                    @endif

                    @if(isset($checklistData['monitoreo_firma']['technician_signature']))
                    <div class="text-center">
                        <h3 class="font-semibold text-gray-900 mb-2">Firma del Técnico</h3>
                        <img src="{{ $checklistData['monitoreo_firma']['technician_signature'] }}"
                             alt="Firma del Técnico"
                             class="max-w-xs mx-auto border border-gray-200 rounded-lg">
                        <p class="text-sm text-gray-600 mt-2">{{ $service->assignedUser->name ?? "Técnico" }}</p>
                    </div>
                    @endif
                </div>
                @endif
            @endif

            <!-- Etapa 1: Puntos de Control - Oculto para desinsectación, desinfección, sanitizacion, desratización, servicios-especiales Y monitoreo-cebaderas -->
            @if($service->service_type !== 'desinsectacion' && $service->service_type !== 'desinfeccion' && $service->service_type !== 'sanitizacion' && $service->service_type !== 'desratizacion' && $service->service_type !== 'servicios-especiales' && !$isMonitoreoCebaderas)
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-6 h-6 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    Puntos de Control
                </h2>
                <ul class="space-y-2">
                    @if(isset($service->checklist_data["points"]) && count($service->checklist_data["points"]) > 0)
                        @php
                            // Convertir el objeto de puntos de control en un array legible
                            $pointsToDisplay = [];
                            $pointsData = $service->checklist_data["points"];

                            // Mapeo de claves a etiquetas legibles
                            $pointsMapping = [
                                'installed_points_check' => 'Puntos instalados',
                                'existing_points_check' => 'Puntos existentes',
                                'spare_points_check' => 'Puntos de repuesto',
                                'bait_weight_check' => 'Peso cebo instalado (gramos)',
                                'physical_installed_check' => 'Puntos físicos instalados',
                                'physical_existing_check' => 'Puntos físicos existentes',
                                'physical_spare_check' => 'Puntos físicos de repuesto'
                            ];

                            // Si es un array asociativo (checkboxes), convertir a array de strings
                            if (is_array($pointsData) && !isset($pointsData[0])) {
                                foreach ($pointsMapping as $key => $label) {
                                    if (isset($pointsData[$key]) && $pointsData[$key]) {
                                        $pointsToDisplay[] = $label;
                                    }
                                }
                            } else {
                                // Si ya es un array de strings, usarlo directamente
                                $pointsToDisplay = $pointsData;
                            }
                        @endphp

                        @if(count($pointsToDisplay) > 0)
                            @foreach($pointsToDisplay as $point)
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
                    @else
                        <li class="text-gray-500 italic">No hay puntos de control registrados</li>
                    @endif
                </ul>
            </div>
            @endif

            <!-- Etapa 2: Productos Aplicados - Oculto para servicios-especiales Y monitoreo-cebaderas -->
            @if($service->service_type !== 'servicios-especiales' && !$isMonitoreoCebaderas)
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-6 h-6 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    Productos Aplicados
                </h2>

                {{-- ✅ Dosis y Agua para desinfección, sanitización y desinsectación --}}
                @if(in_array($service->service_type, ['desinfeccion', 'sanitizacion', 'desinsectacion']) && isset($service->checklist_data["products"]))
                    @php
                        $productsData = $service->checklist_data["products"];
                    @endphp
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                        <div>
                            <strong>Producto aplicado:</strong> {{ $productsData['applied_product'] ?? 'No especificado' }}<br>
                            <strong>Dosis aplicada:</strong> {{ $productsData['dosis'] ?? 'No especificado' }} cc<br>
                            <strong>Agua aplicada:</strong> {{ $productsData['agua'] ?? 'No especificado' }} Lts
                        </div>
                    </div>
                @endif

                <ul class="space-y-2">
                    @if(isset($service->checklist_data["products"]) && count($service->checklist_data["products"]) > 0)
                        @php
                            $productsData = $service->checklist_data["products"];
                            // Si es un array con claves 'productos', usar ese array
                            $productsList = isset($productsData['productos']) ? $productsData['productos'] : $productsData;
                            // Filtrar las claves 'dosis' y 'agua' si existen en el nivel raíz
                            if (is_array($productsList)) {
                                $productsList = array_filter($productsList, function($key) {
                                    return !in_array($key, ['dosis', 'agua']);
                                }, ARRAY_FILTER_USE_KEY);
                            }
                        @endphp

                        @if(count($productsList) > 0)
                            @foreach($productsList as $product)
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
                    @else
                        <li class="text-gray-500 italic">No hay productos aplicados registrados</li>
                    @endif
                </ul>
            </div>
            @endif

            <!-- Etapa 3: Resultados Observados -->
            @if($service->service_type === 'desinsectacion' || $service->service_type === 'desratizacion')
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
                                 class="max-w-xs rounded-lg border border-gray-200" style="width: 100%; object-fit: cover;">
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Etapa 5: Sitios Tratados - Oculto para monitoreo-cebaderas -->
            @if(!$isMonitoreoCebaderas)
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
            @if(isset($service->checklist_data["description"]["service_description"]) && trim($service->checklist_data["description"]["service_description"]) !== '')
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-6 h-6 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                    </svg>
                    Descripción del Servicio
                </h2>
                <p class="text-gray-700 whitespace-pre-wrap">{{ $service->checklist_data["description"]["service_description"] }}</p>
            </div>
            @elseif(isset($service->checklist_data["description"]["content"]) && trim($service->checklist_data["description"]["content"]) !== '')
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-6 h-6 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                    </svg>
                    Descripción del Servicio
                </h2>
                <p class="text-gray-700 whitespace-pre-wrap">{{ $service->checklist_data["description"]["content"] }}</p>
            </div>
            @endif

            <!-- Etapa 6.1: Sugerencias del Servicio -->
            @if(isset($service->checklist_data["description"]["service_sugerencia"]) && trim($service->checklist_data["description"]["service_sugerencia"]) !== '')
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-6 h-6 text-orange-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    Sugerencias
                </h2>
                <p class="text-gray-700 whitespace-pre-wrap">{{ $service->checklist_data["description"]["service_sugerencia"] }}</p>
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
