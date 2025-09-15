<!-- Etapa 5: Sitios Tratados -->
<div class="stage-title">Etapa 5: Sitios Tratados</div>
<div class="stage-instruction">Describa los sitios donde se realizó el tratamiento</div>

<form method="POST" action="{{ route("technician.service.checklist.submit", $service) }}" data-stage="sites">
    @csrf
    <input type="hidden" name="next_stage" value="description">
    <input type="hidden" name="current_stage" value="sites">    
    <div class="form-group">
        <label>Sitios Tratados</label>
        <textarea name="treated_sites" rows="8" 
                  placeholder="Describa detalladamente los sitios tratados, áreas intervenidas, ubicaciones específicas, condiciones del lugar, etc...">{{ $service->checklist_data["sites"]["treated_sites"] ?? "" }}</textarea>
        <p class="field-help">Incluya información detallada sobre las áreas tratadas, condiciones del lugar, y cualquier observación relevante.</p>
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

<style>
.field-help {
    font-size: 12px;
    color: #666;
    margin-top: 5px;
    font-style: italic;
}
</style>
