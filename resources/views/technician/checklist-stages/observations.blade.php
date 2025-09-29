@extends('layouts.app-tec')

@section('css')
<style>
.navigation-buttons {
    display: flex;
    justify-content: space-between;
    margin-top: 40px;
    padding-top: 25px;
    border-top: 3px solid #2d5a27;
    gap: 15px;
}

.back-button, .next-button {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    border-radius: 25px;
    font-weight: 600;
    font-size: 1em;
    transition: all 0.2s ease;
    border: none;
    cursor: pointer;
    text-decoration: none;
    color: white;
}

.back-button {
    background: #6c757d;
}

.back-button:hover {
    background: #5a6268;
}

.next-button {
    background: #2d5a27;
}

.next-button:hover {
    background: #1a3d1a;
}

.arrow {
    font-size: 1.1em;
    font-weight: bold;
}
.observations-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
}

.saved-observations {
    margin-bottom: 30px;
}

.observation-item {
    background: white;
    border: 2px solid #2d5a27;
    border-radius: 12px;
    margin-bottom: 15px;
    overflow: hidden;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.observation-item:hover {
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    transform: translateY(-2px);
    border-color: #1a3d1a;
}

.observation-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 20px;
    background: #6c757d;
    color: white;
    cursor: pointer;
    transition: all 0.3s ease;
}

