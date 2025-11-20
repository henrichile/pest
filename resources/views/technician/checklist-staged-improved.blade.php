@php
// Helper function para obtener la ruta correcta según el modo
function getTechnicianRoute($routeName, ...$params) {
    $isViewingAsTechnician = session('view_as_technician', false) && auth()->check() && auth()->user()->hasRole('super-admin');

    if ($isViewingAsTechnician) {
        // Mapear rutas de technician a technician-view
        $routeMap = [
            'technician.service.detail' => 'technician-view.service.detail',
            'technician.service.checklist' => 'technician-view.service.checklist',
            'technician.service.checklist.stage' => 'technician-view.service.checklist.stage',
            'technician.service.checklist.location' => 'technician-view.service.checklist.location',
            'technician.service.checklist.process-location' => 'technician-view.service.checklist.process-location',
            'technician.service.checklist.submit' => 'technician-view.service.checklist.submit',
            'technician.service.pdf' => 'technician-view.service.pdf',
            'technician.service.checklist-details' => 'technician-view.service.checklist-details',
        ];

        $mappedRoute = $routeMap[$routeName] ?? $routeName;
        return route($mappedRoute, ...$params);
    }

    return route($routeName, ...$params);
}
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checklist - {{ $service->id }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .main-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Header Card */
        .header-card {
            background: white;
            border-radius: 16px;
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .header-title {
            font-size: 28px;
            font-weight: 700;
            color: #1a472a;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header-title::before {
            content: "📋";
            font-size: 32px;
        }

        .header-subtitle {
            color: #666;
            font-size: 16px;
        }

        /* Service Info Card */
        .service-card {
            background: white;
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .service-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }

        .service-card-title {
            font-size: 20px;
            font-weight: 600;
            color: #1a472a;
        }

        .service-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .info-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .info-label {
            font-size: 12px;
            font-weight: 600;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-value {
            font-size: 16px;
            font-weight: 500;
            color: #333;
        }

        .priority-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .priority-alta {
            background: #fee;
            color: #c33;
        }

        .priority-media {
            background: #fff4e6;
            color: #d97706;
        }

        .priority-baja {
            background: #e6f7ff;
            color: #1890ff;
        }

        .location-section {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid #f0f0f0;
        }

        .location-status {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .location-icon {
            font-size: 20px;
        }

        .location-text {
            font-size: 14px;
            color: #666;
        }

        .location-success {
            color: #28a745;
        }

        .location-error {
            color: #dc3545;
        }

        .geolocation-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .geolocation-btn:hover {
            background: #0056b3;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,123,255,0.3);
        }

        /* Progress Card */
        .progress-card {
            background: white;
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .progress-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .progress-title {
            font-size: 18px;
            font-weight: 600;
            color: #1a472a;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .progress-percentage {
            font-size: 24px;
            font-weight: 700;
            color: #1a472a;
        }

        .progress-bar-container {
            position: relative;
            height: 12px;
            background: #e9ecef;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 10px;
        }

        .progress-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, #1a472a 0%, #28a745 100%);
            border-radius: 10px;
            transition: width 0.5s ease;
            box-shadow: 0 2px 8px rgba(26,71,42,0.3);
        }

        .progress-text {
            font-size: 14px;
            color: #666;
            text-align: center;
        }

        /* Stage Navigation */
        .stages-container {
            background: white;
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .stages-list {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
        }

        .stage-item {
            flex: 1;
            min-width: 120px;
            text-align: center;
            padding: 15px 10px;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            background: #f8f9fa;
            border: 2px solid #e9ecef;
        }

        .stage-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .stage-item.completed {
            background: #d4edda;
            border-color: #28a745;
            color: #155724;
        }

        .stage-item.completed::before {
            content: "✓";
            position: absolute;
            top: 5px;
            right: 5px;
            font-size: 16px;
            color: #28a745;
        }

        .stage-item.active {
            background: linear-gradient(135deg, #1a472a 0%, #28a745 100%);
            border-color: #1a472a;
            color: white;
            box-shadow: 0 4px 15px rgba(26,71,42,0.4);
            transform: translateY(-2px);
        }

        .stage-item.pending {
            background: #f8f9fa;
            border-color: #dee2e6;
            color: #6c757d;
        }

        .stage-number {
            display: block;
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stage-name {
            display: block;
            font-size: 12px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Form Container */
        .form-container {
            background: white;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .form-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }

        .form-icon {
            font-size: 28px;
        }

        .form-title {
            font-size: 24px;
            font-weight: 700;
            color: #1a472a;
        }

        /* Navigation Buttons */
        .nav-buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .nav-btn {
            flex: 1;
            padding: 15px 25px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
        }

        .nav-btn-primary {
            background: linear-gradient(135deg, #1a472a 0%, #28a745 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(26,71,42,0.3);
        }

        .nav-btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(26,71,42,0.4);
        }

        .nav-btn-secondary {
            background: #f8f9fa;
            color: #495057;
            border: 2px solid #dee2e6;
        }

        .nav-btn-secondary:hover {
            background: #e9ecef;
            transform: translateY(-2px);
        }

        .nav-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .service-info-grid {
                grid-template-columns: 1fr;
            }

            .stages-list {
                flex-direction: column;
            }

            .stage-item {
                min-width: 100%;
            }

            .nav-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <!-- Header -->
        <div class="header-card">
            <h1 class="header-title">Checklist de Servicio</h1>
            <p class="header-subtitle">Complete todas las etapas para finalizar el servicio</p>
        </div>

        <!-- Service Info -->
        <div class="service-card">
            <div class="service-card-header">
                <h2 class="service-card-title">📦 Información del Servicio</h2>
            </div>
            <div class="service-info-grid">
                <div class="info-item">
                    <span class="info-label">Cliente</span>
                    <span class="info-value">{{ $service->client->name ?? "N/A" }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Tipo de Servicio</span>
                    <span class="info-value">{{ ucfirst(str_replace('-', ' ', $service->service_type)) }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Dirección</span>
                    <span class="info-value">{{ $service->address ?? "N/A" }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Prioridad</span>
                    <span class="info-value">
                        <span class="priority-badge priority-{{ strtolower($service->priority ?? 'media') }}">
                            {{ ucfirst($service->priority ?? 'Media') }}
                        </span>
                    </span>
                </div>
            </div>
            <div class="location-section">
                <div class="location-status">
                    <span class="location-icon">📍</span>
                    <span class="location-text {{ $service->latitude && $service->longitude ? 'location-success' : 'location-error' }}">
                        @if($service->latitude && $service->longitude)
                            Ubicación capturada ({{ number_format($service->latitude, 6) }}, {{ number_format($service->longitude, 6) }})
                        @else
                            Ubicación no capturada
                        @endif
                    </span>
                </div>
                @if(!$service->latitude || !$service->longitude)
                <a href="{{ getTechnicianRoute('technician.service.checklist.location', $service) }}" class="geolocation-btn">
                    <span>🔄</span>
                    <span>Capturar Ubicación</span>
                </a>
                @else
                <a href="{{ getTechnicianRoute('technician.service.checklist.location', $service) }}" class="geolocation-btn" style="background: #6c757d;">
                    <span>🔄</span>
                    <span>Reconectar Ubicación</span>
                </a>
                @endif
            </div>
        </div>

        <!-- Progress -->
        <div class="progress-card">
            <div class="progress-header">
                <h3 class="progress-title">
                    <span>📊</span>
                    <span>Progreso del Checklist</span>
                </h3>
                <span class="progress-percentage">{{ number_format($service->getProgressPercentage(), 0) }}%</span>
            </div>
            <div class="progress-bar-container">
                <div class="progress-bar-fill" style="width: {{ $service->getProgressPercentage() }}%"></div>
            </div>
            <p class="progress-text">Etapa {{ $service->getStageNumber() }} de {{ $service->getTotalStage() }}</p>
        </div>

        <!-- Stages Navigation -->
        <div class="stages-container">
            <div class="stages-list">
                @if($service->service_type === 'monitoreo-cebaderas')
                    @php
                        $stages = [
                            ['num' => 1, 'name' => 'Datos', 'stage' => 'monitoreo-datos'],
                            ['num' => 2, 'name' => 'Croquis', 'stage' => 'monitoreo-croquis'],
                            ['num' => 3, 'name' => 'Monitoreo', 'stage' => 'monitoreo-completo'],
                            ['num' => 4, 'name' => 'Estadísticas', 'stage' => 'monitoreo-estadisticas'],
                            ['num' => 5, 'name' => 'Análisis IA', 'stage' => 'monitoreo-analisis'],
                            ['num' => 6, 'name' => 'Firma', 'stage' => 'monitoreo-firma'],
                        ];
                        $currentIndex = array_search($service->checklist_stage, array_column($stages, 'stage'));
                    @endphp
                    @foreach($stages as $index => $stageInfo)
                        @php
                            $isActive = $service->checklist_stage === $stageInfo['stage'];
                            $isCompleted = $currentIndex !== false && $index < $currentIndex;
                            $isPending = $currentIndex !== false && $index > $currentIndex;
                            $class = $isActive ? 'active' : ($isCompleted ? 'completed' : 'pending');
                        @endphp
                        <div class="stage-item {{ $class }}">
                            <span class="stage-number">{{ $stageInfo['num'] }}</span>
                            <span class="stage-name">{{ $stageInfo['name'] }}</span>
                        </div>
                    @endforeach
                @else
                    <!-- Stages para otros tipos de servicio -->
                @endif
            </div>
        </div>

        <!-- Form Content -->
        <div class="form-container">
            <div class="form-header">
                <span class="form-icon">🎯</span>
                <h2 class="form-title">
                    @if($service->service_type === 'monitoreo-cebaderas')
                        @if($service->checklist_stage === 'monitoreo-datos') Datos del Servicio
                        @elseif($service->checklist_stage === 'monitoreo-croquis') Croquis de Cebaderas
                        @elseif($service->checklist_stage === 'monitoreo-completo') Monitoreo Completo
                        @elseif($service->checklist_stage === 'monitoreo-estadisticas') Estadísticas
                        @elseif($service->checklist_stage === 'monitoreo-analisis') Análisis IA
                        @elseif($service->checklist_stage === 'monitoreo-firma') Firma Final
                        @endif
                    @else
                        {{ ucfirst($service->checklist_stage ?? 'points') }}
                    @endif
                </h2>
            </div>

            @if($service->service_type === 'monitoreo-cebaderas')
                @if($service->checklist_stage === "monitoreo-datos")
                    @include("technician.checklist-stages.monitoreo-datos")
                @elseif($service->checklist_stage === "monitoreo-croquis")
                    @include("technician.checklist-stages.monitoreo-croquis")
                @elseif($service->checklist_stage === "monitoreo-completo")
                    @include("technician.checklist-stages.monitoreo-completo")
                @elseif($service->checklist_stage === "monitoreo-estadisticas")
                    @include("technician.checklist-stages.monitoreo-estadisticas")
                @elseif($service->checklist_stage === "monitoreo-analisis")
                    @include("technician.checklist-stages.monitoreo-analisis")
                @elseif($service->checklist_stage === "monitoreo-firma")
                    @include("technician.checklist-stages.monitoreo-firma")
                @endif
            @else
                @if(($service->checklist_stage ?? "points") === "points")
                    @include("technician.checklist-stages.points")
                @elseif($service->checklist_stage === "products")
                    @include("technician.checklist-stages.products", ['products' => $products ?? collect(), 'stageInstruction' => $stageInstruction ?? ''])
                @elseif($service->checklist_stage === "results")
                    @include("technician.checklist-stages.results")
                @elseif($service->checklist_stage === "observations")
                    @include("technician.checklist-stages.observations")
                @elseif($service->checklist_stage === "sites")
                    @include("technician.checklist-stages.sites")
                @elseif($service->checklist_stage === "description")
                    @include("technician.checklist-stages.description")
                @endif
            @endif
        </div>

        <!-- Navigation Buttons -->
        <div class="nav-buttons">
            @if($previousStage)
                <a href="{{ getTechnicianRoute('technician.service.checklist.stage', ['service' => $service, 'stage' => $previousStage]) }}"
                   class="nav-btn nav-btn-secondary">
                    <span>←</span>
                    <span>Etapa Anterior</span>
                </a>
            @else
                <a href="{{ getTechnicianRoute('technician.service.detail', $service) }}"
                   class="nav-btn nav-btn-secondary">
                    <span>←</span>
                    <span>Volver al Servicio</span>
                </a>
            @endif

            @if($nextStage)
                <button type="submit" form="monitoreoCompletoForm" class="nav-btn nav-btn-primary">
                    <span>Siguiente Etapa</span>
                    <span>→</span>
                </button>
            @else
                <button type="button" class="nav-btn nav-btn-primary" disabled>
                    <span>✓</span>
                    <span>Última Etapa</span>
                </button>
            @endif
        </div>
    </div>
</body>
</html>


