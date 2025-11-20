@php
$isViewingAsTechnician = (session('view_as_technician', false) && auth()->check() && auth()->user()->hasRole('super-admin')) 
    || request()->is('admin/technician-view/*')
    || (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], '/admin/technician-view/') !== false);
$submitRoute = $isViewingAsTechnician ? route('technician-view.service.checklist.submit', $service) : route('technician.service.checklist.submit', $service);
@endphp
<form method="POST" action="{{ $submitRoute }}" data-stage="monitoreo-analisis" id="analisisForm">
    @csrf
    <div class="form-section">
        <h5>🤖 Análisis de Inteligencia Artificial</h5>
        <div class="ai-analysis-box">
            <div class="ai-loading" id="ai-loading">
                <div class="spinner"></div>
                <p>Analizando datos del monitoreo con IA...</p>
            </div>
            <div class="ai-results" id="ai-results" style="display: none;">
                <div class="ai-section">
                    <h6>🔍 Análisis de Patrones</h6>
                    <div class="ai-content" id="pattern-analysis">
                        <p>El análisis de IA identificó los siguientes patrones:</p>
                        <ul id="patterns-list"></ul>
                    </div>
                </div>
                <div class="ai-section">
                    <h6>⚠️ Alertas y Recomendaciones</h6>
                    <div class="ai-content" id="alerts-analysis">
                        <div id="alerts-list"></div>
                    </div>
                </div>
                <div class="ai-section">
                    <h6>📈 Predicciones</h6>
                    <div class="ai-content" id="predictions-analysis">
                        <p id="predictions-text"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="form-section">
        <h5>✏️ Notas del Técnico sobre el Análisis IA</h5>
        <textarea name="technician_ai_notes" 
                  id="technician_ai_notes" 
                  class="form-textarea" 
                  rows="5"
                  placeholder="Comentarios, observaciones o correcciones al análisis de IA...">{{ old('technician_ai_notes', $service->checklist_data['technician_ai_notes'] ?? '') }}</textarea>
    </div>

    <div class="form-section">
        <h5>✅ Validación del Análisis</h5>
        <div class="validation-grid">
            <label class="checkbox-label-large">
                <input type="checkbox" name="ai_analysis_validated" value="1" {{ old('ai_analysis_validated', $service->checklist_data['ai_analysis_validated'] ?? false) ? 'checked' : '' }}>
                <span>Confirmo que he revisado y validado el análisis de IA</span>
            </label>
        </div>
    </div>

    <input type="hidden" name="checklist_stage" value="monitoreo-analisis">
    <input type="hidden" name="next_stage" value="monitoreo-firma">
    <input type="hidden" name="ai_analysis_data" id="ai_analysis_data">
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Simular análisis de IA (en producción esto se haría con una llamada a API)
    setTimeout(function() {
        generateAIAnalysis();
    }, 2000);
});

function generateAIAnalysis() {
    // Obtener datos del monitoreo
    const monitoringData = {
        totalStations: document.getElementById('total-monitored')?.textContent || 0,
        activeStations: document.getElementById('total-active')?.textContent || 0,
        problems: document.getElementById('total-problems')?.textContent || 0,
        // Agregar más datos según sea necesario
    };

    // Generar análisis (esto sería reemplazado por una llamada real a IA)
    const patterns = [
        'Actividad concentrada en sector norte',
        'Consumo promedio del 45% en cebaderas activas',
        'Tendencia a la baja en últimos 30 días'
    ];

    const alerts = [
        { type: 'warning', message: '3 cebaderas requieren atención inmediata' },
        { type: 'info', message: 'Recomendado aumentar monitoreo en zona este' }
    ];

    const predictions = 'Basado en los datos históricos, se prevé una reducción del 15% en actividad de roedores en los próximos 30 días si se mantiene el protocolo actual.';

    // Mostrar resultados
    displayAIAnalysis(patterns, alerts, predictions);
    
    // Guardar en campo hidden
    document.getElementById('ai_analysis_data').value = JSON.stringify({
        patterns,
        alerts,
        predictions,
        generated_at: new Date().toISOString()
    });
}

function displayAIAnalysis(patterns, alerts, predictions) {
    document.getElementById('ai-loading').style.display = 'none';
    document.getElementById('ai-results').style.display = 'block';

    // Patrones
    const patternsList = document.getElementById('patterns-list');
    patternsList.innerHTML = patterns.map(p => `<li>${p}</li>`).join('');

    // Alertas
    const alertsList = document.getElementById('alerts-list');
    alertsList.innerHTML = alerts.map(alert => `
        <div class="alert alert-${alert.type}">
            ${alert.message}
        </div>
    `).join('');

    // Predicciones
    document.getElementById('predictions-text').textContent = predictions;
}
</script>

<style>
.ai-analysis-box {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 35px;
    min-height: 400px;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
}

.ai-loading {
    text-align: center;
    padding: 60px 20px;
}

.spinner {
    border: 5px solid #f3f3f3;
    border-top: 5px solid #22c55e;
    border-radius: 50%;
    width: 60px;
    height: 60px;
    animation: spin 1s linear infinite;
    margin: 0 auto 25px;
    box-shadow: 0 2px 8px rgba(34,197,94,0.2);
}

.ai-loading p {
    color: #6b7280;
    font-size: 16px;
    font-weight: 500;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.ai-section {
    margin-bottom: 30px;
    padding-bottom: 25px;
    border-bottom: 1px solid #e5e7eb;
}

.ai-section:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.ai-section h6 {
    color: #111827;
    margin-bottom: 18px;
    font-size: 18px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 10px;
}

.ai-content {
    background: #ffffff;
    padding: 25px;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
}

.ai-content ul {
    margin: 15px 0;
    padding-left: 25px;
    list-style: none;
}

.ai-content li {
    margin-bottom: 12px;
    padding-left: 25px;
    position: relative;
    color: #111827;
    font-size: 15px;
    line-height: 1.6;
}

.ai-content li::before {
    content: "→";
    position: absolute;
    left: 0;
    color: #22c55e;
    font-weight: bold;
}

.alert {
    padding: 16px 20px;
    border-radius: 10px;
    margin-bottom: 15px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    font-size: 14px;
    line-height: 1.6;
}

.alert-warning {
    background: #fef3c7;
    border-left: 4px solid #f59e0b;
    color: #92400e;
    font-weight: 500;
}

.alert-info {
    background: #dbeafe;
    border-left: 4px solid #3b82f6;
    color: #1e40af;
    font-weight: 500;
}

.validation-grid {
    display: flex;
    align-items: center;
}

.checkbox-label-large {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 16px;
    cursor: pointer;
}

.checkbox-label-large input[type="checkbox"] {
    width: 20px;
    height: 20px;
    cursor: pointer;
}

@media (max-width: 768px) {
    .form-section {
        padding: 16px;
    }
    
    .form-section h5 {
        font-size: 16px;
    }
    
    .ai-analysis-box {
        padding: 20px;
        min-height: 300px;
    }
    
    .ai-loading {
        padding: 40px 15px;
    }
    
    .spinner {
        width: 50px;
        height: 50px;
    }
    
    .ai-section {
        margin-bottom: 20px;
        padding-bottom: 15px;
    }
    
    .ai-section h6 {
        font-size: 16px;
    }
    
    .ai-content {
        padding: 16px;
    }
}

@media (max-width: 640px) {
    .form-section {
        padding: 12px;
    }
    
    .ai-analysis-box {
        padding: 16px;
        min-height: 250px;
    }
    
    .ai-loading {
        padding: 30px 10px;
    }
    
    .spinner {
        width: 40px;
        height: 40px;
    }
}
</style>

