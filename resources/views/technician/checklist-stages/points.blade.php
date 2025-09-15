<!-- Etapa 1: Check de Puntos -->
<div class="stage-title">Etapa 1: Check de Puntos</div>
<div class="stage-instruction">Marque los puntos que correspondan</div>

<form method="POST" action="{{ route("technician.service.checklist.submit", $service) }}" data-stage="points">
    @csrf
    <input type="hidden" name="next_stage" value="products">
    <input type="hidden" name="current_stage" value="points">    
    <div class="checkbox-group">
        <div class="checkbox-item">
            <input type="checkbox" name="installed_points_check" value="1" 
                   {{ ($service->checklist_data["points"]["installed_points_check"] ?? false) ? "checked" : "" }}
                   id="installed_points_check">
            <label for="installed_points_check">Puntos instalados</label>
        </div>
        
        <div class="checkbox-item">
            <input type="checkbox" name="existing_points_check" value="1" 
                   {{ ($service->checklist_data["points"]["existing_points_check"] ?? false) ? "checked" : "" }}
                   id="existing_points_check">
            <label for="existing_points_check">Puntos existentes</label>
        </div>
        
        <div class="checkbox-item">
            <input type="checkbox" name="spare_points_check" value="1" 
                   {{ ($service->checklist_data["points"]["spare_points_check"] ?? false) ? "checked" : "" }}
                   id="spare_points_check">
            <label for="spare_points_check">Puntos de repuesto</label>
        </div>
        
        <div class="checkbox-item">
            <input type="checkbox" name="bait_weight_check" value="1" 
                   {{ ($service->checklist_data["points"]["bait_weight_check"] ?? false) ? "checked" : "" }}
                   id="bait_weight_check">
            <label for="bait_weight_check">Peso cebo instalado (gramos)</label>
        </div>
        
        <div class="checkbox-item">
            <input type="checkbox" name="physical_installed_check" value="1" 
                   {{ ($service->checklist_data["points"]["physical_installed_check"] ?? false) ? "checked" : "" }}
                   id="physical_installed_check">
            <label for="physical_installed_check">Puntos físicos instalados</label>
        </div>
        
        <div class="checkbox-item">
            <input type="checkbox" name="physical_existing_check" value="1" 
                   {{ ($service->checklist_data["points"]["physical_existing_check"] ?? false) ? "checked" : "" }}
                   id="physical_existing_check">
            <label for="physical_existing_check">Puntos físicos existentes</label>
        </div>
        
        <div class="checkbox-item">
            <input type="checkbox" name="physical_spare_check" value="1" 
                   {{ ($service->checklist_data["points"]["physical_spare_check"] ?? false) ? "checked" : "" }}
                   id="physical_spare_check">
            <label for="physical_spare_check">Puntos físicos de repuesto</label>
        </div>
    </div>
    
    <div class="buttons-container">
        <button type="submit" class="next-button">
            Siguiente <span class="arrow">→</span>
        </button>
    </div>
</form>
