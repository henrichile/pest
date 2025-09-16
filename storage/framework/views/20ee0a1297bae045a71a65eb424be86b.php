<?php $__env->startSection('css'); ?>
<style>
    .field-help {
        font-size: 12px;
        color: #666;
        margin-top: 5px;
        font-style: italic;
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<!-- Etapa 5: Sitios Tratados -->
<div class="stage-title">Etapa 5: Sitios Tratados</div>
<div class="stage-instruction">Describa los sitios donde se realizó el tratamiento</div>

<form method="POST" action="<?php echo e(route("technician.service.checklist.submit", $service)); ?>" data-stage="sites">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="next_stage" value="description">
    <input type="hidden" name="current_stage" value="sites">    
    <div class="form-group">
        <label>Sitios Tratados</label>
        <textarea name="treated_sites" rows="8" 
                  placeholder="Describa detalladamente los sitios tratados, áreas intervenidas, ubicaciones específicas, condiciones del lugar, etc..."><?php echo e($service->checklist_data["sites"]["treated_sites"] ?? ""); ?></textarea>
        <p class="field-help">Incluya información detallada sobre las áreas tratadas, condiciones del lugar, y cualquier observación relevante.</p>
    </div>
    
    <div class="buttons-container">
        <a href="<?php echo e(route("technician.service.checklist.stage", ["service" => $service, "stage" => "observations"])); ?>" class="back-button">
            <span class="arrow">←</span> Anterior
        </a>
        <button type="submit" class="next-button">
            Siguiente <span class="arrow">→</span>
        </button>
    </div>
</form>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app-tec', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /media/kike/Linux/pest/resources/views/technician/checklist-stages/sites.blade.php ENDPATH**/ ?>