@php
$isViewingAsTechnician = (session('view_as_technician', false) && auth()->check() && auth()->user()->hasRole('super-admin')) 
    || request()->is('admin/technician-view/*')
    || (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], '/admin/technician-view/') !== false);
$submitRoute = $isViewingAsTechnician ? route('admin.technician-view.service.checklist.submit', $service) : route('technician.service.checklist.submit', $service);
@endphp
<form method="POST" action="{{ $submitRoute }}" data-stage="monitoreo-datos" id="monitoreoDatosForm">
    @csrf
    <div class="form-section">
        <h5>🐀 Plagas Detectadas</h5>
        <div class="input-group">
            <input type="text" 
                   name="pests_detected" 
                   id="pests_detected" 
                   class="form-input" 
                   placeholder="Ej: Cucarachas, Ratones, Ratas..."
                   value="{{ old('pests_detected', $service->checklist_data['monitoreo_datos']['pests_detected'] ?? $service->checklist_data['pests_detected'] ?? '') }}">
            <button type="button" class="add-button" onclick="addPest()">Agregar</button>
        </div>
        <div id="pests-list" class="tags-container"></div>
    </div>

    <div class="form-section">
        <h5>📊 Nivel de Infestación</h5>
        <select name="infestation_level" id="infestation_level" class="form-select" required>
            <option value="">Seleccionar nivel</option>
            <option value="bajo" {{ old('infestation_level', $service->checklist_data['monitoreo_datos']['infestation_level'] ?? $service->checklist_data['infestation_level'] ?? '') === 'bajo' ? 'selected' : '' }}>Bajo</option>
            <option value="medio" {{ old('infestation_level', $service->checklist_data['monitoreo_datos']['infestation_level'] ?? $service->checklist_data['infestation_level'] ?? '') === 'medio' ? 'selected' : '' }}>Medio</option>
            <option value="alto" {{ old('infestation_level', $service->checklist_data['monitoreo_datos']['infestation_level'] ?? $service->checklist_data['infestation_level'] ?? '') === 'alto' ? 'selected' : '' }}>Alto</option>
            <option value="critico" {{ old('infestation_level', $service->checklist_data['monitoreo_datos']['infestation_level'] ?? $service->checklist_data['infestation_level'] ?? '') === 'critico' ? 'selected' : '' }}>Crítico</option>
        </select>
    </div>

    <div class="form-section">
        <h5>📝 Observaciones del Técnico <span class="required">*</span></h5>
        <textarea name="technician_observations" 
                  id="technician_observations" 
                  class="form-textarea" 
                  rows="6" 
                  required
                  placeholder="Describe el trabajo realizado, condiciones encontradas, productos aplicados...">{{ old('technician_observations', $service->checklist_data['monitoreo_datos']['technician_observations'] ?? $service->checklist_data['technician_observations'] ?? '') }}</textarea>
    </div>

    <div class="form-section">
        <h5>💡 Recomendaciones para el Cliente</h5>
        <textarea name="client_recommendations" 
                  id="client_recommendations" 
                  class="form-textarea" 
                  rows="4"
                  placeholder="Medidas preventivas, próximos pasos, cuidados especiales...">{{ old('client_recommendations', $service->checklist_data['monitoreo_datos']['client_recommendations'] ?? $service->checklist_data['client_recommendations'] ?? '') }}</textarea>
    </div>

    <div class="form-section">
        <h5>📷 Fotos del Servicio</h5>
        <div class="photo-upload-area" id="photo-upload-area">
            <input type="file" 
                   name="service_photos[]" 
                   id="service_photos" 
                   multiple 
                   accept="image/*" 
                   class="photo-input"
                   onchange="handlePhotoUpload(event)">
            <div class="upload-placeholder">
                <span class="upload-icon">📷</span>
                <p>Haz clic para subir fotos o arrastra aquí</p>
                <small>PNG, JPG hasta 10MB</small>
            </div>
            <div id="photos-preview" class="photos-preview"></div>
        </div>
    </div>

    <input type="hidden" name="checklist_stage" value="monitoreo-datos">
    <input type="hidden" name="next_stage" value="monitoreo-croquis">
