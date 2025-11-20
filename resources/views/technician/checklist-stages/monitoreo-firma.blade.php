@php
$isViewingAsTechnician = (session('view_as_technician', false) && auth()->check() && auth()->user()->hasRole('super-admin')) 
    || request()->is('admin/technician-view/*')
    || (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], '/admin/technician-view/') !== false);
$submitRoute = $isViewingAsTechnician ? route('technician-view.service.checklist.submit', $service) : route('technician.service.checklist.submit', $service);
@endphp
<form method="POST" action="{{ $submitRoute }}" data-stage="monitoreo-firma" id="firmaForm">
    @csrf
    <div class="form-section">
        <h5>✍️ Firma del Técnico</h5>
        <div class="signature-section">
            <div class="signature-display">
                @if(auth()->user()->signature)
                    <div class="signature-loaded">
                        <span class="check-icon">✔</span>
                        <p>Firma cargada automáticamente desde tu perfil: <strong>{{ auth()->user()->name }}</strong></p>
                    </div>
                    <div class="signature-preview">
                        <img src="{{ Storage::url(auth()->user()->signature) }}" alt="Firma del Técnico" class="signature-image">
                    </div>
                @else
                    <div class="signature-placeholder">
                        <canvas id="signature-canvas" width="600" height="200"></canvas>
                        <div class="signature-actions">
                            <button type="button" class="btn-clear" onclick="clearSignature()">Limpiar</button>
                        </div>
                        <input type="hidden" name="technician_signature" id="technician_signature">
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="form-section">
        <h5>👤 Información del Firmante</h5>
        <div class="form-grid">
            <div>
                <label>Nombre Completo <span class="required">*</span></label>
                <input type="text" 
                       name="signer_name" 
                       id="signer_name" 
                       class="form-input" 
                       value="{{ old('signer_name', $service->client->name ?? '') }}"
                       required>
            </div>
            <div>
                <label>Cargo / Relación</label>
                <select name="signer_position" id="signer_position" class="form-select">
                    <option value="">Seleccionar</option>
                    <option value="encargado" {{ old('signer_position', $service->checklist_data['signer_position'] ?? '') === 'encargado' ? 'selected' : '' }}>Encargado</option>
                    <option value="gerente" {{ old('signer_position', $service->checklist_data['signer_position'] ?? '') === 'gerente' ? 'selected' : '' }}>Gerente</option>
                    <option value="propietario" {{ old('signer_position', $service->checklist_data['signer_position'] ?? '') === 'propietario' ? 'selected' : '' }}>Propietario</option>
                    <option value="representante" {{ old('signer_position', $service->checklist_data['signer_position'] ?? '') === 'representante' ? 'selected' : '' }}>Representante Legal</option>
                    <option value="otro" {{ old('signer_position', $service->checklist_data['signer_position'] ?? '') === 'otro' ? 'selected' : '' }}>Otro</option>
                </select>
            </div>
        </div>
    </div>

    <div class="form-section">
        <h5>📋 Resumen Final del Servicio</h5>
        <div class="summary-box">
            <div class="summary-item">
                <span class="summary-label">Fecha de Monitoreo:</span>
                <span class="summary-value">{{ $service->checklist_data['monitoring_date'] ?? date('Y-m-d') }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Cebaderas Monitoreadas:</span>
                <span class="summary-value">{{ $service->checklist_data['total_monitored'] ?? 0 }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Estado General:</span>
                <span class="summary-value">{{ ucfirst($service->checklist_data['activity_level'] ?? 'N/A') }}</span>
            </div>
        </div>
    </div>

    <div class="form-section">
        <div class="final-actions">
            <div class="checkbox-label-large">
                <input type="checkbox" name="service_completed" value="1" required>
                <span>Confirmo que el servicio ha sido completado según los estándares establecidos</span>
            </div>
        </div>
    </div>

    <input type="hidden" name="checklist_stage" value="monitoreo-firma">
    <input type="hidden" name="next_stage" value="completed">
</form>

<script>
let canvas, ctx;
let isDrawing = false;

document.addEventListener('DOMContentLoaded', function() {
    canvas = document.getElementById('signature-canvas');
    if (canvas) {
        ctx = canvas.getContext('2d');
        ctx.strokeStyle = '#000';
        ctx.lineWidth = 2;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';

        canvas.addEventListener('mousedown', startDrawing);
        canvas.addEventListener('mousemove', draw);
        canvas.addEventListener('mouseup', stopDrawing);
        canvas.addEventListener('mouseout', stopDrawing);

        // Touch events para móviles
        canvas.addEventListener('touchstart', handleTouch);
        canvas.addEventListener('touchmove', handleTouch);
        canvas.addEventListener('touchend', stopDrawing);
    }
});

function startDrawing(e) {
    isDrawing = true;
    const rect = canvas.getBoundingClientRect();
    ctx.beginPath();
    ctx.moveTo(e.clientX - rect.left, e.clientY - rect.top);
}

function draw(e) {
    if (!isDrawing) return;
    const rect = canvas.getBoundingClientRect();
    ctx.lineTo(e.clientX - rect.left, e.clientY - rect.top);
    ctx.stroke();
    updateSignatureData();
}

function stopDrawing() {
    if (isDrawing) {
        isDrawing = false;
        updateSignatureData();
    }
}

function handleTouch(e) {
    e.preventDefault();
    const touch = e.touches[0];
    const mouseEvent = new MouseEvent(e.type === 'touchstart' ? 'mousedown' : 
                                      e.type === 'touchmove' ? 'mousemove' : 'mouseup', {
        clientX: touch.clientX,
        clientY: touch.clientY
    });
    canvas.dispatchEvent(mouseEvent);
}

function clearSignature() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    document.getElementById('technician_signature').value = '';
}

function updateSignatureData() {
    const dataURL = canvas.toDataURL('image/png');
    document.getElementById('technician_signature').value = dataURL;
}

// Validar antes de enviar
document.getElementById('firmaForm')?.addEventListener('submit', function(e) {
    const signature = document.getElementById('technician_signature')?.value || 
                     (document.querySelector('.signature-image') ? 'loaded' : '');
    if (!signature) {
        e.preventDefault();
        alert('Por favor, proporciona una firma antes de completar el servicio.');
        return false;
    }
});
</script>

<style>
.signature-section {
    background: #ffffff;
    padding: 35px;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
}

.signature-loaded {
    background: #f0fdf4;
    border: 1px solid #22c55e;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 25px;
    text-align: center;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
}

.check-icon {
    font-size: 32px;
    color: #22c55e;
    display: block;
    margin-bottom: 12px;
    filter: drop-shadow(0 2px 4px rgba(34,197,94,0.3));
}

.signature-loaded p {
    color: #166534;
    font-weight: 500;
    margin: 0;
    font-size: 15px;
}

.signature-loaded strong {
    color: #166534;
    font-weight: 700;
}

.signature-preview {
    background: #ffffff;
    padding: 25px;
    border-radius: 12px;
    text-align: center;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
}

.signature-image {
    max-width: 100%;
    max-height: 180px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.signature-placeholder {
    background: #ffffff;
    padding: 30px;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
}

#signature-canvas {
    border: 3px solid #dee2e6;
    border-radius: 12px;
    cursor: crosshair;
    display: block;
    width: 100%;
    max-width: 600px;
    margin: 0 auto;
    background: white;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: border-color 0.3s ease;
}

#signature-canvas:hover {
    border-color: #22c55e;
}

@media (max-width: 768px) {
    .form-section {
        padding: 16px;
    }
    
    .form-section h5 {
        font-size: 16px;
    }
    
    .signature-container {
        flex-direction: column;
    }
    
    .signature-box {
        width: 100%;
        margin-bottom: 20px;
    }
    
    #signature-canvas {
        max-width: 100%;
    }
    
    .signature-preview img {
        max-width: 100%;
        max-height: 150px;
    }
}

@media (max-width: 640px) {
    .form-section {
        padding: 12px;
    }
    
    #signature-canvas {
        max-width: 100%;
    }
}

.signature-actions {
    margin-top: 20px;
    text-align: center;
}

.btn-clear {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 700;
    font-size: 14px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(220,53,69,0.3);
}

.btn-clear:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(220,53,69,0.4);
}

.summary-box {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
}

.summary-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid #e5e7eb;
}

.summary-item:last-child {
    border-bottom: none;
}

.summary-label {
    font-weight: 600;
    color: #6b7280;
    font-size: 15px;
}

.summary-value {
    color: #111827;
    font-weight: 500;
    font-size: 16px;
}

.final-actions {
    background: #fef3c7;
    border: 1px solid #f59e0b;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
}
</style>

