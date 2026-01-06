@php
    $isViewingAsTechnician = (session('view_as_technician', false) && auth()->check() && auth()->user()->hasRole('super-admin'))
        || request()->is('admin/technician-view/*')
        || (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], '/admin/technician-view/') !== false);
    $submitRoute = $isViewingAsTechnician ? route('admin.technician-view.service.checklist.submit', $service) : route('technician.service.checklist.submit', $service);
@endphp
@extends('layouts.app-tec')

@section('css')
    <style>
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
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .observation-item:hover {
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
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
            background: rgba(255, 255, 255, 0.2);
            padding: 6px 12px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 0.9em;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .observation-number {
            background: rgba(255, 255, 255, 0.15);
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 0.9em;
            border: 1px solid rgba(255, 255, 255, 0.2);
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
            background: rgba(255, 255, 255, 0.1);
            padding: 4px 8px;
            border-radius: 6px;
        }

        .toggle-icon {
            font-size: 1.2em;
            transition: transform 0.3s ease;
            background: rgba(255, 255, 255, 0.2);
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

        .btn-edit,
        .btn-delete {
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
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
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
            background: rgba(255, 255, 255, 0.2);
            border: 2px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 10px 15px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
        }

        .toggle-form-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            border-color: rgba(255, 255, 255, 0.5);
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
            gap: 20px;
            justify-content: flex-end;
            margin-top: 30px;
            padding-top: 25px;
            border-top: 2px solid #e9ecef;
        }

        .btn-save,
        .btn-cancel {
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
            background: #0a4d14;
            border-color: #0a4d14;
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(12, 206, 28, 0.4);
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

        .modal {
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            border-radius: 12px;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background: #2d5a27;
            color: white;
            border-radius: 12px 12px 0 0;
        }

        .modal-header h3 {
            margin: 0;
            font-size: 1.3em;
        }

        .close {
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            color: white;
            transition: color 0.3s ease;
        }

        .close:hover {
            color: #ccc;
        }

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

            .btn-save,
            .btn-cancel {
                width: 100%;
            }

            .navigation-buttons {
                justify-content: space-between;
            }

            .back-button,
            .next-button {
                font-size: 1.1em;
                padding: 12px 24px;
                border-radius: 25px;
                font-weight: 600;
                transition: all 0.2s ease;
                border: none;
                cursor: pointer;
                text-decoration: none;
                color: white;
                width: 45% !important;
            }
        }
    </style>
@endsection

@section('content')
    <div class="observations-container">
        <!-- Observaciones Guardadas (Acordeón) -->
        <div class="saved-observations" id="savedObservations">
            @if(isset($service->checklist_data["observations"]) && count($service->checklist_data["observations"]) > 0)
                @foreach($service->checklist_data["observations"] as $index => $observation)
                    <div class="observation-item" data-index="{{ $index }}">
                        <div class="observation-header" onclick="toggleObservation({{ $index }})">
                            <div class="observation-summary">
                                <span class="observation-code">{{ $observation['cebadera_code'] ?? 'N/A' }}</span>
                                <span class="observation-number">Obs
                                    #{{ $observation['observation_number'] ?? ($index + 1) }}</span>
                                <span
                                    class="observation-preview">{{ Str::limit($observation['detail'] ?? 'Sin detalle', 50) }}</span>
                            </div>
                            <div class="observation-actions">
                                <span
                                    class="observation-date">{{ isset($observation['created_at']) ? \Carbon\Carbon::parse($observation['created_at'])->format('d/m/Y H:i') : 'Ahora' }}</span>
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
                                            <img src="{{ asset($observation['photo']) }}" alt="Foto de observación"
                                                style="max-width: 200px; max-height: 150px; border-radius: 8px;">
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

            <form method="POST" action="{{ $submitRoute }}" enctype="multipart/form-data" class="observation-form"
                id="addObservationFormNEW" style="display: none;">
                @csrf
                @method('POST')
                <input type="hidden" name="current_stage" value="observations">
                <input type="hidden" name="next_stage" value="observations">

                <div class="form-row">
                    <div class="form-group">
                        @if($service->service_type === 'desratizacion')
                            <label>Código de la Cebadera:</label>

                        @elseif($service->service_type === 'desinsectacion' || $service->service_type === 'sanitizacion' || $service->service_type === 'fumigacion-de-jardines' || $service->service_type === 'servicios-especiales' || $service->service_type === 'desinfeccion')
                            <label>Lugar tratado o estación:</label>
                        @endif
                        @php
                            // Generar el siguiente código de cebadera automáticamente
                            $existingObservations = $service->checklist_data["observations"] ?? [];
                            $maxNumber = 0;
                            foreach ($existingObservations as $obs) {
                                if (isset($obs['cebadera_code']) && !empty($obs['cebadera_code'])) {
                                    if (preg_match('/CE-(\d+)/i', $obs['cebadera_code'], $matches)) {
                                        $number = (int) $matches[1];
                                        if ($number > $maxNumber) {
                                            $maxNumber = $number;
                                        }
                                    }
                                }
                            }
                            $nextCode = sprintf('CE-%03d', $maxNumber + 1);
                        @endphp
                        <input type="text" id="cebadera_code" name="cebadera_code" value="{{ $nextCode }}"
                            placeholder="Ej: CE-001">
                        <small>Se asignará automáticamente si se deja vacío</small>
                    </div>
                    <div class="form-group">
                        <label for="observation_number">N° de Observación</label>
                        <input type="number" id="observation_number" name="observation_number"
                            value="{{ (isset($service->checklist_data["observations"]) ? count($service->checklist_data["observations"]) : 0) + 1 }}"
                            min="1" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="detail">Detalle de la Observación <span style="color: red;">*</span></label>
                    <textarea id="detail" name="detail" placeholder="Describa detalladamente la observación..." rows="4"
                        required></textarea>
                </div>

                <div class="form-group">
                    <label style="display: flex; align-items: flex-start; cursor: pointer; gap: 10px;">
                        <input type="checkbox" id="no_observation" name="no_observation" value="1"
                            style="width: 20px; height: 20px; margin-top: 2px;">
                        <div>
                            <span style="display: block; font-weight: bold; margin-bottom: 2px;">Sin observación</span>
                            <small style="display: block; line-height: 1.3;">
                                Marque esta opción si no hay observaciones que registrar
                            </small>
                        </div>
                    </label>
                </div>

                <div class="form-group">
                    <label for="photo">Foto de la Estación/Cebadera</label>
                    <div class="file-upload">
                        <input type="file" id="photo" name="photo" accept="image/jpeg,image/png,image/gif"
                            onchange="previewPhoto(this)">
                        <label for="photo" class="file-label">
                            <span class="file-text">Seleccionar archivo</span>
                            <span class="file-info">Sin archivos seleccionados</span>
                        </label>
                        <div class="file-requirements">Formatos permitidos: JPG, PNG, GIF. Máximo 2MB</div>
                    </div>
                    <div class="photo-preview" id="photoPreview" style="display: none;">
                        <img id="previewImg" src="" alt="Vista previa"
                            style="max-width: 200px; max-height: 150px; border-radius: 8px;">
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-save">Guardar</button>
                    <button type="button" class="btn-cancel" onclick="toggleAddForm()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para Editar Observación -->
    <div id="editModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Editar Observación</h3>
                <span class="close" onclick="closeEditModal()">&times;</span>
            </div>
            <form id="editObservationForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('POST')
                <input type="hidden" id="editObservationIndex" name="observation_index">

                <div class="form-row">
                    <div class="form-group">
                        @if($service->service_type === 'desratizacion')
                            <label>Código de la Cebadera:</label>
                        @else
                            <label>Lugar tratado o estación:</label>
                        @endif
                        <input type="text" id="editCebaderaCode" name="cebadera_code" required>
                    </div>
                    <div class="form-group">
                        <label for="editObservationNumber">N° de Observación</label>
                        <input type="number" id="editObservationNumber" name="observation_number" min="1" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="editDetail">Detalle de la Observación</label>
                    <textarea id="editDetail" name="detail" rows="4" required></textarea>
                </div>

                <div class="form-group">
                    <label for="editPhoto">Foto de la Estación/Cebadera (opcional)</label>
                    <div class="file-upload">
                        <input type="file" id="editPhoto" name="photo" accept="image/jpeg,image/png,image/gif"
                            onchange="previewEditPhoto(this)">
                        <label for="editPhoto" class="file-label">
                            <span class="file-text">Seleccionar nuevo archivo</span>
                            <span class="file-info">Sin archivos seleccionados</span>
                        </label>
                        <div class="file-requirements">Formatos permitidos: JPG, PNG, GIF. Máximo 2MB</div>
                    </div>
                    <div class="photo-preview" id="editPhotoPreview" style="display: none;">
                        <img id="editPreviewImg" src="" alt="Vista previa"
                            style="max-width: 200px; max-height: 150px; border-radius: 8px;">
                    </div>
                    <div id="currentPhotoInfo" style="margin-top: 10px; font-size: 0.9em; color: #666;"></div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-save">Guardar Cambios</button>
                    <button type="button" class="btn-cancel" onclick="closeEditModal()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Botones de Navegación -->
    <div class="buttons-container">
        <a href="{{ route("technician.service.checklist.stage", ["service" => $service, "stage" => "results"]) }}"
            class="back-button">
            <span class="arrow">←</span> Anterior
        </a>
        <a href="{{ route("technician.service.checklist.stage", ["service" => $service, "stage" => "sites"]) }}"
            class="next-button">
            Siguiente <span class="arrow">→</span>
        </a>
    </div>
@endsection

@section('scripts')
    <script>
        // Manejar el checkbox "Sin observación"
        document.addEventListener('DOMContentLoaded', function () {
            const noObservationCheckbox = document.getElementById('no_observation');
            const detailTextarea = document.getElementById('detail');

            if (noObservationCheckbox && detailTextarea) {
                noObservationCheckbox.addEventListener('change', function () {
                    if (this.checked) {
                        // Remover el atributo required y llenar con "Sin observación"
                        detailTextarea.removeAttribute('required');
                        detailTextarea.value = 'Sin observación';
                        detailTextarea.disabled = true;
                    } else {
                        // Restaurar el atributo required y limpiar el campo
                        detailTextarea.setAttribute('required', 'required');
                        detailTextarea.value = '';
                        detailTextarea.disabled = false;
                    }
                });
            }
        });

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

        function generateNextCebaderaCode() {
            // Obtener todas las observaciones existentes
            const observationItems = document.querySelectorAll('.observation-item');
            let maxNumber = 0;

            observationItems.forEach(item => {
                const codeElement = item.querySelector('.observation-code');
                if (codeElement) {
                    const code = codeElement.textContent.trim();
                    // Extraer el número del código (ej: CE-001 -> 1)
                    const match = code.match(/CE-(\d+)/i);
                    if (match) {
                        const number = parseInt(match[1]);
                        if (number > maxNumber) {
                            maxNumber = number;
                        }
                    }
                }
            });

            // Generar el siguiente código
            const nextNumber = maxNumber + 1;
            return `CE-${String(nextNumber).padStart(3, '0')}`;
        }

        function toggleAddForm() {
            const form = document.getElementById('addObservationFormNEW');
            const icon = document.getElementById('toggleFormIcon');

            if (form.style.display === 'none') {
                form.style.display = 'block';
                icon.textContent = '▲';
                // Actualizar el código de cebadera con el siguiente disponible
                const cebaderaInput = document.getElementById('cebadera_code');
                if (cebaderaInput) {
                    cebaderaInput.value = generateNextCebaderaCode();
                }
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

                reader.onload = function (e) {
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
            // Obtener los datos de la observación desde el DOM
            const observationItem = document.querySelector(`[data-index="${index}"]`);
            const header = observationItem.querySelector('.observation-header');
            const cebaderaCode = header.querySelector('.observation-code')?.textContent || '';
            const observationNumber = header.querySelector('.observation-number')?.textContent.replace('Obs #', '') || '';
            const detail = observationItem.querySelector('.observation-content .detail-row:last-child span')?.textContent || '';

            // Obtener información de la foto actual si existe
            const currentPhoto = observationItem.querySelector('.observation-photo img');
            const currentPhotoSrc = currentPhoto ? currentPhoto.src : '';
            const currentPhotoInfo = document.getElementById('currentPhotoInfo');

            // Llenar el formulario del modal
            document.getElementById('editObservationIndex').value = index;
            document.getElementById('editCebaderaCode').value = cebaderaCode;
            document.getElementById('editObservationNumber').value = observationNumber;
            document.getElementById('editDetail').value = detail;

            // Mostrar información de la foto actual
            if (currentPhotoSrc) {
                const photoName = currentPhotoSrc.split('/').pop();
                currentPhotoInfo.innerHTML = `<i>📷 Foto actual: ${photoName}</i>`;
            } else {
                currentPhotoInfo.innerHTML = '<i>No hay foto actual</i>';
            }

            // Limpiar preview de nueva foto
            document.getElementById('editPhotoPreview').style.display = 'none';
            document.querySelector('#editModal .file-info').textContent = 'Sin archivos seleccionados';

            // Mostrar el modal
            document.getElementById('editModal').style.display = 'flex';
            // Cerrar todas las observaciones abiertas
            document.querySelectorAll('.observation-content').forEach(item => {
                item.style.display = 'none';
            });
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';

            // Limpiar el formulario
            document.getElementById('editObservationForm').reset();
            document.getElementById('editPhotoPreview').style.display = 'none';
            document.getElementById('currentPhotoInfo').innerHTML = '';
        }

        function previewEditPhoto(input) {
            const preview = document.getElementById('editPhotoPreview');
            const previewImg = document.getElementById('editPreviewImg');
            const fileInfo = input.parentElement.querySelector('.file-info');

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function (e) {
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

        function deleteObservation(index) {
            if (confirm('¿Estás seguro de que quieres eliminar esta observación?')) {
                // Obtener serviceId de múltiples fuentes posibles
                let serviceId = '{{ $service->id ?? "undefined" }}';

                // Si serviceId es "undefined", intentar obtenerlo de otras fuentes
                if (serviceId === 'undefined' || serviceId === '') {
                    // Intentar obtenerlo de la URL actual
                    const currentUrl = window.location.href;
                    const urlMatch = currentUrl.match(/\/technician\/services\/(\d+)\/checklist/);
                    if (urlMatch && urlMatch[1]) {
                        serviceId = urlMatch[1];
                    }
                }

                // Validar que tenemos serviceId
                if (!serviceId || serviceId === 'undefined' || serviceId === '') {
                    alert('Error: No se pudo obtener el ID del servicio. Por favor, recarga la página.');
                    return;
                }

                // Hacer petición AJAX para eliminar
                fetch(`/technician/services/${serviceId}/checklist/observations/${index}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'Accept': 'application/json'
                    }
                })
                    .then(response => {
                        if (!response.ok) {
                            return response.text().then(text => {
                                throw new Error(`HTTP ${response.status}: ${text}`);
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            alert(data.message || 'Observación eliminada exitosamente');
                            // Recargar la página para mostrar los cambios
                            window.location.reload();
                        } else {
                            throw new Error(data.message || 'Error al eliminar la observación');
                        }
                    })
                    .catch(error => {
                        console.error('Error al eliminar observación:', error);
                        alert('Error al eliminar la observación: ' + (error.message || 'Error desconocido'));
                    });
            }
        }

        // Configurar el envío del formulario de edición
        document.getElementById('editObservationForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const index = document.getElementById('editObservationIndex').value;

            // Obtener serviceId de múltiples fuentes posibles
            let serviceId = '{{ $service->id ?? "undefined" }}';

            // Si serviceId es "undefined", intentar obtenerlo de otras fuentes
            if (serviceId === 'undefined' || serviceId === '' || serviceId === '0') {
                // Intentar obtenerlo de la URL actual
                const currentUrl = window.location.href;
                const urlMatch = currentUrl.match(/\/technician\/services\/(\d+)\/checklist/);
                if (urlMatch && urlMatch[1]) {
                    serviceId = urlMatch[1];
                } else {
                    // Intentar obtenerlo de un campo oculto si existe
                    const hiddenServiceId = document.querySelector('input[name="service_id"]');
                    if (hiddenServiceId) {
                        serviceId = hiddenServiceId.value;
                    }
                }
            }

            // Validar que tenemos valores válidos
            if (!serviceId || serviceId === 'undefined' || serviceId === '' || serviceId === '0') {
                alert('Error: No se pudo obtener el ID del servicio. Por favor, recarga la página.');
                return;
            }

            if (!index || index === '' || index === 'undefined') {
                alert('Error: Índice de observación no válido. Por favor, recarga la página.');
                return;
            }

            console.log('Editando observación:', { serviceId, index }); // Debug

            // Mostrar indicador de carga
            const submitBtn = this.querySelector('.btn-save');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Guardando...';
            submitBtn.disabled = true;
            console.log('Editando observación:', formData); // Debug
            // Hacer petición AJAX
            fetch(`/technician/services/${serviceId}/checklist/observations/${index}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            })
                .then(response => {
                    // Verificar si la respuesta es exitosa
                    if (!response.ok) {
                        return response.text().then(text => {
                            throw new Error(`HTTP ${response.status}: ${text}`);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Cerrar modal
                        closeEditModal();

                        // Mostrar mensaje de éxito
                        alert(data.message || 'Observación actualizada exitosamente');

                        // Recargar la página para mostrar los cambios
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        console.error(data.message || 'Error al actualizar la observación');
                        throw new Error(data.message || 'Error al actualizar la observación');
                    }
                })
                .catch(error => {
                    console.error('Error completo:', error);

                    // Obtener el mensaje de error de manera segura
                    let errorMessage = 'Error desconocido';

                    if (error.message) {
                        if (typeof error.message === 'string') {
                            errorMessage = error.message;
                        } else if (typeof error.message === 'object') {
                            errorMessage = JSON.stringify(error.message);
                        } else {
                            errorMessage = String(error.message);
                        }
                    } else if (error.status) {
                        errorMessage = `Error HTTP ${error.status}: ${error.statusText || 'Sin detalles'}`;
                    } else if (typeof error === 'string') {
                        errorMessage = error;
                    } else {
                        errorMessage = 'Error interno del navegador';
                    }

                    // Si el error es de parsing JSON, mostrar más detalles
                    if (errorMessage.includes('JSON.parse') || errorMessage.includes('Unexpected token')) {
                        alert('Error: El servidor devolvió una respuesta no válida. Esto podría deberse a un error interno. Por favor, recarga la página e intenta nuevamente.');
                    } else {
                        alert('Error al actualizar la observación: ' + errorMessage);
                    }

                    // Restaurar el botón
                    submitBtn.textContent = originalText;
                    submitBtn.disabled = false;
                });
        });

        // Auto-expandir la primera observación si solo hay una
        document.addEventListener('DOMContentLoaded', function () {
            const observations = document.querySelectorAll('.observation-item');
            if (observations.length === 1) {
                toggleObservation(0);
            }

            // Verificar si hay un índice específico para editar desde la URL
            @if(session('edit_observation_index') !== null)
                const editIndex = {{ session('edit_observation_index') }};
                if (editIndex >= 0 && editIndex < observations.length) {
                    // Pequeño delay para asegurar que el DOM esté listo
                    setTimeout(() => {
                        editObservation(editIndex);
                    }, 500);
                }
            @endif
        });
    </script>
@endsection