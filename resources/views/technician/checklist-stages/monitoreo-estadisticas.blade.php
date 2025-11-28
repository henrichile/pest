@php
$isViewingAsTechnician = (session('view_as_technician', false) && auth()->check() && auth()->user()->hasRole('super-admin')) 
    || request()->is('admin/technician-view/*')
    || (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], '/admin/technician-view/') !== false);
$submitRoute = $isViewingAsTechnician ? route('admin.technician-view.service.checklist.submit', $service) : route('technician.service.checklist.submit', $service);

// Obtener datos de todas las etapas anteriores
$monitoreoDatos = $service->checklist_data['monitoreo_datos'] ?? $service->checklist_data ?? [];
$monitoreoCompleto = $service->checklist_data['monitoreo_completo'] ?? [];
$baitStations = $monitoreoCompleto['bait_stations'] ?? [];

// Obtener plagas detectadas de la etapa de datos
$pestsDetected = $monitoreoDatos['pests_detected_list'] ?? $monitoreoDatos['pests_detected'] ?? [];
if (is_string($pestsDetected)) {
    $pestsDetected = json_decode($pestsDetected, true) ?? [];
}
if (!is_array($pestsDetected)) {
    $pestsDetected = [];
}

// Calcular estadísticas desde las cebaderas
$totalMonitoreadas = count($baitStations);
$totalActivas = 0;
$totalConProblemas = 0;
$totalConsumo = 0;
$totalCapturas = 0;
$consumoPromedio = 0;
$nivelActual = 'bajo';

foreach ($baitStations as $station) {
    // Verificar si está activa (tiene producto y no está bloqueada/sustraída)
    $isActive = true;
    $hasProblems = false;
    
    if (isset($station['observations']) && is_array($station['observations'])) {
        if (in_array('bloqueada', $station['observations']) || in_array('sustraida', $station['observations'])) {
            $isActive = false;
        }
        if (in_array('bloqueada', $station['observations']) || 
            in_array('sustraida', $station['observations']) || 
            in_array('hongos', $station['observations']) || 
            in_array('sucia', $station['observations'])) {
            $hasProblems = true;
        }
    }
    
    if ($isActive) {
        $totalActivas++;
    }
    if ($hasProblems) {
        $totalConProblemas++;
    }
    
    // Calcular consumo (si tiene consumo_50, significa 50% de consumo)
    if (isset($station['observations']) && is_array($station['observations']) && in_array('consumo_50', $station['observations'])) {
        $totalConsumo += 50;
    } elseif (isset($station['consumption'])) {
        $totalConsumo += floatval($station['consumption']);
    }
    
    // Contar capturas (si hay trampas con capturas)
    if (isset($station['captures'])) {
        $totalCapturas += intval($station['captures']);
    }
}

if ($totalMonitoreadas > 0) {
    $consumoPromedio = ($totalConsumo / $totalMonitoreadas);
    // Determinar nivel actual basado en consumo promedio
    if ($consumoPromedio > 50) {
        $nivelActual = 'critico';
    } elseif ($consumoPromedio > 30) {
        $nivelActual = 'alto';
    } elseif ($consumoPromedio > 10) {
        $nivelActual = 'medio';
    } else {
        $nivelActual = 'bajo';
    }
}

// Obtener datos históricos para el gráfico (si existen)
$historicalData = $monitoreoCompleto['historical_data'] ?? [];

// Si no hay datos históricos, crear datos basados en el monitoreo actual
if (empty($historicalData) && $totalMonitoreadas > 0) {
    $today = \Carbon\Carbon::today();
    $historicalData = [];
    for ($i = 6; $i >= 0; $i--) {
        $date = $today->copy()->subDays($i);
        $historicalData[] = [
            'date' => $date->format('Y-m-d'),
            'consumption_percent' => $i === 0 ? $consumoPromedio : 0,
            'captures' => $i === 0 ? $totalCapturas : 0
        ];
    }
}
@endphp

