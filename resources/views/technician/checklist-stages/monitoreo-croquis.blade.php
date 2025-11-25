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
                <span class="upload-icon">📐</span>
                <p>Haz clic para subir croquis o arrastra aquí</p>
                <small>PNG, JPG, PDF hasta 10MB</small>
                <small style="display: block; margin-top: 5px; color: #6b7280;">Límite del servidor: {{ ini_get('upload_max_filesize') }}</small>
            </div>
            <div id="croquis-preview" class="croquis-preview"></div>
            <div id="file-size-warning" style="display: none; color: #dc2626; margin-top: 10px; font-weight: bold;"></div>
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
    const file = event.target.files[0];
    const warning = document.getElementById('file-size-warning');
    const preview = document.getElementById('croquis-preview');

    if (!file) {
        warning.style.display = 'none';
        preview.innerHTML = '';
        return;
    }

    // Obtener límite del servidor (convertir a bytes)
    const serverLimit = '{{ ini_get("upload_max_filesize") }}';
    const serverLimitMB = parseFloat(serverLimit);
    const serverLimitBytes = serverLimitMB * 1024 * 1024;

    // Validar tamaño
    const fileSizeMB = (file.size / 1024 / 1024).toFixed(2);

    if (file.size > serverLimitBytes) {
        warning.textContent = `⚠️ El archivo es demasiado grande (${fileSizeMB}MB). El límite del servidor es ${serverLimitMB}MB. Por favor, reduce el tamaño de la imagen.`;
        warning.style.display = 'block';
        event.target.value = ''; // Limpiar el input
        preview.innerHTML = '';
        return;
    } else if (file.size > 10 * 1024 * 1024) {
        warning.textContent = `⚠️ El archivo es grande (${fileSizeMB}MB). Se recomienda usar archivos más pequeños para una carga más rápida.`;
        warning.style.display = 'block';
        warning.style.color = '#f59e0b'; // Advertencia amarilla
    } else {
        warning.style.display = 'none';
    }

    // Mostrar preview
    if (file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `
                <div class="croquis-preview-item">
                    <img src="${e.target.result}" alt="Croquis Preview">
                    <p>${file.name} (${fileSizeMB}MB)</p>
                </div>
            `;
        };
        reader.readAsDataURL(file);
    } else if (file.type === 'application/pdf') {
        preview.innerHTML = `
            <div class="croquis-preview-item">
                <div style="padding: 40px; background: #f3f4f6; border-radius: 8px;">
                    <span style="font-size: 48px;">📄</span>
                    <p>${file.name} (${fileSizeMB}MB)</p>
                </div>
            </div>
        `;
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

