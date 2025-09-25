@extends('layouts.app')

@section('title', 'Crear Template de Checklist por Etapas')

@section('content')
<div class="container mx-auto px-4 py-8" x-data="checklistTemplateForm()">
    <div class="max-w-6xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg">
            <!-- Header -->
            <div class="border-b border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold text-gray-900">
                        <i class="fas fa-clipboard-list text-green-600 mr-3"></i>
                        Crear Template de Checklist por Etapas
                    </h1>
                    <a href="{{ route('admin.checklist-templates.index') }}" 
                       class="text-gray-600 hover:text-gray-900 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Volver
                    </a>
                </div>
            </div>

            <form action="{{ route('admin.checklist-templates.store') }}" method="POST" class="p-6">
                @csrf
                
                <!-- Información Básica -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nombre del Template *
                        </label>
                        <input type="text" 
                               name="name" 
                               x-model="template.name"
                               required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Tipo de Servicio *
                        </label>
                        <select name="service_type_id" 
                                x-model="template.serviceTypeId"
                                required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                            <option value="">Seleccionar tipo de servicio</option>
                            @foreach($serviceTypes as $serviceType)
                                <option value="{{ $serviceType->id }}">{{ $serviceType->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-8">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Descripción
                    </label>
                    <textarea name="description" 
                              x-model="template.description"
                              rows="3"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500"></textarea>
                </div>

                <!-- Etapas del Checklist -->
                <div class="mb-8">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-semibold text-gray-900">
                            <i class="fas fa-layer-group text-blue-600 mr-2"></i>
                            Etapas del Checklist
                        </h2>
                        <button type="button" 
                                @click="addStage()"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                            <i class="fas fa-plus mr-2"></i>Agregar Etapa
                        </button>
                    </div>

                    <!-- Lista de Etapas -->
                    <div class="space-y-6">
                        <template x-for="(stage, stageIndex) in template.stages" :key="stageIndex">
                            <div class="border border-gray-300 rounded-lg p-6 bg-gray-50">
                                <!-- Header de la Etapa -->
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-medium text-gray-900">
                                        Etapa <span x-text="stageIndex + 1"></span>
                                    </h3>
                                    <button type="button" 
                                            @click="removeStage(stageIndex)"
                                            x-show="template.stages.length > 1"
                                            class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>

                                <!-- Hidden inputs para la etapa -->
                                <input type="hidden" :name="`stages[${stageIndex}][name]`" x-bind:value="stage.name">
                                <input type="hidden" :name="`stages[${stageIndex}][description]`" x-bind:value="stage.description">
                                <input type="hidden" :name="`stages[${stageIndex}][is_required]`" x-bind:value="stage.isRequired ? '1' : '0'">

                                <!-- Datos de la Etapa -->
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Nombre de la Etapa *
                                        </label>
                                        <input type="text" 
                                               x-model="stage.name"
                                               required
                                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    
                                    <div>
                                        <label class="flex items-center">
                                            <input type="checkbox" 
                                                   x-model="stage.isRequired"
                                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                            <span class="ml-2 text-sm text-gray-700">Etapa obligatoria</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Descripción de la Etapa
                                    </label>
                                    <textarea x-model="stage.description"
                                              rows="2"
                                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                                </div>

                                <!-- Items de la Etapa -->
                                <div class="border-t border-gray-300 pt-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="text-md font-medium text-gray-800">
                                            <i class="fas fa-check-square text-green-600 mr-2"></i>
                                            Puntos de Verificación
                                        </h4>
                                        <button type="button" 
                                                @click="addItem(stageIndex)"
                                                class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm transition-colors">
                                            <i class="fas fa-plus mr-1"></i>Agregar Punto
                                        </button>
                                    </div>

                                    <!-- Lista de Items -->
                                    <div class="space-y-4">
                                        <template x-for="(item, itemIndex) in stage.items" :key="itemIndex">
                                            <div class="border border-gray-200 rounded-lg p-4 bg-white">
                                                <!-- Hidden inputs para el item -->
                                                <input type="hidden" :name="`stages[${stageIndex}][items][${itemIndex}][title]`" x-bind:value="item.title">
                                                <input type="hidden" :name="`stages[${stageIndex}][items][${itemIndex}][description]`" x-bind:value="item.description">
                                                <input type="hidden" :name="`stages[${stageIndex}][items][${itemIndex}][type]`" x-bind:value="item.type">
                                                <input type="hidden" :name="`stages[${stageIndex}][items][${itemIndex}][options]`" x-bind:value="item.options">
                                                <input type="hidden" :name="`stages[${stageIndex}][items][${itemIndex}][is_required]`" x-bind:value="item.isRequired ? '1' : '0'">

                                                <div class="flex items-center justify-between mb-3">
                                                    <span class="text-sm font-medium text-gray-600">
                                                        Punto <span x-text="itemIndex + 1"></span>
                                                    </span>
                                                    <button type="button" 
                                                            @click="removeItem(stageIndex, itemIndex)"
                                                            x-show="stage.items.length > 1"
                                                            class="text-red-600 hover:text-red-900 text-sm">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>

                                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
                                                    <div>
                                                        <label class="block text-xs font-medium text-gray-600 mb-1">
                                                            Título del Punto *
                                                        </label>
                                                        <input type="text" 
                                                               x-model="item.title"
                                                               required
                                                               class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:ring-green-500 focus:border-green-500">
                                                    </div>

                                                    <div>
                                                        <label class="block text-xs font-medium text-gray-600 mb-1">
                                                            Tipo de Campo *
                                                        </label>
                                                        <select x-model="item.type"
                                                                required
                                                                class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:ring-green-500 focus:border-green-500">
                                                            <option value="">Seleccionar tipo</option>
                                                            <option value="text">Texto</option>
                                                            <option value="number">Número</option>
                                                            <option value="select">Selección</option>
                                                            <option value="checkbox">Checkbox</option>
                                                            <option value="file">Archivo</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="mt-3">
                                                    <label class="block text-xs font-medium text-gray-600 mb-1">
                                                        Descripción/Instrucciones
                                                    </label>
                                                    <textarea x-model="item.description"
                                                              rows="2"
                                                              class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:ring-green-500 focus:border-green-500"></textarea>
                                                </div>

                                                <!-- Opciones para Select -->
                                                <div x-show="item.type === 'select'" class="mt-3">
                                                    <label class="block text-xs font-medium text-gray-600 mb-1">
                                                        Opciones (separadas por coma)
                                                    </label>
                                                    <input type="text" 
                                                           x-model="item.options"
                                                           placeholder="Opción 1, Opción 2, Opción 3"
                                                           class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:ring-green-500 focus:border-green-500">
                                                </div>

                                                <div class="mt-3">
                                                    <label class="flex items-center">
                                                        <input type="checkbox" 
                                                               x-model="item.isRequired"
                                                               class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                                        <span class="ml-2 text-xs text-gray-700">Campo obligatorio</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.checklist-templates.index') }}" 
                       class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                        <i class="fas fa-save mr-2"></i>Crear Template
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function checklistTemplateForm() {
    return {
        template: {
            name: '',
            description: '',
            serviceTypeId: '',
            stages: [
                {
                    name: '',
                    description: '',
                    isRequired: true,
                    items: [
                        {
                            title: '',
                            description: '',
                            type: '',
                            options: '',
                            isRequired: true
                        }
                    ]
                }
            ]
        },
        
        addStage() {
            this.template.stages.push({
                name: '',
                description: '',
                isRequired: true,
                items: [
                    {
                        title: '',
                        description: '',
                        type: '',
                        options: '',
                        isRequired: true
                    }
                ]
            });
        },
        
        removeStage(stageIndex) {
            if (this.template.stages.length > 1) {
                this.template.stages.splice(stageIndex, 1);
            }
        },
        
        addItem(stageIndex) {
            this.template.stages[stageIndex].items.push({
                title: '',
                description: '',
                type: '',
                options: '',
                isRequired: true
            });
        },
        
        removeItem(stageIndex, itemIndex) {
            if (this.template.stages[stageIndex].items.length > 1) {
                this.template.stages[stageIndex].items.splice(itemIndex, 1);
            }
        }
    }
}
</script>
@endsection