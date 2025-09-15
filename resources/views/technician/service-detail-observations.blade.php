@extends("layouts.app")

@section("title", "Detalle del Servicio - Pest Controller SAT")
@section("page-title", "Detalle del Servicio")

@section("content")
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Detalle del Servicio</h2>
            <p class="text-gray-600">Cliente: {{ $service->client->name }}</p>
            <p class="text-gray-600">Fecha: {{ $service->scheduled_date->format("d/m/Y H:i") }}</p>
        </div>
        <div class="flex items-center space-x-4">
            <a href="{{ route("technician.service.show", $service) }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                Volver al Servicio
            </a>
        </div>
    </div>

    <!-- Información del Checklist -->
    @if($service->checklist_completed)
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Información del Checklist</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">Puntos Instalados</label>
                <p class="text-lg font-semibold text-gray-900">{{ $service->installed_points ?? "N/A" }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Puntos Existentes</label>
                <p class="text-lg font-semibold text-gray-900">{{ $service->existing_points ?? "N/A" }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Puntos de Repuesto</label>
                <p class="text-lg font-semibold text-gray-900">{{ $service->spare_points ?? "N/A" }}</p>
            </div>
        </div>
        
        <div class="mt-6">
            <label class="block text-sm font-medium text-gray-700">Productos Aplicados</label>
            <p class="text-gray-900">{{ $service->applied_products ?? "No especificado" }}</p>
        </div>
        
        @if($service->observed_results)
        <div class="mt-6">
            <label class="block text-sm font-medium text-gray-700">Resultados Observados</label>
            <div class="flex flex-wrap gap-2 mt-2">
                @foreach($service->observed_results as $result)
                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">{{ $result }}</span>
                @endforeach
            </div>
        </div>
        @endif
        
        <div class="mt-6">
            <label class="block text-sm font-medium text-gray-700">Sitios Tratados</label>
            <p class="text-gray-900">{{ $service->treated_sites ?? "No especificado" }}</p>
        </div>
    </div>
    @endif

    <!-- Observaciones Existentes -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Observaciones Registradas</h3>
        
        @if($service->observations->count() > 0)
        <div class="space-y-4">
            @foreach($service->observations as $observation)
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex justify-between items-start mb-2">
                    <div class="flex items-center space-x-4">
                        @if($observation->bait_station_code)
                        <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-sm">
                            CE: {{ $observation->bait_station_code }}
                        </span>
                        @endif
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-sm">
                            OBS: {{ $observation->observation_number }}
                        </span>
                    </div>
                    <span class="text-sm text-gray-500">{{ $observation->created_at->format("d/m/Y H:i") }}</span>
                </div>
                
                <p class="text-gray-900 mb-2">{{ $observation->detail }}</p>
                
                @if($observation->photo_path)
                <div class="mb-2">
                    <img src="{{ asset("storage/" . $observation->photo_path) }}" 
                         alt="Foto de observación" 
                         class="w-48 h-48 object-cover rounded-lg shadow-sm border border-gray-200">
                </div>
                @endif
                
                @if($observation->complementary_observation)
                <div class="mt-2 p-2 bg-gray-50 rounded">
                    <p class="text-sm text-gray-700">{{ $observation->complementary_observation }}</p>
                </div>
                @endif
            </div>
            @endforeach
        </div>
        @else
        <p class="text-gray-500 text-center py-8">No hay observaciones registradas aún</p>
        @endif
    </div>

    <!-- Formulario para Nueva Observación -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Agregar Nueva Observación</h3>
        
        <form method="POST" action="{{ route("technician.service.observations.save", $service) }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Código de la Cebadera (N° CE)</label>
                    <input type="text" name="bait_station_code" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                           placeholder="Ej: CE-001">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Número de Observación (N° OBS)</label>
                    <input type="number" name="observation_number" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                           min="1" value="{{ $service->observations->count() + 1 }}" required>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Detalle de la Observación</label>
                <textarea name="detail" rows="3" 
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                          placeholder="Describa detalladamente la observación..." required></textarea>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Foto de la Estación/Cebadera</label>
                <input type="file" name="photo" accept="image/*" 
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-sm text-gray-500 mt-1">Formatos permitidos: JPG, PNG, GIF. Máximo 2MB</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Observación Complementaria</label>
                <textarea name="complementary_observation" rows="2" 
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                          placeholder="Información adicional o comentarios..."></textarea>
            </div>
            
            <div class="flex justify-end">
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    Guardar Observación
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
