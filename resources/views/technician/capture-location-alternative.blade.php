<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Servicio - {{ $service->id }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: #000000;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            max-width: 500px;
            width: 100%;
            text-align: center;
        }
        
        .logo {
            width: 80px;
            height: 80px;
            background: #1a472a;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: white;
            font-size: 24px;
            font-weight: bold;
        }
        
        h1 {
            color: #1a472a;
            margin-bottom: 10px;
            font-size: 28px;
        }
        
        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 16px;
        }
        
        .service-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: left;
        }
        
        .service-info h3 {
            color: #1a472a;
            margin-bottom: 10px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        
        .info-label {
            font-weight: 600;
            color: #333;
        }
        
        .info-value {
            color: #666;
        }
        
        .status-box {
            background: #e3f2fd;
            border: 2px solid #2196f3;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: left;
        }
        
        .status-box.success {
            background: #e8f5e8;
            border-color: #4caf50;
        }
        
        .status-box.error {
            background: #ffebee;
            border-color: #f44336;
        }
        
        .status-box h4 {
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }
        
        .status-box p {
            margin-bottom: 10px;
        }
        
        .start-button {
            background: #1a472a;
            color: white;
            border: none;
            padding: 20px 40px;
            border-radius: 50px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            margin-bottom: 20px;
        }
        
        .start-button:hover {
            background: #2d5016;
            transform: translateY(-2px);
        }
        
        .start-button:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }
        
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #1a472a;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 10px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .retry-button {
            background: #ff9800;
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            margin-bottom: 10px;
        }
        
        .retry-button:hover {
            background: #f57c00;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">PC</div>
        <h1>Iniciar Servicio</h1>
        <p class="subtitle">Capturando ubicación automáticamente...</p>
        
        <div class="service-info">
            <h3>Detalles del Servicio</h3>
            <div class="info-row">
                <span class="info-label">Cliente:</span>
                <span class="info-value">{{ $service->client->name ?? "N/A" }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Tipo:</span>
                <span class="info-value">{{ ucfirst($service->service_type ?? "Desratización") }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Dirección:</span>
                <span class="info-value">{{ $service->address ?? "N/A" }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Prioridad:</span>
                <span class="info-value">{{ ucfirst($service->priority ?? "Media") }}</span>
            </div>
        </div>
        
        <div id="statusBox" class="status-box">
            <h4><span class="loading-spinner"></span>Obteniendo ubicación...</h4>
            <p>Por favor, permite el acceso a tu ubicación cuando el navegador lo solicite.</p>
        </div>
        
        <form id="locationForm" method="POST" action="{{ route("technician.service.checklist.process-location", $service) }}" style="display: none;">
            @csrf
            <input type="hidden" id="latitude" name="latitude" required>
            <input type="hidden" id="longitude" name="longitude" required>
            <input type="hidden" id="location_accuracy" name="location_accuracy" value="10">
            
            <button type="submit" class="start-button">
                🚀 Iniciar Servicio
            </button>
        </form>
        
        <button id="retryBtn" class="retry-button" style="display: none;">
            🔄 Intentar Nuevamente
        </button>
    </div>

    <script>
        const statusBox = document.getElementById("statusBox");
        const locationForm = document.getElementById("locationForm");
        const retryBtn = document.getElementById("retryBtn");
        
        const latitudeInput = document.getElementById("latitude");
        const longitudeInput = document.getElementById("longitude");
        const accuracyInput = document.getElementById("location_accuracy");

        function updateStatus(message, type = 'loading') {
            statusBox.className = `status-box ${type}`;
            
            if (type === 'loading') {
                statusBox.innerHTML = `
                    <h4><span class="loading-spinner"></span>${message}</h4>
                    <p>Por favor, permite el acceso a tu ubicación cuando el navegador lo solicite.</p>
                `;
            } else if (type === 'success') {
                statusBox.innerHTML = `
                    <h4>✅ ${message}</h4>
                    <p>Ubicación capturada correctamente. Puedes iniciar el servicio.</p>
                `;
            } else if (type === 'error') {
                statusBox.innerHTML = `
                    <h4>❌ ${message}</h4>
                    <p>No se pudo obtener la ubicación automáticamente.</p>
                `;
            }
        }

        function showLocationForm() {
            locationForm.style.display = 'block';
        }

        function showRetryButton() {
            retryBtn.style.display = 'block';
        }

        function getLocation() {
            if (!navigator.geolocation) {
                updateStatus("Tu navegador no soporta geolocalización", 'error');
                showRetryButton();
                return;
            }

            updateStatus("Obteniendo ubicación...", 'loading');

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    const accuracy = position.coords.accuracy;

                    latitudeInput.value = lat;
                    longitudeInput.value = lng;
                    accuracyInput.value = Math.round(accuracy);

                    updateStatus("¡Ubicación capturada exitosamente!", 'success');
                    showLocationForm();
                },
                function(error) {
                    let message = "Error al obtener ubicación: ";
                    switch(error.code) {
                        case 1:
                            message = "Permiso denegado por el usuario";
                            break;
                        case 2:
                            message = "Ubicación no disponible";
                            break;
                        case 3:
                            message = "Tiempo agotado";
                            break;
                        default:
                            message = "Error desconocido";
                            break;
                    }
                    
                    updateStatus(message, 'error');
                    showRetryButton();
                },
                {
                    enableHighAccuracy: true,
                    timeout: 15000,
                    maximumAge: 0
                }
            );
        }

        // Intentar captura automática al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            // Pequeño delay para que el usuario vea la interfaz
            setTimeout(getLocation, 1000);
        });

        // Event listener para reintentar
        retryBtn.addEventListener('click', function() {
            retryBtn.style.display = 'none';
            locationForm.style.display = 'none';
            getLocation();
        });
    </script>
</body>
</html>
