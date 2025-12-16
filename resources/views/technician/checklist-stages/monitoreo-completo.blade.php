@php
$isViewingAsTechnician = (session('view_as_technician', false) && auth()->check() && auth()->user()->hasRole('super-admin')) 
    || request()->is('admin/technician-view/*')
    || (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], '/admin/technician-view/') !== false);
$submitRoute = $isViewingAsTechnician ? route('admin.technician-view.service.checklist.submit', $service) : route('technician.service.checklist.submit', $service);
@endphp
<form method="POST" action="{{ $submitRoute }}" data-stage="monitoreo-completo" id="monitoreoCompletoForm" enctype="multipart/form-data">
    @csrf
    <div class="form-section">
        <h5>📅 Información General</h5>
        <div class="form-grid">
            <div>
                <label>Fecha de Monitoreo <span class="required">*</span></label>
                <input type="date" 
                       name="monitoring_date" 
                       id="monitoring_date" 
                       class="form-input" 
                       value="{{ old('monitoring_date', $service->checklist_data['monitoring_date'] ?? date('Y-m-d')) }}"
                       required>
            </div>
            <div>
                <label>Total Cebaderas Instaladas</label>
                <input type="number" 
                       name="total_bait_stations" 
                       id="total_bait_stations" 
                       class="form-input" 
                       min="0"
                       value="{{ old('total_bait_stations', $service->checklist_data['total_bait_stations'] ?? 0) }}">
            </div>
        </div>
    </div>

    <div class="form-section">
        <div class="section-header">
            <h5>🐀 Cebaderas Monitoreadas</h5>
            <button type="button" class="add-button" onclick="addBaitStation()">+ Agregar Cebadera</button>
        </div>
        <div id="bait-stations-container">
            <!-- Las cebaderas se agregarán dinámicamente aquí -->
        </div>
    </div>

    <div class="form-section">
        <div class="section-header">
            <h5>🪤 Trampas de Captura</h5>
            <button type="button" class="add-button" onclick="addTrap()">+ Agregar Trampa</button>
        </div>
        <div id="traps-container">
            <!-- Las trampas se agregarán dinámicamente aquí -->
        </div>
    </div>

    <div class="form-section">
        <h5>📊 Conclusiones</h5>
        <div class="form-section">
            <h6>Observaciones Generales</h6>
            <textarea name="general_observations" 
                      id="general_observations" 
                      class="form-textarea" 
                      rows="4"
                      placeholder="Resumen general del estado encontrado...">{{ old('general_observations', $service->checklist_data['general_observations'] ?? '') }}</textarea>
        </div>
        <div class="form-section">
            <h6>Recomendaciones para el Cliente</h6>
            <textarea name="client_recommendations_monitoring" 
                      id="client_recommendations_monitoring" 
                      class="form-textarea" 
                      rows="4"
                      placeholder="Recomendaciones y acciones sugeridas...">{{ old('client_recommendations_monitoring', $service->checklist_data['client_recommendations_monitoring'] ?? '') }}</textarea>
        </div>
    </div>

    <input type="hidden" name="checklist_stage" value="monitoreo-completo">
    <input type="hidden" name="next_stage" value="monitoreo-estadisticas">
    <input type="hidden" name="bait_stations_data" id="bait_stations_data">
    <input type="hidden" name="traps_data" id="traps_data">
</form>

<script>
let baitStationCounter = 0;
let trapCounter = 0;

