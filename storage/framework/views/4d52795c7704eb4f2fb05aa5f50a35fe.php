<!-- Etapa 3: Resultados Observados -->
<div class="stage-title">Etapa 3: Resultados Observados</div>
<div class="stage-instruction">Marque los resultados encontrados</div>

<form method="POST" action="<?php echo e(route("technician.service.checklist.submit", $service)); ?>" data-stage="results">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="next_stage" value="observations">
    <input type="hidden" name="current_stage" value="results">    
    <div class="checkbox-group">
        <?php
            $observedResults = $service->checklist_data["results"]["observed_results"] ?? [];
            $options = [
                "1" => "Roído",
                "2" => "Muestra roedor", 
                "3" => "Sustraído",
                "4" => "Destruido",
                "5" => "Hongo",
                "6" => "Polvo",
                "7" => "Captura roedor",
                "8" => "Bloqueado",
                "9" => "Babosa"
            ];
        ?>
        
        <?php $__currentLoopData = $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $number => $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="checkbox-item">
            <input type="checkbox" name="observed_results[]" value="<?php echo e($option); ?>" 
                   <?php echo e(in_array($option, $observedResults) ? "checked" : ""); ?>

                   id="result_<?php echo e($number); ?>">
            <label for="result_<?php echo e($number); ?>"><?php echo e($number); ?> | <?php echo e($option); ?></label>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    
    <!-- Campos adicionales -->
    <div class="form-group">
        <label>PUNTOS INSTALADOS</label>
        <input type="number" name="total_installed_points" 
               value="<?php echo e($service->checklist_data["results"]["total_installed_points"] ?? ""); ?>"
               min="0" placeholder="Cantidad de puntos instalados">
    </div>
    
    <div class="form-group">
        <label>ACTIVIDAD DE CONSUMO TOTAL</label>
        <input type="number" name="total_consumption_activity" 
               value="<?php echo e($service->checklist_data["results"]["total_consumption_activity"] ?? ""); ?>"
               step="0.01" min="0" placeholder="Actividad de consumo">
    </div>
    
    <div class="buttons-container">
        <a href="<?php echo e(route("technician.service.checklist.submit", $service)); ?>" class="back-button">
            <span class="arrow">←</span> Anterior
        </a>
        <button type="submit" class="next-button">
            Siguiente <span class="arrow">→</span>
        </button>
    </div>
</form>
<?php /**PATH /var/www/html/pest-controller/resources/views/technician/checklist-stages/results.blade.php ENDPATH**/ ?>