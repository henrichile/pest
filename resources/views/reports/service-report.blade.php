<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CERTIFICADO DE SERVICIOS - {{ $report->report_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: "Arial", sans-serif;
            font-size: 10px;
            line-height: 1.2;
            color: #333;
            background: white;
        }
        
        .page {
            width: 210mm;
            min-height: 297mm;
            padding: 15mm;
            margin: 0 auto;
            background: white;
            position: relative;
        }
        
        .header {
            background: linear-gradient(135deg, #1a472a 0%, #2d5016 100%);
            color: white;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
            border-radius: 8px;
            position: relative;
        }
        
        .company-logo {
            width: 60px;
            height: 60px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-size: 20px;
            font-weight: bold;
            color: #1a472a;
            border: 2px solid #fff;
        }
        
        .header h1 {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .header h2 {
            font-size: 16px;
            font-weight: normal;
            opacity: 0.95;
        }
        
        .qr-section {
            position: absolute;
            top: 20px;
            right: 20px;
            background: white;
            padding: 10px;
            border: 2px solid #1a472a;
            border-radius: 8px;
            text-align: center;
        }
        
        .qr-code {
            width: 60px;
            height: 60px;
            margin-bottom: 5px;
        }
        
        .qr-text {
            font-size: 8px;
            color: #1a472a;
            font-weight: bold;
        }
        
        .report-info {
            background: #f8f9fa;
            padding: 15px;
            margin-bottom: 20px;
            border-left: 4px solid #1a472a;
            border-radius: 0 5px 5px 0;
        }
        
        .report-number {
            font-size: 14px;
            font-weight: bold;
            color: #1a472a;
        }
        
        .report-date {
            font-size: 11px;
            color: #666;
            margin-top: 3px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 15px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            background: #d4edda;
            color: #155724;
            margin-top: 5px;
        }
        
        .section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        
        .section-title {
            background: #1a472a;
            color: white;
            padding: 8px 12px;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 10px;
            border-radius: 4px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .info-item {
            margin-bottom: 8px;
        }
        
        .info-label {
            font-weight: bold;
            color: #1a472a;
            font-size: 10px;
            display: inline-block;
            width: 100px;
        }
        
        .info-value {
            color: #333;
            font-size: 10px;
        }
        
        .checklist-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .checklist-item {
            display: flex;
            justify-content: space-between;
            padding: 4px 0;
            border-bottom: 1px dotted #ccc;
            font-size: 9px;
        }
        
        .checklist-item:last-child {
            border-bottom: none;
        }
        
        .checklist-label {
            font-weight: bold;
            color: #1a472a;
        }
        
        .checklist-value {
            color: #333;
        }
        
        .observation-item {
            background: #f8f9fa;
            padding: 10px;
            margin-bottom: 10px;
            border-left: 3px solid #1a472a;
            border-radius: 0 4px 4px 0;
        }
        
        .observation-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        
        .observation-code {
            font-weight: bold;
            color: #1a472a;
            font-size: 9px;
        }
        
        .observation-date {
            font-size: 8px;
            color: #666;
        }
        
        .observation-detail {
            color: #333;
            font-size: 9px;
            line-height: 1.3;
        }
        
        .photos-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-top: 10px;
        }
        
        .photo-item {
            text-align: center;
            background: #f8f9fa;
            padding: 8px;
            border-radius: 4px;
            border: 1px dashed #1a472a;
        }
        
        .photo-placeholder {
            width: 100%;
            height: 80px;
            background: #e9ecef;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #666;
            font-size: 8px;
            margin-bottom: 5px;
        }
        
        .photo-caption {
            font-size: 8px;
            color: #1a472a;
            font-weight: bold;
        }
        
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-top: 10px;
        }
        
        .chart-item {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #e9ecef;
            text-align: center;
        }
        
        .chart-title {
            color: #1a472a;
            font-size: 9px;
            font-weight: bold;
            margin-bottom: 8px;
        }
        
        .chart-placeholder {
            width: 100%;
            height: 80px;
            background: #e9ecef;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #666;
            font-size: 8px;
        }
        
        .geolocation-box {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            border-left: 3px solid #1a472a;
            margin-top: 10px;
        }
        
        .geolocation-title {
            color: #1a472a;
            font-size: 10px;
            font-weight: bold;
            margin-bottom: 8px;
        }
        
        .geolocation-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        
        .geolocation-item {
            font-size: 9px;
        }
        
        .geolocation-label {
            font-weight: bold;
            color: #1a472a;
        }
        
        .geolocation-value {
            color: #333;
        }
        
        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-top: 30px;
            page-break-inside: avoid;
        }
        
        .signature-box {
            text-align: center;
            border-top: 1px solid #1a472a;
            padding-top: 10px;
            margin-top: 40px;
        }
        
        .signature-label {
            font-weight: bold;
            color: #1a472a;
            margin-bottom: 5px;
            font-size: 10px;
        }
        
        .signature-line {
            border-bottom: 1px solid #333;
            height: 30px;
            margin-bottom: 5px;
        }
        
        .signature-info {
            font-size: 8px;
            color: #666;
        }
        
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #1a472a;
            color: white;
            padding: 8px 15px;
            font-size: 8px;
            text-align: center;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        .two-column {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .full-width {
            grid-column: 1 / -1;
        }
    </style>
</head>
<body>
    <div class="page">
        <!-- Header -->
        <div class="header">
            <div class="qr-section">
                <div class="qr-code">
                    {!! $qrCode !!}
                </div>
                <div class="qr-text">VALIDAR</div>
            </div>
            
            <div class="company-logo">PC</div>
            <h1>PEST CONTROLLER SAT</h1>
            <h2>CERTIFICADO DE SERVICIOS</h2>
        </div>

        <!-- Report Information -->
        <div class="report-info">
            <div class="report-number">Reporte: {{ $report->report_number }}</div>
            <div class="report-date">Generado: {{ $report->generated_at->format("d/m/Y H:i") }}</div>
            <div class="status-badge">Servicio Completado</div>
        </div>

        <!-- Client Information -->
        <div class="section">
            <div class="section-title">📋 Información del Cliente</div>
            <div class="info-grid">
                <div>
                    <div class="info-item">
                        <span class="info-label">Nombre:</span>
                        <span class="info-value">{{ $service->client->name ?? "N/A" }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Teléfono:</span>
                        <span class="info-value">{{ $service->client->phone ?? "N/A" }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Email:</span>
                        <span class="info-value">{{ $service->client->email ?? "N/A" }}</span>
                    </div>
                </div>
                <div>
                    <div class="info-item">
                        <span class="info-label">Dirección:</span>
                        <span class="info-value">{{ $service->address ?? "N/A" }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Fecha Servicio:</span>
                        <span class="info-value">{{ $service->scheduled_date->format("d/m/Y") ?? "N/A" }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Técnico:</span>
                        <span class="info-value">{{ $service->assignedUser->name ?? "N/A" }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Geolocation -->
            <div class="geolocation-box">
                <div class="geolocation-title">📍 Geolocalización del Servicio</div>
                <div class="geolocation-grid">
                    <div class="geolocation-item">
                        <span class="geolocation-label">Ubicación:</span>
                        <span class="geolocation-value">{{ $service->address ?? "N/A" }}</span>
                    </div>
                    <div class="geolocation-item">
                        <span class="geolocation-label">Coordenadas GPS:</span>
                        <span class="geolocation-value">{{ $service->latitude ?? "N/A" }}, {{ $service->longitude ?? "N/A" }}</span>
                    </div>
                    <div class="geolocation-item">
                        <span class="geolocation-label">Precisión:</span>
                        <span class="geolocation-value">{{ $service->location_accuracy ?? "N/A" }} metros</span>
                    </div>
                    <div class="geolocation-item">
                        <span class="geolocation-label">Capturado:</span>
                        <span class="geolocation-value">{{ $service->location_captured_at ? $service->location_captured_at->format("d/m/Y H:i") : "N/A" }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Service Details -->
        <div class="section">
            <div class="section-title">🔧 Detalles del Servicio</div>
            <div class="info-grid">
                <div>
                    <div class="info-item">
                        <span class="info-label">Tipo:</span>
                        <span class="info-value">{{ ucfirst($service->service_type ?? "Desratización") }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Prioridad:</span>
                        <span class="info-value">{{ ucfirst($service->priority ?? "Media") }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Estado:</span>
                        <span class="info-value">{{ ucfirst($service->status ?? "Completado") }}</span>
                    </div>
                </div>
                <div>
                    <div class="info-item">
                        <span class="info-label">Iniciado:</span>
                        <span class="info-value">{{ $service->started_at ? $service->started_at->format("d/m/Y H:i") : "N/A" }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Completado:</span>
                        <span class="info-value">{{ $service->completed_at ? $service->completed_at->format("d/m/Y H:i") : "N/A" }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Duración:</span>
                        <span class="info-value">
                            @if($service->started_at && $service->completed_at)
                                {{ $service->started_at->diffInMinutes($service->completed_at) }} min
                            @else
                                N/A
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        @if($service->service_type !== 'desinsectacion')
        <!-- Checklist Results - Oculto para desinsectación -->
        <div class="section">
            <div class="section-title">✅ Checklist Técnico</div>
            <div class="checklist-grid">
                @if(isset($checklistData["points"]))
                <div>
                    <div style="font-weight: bold; color: #1a472a; margin-bottom: 8px; font-size: 10px;">Check de Puntos</div>
                    <div class="checklist-item">
                        <span class="checklist-label">Puntos Instalados:</span>
                        <span class="checklist-value">{{ $checklistData["points"]["installed_points_check"] ? "✓" : "✗" }}</span>
                    </div>
                    <div class="checklist-item">
                        <span class="checklist-label">Puntos Existentes:</span>
                        <span class="checklist-value">{{ $checklistData["points"]["existing_points_check"] ? "✓" : "✗" }}</span>
                    </div>
                    <div class="checklist-item">
                        <span class="checklist-label">Puntos Repuesto:</span>
                        <span class="checklist-value">{{ $checklistData["points"]["spare_points_check"] ? "✓" : "✗" }}</span>
                    </div>
                    <div class="checklist-item">
                        <span class="checklist-label">Verif. Física Inst.:</span>
                        <span class="checklist-value">{{ $checklistData["points"]["physical_installed_check"] ? "✓" : "✗" }}</span>
                    </div>
                </div>
                <div>
                    <div style="font-weight: bold; color: #1a472a; margin-bottom: 8px; font-size: 10px;">Verificaciones</div>
                    <div class="checklist-item">
                        <span class="checklist-label">Verif. Física Exist.:</span>
                        <span class="checklist-value">{{ $checklistData["points"]["physical_existing_check"] ? "✓" : "✗" }}</span>
                    </div>
                    <div class="checklist-item">
                        <span class="checklist-label">Verif. Física Rep.:</span>
                        <span class="checklist-value">{{ $checklistData["points"]["physical_spare_check"] ? "✓" : "✗" }}</span>
                    </div>
                    <div class="checklist-item">
                        <span class="checklist-label">Peso Cebo:</span>
                        <span class="checklist-value">{{ $checklistData["points"]["bait_weight_check"] ? "✓" : "✗" }}</span>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Products and Results -->
        <div class="section">
            <div class="section-title">🧪 Productos y Resultados</div>
            <div class="two-column">
                @if(isset($checklistData["products"]))
                <div>
                    <div style="font-weight: bold; color: #1a472a; margin-bottom: 8px; font-size: 10px;">Productos Aplicados</div>
                    <div class="checklist-item">
                        <span class="checklist-label">Producto:</span>
                        <span class="checklist-value">{{ $checklistData["products"]["applied_product"] ?? "N/A" }}</span>
                    </div>
                </div>
                @endif
                
                @if(isset($checklistData["results"]))
                <div>
                    @if($service->service_type === 'desinsectacion')
                    <div style="font-weight: bold; color: #1a472a; margin-bottom: 8px; font-size: 10px;">Lámparas Ultravioletas</div>
                    <div class="checklist-item">
                        <span class="checklist-label">Lámparas UV:</span>
                        <span class="checklist-value">{{ $checklistData["results"]["uv_lamps"] ?? "N/A" }}</span>
                    </div>
                    <div class="checklist-item">
                        <span class="checklist-label">TUV:</span>
                        <span class="checklist-value">{{ $checklistData["results"]["tuv"] ?? "N/A" }}</span>
                    </div>
                    <div class="checklist-item">
                        <span class="checklist-label">Dispositivos Instalados:</span>
                        <span class="checklist-value">{{ $checklistData["results"]["devices_installed"] ?? "N/A" }}</span>
                    </div>
                    <div class="checklist-item">
                        <span class="checklist-label">Dispositivos Existentes:</span>
                        <span class="checklist-value">{{ $checklistData["results"]["devices_existing"] ?? "N/A" }}</span>
                    </div>
                    <div class="checklist-item">
                        <span class="checklist-label">Dispositivos Repuestos:</span>
                        <span class="checklist-value">{{ $checklistData["results"]["devices_replaced"] ?? "N/A" }}</span>
                    </div>
                    @else
                    <div style="font-weight: bold; color: #1a472a; margin-bottom: 8px; font-size: 10px;">Resultados</div>
                    <div class="checklist-item">
                        <span class="checklist-label">Puntos Totales:</span>
                        <span class="checklist-value">{{ $checklistData["results"]["total_installed_points"] ?? "N/A" }}</span>
                    </div>
                    <div class="checklist-item">
                        <span class="checklist-label">Consumo:</span>
                        <span class="checklist-value">{{ $checklistData["results"]["total_consumption_activity"] ?? "N/A" }}g</span>
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>

        <!-- Photos Section -->
        <div class="section">
            <div class="section-title">📸 Fotografías del Servicio</div>
            <div class="photos-grid">
                <div class="photo-item">
                    <div class="photo-placeholder">�� Antes</div>
                    <div class="photo-caption">Estado inicial</div>
                </div>
                <div class="photo-item">
                    <div class="photo-placeholder">📷 Durante</div>
                    <div class="photo-caption">Proceso</div>
                </div>
                <div class="photo-item">
                    <div class="photo-placeholder">📷 Después</div>
                    <div class="photo-caption">Estado final</div>
                </div>
                <div class="photo-item">
                    <div class="photo-placeholder">📷 Evidencia</div>
                    <div class="photo-caption">Hallazgos</div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="section">
            <div class="section-title">📊 Gráficos y Estadísticas</div>
            <div class="charts-grid">
                <div class="chart-item">
                    <div class="chart-title">Puntos Instalados</div>
                    <div class="chart-placeholder">📈 Gráfico de Barras</div>
                </div>
                <div class="chart-item">
                    <div class="chart-title">Actividad Consumo</div>
                    <div class="chart-placeholder">📊 Gráfico Circular</div>
                </div>
                <div class="chart-item">
                    <div class="chart-title">Evolución Temporal</div>
                    <div class="chart-placeholder">📉 Gráfico de Líneas</div>
                </div>
                <div class="chart-item">
                    <div class="chart-title">Mapa de Calor</div>
                    <div class="chart-placeholder">🗺️ Mapa Interactivo</div>
                </div>
            </div>
        </div>

        <!-- Observations -->
        @if($service->observations && $service->observations->count() > 0)
        <div class="section">
            <div class="section-title">�� Observaciones Detalladas</div>
            @foreach($service->observations as $observation)
            <div class="observation-item">
                <div class="observation-header">
                    <span class="observation-code">
                        @if($observation->bait_station_code)
                            CE: {{ $observation->bait_station_code }}
                        @endif
                        OBS: {{ $observation->observation_number }}
                    </span>
                    <span class="observation-date">{{ $observation->created_at->format("d/m/Y H:i") }}</span>
                </div>
                <div class="observation-detail">{{ $observation->detail }}</div>
                @if($observation->complementary_observation)
                <div class="observation-detail" style="font-style: italic; color: #666; margin-top: 3px;">{{ $observation->complementary_observation }}</div>
                @endif
            </div>
            @endforeach
        </div>
        @endif

        <!-- Signatures -->
        <div class="signatures">
            <div class="signature-box">
                <div class="signature-label">Firma del Cliente</div>
                <div class="signature-line"></div>
                <div class="signature-info">
                    {{ $service->client->name ?? "Cliente" }}<br>
                    {{ $service->client->phone ?? "Teléfono" }}
                </div>
            </div>
            <div class="signature-box">
                <div class="signature-label">Firma del Técnico</div>
                <div class="signature-line"></div>
                <div class="signature-info">
                    {{ $service->assignedUser->name ?? "Técnico" }}<br>
                    {{ $service->assignedUser->email ?? "Email" }}
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div>PEST CONTROLLER SAT - Certificado generado el {{ $report->generated_at->format("d/m/Y H:i") }}</div>
        <div>Código: {{ $report->qr_code }} | Reporte: {{ $report->report_number }}</div>
    </div>
</body>
</html>