function addBaitStation() {
    baitStationCounter++;
    const container = document.getElementById('bait-stations-container');
    const stationDiv = document.createElement('div');
    stationDiv.className = 'bait-station-card';
    stationDiv.id = `bait-station-${baitStationCounter}`;
    stationDiv.innerHTML = `
        <div class="card-header">
            <h6>Cebadera #${baitStationCounter}</h6>
            <button type="button" class="delete-button" onclick="removeBaitStation(${baitStationCounter})">🗑️</button>
        </div>
        <div class="form-grid">
            <div>
                <label>Código del Punto</label>
                <input type="text" name="bait_stations[${baitStationCounter}][code]" class="form-input placeholder:text-gray-400 dark:placeholder:text-gray-500 dark:text-white dark:bg-gray-700 dark:border-gray-600" placeholder="CB-001" required>
            </div>
            <div>
                <label>Ubicación</label>
                <input type="text" name="bait_stations[${baitStationCounter}][location]" class="form-input placeholder:text-gray-400 dark:placeholder:text-gray-500 dark:text-white dark:bg-gray-700 dark:border-gray-600" placeholder="Bodega sector norte" required>
            </div>
        </div>
        <div class="product-section">
            <h6>📦 Producto Aplicado</h6>
            <div class="form-grid">
                <div>
                    <label>Tipo de Producto</label>
                    <select name="bait_stations[${baitStationCounter}][product_type]" class="form-select dark:text-white dark:bg-gray-700 dark:border-gray-600" required>
                        <option value="">Seleccionar producto</option>
                        <option value="bloque">Bloque</option>
                        <option value="pasta">Pasta</option>
                        <option value="granulado">Granulado</option>
                        <option value="liquido">Líquido</option>
                    </select>
                </div>
                <div>
                    <label>Cantidad</label>
                    <div class="quantity-group">
                        <input type="number" name="bait_stations[${baitStationCounter}][quantity]" class="form-input dark:text-white dark:bg-gray-700 dark:border-gray-600" min="0" step="0.01" placeholder="50" required>
                        <select name="bait_stations[${baitStationCounter}][unit]" class="form-select dark:text-white dark:bg-gray-700 dark:border-gray-600" style="width: 80px;">
                            <option value="g">g</option>
                            <option value="ml">ml</option>
                            <option value="unidad">unidad</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="observations-section">
            <h6>Observaciones</h6>
            <div class="observations-grid">
                <label class="checkbox-label">
                    <input type="checkbox" name="bait_stations[${baitStationCounter}][observations][]" value="consumo_50">
                    <span>1. Consumo 50%</span>
                </label>
                <label class="checkbox-label">
                    <input type="checkbox" name="bait_stations[${baitStationCounter}][observations][]" value="bloqueada">
                    <span>2. Cebadera bloqueada</span>
                </label>
                <label class="checkbox-label">
                    <input type="checkbox" name="bait_stations[${baitStationCounter}][observations][]" value="muestra_roedores">
                    <span>3. Muestra de roedores (orina/daño)</span>
                </label>
                <label class="checkbox-label">
                    <input type="checkbox" name="bait_stations[${baitStationCounter}][observations][]" value="sustraida">
                    <span>4. Cebadera sustraída</span>
                </label>
                <label class="checkbox-label">
                    <input type="checkbox" name="bait_stations[${baitStationCounter}][observations][]" value="hongos">
                    <span>5. Cebos con hongos</span>
                </label>
                <label class="checkbox-label">
                    <input type="checkbox" name="bait_stations[${baitStationCounter}][observations][]" value="sucia">
                    <span>6. Cebadera sucia/con polvo</span>
                </label>
                <label class="checkbox-label">
                    <input type="checkbox" name="bait_stations[${baitStationCounter}][observations][]" value="actividad_biologica">
                    <span>8. Actividad biológica (babosas)</span>
                </label>
            </div>
        </div>
        <div class="photo-section">
            <h6>📷 Fotografías</h6>
            <div class="photo-upload-area-small">
                <input type="file" name="bait_stations[${baitStationCounter}][photos][]" multiple accept="image/*" class="photo-input" onchange="handleStationPhotoUpload(event, ${baitStationCounter})">
                <div class="upload-placeholder-small">
                    <span>📷</span>
                    <p>Click para subir fotos</p>
                </div>
                <div class="photos-preview-small" id="station-photos-${baitStationCounter}"></div>
            </div>
        </div>
    `;
    container.appendChild(stationDiv);
}

function removeBaitStation(id) {
    const station = document.getElementById(`bait-station-${id}`);
    if (station) station.remove();
}

