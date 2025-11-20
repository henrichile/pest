@extends("layouts.app")

@section("content")
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">{{ $serviceType->name }}</h1>
            <div class="space-x-3">
                <a href="{{ route("admin.service-types.edit", $serviceType) }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                    Editar
                </a>
                <a href="{{ route("admin.service-types.index") }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors">
                    Volver
                </a>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Información del Tipo de Servicio</h3>
            </div>
            
            <div class="px-6 py-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Nombre</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $serviceType->name }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500">Slug</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $serviceType->slug }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500">Descripción</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $serviceType->description ?? "Sin descripción" }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500">Estado</label>
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $serviceType->is_active ? "bg-green-100 text-green-800" : "bg-red-100 text-red-800" }}">
                        {{ $serviceType->is_active ? "Activo" : "Inactivo" }}
                    </span>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500">Servicios Asociados</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $serviceType->services->count() }} servicios</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500">Templates de Checklist</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $serviceType->checklistTemplates->count() }} templates</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500">Creado</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $serviceType->created_at->format("d/m/Y H:i") }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500">Última actualización</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $serviceType->updated_at->format("d/m/Y H:i") }}</p>
                </div>
            </div>
        </div>

        @if($serviceType->services->count() > 0)
        <div class="mt-6 bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Servicios Asociados</h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($serviceType->services as $service)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">#{{ $service->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $service->client->name ?? "N/A" }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $service->scheduled_date ? $service->scheduled_date->format("d/m/Y") : "N/A" }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $service->status === "completado" ? "bg-green-100 text-green-800" : "bg-yellow-100 text-yellow-800" }}">
                                    {{ ucfirst($service->status) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Templates de Checklist -->
        <div class="mt-6 bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">Templates de Checklist</h3>
                <a href="{{ route('admin.checklist-templates.create') }}?service_type_id={{ $serviceType->id }}" 
                   class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 text-sm rounded transition-colors">
                    <i class="fas fa-plus mr-1"></i>Nuevo Template
                </a>
            </div>
            
            @if($serviceType->checklistTemplates->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Creado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($serviceType->checklistTemplates as $template)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $template->name }}</div>
                                @if($template->description)
                                <div class="text-sm text-gray-500">{{ Str::limit($template->description, 50) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $template->items->count() }} elementos
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $template->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $template->is_active ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $template->created_at->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <a href="{{ route('admin.checklist-templates.show', $template) }}" 
                                   class="text-blue-600 hover:text-blue-900">Ver</a>
                                <a href="{{ route('admin.checklist-templates.edit', $template) }}" 
                                   class="text-green-600 hover:text-green-900">Editar</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="px-6 py-8 text-center">
                <i class="fas fa-clipboard-list text-3xl text-gray-400 mb-3"></i>
                <h4 class="text-lg font-medium text-gray-900 mb-2">No hay templates de checklist</h4>
                <p class="text-gray-500 mb-4">Crea un template para este tipo de servicio para estandarizar los checklists.</p>
                <a href="{{ route('admin.checklist-templates.create') }}?service_type_id={{ $serviceType->id }}" 
                   class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-plus mr-2"></i>Crear Template
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
