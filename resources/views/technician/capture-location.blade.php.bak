<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Capturar Ubicación - Servicio {{ $service->id }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        
        .location-button {
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
        
        .location-button:hover {
            background: #2d5016;
            transform: translateY(-2px);
        }
        
        .location-button:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }
        
        .status {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .status.loading {
            background: #e3f2fd;
            color: #1976d2;
            border: 2px solid #1976d2;
        }
        
        .status.success {
            background: #e8f5e8;
            color: #2e7d32;
            border: 2px solid #2e7d32;
        }
        
        .status.error {
            background: #ffebee;
            color: #c62828;
            border: 2px solid #c62828;
        }
        
        .coordinates {
            background: #f0f0f0;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-family: monospace;
            font-size: 14px;
        }
        
        .continue-button {
            background: #4caf50;
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
        }
        
        .continue-button:hover {
            background: #45a049;
        }
        
        .continue-button:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        
        .spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #1a472a;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 10px;
        }
        
        
        .status.info {
            background: #e3f2fd;
            color: #1976d2;
            border: 2px solid #1976d2;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">PC</div>
        <h1>Iniciar Servicio</h1>
        <p class="subtitle">Necesitamos capturar tu ubicación para comenzar</p>
        
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
        
        <div id="status" class="status hidden"></div>
        
        <button id="locationBtn" class="location-button">
            📍 Capturar Mi Ubicación
        </button>
        
        <div id="coordinates" class="coordinates hidden"></div>
        
        <form id="locationForm" method="POST" action="{{ route("technician.service.checklist.process-location", $service) }}" class="hidden">
            @csrf
            <input type="hidden" name="latitude" id="latitude">
            <input type="hidden" name="longitude" id="longitude">
            <input type="hidden" name="location_accuracy" id="location_accuracy">
            <input type="hidden" name="address" value="{{ $service->address ?? 'Ubicación capturada' }}">
            <button type="submit" id="continueBtn" class="continue-button" disabled>
                ✅ Continuar al Checklist
            </button>
        </form>
    </div>

    
    
    <script>
        const locationBtn = document.getElementById("locationBtn");
        const statusDiv = document.getElementById("status");
        const coordinatesDiv = document.getElementById("coordinates");
        const locationForm = document.getElementById("locationForm");
        const continueBtn = document.getElementById("continueBtn");
        const latitudeInput = document.getElementById("latitude");
        const longitudeInput = document.getElementById("longitude");
        const accuracyInput = document.getElementById("location_accuracy");

        // Mostrar instrucciones al cargar la página
        window.addEventListener("load", function() {
            showStatus("info", "ℹ️ <strong>Instrucciones importantes:</strong><br>1. Asegúrate de estar en HTTPS o localhost<br>2. Haz clic en el botón para solicitar permisos<br>3. Si no aparece popup, revisa la barra de direcciones");
        });

        locationBtn.addEventListener("click", function() {
            if (!navigator.geolocation) {
                showStatus("error", "❌ Tu navegador no soporta geolocalización. Usa Chrome, Firefox o Safari actualizado.");
                return;
            }

            // Verificar si estamos en HTTPS o localhost
            if (location.protocol !== "https:" && location.hostname !== "localhost" && location.hostname !== "127.0.0.1") {
                showStatus("error", "❌ <strong>Problema de seguridad:</strong><br>La geolocalización solo funciona en:<br>• Sitios HTTPS (https://)<br>• localhost<br><br>💡 <strong>Solución:</strong> Contacta al administrador para habilitar HTTPS");
                return;
            }

            showStatus("loading", "🔄 <strong>Solicitando permisos de ubicación...</strong><br>Si no aparece un popup, revisa la barra de direcciones");
            locationBtn.disabled = true;
            locationBtn.innerHTML = "<div class=\"spinner\"></div>Solicitando permisos...";

            // Configuración más agresiva para obtener permisos
          // Mostrar mensaje de solicitud de permisos
            coordinatesDiv.innerHTML = "<div style="background: #e3f2fd; padding: 15px; border-radius: 8px; margin: 10px 0; border-left: 4px solid #2196f3;"><strong>📍 Solicitando permisos de ubicación...</strong><br>Por favor, permite el acceso a tu ubicación cuando el navegador lo solicite.</div>";
            coordinatesDiv.classList.remove("hidden");
            const options = {
                enableHighAccuracy: false,
                timeout: 10000,
                maximumAge: 60000
            };

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    const accuracy = position.coords.accuracy;

                    // Guardar coordenadas
                    latitudeInput.value = lat;
                    longitudeInput.value = lng;
                    accuracyInput.value = accuracy;

                    // Mostrar coordenadas
                    coordinatesDiv.innerHTML = `
                        <strong>📍 Ubicación capturada exitosamente:</strong><br>
                        Latitud: ${lat.toFixed(6)}<br>
                        Longitud: ${lng.toFixed(6)}<br>
                        Precisión: ${Math.round(accuracy)} metros<br>
                        <small>✅ Coordenadas guardadas correctamente</small>
                    `;
                    coordinatesDiv.classList.remove("hidden");

                    showStatus("success", "✅ <strong>¡Ubicación capturada!</strong><br>Ahora puedes continuar al checklist");
                    
                    // Mostrar formulario y habilitar botón
                    locationForm.classList.remove("hidden");
                    continueBtn.disabled = false;
                    
                    // Restaurar botón
                    locationBtn.disabled = false;
                    locationBtn.innerHTML = "✅ Ubicación Capturada";
                    locationBtn.style.background = "#4caf50";
                },
                function(error) {
                    let message = "❌ <strong>Error al obtener ubicación:</strong><br>";
                    let instructions = "";
                    
                    switch(error.code) {
                        case 1:
                            message += "Permiso denegado por el usuario.";
                            instructions = `
                                <div style="background: #fff3cd; padding: 15px; border-radius: 8px; margin-top: 10px; border-left: 4px solid #ffc107;">
                                    <strong>🔧 Soluciones paso a paso:</strong><br><br>
                                    <strong>Opción 1 - Barra de direcciones:</strong><br>
                                    1. Busca el ícono de ubicación (📍) en la barra de direcciones<br>
                                    2. Haz clic en él y selecciona "Permitir"<br><br>
                                    <strong>Opción 2 - Configuración del navegador:</strong><br>
                                    1. Ve a Configuración del navegador<br>
                                    2. Busca "Privacidad y seguridad"<br>
                                    3. En "Permisos de sitio", busca esta página<br>
                                    4. Cambia "Ubicación" a "Permitir"<br><br>
                                    <strong>Opción 3 - Recargar página:</strong><br>
                                    1. Recarga la página (F5)<br>
                                    2. Vuelve a intentar<br>
                                </div>
                            `;
                            break;
                        case 2:
                            message += "Ubicación no disponible.";
                            instructions = `
                                <div style="background: #f8d7da; padding: 15px; border-radius: 8px; margin-top: 10px; border-left: 4px solid #dc3545;">
                                    <strong>🔧 Soluciones:</strong><br><br>
                                    1. Verifica que el GPS esté activado en tu dispositivo<br>
                                    2. Asegúrate de tener señal de GPS<br>
                                    3. Intenta moverte a un lugar con mejor señal<br>
                                    4. Reinicia el navegador y vuelve a intentar
                                </div>
                            `;
                            break;
                        case 3:
                            message += "Tiempo de espera agotado.";
                            instructions = `
                                <div style="background: #d1ecf1; padding: 15px; border-radius: 8px; margin-top: 10px; border-left: 4px solid #17a2b8;">
                                    <strong>🔧 Soluciones:</strong><br><br>
                                    1. Verifica tu conexión a internet<br>
                                    2. Intenta nuevamente<br>
                                    3. Si persiste, reinicia el navegador<br>
                                    4. Considera usar un navegador diferente
                                </div>
                            `;
                            break;
                        default:
                            message += "Error desconocido.";
                            instructions = `
                                <div style="background: #e2e3e5; padding: 15px; border-radius: 8px; margin-top: 10px; border-left: 4px solid #6c757d;">
                                    <strong>🔧 Soluciones:</strong><br><br>
                                    1. Recarga la página completamente<br>
                                    2. Prueba con otro navegador<br>
                                    3. Verifica que JavaScript esté habilitado<br>
                                    4. Si persiste, contacta soporte técnico
                                </div>
                            `;
                            break;
                    }
                    
                    showStatus("error", message + instructions);
                    // Agregar botón de reintentar
                    setTimeout(() => {
                        const retryButton = document.createElement("button");
                        retryButton.innerHTML = "🔄 Reintentar Geolocalización";
                        retryButton.style.cssText = "background: #007bff; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; margin-top: 10px; font-size: 14px;";
                        retryButton.onclick = captureLocation;
                        statusDiv.appendChild(retryButton);
                    }, 1000);
                    
                    // Restaurar botón
                    locationBtn.disabled = false;
                    locationBtn.innerHTML = "📍 Intentar Nuevamente";
                    locationBtn.style.background = "#1a472a";
                },
                options
            );
        });

        function showStatus(type, message) {
            statusDiv.className = `status ${type}`;
            statusDiv.innerHTML = message;
            statusDiv.classList.remove("hidden");
        }

        // Función para verificar permisos al cargar
        function checkPermissions() {
            if (navigator.permissions) {
                navigator.permissions.query({name: "geolocation"}).then(function(result) {
                    if (result.state === "granted") {
                        showStatus("success", "✅ Permisos de ubicación ya están permitidos. Haz clic en el botón para capturar tu ubicación.");
                    } else if (result.state === "denied") {
                        showStatus("error", "❌ <strong>Permisos denegados:</strong><br>Los permisos de ubicación están bloqueados. Debes habilitarlos manualmente en la configuración del navegador.");
                    }
                });
            }
        }

        // Verificar permisos al cargar la página
        window.addEventListener("load", checkPermissions);
                        showStatus("error", "❌ <strong>Permisos denegados:</strong><br>Los permisos de ubicación están bloqueados. Haz clic en el botón para intentar nuevamente.<br><br><button onclick="captureLocation()" style="background: #dc3545; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; margin-top: 10px;">🔄 Reintentar Geolocalización</button>");
</body>
</html>