function addTrap() {
    trapCounter++;
    const container = document.getElementById('traps-container');
    const trapDiv = document.createElement('div');
    trapDiv.className = 'trap-card';
    trapDiv.id = `trap-${trapCounter}`;
    trapDiv.innerHTML = `
        <div class="card-header">
            <h6>Trampa #${trapCounter}</h6>
            <button type="button" class="delete-button" onclick="removeTrap(${trapCounter})">🗑️</button>
        </div>
        <div class="form-grid">
            <div>
                <label>Código del Punto</label>
                <input type="text" name="traps[${trapCounter}][code]" class="form-input placeholder:text-gray-400 dark:placeholder:text-gray-500 dark:text-white dark:bg-gray-700 dark:border-gray-600" placeholder="TR-001" required>
            </div>
            <div>
                <label>Ubicación</label>
                <input type="text" name="traps[${trapCounter}][location]" class="form-input placeholder:text-gray-400 dark:placeholder:text-gray-500 dark:text-white dark:bg-gray-700 dark:border-gray-600" placeholder="Cocina pared este" required>
            </div>
        </div>
        <div class="form-grid">
            <div>
                <label>Producto/Material Utilizado</label>
                <select name="traps[${trapCounter}][product_type]" class="form-select dark:text-white dark:bg-gray-700 dark:border-gray-600">
                    <option value="">Seleccionar</option>
                    <option value="pegajosa">Trampa Pegajosa</option>
                    <option value="mecanica">Trampa Mecánica</option>
                    <option value="cebo">Cebo</option>
                </select>
            </div>
            <div>
                <label>Cantidad</label>
                <input type="number" name="traps[${trapCounter}][quantity]" class="form-input dark:text-white dark:bg-gray-700 dark:border-gray-600" min="1" value="1">
            </div>
        </div>
        <div>
            <label>Estado de la Trampa</label>
            <select name="traps[${trapCounter}][status]" class="form-select dark:text-white dark:bg-gray-700 dark:border-gray-600">
                <option value="">Seleccionar estado</option>
                <option value="activa">Activa</option>
                <option value="captura">Con Captura</option>
                <option value="dañada">Dañada</option>
                <option value="sustraida">Sustraída</option>
            </select>
        </div>
        <div class="photo-section">
            <h6>📷 Fotografías</h6>
            <div class="photo-upload-area-small">
                <input type="file" name="traps[${trapCounter}][photos][]" multiple accept="image/*" class="photo-input" onchange="handleTrapPhotoUpload(event, ${trapCounter})">
                <div class="upload-placeholder-small">
                    <span>📷</span>
                    <p>Click para subir fotos</p>
                </div>
                <div class="photos-preview-small" id="trap-photos-${trapCounter}"></div>
            </div>
        </div>
        <div>
            <label>Notas</label>
            <textarea name="traps[${trapCounter}][notes]" class="form-textarea dark:text-white dark:bg-gray-700 dark:border-gray-600" rows="2" placeholder="Observaciones..."></textarea>
        </div>
    `;
    container.appendChild(trapDiv);
}

function removeTrap(id) {
    const trap = document.getElementById(`trap-${id}`);
    if (trap) trap.remove();
}

function handleStationPhotoUpload(event, stationId) {
    const files = event.target.files;
    const preview = document.getElementById(`station-photos-${stationId}`);
    preview.innerHTML = '';
    Array.from(files).forEach(file => {
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'photo-preview-item-small';
                div.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
                preview.appendChild(div);
            };
            reader.readAsDataURL(file);
        }
    });
}

function handleTrapPhotoUpload(event, trapId) {
    const files = event.target.files;
    const preview = document.getElementById(`trap-photos-${trapId}`);
    preview.innerHTML = '';
    Array.from(files).forEach(file => {
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'photo-preview-item-small';
                div.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
                preview.appendChild(div);
            };
            reader.readAsDataURL(file);
        }
    });
}

// Cargar datos existentes si hay
@php
    $monitoreoCompleto = $service->checklist_data['monitoreo_completo'] ?? [];
    $existingBaitStations = $monitoreoCompleto['bait_stations'] ?? $service->checklist_data['bait_stations'] ?? [];
    $existingTraps = $monitoreoCompleto['traps'] ?? $service->checklist_data['traps'] ?? [];
@endphp

