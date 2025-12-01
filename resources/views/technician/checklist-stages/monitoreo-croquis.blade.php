@php
$isViewingAsTechnician = (session('view_as_technician', false) && auth()->check() && auth()->user()->hasRole('super-admin')) 
    || request()->is('admin/technician-view/*')
    || (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], '/admin/technician-view/') !== false);
$submitRoute = $isViewingAsTechnician ? route('admin.technician-view.service.checklist.submit', $service) : route('technician.service.checklist.submit', $service);
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
        @php
            $monitoreoCroquis = $service->checklist_data['monitoreo_croquis'] ?? [];
            $existingCroquis = $monitoreoCroquis['croquis_file'] ?? null;
        @endphp
        <div class="photo-upload-area" id="croquis-upload-area">
            <input type="file" 
                   name="croquis_file" 
                   id="croquis_file" 
                   accept="image/*" 
                   class="photo-input"
                   onchange="handleCroquisUpload(event)">
            <div class="upload-placeholder">
                <span class="upload-icon">📐</span>
                <p>Haz clic para subir croquis o arrastra aquí</p>
                <small>PNG, JPG, PDF hasta 10MB</small>
            </div>
            <div id="croquis-preview" class="croquis-preview">
                @if($existingCroquis)
                    <div class="croquis-preview-item">
                        <img src="/{{ $existingCroquis }}" alt="Croquis existente" class="sketch-image">
                        <p>Croquis guardado anteriormente</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="form-section">
        <h5>📝 Notas sobre el Croquis</h5>
        <textarea name="croquis_notes" 
                  id="croquis_notes" 
                  class="form-textarea" 
                  rows="4"
                  placeholder="Anotaciones sobre el croquis, cambios realizados, nuevas ubicaciones...">{{ old('croquis_notes', $monitoreoCroquis['croquis_notes'] ?? $service->checklist_data['croquis_notes'] ?? '') }}</textarea>
    </div>

    <input type="hidden" name="checklist_stage" value="monitoreo-croquis">
    <input type="hidden" name="next_stage" value="monitoreo-completo">
</form>

<script>
function handleCroquisUpload(event) {
    const file = event.target.files[0];
    if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('croquis-preview');
            preview.innerHTML = `
                <div class="croquis-preview-item">
                    <img src="${e.target.result}" alt="Croquis Preview">
                    <p>${file.name}</p>
                </div>
            `;
        };
        reader.readAsDataURL(file);
    }
}
</script>

<style>
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

.alert-warning strong {
    display: block;
    margin-bottom: 8px;
    font-size: 16px;
}

.alert-warning p {
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

.croquis-preview {
    margin-top: 25px;
}

.croquis-preview-item {
    background: #ffffff;
    padding: 20px;
    border-radius: 12px;
    text-align: center;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
}

.croquis-preview-item img {
    max-width: 100%;
    max-height: 300px;
    border-radius: 12px;
    margin-bottom: 15px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.croquis-preview-item p {
    margin: 0;
    color: #6b7280;
    font-size: 14px;
}

@media (max-width: 768px) {
    .form-section {
        padding: 16px;
    }
    
    .form-section h5 {
        font-size: 16px;
    }
    
    .croquis-preview-item img {
        max-height: 200px;
    }
    
    .croquis-image {
        max-height: 250px;
    }
}

@media (max-width: 640px) {
    .form-section {
        padding: 12px;
    }
    
    .croquis-image {
        max-height: 200px;
    }
}
</style>

