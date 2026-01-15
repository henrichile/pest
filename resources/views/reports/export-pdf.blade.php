<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Reporte de Servicios</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 15px;
            color: #111827;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 3px solid #22c55e;
            padding-bottom: 10px;
        }

        .header h1 {
            color: #111827;
            margin: 0;
            font-size: 20px;
        }

        .header p {
            color: #6b7280;
            margin: 3px 0;
            font-size: 10px;
        }

        /* Tarjetas de estadísticas */
        .stats-container {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .stat-card {
            display: table-cell;
            width: 16.66%;
            padding: 8px;
            text-align: center;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
        }

        .stat-card:first-child {
            border-radius: 8px 0 0 8px;
        }

        .stat-card:last-child {
            border-radius: 0 8px 8px 0;
        }

        .stat-value {
            font-size: 18px;
            font-weight: bold;
            color: #111827;
        }

        .stat-label {
            font-size: 8px;
            color: #6b7280;
            text-transform: uppercase;
        }

        .stat-card.blue {
            border-top: 3px solid #3b82f6;
        }

        .stat-card.green {
            border-top: 3px solid #22c55e;
        }

        .stat-card.yellow {
            border-top: 3px solid #f59e0b;
        }

        .stat-card.purple {
            border-top: 3px solid #8b5cf6;
        }

        .stat-card.orange {
            border-top: 3px solid #f97316;
        }

        /* Sección de gráficos */
        .charts-row {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .chart-box {
            display: table-cell;
            width: 50%;
            padding: 10px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            vertical-align: top;
        }

        .chart-box:first-child {
            border-radius: 8px 0 0 8px;
            border-right: none;
        }

        .chart-box:last-child {
            border-radius: 0 8px 8px 0;
        }

        .chart-title {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 8px;
            color: #374151;
        }

        /* Barras de gráficos */
        .bar-chart {
            width: 100%;
        }

        .bar-row {
            margin-bottom: 6px;
        }

        .bar-label {
            font-size: 8px;
            color: #374151;
            margin-bottom: 2px;
        }

        .bar-container {
            background: #e5e7eb;
            height: 12px;
            border-radius: 4px;
            overflow: hidden;
        }

        .bar-fill {
            height: 100%;
            border-radius: 4px;
            position: relative;
        }

        .bar-fill.green {
            background: #22c55e;
        }

        .bar-fill.blue {
            background: #3b82f6;
        }

        .bar-fill.yellow {
            background: #f59e0b;
        }

        .bar-fill.red {
            background: #ef4444;
        }

        .bar-fill.purple {
            background: #8b5cf6;
        }

        .bar-fill.pink {
            background: #ec4899;
        }

        .bar-fill.cyan {
            background: #06b6d4;
        }

        .bar-fill.orange {
            background: #f97316;
        }

        .bar-value {
            font-size: 7px;
            color: white;
            padding-left: 4px;
            line-height: 12px;
        }

        /* Top 5 */
        .top-row {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .top-box {
            display: table-cell;
            width: 50%;
            padding: 10px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            vertical-align: top;
        }

        .top-box:first-child {
            border-radius: 8px 0 0 8px;
            border-right: none;
        }

        .top-box:last-child {
            border-radius: 0 8px 8px 0;
        }

        .top-item {
            display: table;
            width: 100%;
            padding: 4px 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .top-item:last-child {
            border-bottom: none;
        }

        .top-rank {
            display: table-cell;
            width: 20px;
            font-weight: bold;
            color: #f59e0b;
        }

        .top-name {
            display: table-cell;
            font-size: 9px;
        }

        .top-count {
            display: table-cell;
            text-align: right;
            font-weight: bold;
            font-size: 9px;
        }

        /* Tabla de servicios */
        table.services {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table.services th {
            background-color: #22c55e;
            color: white;
            padding: 6px 4px;
            text-align: left;
            border: 1px solid #16a34a;
            font-size: 8px;
            font-weight: bold;
        }

        table.services td {
            padding: 4px;
            border: 1px solid #e5e7eb;
            font-size: 7px;
            color: #111827;
        }

        table.services tr:nth-child(even) {
            background-color: #f9fafb;
        }

        /* Resumen */
        .summary {
            margin-top: 15px;
            padding: 10px;
            background-color: #f0fdf4;
            border: 1px solid #22c55e;
            border-radius: 8px;
        }

        .summary h3 {
            margin: 0 0 8px 0;
            color: #166534;
            font-size: 11px;
        }

        .summary p {
            margin: 3px 0;
            color: #166534;
            font-size: 9px;
        }

        .page-break {
            page-break-before: always;
        }

        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #374151;
            margin: 15px 0 10px 0;
            padding-bottom: 5px;
            border-bottom: 2px solid #e5e7eb;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>📊 Reporte de Servicios</h1>
        <p>Período: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} -
            {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</p>
        <p>Generado el: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <!-- Tarjetas de Estadísticas -->
    <div class="stats-container">
        <div class="stat-card blue">
            <div class="stat-value">{{ $totalServices }}</div>
            <div class="stat-label">Total Servicios</div>
        </div>
        <div class="stat-card green">
            <div class="stat-value">{{ $completedServices }}</div>
            <div class="stat-label">Completados</div>
        </div>
        <div class="stat-card yellow">
            <div class="stat-value">{{ $pendingServices }}</div>
            <div class="stat-label">Pendientes</div>
        </div>
        <div class="stat-card purple">
            <div class="stat-value">${{ number_format($periodIncome, 0, ',', '.') }}</div>
            <div class="stat-label">Ingresos</div>
        </div>
        <div class="stat-card blue">
            <div class="stat-value">{{ $uniqueClients }}</div>
            <div class="stat-label">Clientes</div>
        </div>
        <div class="stat-card orange">
            <div class="stat-value">{{ $activeTechnicians }}</div>
            <div class="stat-label">Técnicos</div>
        </div>
    </div>

    <!-- Gráficos de Distribución -->
    <div class="charts-row">
        <!-- Distribución por Estado -->
        <div class="chart-box">
            <div class="chart-title">📈 Distribución por Estado</div>
            <div class="bar-chart">
                @php
                    $statusColors = ['finalizado' => 'green', 'completed' => 'green', 'en_progreso' => 'blue', 'in_progress' => 'blue', 'pendiente' => 'yellow', 'pending' => 'yellow', 'cancelado' => 'red'];
                @endphp
                @foreach($statusDistribution as $status => $count)
                    @php
                        $percentage = $maxStatusCount > 0 ? ($count / $maxStatusCount) * 100 : 0;
                        $color = $statusColors[$status] ?? 'blue';
                    @endphp
                    <div class="bar-row">
                        <div class="bar-label">{{ ucfirst(str_replace('_', ' ', $status)) }}: {{ $count }}</div>
                        <div class="bar-container">
                            <div class="bar-fill {{ $color }}" style="width: {{ $percentage }}%;">
                                <span
                                    class="bar-value">{{ number_format($totalServices > 0 ? ($count / $totalServices) * 100 : 0, 1) }}%</span>
                            </div>
                        </div>
                    </div>
                @endforeach
                @if($statusDistribution->isEmpty())
                    <p style="color: #6b7280; font-size: 8px;">Sin datos disponibles</p>
                @endif
            </div>
        </div>

        <!-- Distribución por Tipo -->
        <div class="chart-box">
            <div class="chart-title">🔧 Distribución por Tipo de Servicio</div>
            <div class="bar-chart">
                @php
                    $typeColors = ['purple', 'pink', 'cyan', 'orange', 'blue', 'green', 'yellow', 'red'];
                    $colorIndex = 0;
                @endphp
                @foreach($typeDistribution as $type => $count)
                    @php
                        $percentage = $maxTypeCount > 0 ? ($count / $maxTypeCount) * 100 : 0;
                        $color = $typeColors[$colorIndex % count($typeColors)];
                        $colorIndex++;
                    @endphp
                    <div class="bar-row">
                        <div class="bar-label">{{ ucfirst(str_replace('-', ' ', $type ?? 'N/A')) }}: {{ $count }}</div>
                        <div class="bar-container">
                            <div class="bar-fill {{ $color }}" style="width: {{ $percentage }}%;">
                                <span
                                    class="bar-value">{{ number_format($totalServices > 0 ? ($count / $totalServices) * 100 : 0, 1) }}%</span>
                            </div>
                        </div>
                    </div>
                @endforeach
                @if($typeDistribution->isEmpty())
                    <p style="color: #6b7280; font-size: 8px;">Sin datos disponibles</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Top 5 -->
    <div class="top-row">
        <!-- Top 5 Clientes -->
        <div class="top-box">
            <div class="chart-title">👥 Top 5 Clientes</div>
            @forelse($topClients as $index => $item)
                <div class="top-item">
                    <div class="top-rank">{{ $index + 1 }}.</div>
                    <div class="top-name">{{ $item['client']->business_name ?? $item['client']->name ?? 'N/A' }}</div>
                    <div class="top-count">{{ $item['count'] }} servicios</div>
                </div>
            @empty
                <p style="color: #6b7280; font-size: 8px; text-align: center;">Sin datos disponibles</p>
            @endforelse
        </div>

        <!-- Top 5 Técnicos -->
        <div class="top-box">
            <div class="chart-title">🔧 Top 5 Técnicos</div>
            @forelse($topTechnicians as $index => $item)
                <div class="top-item">
                    <div class="top-rank">{{ $index + 1 }}.</div>
                    <div class="top-name">{{ $item['technician']->name ?? 'N/A' }}</div>
                    <div class="top-count">{{ $item['count'] }} servicios</div>
                </div>
            @empty
                <p style="color: #6b7280; font-size: 8px; text-align: center;">Sin datos disponibles</p>
            @endforelse
        </div>
    </div>

    <!-- Tabla de Servicios -->
    <div class="section-title">📋 Detalle de Servicios</div>
    <table class="services">
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Tipo</th>
                <th>Fecha</th>
                <th>Estado</th>
                <th>Prioridad</th>
                <th>Técnico</th>
                <th>Precio</th>
            </tr>
        </thead>
        <tbody>
            @forelse($services as $service)
                <tr>
                    <td>{{ $service->id }}</td>
                    <td>{{ $service->client->business_name ?? $service->client->name ?? 'N/A' }}</td>
                    <td>{{ ucfirst(str_replace('-', ' ', $service->service_type ?? 'N/A')) }}</td>
                    <td>{{ $service->scheduled_date ? $service->scheduled_date->format('d/m/Y') : 'N/A' }}</td>
                    <td>{{ ucfirst($service->status ?? 'N/A') }}</td>
                    <td>{{ ucfirst($service->priority ?? 'N/A') }}</td>
                    <td>{{ $service->assignedUser->name ?? 'Sin asignar' }}</td>
                    <td>{{ $service->price ? '$' . number_format($service->price, 0, ',', '.') : 'N/A' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 20px; color: #6b7280;">No hay servicios para el
                        período seleccionado</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Resumen Final -->
    <div class="summary">
        <h3>✅ Resumen del Período</h3>
        <p><strong>Total de Servicios:</strong> {{ $totalServices }}</p>
        <p><strong>Completados:</strong> {{ $completedServices }} ({{ $completedPercentage }}%)</p>
        <p><strong>Pendientes:</strong> {{ $pendingServices }}</p>
        <p><strong>En Progreso:</strong> {{ $inProgressServices }}</p>
        <p><strong>Ingresos Totales:</strong> ${{ number_format($periodIncome, 0, ',', '.') }}</p>
        <p><strong>Clientes Atendidos:</strong> {{ $uniqueClients }}</p>
        <p><strong>Técnicos Activos:</strong> {{ $activeTechnicians }}</p>
    </div>
</body>

</html>