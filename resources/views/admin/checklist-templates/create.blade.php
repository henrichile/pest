@extends('layouts.app')

@section('title', 'Crear Template de Checklist - Pest Controller SAT')
@section('page-title', 'Crear Template de Checklist')

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('admin.checklist-templates.store') }}" method="POST" x-data="checklistForm()" class="space-y-6">
        @csrf
        
        <!-- Header -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-gray-900">Información del Template</h2>
                <a href="{{ route('admin.checklist-templates.index') }}" 
                   class="text-gray-600 hover:text-gray-900">
                    <i class="fas fa-arrow-left mr-2"></i>Volver
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nombre del Template *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500 @error('name') border-red-500 @enderror"
                           placeholder="Ej: Checklist Desratización Estándar">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Servicio *</label>
                    <select name="service_type_id" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500 @error('service_type_id') border-red-500 @enderror">
                        <option value="">Seleccionar tipo de servicio</option>
                        @foreach($serviceTypes as $serviceType)
                        <option value="{{ $serviceType->id }}" {{ (old('service_type_id') ?: request('service_type_id')) == $serviceType->id ? 'selected' : '' }}>
                            {{ $serviceType->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('service_type_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                    <textarea name="description" rows="3"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500 @error('description') border-red-500 @enderror"
                              placeholder="Descripción opcional del template">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Items del Checklist -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-gray-900">Items del Checklist</h2>
                <button type="button" @click="addItem()" 
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-plus mr-2"></i>Agregar Item
                </button>
            </div>

            <div id="items-container">
                <template x-for="(item, index) in items" :key="index">
                    <div class="border border-gray-200 rounded-lg p-4 mb-4 relative">
                        <!-- Botón eliminar -->
                        <button type="button" @click="removeItem(index)" 
                                class="absolute top-2 right-2 text-red-600 hover:text-red-900">
                            <i class="fas fa-trash"></i>
                        </button>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Título *</label>
                                <input type="text" :name="'items[' + index + '][title]'" x-model="item.title" required
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500"
                                       placeholder="Título del item">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tipo *</label>
                                <select :name="'items[' + index + '][type]'" x-model="item.type" required
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                                    <option value="">Seleccionar tipo</option>
                                    <option value="text">Texto</option>
                                    <option value="number">Número</option>
                                    <option value="select">Lista desplegable</option>
                                    <option value="checkbox">Checkbox</option>
                                    <option value="file">Archivo</option>
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                                <textarea :name="'items[' + index + '][description]'" x-model="item.description" rows="2"
                                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500"
                                          placeholder="Descripción del item (opcional)"></textarea>
                            </div>

                            <!-- Opciones para select -->
                            <div x-show="item.type === 'select'" class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Opciones (separadas por comas)</label>
                                <input type="text" :name="'items[' + index + '][options]'" x-model="item.options"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500"
                                       placeholder="Opción 1, Opción 2, Opción 3">
                            </div>

                            <!-- Campo requerido -->
                            <div class="flex items-center">
                                <input type="checkbox" :name="'items[' + index + '][is_required]'" x-model="item.is_required" value="1"
                                       class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                <label class="ml-2 text-sm text-gray-700">Campo obligatorio</label>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Mensaje cuando no hay items -->
                <div x-show="items.length === 0" class="text-center py-8 text-gray-500">
                    <i class="fas fa-clipboard-list text-3xl mb-2"></i>
                    <p>No hay items en el checklist. Agrega al menos uno para continuar.</p>
                </div>
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('admin.checklist-templates.index') }}" 
               class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg transition-colors">
                Cancelar
            </a>
            <button type="submit" 
                    class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg transition-colors">
                <i class="fas fa-save mr-2"></i>Crear Template
            </button>
        </div>
    </form>
</div>

<script>
function checklistForm() {
    return {
        items: [
            {
                title: '',
                description: '',
                type: '',
                options: '',
                is_required: false
            }
        ],
        
        addItem() {
            this.items.push({
                title: '',
                description: '',
                type: '',
                options: '',
                is_required: false
            });
        },
        
        removeItem(index) {
            if (this.items.length > 1) {
                this.items.splice(index, 1);
            }
        }
    }
}
</script>
@endsection
