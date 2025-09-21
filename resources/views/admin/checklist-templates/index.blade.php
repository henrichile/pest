@extends('layouts.app')

@section('title', 'Templates de Checklist - Pest Controller SAT')
@section('page-title', 'Gestión de Templates de Checklist')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 flex-1 mr-4">
            <h3 class="text-lg font-semibold text-green-800">Templates de Checklist</h3>
            <p class="text-green-600 mt-1">Gestiona los templates de checklist para cada tipo de servicio.</p>
        </div>
        <a href="{{ route('admin.checklist-templates.create') }}" 
           class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors">
            <i class="fas fa-plus mr-2"></i>Nuevo Template
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <!-- Templates Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($templates as $template)
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">{{ $template->name }}</h3>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $template->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $template->is_active ? 'Activo' : 'Inactivo' }}
                    </span>
                </div>

                <div class="space-y-2 mb-4">
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-tag mr-2"></i>
                        <span class="font-medium">Tipo de Servicio:</span>
                        <span class="ml-1">{{ $template->serviceType->name }}</span>
                    </div>
                    
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-list mr-2"></i>
                        <span class="font-medium">Items:</span>
                        <span class="ml-1">{{ $template->items->count() }} elementos</span>
                    </div>
                    
                    @if($template->description)
                    <div class="text-sm text-gray-600">
                        <i class="fas fa-info-circle mr-2"></i>
                        <span>{{ Str::limit($template->description, 100) }}</span>
                    </div>
                    @endif
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                    <div class="flex space-x-2">
                        <a href="{{ route('admin.checklist-templates.show', $template) }}" 
                           class="text-blue-600 hover:text-blue-900 text-sm">
                            <i class="fas fa-eye mr-1"></i>Ver
                        </a>
                        <a href="{{ route('admin.checklist-templates.edit', $template) }}" 
                           class="text-green-600 hover:text-green-900 text-sm">
                            <i class="fas fa-edit mr-1"></i>Editar
                        </a>
                        <form action="{{ route('admin.checklist-templates.duplicate', $template) }}" 
                              method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-purple-600 hover:text-purple-900 text-sm">
                                <i class="fas fa-copy mr-1"></i>Duplicar
                            </button>
                        </form>
                    </div>
                    
                    <form action="{{ route('admin.checklist-templates.destroy', $template) }}" 
                          method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="text-red-600 hover:text-red-900 text-sm"
                                onclick="return confirm('¿Estás seguro de eliminar este template?')">
                            <i class="fas fa-trash mr-1"></i>Eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full">
            <div class="text-center py-12">
                <i class="fas fa-clipboard-list text-4xl text-gray-400 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No hay templates de checklist</h3>
                <p class="text-gray-500 mb-4">Comienza creando tu primer template de checklist.</p>
                <a href="{{ route('admin.checklist-templates.create') }}" 
                   class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-plus mr-2"></i>Crear Template
                </a>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Statistics Card -->
    @if($templates->count() > 0)
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Estadísticas</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="text-center">
                <div class="text-2xl font-bold text-green-600">{{ $templates->count() }}</div>
                <div class="text-sm text-gray-600">Total Templates</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-blue-600">{{ $templates->where('is_active', true)->count() }}</div>
                <div class="text-sm text-gray-600">Activos</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-purple-600">{{ $templates->sum(function($t) { return $t->items->count(); }) }}</div>
                <div class="text-sm text-gray-600">Total Items</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-orange-600">{{ $templates->groupBy('service_type_id')->count() }}</div>
                <div class="text-sm text-gray-600">Tipos de Servicio</div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
