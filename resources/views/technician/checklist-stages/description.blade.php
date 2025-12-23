@extends('layouts.app-tec')

@section('content')

    <!-- Etapa 6: Descripción del Servicio -->
    <div class="stage-title">Descripción del Servicio y Sugerencias</div>
    <div class="stage-instruction">Complete la descripción final del servicio realizado</div>

    @php
        $isViewingAsTechnician = (session('view_as_technician', false) && auth()->check() && auth()->user()->hasRole('super-admin'))
            || request()->is('admin/technician-view/*')
            || (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], '/admin/technician-view/') !== false);
        $submitRoute = $isViewingAsTechnician ? route('admin.technician-view.service.checklist.submit', $service) : route('technician.service.checklist.submit', $service);
    @endphp
    <form method="POST" action="{{ $submitRoute }}" data-stage="description" id="checklistForm">
        <input type="hidden" name="stage" value="description">
        @csrf
        <input type="hidden" name="next_stage" value="completed">
        <input type="hidden" name="current_stage" value="description">
        <div class="form-group">
            <label>Descripción del Servicio</label>
            <textarea name="service_description" rows="8"
                placeholder="Describa el servicio realizado, resultados obtenidos.">{{ $service->checklist_data["description"]["service_description"] ?? "" }}</textarea>
        </div>

        <div class="form-group">
            <label>Sugerencias</label>
            <textarea name="service_sugerencia" rows="6"
                placeholder="Recomendaciones para el cliente, próximos pasos, sugerencias de mejora, etc...">{{ $service->checklist_data["description"]["service_sugerencia"] ?? "" }}</textarea>
        </div>

        <!-- Resumen del Checklist -->
        <div class="checklist-summary">
            <h5>Resumen del Monitoreo</h5>
            <div class="summary-grid">
                <div class="summary-item">
                    <span class="summary-label">Producto Aplicado:</span>
                    <p class="summary-value">
                        {{ $service->checklist_data["products"]["applied_product"] ?? "No especificado" }}
                    </p>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Sitios Tratados:</span>
                    <p class="summary-value">
                        {{ Str::limit($service->checklist_data["sites"]["treated_sites"] ?? "No especificado", 50) }}
                    </p>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Observaciones:</span>
                    <p class="summary-value">{{ count($service->checklist_data["observations"] ?? []) }} registradas</p>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Resultados:</span>
                    <p class="summary-value">{{ count($service->checklist_data["results"]["observed_results"] ?? []) }}
                        tipos encontrados</p>
                </div>
            </div>
        </div>

        <!-- Campos de Firma Digital -->
        <div class="signatures-section">
            <h5>Firmas Digitales de Confirmación</h5>
        </div>
        <div class="signatures-grid">
            <div class="signature-group">
                <label>Firma del Técnico</label>
                <canvas id="technicianSignature"
                    style="width: 100%; background-color: #fff; border: 1px solid #ccc;"></canvas>
                <div class="signature-controls">
                    <button type="button" class="clear-signature" data-canvas="technicianSignature">Limpiar</button>
                    <span class="signature-status" id="technicianStatus">Sin firma</span>
                </div>
                <p class="signature-help">Dibuje su firma en el área superior</p>
                <input type="hidden" name="technician_signature" id="technicianSignatureData">
            </div>

            <div class="signature-group">
                <label>Firma del Cliente</label>
                <canvas id="clientSignature" style="width: 100%; background-color: #fff; border: 1px solid #ccc;"></canvas>
                <div class="signature-controls">
                    <button type="button" class="clear-signature" data-canvas="clientSignature">Limpiar</button>
                    <span class="signature-status" id="clientStatus">Sin firma</span>
                </div>
                <p class="signature-help">Dibuje su firma en el área superior</p>
                <input type="hidden" name="client_signature" id="clientSignatureData">
            </div>
        </div>

        <div class="signature-date">
            <label>Fecha de Finalización</label>
            <input type="date" name="completion_date"
                value="{{ $service->checklist_data["description"]["completion_date"] ?? date("Y-m-d") }}" required>
        </div>


        <div class="buttons-container">
            <a href="{{ route("technician.service.checklist.stage", ["service" => $service, "stage" => "sites"]) }}"
                class="back-button">
                <span class="arrow">←</span> Anterior
            </a>
            <button type="submit" class="next-button" id="finalizeButton" disabled>
                Finalizar ✓
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
            /*  display: grid;
                                                        grid-template-columns: 1fr 1fr;*/
            gap: 20px;
            margin-bottom: 20px;
            max-width: 100%;
        }

        .signature-group {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
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
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            width: 100%;
            box-sizing: border-box;
        }

        .signature-pad canvas {
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            cursor: crosshair;
            display: block;
            margin: 0 auto;
            width: 100%;
            height: 150px;
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
                max-width: 100%;
                height: 120px;
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
        document.addEventListener('DOMContentLoaded', function () {
            // Configuración de canvas para firmas
            const technicianCanvas = document.getElementById('technicianSignature');
            const clientCanvas = document.getElementById('clientSignature');
            let technicianCtx = technicianCanvas.getContext('2d');
            let clientCtx = clientCanvas.getContext('2d');

            // Función para redimensionar canvas a su tamaño real en pantalla
            function resizeCanvas(canvas) {
                const rect = canvas.getBoundingClientRect();
                const dpr = window.devicePixelRatio || 1;

                // Establecer el tamaño interno del canvas igual al tamaño CSS
                canvas.width = rect.width * dpr;
                canvas.height = rect.height * dpr;

                const ctx = canvas.getContext('2d');
                ctx.scale(dpr, dpr);

                // Configurar estilos de dibujo
                ctx.strokeStyle = '#1a472a';
                ctx.lineWidth = 2;
                ctx.lineCap = 'round';
                ctx.lineJoin = 'round';

                // Llenar con fondo blanco
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, rect.width, rect.height);

                return ctx;
            }

            // Redimensionar ambos canvas
            technicianCtx = resizeCanvas(technicianCanvas);
            clientCtx = resizeCanvas(clientCanvas);

            // Redimensionar cuando cambia el tamaño de ventana
            window.addEventListener('resize', function () {
                technicianCtx = resizeCanvas(technicianCanvas);
                clientCtx = resizeCanvas(clientCanvas);
            });

            // Variables para tracking de dibujo
            let isDrawing = false;
            let lastX = 0;
            let lastY = 0;
            let currentCanvas = null;
            let currentCtx = null;

            // Función para obtener coordenadas correctas del canvas
            function getCanvasCoordinates(canvas, clientX, clientY) {
                const rect = canvas.getBoundingClientRect();
                return {
                    x: clientX - rect.left,
                    y: clientY - rect.top
                };
            }

            // Función para iniciar dibujo
            function startDrawing(e, canvas, ctx) {
                isDrawing = true;
                currentCanvas = canvas;
                currentCtx = ctx;
                const coords = getCanvasCoordinates(canvas, e.clientX, e.clientY);
                lastX = coords.x;
                lastY = coords.y;
            }

            // Función para dibujar
            function draw(e) {
                if (!isDrawing || !currentCanvas || !currentCtx) return;

                const coords = getCanvasCoordinates(currentCanvas, e.clientX, e.clientY);

                currentCtx.beginPath();
                currentCtx.moveTo(lastX, lastY);
                currentCtx.lineTo(coords.x, coords.y);
                currentCtx.stroke();

                lastX = coords.x;
                lastY = coords.y;
            }

            // Función para detener dibujo
            function stopDrawing(canvasId) {
                if (isDrawing) {
                    isDrawing = false;
                    currentCanvas = null;
                    currentCtx = null;
                    updateSignatureStatus(canvasId);
                }
            }

            // Eventos para canvas del técnico
            technicianCanvas.addEventListener('mousedown', (e) => startDrawing(e, technicianCanvas, technicianCtx));
            technicianCanvas.addEventListener('mousemove', (e) => draw(e));
            technicianCanvas.addEventListener('mouseup', () => stopDrawing('technicianSignature'));
            technicianCanvas.addEventListener('mouseout', () => stopDrawing('technicianSignature'));

            // Eventos táctiles para móviles - Técnico
            technicianCanvas.addEventListener('touchstart', (e) => {
                e.preventDefault();
                const touch = e.touches[0];
                startDrawing({ clientX: touch.clientX, clientY: touch.clientY }, technicianCanvas, technicianCtx);
            }, { passive: false });

            technicianCanvas.addEventListener('touchmove', (e) => {
                e.preventDefault();
                const touch = e.touches[0];
                draw({ clientX: touch.clientX, clientY: touch.clientY });
            }, { passive: false });

            technicianCanvas.addEventListener('touchend', (e) => {
                e.preventDefault();
                stopDrawing('technicianSignature');
            }, { passive: false });

            // Eventos para canvas del cliente
            clientCanvas.addEventListener('mousedown', (e) => startDrawing(e, clientCanvas, clientCtx));
            clientCanvas.addEventListener('mousemove', (e) => draw(e));
            clientCanvas.addEventListener('mouseup', () => stopDrawing('clientSignature'));
            clientCanvas.addEventListener('mouseout', () => stopDrawing('clientSignature'));

            // Eventos táctiles para móviles - Cliente
            clientCanvas.addEventListener('touchstart', (e) => {
                e.preventDefault();
                const touch = e.touches[0];
                startDrawing({ clientX: touch.clientX, clientY: touch.clientY }, clientCanvas, clientCtx);
            }, { passive: false });

            clientCanvas.addEventListener('touchmove', (e) => {
                e.preventDefault();
                const touch = e.touches[0];
                draw({ clientX: touch.clientX, clientY: touch.clientY });
            }, { passive: false });

            clientCanvas.addEventListener('touchend', (e) => {
                e.preventDefault();
                stopDrawing('clientSignature');
            }, { passive: false });

            // Función para limpiar firma
            function clearSignature(canvasId) {
                const canvas = document.getElementById(canvasId);
                const rect = canvas.getBoundingClientRect();
                const ctx = canvas.getContext('2d');
                const dpr = window.devicePixelRatio || 1;

                // Limpiar todo el canvas
                ctx.setTransform(1, 0, 0, 1, 0, 0);
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                ctx.scale(dpr, dpr);

                // Rellenar con blanco
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, rect.width, rect.height);

                // Restaurar estilos de dibujo
                ctx.strokeStyle = '#1a472a';
                ctx.lineWidth = 2;
                ctx.lineCap = 'round';
                ctx.lineJoin = 'round';

                // Actualizar el contexto correspondiente
                if (canvasId === 'technicianSignature') {
                    technicianCtx = ctx;
                } else {
                    clientCtx = ctx;
                }

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
                button.addEventListener('click', function () {
                    const canvasId = this.getAttribute('data-canvas');
                    clearSignature(canvasId);
                });
            });

            // Prevenir envío del formulario si no hay firmas
            document.getElementById('checklistForm').addEventListener('submit', function (e) {
                const technicianSigned = document.getElementById('technicianSignatureData').value !== '';
                const clientSigned = document.getElementById('clientSignatureData').value !== '';

                if (!technicianSigned || !clientSigned) {
                    e.preventDefault();
                    alert('Por favor, complete ambas firmas antes de finalizar el monitoreo.');
                }
            });
        });
    </script>
@endsection