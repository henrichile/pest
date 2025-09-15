<!-- Etapa 2: Productos Aplicados -->
<div class="stage-title">Etapa 2: Productos Aplicados</div>
<div class="stage-instruction">Seleccione el producto utilizado</div>

<form method="POST" action="{{ route("technician.service.checklist.submit", $service) }}" data-stage="products">
    @csrf
    <input type="hidden" name="next_stage" value="results">
    <input type="hidden" name="current_stage" value="products">    
    <div class="radio-group">
        <div class="radio-item">
            <input type="radio" name="applied_product" value="DETIA PLUS BLOQUE (Brodifacoum 0.005%) (P-426/15)"
                   {{ ($service->checklist_data["products"]["applied_product"] ?? "") === "DETIA PLUS BLOQUE (Brodifacoum 0.005%) (P-426/15)" ? "checked" : "" }}
                   id="product_1">
            <label for="product_1">DETIA PLUS BLOQUE (Brodifacoum 0.005%) (P-426/15)</label>
        </div>
        
        <div class="radio-item">
            <input type="radio" name="applied_product" value="RASTOP MOLIENDA (Bromadolona 0.005%) (P-446/16)"
                   {{ ($service->checklist_data["products"]["applied_product"] ?? "") === "RASTOP MOLIENDA (Bromadolona 0.005%) (P-446/16)" ? "checked" : "" }}
                   id="product_2">
            <label for="product_2">RASTOP MOLIENDA (Bromadolona 0.005%) (P-446/16)</label>
        </div>
        
        <div class="radio-item">
            <input type="radio" name="applied_product" value="PASTA RASTOP (Bromadolona 0.005%) (P-498/16)"
                   {{ ($service->checklist_data["products"]["applied_product"] ?? "") === "PASTA RASTOP (Bromadolona 0.005%) (P-498/16)" ? "checked" : "" }}
                   id="product_3">
            <label for="product_3">PASTA RASTOP (Bromadolona 0.005%) (P-498/16)</label>
        </div>
    </div>
    
    <div class="buttons-container">
        <a href="{{ route("technician.service.checklist.submit", $service) }}" class="back-button">
            <span class="arrow">←</span> Anterior
        </a>
        <button type="submit" class="next-button">
            Siguiente <span class="arrow">→</span>
        </button>
    </div>
</form>