@if(count($existingBaitStations) > 0)
    document.addEventListener('DOMContentLoaded', function() {
        const existingStations = @json($existingBaitStations);
        existingStations.forEach((station, index) => {
            baitStationCounter = index + 1;
            addBaitStation();
            
            // Llenar datos de la cebadera
            const stationDiv = document.getElementById(`bait-station-${baitStationCounter}`);
            if (stationDiv) {
                // Código y ubicación
                stationDiv.querySelector(`input[name="bait_stations[${baitStationCounter}][code]"]`).value = station.code || '';
                stationDiv.querySelector(`input[name="bait_stations[${baitStationCounter}][location]"]`).value = station.location || '';
                
                // Tipo de producto
                if (station.product_type) {
                    stationDiv.querySelector(`select[name="bait_stations[${baitStationCounter}][product_type]"]`).value = station.product_type;
                }
                
                // Cantidad y unidad
                stationDiv.querySelector(`input[name="bait_stations[${baitStationCounter}][quantity]"]`).value = station.quantity || 0;
                if (station.unit) {
                    stationDiv.querySelector(`select[name="bait_stations[${baitStationCounter}][unit]"]`).value = station.unit;
                }
                
                // Observaciones (checkboxes)
                if (station.observations && Array.isArray(station.observations)) {
                    station.observations.forEach(obs => {
                        const checkbox = stationDiv.querySelector(`input[type="checkbox"][value="${obs}"]`);
                        if (checkbox) checkbox.checked = true;
                    });
                }
                
                // Fotos existentes
                if (station.photos && Array.isArray(station.photos) && station.photos.length > 0) {
                    const preview = document.getElementById(`station-photos-${baitStationCounter}`);
                    if (preview) {
                        station.photos.forEach(photoPath => {
                            const div = document.createElement('div');
                            div.className = 'photo-preview-item-small';
                            div.innerHTML = `<img src="/${photoPath}" alt="Foto existente" style="width: 100%; height: 120px; object-fit: cover; border-radius: 8px;">`;
                            preview.appendChild(div);
                        });
                    }
                }
            }
        });
    });
@endif

@if(count($existingTraps) > 0)
    document.addEventListener('DOMContentLoaded', function() {
        const existingTraps = @json($existingTraps);
        existingTraps.forEach((trap, index) => {
            trapCounter = index + 1;
            addTrap();
            
            // Llenar datos de la trampa
            const trapDiv = document.getElementById(`trap-${trapCounter}`);
            if (trapDiv) {
                // Código y ubicación
                trapDiv.querySelector(`input[name="traps[${trapCounter}][code]"]`).value = trap.code || '';
                trapDiv.querySelector(`input[name="traps[${trapCounter}][location]"]`).value = trap.location || '';
                
                // Tipo de producto
                if (trap.product_type) {
                    trapDiv.querySelector(`select[name="traps[${trapCounter}][product_type]"]`).value = trap.product_type;
                }
                
                // Cantidad
                trapDiv.querySelector(`input[name="traps[${trapCounter}][quantity]"]`).value = trap.quantity || 1;
                
                // Estado
                if (trap.status) {
                    trapDiv.querySelector(`select[name="traps[${trapCounter}][status]"]`).value = trap.status;
                }
                
                // Notas
                if (trap.notes) {
                    trapDiv.querySelector(`textarea[name="traps[${trapCounter}][notes]"]`).value = trap.notes;
                }
                
                // Fotos existentes
                if (trap.photos && Array.isArray(trap.photos) && trap.photos.length > 0) {
                    const preview = document.getElementById(`trap-photos-${trapCounter}`);
                    if (preview) {
                        trap.photos.forEach(photoPath => {
                            const div = document.createElement('div');
                            div.className = 'photo-preview-item-small';
                            div.innerHTML = `<img src="/${photoPath}" alt="Foto existente" style="width: 100%; height: 120px; object-fit: cover; border-radius: 8px;">`;
                            preview.appendChild(div);
                        });
                    }
                }
            }
        });
    });
@endif

// Cargar datos generales si existen
@php
    $monitoringDate = $monitoreoCompleto['monitoring_date'] ?? $service->checklist_data['monitoring_date'] ?? date('Y-m-d');
    $totalBaitStations = $monitoreoCompleto['total_bait_stations'] ?? $service->checklist_data['total_bait_stations'] ?? 0;
    $generalObservations = $monitoreoCompleto['general_observations'] ?? $service->checklist_data['general_observations'] ?? '';
    $clientRecommendations = $monitoreoCompleto['client_recommendations_monitoring'] ?? $service->checklist_data['client_recommendations_monitoring'] ?? '';
