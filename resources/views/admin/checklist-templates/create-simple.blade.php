@extends('layouts.app')

@section('title', 'Crear Template de Checklist - Versión Simple')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg">
            <!-- Header -->
            <div class="border-b border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold text-gray-900">
                        <i class="fas fa-clipboard-list text-green-600 mr-3"></i>
                        Crear Template de Checklist (Con Etapas)
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
                               required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Tipo de Servicio *
                        </label>
                        <select name="service_type_id" 
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
                              rows="3"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500"></textarea>
                </div>

                <!-- Etapa 1 (Hardcoded para prueba) -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">
                        <i class="fas fa-layer-group text-blue-600 mr-2"></i>
                        Etapa 1: Inspección Inicial
                    </h2>
                    
                    <div class="border border-gray-300 rounded-lg p-6 bg-gray-50">
                        <input type="hidden" name="stages[0][name]" value="Inspección Inicial">
                        <input type="hidden" name="stages[0][description]" value="Primera etapa del proceso">
                        <input type="hidden" name="stages[0][is_required]" value="1">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Punto 1: Estado del área
                                </label>
                                <input type="hidden" name="stages[0][items][0][title]" value="Estado del área">
                                <input type="hidden" name="stages[0][items][0][type]" value="select">
                                <input type="hidden" name="stages[0][items][0][options]" value="Excelente,Bueno,Regular,Malo">
                                <input type="hidden" name="stages[0][items][0][is_required]" value="1">
                                <select class="w-full border border-gray-300 rounded-lg px-3 py-2" disabled>
                                    <option>Excelente</option>
                                    <option>Bueno</option>
                                    <option>Regular</option>
                                    <option>Malo</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Punto 2: Temperatura ambiente
                                </label>
                                <input type="hidden" name="stages[0][items][1][title]" value="Temperatura ambiente">
                                <input type="hidden" name="stages[0][items][1][type]" value="number">
                                <input type="hidden" name="stages[0][items][1][is_required]" value="1">
                                <input type="number" class="w-full border border-gray-300 rounded-lg px-3 py-2" disabled placeholder="°C">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Etapa 2 (Hardcoded para prueba) -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">
                        <i class="fas fa-layer-group text-blue-600 mr-2"></i>
                        Etapa 2: Aplicación
                    </h2>
                    
                    <div class="border border-gray-300 rounded-lg p-6 bg-gray-50">
                        <input type="hidden" name="stages[1][name]" value="Aplicación">
                        <input type="hidden" name="stages[1][description]" value="Proceso de aplicación del tratamiento">
                        <input type="hidden" name="stages[1][is_required]" value="1">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Punto 1: Producto aplicado
                                </label>
                                <input type="hidden" name="stages[1][items][0][title]" value="Producto aplicado">
                                <input type="hidden" name="stages[1][items][0][type]" value="text">
                                <input type="hidden" name="stages[1][items][0][is_required]" value="1">
                                <input type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2" disabled placeholder="Nombre del producto">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Punto 2: Evidencia fotográfica
                                </label>
                                <input type="hidden" name="stages[1][items][1][title]" value="Evidencia fotográfica">
                                <input type="hidden" name="stages[1][items][1][type]" value="file">
                                <input type="hidden" name="stages[1][items][1][is_required]" value="0">
                                <input type="file" class="w-full border border-gray-300 rounded-lg px-3 py-2" disabled>
                            </div>
                        </div>
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
                        <i class="fas fa-save mr-2"></i>Crear Template (Versión Prueba)
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
