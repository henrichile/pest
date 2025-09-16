@extends('layouts.app-tec')

@section('content')

<!-- Etapa 3: Resultados Observados -->
<div class="stage-title">Etapa 3: Resultados Observados</div>
<div class="stage-instruction">Marque los resultados encontrados</div>

<form method="POST" action="{{ route("technician.service.checklist.submit", $service) }}" data-stage="results">
    @csrf
    <input type="hidden" name="next_stage" value="observations">
    <input type="hidden" name="current_stage" value="results">    
    <div class="checkbox-group">
        @php
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
        @endphp
        
        @foreach($options as $number => $option)
        <div class="checkbox-item">
            <input type="checkbox" name="observed_results[]" value="{{ $option }}" 
                   {{ in_array($option, $observedResults) ? "checked" : "" }}
                   id="result_{{ $number }}">
            <label for="result_{{ $number }}">{{ $number }} | {{ $option }}</label>
        </div>
        @endforeach
    </div>
    
    <!-- Campos adicionales -->
    <div class="form-group">
        <label>PUNTOS INSTALADOS</label>
        <input type="number" name="total_installed_points" 
               value="{{ $service->checklist_data["results"]["total_installed_points"] ?? "" }}"
               min="0" placeholder="Cantidad de puntos instalados">
    </div>
    
    <div class="form-group">
        <label>ACTIVIDAD DE CONSUMO TOTAL</label>
        <input type="number" name="total_consumption_activity" 
               value="{{ $service->checklist_data["results"]["total_consumption_activity"] ?? "" }}"
               step="0.01" min="0" placeholder="Actividad de consumo">
    </div>
    
    <div class="buttons-container">
<<<<<<< Updated upstream
        <a href="{{ route("technician.service.checklist.stage", ["service" => $service, "stage" => "products"]) }}" class="back-button">
=======
        <a href="{{ route("technician.service.checklist.stage", ["service" => $service, "stage" => "observations"]) }}" class="back-button">
>>>>>>> Stashed changes
            <span class="arrow">←</span> Anterior
        </a>
        <button type="submit" class="next-button">
            Siguiente <span class="arrow">→</span>
        </button>
    </div>
</form>
@endsection