@endphp

document.addEventListener('DOMContentLoaded', function() {
    // Llenar fecha de monitoreo
    const monitoringDateInput = document.getElementById('monitoring_date');
    if (monitoringDateInput) {
        monitoringDateInput.value = '{{ $monitoringDate }}';
    }
    
    // Llenar total de cebaderas
    const totalBaitStationsInput = document.getElementById('total_bait_stations');
    if (totalBaitStationsInput) {
        totalBaitStationsInput.value = {{ $totalBaitStations }};
    }
    
    // Llenar observaciones generales
    const generalObservationsTextarea = document.getElementById('general_observations');
    if (generalObservationsTextarea) {
        generalObservationsTextarea.value = @json($generalObservations);
    }
    
    // Llenar recomendaciones
    const clientRecommendationsTextarea = document.getElementById('client_recommendations_monitoring');
    if (clientRecommendationsTextarea) {
        clientRecommendationsTextarea.value = @json($clientRecommendations);
    }
});
</script>

<style>
.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #e5e7eb;
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

@media (max-width: 768px) {
    .form-section {
        padding: 16px;
    }
    
    .form-section h5 {
        font-size: 16px;
    }
    
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .bait-station-card, .trap-card {
        padding: 16px;
    }
    
    .section-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
    }
    
    .add-button {
        width: 100%;
    }
    
    .photo-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 640px) {
    .form-section {
        padding: 12px;
    }
    
    .bait-station-card, .trap-card {
        padding: 12px;
    }
}

.bait-station-card, .trap-card {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 25px;
    margin-bottom: 25px;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    transition: all 0.3s ease;
}

.bait-station-card:hover, .trap-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.12);
    transform: translateY(-2px);
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #e5e7eb;
}

.card-header h6 {
    margin: 0;
    color: #111827;
    font-size: 18px;
    font-weight: 700;
}

.delete-button {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(220,53,69,0.3);
}

.delete-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(220,53,69,0.4);
}

.product-section, .observations-section, .photo-section {
    margin-top: 25px;
    padding-top: 25px;
    border-top: 1px solid #e5e7eb;
}

.product-section h6, .observations-section h6, .photo-section h6 {
    color: #111827;
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.observations-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 12px;
    margin-top: 15px;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    padding: 12px;
    background: #ffffff;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.checkbox-label:hover {
    border-color: #22c55e;
    background: #f9fafb;
}

.checkbox-label input[type="checkbox"]:checked + span {
    color: #111827;
    font-weight: 600;
}

.checkbox-label input[type="checkbox"]:checked ~ span {
    color: #111827;
}

.checkbox-label:has(input[type="checkbox"]:checked) {
    border-color: #22c55e;
    background: #f0fdf4;
}

.quantity-group {
    display: flex;
    gap: 12px;
    align-items: flex-start;
}

.quantity-group .form-input {
    flex: 1;
}

.quantity-group .form-select {
    width: auto;
    min-width: 100px;
}

.photo-upload-area-small {
    position: relative;
    border: 2px dashed #d1d5db;
    border-radius: 12px;
    padding: 25px;
    text-align: center;
    cursor: pointer;
    background: #f9fafb;
    transition: all 0.3s ease;
}

.photo-upload-area-small:hover {
    border-color: #22c55e;
    background: #f0fdf4;
}

/* Hide native file input text */
.photo-upload-area-small .photo-input {
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    opacity: 0;
    cursor: pointer;
    z-index: 10;
}

.upload-placeholder-small {
    pointer-events: none;
}

.upload-placeholder-small span {
    font-size: 32px;
    display: block;
    margin-bottom: 10px;
}

.upload-placeholder-small p {
    color: #6b7280;
    font-size: 14px;
    margin: 0;
}

.photos-preview-small {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 15px;
    margin-top: 20px;
}

.photo-preview-item-small {
    position: relative;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.photo-preview-item-small img {
    width: 100%;
    height: 120px;
    object-fit: cover;
    border-radius: 8px;
}
</style>

