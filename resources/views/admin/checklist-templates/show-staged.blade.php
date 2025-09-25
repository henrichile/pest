@extends('layouts.app')

@section('title', $checklistTemplate->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-lg mb-6">
            <div class="border-b border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">
                            <i class="fas fa-clipboard-list text-green-600 mr-3"></i>
                            {{ $checklistTemplate->name }}
                        </h1>
                        <p class="text-gray-600 mt-1">Template de Checklist</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('admin.checklist-templates.edit', $checklistTemplate) }}" 
                           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                            <i class="fas fa-edit mr-2"></i>Editar
                        </a>
                        <a href="{{ route('admin.checklist-templates.index') }}" 
                           class="text-gray-600 hover:text-gray-900 transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i>Volver
                        </a>
                    </div>
                </div>
            </div>

            <!-- Información del Template -->
            <div class="px-6 py-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Tipo de Servicio</h3>
                        <p class="text-lg text-gray-900">{{ $checklistTemplate->serviceType->name }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Total de Etapas</h3>
                        <p class="text-lg text-gray-900">{{ $checklistTemplate->stages->count() }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Total de Puntos</h3>
                        <p class="text-lg text-gray-900">{{ $checklistTemplate->stages->sum(function($stage) { return $stage->items->count(); }) }}</p>
                    </div>
                </div>

                @if($checklistTemplate->description)
                <div class="mt-6">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Descripción</h3>
                    <p class="text-gray-900">{{ $checklistTemplate->description }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Etapas del Checklist -->
        <div class="space-y-6">
            @foreach($checklistTemplate->stages as $stage)
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <!-- Header de la Etapa -->
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="bg-white bg-opacity-20 rounded-full w-8 h-8 flex items-center justify-center mr-4">
                                <span class="text-white font-bold">{{ $loop->iteration }}</span>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-white">{{ $stage->name }}</h2>
                                @if($stage->description)
                                    <p class="text-blue-100 mt-1">{{ $stage->description }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-blue-100 text-sm">
                                {{ $stage->items->count() }} puntos de verificación
                            </div>
                            @if($stage->is_required)
                                <div class="text-blue-100 text-xs mt-1">
                                    <i class="fas fa-exclamation-circle mr-1"></i>Etapa obligatoria
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Puntos de Verificación -->
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($stage->items as $item)
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-start justify-between mb-3">
                                <h4 class="font-medium text-gray-900 flex-1">{{ $item->title }}</h4>
                                <div class="flex items-center space-x-2 ml-3">
                                    <!-- Tipo de Campo -->
                                    @switch($item->type)
                                        @case('text')
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                <i class="fas fa-font mr-1"></i>Texto
                                            </span>
                                            @break
                                        @case('number')
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-hashtag mr-1"></i>Número
                                            </span>
                                            @break
                                        @case('select')
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                <i class="fas fa-list mr-1"></i>Selección
                                            </span>
                                            @break
                                        @case('checkbox')
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                <i class="fas fa-check-square mr-1"></i>Checkbox
                                            </span>
                                            @break
                                        @case('file')
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <i class="fas fa-file mr-1"></i>Archivo
                                            </span>
                                            @break
                                    @endswitch

                                    <!-- Campo Obligatorio -->
                                    @if($item->is_required)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <i class="fas fa-asterisk mr-1"></i>Obligatorio
                                        </span>
                                    @endif
                                </div>
                            </div>

                            @if($item->description)
                                <p class="text-gray-600 text-sm mb-3">{{ $item->description }}</p>
                            @endif

                            <!-- Opciones para Select -->
                            @if($item->type === 'select' && $item->options)
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <h5 class="text-xs font-medium text-gray-700 mb-2">Opciones disponibles:</h5>
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($item->options as $option)
                                            <span class="inline-block bg-white border border-gray-200 rounded px-2 py-1 text-xs text-gray-700">
                                                {{ trim($option) }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Preview del Campo -->
                            <div class="mt-3 p-3 bg-gray-50 rounded-lg">
                                <h5 class="text-xs font-medium text-gray-700 mb-2">Vista previa:</h5>
                                @switch($item->type)
                                    @case('text')
                                        <input type="text" disabled placeholder="Campo de texto..." class="w-full px-2 py-1 text-sm border border-gray-300 rounded bg-white">
                                        @break
                                    @case('number')
                                        <input type="number" disabled placeholder="0" class="w-full px-2 py-1 text-sm border border-gray-300 rounded bg-white">
                                        @break
                                    @case('select')
                                        <select disabled class="w-full px-2 py-1 text-sm border border-gray-300 rounded bg-white">
                                            <option>Seleccionar opción...</option>
                                            @if($item->options)
                                                @foreach($item->options as $option)
                                                    <option>{{ trim($option) }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @break
                                    @case('checkbox')
                                        <label class="flex items-center">
                                            <input type="checkbox" disabled class="rounded border-gray-300 text-green-600">
                                            <span class="ml-2 text-sm text-gray-700">Marcar si aplica</span>
                                        </label>
                                        @break
                                    @case('file')
                                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-3 text-center">
                                            <i class="fas fa-cloud-upload-alt text-gray-400 text-lg mb-1"></i>
                                            <p class="text-xs text-gray-500">Subir archivo</p>
                                        </div>
                                        @break
                                @endswitch
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Acciones Adicionales -->
        <div class="mt-8 bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Acciones</h3>
            <div class="flex flex-wrap gap-3">
                <form action="{{ route('admin.checklist-templates.duplicate', $checklistTemplate) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-copy mr-2"></i>Duplicar Template
                    </button>
                </form>

                <a href="{{ route('admin.checklist-templates.edit', $checklistTemplate) }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-edit mr-2"></i>Editar Template
                </a>

                <form action="{{ route('admin.checklist-templates.destroy', $checklistTemplate) }}" 
                      method="POST" 
                      class="inline"
                      onsubmit="return confirm('¿Estás seguro de que quieres eliminar este template?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-trash mr-2"></i>Eliminar Template
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
