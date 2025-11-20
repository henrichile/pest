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
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #22c55e;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #111827;
            margin: 0;
            font-size: 18px;
        }
        .header p {
            color: #6b7280;
            margin: 5px 0;
            font-size: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th {
            background-color: #f3f4f6;
            color: #111827;
            padding: 8px;
            text-align: left;
            border: 1px solid #e5e7eb;
            font-size: 9px;
            font-weight: bold;
        }
        td {
            padding: 6px;
            border: 1px solid #e5e7eb;
            font-size: 8px;
            color: #111827;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .summary {
            margin-top: 20px;
            padding: 15px;
            background-color: #f3f4f6;
            border-radius: 5px;
        }
        .summary h3 {
            margin: 0 0 10px 0;
            color: #111827;
            font-size: 12px;
        }
        .summary p {
            margin: 5px 0;
            color: #6b7280;
            font-size: 9px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Servicios</h1>
        <p>Período: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</p>
        <p>Generado el: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <table>
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
                <td>{{ $service->price ? '$' . number_format($service->price, 2, ',', '.') : 'N/A' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center; padding: 20px; color: #6b7280;">No hay servicios para el período seleccionado</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary">
        <h3>Resumen</h3>
        <p><strong>Total de Servicios:</strong> {{ $services->count() }}</p>
        <p><strong>Completados:</strong> {{ $services->where('status', 'completed')->count() }}</p>
        <p><strong>Pendientes:</strong> {{ $services->whereIn('status', ['pendiente', 'pending', 'in_progress'])->count() }}</p>
        <p><strong>Ingresos Totales:</strong> ${{ number_format($services->where('status', 'completed')->whereNotNull('price')->sum('price'), 2, ',', '.') }}</p>
    </div>
</body>
</html>