.observation-header:hover {
    background: linear-gradient(135deg, #1a3d1a 0%, #0f2410 100%);
}

.observation-summary {
    display: flex;
    align-items: center;
    gap: 15px;
    flex: 1;
}

.observation-code {
    background: rgba(255,255,255,0.2);
    padding: 6px 12px;
    border-radius: 8px;
    font-weight: bold;
    font-size: 0.9em;
    border: 1px solid rgba(255,255,255,0.3);
}

.observation-number {
    background: rgba(255,255,255,0.15);
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 0.9em;
    border: 1px solid rgba(255,255,255,0.2);
}

.observation-preview {
    font-size: 0.95em;
    opacity: 0.9;
    font-weight: 500;
}

.observation-actions {
    display: flex;
    align-items: center;
    gap: 10px;
}

.observation-date {
    font-size: 0.85em;
    opacity: 0.8;
    background: rgba(255,255,255,0.1);
    padding: 4px 8px;
    border-radius: 6px;
}

.toggle-icon {
    font-size: 1.2em;
    transition: transform 0.3s ease;
    background: rgba(255,255,255,0.2);
    padding: 4px 8px;
    border-radius: 6px;
}

.observation-content {
    padding: 25px;
    background: white;
    border-top: 2px solid #2d5a27;
}

.observation-details {
    margin-bottom: 20px;
}

.detail-row {
    display: flex;
    margin-bottom: 15px;
    align-items: flex-start;
    padding: 10px;
    background: #f8f9fa;
    border-radius: 8px;
    border-left: 4px solid #2d5a27;
}

.detail-row label {
    font-weight: 700;
    color: #2d5a27;
    min-width: 180px;
    margin-right: 15px;
    font-size: 0.95em;
}

.detail-row span {
    color: #333;
    flex: 1;
    word-break: break-word;
    font-weight: 500;
}

.observation-photo {
    margin-top: 10px;
}

.observation-photo img {
    border: 3px solid #2d5a27;
    box-shadow: 0 4px 12px rgba(45, 90, 39, 0.2);
}

.observation-actions-bottom {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    padding-top: 20px;
    border-top: 2px solid #e9ecef;
}

.btn-edit, .btn-delete {
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 0.9em;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-edit {
    background: #2d5a27;
    color: white;
    border: 2px solid #2d5a27;
}

.btn-edit:hover {
    background: #1a3d1a;
    border-color: #1a3d1a;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(45, 90, 39, 0.3);
}

.btn-delete {
    background: #dc3545;
    color: white;
    border: 2px solid #dc3545;
}

.btn-delete:hover {
    background: #c82333;
    border-color: #c82333;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
}

.no-observations {
    text-align: center;
    padding: 50px 20px;
    color: #666;
    background: white;
    border-radius: 12px;
    border: 3px dashed #2d5a27;
    font-size: 1.1em;
    font-weight: 500;
}

.add-observation-section {
    background: white;
    border-radius: 12px;
    border: 2px solid #2d5a27;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.section-title {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    background: #6c757d;
    color: white;
    cursor: pointer;
}

.section-title h3 {
    margin: 0;
    font-size: 1.3em;
    font-weight: 700;
}

.toggle-form-btn {
    background: rgba(255,255,255,0.2);
    border: 2px solid rgba(255,255,255,0.3);
    color: white;
    padding: 10px 15px;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 600;
}

.toggle-form-btn:hover {
    background: rgba(255,255,255,0.3);
    border-color: rgba(255,255,255,0.5);
    transform: scale(1.05);
}

.observation-form {
    padding: 30px;
    background: white;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 25px;
    margin-bottom: 25px;
}

.form-group {
    margin-bottom: 25px;
}

.form-group label {
    display: block;
    margin-bottom: 10px;
    font-weight: 700;
    color: #2d5a27;
    font-size: 1.05em;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 15px;
    border: 2px solid #e9ecef;
    border-radius: 10px;
    font-size: 1em;
    transition: all 0.3s ease;
    background: white;
}

.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #2d5a27;
    box-shadow: 0 0 0 4px rgba(45, 90, 39, 0.1);
    background: #f8f9fa;
}

.file-upload {
    position: relative;
}

.file-upload input[type="file"] {
    position: absolute;
    opacity: 0;
    width: 100%;
    height: 100%;
    cursor: pointer;
}

.file-label {
    display: block;
    padding: 20px;
    border: 3px dashed #2d5a27;
    border-radius: 10px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    background: #f8f9fa;
}

.file-label:hover {
    border-color: #1a3d1a;
    background: rgba(45, 90, 39, 0.05);
    transform: translateY(-2px);
}

.file-text {
    display: block;
    font-weight: 700;
    color: #2d5a27;
    margin-bottom: 8px;
    font-size: 1.1em;
}

.file-info {
    font-size: 0.95em;
    color: #666;
    font-weight: 500;
}

.file-requirements {
    font-size: 0.85em;
    color: #666;
    margin-top: 10px;
    text-align: center;
    font-weight: 500;
}

.photo-preview {
    margin-top: 20px;
    text-align: center;
}

.photo-preview img {
    border: 3px solid #2d5a27;
    box-shadow: 0 4px 12px rgba(45, 90, 39, 0.2);
}

.form-actions {
    display: flex;
    gap: 20px;
    justify-content: flex-end;
    margin-top: 30px;
    padding-top: 25px;
    border-top: 2px solid #e9ecef;
}

.btn-save, .btn-cancel {
    padding: 15px;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    font-size: 1.1em;
    font-weight: 700;
    transition: all 0.3s ease;
}

.btn-save {
    background: #0cce1c;
    color: white;
    border: 2px solid #0e5c04;
}

.btn-save:hover {
    background: linear-gradient(135deg, #1a3d1a 0%, #0f2410 100%);
    border-color: #1a3d1a;
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(45, 90, 39, 0.4);
}

.btn-cancel {
    background: #6c757d;
    color: white;
    border: 2px solid #6c757d;
}

.btn-cancel:hover {
    background: #5a6268;
    border-color: #5a6268;
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(108, 117, 125, 0.4);
}

    font-size: 1.2em;
    font-weight: bold;
}

/* Responsive */
@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .observation-summary {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    
    .observation-actions {
        flex-direction: column;
        align-items: flex-end;
        gap: 8px;
    }
    
    .detail-row {
        flex-direction: column;
    }
    
    .detail-row label {
        min-width: auto;
        margin-bottom: 8px;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .btn-save, .btn-cancel {
        width: 100%;
    }
}
@media (max-width: 480px) {
    .observation-header{
        display: grid !important;
    }
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .observation-summary {
        display: grid !important;
    
    }

    .observation-code{
        width: 270px !important;
        clear: both;
    }

    .observation-number{
        width: 270px !important;
        clear: both;
    }

    .observation-preview {
        width: 270px !important;
        margin-bottom: 5PX;
        clear: both;
    }

    
    .observation-actions {
        flex-direction: row;
        align-items: center;
        gap: 110px;
    }
    
    .detail-row {
        flex-direction: row;
    }
    
    .detail-row label {
        min-width: auto;
        margin-bottom: 8px;
    }
    
    .form-actions {
        flex-direction: row;
    }
    
    .btn-save, .btn-cancel {
        width: 100%;
    }

    .observation-actions-bottom {
    gap: 12px;
    justify-content: flex-end;
    padding-top: 20px;
    border-top: 2px solid #e9ecef;
}
}
</style>
@endsection

@section('content')

<!-- Updated: 2024-09-14 20:58 - COMPLETE RESTORATION -->
<!-- Etapa 4: Observaciones Detalladas -->
<div class="stage-title">Observaciones Detalladas</div>
<div class="stage-instruction">Registre observaciones específicas con fotos</div>

<div class="observations-container">
    <!-- Observaciones Guardadas (Acordeón) -->
    <div class="saved-observations" id="savedObservations">
        @if(isset($service->checklist_data["observations"]) && count($service->checklist_data["observations"]) > 0)
            @foreach($service->checklist_data["observations"] as $index => $observation)
                <div class="observation-item" data-index="{{ $index }}">
                    <div class="observation-header" onclick="toggleObservation({{ $index }})">
                        <div class="observation-summary">
                            <span class="observation-code">{{ $observation['cebadera_code'] ?? 'N/A' }}</span>
                            <span class="observation-number">Obs #{{ $observation['observation_number'] ?? ($index + 1) }}</span>
                            <span class="observation-preview">{{ Str::limit($observation['detail'] ?? 'Sin detalle', 50) }}</span>
                        </div>
                        <div class="observation-actions">
                            <span class="observation-date">{{ isset($observation['created_at']) ? \Carbon\Carbon::parse($observation['created_at'])->format('d/m/Y H:i') : 'Ahora' }}</span>
                            <span class="toggle-icon" id="toggleIcon{{ $index }}">▼</span>
                        </div>
                    </div>
                    <div class="observation-content" id="observationContent{{ $index }}" style="display: none;">
                        <div class="observation-details">
                            <div class="detail-row">
                                
                                @if($service->service_type === 'desratizacion')
                                    <label>Código de la Cebadera:</label>
                                    <span>{{ $observation['cebadera_code'] ?? 'N/A' }}</span>
                                @elseif($service->service_type === 'desinsectacion' || $service->service_type === 'sanitizacion' || $service->service_type === 'fumigacion-de-jardines' || $service->service_type === 'servicios-especiales' || $service->service_type === 'desinsectacion')
                                    <label>Lugar tratado:</label>
                                @endif
                            </div>
                            <div class="detail-row">
                                    <label>Número de Observación:</label>
                                    <span>{{ $observation['observation_number'] ?? ($index + 1) }}</span>
                            </div>
                            <div class="detail-row">
                                <label>Detalle:</label>
                                <span>{{ $observation['detail'] ?? 'Sin detalle' }}</span>
                            </div>
                            @if(isset($observation['photo']) && $observation['photo'])
                                <div class="detail-row">
                                    <label>Foto:</label>
                                    <div class="observation-photo">
                                        <img src="{{ asset($observation['photo']) }}" alt="Foto de observación" style="max-width: 200px; max-height: 150px; border-radius: 8px;">
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="observation-actions-bottom">
                            <button type="button" class="btn-edit" onclick="editObservation({{ $index }})">Editar</button>
                            <button type="button" class="btn-delete" onclick="deleteObservation({{ $index }})">Eliminar</button>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="no-observations">
                <p>No hay observaciones guardadas aún.</p>
            </div>
        @endif
    </div>

    <!-- Formulario para Agregar Nueva Observación -->
    <div class="add-observation-section">
        <div class="section-title">
            <h3>Agregar Nueva Observación</h3>
            <button type="button" class="toggle-form-btn" onclick="toggleAddForm()">
                <span id="toggleFormIcon">▼</span>
            </button>
        </div>
        
        <form method="POST" action="{{ route("technician.service.checklist.submit", $service) }}" enctype="multipart/form-data" class="observation-form" id="addObservationFormNEW" style="display: none;">
            @csrf
            <input type="hidden" name="current_stage" value="observations">
            <input type="hidden" name="next_stage" value="observations">
            
            <div class="form-row">
                <div class="form-group">
                     
                    @if($service->service_type === 'desratizacion')
                        <label>Código de la Cebadera:</label>
                        <span>{{ $observation['cebadera_code'] ?? 'N/A' }}</span>
                    @elseif($service->service_type === 'desinsectacion' || $service->service_type === 'sanitizacion' || $service->service_type === 'fumigacion-de-jardines' || $service->service_type === 'servicios-especiales' || $service->service_type === 'desinfeccion')
                        <label>Lugar tratado o estación:</label>
                    @endif
                    <input type="text" id="cebadera_code" name="cebadera_code" placeholder="Ej: CE-001" required>
                </div>
                <div class="form-group">
                    <label for="observation_number">N° de Observación</label>
                    <input type="number" id="observation_number" name="observation_number" value="{{ (isset($service->checklist_data["observations"]) ? count($service->checklist_data["observations"]) : 0) + 1 }}" min="1" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="detail">Detalle de la Observación</label>
                <textarea id="detail" name="detail" placeholder="Describa detalladamente la observación..." rows="4" required></textarea>
            </div>
            
            <div class="form-group">
                <label for="photo">Foto de la Estación/Cebadera</label>
                <div class="file-upload">
                    <input type="file" id="photo" name="photo" accept="image/jpeg,image/png,image/gif" onchange="previewPhoto(this)">
                    <label for="photo" class="file-label">
                        <span class="file-text">Seleccionar archivo</span>
                        <span class="file-info">Sin archivos seleccionados</span>
                    </label>
                    <div class="file-requirements">Formatos permitidos: JPG, PNG, GIF. Máximo 2MB</div>
                </div>
                <div class="photo-preview" id="photoPreview" style="display: none;">
                    <img id="previewImg" src="" alt="Vista previa" style="max-width: 200px; max-height: 150px; border-radius: 8px;">
                </div>
            </div>
            
            <div class="form-group">
            
            <div class="form-actions">
                <button type="submit" class="btn-save">Guardar</button>
                <button type="button" class="btn-cancel" onclick="toggleAddForm()">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<!-- Botones de Navegación -->
<div class="navigation-buttons">
    <a href="{{ route("technician.service.checklist.stage", ["service" => $service, "stage" => "results"]) }}" class="back-button">
        <span class="arrow">←</span> Anterior
    </a>
    <a href="{{ route("technician.service.checklist.stage", ["service" => $service, "stage" => "sites"]) }}" class="next-button">
        Siguiente <span class="arrow">→</span>
    </a>
</div>
@endsection


@section('scripts')
<script>
function toggleObservation(index) {
    const content = document.getElementById('observationContent' + index);
    const icon = document.getElementById('toggleIcon' + index);
    
    if (content.style.display === 'none') {
        content.style.display = 'block';
        icon.textContent = '▲';
        icon.style.transform = 'rotate(180deg)';
    } else {
        content.style.display = 'none';
        icon.textContent = '▼';
        icon.style.transform = 'rotate(0deg)';
    }
}

function toggleAddForm() {
    const form = document.getElementById('addObservationFormNEW');
    const icon = document.getElementById('toggleFormIcon');
    
    if (form.style.display === 'none') {
        form.style.display = 'block';
        icon.textContent = '▲';
    } else {
        form.style.display = 'none';
        icon.textContent = '▼';
    }
}

function previewPhoto(input) {
    const preview = document.getElementById('photoPreview');
    const previewImg = document.getElementById('previewImg');
    const fileInfo = input.parentElement.querySelector('.file-info');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
            fileInfo.textContent = input.files[0].name;
        };
        
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.style.display = 'none';
        fileInfo.textContent = 'Sin archivos seleccionados';
    }
}

function editObservation(index) {
    // Implementar funcionalidad de edición
    alert('Funcionalidad de edición en desarrollo');
}

function deleteObservation(index) {
    if (confirm('¿Estás seguro de que quieres eliminar esta observación?')) {
        // Implementar funcionalidad de eliminación
        alert('Funcionalidad de eliminación en desarrollo');
    }
}

// Auto-expandir la primera observación si solo hay una
document.addEventListener('DOMContentLoaded', function() {
    const observations = document.querySelectorAll('.observation-item');
    if (observations.length === 1) {
        toggleObservation(0);
    }
});
</script>
@endsection
