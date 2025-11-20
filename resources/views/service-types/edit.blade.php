@extends("layouts.app")

@section("content")
<div class="container mx-auto px-4 py-6">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Editar Tipo de Servicio</h1>
        
        <div class="bg-white shadow rounded-lg p-6">
            <form method="POST" action="{{ route("admin.service-types.update", $serviceType) }}">
                @csrf
                @method("PUT")
                
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nombre del Tipo de Servicio</label>
                    <input type="text" name="name" id="name" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                           placeholder="Ej: Fumigación, Control de Plagas, etc."
                           value="{{ old("name", $serviceType->name) }}">
                    @error("name")
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                    <textarea name="description" id="description" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                              placeholder="Describe brevemente este tipo de servicio">{{ old("description", $serviceType->description) }}</textarea>
                    @error("description")
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-6">
                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="is_active" value="1"
                               class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded"
                               {{ old("is_active", $serviceType->is_active) ? "checked" : "" }}>
                        <label for="is_active" class="ml-2 block text-sm text-gray-900">
                            Tipo de servicio activo
                        </label>
                    </div>
                    @error("is_active")
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="flex justify-end space-x-3">
                    <a href="{{ route("admin.service-types.index") }}" 
                       class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                        Actualizar Tipo de Servicio
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
