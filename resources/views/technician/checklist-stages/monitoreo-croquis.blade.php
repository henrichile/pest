@php
$isViewingAsTechnician = (session('view_as_technician', false) && auth()->check() && auth()->user()->hasRole('super-admin')) 
    || request()->is('admin/technician-view/*')
    || (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], '/admin/technician-view/') !== false);
$submitRoute = $isViewingAsTechnician ? route('technician-view.service.checklist.submit', $service) : route('technician.service.checklist.submit', $service);
@endphp
<form method="POST" action="{{ $submitRoute }}" data-stage="monitoreo-croquis" id="croquisForm" enctype="multipart/form-data">
    @csrf
    <div class="form-section">
        <h5>📍 Croquis de Cebaderas del Cliente</h5>
        
        @if(empty($service->client->bait_station_sketch))
        <div class="alert alert-warning">
            <strong>⚠️ Este cliente no tiene un croquis de cebaderas configurado.</strong>
            <p>Puedes continuar con el monitoreo o contactar al administrador para configurar el croquis.</p>
        </div>
        @else
        <div class="sketch-display">
            <img src="{{ Storage::url($service->client->bait_station_sketch) }}" alt="Croquis de Cebaderas" class="sketch-image">
        </div>
        @endif
    </div>

    <div class="form-section">
        <h5>📸 Subir/Actualizar Croquis</h5>

        @if($errors->has('croquis_file'))
        <div class="alert alert-danger">
            <strong>⚠️ Error:</strong> {{ $errors->first('croquis_file') }}
        </div>
        @endif

        @if(isset($service->checklist_data['monitoreo_croquis']['croquis_file']))
        <div class="alert alert-info">
            <strong>✓ Croquis actual:</strong>
            <a href="{{ asset($service->checklist_data['monitoreo_croquis']['croquis_file']) }}" target="_blank" style="color: #2563eb; text-decoration: underline;">Ver croquis guardado</a>
        </div>
        @endif

        <div class="photo-upload-area" id="croquis-upload-area">
            <input type="file"
                   name="croquis_file"
                   id="croquis_file"
                   accept="image/*,application/pdf"
                   class="photo-input"
                   onchange="handleCroquisUpload(event)">
            <div class="upload-placeholder">
                <span class="upload-icon">📷</span>
                <p>Haz clic para subir croquis o arrastra aquí</p>
                <small>PNG, JPG, PDF hasta 10MB</small>
            </div>
            <div id="croquis-preview" class="photos-preview"></div>
        </div>
    </div>

    <div class="form-section">
        <h5>📝 Notas sobre el Croquis</h5>
        <textarea name="croquis_notes" 
                  id="croquis_notes" 
                  class="form-textarea" 
                  rows="4"
                  placeholder="Anotaciones sobre el croquis, cambios realizados, nuevas ubicaciones...">{{ old('croquis_notes', $service->checklist_data['croquis_notes'] ?? '') }}</textarea>
    </div>

    <input type="hidden" name="checklist_stage" value="monitoreo-croquis">
    <input type="hidden" name="next_stage" value="monitoreo-completo">
</form>

<script>
function handleCroquisUpload(event) {
    const files = event.target.files;
    const preview = document.getElementById('croquis-preview');
    preview.innerHTML = '';
    
    Array.from(files).forEach((file, index) => {
        const div = document.createElement('div');
        div.className = 'photo-preview-item';
        
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                div.innerHTML = `
                    <img src="${e.target.result}" alt="Preview">
                    <button type="button" onclick="removeCroquis(${index})" class="photo-remove">×</button>
                `;
                preview.appendChild(div);
            };
            reader.readAsDataURL(file);
        } else if (file.type === 'application/pdf') {
             div.innerHTML = `
                <div style="padding: 40px; background: #f3f4f6; display: flex; flex-direction: column; align-items: center; justify-content: center; height: 160px;">
                    <span style="font-size: 48px;">📄</span>
                    <p style="margin-top: 10px; font-size: 12px; color: #6b7280;">${file.name}</p>
                </div>
                <button type="button" onclick="removeCroquis(${index})" class="photo-remove">×</button>
            `;
            preview.appendChild(div);
        }
    });
}

function removeCroquis(index) {
    const input = document.getElementById('croquis_file');
    const dt = new DataTransfer();
    Array.from(input.files).forEach((file, i) => {
        if (i !== index) dt.items.add(file);
    });
    input.files = dt.files;
    handleCroquisUpload({ target: input });
}
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

.alert {
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 25px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.alert-warning {
    background: #fef3c7;
    border: 1px solid #f59e0b;
    color: #92400e;
}

.alert-danger {
    background: #fee2e2;
    border: 1px solid #ef4444;
    color: #991b1b;
}

.alert-info {
    background: #dbeafe;
    border: 1px solid #3b82f6;
    color: #1e40af;
}

.alert-warning strong,
.alert-danger strong,
.alert-info strong {
    display: block;
    margin-bottom: 8px;
    font-size: 16px;
}

.alert-warning p,
.alert-danger p,
.alert-info p {
    margin: 0;
    font-size: 14px;
    line-height: 1.6;
}

.sketch-display {
    background: #ffffff;
    padding: 30px;
    border-radius: 12px;
    text-align: center;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
}

.sketch-image {
    max-width: 100%;
    max-height: 500px;
    border-radius: 12px;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    border: 1px solid #e5e7eb;
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
    background: #ffffff;
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

.form-textarea {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #dee2e6;
    border-radius: 10px;
    font-size: 15px;
    transition: all 0.3s ease;
    background: #ffffff;
    font-family: inherit;
}

.form-textarea:focus {
    outline: none;
    border-color: #22c55e;
    box-shadow: 0 0 0 3px rgba(34,197,94,0.1);
}

@media (max-width: 768px) {
    .form-section {
        padding: 16px;
    }
    
    .form-section h5 {
        font-size: 16px;
    }
    
    .photo-upload-area {
        padding: 24px;
    }
    
    .upload-icon {
        font-size: 40px;
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
</style>

