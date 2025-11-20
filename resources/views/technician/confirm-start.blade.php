@extends("layouts.app")

@section("title", "Confirmar Inicio de Servicio - Pest Controller SAT")
@section("page-title", "Confirmar Inicio de Servicio")

@section("content")
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <!-- Header -->
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-2">¡Servicio Creado Exitosamente!</h2>
            <p class="text-gray-600">¿Deseas iniciar el servicio ahora mismo?</p>
        </div>

        <!-- Service Details -->
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-3">Detalles del Servicio</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Cliente</p>
                    <p class="font-medium text-gray-900">{{ $service->client->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Tipo de Servicio</p>
                    <p class="font-medium text-gray-900">{{ ucfirst($service->service_type) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Fecha Programada</p>
                    <p class="font-medium text-gray-900">{{ $service->scheduled_date->format('d/m/Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Prioridad</p>
                    <p class="font-medium text-gray-900">{{ ucfirst($service->priority) }}</p>
                </div>
                <div class="md:col-span-2">
                    <p class="text-sm text-gray-600">Dirección</p>
                    <p class="font-medium text-gray-900">{{ $service->address }}</p>
                </div>
                @if($service->description)
                <div class="md:col-span-2">
                    <p class="text-sm text-gray-600">Descripción</p>
                    <p class="font-medium text-gray-900">{{ $service->description }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <!-- Start Now Button -->
            <form method="POST" action="{{ route('technician.service.start', $service) }}" class="flex-1">
                @csrf
                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-6 rounded-lg transition-colors flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Iniciar Ahora
                </button>
            </form>

            <!-- Save for Later Button -->
            <a href="{{ route('technician.services') }}" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-medium py-3 px-6 rounded-lg transition-colors flex items-center justify-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                </svg>
                Guardar para Después
            </a>
        </div>

        <!-- Info Text -->
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-500">
                Si inicias ahora, comenzarás el proceso de checklist paso a paso.<br>
                Si guardas para después, el servicio quedará en tu listado de servicios programados.
            </p>
        </div>
    </div>
</div>
@endsection
