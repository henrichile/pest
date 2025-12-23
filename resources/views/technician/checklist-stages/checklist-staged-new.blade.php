<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Servicio- {{ $service->id }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <style>
        /* Versión: 2024-11-12-09:30 - Diseño actualizado según dashboard REFERENCIA */
        /* IMPORTANTE: Este archivo tiene fondo gris claro #f9fafb y diseño moderno */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: #f9fafb !important;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 12px;
            padding: 32px;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            max-width: 1000px;
            width: 100%;
            margin: 0 auto;
        }

        h1 {
            color: #111827;
            margin-bottom: 10px;
            font-size: 28px;
            font-weight: 700;
        }

        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 16px;
        }

        .service-info {
            background: #ffffff;
            padding: 24px;
            border-radius: 8px;
            margin-bottom: 24px;
            text-align: left;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }

        .client-info-bar {
            background: #22c55e;
            color: white;
            padding: 16px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            align-items: center;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        }

        .client-info-bar .info-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 500;
        }

        .client-info-bar .info-item strong {
            font-weight: 700;
            margin-right: 5px;
        }

        .service-info h3 {
            color: #111827;
            margin-bottom: 20px;
            font-size: 18px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #495057;
            font-size: 14px;
        }

        .info-value {
            color: #212529;
            font-weight: 500;
            font-size: 15px;
        }

        .progress-box {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 24px;
            margin-bottom: 24px;
            text-align: left;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }

        .progress-box h4 {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 18px;
            font-weight: 600;
            color: #111827;
        }

        .progress-bar {
            width: 100%;
            height: 14px;
            background: #e0e0e0;
            border-radius: 10px;
            overflow: hidden;
            margin: 15px 0;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .progress-fill {
            height: 100%;
            background: #22c55e;
            border-radius: 10px;
            transition: width 0.5s ease;
        }

        .progress-text {
            font-size: 15px;
            color: #495057;
            margin-top: 10px;
            font-weight: 500;
            text-align: center;
        }

        .stage-box {
            background: #ffffff;
            padding: 24px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: left;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }

        .stage-box h4 {
            color: #111827;
            margin-bottom: 24px;
            font-size: 18px;
            font-weight: 600;
            display: flex;
            align-items: center;
            padding-bottom: 16px;
            border-bottom: 1px solid #e5e7eb;
        }

        .stage-box h4 .icon {
            margin-right: 12px;
            font-size: 24px;
        }

        .stage-title {
            color: #1a472a;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .stage-instruction {
            color: #666;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #495057;
            font-size: 14px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: #ffffff;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #22c55e;
            outline: none;
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1);
        }

        .form-group input:hover,
        .form-group select:hover,
        .form-group textarea:hover {
            border-color: #adb5bd;
        }

        .checkbox-group {
            display: grid;
            grid-template-columns: 1fr;
            gap: 12px;
            margin-bottom: 20px;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            padding: 15px;
            background: white;
            border-radius: 8px;
            border: 2px solid #e0e0e0;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .checkbox-item:hover {
            border-color: #22c55e;
            background: #f9fafb;
        }

        .checkbox-item.checked {
            border-color: #22c55e;
            background: #f0fdf4;
        }

        .checkbox-item input[type="checkbox"] {
            width: 20px;
            height: 20px;
            margin-right: 15px;
            cursor: pointer;
            accent-color: #22c55e;
        }

        .checkbox-item label {
            margin: 0;
            cursor: pointer;
            flex: 1;
            font-size: 16px;
            color: #333;
        }

        .radio-group {
            display: grid;
            grid-template-columns: 1fr;
            gap: 12px;
            margin-bottom: 20px;
        }

        .radio-item {
            display: flex;
            align-items: center;
            padding: 15px;
            background: white;
            border-radius: 8px;
            border: 2px solid #e0e0e0;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .radio-item:hover {
            border-color: #22c55e;
            background: #f9fafb;
        }

        .radio-item.checked {
            border-color: #22c55e;
            background: #f0fdf4;
        }

        .radio-item input[type="radio"] {
            width: 20px;
            height: 20px;
            margin-right: 15px;
            cursor: pointer;
            accent-color: #22c55e;
        }

        .radio-item label {
            margin: 0;
            cursor: pointer;
            flex: 1;
            font-size: 16px;
            color: #333;
        }

        .buttons-container {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .next-button {
            background: #22c55e;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }

        .next-button:hover {
            background: #16a34a;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1);
        }

        .next-button:disabled {
            background: #adb5bd;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .next-button .arrow {
            margin-left: 8px;
            font-size: 18px;
        }

        .back-button {
            background: #ffffff;
            color: #374151;
            border: 1px solid #d1d5db;
            padding: 12px 24px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }

        .back-button:hover {
            background: #f9fafb;
            border-color: #9ca3af;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1);
        }

        .back-button .arrow {
            margin-right: 8px;
            font-size: 18px;
        }

        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #22c55e;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 10px;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .stage-indicator {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 24px;
            padding: 16px;
            background: #ffffff;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }

        .stage-indicator span {
            flex: 1;
            padding: 18px 12px;
            border-radius: 12px;
            background: #ffffff;
            border: 2px solid #dee2e6;
            text-align: center;
            font-size: 13px;
            font-weight: 600;
            color: #6c757d;
            transition: all 0.3s ease;
            position: relative;
            cursor: default;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .stage-number {
            font-size: 20px;
            font-weight: 700;
            line-height: 1;
        }

        .stage-name {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            line-height: 1.2;
        }

        .stage-indicator span.completed {
            background: #ffffff;
            border: 2px solid #22c55e;
            color: #22c55e;
        }

        .stage-indicator span.completed .stage-number {
            color: #22c55e;
        }

        .stage-indicator span.completed::before {
            content: "";
            position: absolute;
            top: 8px;
            right: 8px;
            width: 20px;
            height: 20px;
            background: #22c55e;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='white' stroke-width='3'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M4.5 12.75l6 6 9-13.5'/%3E%3C/svg%3E");
            background-size: 14px 14px;
            background-repeat: no-repeat;
            background-position: center;
        }

        .stage-indicator span.active {
            background: #ffffff;
            border: 2px solid #22c55e;
            color: #22c55e;
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1);
            transform: translateY(-2px);
        }

        .stage-indicator span.active .stage-number {
            color: #22c55e;
            font-weight: 700;
        }

        .stage-indicator span.active::after {
            content: "";
            position: absolute;
            top: 8px;
            right: 8px;
            width: 12px;
            height: 12px;
            background: #22c55e;
            border-radius: 50%;
            border: 2px solid #ffffff;
        }

        .stage-indicator span.pending {
            background: #ffffff;
            border-color: #dee2e6;
            color: #adb5bd;
        }

        .stage-indicator a {
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .geolocation-retry-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #22c55e;
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.2s ease;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }

        .geolocation-retry-btn:hover {
            background: #16a34a;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1);
        }

        /* Estilos mejorados para formularios de etapas */
        .form-section {
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 12px;
            border: 1px solid #e9ecef;
        }

        .form-section h5 {
            color: #111827;
            margin-bottom: 20px;
            font-size: 18px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e5e7eb;
        }

        .form-section h6 {
            color: #495057;
            margin-bottom: 15px;
            font-size: 16px;
            font-weight: 600;
        }

        .form-input,
        .form-select,
        .form-textarea {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: #ffffff;
            font-family: inherit;
        }

        .form-input:focus,
        .form-select:focus,
        .form-textarea:focus {
            border-color: #22c55e;
            outline: none;
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1);
        }

        .form-textarea {
            resize: vertical;
            min-height: 100px;
        }

        .add-button {
            background: #22c55e;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }

        .add-button:hover {
            background: #16a34a;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1);
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e5e7eb;
        }

        .section-header h5 {
            margin: 0;
            border: none;
            padding: 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Indicador de versión actualizada -->
        <div>
            ✅ DISEÑO ACTUALIZADO - Versión 2024-11-12-09:30 - Si NO ves este banner verde, tu navegador tiene caché
        </div>

        <div style="text-align: center; margin-bottom: 32px;">
            <h1 style="font-size: 32px; margin-bottom: 8px; color: #111827;">Servicio - {{ $service->id }}</h1>
            <p>Complete todas las etapas para finalizar el servicio</p>
        </div>

        <div class="service-info">
            <h3>
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                    style="width: 20px; height: 20px; color: #6b7280;">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                </svg>
                Detalles del Servicio
            </h3>

            <!-- Barra azul de información del cliente -->
            <div class="client-info-bar">
                <div class="info-item">
                    <strong>Cliente:</strong>
                    <span>{{ $service->client->name ?? "N/A" }}</span>
                </div>
                <div class="info-item">
                    <strong>Dirección:</strong>
                    <span>{{ $service->address ?? "N/A" }}</span>
                </div>
                @if($service->client && $service->client->phone)
                    <div class="info-item">
                        <strong>Tel:</strong>
                        <span>{{ $service->client->phone }}</span>
                    </div>
                @endif
            </div>

            <div class="info-row">
                <span class="info-label">Tipo de Servicio:</span>
                <span class="info-value">{{ ucfirst(str_replace('-', ' ', $service->service_type)) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Prioridad:</span>
                <span class="info-value">
                    <span style="display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600; 
                        @if(strtolower($service->priority ?? 'media') === 'alta') background: #fee; color: #c33;
                        @elseif(strtolower($service->priority ?? 'media') === 'media') background: #fff4e6; color: #d97706;
                        @else background: #e6f7ff; color: #1890ff;
                        @endif">
                        {{ ucfirst($service->priority ?? "Media") }}
                    </span>
                </span>
            </div>
            <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px;">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                        style="width: 20px; height: 20px; @if($service->latitude && $service->longitude) color: #22c55e; @else color: #ef4444; @endif">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                    </svg>
                    <span class="info-label">Ubicación:</span>
                    <span class="info-value"
                        style="@if($service->latitude && $service->longitude) color: #22c55e; font-weight: 600; @else color: #ef4444; @endif">
                        @if($service->latitude && $service->longitude)
                            Capturada ({{ number_format($service->latitude, 6) }},
                            {{ number_format($service->longitude, 6) }})
                        @else
                            No capturada
                        @endif
                    </span>
                </div>
                <div style="text-align: center;">
                    <a href="{{ route('technician.service.checklist.location', $service) }}"
                        class="geolocation-retry-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" height="20" width="20" viewBox="0 0 640 640"
                            fill="currentColor"
                            class="dark:fill-white"><!--!Font Awesome Free v7.1.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                            <path
                                d="M544.1 256L552 256C565.3 256 576 245.3 576 232L576 88C576 78.3 570.2 69.5 561.2 65.8C552.2 62.1 541.9 64.2 535 71L483.3 122.8C439 86.1 382 64 320 64C191 64 84.3 159.4 66.6 283.5C64.1 301 76.2 317.2 93.7 319.7C111.2 322.2 127.4 310 129.9 292.6C143.2 199.5 223.3 128 320 128C364.4 128 405.2 143 437.7 168.3L391 215C384.1 221.9 382.1 232.2 385.8 241.2C389.5 250.2 398.3 256 408 256L544.1 256zM573.5 356.5C576 339 563.8 322.8 546.4 320.3C529 317.8 512.7 330 510.2 347.4C496.9 440.4 416.8 511.9 320.1 511.9C275.7 511.9 234.9 496.9 202.4 471.6L249 425C255.9 418.1 257.9 407.8 254.2 398.8C250.5 389.8 241.7 384 232 384L88 384C74.7 384 64 394.7 64 408L64 552C64 561.7 69.8 570.5 78.8 574.2C87.8 577.9 98.1 575.8 105 569L156.8 517.2C201 553.9 258 576 320 576C449 576 555.7 480.6 573.4 356.5z" />
                        </svg>
                        <span>{{ $service->latitude && $service->longitude ? 'Reconectar' : 'Capturar' }}
                            Geolocalización</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="progress-box">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h4 style="margin: 0; display: flex; align-items: center; gap: 10px;">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                        style="width: 20px; height: 20px; color: #6b7280;">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                    </svg>
                    Progreso del Servicio
                </h4>
                <span
                    style="font-size: 24px; font-weight: 700; color: #22c55e;">{{ number_format($service->getProgressPercentage(), 0) }}%</span>
            </div>
            <div class="progress-bar">
                <div class="progress-fill" style="width: {{ $service->getProgressPercentage() }}%"></div>
            </div>
            <div class="progress-text">Etapa {{ $service->getStageNumber() }} de {{ $service->getTotalStage() }}</div>
        </div>

        <div class="stage-indicator">
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
                    <span class="{{ $class }}">
                        <span class="stage-number">{{ $stageInfo['num'] }}</span>
                        <span class="stage-name">{{ $stageInfo['name'] }}</span>
                    </span>
                @endforeach
            @else
                @if($service->service_type === 'desratizacion')
                    <span
                        class="{{ $service->checklist_stage === 'points' ? 'active' : ($service->getStageNumber() > 1 ? 'completed' : '') }}">Puntos</span>
                @endif
                <span
                    class="{{ $service->checklist_stage === 'products' ? 'active' : ($service->getStageNumber() > 2 ? 'completed' : '') }}">Productos</span>
                {{-- ✅ CORREGIDO: Results solo para desratización y desinsectación, NO para sanitización --}}
                @if(in_array($service->service_type, ['desratizacion', 'desinsectacion']))
                    <span
                        class="{{ $service->checklist_stage === 'results' ? 'active' : ($service->getStageNumber() > 3 ? 'completed' : '') }}">Resultados</span>
                @endif
                <span
                    class="{{ $service->checklist_stage === 'observations' ? 'active' : ($service->getStageNumber() > 4 ? 'completed' : '') }}">Observaciones</span>
                <span
                    class="{{ $service->checklist_stage === 'sites' ? 'active' : ($service->getStageNumber() > 5 ? 'completed' : '') }}">Sitios</span>
                <span
                    class="{{ $service->checklist_stage === 'description' ? 'active' : ($service->getStageNumber() > 6 ? 'completed' : '') }}">Descripción</span>
            @endif
        </div>

        <div class="stage-box">
            <h4>
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                    style="width: 20px; height: 20px; color: #6b7280;">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                </svg>
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
            </h4>

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

        <div class="buttons-container">
            @if($previousStage)
                <a href="{{ route('technician.service.checklist.stage', ['service' => $service, 'stage' => $previousStage]) }}"
                    class="back-button">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                        style="width: 18px; height: 18px; margin-right: 8px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    <span>Etapa Anterior</span>
                </a>
            @else
                <a href="{{ route('technician.service.detail', $service) }}" class="back-button">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                        style="width: 18px; height: 18px; margin-right: 8px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    <span>Volver al Servicio</span>
                </a>
            @endif

            @if($nextStage)
                @if($service->service_type === 'monitoreo-cebaderas')
                    <button type="submit"
                        form="{{ $service->checklist_stage === 'monitoreo-datos' ? 'monitoreoDatosForm' : ($service->checklist_stage === 'monitoreo-croquis' ? 'croquisForm' : ($service->checklist_stage === 'monitoreo-completo' ? 'monitoreoCompletoForm' : ($service->checklist_stage === 'monitoreo-estadisticas' ? 'estadisticasForm' : ($service->checklist_stage === 'monitoreo-analisis' ? 'analisisForm' : 'firmaForm')))) }}"
                        class="next-button">
                        <span>Siguiente Etapa</span>
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                            style="width: 18px; height: 18px; margin-left: 8px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                        </svg>
                    </button>
                @else
                    <a href="{{ route('technician.service.checklist.stage', ['service' => $service, 'stage' => $nextStage]) }}"
                        class="next-button">
                        <span>Siguiente Etapa</span>
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                            style="width: 18px; height: 18px; margin-left: 8px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                @endif
            @else
                <button type="button" class="next-button bg-green-500" disabled>
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                        style="width: 18px; height: 18px; margin-right: 8px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                    <span>Última Etapa</span>
                </button>
            @endif
        </div>

    </div>

    <script>
        // Auto-guardar datos al cambiar de etapa
        document.addEventListener("DOMContentLoaded", function () {
            const forms = document.querySelectorAll("form[data-stage]");
            forms.forEach(form => {
                form.addEventListener("submit", function (e) {
                    // Agregar indicador de carga
                    const submitBtn = form.querySelector("button[type=\"submit\"]");
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = "<span class=\"loading-spinner\"></span>Guardando...";
                    }
                });
            });

            // Mejorar interacción de checkboxes
            const checkboxes = document.querySelectorAll('.checkbox-item input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function () {
                    const item = this.closest('.checkbox-item');
                    if (this.checked) {
                        item.classList.add('checked');
                    } else {
                        item.classList.remove('checked');
                    }
                });
            });

            // Mejorar interacción de radio buttons
            const radios = document.querySelectorAll('.radio-item input[type="radio"]');
            radios.forEach(radio => {
                radio.addEventListener('change', function () {
                    // Remover clase checked de todos los radio items
                    document.querySelectorAll('.radio-item').forEach(item => {
                        item.classList.remove('checked');
                    });
                    // Agregar clase checked al item seleccionado
                    if (this.checked) {
                        this.closest('.radio-item').classList.add('checked');
                    }
                });
            });
        });
    </script>
</body>

</html>