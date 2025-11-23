<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Servicio - {{ $service->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #1a472a;
            padding-bottom: 20px;
            position: relative;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #1a472a;
            margin-bottom: 10px;
        }

        .title {
            font-size: 20px;
            font-weight: bold;
            color: #333;
        }

        .qr-code {
            position: absolute;
            top: 0;
            right: 0;
            width: 80px;
            height: 80px;
            border: 1px solid #ddd;
        }

        .service-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding: 5px 0;
        }

        .info-label {
            font-weight: bold;
            color: #1a472a;
        }

        .info-value {
            color: #333;
        }

        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #1a472a;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }

        .checklist-item {
            margin-bottom: 8px;
            padding: 5px 0;
        }

        .observation-item {
            background: #f8f9fa;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
            border-left: 4px solid #1a472a;
        }

        .observation-header {
            font-weight: bold;
            color: #1a472a;
            margin-bottom: 8px;
        }

        .observation-detail {
            margin-bottom: 8px;
        }

        .observation-photo, .bait-station-photo, .croquis-image, .service-photo {
            max-width: 300px;
            max-height: 200px;
            border-radius: 8px;
            margin-top: 10px;
            border: 1px solid #ddd;
            object-fit: contain;
        }

        .photo-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }

        .photo-item {
            flex: 0 0 auto;
        }

        .signature-section {
            margin-top: 30px;
            border-top: 2px solid #1a472a;
            padding-top: 20px;
        }

        .signature-box {
            display: inline-block;
            width: 45%;
            margin: 10px 2%;
            text-align: center;
        }

        .signature-image {
            max-width: 200px;
            max-height: 80px;
            border: 1px solid #ddd;
            margin-bottom: 5px;
        }

        .signature-line {
            border-bottom: 1px solid #333;
            height: 40px;
            margin-bottom: 5px;
        }

        .signature-label {
            font-size: 12px;
            color: #666;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }

        .status-finalizado {
            background: #d4edda;
            color: #155724;
        }

        .priority-alta {
            background: #f8d7da;
            color: #721c24;
        }

        .priority-media {
            background: #fff3cd;
            color: #856404;
        }

        .priority-baja {
            background: #d1ecf1;
            color: #0c5460;
        }

        .points-list {
            margin-left: 20px;
        }

        .points-list li {
            margin-bottom: 5px;
        }

        .no-data {
            color: #666;
            font-style: italic;
        }

        .geolocation-info {
            background: #e8f5e8;
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
        }

        .product-info {
            background: #fff3cd;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .technical-findings {
            background: #f8d7da;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .validation-info {
            background: #d1ecf1;
            padding: 10px;
            border-radius: 5px;
            margin-top: 20px;
            font-size: 11px;
        }

        .bait-station-card {
            background: #f8f9fa;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
            border-left: 4px solid #1a472a;
        }

        .bait-station-header {
            font-weight: bold;
            color: #1a472a;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .bait-station-detail {
            margin-bottom: 5px;
            font-size: 12px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-top: 10px;
        }

        .stat-item {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
        }

        .stat-label {
            font-size: 11px;
            color: #666;
            margin-bottom: 5px;
        }

        .stat-value {
            font-size: 18px;
            font-weight: bold;
            color: #1a472a;
        }

        .ai-analysis-box {
            background: #e8f5e8;
            padding: 15px;
            border-radius: 8px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">PEST CONTROLLER</div>
        <div class="title">REPORTE DE SERVICIO COMPLETADO</div>
        @if(isset($qrCode))
        <div class="qr-code">
            <img src="data:image/png;base64,{{ $qrCode }}" alt="QR de Validación" style="width: 100%; height: 100%;">
        </div>
        @endif
    </div>

    {{-- Datos del Cliente, Sitio y Dirección Geolocalizada --}}
    <div class="service-info">
        <div class="info-row">
            <span class="info-label">Número de Servicio:</span>
            <span class="info-value">#{{ $service->id }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Cliente:</span>
            <span class="info-value">{{ $service->client->name ?? "N/A" }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Dirección:</span>
            <span class="info-value">{{ $service->address ?? "N/A" }}</span>
        </div>
        @if($service->latitude && $service->longitude)
        <div class="geolocation-info">
            <div class="info-row">
                <span class="info-label">Coordenadas GPS:</span>
                <span class="info-value">{{ $service->latitude }}, {{ $service->longitude }}</span>
            </div>

            @php
                $mapboxToken = config('services.mapbox.access_token');
                $lat = $service->latitude;
                $lng = $service->longitude;
                $zoom = 15;
                $width = 700;
                $height = 400;

                // URL de la API de Mapbox Static Images
                $mapboxUrl = "https://api.mapbox.com/styles/v1/mapbox/streets-v12/static/pin-s+ff0000({$lng},{$lat})/{$lng},{$lat},{$zoom}/{$width}x{$height}@2x?access_token={$mapboxToken}";

                // Intentar obtener la imagen del mapa
                $mapImageBase64 = null;
                try {
                    $mapImageContent = @file_get_contents($mapboxUrl);
                    if ($mapImageContent !== false) {
                        $mapImageBase64 = base64_encode($mapImageContent);
                    }
                } catch (\Exception $e) {
                    \Log::error('Error loading Mapbox static map: ' . $e->getMessage());
                }
            @endphp

            @if($mapImageBase64)
            <div style="margin-top: 15px; text-align: center;">
                <img src="data:image/png;base64,{{ $mapImageBase64 }}"
                     style="max-width: 100%; height: auto; border: 2px solid #e5e7eb; border-radius: 8px;"
                     alt="Mapa de ubicación">
            </div>
            @else
            <div class="info-row">
                <span class="info-label">Ver en mapa:</span>
                <span class="info-value">https://maps.google.com/?q={{ $service->latitude }},{{ $service->longitude }}</span>
            </div>
            @endif
        </div>
        @endif
        <div class="info-row">
            <span class="info-label">Tipo de Servicio:</span>
            <span class="info-value">{{ $service->serviceType->name ?? ucfirst(str_replace('-', ' ', $service->service_type)) }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Técnico Asignado:</span>
            <span class="info-value">{{ $service->assignedUser->name ?? "N/A" }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Fecha de Servicio:</span>
            <span class="info-value">{{ $service->scheduled_date ? $service->scheduled_date->format("d/m/Y H:i") : "N/A" }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Fecha de Finalización:</span>
            <span class="info-value">{{ $service->checklist_completed_at ? $service->checklist_completed_at->format("d/m/Y H:i") : "N/A" }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Estado:</span>
            <span class="info-value">
                <span class="status-badge status-finalizado">FINALIZADO</span>
            </span>
        </div>
        <div class="info-row">
            <span class="info-label">Prioridad:</span>
            <span class="info-value">
                @php
                    $priority = strtoupper($service->priority ?? "MEDIA");
                    $priorityClass = 'priority-' . strtolower($service->priority ?? "media");
                @endphp
                <span class="status-badge {{ $priorityClass }}">{{ $priority }}</span>
            </span>
        </div>
    </div>

    @php
        $checklistData = $service->checklist_data ?? [];
        $isMonitoreoCebaderas = $service->service_type === 'monitoreo-cebaderas';
    @endphp

    @if($isMonitoreoCebaderas)
        {{-- PROCESO DE MONITOREO DE CEBADERAS --}}

        {{-- 1. DATOS DEL SERVICIO --}}
        @if(isset($checklistData['monitoreo_datos']))
        <div class="section">
            <div class="section-title">1. DATOS DEL SERVICIO</div>

            @if(isset($checklistData['monitoreo_datos']['pests_detected_list']) && count($checklistData['monitoreo_datos']['pests_detected_list']) > 0)
            <div class="checklist-item">
                <strong>Plagas Detectadas:</strong>
                <ul class="points-list">
                    @foreach($checklistData['monitoreo_datos']['pests_detected_list'] as $pest)
                        <li>{{ $pest }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            @if(isset($checklistData['monitoreo_datos']['infestation_level']))
            <div class="checklist-item">
                <strong>Nivel de Infestación:</strong> {{ ucfirst($checklistData['monitoreo_datos']['infestation_level']) }}
            </div>
            @endif

            @if(isset($checklistData['monitoreo_datos']['technician_observations']))
            <div class="checklist-item">
                <strong>Observaciones del Técnico:</strong><br>
                {{ $checklistData['monitoreo_datos']['technician_observations'] }}
            </div>
            @endif

            @if(isset($checklistData['monitoreo_datos']['client_recommendations']))
            <div class="checklist-item">
                <strong>Recomendaciones al Cliente:</strong><br>
                {{ $checklistData['monitoreo_datos']['client_recommendations'] }}
            </div>
            @endif

            @if(isset($checklistData['monitoreo_datos']['service_photos']) && count($checklistData['monitoreo_datos']['service_photos']) > 0)
            <div class="checklist-item">
                <strong>Fotografías del Servicio:</strong>
                <div class="photo-grid">
                    @foreach($checklistData['monitoreo_datos']['service_photos'] as $photo)
                        @php
                            $imageSrc = null;
                            $photoPath = str_replace(['storage/storage/', 'storage/'], '', $photo);

                            $possiblePaths = [
                                storage_path('app/public/' . $photoPath),
                                storage_path('app/' . $photoPath),
                                public_path($photoPath),
                                public_path('storage/' . $photoPath),
                            ];

                            foreach ($possiblePaths as $fullPath) {
                                if (file_exists($fullPath)) {
                                    try {
                                        $imageData = base64_encode(file_get_contents($fullPath));
                                        $extension = pathinfo($fullPath, PATHINFO_EXTENSION);
                                        $mimeType = in_array(strtolower($extension), ['jpg', 'jpeg']) ? 'jpeg' : strtolower($extension);
                                        $imageSrc = 'data:image/' . $mimeType . ';base64,' . $imageData;
                                        break;
                                    } catch (\Exception $e) {
                                        \Log::error('Error cargando foto de servicio: ' . $fullPath);
                                    }
                                }
                            }
                        @endphp
                        @if($imageSrc)
                        <div class="photo-item">
                            <img src="{{ $imageSrc }}" alt="Foto del servicio" class="service-photo">
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        @endif

        {{-- 2. CROQUIS DE CEBADERAS --}}
        @if(isset($checklistData['monitoreo_croquis']))
        <div class="section">
            <div class="section-title">2. CROQUIS DE CEBADERAS</div>

            @if(isset($checklistData['monitoreo_croquis']['croquis_notes']))
            <div class="checklist-item">
                <strong>Notas del Croquis:</strong><br>
                {{ $checklistData['monitoreo_croquis']['croquis_notes'] }}
            </div>
            @endif

            @if(isset($checklistData['monitoreo_croquis']['croquis_file']))
            @php
                $imageSrc = null;
                $croquisPath = str_replace(['storage/storage/', 'storage/'], '', $checklistData['monitoreo_croquis']['croquis_file']);

                $possiblePaths = [
                    storage_path('app/public/' . $croquisPath),
                    storage_path('app/' . $croquisPath),
                    public_path($croquisPath),
                    public_path('storage/' . $croquisPath),
                ];

                foreach ($possiblePaths as $fullPath) {
                    if (file_exists($fullPath)) {
                        try {
                            $imageData = base64_encode(file_get_contents($fullPath));
                            $extension = pathinfo($fullPath, PATHINFO_EXTENSION);
                            $mimeType = in_array(strtolower($extension), ['jpg', 'jpeg']) ? 'jpeg' : strtolower($extension);
                            $imageSrc = 'data:image/' . $mimeType . ';base64,' . $imageData;
                            break;
                        } catch (\Exception $e) {
                            \Log::error('Error cargando croquis: ' . $fullPath);
                        }
                    }
                }
            @endphp
            @if($imageSrc)
            <div class="checklist-item">
                <strong>Croquis:</strong><br>
                <img src="{{ $imageSrc }}" alt="Croquis de cebaderas" class="croquis-image">
            </div>
            @endif
            @endif
        </div>
        @endif

        {{-- 3. MONITOREO COMPLETO --}}
        @if(isset($checklistData['monitoreo_completo']))
        <div class="section">
            <div class="section-title">3. MONITOREO COMPLETO</div>

            @if(isset($checklistData['monitoreo_completo']['monitoring_date']))
            <div class="checklist-item">
                <strong>Fecha de Monitoreo:</strong> {{ \Carbon\Carbon::parse($checklistData['monitoreo_completo']['monitoring_date'])->format('d/m/Y') }}
            </div>
            @endif

            @if(isset($checklistData['monitoreo_completo']['total_bait_stations']))
            <div class="checklist-item">
                <strong>Total de Cebaderas Instaladas:</strong> {{ $checklistData['monitoreo_completo']['total_bait_stations'] }}
            </div>
            @endif

            @if(isset($checklistData['monitoreo_completo']['bait_stations']) && count($checklistData['monitoreo_completo']['bait_stations']) > 0)
            <div class="checklist-item">
                <strong>Cebaderas Monitoreadas:</strong>
                @foreach($checklistData['monitoreo_completo']['bait_stations'] as $index => $station)
                <div class="bait-station-card">
                    <div class="bait-station-header">Cebadera #{{ $index + 1 }}</div>

                    @if(isset($station['code']))
                    <div class="bait-station-detail"><strong>Código:</strong> {{ $station['code'] }}</div>
                    @endif

                    @if(isset($station['location']))
                    <div class="bait-station-detail"><strong>Ubicación:</strong> {{ $station['location'] }}</div>
                    @endif

                    @if(isset($station['product_type']))
                    <div class="bait-station-detail"><strong>Tipo de Producto:</strong> {{ $station['product_type'] }}</div>
                    @endif

                    @if(isset($station['quantity']))
                    <div class="bait-station-detail"><strong>Cantidad:</strong> {{ $station['quantity'] }} {{ $station['unit'] ?? 'g' }}</div>
                    @endif

                    @if(isset($station['observations']) && is_array($station['observations']) && count($station['observations']) > 0)
                    <div class="bait-station-detail">
                        <strong>Observaciones:</strong>
                        <ul class="points-list">
                            @foreach($station['observations'] as $obs)
                                <li>{{ ucfirst(str_replace('_', ' ', $obs)) }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    @if(isset($station['consumption']))
                    <div class="bait-station-detail"><strong>Consumo:</strong> {{ $station['consumption'] }}%</div>
                    @endif

                    @if(isset($station['captures']))
                    <div class="bait-station-detail"><strong>Capturas:</strong> {{ $station['captures'] }}</div>
                    @endif

                    @if(isset($station['photos']) && count($station['photos']) > 0)
                    <div class="bait-station-detail">
                        <strong>Fotografías:</strong>
                        <div class="photo-grid">
                            @foreach($station['photos'] as $photo)
                                @php
                                    $imageSrc = null;
                                    $photoPath = str_replace(['storage/storage/', 'storage/'], '', $photo);

                                    $possiblePaths = [
                                        storage_path('app/public/' . $photoPath),
                                        storage_path('app/' . $photoPath),
                                        public_path($photoPath),
                                        public_path('storage/' . $photoPath),
                                    ];

                                    foreach ($possiblePaths as $fullPath) {
                                        if (file_exists($fullPath)) {
                                            try {
                                                $imageData = base64_encode(file_get_contents($fullPath));
                                                $extension = pathinfo($fullPath, PATHINFO_EXTENSION);
                                                $mimeType = in_array(strtolower($extension), ['jpg', 'jpeg']) ? 'jpeg' : strtolower($extension);
                                                $imageSrc = 'data:image/' . $mimeType . ';base64,' . $imageData;
                                                break;
                                            } catch (\Exception $e) {
                                                \Log::error('Error cargando foto de cebadera: ' . $fullPath);
                                            }
                                        }
                                    }
                                @endphp
                                @if($imageSrc)
                                <div class="photo-item">
                                    <img src="{{ $imageSrc }}" alt="Foto de cebadera {{ $station['code'] ?? ($index + 1) }}" class="bait-station-photo">
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
            @endif

            @if(isset($checklistData['monitoreo_completo']['general_observations']))
            <div class="checklist-item">
                <strong>Observaciones Generales:</strong><br>
                {{ $checklistData['monitoreo_completo']['general_observations'] }}
            </div>
            @endif

            @if(isset($checklistData['monitoreo_completo']['client_recommendations_monitoring']))
            <div class="checklist-item">
                <strong>Recomendaciones al Cliente:</strong><br>
                {{ $checklistData['monitoreo_completo']['client_recommendations_monitoring'] }}
            </div>
            @endif
        </div>
        @endif

        {{-- 4. ESTADÍSTICAS --}}
        @if(isset($checklistData['monitoreo_estadisticas']))
        <div class="section">
            <div class="section-title">4. ESTADÍSTICAS</div>

            <div class="stats-grid">
                @if(isset($checklistData['monitoreo_estadisticas']['total_monitored']))
                <div class="stat-item">
                    <div class="stat-label">Cebaderas Monitoreadas</div>
                    <div class="stat-value">{{ $checklistData['monitoreo_estadisticas']['total_monitored'] }}</div>
                </div>
                @endif

                @if(isset($checklistData['monitoreo_estadisticas']['total_active']))
                <div class="stat-item">
                    <div class="stat-label">Cebaderas Activas</div>
                    <div class="stat-value">{{ $checklistData['monitoreo_estadisticas']['total_active'] }}</div>
                </div>
                @endif

                @if(isset($checklistData['monitoreo_estadisticas']['total_problems']))
                <div class="stat-item">
                    <div class="stat-label">Con Problemas</div>
                    <div class="stat-value">{{ $checklistData['monitoreo_estadisticas']['total_problems'] }}</div>
                </div>
                @endif

                @if(isset($checklistData['monitoreo_estadisticas']['average_consumption_percent']))
                <div class="stat-item">
                    <div class="stat-label">Consumo Promedio</div>
                    <div class="stat-value">{{ number_format($checklistData['monitoreo_estadisticas']['average_consumption_percent'], 1) }}%</div>
                </div>
                @endif
            </div>

            @if(isset($checklistData['monitoreo_estadisticas']['activity_level']))
            <div class="checklist-item" style="margin-top: 15px;">
                <strong>Nivel de Actividad:</strong> {{ strtoupper($checklistData['monitoreo_estadisticas']['activity_level']) }}
            </div>
            @endif

            @if(isset($checklistData['monitoreo_estadisticas']['executive_summary']))
            <div class="checklist-item">
                <strong>Resumen Ejecutivo:</strong><br>
                {{ $checklistData['monitoreo_estadisticas']['executive_summary'] }}
            </div>
            @endif
        </div>
        @endif

        {{-- 5. ANÁLISIS IA --}}
        @if(isset($checklistData['monitoreo_analisis']))
        <div class="section">
            <div class="section-title">5. ANÁLISIS IA</div>

            @if(isset($checklistData['monitoreo_analisis']['ai_analysis_data']) && is_array($checklistData['monitoreo_analisis']['ai_analysis_data']))
            <div class="ai-analysis-box">
                @foreach($checklistData['monitoreo_analisis']['ai_analysis_data'] as $key => $value)
                    @if(is_string($value) || is_numeric($value))
                    <div class="checklist-item">
                        <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}
                    </div>
                    @endif
                @endforeach
            </div>
            @endif

            @if(isset($checklistData['monitoreo_analisis']['technician_ai_notes']))
            <div class="checklist-item">
                <strong>Notas del Técnico sobre el Análisis IA:</strong><br>
                {{ $checklistData['monitoreo_analisis']['technician_ai_notes'] }}
            </div>
            @endif

            @if(isset($checklistData['monitoreo_analisis']['ai_analysis_validated']) && $checklistData['monitoreo_analisis']['ai_analysis_validated'])
            <div class="checklist-item">
                <strong>Estado:</strong> <span class="status-badge status-finalizado">Análisis Validado</span>
            </div>
            @endif
        </div>
        @endif

        {{-- 6. FIRMA FINAL --}}
        @if(isset($checklistData['monitoreo_firma']))
        <div class="section">
            <div class="section-title">6. FIRMA FINAL</div>

            @if(isset($checklistData['monitoreo_firma']['signer_name']))
            <div class="checklist-item">
                <strong>Firmante:</strong> {{ $checklistData['monitoreo_firma']['signer_name'] }}
            </div>
            @endif

            @if(isset($checklistData['monitoreo_firma']['signer_position']))
            <div class="checklist-item">
                <strong>Cargo/Relación:</strong> {{ ucfirst($checklistData['monitoreo_firma']['signer_position']) }}
            </div>
            @endif

            @if(isset($checklistData['monitoreo_firma']['service_completed']) && $checklistData['monitoreo_firma']['service_completed'])
            <div class="checklist-item">
                <strong>Confirmación:</strong> <span class="status-badge status-finalizado">Servicio Completado</span>
            </div>
            @endif
        </div>
        @endif

    @else
        {{-- PROCESO ESTÁNDAR (NO MONITOREO CEBADERAS) --}}

        {{-- Tipo de Servicio y Hallazgos Técnicos --}}
        @if($checklistData)
            <div class="section">
                <div class="section-title">Hallazgos Técnicos - Puntos de Control</div>
                <ul class="points-list">
                    @if(isset($checklistData["points"]) && count($checklistData["points"]) > 0)
                        @php
                            // Convertir el objeto de puntos de control en un array legible
                            $pointsToDisplay = [];
                            $pointsData = $checklistData["points"];

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
                            <li>{{ $point }}</li>
                            @endforeach
                        @else
                            <li>No hay puntos de control registrados</li>
                        @endif
                    @else
                        <li>No hay puntos de control registrados</li>
                    @endif
                </ul>
            </div>

            @if(isset($checklistData["results"]) && count($checklistData["results"]) > 0)
            <div class="section">
                <div class="section-title">Hallazgos Técnicos - Resultados Observados</div>
                <div class="technical-findings">
                    <ul class="points-list">
                        @if(isset($checklistData["results"]["observed_results"]) && count($checklistData["results"]["observed_results"]) > 0)
                            @foreach($checklistData["results"]["observed_results"] as $result)
                            <li>{{ $result }}</li>
                            @endforeach
                        @else
                            <li>No hay resultados observados registrados</li>
                        @endif
                    </ul>
                </div>
            </div>
            @endif

            {{-- Insumos Utilizados (Producto + Lote) --}}
            @if(isset($checklistData["products"]["applied_product"]))
            <div class="section">
                <div class="section-title">Insumos Utilizados</div>
                <div class="product-info">
                    <strong>Producto:</strong> {{ $checklistData["products"]["applied_product"] }}
                </div>
            </div>
            @else
                <div class="section">
                    <div class="section-title">Insumos Utilizados</div>
                    <div class="product-info">No hay productos aplicados registrados</div>
                </div>
            @endif

            {{-- Observaciones con Imágenes --}}
            @if(isset($checklistData["observations"]) && count($checklistData["observations"]) > 0)
            <div class="section">
                <div class="section-title">Observaciones Detalladas con Fotografías</div>
                @foreach($checklistData["observations"] as $index => $observation)
                <div class="observation-item">
                    <div class="observation-header">
                        Observación #{{ $observation['observation_number'] ?? ($index + 1) }}
                        @if(isset($observation['cebadera_code']))
                            - CE: {{ $observation['cebadera_code'] }}
                        @endif
                    </div>
                    <div class="observation-detail">
                        <strong>Detalle:</strong> {{ $observation['detail'] ?? 'No especificado' }}
                    </div>
                    @if(isset($observation['created_at']))
                    <div class="observation-detail">
                        <strong>Fecha:</strong> {{ \Carbon\Carbon::parse($observation['created_at'])->format('d/m/Y H:i') }}
                    </div>
                    @endif
                    @if(isset($observation['photo']) && $observation['photo'])
                    @php
                        $imageSrc = null;
                        $photoPath = $observation['photo'];

                        // Normalizar la ruta removiendo múltiples prefijos si existen
                        $photoPath = str_replace(['storage/storage/', 'storage/'], '', $photoPath);

                        // Intentar varias rutas posibles
                        $possiblePaths = [
                            storage_path('app/public/' . $photoPath),
                            storage_path('app/' . $photoPath),
                            public_path($photoPath),
                            public_path('storage/' . $photoPath),
                        ];

                        foreach ($possiblePaths as $fullPath) {
                            if (file_exists($fullPath)) {
                                try {
                                    $imageData = base64_encode(file_get_contents($fullPath));
                                    $extension = pathinfo($fullPath, PATHINFO_EXTENSION);
                                    $mimeType = in_array(strtolower($extension), ['jpg', 'jpeg']) ? 'jpeg' : strtolower($extension);
                                    $imageSrc = 'data:image/' . $mimeType . ';base64,' . $imageData;
                                    break;
                                } catch (\Exception $e) {
                                    \Log::error('Error cargando imagen: ' . $fullPath . ' - ' . $e->getMessage());
                                }
                            }
                        }

                        if (!$imageSrc) {
                            \Log::warning('No se encontró la imagen en ninguna ruta: ' . $observation['photo']);
                        }
                    @endphp
                    @if($imageSrc)
                    <div class="observation-detail">
                        <strong>Fotografía:</strong><br>
                        <img src="{{ $imageSrc }}" alt="Foto de observación" class="observation-photo">
                    </div>
                    @else
                    <div class="observation-detail">
                        <strong>Fotografía:</strong> <span class="no-data">No se pudo cargar la imagen ({{ $observation['photo'] }})</span>
                    </div>
                    @endif
                    @endif
                </div>
                @endforeach
            </div>
            @else
                <div class="observation-item">No hay observaciones registradas</div>
            @endif

            {{-- Sitios Tratados --}}
            @if(isset($checklistData["sites"]["treated_sites"]) && !empty($checklistData["sites"]["treated_sites"]))
            <div class="section">
                <div class="section-title">Sitios Tratados</div>
                <div class="checklist-item">{{ $checklistData["sites"]["treated_sites"] }}</div>
            </div>
            @endif

            {{-- Descripción del Servicio --}}
            @if(isset($checklistData["description"]["content"]))
            <div class="section">
                <div class="section-title">Descripción del Servicio</div>
                <div class="checklist-item">{{ $checklistData["description"]["content"] }}</div>
            </div>
            @endif
        @endif
    @endif

    {{-- Firmas del Cliente y Técnico --}}
    <div class="signature-section">
        <div class="section-title">Firmas de Confirmación</div>

        @php
            $technicianSignature = null;
            $clientSignature = null;

            // Buscar firma del técnico en diferentes ubicaciones
            if($isMonitoreoCebaderas && isset($checklistData['monitoreo_firma']['technician_signature'])) {
                $technicianSignature = $checklistData['monitoreo_firma']['technician_signature'];
            } elseif(isset($checklistData['description']['technician_signature'])) {
                $technicianSignature = $checklistData['description']['technician_signature'];
            }

            // Buscar firma del cliente
            if(isset($checklistData['description']['client_signature'])) {
                $clientSignature = $checklistData['description']['client_signature'];
            }
        @endphp

        @if($technicianSignature)
        @php
            // Si es base64, usarlo directamente, si no, cargar desde archivo
            if (strpos($technicianSignature, 'data:image') === 0) {
                $signatureSrc = $technicianSignature;
            } else {
                $photoPath = str_replace('storage/', '', $technicianSignature);
                $fullPath = storage_path('app/public/' . $photoPath);
                if (file_exists($fullPath)) {
                    $imageData = base64_encode(file_get_contents($fullPath));
                    $signatureSrc = 'data:image/png;base64,' . $imageData;
                } else {
                    $signatureSrc = null;
                }
            }
        @endphp
        @if($signatureSrc)
        <div class="signature-box">
            <div class="signature-label">Firma del Técnico</div>
            <img src="{{ $signatureSrc }}" alt="Firma del Técnico" class="signature-image">
            <div class="signature-label">{{ $service->assignedUser->name ?? "Técnico" }}</div>
        </div>
        @endif
        @else
        <div class="signature-box">
            <div class="signature-line"></div>
            <div class="signature-label">Firma del Técnico</div>
        </div>
        @endif

        @if($clientSignature)
        @php
            if (strpos($clientSignature, 'data:image') === 0) {
                $signatureSrc = $clientSignature;
            } else {
                $photoPath = str_replace('storage/', '', $clientSignature);
                $fullPath = storage_path('app/public/' . $photoPath);
                if (file_exists($fullPath)) {
                    $imageData = base64_encode(file_get_contents($fullPath));
                    $signatureSrc = 'data:image/png;base64,' . $imageData;
                } else {
                    $signatureSrc = null;
                }
            }
        @endphp
        @if($signatureSrc)
        <div class="signature-box">
            <div class="signature-label">Firma del Cliente</div>
            <img src="{{ $signatureSrc }}" alt="Firma del Cliente" class="signature-image">
            <div class="signature-label">
                @if($isMonitoreoCebaderas && isset($checklistData['monitoreo_firma']['signer_name']))
                    {{ $checklistData['monitoreo_firma']['signer_name'] }}
                @else
                    {{ $service->client->name ?? "Cliente" }}
                @endif
            </div>
        </div>
        @endif
        @else
        <div class="signature-box">
            <div class="signature-line"></div>
            <div class="signature-label">Firma del Cliente</div>
        </div>
        @endif

        <div style="clear: both;"></div>
        <div style="margin-top: 20px; text-align: center;">
            <div class="signature-label">Fecha de Finalización: {{ $service->checklist_completed_at ? $service->checklist_completed_at->format("d/m/Y H:i") : date("d/m/Y H:i") }}</div>
        </div>
    </div>

    {{-- Información de Validación y Trazabilidad --}}
    <div class="validation-info">
        <div class="section-title">Información de Validación</div>
        <div class="info-row">
            <span class="info-label">ID de Validación:</span>
            <span class="info-value">{{ $validationId ?? 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Hash de Integridad:</span>
            <span class="info-value">{{ $integrityHash ?? 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Fecha de Generación:</span>
            <span class="info-value">{{ now()->format("d/m/Y H:i:s") }}</span>
        </div>
    </div>

    <div class="footer">
        <p>Este documento fue generado automáticamente por el sistema Pest Controller</p>
        <p>Servicio completado por: {{ $service->assignedUser->name ?? "Técnico asignado" }}</p>
        <p>Documento con trazabilidad digital - QR de validación incluido</p>
    </div>
</body>
</html>

