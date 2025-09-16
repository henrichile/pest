<?php $__env->startSection('content'); ?>

<!-- Etapa 2: Productos Aplicados -->
<div class="stage-title">Etapa 2: Productos Aplicados</div>
<div class="stage-instruction">Seleccione el producto utilizado</div>

<form method="POST" action="<?php echo e(route("technician.service.checklist.submit", $service)); ?>" data-stage="products">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="next_stage" value="results">
    <input type="hidden" name="current_stage" value="products">    
    <div class="radio-group">
        <div class="radio-item">
            <input type="radio" name="applied_product" value="DETIA PLUS BLOQUE (Brodifacoum 0.005%) (P-426/15)"
                   <?php echo e(($service->checklist_data["products"]["applied_product"] ?? "") === "DETIA PLUS BLOQUE (Brodifacoum 0.005%) (P-426/15)" ? "checked" : ""); ?>

                   id="product_1">
            <label for="product_1">DETIA PLUS BLOQUE (Brodifacoum 0.005%) (P-426/15)</label>
        </div>
        
        <div class="radio-item">
            <input type="radio" name="applied_product" value="RASTOP MOLIENDA (Bromadolona 0.005%) (P-446/16)"
                   <?php echo e(($service->checklist_data["products"]["applied_product"] ?? "") === "RASTOP MOLIENDA (Bromadolona 0.005%) (P-446/16)" ? "checked" : ""); ?>

                   id="product_2">
            <label for="product_2">RASTOP MOLIENDA (Bromadolona 0.005%) (P-446/16)</label>
        </div>
        
        <div class="radio-item">
            <input type="radio" name="applied_product" value="PASTA RASTOP (Bromadolona 0.005%) (P-498/16)"
                   <?php echo e(($service->checklist_data["products"]["applied_product"] ?? "") === "PASTA RASTOP (Bromadolona 0.005%) (P-498/16)" ? "checked" : ""); ?>

                   id="product_3">
            <label for="product_3">PASTA RASTOP (Bromadolona 0.005%) (P-498/16)</label>
        </div>
    </div>
    
    <div class="buttons-container">
        <a href="<?php echo e(route("technician.service.checklist.stage", ["service" => $service, "stage" => "points"])); ?>" class="back-button">
            <span class="arrow">←</span> Anterior
        </a>
        <button type="submit" class="next-button">
            Siguiente <span class="arrow">→</span>
        </button>
    </div>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app-tec', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /media/kike/Linux/pest/resources/views/technician/checklist-stages/products.blade.php ENDPATH**/ ?>