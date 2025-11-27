@php
$isViewingAsTechnician = (session('view_as_technician', false) && auth()->check() && auth()->user()->hasRole('super-admin')) 
    || request()->is('admin/technician-view/*')
    || (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], '/admin/technician-view/') !== false);
$submitRoute = $isViewingAsTechnician ? route('technician-view.service.checklist.submit', $service) : route('technician.service.checklist.submit', $service);
@endphp
<form method="POST" action="{{ $submitRoute }}" data-stage="monitoreo-firma" id="firmaForm">
    @csrf
    <div class="form-section">
        <h5>✍️ Firmas</h5>
        
        <div class="signature-container" style="display: flex; gap: 20px; flex-wrap: wrap;">
            <!-- Firma del Técnico -->
            <div class="signature-box" style="flex: 1; min-width: 300px;">
                <h6 style="margin-bottom: 15px; color: #4b5563; font-weight: 600;">Firma del Técnico</h6>
                <div class="signature-display">
                    @if(auth()->user()->signature)
                        <div class="signature-loaded">
                            <span class="check-icon">✔</span>
                            <p>Firma cargada: <strong>{{ auth()->user()->name }}</strong></p>
                        </div>
                        <div class="signature-preview">
                            <img src="{{ Storage::url(auth()->user()->signature) }}" alt="Firma del Técnico" class="signature-image">
                        </div>
                    @else
                        <div class="signature-placeholder">
                            <canvas id="technician-canvas" class="signature-canvas" width="400" height="200"></canvas>
                            <div class="signature-actions">
                                <button type="button" class="btn-clear" onclick="signaturePad.clear('technician')">Limpiar</button>
                            </div>
                            <input type="hidden" name="technician_signature" id="technician_signature">
                        </div>
                    @endif
                </div>
            </div>

            <!-- Firma del Cliente -->
            <div class="signature-box" style="flex: 1; min-width: 300px;">
                <h6 style="margin-bottom: 15px; color: #4b5563; font-weight: 600;">Firma del Cliente</h6>
                <div class="signature-display">
                    <div class="signature-placeholder">
                        <canvas id="client-canvas" class="signature-canvas" width="400" height="200"></canvas>
                        <div class="signature-actions">
                            <button type="button" class="btn-clear" onclick="signaturePad.clear('client')">Limpiar</button>
                        </div>
                        <input type="hidden" name="client_signature" id="client_signature">
                    </div>
                </div>
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
const signaturePad = {
    pads: {},
    isDrawing: false,
    currentCanvas: null,
    currentCtx: null,

    init: function() {
        // Initialize Technician Canvas if it exists
        const techCanvas = document.getElementById('technician-canvas');
        if (techCanvas) this.setupCanvas(techCanvas, 'technician');

        // Initialize Client Canvas
        const clientCanvas = document.getElementById('client-canvas');
        if (clientCanvas) this.setupCanvas(clientCanvas, 'client');
    },

    setupCanvas: function(canvas, id) {
        const ctx = canvas.getContext('2d');
        ctx.strokeStyle = '#000';
        ctx.lineWidth = 2;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';

        this.pads[id] = { canvas, ctx };

        // Mouse events
        canvas.addEventListener('mousedown', (e) => this.startDrawing(e, id));
        canvas.addEventListener('mousemove', (e) => this.draw(e, id));
        canvas.addEventListener('mouseup', () => this.stopDrawing());
        canvas.addEventListener('mouseout', () => this.stopDrawing());

        // Touch events
        canvas.addEventListener('touchstart', (e) => this.handleTouch(e, id));
        canvas.addEventListener('touchmove', (e) => this.handleTouch(e, id));
        canvas.addEventListener('touchend', () => this.stopDrawing());
    },

    startDrawing: function(e, id) {
        this.isDrawing = true;
        this.currentCanvas = this.pads[id].canvas;
        this.currentCtx = this.pads[id].ctx;
        
        const rect = this.currentCanvas.getBoundingClientRect();
        this.currentCtx.beginPath();
        this.currentCtx.moveTo(e.clientX - rect.left, e.clientY - rect.top);
    },

    draw: function(e, id) {
        if (!this.isDrawing || this.currentCanvas !== this.pads[id].canvas) return;
        
        const rect = this.currentCanvas.getBoundingClientRect();
        this.currentCtx.lineTo(e.clientX - rect.left, e.clientY - rect.top);
        this.currentCtx.stroke();
        this.updateInput(id);
    },

    stopDrawing: function() {
        if (this.isDrawing) {
            this.isDrawing = false;
            this.currentCanvas = null;
            this.currentCtx = null;
        }
    },

    handleTouch: function(e, id) {
        e.preventDefault();
        const touch = e.touches[0];
        const mouseEvent = new MouseEvent(e.type === 'touchstart' ? 'mousedown' : 
                                          e.type === 'touchmove' ? 'mousemove' : 'mouseup', {
            clientX: touch.clientX,
            clientY: touch.clientY
        });
        this.pads[id].canvas.dispatchEvent(mouseEvent);
    },

    clear: function(id) {
        const pad = this.pads[id];
        if (pad) {
            pad.ctx.clearRect(0, 0, pad.canvas.width, pad.canvas.height);
            document.getElementById(id + '_signature').value = '';
        }
    },

    updateInput: function(id) {
        const pad = this.pads[id];
        if (pad) {
            const dataURL = pad.canvas.toDataURL('image/png');
            document.getElementById(id + '_signature').value = dataURL;
        }
    }
};

document.addEventListener('DOMContentLoaded', function() {
    signaturePad.init();
});

// Validar antes de enviar
document.getElementById('firmaForm')?.addEventListener('submit', function(e) {
    // Validar firma técnico (si no está cargada desde perfil)
    const techSigInput = document.getElementById('technician_signature');
    const techSigLoaded = document.querySelector('.signature-loaded');
    
    if (!techSigLoaded && (!techSigInput || !techSigInput.value)) {
        e.preventDefault();
        alert('Por favor, proporciona la firma del técnico.');
        return false;
    }

    // Validar firma cliente
    const clientSigInput = document.getElementById('client_signature');
    if (!clientSigInput || !clientSigInput.value) {
        e.preventDefault();
        alert('Por favor, proporciona la firma del cliente.');
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

.signature-canvas {
    border: 3px solid #dee2e6;
    border-radius: 12px;
    cursor: crosshair;
    display: block;
    width: 100%;
    max-width: 100%;
    margin: 0 auto;
    background: white;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: border-color 0.3s ease;
}

.signature-canvas:hover {
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

