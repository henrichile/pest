@extends('layouts.app-tec')

@section('content')

<!-- Etapa 6: Descripción del Servicio -->
<div class="stage-title">Descripción del Servicio y Sugerencias</div>
<div class="stage-instruction">Complete la descripción final del servicio realizado</div>

<form method="POST" action="{{ route("technician.service.checklist.submit", $service) }}" data-stage="description" id="checklistForm">
    @csrf
    <input type="hidden" name="next_stage" value="completed">
    <input type="hidden" name="current_stage" value="description">    
    <div class="form-group">
        <label>Descripción del Servicio y Sugerencias</label>
        <textarea name="service_description" rows="8" 
                  placeholder="Describa el servicio realizado, resultados obtenidos, recomendaciones para el cliente, próximos pasos, sugerencias de mejora, etc...">{{ $service->checklist_data["description"]["service_description"] ?? "" }}</textarea>
        <p class="field-help">Incluya una descripción completa del servicio, resultados obtenidos, recomendaciones y sugerencias para el cliente.</p>
    </div>
    
    <!-- Resumen del Checklist -->
    <div class="checklist-summary">
        <h5>Resumen del Checklist</h5>
        <div class="summary-grid">
            <div class="summary-item">
                <span class="summary-label">Producto Aplicado:</span>
                <p class="summary-value">{{ $service->checklist_data["products"]["applied_product"] ?? "No especificado" }}</p>
            </div>
            <div class="summary-item">
                <span class="summary-label">Sitios Tratados:</span>
                <p class="summary-value">{{ Str::limit($service->checklist_data["sites"]["treated_sites"] ?? "No especificado", 50) }}</p>
            </div>
            <div class="summary-item">
                <span class="summary-label">Observaciones:</span>
                <p class="summary-value">{{ count($service->checklist_data["observations"] ?? []) }} registradas</p>
            </div>
            <div class="summary-item">
                <span class="summary-label">Resultados:</span>
                <p class="summary-value">{{ count($service->checklist_data["results"]["observed_results"] ?? []) }} tipos encontrados</p>
            </div>
        </div>
    </div>
    
    <!-- Campos de Firma Digital -->
    <div class="signatures-section">
        <h5>Firmas Digitales de Confirmación</h5>
        <div class="signatures-grid">
            <div class="signature-group">
                <label>Firma del Técnico</label>
                <div class="signature-pad">
                    <canvas id="technicianSignature" width="280" height="120"></canvas>
                    <div class="signature-controls">
                        <button type="button" class="clear-signature" data-canvas="technicianSignature">Limpiar</button>
                        <span class="signature-status" id="technicianStatus">Sin firma</span>
                    </div>
                </div>
                <p class="signature-help">Dibuje su firma en el área superior</p>
                <input type="hidden" name="technician_signature" id="technicianSignatureData">
            </div>
            
            <div class="signature-group">
                <label>Firma del Cliente</label>
                <div class="signature-pad">
                    <canvas id="clientSignature" width="280" height="120"></canvas>
                    <div class="signature-controls">
                        <button type="button" class="clear-signature" data-canvas="clientSignature">Limpiar</button>
                        <span class="signature-status" id="clientStatus">Sin firma</span>
                    </div>
                </div>
                <p class="signature-help">Dibuje su firma en el área superior</p>
                <input type="hidden" name="client_signature" id="clientSignatureData">
            </div>
        </div>
        
        <div class="signature-date">
            <label>Fecha de Finalización</label>
            <input type="date" name="completion_date" 
                   value="{{ $service->checklist_data["description"]["completion_date"] ?? date("Y-m-d") }}"
                   required>
        </div>
    </div>
    
    <div class="buttons-container">
        <a href="{{ route("technician.service.checklist.stage", ["service" => $service, "stage" => "sites"]) }}" class="back-button">
            <span class="arrow">←</span> Anterior
        </a>
        <button type="submit" class="next-button" id="finalizeButton" disabled>
            Finalizar Checklist ✓
        </button>
    </div>
</form>

<style>
.checklist-summary {
    background: #f8f9fa;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    padding: 20px;
    margin: 20px 0;
}

.checklist-summary h5 {
    color: #1a472a;
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 15px;
}

.summary-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

.summary-item {
    display: flex;
    flex-direction: column;
}

.summary-label {
    font-weight: 600;
    color: #333;
    font-size: 14px;
    margin-bottom: 5px;
}

.summary-value {
    color: #666;
    font-size: 14px;
    margin: 0;
    line-height: 1.4;
}

.signatures-section {
    background: #f8f9fa;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    padding: 20px;
    margin: 20px 0;
    max-width: 100%;
    overflow: hidden;
}

.signatures-section h5 {
    color: #1a472a;
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 20px;
    text-align: center;
}

