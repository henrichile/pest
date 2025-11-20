@extends('layouts.app')

@section('title', 'Editar Reporte Programado')

@section('content')
<div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Título -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold" style="color: #111827;">Editar Reporte Programado</h1>
                <p class="text-sm mt-1" style="color: #6b7280;">Modifica la configuración del reporte programado</p>
            </div>
            <a href="{{ route('admin.reports.scheduled') }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors" style="background: #f3f4f6; color: #6b7280; border: 1px solid #e5e7eb;">
                Cancelar
            </a>
        </div>
    </div>

    <!-- Formulario -->
    <form action="{{ route('admin.reports.scheduled.update', $scheduledReport) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        
        <!-- Información Básica -->
        <div class="bg-white rounded-lg shadow-md border p-6" style="border: 1px solid #e5e7eb;">
            <h3 class="text-lg font-semibold mb-4" style="color: #111827;">Información Básica</h3>
            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium mb-2" style="color: #6b7280;">Nombre del Reporte *</label>
                    <input type="text" name="name" id="name" required value="{{ old('name', $scheduledReport->name) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="type" class="block text-sm font-medium mb-2" style="color: #6b7280;">Tipo de Reporte *</label>
                        <select name="type" id="type" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="services" {{ old('type', $scheduledReport->type) === 'services' ? 'selected' : '' }}>Servicios</option>
                            <option value="clients" {{ old('type', $scheduledReport->type) === 'clients' ? 'selected' : '' }}>Clientes</option>
                            <option value="technicians" {{ old('type', $scheduledReport->type) === 'technicians' ? 'selected' : '' }}>Técnicos</option>
                            <option value="financial" {{ old('type', $scheduledReport->type) === 'financial' ? 'selected' : '' }}>Financiero</option>
                        </select>
                    </div>

                    <div>
                        <label for="format" class="block text-sm font-medium mb-2" style="color: #6b7280;">Formato *</label>
                        <select name="format" id="format" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="pdf" {{ old('format', $scheduledReport->format) === 'pdf' ? 'selected' : '' }}>PDF</option>
                            <option value="csv" {{ old('format', $scheduledReport->format) === 'csv' ? 'selected' : '' }}>CSV</option>
                            <option value="excel" {{ old('format', $scheduledReport->format) === 'excel' ? 'selected' : '' }}>Excel</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="frequency" class="block text-sm font-medium mb-2" style="color: #6b7280;">Frecuencia *</label>
                    <select name="frequency" id="frequency" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="daily" {{ old('frequency', $scheduledReport->frequency) === 'daily' ? 'selected' : '' }}>Diario</option>
                        <option value="weekly" {{ old('frequency', $scheduledReport->frequency) === 'weekly' ? 'selected' : '' }}>Semanal</option>
                        <option value="monthly" {{ old('frequency', $scheduledReport->frequency) === 'monthly' ? 'selected' : '' }}>Mensual</option>
                        <option value="quarterly" {{ old('frequency', $scheduledReport->frequency) === 'quarterly' ? 'selected' : '' }}>Trimestral</option>
                        <option value="yearly" {{ old('frequency', $scheduledReport->frequency) === 'yearly' ? 'selected' : '' }}>Anual</option>
                    </select>
                </div>

                <div>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $scheduledReport->is_active) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                        <span class="text-sm font-medium" style="color: #6b7280;">Reporte activo</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="bg-white rounded-lg shadow-md border p-6" style="border: 1px solid #e5e7eb;">
            <h3 class="text-lg font-semibold mb-4" style="color: #111827;">Filtros (Opcional)</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="start_date" class="block text-sm font-medium mb-2" style="color: #6b7280;">Fecha Inicio</label>
                    <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $scheduledReport->filters['start_date'] ?? '') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>

                <div>
                    <label for="end_date" class="block text-sm font-medium mb-2" style="color: #6b7280;">Fecha Fin</label>
                    <input type="date" name="end_date" id="end_date" value="{{ old('end_date', $scheduledReport->filters['end_date'] ?? '') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>

                <div>
                    <label for="service_type" class="block text-sm font-medium mb-2" style="color: #6b7280;">Tipo de Servicio</label>
                    <select name="service_type" id="service_type"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="all">Todos</option>
                        <option value="fumigacion" {{ (old('service_type', $scheduledReport->filters['service_type'] ?? '') === 'fumigacion') ? 'selected' : '' }}>Fumigación</option>
                        <option value="desratizacion" {{ (old('service_type', $scheduledReport->filters['service_type'] ?? '') === 'desratizacion') ? 'selected' : '' }}>Desratización</option>
                        <option value="sanitizacion" {{ (old('service_type', $scheduledReport->filters['service_type'] ?? '') === 'sanitizacion') ? 'selected' : '' }}>Sanitización</option>
                        <option value="monitoreo-cebaderas" {{ (old('service_type', $scheduledReport->filters['service_type'] ?? '') === 'monitoreo-cebaderas') ? 'selected' : '' }}>Monitoreo Cebaderas</option>
                    </select>
                </div>

                <div>
                    <label for="client_id" class="block text-sm font-medium mb-2" style="color: #6b7280;">Cliente</label>
                    <select name="client_id" id="client_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="all">Todos</option>
                        @foreach($allClients as $client)
                            <option value="{{ $client->id }}" {{ (old('client_id', $scheduledReport->filters['client_id'] ?? '') == $client->id) ? 'selected' : '' }}>
                                {{ $client->business_name ?? $client->name ?? 'Cliente #' . $client->id }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="technician_id" class="block text-sm font-medium mb-2" style="color: #6b7280;">Técnico</label>
                    <select name="technician_id" id="technician_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="all">Todos</option>
                        @foreach($allTechnicians as $technician)
                            <option value="{{ $technician->id }}" {{ (old('technician_id', $scheduledReport->filters['technician_id'] ?? '') == $technician->id) ? 'selected' : '' }}>
                                {{ $technician->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium mb-2" style="color: #6b7280;">Estado</label>
                    <select name="status" id="status"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="all">Todos</option>
                        <option value="pendiente" {{ (old('status', $scheduledReport->filters['status'] ?? '') === 'pendiente') ? 'selected' : '' }}>Pendiente</option>
                        <option value="pending" {{ (old('status', $scheduledReport->filters['status'] ?? '') === 'pending') ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ (old('status', $scheduledReport->filters['status'] ?? '') === 'in_progress') ? 'selected' : '' }}>En Progreso</option>
                        <option value="completed" {{ (old('status', $scheduledReport->filters['status'] ?? '') === 'completed') ? 'selected' : '' }}>Completado</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Destinatarios -->
        <div class="bg-white rounded-lg shadow-md border p-6" style="border: 1px solid #e5e7eb;">
            <h3 class="text-lg font-semibold mb-4" style="color: #111827;">Destinatarios (Opcional)</h3>
            <div>
                <label for="recipients" class="block text-sm font-medium mb-2" style="color: #6b7280;">Emails (separados por comas)</label>
                <input type="text" name="recipients" id="recipients" value="{{ old('recipients', $scheduledReport->recipients ? implode(', ', $scheduledReport->recipients) : '') }}"
                       placeholder="email1@ejemplo.com, email2@ejemplo.com"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
        </div>

        <!-- Botones -->
        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.reports.scheduled') }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors" style="background: #f3f4f6; color: #6b7280; border: 1px solid #e5e7eb;">
                Cancelar
            </a>
            <button type="submit" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors bg-green-500 text-white hover:bg-green-600">
                Guardar Cambios
            </button>
        </div>
    </form>
</div>
@endsection