<form method="POST" action="{{ $submitRoute }}" data-stage="monitoreo-estadisticas" id="estadisticasForm">
    @csrf
    
    <!-- Resumen de Monitoreo -->
    <div class="stats-section">
        <h5 class="stats-title">📊 Resumen de Monitoreo</h5>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">🐀</div>
                <div class="stat-info">
                    <div class="stat-label">CEBADERAS MONITOREADAS</div>
                    <div class="stat-value">{{ $totalMonitoreadas }}</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="color: #22c55e;">✅</div>
                <div class="stat-info">
                    <div class="stat-label">CEBADERAS ACTIVAS</div>
                    <div class="stat-value" style="color: #22c55e;">{{ $totalActivas }}</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="color: #f59e0b;">⚠️</div>
                <div class="stat-info">
                    <div class="stat-label">CON PROBLEMAS</div>
                    <div class="stat-value" style="color: #f59e0b;">{{ $totalConProblemas }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Métricas Clave -->
    <div class="metrics-section">
        <div class="metric-item">
            <div class="metric-label">Total Monitoreos</div>
            <div class="metric-value">{{ $totalMonitoreadas }}</div>
        </div>
        <div class="metric-item">
            <div class="metric-label">Consumo Promedio</div>
            <div class="metric-value" style="color: {{ $consumoPromedio > 30 ? '#ef4444' : ($consumoPromedio > 10 ? '#f59e0b' : '#22c55e') }};">
                {{ number_format($consumoPromedio, 1) }}%
            </div>
        </div>
        <div class="metric-item">
            <div class="metric-label">Nivel Actual</div>
            <div class="metric-badge nivel-{{ $nivelActual }}">
                {{ strtoupper($nivelActual) }}
            </div>
        </div>
    </div>

    <!-- Plagas Detectadas -->
    @if(count($pestsDetected) > 0)
    <div class="pests-section">
        <h5 class="stats-title">🔍 Plagas Detectadas</h5>
        <div class="pests-list">
            @foreach($pestsDetected as $pest)
                <span class="pest-tag">{{ $pest }}</span>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Evolución del Consumo -->
    <div class="chart-section">
        <h5 class="stats-title">📊 Evolución del Consumo</h5>
        <div class="chart-wrapper">
            <canvas id="consumptionChart"></canvas>
        </div>
        <div class="chart-legend">
            <div class="legend-item">
                <span class="legend-line" style="background: #ef4444;"></span>
                <span>% Consumo</span>
            </div>
            <div class="legend-item">
                <span class="legend-line" style="background: #6b7280;"></span>
                <span>Capturas</span>
            </div>
        </div>
    </div>

    <input type="hidden" name="checklist_stage" value="monitoreo-estadisticas">
    <input type="hidden" name="next_stage" value="monitoreo-analisis">
</form>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
// Datos para el gráfico
const historicalData = @json($historicalData ?? []);
const consumptionData = historicalData.map(d => d.consumption_percent || 0);
const capturesData = historicalData.map(d => d.captures || 0);
const dates = historicalData.map(d => {
    if (d.date) {
        const date = new Date(d.date);
        return date.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit' });
    }
    return '';
});

// Si no hay datos históricos, crear datos de ejemplo basados en el monitoreo actual
let chartLabels = dates.length > 0 ? dates : [];
let chartConsumptionData = consumptionData.length > 0 ? consumptionData : [];
let chartCapturesData = capturesData.length > 0 ? capturesData : [];

// Si no hay datos, crear datos de ejemplo para los últimos 7 días
if (chartLabels.length === 0) {
    const today = new Date();
    for (let i = 6; i >= 0; i--) {
        const date = new Date(today);
        date.setDate(date.getDate() - i);
        chartLabels.push(date.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit' }));
        chartConsumptionData.push(0);
        chartCapturesData.push(0);
    }
}

// Crear gráfico
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('consumptionChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [
                    {
                        label: '% Consumo',
                        data: chartConsumptionData,
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Capturas',
                        data: chartCapturesData,
                        borderColor: '#6b7280',
                        backgroundColor: 'rgba(107, 114, 128, 0.1)',
                        tension: 0.4,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 4,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }
});
</script>

<style>
/* Resetear estilos de form-section para esta vista */
#estadisticasForm .form-section {
    margin-bottom: 0;
    padding: 0;
    background: transparent;
    border: none;
    box-shadow: none;
}

.stats-section, .metrics-section, .pests-section, .chart-section {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 14px;
    margin-bottom: 16px;
    position: relative;
    z-index: 1;
    overflow: visible;
}

.stats-title {
    color: #111827;
    margin: 0 0 12px 0;
    font-size: 14px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 6px;
    padding-bottom: 8px;
    border-bottom: 1px solid #e5e7eb;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
}

.stat-card {
    background: #f9fafb;
    padding: 12px;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    display: flex;
    align-items: center;
    gap: 10px;
}

.stat-icon {
    font-size: 24px;
    flex-shrink: 0;
    line-height: 1;
}

.stat-info {
    flex: 1;
    min-width: 0;
}

.stat-label {
    font-size: 9px;
    color: #6b7280;
    margin-bottom: 2px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.2px;
    line-height: 1.2;
}

.stat-value {
    font-size: 20px;
    font-weight: 700;
    color: #111827;
    line-height: 1;
}

.metrics-section {
    display: flex;
    gap: 12px;
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 14px;
    margin-bottom: 16px;
}

.metric-item {
    flex: 1;
    text-align: center;
    padding: 8px;
}

.metric-label {
    font-size: 10px;
    color: #6b7280;
    margin-bottom: 4px;
    font-weight: 500;
}

.metric-value {
    font-size: 18px;
    font-weight: 700;
    color: #111827;
    line-height: 1;
}

.metric-badge {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 12px;
    font-size: 10px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.2px;
}

.nivel-bajo {
    background: #22c55e;
    color: white;
}

.nivel-medio {
    background: #f59e0b;
    color: white;
}

.nivel-alto {
    background: #ef4444;
    color: white;
}

.nivel-critico {
    background: #dc2626;
    color: white;
}

.pests-list {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-top: 8px;
}

.pest-tag {
    background: #d4edda;
    color: #155724;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    border: 1px solid #28a745;
}

.chart-wrapper {
    background: #ffffff;
    padding: 12px;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    margin-bottom: 10px;
    height: 220px;
    position: relative;
    overflow: hidden;
    z-index: 1;
}

.chart-wrapper canvas {
    max-width: 100% !important;
    max-height: 100% !important;
    position: relative !important;
}

.chart-legend {
    display: flex;
    gap: 12px;
    justify-content: center;
    flex-wrap: wrap;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 11px;
    color: #6b7280;
}

.legend-line {
    width: 20px;
    height: 2px;
    border-radius: 2px;
}

@media (max-width: 768px) {
    .stats-section, .metrics-section, .pests-section, .chart-section {
        padding: 12px;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
        gap: 8px;
    }
    
    .metrics-section {
        flex-direction: column;
    }
    
    .metric-item {
        width: 100%;
    }
    
    .chart-wrapper {
        height: 180px;
        padding: 10px;
    }
    
    .section-title {
        font-size: 16px;
    }
}

@media (max-width: 640px) {
    .stats-section, .metrics-section, .pests-section, .chart-section {
        padding: 10px;
    }
    
    .chart-wrapper {
        height: 160px;
    }
    
    .stat-value {
        font-size: 20px;
    }
    
    .stat-label {
        font-size: 11px;
    }
}
</style>