</form>

<script>
let selectedPests = [];

function addPest() {
    const input = document.getElementById('pests_detected');
    const pest = input.value.trim();
    if (pest && !selectedPests.includes(pest)) {
        selectedPests.push(pest);
        updatePestsList();
        input.value = '';
    }
}

function updatePestsList() {
    const container = document.getElementById('pests-list');
    container.innerHTML = selectedPests.map((pest, index) => `
        <span class="tag">
            ${pest}
            <button type="button" onclick="removePest(${index})" class="tag-remove">×</button>
        </span>
    `).join('');
    
    // Actualizar campo hidden con las plagas
    const hiddenInput = document.createElement('input');
    hiddenInput.type = 'hidden';
    hiddenInput.name = 'pests_detected_list';
    hiddenInput.value = JSON.stringify(selectedPests);
    const existing = document.querySelector('input[name="pests_detected_list"]');
    if (existing) existing.remove();
    document.getElementById('monitoreoDatosForm').appendChild(hiddenInput);
}

function removePest(index) {
    selectedPests.splice(index, 1);
    updatePestsList();
}

function handlePhotoUpload(event) {
    const files = event.target.files;
    const preview = document.getElementById('photos-preview');
    preview.innerHTML = '';
    
    Array.from(files).forEach((file, index) => {
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'photo-preview-item';
                div.innerHTML = `
                    <img src="${e.target.result}" alt="Preview">
                    <button type="button" onclick="removePhoto(${index})" class="photo-remove">×</button>
                `;
                preview.appendChild(div);
            };
            reader.readAsDataURL(file);
        }
    });
}

function removePhoto(index) {
    const input = document.getElementById('service_photos');
    const dt = new DataTransfer();
    Array.from(input.files).forEach((file, i) => {
        if (i !== index) dt.items.add(file);
    });
    input.files = dt.files;
    handlePhotoUpload({ target: input });
}

// Cargar plagas existentes si hay
@php
    $monitoreoDatos = $service->checklist_data['monitoreo_datos'] ?? [];
    $existingPests = $monitoreoDatos['pests_detected_list'] ?? $service->checklist_data['pests_detected_list'] ?? [];
    $existingPhotos = $monitoreoDatos['service_photos'] ?? $service->checklist_data['service_photos'] ?? [];
@endphp

@if(count($existingPests) > 0)
    selectedPests = @json($existingPests);
    updatePestsList();
@endif

// Cargar fotos existentes si hay
@if(count($existingPhotos) > 0)
    document.addEventListener('DOMContentLoaded', function() {
        const preview = document.getElementById('photos-preview');
        if (preview) {
            const existingPhotos = @json($existingPhotos);
            existingPhotos.forEach((photoPath, index) => {
                const div = document.createElement('div');
                div.className = 'photo-preview-item';
                div.innerHTML = `
                    <img src="/${photoPath}" alt="Foto existente">
                    <button type="button" onclick="removeExistingPhoto(${index})" class="photo-remove">×</button>
                `;
                preview.appendChild(div);
            });
        }
    });
    
    function removeExistingPhoto(index) {
        // En una implementación completa, esto debería enviar una petición al servidor
        // para eliminar la foto del almacenamiento. Por ahora, solo la removemos del DOM.
        const preview = document.getElementById('photos-preview');
        const items = preview.querySelectorAll('.photo-preview-item');
        if (items[index]) {
            items[index].remove();
        }
    }
@endif
</script>

<style>
.form-section {
    margin-bottom: 30px;
    padding: 25px;
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
}

.form-section h5 {
    color: #111827;
    margin-bottom: 20px;
    font-size: 18px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 10px;
    padding-bottom: 12px;
    border-bottom: 1px solid #e5e7eb;
}

.required {
    color: #dc3545;
    font-weight: 700;
}