.signatures-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
    max-width: 100%;
}

.signature-group {
    display: flex;
    flex-direction: column;
    align-items: center;
    max-width: 100%;
    overflow: hidden;
}

.signature-group label {
    font-weight: 600;
    color: #333;
    font-size: 16px;
    margin-bottom: 15px;
    text-align: center;
}

.signature-pad {
    background: white;
    border: 2px solid #ddd;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    width: 100%;
    max-width: 320px;
    box-sizing: border-box;
}

.signature-pad canvas {
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    cursor: crosshair;
    display: block;
    margin: 0 auto;
    max-width: 100%;
    height: auto;
}

.signature-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 10px;
    padding: 0 5px;
    width: 100%;
}

.clear-signature {
    background: #dc3545;
    color: white;
    border: none;
    padding: 6px 12px;
    border-radius: 4px;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    flex-shrink: 0;
}

.clear-signature:hover {
    background: #c82333;
    transform: translateY(-1px);
}

.signature-status {
    font-size: 12px;
    font-weight: 600;
    color: #dc3545;
    text-align: right;
    flex: 1;
}

.signature-status.signed {
    color: #28a745;
}

.signature-help {
    font-size: 12px;
    color: #666;
    text-align: center;
    margin: 0;
    font-style: italic;
    max-width: 100%;
}

.signature-date {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #e0e0e0;
}

.signature-date label {
    font-weight: 600;
    color: #333;
    font-size: 14px;
    margin-bottom: 8px;
}

.signature-date input {
    padding: 8px 12px;
    border: 2px solid #ddd;
    border-radius: 5px;
    font-size: 14px;
    color: #333;
    background: white;
    transition: all 0.3s ease;
}

.signature-date input:focus {
    border-color: #1a472a;
    outline: none;
}

.next-button:disabled {
    background: #ccc;
    cursor: not-allowed;
    transform: none;
}

.next-button:disabled:hover {
    background: #ccc;
    transform: none;
}

