<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Captura de Ubicación - Servicio #{{ $service->id }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #000;
            color: #fff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .header {
            background: #1a1a1a;
            padding: 1rem;
            text-align: center;
            border-bottom: 1px solid #333;
        }

        .header h1 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .header p {
            color: #888;
            font-size: 0.9rem;
        }

        .container {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            max-width: 500px;
            margin: 0 auto;
        }

        .status-card {
            background: #1a1a1a;
            border: 1px solid #333;
            border-radius: 12px;
            padding: 2rem;
            text-align: center;
            margin-bottom: 2rem;
            width: 100%;
        }

        .status-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .status-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .status-message {
            color: #888;
            font-size: 0.9rem;
            line-height: 1.4;
        }

        .loading {
            color: #4CAF50;
        }

        .success {
            color: #4CAF50;
        }

        .error {
            color: #f44336;
        }

        .service-info {
            background: #1a1a1a;
            border: 1px solid #333;
            border-radius: 12px;
            padding: 1.5rem;
            width: 100%;
            margin-bottom: 2rem;
        }

        .service-info h3 {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #4CAF50;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .info-label {
            color: #888;
        }

        .info-value {
            color: #fff;
            font-weight: 500;
        }

        .actions {
            display: flex;
            gap: 1rem;
            width: 100%;
        }

        .btn {
            flex: 1;
            padding: 1rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            text-align: center;
            display: inline-block;
        }

        .btn-primary {
            background: #4CAF50;
            color: white;
        }

        .btn-primary:hover {
            background: #45a049;
        }

        .btn-secondary {
            background: #333;
            color: #fff;
            border: 1px solid #555;
        }

        .btn-secondary:hover {
            background: #444;
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .hidden {
            display: none;
        }

        @media (max-width: 480px) {
            .container {
                padding: 1rem;
            }

            .status-card,
            .service-info {
                padding: 1rem;
            }

            .actions {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>📍 Captura de Ubicación</h1>
        <p>Servicio #{{ $service->id }} - {{ $service->client->name ?? 'Cliente' }}</p>
    </div>

    <div class="container">
        <!-- Estado de carga -->
        <div id="loading-card" class="status-card">
            <div class="status-icon loading">🔄</div>
            <div class="status-title">Capturando ubicación...</div>
            <div class="status-message">Por favor espera mientras obtenemos tu ubicación actual</div>
        </div>

        <!-- Estado de éxito -->
        <div id="success-card" class="status-card hidden">
            <div class="status-icon success">✅</div>
            <div class="status-title">Ubicación capturada</div>
            <div class="status-message">Tu ubicación ha sido registrada correctamente</div>
        </div>

        <!-- Estado de error -->
        <div id="error-card" class="status-card hidden">
            <div class="actions" style="margin-top:1rem">
                <button type="button" class="btn btn-primary" onclick="retryGeolocation()">Reintentar
                    geolocalización</button>
            </div>
            <div class="status-icon error">❌</div>
            <div class="status-title">Error al obtener ubicación</div>
            <div class="status-message">No se pudo obtener tu ubicación. Por favor verifica que tengas habilitado el GPS
                y los permisos de ubicación.</div>
        </div>

        <!-- Información del servicio -->
        <div class="service-info">
            <h3>📋 Detalles del Servicio</h3>
            <div class="info-row">
                <span class="info-label">Cliente:</span>
                <span class="info-value">{{ $service->client->name ?? 'No especificado' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Tipo:</span>
                <span class="info-value">{{ $service->serviceType->name ?? 'No especificado' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Dirección:</span>
                <span class="info-value">{{ $service->address ?? 'No especificada' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Fecha programada:</span>
                <span
                    class="info-value">{{ $service->scheduled_date ? \Carbon\Carbon::parse($service->scheduled_date)->format('d/m/Y H:i') : 'No especificada' }}</span>
            </div>
        </div>

        <!-- Acciones -->
        <div class="actions">
            <button id="continue-btn" class="btn btn-primary hidden" onclick="continueToChecklist()">
                Continuar
            </button>
            <a href="{{ route('technician.services') }}" class="btn btn-secondary">
                Volver a Servicios
            </a>
        </div>
    </div>

    <script>
        let capturedLocation = null;

        // Función para obtener la ubicación
        function getCurrentLocation() {
            if (!navigator.geolocation) {
                showError('Tu navegador no soporta geolocalización');
                return;
            }

            const options = {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            };

            navigator.geolocation.getCurrentPosition(
                function (position) {
                    capturedLocation = {
                        latitude: position.coords.latitude,
                        longitude: position.coords.longitude,
                        accuracy: position.coords.accuracy
                    };
                    showSuccess();
                },
                function (error) {
                    let errorMessage = 'Error al obtener la ubicación';
                    switch (error.code) {
                        case error.PERMISSION_DENIED:
                            errorMessage = 'Permisos de ubicación denegados';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            errorMessage = 'Ubicación no disponible';
                            break;
                        case error.TIMEOUT:
                            errorMessage = 'Tiempo de espera agotado';
                            break;
                    }
                    showError(errorMessage);
                },
                options
            );
        }

        // Mostrar estado de éxito
        function showSuccess() {
            document.getElementById('loading-card').classList.add('hidden');
            document.getElementById('success-card').classList.remove('hidden');
            document.getElementById('continue-btn').classList.remove('hidden');
        }

        // Mostrar estado de error
        function showError(message) {

            function retryGeolocation() {
                document.getElementById("error-card").classList.add("hidden");
                document.getElementById("loading-card").classList.remove("hidden");
                setTimeout(getCurrentLocation, 200);
            }

            document.getElementById('loading-card').classList.add('hidden');
            document.getElementById('error-card').classList.remove('hidden');
            document.getElementById('error-card').querySelector('.status-message').textContent = message;
        }

        // Continuar al checklist
        function continueToChecklist() {
            if (!capturedLocation) {
                alert('No se ha capturado la ubicación');
                return;
            }

            // Crear formulario para enviar la ubicación
            const form = document.createElement('form');
            form.method = 'POST';
            @php
                $isViewingAsTechnician = (session('view_as_technician', false) && auth()->check() && auth()->user()->hasRole('super-admin'))
                    || request()->is('admin/technician-view/*')
                    || (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], '/admin/technician-view/') !== false);
                $processLocationRoute = $isViewingAsTechnician ? route('admin.technician-view.service.checklist.process-location', $service) : route('technician.service.checklist.process-location', $service);
            @endphp
            form.action = '{{ $processLocationRoute }}';

            // Agregar token CSRF
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);

            // Agregar coordenadas
            const latInput = document.createElement('input');
            latInput.type = 'hidden';
            latInput.name = 'latitude';
            latInput.value = capturedLocation.latitude;
            form.appendChild(latInput);

            const lngInput = document.createElement('input');
            lngInput.type = 'hidden';
            lngInput.name = 'longitude';
            lngInput.value = capturedLocation.longitude;
            form.appendChild(lngInput);

            // Agregar dirección (usar la del servicio)
            const addressInput = document.createElement('input');
            addressInput.type = 'hidden';
            addressInput.name = 'address';
            addressInput.value = '{{ $service->address ?? "Ubicación capturada" }}';
            form.appendChild(addressInput);

            // Enviar formulario
            document.body.appendChild(form);
            form.submit();
        }

        // Iniciar captura automáticamente al cargar la página  
        document.addEventListener('DOMContentLoaded', function() {
            getCurrentLocation();
        });
            < /s
cript> 
 </body></html>
