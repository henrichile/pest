<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Servicio - <?php echo e($service->id); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            line-height: 1.4;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #1a472a;
            padding-bottom: 20px;
            position: relative;
        }
        
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #1a472a;
            margin-bottom: 10px;
        }
        
        .title {
            font-size: 20px;
            font-weight: bold;
            color: #333;
        }
        
        .qr-code {
            position: absolute;
            top: 0;
            right: 0;
            width: 80px;
            height: 80px;
            border: 1px solid #ddd;
        }
        
        .service-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding: 5px 0;
        }
        
        .info-label {
            font-weight: bold;
            color: #1a472a;
        }
        
        .info-value {
            color: #333;
        }
        
        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #1a472a;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        
        .checklist-item {
            margin-bottom: 8px;
            padding: 5px 0;
        }
        
        .observation-item {
            background: #f8f9fa;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
            border-left: 4px solid #1a472a;
        }
        
        .observation-header {
            font-weight: bold;
            color: #1a472a;
            margin-bottom: 8px;
        }
        
        .observation-detail {
            margin-bottom: 8px;
        }
        
        .observation-photo {
            max-width: 200px;
            max-height: 150px;
            border-radius: 8px;
            margin-top: 10px;
            border: 1px solid #ddd;
        }
        
        .signature-section {
            margin-top: 30px;
            border-top: 2px solid #1a472a;
            padding-top: 20px;
        }
        
        .signature-box {
            display: inline-block;
            width: 45%;
            margin: 10px 2%;
            text-align: center;
        }
        
        .signature-image {
            max-width: 200px;
            max-height: 80px;
            border: 1px solid #ddd;
            margin-bottom: 5px;
        }
        
        .signature-line {
            border-bottom: 1px solid #333;
            height: 40px;
            margin-bottom: 5px;
        }
        
        .signature-label {
            font-size: 12px;
            color: #666;
        }
        
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .status-finalizado {
            background: #d4edda;
            color: #155724;
        }
        
        .priority-alta {
            background: #f8d7da;
            color: #721c24;
        }
        
        .priority-media {
            background: #fff3cd;
            color: #856404;
        }
        
        .priority-baja {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .points-list {
            margin-left: 20px;
        }
        
        .points-list li {
            margin-bottom: 5px;
        }
        
        .no-data {
            color: #666;
            font-style: italic;
        }
        
        .geolocation-info {
            background: #e8f5e8;
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
        }
        
        .product-info {
            background: #fff3cd;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        
        .technical-findings {
            background: #f8d7da;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        
        .validation-info {
            background: #d1ecf1;
            padding: 10px;
            border-radius: 5px;
            margin-top: 20px;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">PEST CONTROLLER</div>
        <div class="title">REPORTE DE SERVICIO COMPLETADO</div>
        <?php if(isset($qrCode)): ?>
        <div class="qr-code">
            <img src="data:image/png;base64,<?php echo e($qrCode); ?>" alt="QR de Validación" style="width: 100%; height: 100%;">
        </div>
        <?php endif; ?>
    </div>
    
    
    <div class="service-info">
        <div class="info-row">
            <span class="info-label">Número de Servicio:</span>
            <span class="info-value">#<?php echo e($service->id); ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Cliente:</span>
            <span class="info-value"><?php echo e($service->client->name ?? "N/A"); ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Dirección:</span>
            <span class="info-value"><?php echo e($service->address ?? "N/A"); ?></span>
        </div>
        <?php if($service->latitude && $service->longitude): ?>
        <div class="geolocation-info">
            <div class="info-row">
                <span class="info-label">Coordenadas GPS:</span>
                <span class="info-value"><?php echo e($service->latitude); ?>, <?php echo e($service->longitude); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Enlace Google Maps:</span>
                <span class="info-value">https://maps.google.com/?q=<?php echo e($service->latitude); ?>,<?php echo e($service->longitude); ?></span>
            </div>
        </div>
        <?php endif; ?>
        <div class="info-row">
            <span class="info-label">Tipo de Servicio:</span>
            <span class="info-value"><?php echo e($service->serviceType->name ?? "N/A"); ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Técnico Asignado:</span>
            <span class="info-value"><?php echo e($service->assignedUser->name ?? "N/A"); ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Fecha de Servicio:</span>
            <span class="info-value"><?php echo e($service->scheduled_date ? $service->scheduled_date->format("d/m/Y H:i") : "N/A"); ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Fecha de Finalización:</span>
            <span class="info-value"><?php echo e($service->checklist_completed_at ? $service->checklist_completed_at->format("d/m/Y H:i") : "N/A"); ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Estado:</span>
            <span class="info-value">
                <span class="status-badge status-finalizado">FINALIZADO</span>
            </span>
        </div>
        <div class="info-row">
            <span class="info-label">Prioridad:</span>
            <span class="info-value">
                <?php
                    $priority = strtoupper($service->priority ?? "MEDIA");
                    $priorityClass = 'priority-' . strtolower($service->priority ?? "media");
                ?>
                <span class="status-badge <?php echo e($priorityClass); ?>"><?php echo e($priority); ?></span>
            </span>
        </div>
    </div>
    
    
    <?php if($service->checklist_data): ?>
        <div class="section">
            <div class="section-title">Hallazgos Técnicos - Puntos de Control</div>
            <ul class="points-list">
                <?php if(isset($service->checklist_data["points"]) && count($service->checklist_data["points"]) > 0): ?>
                    <?php $__currentLoopData = $service->checklist_data["points"]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $point): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($point); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <li>No hay puntos de control registrados</li>
                <?php endif; ?>
            </ul>
        </div>
        
        <?php if(isset($service->checklist_data["results"]) && count($service->checklist_data["results"]) > 0): ?>
        <div class="section">
            <div class="section-title">Hallazgos Técnicos - Resultados Observados</div>
            <div class="technical-findings">
                <ul class="points-list">
                    <?php if(isset($service->checklist_data["results"]["observed_results"]) && count($service->checklist_data["results"]["observed_results"]) > 0): ?>
                        <?php $__currentLoopData = $service->checklist_data["results"]["observed_results"]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($result); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        <li>No hay resultados observados registrados</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        <?php endif; ?>
    
    
    <?php if($service->checklist_data && isset($service->checklist_data["products"]["applied_product"])): ?>
    <div class="section">
        <div class="section-title">Insumos Utilizados</div>
        <div class="product-info">
            <strong>Producto:</strong> <?php echo e($service->checklist_data["products"]["applied_product"]); ?>

        </div>
    <?php else: ?>
        <div class="section">
            <div class="section-title">Insumos Utilizados</div>
            <div class="product-info">No hay productos aplicados registrados</div>
        </div>
    <?php endif; ?>
    
    
    <?php if($service->checklist_data && isset($service->checklist_data["observations"]) && count($service->checklist_data["observations"]) > 0): ?>
    <div class="section">
        <div class="section-title">Observaciones Detalladas con Fotografías</div>
        <?php $__currentLoopData = $service->checklist_data["observations"]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $observation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="observation-item">
            <div class="observation-header">
                Observación #<?php echo e($observation['observation_number'] ?? ($index + 1)); ?>

                <?php if(isset($observation['cebadera_code'])): ?>
                    - CE: <?php echo e($observation['cebadera_code']); ?>

                <?php endif; ?>
            </div>
            <div class="observation-detail">
                <strong>Detalle:</strong> <?php echo e($observation['detail'] ?? 'No especificado'); ?>

            </div>
            <?php if(isset($observation['created_at'])): ?>
            <div class="observation-detail">
                <strong>Fecha:</strong> <?php echo e(\Carbon\Carbon::parse($observation['created_at'])->format('d/m/Y H:i')); ?>

            </div>
            <?php endif; ?>
            <?php if(isset($observation['photo']) && $observation['photo']): ?>
            <div class="observation-detail">
                <strong>Fotografía:</strong><br>
                <img src="<?php echo e(storage_path('app/public/') . str_replace('storage/', '', $observation['photo'])); ?>" alt="Foto de observación" class="observation-photo">
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php else: ?>
        <div class="observation-item">No hay observaciones registradas</div>
    <?php endif; ?>
    </div>
    <?php endif; ?>
    
    
    <?php if($service->checklist_data && isset($service->checklist_data["sites"]["treated_sites"]) && !empty($service->checklist_data["sites"]["treated_sites"])): ?>
    <div class="section">
        <div class="section-title">Sitios Tratados</div>
        <div class="checklist-item"><?php echo e($service->checklist_data["sites"]["treated_sites"]); ?></div>
    </div>
    <?php endif; ?>
    
    
    <?php if($service->checklist_data && isset($service->checklist_data["description"]["content"])): ?>
    <div class="section">
        <div class="section-title">Descripción del Servicio</div>
        <div class="checklist-item"><?php echo e($service->checklist_data["description"]["content"]); ?></div>
    </div>
    <?php endif; ?>
    
    
    <div class="signature-section">
        <div class="section-title">Firmas de Confirmación</div>
        
        <?php if($service->checklist_data && isset($service->checklist_data["description"]["technician_signature"]) && $service->checklist_data["description"]["technician_signature"]): ?>
        <div class="signature-box">
            <div class="signature-label">Firma del Técnico</div>
            <img src="<?php echo e($service->checklist_data["description"]["technician_signature"]); ?>" alt="Firma del Técnico" class="signature-image">
            <div class="signature-label"><?php echo e($service->assignedUser->name ?? "Técnico"); ?></div>
        </div>
        <?php else: ?>
        <div class="signature-box">
            <div class="signature-line"></div>
            <div class="signature-label">Firma del Técnico</div>
        </div>
        <?php endif; ?>
        
        <?php if($service->checklist_data && isset($service->checklist_data["description"]["client_signature"]) && $service->checklist_data["description"]["client_signature"]): ?>
        <div class="signature-box">
            <div class="signature-label">Firma del Cliente</div>
            <img src="<?php echo e($service->checklist_data["description"]["client_signature"]); ?>" alt="Firma del Cliente" class="signature-image">
            <div class="signature-label"><?php echo e($service->client->name ?? "Cliente"); ?></div>
        </div>
        <?php else: ?>
        <div class="signature-box">
            <div class="signature-line"></div>
            <div class="signature-label">Firma del Cliente</div>
        </div>
        <?php endif; ?>
        
        <div style="clear: both;"></div>
        <div style="margin-top: 20px; text-align: center;">
            <div class="signature-label">Fecha de Finalización: <?php echo e($service->checklist_completed_at ? $service->checklist_completed_at->format("d/m/Y H:i") : date("d/m/Y H:i")); ?></div>
        </div>
    </div>
    
    
    <div class="validation-info">
        <div class="section-title">Información de Validación</div>
        <div class="info-row">
            <span class="info-label">ID de Validación:</span>
            <span class="info-value"><?php echo e($validationId ?? 'N/A'); ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Hash de Integridad:</span>
            <span class="info-value"><?php echo e($integrityHash ?? 'N/A'); ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Fecha de Generación:</span>
            <span class="info-value"><?php echo e(now()->format("d/m/Y H:i:s")); ?></span>
        </div>
    </div>
    
    <div class="footer">
        <p>Este documento fue generado automáticamente por el sistema Pest Controller</p>
        <p>Servicio completado por: <?php echo e($service->assignedUser->name ?? "Técnico asignado"); ?></p>
        <p>Documento con trazabilidad digital - QR de validación incluido</p>
    </div>
</body>
</html>
<?php /**PATH /var/www/html/pest-controller/resources/views/technician/service-pdf.blade.php ENDPATH**/ ?>