/* Responsive Design */
@media (max-width: 768px) {
    .summary-grid {
        grid-template-columns: 1fr;
    }
    
    .signatures-grid {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .signature-pad {
        max-width: 100%;
        padding: 10px;
    }
    
    .signature-pad canvas {
        width: 100%;
        max-width: 250px;
        height: 100px;
    }
    
    .signatures-section {
        padding: 15px;
    }
}

@media (max-width: 480px) {
    .signature-pad canvas {
        max-width: 200px;
        height: 80px;
    }
    
    .signature-controls {
        flex-direction: column;
        gap: 8px;
        align-items: center;
    }
    
    .signature-status {
        text-align: center;
    }
}
</style>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configuración de canvas para firmas
    const technicianCanvas = document.getElementById('technicianSignature');
    const clientCanvas = document.getElementById('clientSignature');
    const technicianCtx = technicianCanvas.getContext('2d');
    const clientCtx = clientCanvas.getContext('2d');
    
    // Configurar canvas
    function setupCanvas(canvas, ctx) {
        ctx.strokeStyle = '#1a472a';
        ctx.lineWidth = 2;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';
        
        // Limpiar canvas
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
    }
    
    setupCanvas(technicianCanvas, technicianCtx);
    setupCanvas(clientCanvas, clientCtx);
    
    // Variables para tracking de dibujo
    let isDrawing = false;
    let lastX = 0;
    let lastY = 0;
    
    // Función para iniciar dibujo
    function startDrawing(e, ctx) {
        isDrawing = true;
        const rect = e.target.getBoundingClientRect();
        lastX = e.clientX - rect.left;
        lastY = e.clientY - rect.top;
    }
    
    // Función para dibujar
    function draw(e, ctx) {
        if (!isDrawing) return;
        
        const rect = e.target.getBoundingClientRect();
        const currentX = e.clientX - rect.left;
        const currentY = e.clientY - rect.top;
        
        ctx.beginPath();
        ctx.moveTo(lastX, lastY);
        ctx.lineTo(currentX, currentY);
        ctx.stroke();
        
        lastX = currentX;
        lastY = currentY;
    }
    
    // Función para detener dibujo
    function stopDrawing(ctx, canvasId) {
        if (isDrawing) {
            isDrawing = false;
            updateSignatureStatus(canvasId);
        }
    }
    
    // Eventos para canvas del técnico
    technicianCanvas.addEventListener('mousedown', (e) => startDrawing(e, technicianCtx));
    technicianCanvas.addEventListener('mousemove', (e) => draw(e, technicianCtx));
    technicianCanvas.addEventListener('mouseup', () => stopDrawing(technicianCtx, 'technicianSignature'));
    technicianCanvas.addEventListener('mouseout', () => stopDrawing(technicianCtx, 'technicianSignature'));
    
    // Eventos táctiles para móviles
    technicianCanvas.addEventListener('touchstart', (e) => {
        e.preventDefault();
        const touch = e.touches[0];
        const mouseEvent = new MouseEvent('mousedown', {
            clientX: touch.clientX,
            clientY: touch.clientY
        });
        technicianCanvas.dispatchEvent(mouseEvent);
    });
    
    technicianCanvas.addEventListener('touchmove', (e) => {
        e.preventDefault();
        const touch = e.touches[0];
        const mouseEvent = new MouseEvent('mousemove', {
            clientX: touch.clientX,
            clientY: touch.clientY
        });
        technicianCanvas.dispatchEvent(mouseEvent);
    });
    
    technicianCanvas.addEventListener('touchend', (e) => {
        e.preventDefault();
        const mouseEvent = new MouseEvent('mouseup', {});
        technicianCanvas.dispatchEvent(mouseEvent);
    });
    
    // Eventos para canvas del cliente
    clientCanvas.addEventListener('mousedown', (e) => startDrawing(e, clientCtx));
    clientCanvas.addEventListener('mousemove', (e) => draw(e, clientCtx));
    clientCanvas.addEventListener('mouseup', () => stopDrawing(clientCtx, 'clientSignature'));
    clientCanvas.addEventListener('mouseout', () => stopDrawing(clientCtx, 'clientSignature'));
    
    // Eventos táctiles para móviles - Cliente
    clientCanvas.addEventListener('touchstart', (e) => {
        e.preventDefault();
        const touch = e.touches[0];
        const mouseEvent = new MouseEvent('mousedown', {
            clientX: touch.clientX,
            clientY: touch.clientY
        });
        clientCanvas.dispatchEvent(mouseEvent);
    });
    
    clientCanvas.addEventListener('touchmove', (e) => {
        e.preventDefault();
        const touch = e.touches[0];
        const mouseEvent = new MouseEvent('mousemove', {
            clientX: touch.clientX,
            clientY: touch.clientY
        });
        clientCanvas.dispatchEvent(mouseEvent);
    });
    
    clientCanvas.addEventListener('touchend', (e) => {
        e.preventDefault();
        const mouseEvent = new MouseEvent('mouseup', {});
        clientCanvas.dispatchEvent(mouseEvent);
    });
    
    // Función para limpiar firma
    function clearSignature(canvasId) {
        const canvas = document.getElementById(canvasId);
        const ctx = canvas.getContext('2d');
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        
        updateSignatureStatus(canvasId);
    }
    
    // Función para actualizar estado de firma
    function updateSignatureStatus(canvasId) {
        const canvas = document.getElementById(canvasId);
        const ctx = canvas.getContext('2d');
        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        const data = imageData.data;
        
        let hasSignature = false;
        for (let i = 0; i < data.length; i += 4) {
            if (data[i] !== 255 || data[i + 1] !== 255 || data[i + 2] !== 255) {
                hasSignature = true;
                break;
            }
        }
        
        const statusElement = document.getElementById(canvasId.replace('Signature', 'Status'));
        const hiddenInput = document.getElementById(canvasId + 'Data');
        
        if (hasSignature) {
            statusElement.textContent = 'Firmado ✓';
            statusElement.className = 'signature-status signed';
            hiddenInput.value = canvas.toDataURL('image/png');
        } else {
            statusElement.textContent = 'Sin firma';
            statusElement.className = 'signature-status';
            hiddenInput.value = '';
        }
        
        checkFormCompletion();
    }
    
    // Función para verificar si el formulario está completo
    function checkFormCompletion() {
        const technicianSigned = document.getElementById('technicianSignatureData').value !== '';
        const clientSigned = document.getElementById('clientSignatureData').value !== '';
        const finalizeButton = document.getElementById('finalizeButton');
        
        if (technicianSigned && clientSigned) {
            finalizeButton.disabled = false;
        } else {
            finalizeButton.disabled = true;
        }
    }
    
    // Eventos para botones de limpiar
    document.querySelectorAll('.clear-signature').forEach(button => {
        button.addEventListener('click', function() {
            const canvasId = this.getAttribute('data-canvas');
            clearSignature(canvasId);
        });
    });
    
    // Prevenir envío del formulario si no hay firmas
    document.getElementById('checklistForm').addEventListener('submit', function(e) {
        const technicianSigned = document.getElementById('technicianSignatureData').value !== '';
        const clientSigned = document.getElementById('clientSignatureData').value !== '';
        
        if (!technicianSigned || !clientSigned) {
            e.preventDefault();
            alert('Por favor, complete ambas firmas antes de finalizar el checklist.');
        }
    });
});
</script>
@endsection