.info-display {
    background: #ffffff;
    padding: 20px;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
    padding: 10px 0;
    border-bottom: 1px solid #e5e7eb;
}

.info-item:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.info-label {
    font-weight: 600;
    color: #6b7280;
    font-size: 14px;
}

.info-value {
    color: #111827;
    font-weight: 500;
    font-size: 15px;
}

.input-group {
    display: flex;
    gap: 12px;
    align-items: flex-start;
}

.form-input, .form-select, .form-textarea {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #dee2e6;
    border-radius: 10px;
    font-size: 15px;
    transition: all 0.3s ease;
    background: #ffffff;
    font-family: inherit;
}

.form-input:focus, .form-select:focus, .form-textarea:focus {
    outline: none;
    border-color: #22c55e;
    box-shadow: 0 0 0 3px rgba(34,197,94,0.1);
}

.form-input:hover, .form-select:hover, .form-textarea:hover {
    border-color: #adb5bd;
}

.add-button {
    background: #22c55e;
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    white-space: nowrap;
    font-size: 14px;
    transition: all 0.2s ease;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}

.add-button:hover {
    background: #16a34a;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1);
}

.tags-container {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 10px;
}

.tag {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    color: #155724;
    padding: 8px 16px;
    border-radius: 20px;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    font-size: 14px;
    font-weight: 600;
    border: 1px solid #28a745;
    box-shadow: 0 2px 4px rgba(40,167,69,0.2);
}

.tag-remove {
    background: rgba(220,53,69,0.1);
    border: none;
    color: #dc3545;
    cursor: pointer;
    font-size: 16px;
    line-height: 1;
    padding: 2px 6px;
    border-radius: 50%;
    width: 22px;
    height: 22px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.tag-remove:hover {
    background: #dc3545;
    color: white;
    transform: scale(1.1);
}

.photo-upload-area {
    position: relative;
    border: 2px dashed #d1d5db;
    border-radius: 12px;
    padding: 40px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    background: #f9fafb;
}

.photo-upload-area:hover {
    border-color: #22c55e;
    background: #f0fdf4;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1);
}

.photo-input {
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    opacity: 0;
    cursor: pointer;
}

.upload-placeholder {
    pointer-events: none;
}

.upload-icon {
    font-size: 56px;
    display: block;
    margin-bottom: 15px;
    filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
}

.upload-placeholder p {
    color: #111827;
    font-size: 16px;
    font-weight: 500;
    margin: 10px 0;
}

@media (max-width: 768px) {
    .form-section {
        padding: 16px;
    }
    
    .form-section h5 {
        font-size: 16px;
    }
    
    .form-grid {
        grid-template-columns: 1fr !important;
    }
    
    .input-group {
        flex-direction: column;
    }
    
    .photo-upload-area {
        padding: 24px;
    }
    
    .upload-icon {
        font-size: 40px;
    }
    
    .tags-container {
        gap: 6px;
    }
    
    .tag {
        font-size: 12px;
        padding: 6px 12px;
    }
}

@media (max-width: 640px) {
    .form-section {
        padding: 12px;
    }
    
    .photo-upload-area {
        padding: 20px;
    }
}

.upload-placeholder small {
    color: #6b7280;
    font-size: 13px;
}

.photos-preview {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    gap: 20px;
    margin-top: 25px;
}

.photo-preview-item {
    position: relative;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    transition: all 0.3s ease;
}

.photo-preview-item:hover {
    transform: translateY(-4px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.2);
}

.photo-preview-item img {
    width: 100%;
    height: 160px;
    object-fit: cover;
    display: block;
}

.photo-remove {
    position: absolute;
    top: 8px;
    right: 8px;
    background: rgba(220, 53, 69, 0.95);
    color: white;
    border: none;
    border-radius: 50%;
    width: 32px;
    height: 32px;
    cursor: pointer;
    font-size: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(220,53,69,0.4);
}

.photo-remove:hover {
    background: #dc3545;
    transform: scale(1.1);
    box-shadow: 0 4px 12px rgba(220,53,69,0.6);
}
</style>

