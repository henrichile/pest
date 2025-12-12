@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 md:py-8">
        <!-- Header con hamburguesa y título (Móvil) -->
        <div class="mb-4 md:hidden" style="padding-top: 2.5rem;">
            <div class="flex items-center gap-3">
                <!-- Hamburguesa -->
                <button id="page-mobile-menu-button" class="flex-shrink-0 p-2 rounded-lg bg-white border border-gray-300 shadow-md hover:bg-gray-50 transition-colors" style="z-index: 50;">
                    <svg id="page-menu-icon" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="text-gray-900 dark:text-white">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                    <svg id="page-close-icon" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="text-gray-900 dark:text-white">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                
                <!-- Título -->
                <div class="flex-1">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white" class="font-bold">
                        Detalles del Checklist
                    </h2>
                </div>
            </div>
        </div>
        <!-- Header Desktop -->
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
                // Permitir mostrar secciones de monitoreo si existen los datos, independientemente del tipo exacto
                $hasMonitoreoData = isset($checklistData['monitoreo_completo']) || isset($checklistData['monitoreo_datos']) || isset($checklistData['monitoreo_estadisticas']);
            @endphp

            {{-- SECCIÓN ESPECÍFICA PARA MONITOREO DE CEBADERAS --}}
            @if($isMonitoreoCebaderas || $hasMonitoreoData)
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
                @php
                    // Fallback: Calcular estadísticas si no existen O si están en cero pero hay datos reales
                    $stats = $checklistData['monitoreo_estadisticas'] ?? null;
                    $hasStations = isset($checklistData['monitoreo_completo']['bait_stations']) && count($checklistData['monitoreo_completo']['bait_stations']) > 0;
                    $statsAreEmpty = !isset($stats) || (isset($stats['total_monitored']) && $stats['total_monitored'] == 0 && $hasStations);

                    if ($statsAreEmpty && isset($checklistData['monitoreo_completo'])) {
                        $monitoreoCompleto = $checklistData['monitoreo_completo'];
                        $baitStations = $monitoreoCompleto['bait_stations'] ?? [];
                        
                        $totalMonitored = count($baitStations);
                        $totalActive = 0;
                        $totalProblems = 0;
                        $totalConsumption = 0;
                        $totalCaptures = 0;
                        
                        foreach ($baitStations as $station) {
                            $observations = $station['observations'] ?? [];
                            if (!is_array($observations)) $observations = [];
                            
                            // Verificar si está activa (Lógica del PDF: todas menos bloqueadas/sustraídas)
                            $isActive = true;
                            if (in_array('bloqueada', $observations) || in_array('sustraida', $observations)) {
                                $isActive = false;
                            }
                            if ($isActive) {
                                $totalActive++;
                            }
                            
                            // Verificar problemas
                            if (in_array('bloqueada', $observations) || 
                                in_array('sustraida', $observations) || 
                                in_array('hongos', $observations) || 
                                in_array('sucia', $observations)) {
                                $totalProblems++;
                            }
                            
                            // Calcular consumo
                            if (in_array('consumo_50', $observations)) {
                                $totalConsumption += 50;
                            } elseif (isset($station['consumption'])) {
                                $totalConsumption += floatval($station['consumption']);
                            }
                            
                            // Contar capturas
                            if (isset($station['captures'])) {
                                $totalCaptures += intval($station['captures']);
                            }
                        }
                        
                        // Consumo promedio sobre el total de monitoreadas (como en el PDF)
                        $avgConsumption = $totalMonitored > 0 ? ($totalConsumption / $totalMonitored) : 0;
                        
                        // Determinar nivel de actividad
                        $activityLevel = 'Bajo';
                        if ($avgConsumption > 50) {
                            $activityLevel = 'Crítico';
                        } elseif ($avgConsumption > 30) {
                            $activityLevel = 'Alto';
                        } elseif ($avgConsumption > 10) {
                            $activityLevel = 'Medio';
                        }
                        
                        $checklistData['monitoreo_estadisticas'] = [
                            'total_monitored' => $totalMonitored,
                            'total_active' => $totalActive,
                            'total_problems' => $totalProblems,
                            'average_consumption_percent' => $avgConsumption,
                            'activity_level' => $activityLevel,
                            'executive_summary' => 'Resumen generado automáticamente basado en los datos de monitoreo.'
                        ];
                    }
                @endphp
                @if(isset($checklistData['monitoreo_estadisticas']))
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                        <svg class="w-6 h-6 text-purple-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"></path>
                            <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"></path>
                        </svg>
                        4. Estadísticas
                    </h2>

                    <!-- Fila Superior: Tarjetas de Estado -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <!-- Monitoreadas -->
                        <div class="bg-white rounded-lg p-4 border border-gray-200 text-center">
                            <h3 class="text-xs font-bold text-gray-500 uppercase mb-1">Cebaderas Monitoreadas</h3>
                            <p class="text-3xl font-bold text-gray-800">{{ $checklistData['monitoreo_estadisticas']['total_monitored'] }}</p>
                        </div>
                        
                        <!-- Activas -->
                        <div class="bg-green-50 rounded-lg p-4 border border-green-200 text-center">
                            <h3 class="text-xs font-bold text-green-700 uppercase mb-1">Cebaderas Activas</h3>
                            <p class="text-3xl font-bold text-green-600">{{ $checklistData['monitoreo_estadisticas']['total_active'] }}</p>
                        </div>
                        
                        <!-- Con Problemas -->
                        <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-200 text-center">
                            <h3 class="text-xs font-bold text-yellow-700 uppercase mb-1">Con Problemas</h3>
                            <p class="text-3xl font-bold text-yellow-600">{{ $checklistData['monitoreo_estadisticas']['total_problems'] }}</p>
                        </div>
                    </div>

                    <!-- Fila Inferior: Resumen de Métricas -->
                    <div class="bg-gray-50 rounded-lg border border-gray-200 p-6 mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center divide-y md:divide-y-0 md:divide-x divide-gray-200">
                            <div class="py-2 md:py-0">
                                <h3 class="text-sm text-gray-500 mb-1">Total Monitoreos</h3>
                                <p class="text-2xl font-bold text-gray-800">{{ $checklistData['monitoreo_estadisticas']['total_monitored'] }}</p>
                            </div>
                            <div class="py-2 md:py-0">
                                <h3 class="text-sm text-gray-500 mb-1">Consumo Promedio</h3>
                                <p class="text-2xl font-bold text-red-500">{{ number_format($checklistData['monitoreo_estadisticas']['average_consumption_percent'], 1) }}%</p>
                            </div>
                            <div class="py-2 md:py-0">
                                <h3 class="text-sm text-gray-500 mb-1">Nivel Actual</h3>
                                @php
                                    $level = strtoupper($checklistData['monitoreo_estadisticas']['activity_level'] ?? 'BAJO');
                                    // Mapeo de colores según nivel
                                    $colorClass = match($level) {
                                        'CRÍTICO' => 'bg-red-600',
                                        'ALTO' => 'bg-red-500',
                                        'MEDIO' => 'bg-yellow-500',
                                        default => 'bg-green-500' // BAJO
                                    };
                                @endphp
                                <span class="inline-block px-4 py-1 rounded-full text-white text-sm font-bold {{ $colorClass }}">
                                    {{ $level }}
                                </span>
                            </div>
                        </div>
                    </div>

                    @if(isset($checklistData['monitoreo_estadisticas']['executive_summary']))
                    <div class="mb-4">
                        <strong class="text-gray-900">Resumen Ejecutivo:</strong>
                        <p class="text-gray-700 mt-2">{{ $checklistData['monitoreo_estadisticas']['executive_summary'] }}</p>
                    </div>
                    @endif
                    
                    <!-- Gráfico de Evolución -->
                    @if(isset($checklistData['monitoreo_estadisticas']['historical_data']) && count($checklistData['monitoreo_estadisticas']['historical_data']) > 0)
                        <div class="mt-8">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Evolución del Consumo</h3>
                            <canvas id="consumptionChart" width="400" height="200"></canvas>
                        </div>
                    @endif
                </div>
                @endif

@push('scripts')
<script>
(function() {
    function initPageMenuButton() {
        const pageMenuButton = document.getElementById('page-mobile-menu-button');
        
        if (!pageMenuButton) {
            console.warn('[PAGE MENU] Botón page-mobile-menu-button no encontrado, reintentando...');
            setTimeout(initPageMenuButton, 100);
            return;
        }
        
        console.log('[PAGE MENU] Botón encontrado, configurando listener...');
        
        pageMenuButton.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('[PAGE MENU] Click detectado, llamando a window.openMobileMenu()');
            
            if (typeof window.openMobileMenu === 'function') {
                window.openMobileMenu();
            } else {
                console.error('[PAGE MENU] window.openMobileMenu no está definida!');
            }
        });
        
        console.log('[PAGE MENU] Listener configurado correctamente');
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPageMenuButton);
    } else {
        initPageMenuButton();
    }
})();
</script>
@endpush

@endsection
