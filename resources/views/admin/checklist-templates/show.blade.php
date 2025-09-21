@extends('layouts.app')

@section('title', 'Ver Template de Checklist - Pest Controller SAT')
@section('page-title', 'Detalle del Template')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-semibold text-gray-900">{{ $checklistTemplate->name }}</h2>
                <p class="text-gray-600 mt-1">Template de checklist para {{ $checklistTemplate->serviceType->name }}</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('admin.checklist-templates.edit', $checklistTemplate) }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-edit mr-2"></i>Editar
                </a>
                <a href="{{ route('admin.checklist-templates.index') }}" 
                   class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Volver
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="text-sm text-gray-600">Estado</div>
                <div class="text-lg font-semibold">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium 
                                {{ $checklistTemplate->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $checklistTemplate->is_active ? 'Activo' : 'Inactivo' }}
                    </span>
                </div>
            </div>

            <div class="bg-gray-50 rounded-lg p-4">
                <div class="text-sm text-gray-600">Tipo de Servicio</div>
                <div class="text-lg font-semibold">{{ $checklistTemplate->serviceType->name }}</div>
            </div>

            <div class="bg-gray-50 rounded-lg p-4">
                <div class="text-sm text-gray-600">Total de Items</div>
                <div class="text-lg font-semibold">{{ $checklistTemplate->items->count() }} elementos</div>
            </div>
        </div>

        @if($checklistTemplate->description)
        <div class="mt-6">
            <h3 class="text-sm font-medium text-gray-700 mb-2">Descripción</h3>
            <p class="text-gray-600">{{ $checklistTemplate->description }}</p>
        </div>
        @endif
    </div>

    <!-- Items del Checklist -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-semibold text-gray-900">Items del Checklist</h3>
            <div class="text-sm text-gray-600">
                {{ $checklistTemplate->items->count() }} elemento(s)
            </div>
        </div>

        @if($checklistTemplate->items->count() > 0)
        <div class="space-y-4">
            @foreach($checklistTemplate->items as $item)
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex justify-between items-start mb-3">
                    <div class="flex-1">
                        <h4 class="text-lg font-medium text-gray-900">
                            {{ $item->title }}
                            @if($item->is_required)
                                <span class="text-red-500 ml-1">*</span>
                            @endif
                        </h4>
                        @if($item->description)
                        <p class="text-gray-600 mt-1">{{ $item->description }}</p>
                        @endif
                    </div>
                    <div class="ml-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $item->type === 'text' ? 'bg-blue-100 text-blue-800' : 
                                       ($item->type === 'number' ? 'bg-green-100 text-green-800' : 
                                       ($item->type === 'select' ? 'bg-purple-100 text-purple-800' : 
                                       ($item->type === 'checkbox' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800'))) }}">
                            {{ ucfirst($item->type) }}
                        </span>
                    </div>
                </div>

                <!-- Previsualización del campo -->
                <div class="mt-3 p-3 bg-gray-50 rounded-lg">
                    <div class="text-sm text-gray-600 mb-2">Previsualización:</div>
                    
                    @if($item->type === 'text')
                        <input type="text" placeholder="Campo de texto" disabled
                               class="w-full border border-gray-300 rounded px-3 py-2 bg-white opacity-75">
                    
                    @elseif($item->type === 'number')
                        <input type="number" placeholder="Campo numérico" disabled
                               class="w-full border border-gray-300 rounded px-3 py-2 bg-white opacity-75">
                    
                    @elseif($item->type === 'select')
                        <select disabled class="w-full border border-gray-300 rounded px-3 py-2 bg-white opacity-75">
                            <option>Seleccionar opción...</option>
                            @if($item->options)
                                @foreach(json_decode($item->options, true) as $option)
                                <option>{{ $option }}</option>
                                @endforeach
                            @endif
                        </select>
                        @if($item->options)
                        <div class="mt-2 text-xs text-gray-500">
                            Opciones: {{ implode(', ', json_decode($item->options, true)) }}
                        </div>
                        @endif
                    
                    @elseif($item->type === 'checkbox')
                        <label class="flex items-center">
                            <input type="checkbox" disabled class="rounded border-gray-300 opacity-75">
                            <span class="ml-2 text-gray-600">Opción marcable</span>
                        </label>
                    
                    @elseif($item->type === 'file')
                        <input type="file" disabled
                               class="w-full border border-gray-300 rounded px-3 py-2 bg-white opacity-75">
                    @endif
                </div>

                <!-- Información adicional -->
                <div class="mt-3 flex justify-between items-center text-xs text-gray-500">
                    <div>Orden: #{{ $item->order }}</div>
                    <div>{{ $item->is_required ? 'Campo obligatorio' : 'Campo opcional' }}</div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-8 text-gray-500">
            <i class="fas fa-clipboard-list text-3xl mb-2"></i>
            <p>Este template no tiene items configurados.</p>
        </div>
        @endif
    </div>

    <!-- Información adicional -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Información del Template</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <div class="text-sm text-gray-600">Creado</div>
                <div class="text-sm font-medium">{{ $checklistTemplate->created_at->format('d/m/Y H:i') }}</div>
            </div>
            <div>
                <div class="text-sm text-gray-600">Última modificación</div>
                <div class="text-sm font-medium">{{ $checklistTemplate->updated_at->format('d/m/Y H:i') }}</div>
            </div>
        </div>
    </div>

    <!-- Acciones -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Acciones</h3>
        <div class="flex space-x-4">
            <form action="{{ route('admin.checklist-templates.duplicate', $checklistTemplate) }}" 
                  method="POST" class="inline">
                @csrf
                <button type="submit" 
                        class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-copy mr-2"></i>Duplicar Template
                </button>
            </form>

            <form action="{{ route('admin.checklist-templates.destroy', $checklistTemplate) }}" 
                  method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors"
                        onclick="return confirm('¿Estás seguro de eliminar este template? Esta acción no se puede deshacer.')">
                    <i class="fas fa-trash mr-2"></i>Eliminar Template
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
