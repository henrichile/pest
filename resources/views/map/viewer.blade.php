<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visor de Mapas - Servicios</title>
    <link href='https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.css' rel='stylesheet' />
    <style>
        body {
            margin: 0;
            padding: 20px;
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #eee;
        }
        .map-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        .map-section {
            border: 1px solid #ddd;
            border-radius: 6px;
            overflow: hidden;
        }
        .map-header {
            background: #333;
            color: white;
            padding: 10px 15px;
            font-weight: bold;
        }
        .map-content {
            padding: 15px;
        }
        #mapbox-map {
            width: 100%;
            height: 300px;
            border-radius: 4px;
        }
        .static-map {
            text-align: center;
        }
        .static-map img {
            max-width: 100%;
            height: auto;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        .service-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            margin-top: 20px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        .info-item {
            background: white;
            padding: 10px;
            border-radius: 4px;
            border-left: 4px solid #007bff;
        }
        .info-label {
            font-weight: bold;
            color: #666;
            font-size: 0.9em;
        }
        .info-value {
            color: #333;
            margin-top: 5px;
        }
        .buttons {
            text-align: center;
            margin-top: 20px;
        }
        .btn {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            margin: 0 5px;
            text-decoration: none;
            display: inline-block;
        }
        .btn:hover {
            background: #0056b3;
        }
        .btn-secondary {
            background: #6c757d;
        }
        .btn-secondary:hover {
            background: #545b62;
        }
        .coordinates {
            font-family: monospace;
            background: #f1f1f1;
            padding: 5px 8px;
            border-radius: 3px;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🗺️ Visor de Mapas - Servicios</h1>
            <p>Visualización de ubicaciones de servicios con Mapbox</p>
        </div>

        @if($service && $service->hasCoordinates())
            <div class="map-container">
                <!-- Mapa Interactivo -->
                <div class="map-section">
                    <div class="map-header">
                        📍 Mapa Interactivo
                    </div>
                    <div class="map-content">
                        <div id="mapbox-map"></div>
                        <p style="margin-top: 10px; font-size: 0.9em; color: #666;">
                            Mapa interactivo con controles de zoom y navegación
                        </p>
                    </div>
                </div>

                <!-- Mapa Estático -->
                <div class="map-section">
                    <div class="map-header">
                        🖼️ Mapa Estático
                    </div>
                    <div class="map-content">
                        <div class="static-map">
                            @php
                                $mapUrl = $service->generateMapImage(500, 300, 15);
                            @endphp
                            
                            @if($mapUrl)
                                <img src="{{ $mapUrl }}" alt="Mapa del servicio" />
                                <p style="margin-top: 10px; font-size: 0.9em; color: #666;">
                                    Imagen estática para PDFs y reportes
                                </p>
                            @else
                                <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 4px;">
                                    ❌ No se pudo generar el mapa estático
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información del Servicio -->
            <div class="service-info">
                <h3>📋 Información del Servicio</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">ID del Servicio</div>
                        <div class="info-value">#{{ $service->id }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Coordenadas</div>
                        <div class="info-value">
                            <span class="coordinates">{{ $service->getCoordinatesString() }}</span>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Latitud</div>
                        <div class="info-value">{{ $service->latitude }}°</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Longitud</div>
                        <div class="info-value">{{ $service->longitude }}°</div>
                    </div>
                    @if($service->location_accuracy)
                        <div class="info-item">
                            <div class="info-label">Precisión</div>
                            <div class="info-value">{{ $service->location_accuracy }}m</div>
                        </div>
                    @endif
                    @if($service->location_captured_at)
                        <div class="info-item">
                            <div class="info-label">Capturado</div>
                            <div class="info-value">{{ $service->location_captured_at->format('d/m/Y H:i') }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="buttons">
                <a href="{{ route('services.show', $service) }}" class="btn">
                    👁️ Ver Servicio
                </a>
                <a href="{{ route('services.pdf', $service) }}" class="btn btn-secondary">
                    📄 Generar PDF
                </a>
                <button onclick="regenerateMap()" class="btn btn-secondary">
                    🔄 Regenerar Mapa
                </button>
            </div>

        @else
            <div style="text-align: center; padding: 40px; background: #f8d7da; color: #721c24; border-radius: 6px;">
                <h3>❌ Sin Coordenadas</h3>
                <p>Este servicio no tiene coordenadas GPS disponibles.</p>
                <a href="{{ route('services.capture-location', $service ?? 0) }}" class="btn">
                    📍 Capturar Ubicación
                </a>
            </div>
        @endif
    </div>

    <!-- Scripts -->
    <script src='https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.js'></script>
    <script>
        // Configuración de Mapbox
        mapboxgl.accessToken = '{{ env('MAPBOX_ACCESS_TOKEN') }}';

        @if($service && $service->hasCoordinates())
            // Inicializar mapa interactivo
            const map = new mapboxgl.Map({
                container: 'mapbox-map',
                style: 'mapbox://styles/mapbox/streets-v11',
                center: [{{ $service->longitude }}, {{ $service->latitude }}],
                zoom: 15
            });

            // Agregar marcador
            new mapboxgl.Marker({
                color: '#FF0000'
            })
            .setLngLat([{{ $service->longitude }}, {{ $service->latitude }}])
            .setPopup(new mapboxgl.Popup({ offset: 25 })
                .setHTML(`
                    <div style="text-align: center;">
                        <h4>📍 Servicio #{{ $service->id }}</h4>
                        <p><strong>Coordenadas:</strong><br>
                        {{ $service->getCoordinatesString() }}</p>
                        @if($service->location_accuracy)
                            <p><strong>Precisión:</strong> {{ $service->location_accuracy }}m</p>
                        @endif
                    </div>
                `))
            .addTo(map);

            // Agregar controles
            map.addControl(new mapboxgl.NavigationControl());
            map.addControl(new mapboxgl.FullscreenControl());

            // Función para regenerar mapa
            function regenerateMap() {
                if (confirm('¿Regenerar el mapa estático?')) {
                    window.location.reload();
                }
            }
        @else
            function regenerateMap() {
                alert('No hay coordenadas disponibles para generar el mapa');
            }
        @endif
    </script>
</body>
</html>
