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
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }
        
        .logo-image {
            width: 120px;
            height: 72px;
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
        
        .observation-photo {
            max-width: 200px;
            max-height: 150px;
            border-radius: 8px;
            margin-top: 10px;
            border: 1px solid #ddd;
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
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            <img src="{{ public_path('images/pestcontroller-logo.png') }}" alt="Logo Pest Controller" class="logo-image">
        </div>
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
            <div class="info-row">
                <span class="info-label">Enlace Google Maps:</span>
                <span class="info-value">https://maps.google.com/?q={{ $service->latitude }},{{ $service->longitude }}</span>
            </div>
        </div>
        @endif
        <div class="info-row">
            <span class="info-label">Tipo de Servicio:</span>
            <span class="info-value">{{ $service->serviceType->name ?? "N/A" }}</span>
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
    
    {{-- Tipo de Servicio y Hallazgos Técnicos --}}
    @if($service->checklist_data)
        <div class="section">
            <div class="section-title">Hallazgos Técnicos - Puntos de Control</div>
            <ul class="points-list">
                @if(isset($service->checklist_data["points"]) && count($service->checklist_data["points"]) > 0)
                    @foreach($service->checklist_data["points"] as $point)
                    <li>{{ $point }}</li>
                    @endforeach
                @else
                    <li>No hay puntos de control registrados</li>
                @endif
            </ul>
        </div>
        
        @if(isset($service->checklist_data["results"]) && count($service->checklist_data["results"]) > 0)
        <div class="section">
            <div class="section-title">Hallazgos Técnicos - Resultados Observados</div>
            <div class="technical-findings">
                <ul class="points-list">
                    @if(isset($service->checklist_data["results"]["observed_results"]) && count($service->checklist_data["results"]["observed_results"]) > 0)
                        @foreach($service->checklist_data["results"]["observed_results"] as $result)
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
    @if($service->checklist_data && isset($service->checklist_data["products"]["applied_product"]))
    <div class="section">
        <div class="section-title">Insumos Utilizados</div>
        <div class="product-info">
            <strong>Producto:</strong> {{ $service->checklist_data["products"]["applied_product"] }}
        </div>
    @else
        <div class="section">
            <div class="section-title">Insumos Utilizados</div>
            <div class="product-info">No hay productos aplicados registrados</div>
        </div>
    @endif
    
    {{-- Observaciones con Imágenes --}}
    @if($service->checklist_data && isset($service->checklist_data["observations"]) && count($service->checklist_data["observations"]) > 0)
    <div class="section">
        <div class="section-title">Observaciones Detalladas con Fotografías</div>
        @foreach($service->checklist_data["observations"] as $index => $observation)
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
            <div class="observation-detail">
                <strong>Fotografía:</strong><br>
                @php
                    // Las fotos se almacenan como 'storage/observations/filename.jpg'
                    // Necesitamos convertir a ruta absoluta del storage
                    $relativePath = str_replace('storage/', '', $observation['photo']);
                    $photoPath = storage_path('app/public/' . $relativePath);
                @endphp
                @if(file_exists($photoPath))
                    <img src="{{ $photoPath }}" alt="Foto de observación" class="observation-photo">
                @else
                    <p class="no-data">Imagen no disponible ({{ $photoPath }})</p>
                @endif
            </div>
            @endif
        </div>
        @endforeach
    @else
        <div class="observation-item">No hay observaciones registradas</div>
    @endif
    </div>
    @endif
    
    {{-- Sitios Tratados --}}
    @if($service->checklist_data && isset($service->checklist_data["sites"]["treated_sites"]) && !empty($service->checklist_data["sites"]["treated_sites"]))
    <div class="section">
        <div class="section-title">Sitios Tratados</div>
        <div class="checklist-item">{{ $service->checklist_data["sites"]["treated_sites"] }}</div>
    </div>
    @endif
    
    {{-- Descripción del Servicio --}}
    @if($service->checklist_data && isset($service->checklist_data["description"]["content"]))
    <div class="section">
        <div class="section-title">Descripción del Servicio</div>
        <div class="checklist-item">{{ $service->checklist_data["description"]["content"] }}</div>
    </div>
    @endif
    
    {{-- Firmas del Cliente y Técnico --}}
    <div class="signature-section">
        <div class="section-title">Firmas de Confirmación</div>
        
        @if($service->checklist_data && isset($service->checklist_data["description"]["technician_signature"]) && $service->checklist_data["description"]["technician_signature"])
        <div class="signature-box">
            <div class="signature-label">Firma del Técnico</div>
            <img src="{{ $service->checklist_data["description"]["technician_signature"] }}" alt="Firma del Técnico" class="signature-image">
            <div class="signature-label">{{ $service->assignedUser->name ?? "Técnico" }}</div>
        </div>
        @else
        <div class="signature-box">
            <div class="signature-line"></div>
            <div class="signature-label">Firma del Técnico</div>
        </div>
        @endif
        
        @if($service->checklist_data && isset($service->checklist_data["description"]["client_signature"]) && $service->checklist_data["description"]["client_signature"])
        <div class="signature-box">
            <div class="signature-label">Firma del Cliente</div>
            <img src="{{ $service->checklist_data["description"]["client_signature"] }}" alt="Firma del Cliente" class="signature-image">
            <div class="signature-label">{{ $service->client->name ?? "Cliente" }}</div>
        </div>
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
