@extends('layouts.app')

@section('title', 'Programar Nuevo Reporte')

@section('content')
<div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Título -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Programar Nuevo Reporte</h1>
                <p class="text-sm mt-1 text-gray-600 dark:text-white">Configura un reporte que se generará automáticamente según la frecuencia seleccionada</p>
            </div>
            <a href="{{ route('admin.reports.scheduled') }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                Cancelar
            </a>
        </div>
    </div>

    <!-- Formulario -->
    <form action="{{ route('admin.reports.scheduled.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <!-- Información Básica -->
        <div class="bg-white rounded-lg shadow-md border p-6" style="border: 1px solid #e5e7eb;">
            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Información Básica</h3>
            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium mb-2 text-gray-600 dark:text-white">Nombre del Reporte *</label>
                    <input type="text" name="name" id="name" required value="{{ old('name') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="type" class="block text-sm font-medium mb-2 text-gray-600 dark:text-white">Tipo de Reporte *</label>
                        <select name="type" id="type" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="services" {{ old('type') === 'services' ? 'selected' : '' }}>Servicios</option>
                            <option value="clients" {{ old('type') === 'clients' ? 'selected' : '' }}>Clientes</option>
                            <option value="technicians" {{ old('type') === 'technicians' ? 'selected' : '' }}>Técnicos</option>
                            <option value="financial" {{ old('type') === 'financial' ? 'selected' : '' }}>Financiero</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="format" class="block text-sm font-medium mb-2 text-gray-600 dark:text-white">Formato *</label>
                        <select name="format" id="format" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="pdf" {{ old('format') === 'pdf' ? 'selected' : '' }}>PDF</option>
                            <option value="csv" {{ old('format') === 'csv' ? 'selected' : '' }}>CSV</option>
                            <option value="excel" {{ old('format') === 'excel' ? 'selected' : '' }}>Excel</option>
                        </select>
                        @error('format')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="frequency" class="block text-sm font-medium mb-2 text-gray-600 dark:text-white">Frecuencia *</label>
                    <select name="frequency" id="frequency" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="daily" {{ old('frequency') === 'daily' ? 'selected' : '' }}>Diario</option>
                        <option value="weekly" {{ old('frequency') === 'weekly' ? 'selected' : '' }}>Semanal</option>
                        <option value="monthly" {{ old('frequency') === 'monthly' ? 'selected' : '' }}>Mensual</option>
                        <option value="quarterly" {{ old('frequency') === 'quarterly' ? 'selected' : '' }}>Trimestral</option>
                        <option value="yearly" {{ old('frequency') === 'yearly' ? 'selected' : '' }}>Anual</option>
                    </select>
                    @error('frequency')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="bg-white rounded-lg shadow-md border p-6" style="border: 1px solid #e5e7eb;">
            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Filtros (Opcional)</h3>
            <p class="text-sm mb-4 text-gray-600 dark:text-white">Los filtros se aplicarán automáticamente al generar el reporte. Si no se especifican, se usarán los valores predeterminados.</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="start_date" class="block text-sm font-medium mb-2 text-gray-600 dark:text-white">Fecha Inicio</label>
                    <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>

                <div>
                    <label for="end_date" class="block text-sm font-medium mb-2 text-gray-600 dark:text-white">Fecha Fin</label>
                    <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>

                <div>
                    <label for="service_type" class="block text-sm font-medium mb-2 text-gray-600 dark:text-white">Tipo de Servicio</label>
                    <select name="service_type" id="service_type"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="all">Todos</option>
                        <option value="fumigacion">Fumigación</option>
                        <option value="desratizacion">Desratización</option>
                        <option value="sanitizacion">Sanitización</option>
                        <option value="monitoreo-cebaderas">Monitoreo Cebaderas</option>
                    </select>
                </div>

                <div>
                    <label for="client_id" class="block text-sm font-medium mb-2 text-gray-600 dark:text-white">Cliente</label>
                    <select name="client_id" id="client_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="all">Todos</option>
                        @foreach($allClients as $client)
                            <option value="{{ $client->id }}">{{ $client->business_name ?? $client->name ?? 'Cliente #' . $client->id }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="technician_id" class="block text-sm font-medium mb-2 text-gray-600 dark:text-white">Técnico</label>
                    <select name="technician_id" id="technician_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="all">Todos</option>
                        @foreach($allTechnicians as $technician)
                            <option value="{{ $technician->id }}">{{ $technician->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium mb-2 text-gray-600 dark:text-white">Estado</label>
                    <select name="status" id="status"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="all">Todos</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="pending">Pending</option>
                        <option value="in_progress">En Progreso</option>
                        <option value="completed">Completado</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Destinatarios -->
        <div class="bg-white rounded-lg shadow-md border p-6" style="border: 1px solid #e5e7eb;">
            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Destinatarios (Opcional)</h3>
            <div>
                <label for="recipients" class="block text-sm font-medium mb-2 text-gray-600 dark:text-white">Emails (separados por comas)</label>
                <input type="text" name="recipients" id="recipients" value="{{ old('recipients') }}"
                       placeholder="email1@ejemplo.com, email2@ejemplo.com"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                <p class="mt-1 text-xs text-gray-600 dark:text-white">Si se especifican emails, el reporte se enviará automáticamente a estos destinatarios cuando se genere.</p>
            </div>
        </div>

        <!-- Botones -->
        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.reports.scheduled') }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                Cancelar
            </a>
            <button type="submit" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors bg-green-500 text-white hover:bg-green-600">
                Programar Reporte
            </button>
        </div>
    </form>
</div>
@endsection

