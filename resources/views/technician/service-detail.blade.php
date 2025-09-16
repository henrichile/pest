@extends("layouts.app")

@section("content")
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
            <h1 class="text-2xl font-bold text-white">Detalle del Servicio</h1>
        </div>

        <!-- Content -->
        <div class="p-6">
            <!-- Información del Cliente -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Información del Cliente</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Nombre</p>
                        <p class="font-medium text-gray-900">{{ $service->client->name ?? "N/A" }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Teléfono</p>
                        <p class="font-medium text-gray-900">{{ $service->client->phone ?? "N/A" }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Email</p>
                        <p class="font-medium text-gray-900">{{ $service->client->email ?? "N/A" }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Dirección</p>
                        <p class="font-medium text-gray-900">{{ $service->address ?? "N/A" }}</p>
                    </div>
                </div>
            </div>

            <!-- Acciones del Servicio -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Acciones del Servicio</h2>
                
                @if($service->status == "pendiente")
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-blue-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <h3 class="text-lg font-medium text-blue-900">Servicio Pendiente</h3>
                            <p class="text-blue-700">El servicio está programado para {{ $service->scheduled_date->format("d/m/Y H:i") }}</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route("technician.service.start", $service) }}">
                            
                            <span 
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Iniciar Servicio
                            </span></a>
                        
                    </div>
                </div>
                @elseif($service->status == "en_progreso")
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-yellow-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <h3 class="text-lg font-medium text-yellow-900">Servicio en Progreso</h3>
                            <p class="text-yellow-700">Iniciado el {{ $service->started_at->format("d/m/Y H:i") }}</p>
                        </div>
                    </div>
                    <div class="mt-4 flex space-x-3">
                        <a href="{{ route("technician.service.checklist", $service) }}" 
                           class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                            </svg>
                            Realizar Checklist
                        </a>
                        <a href="{{ route("technician.service.checklist-details", $service) }}" 
                           class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            Ver Detalle
                        </a>
                    </div>
                </div>
                @endif

                <!-- Checklist Completado -->
                @if($service->status === "finalizado")
                <div class="mt-6 bg-green-50 border border-green-200 rounded-lg p-6">
                    <div class="flex items-center mb-4">
                        <svg class="w-6 h-6 text-green-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <h3 class="text-lg font-medium text-green-900">Checklist Completado</h3>
                            <p class="text-green-700">Completado el {{ $service->checklist_completed_at ? $service->checklist_completed_at->format("d/m/Y H:i") : "Fecha no disponible" }}</p>
                        </div>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route("technician.service.checklist-details", $service) }}" 
                           class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            Ver Observaciones Detalladas
                        </a>
                        
                        <!-- Botón para Generar PDF -->
                        <a href="{{ route("technician.service.pdf", $service) }}" class="inline">
                            
                            <span 
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Generar PDF
                            </span></a>
                        
                    </div>
                </div>
                @endif
            </div>

            <!-- Información Adicional -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Detalles del Servicio</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tipo:</span>
                            <span class="font-medium">{{ $service->service_type ?? "Sanitización" }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Prioridad:</span>
                            <span class="font-medium">{{ ucfirst($service->priority ?? "Media") }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Programado:</span>
                            <span class="font-medium">{{ $service->scheduled_date->format("d/m/Y H:i") }}</span>
                        </div>
                        @if($service->started_at)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Iniciado:</span>
                            <span class="font-medium">{{ $service->started_at->format("d/m/Y H:i") }}</span>
                        </div>
                        @endif
                        @if($service->completed_at)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Completado:</span>
                            <span class="font-medium">{{ $service->checklist_completed_at ? $service->checklist_completed_at->format("d/m/Y H:i") : "Fecha no disponible" }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Estadísticas</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Observaciones:</span>
                            <span class="font-medium">{{ $service->observations->count() }}</span>
                        </div>
                        @if($service->started_at && $service->completed_at)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Duración:</span>
                            <span class="font-medium">{{ $service->started_at->diffInMinutes($service->completed_at) }} min</span>
                        </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-gray-600">Checklist:</span>
                            <span class="font-medium">
                                @if($service->status === "finalizado")
                                    <span class="text-green-600">✓ Completado</span>
                                @else
                                    <span class="text-yellow-600">⏳ Pendiente</span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Descripción -->
            @if($service->description)
            <div class="mt-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Descripción</h3>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-gray-700">{{ $service->description }}</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 px-6 py-4 border-t">
            <div class="flex justify-between items-center">
                <a href="{{ route("technician.services") }}" 
                   class="inline-flex items-center px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Volver a Servicios
                </a>
                <div class="text-sm text-gray-500">
                    Última actualización: {{ $service->updated_at->format("d/m/Y H:i") }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
