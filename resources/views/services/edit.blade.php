@extends("layouts.app")

@section("title", "Editar Servicio - Pest Controller SAT")
@section("page-title", "Editar Servicio")

@section("content")
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Editar Servicio</h2>
            <p class="text-gray-600">Modifica los datos del servicio</p>
        </div>

        <form method="POST" action="{{ route("admin.services.update", $service) }}" class="space-y-6">
            @csrf
            @method("PUT")
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Cliente -->
                <div>
                    <label for="client_id" class="block text-sm font-medium text-gray-700 mb-2">Cliente *</label>
                    <select id="client_id" name="client_id" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 @error("client_id") border-red-500 @enderror">
                        <option value="">Seleccione un cliente</option>
                        @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ old("client_id", $service->client_id) == $client->id ? "selected" : "" }}>
                            {{ $client->name }} - {{ $client->rut }}
                        </option>
                        @endforeach
                    </select>
                    @error("client_id")
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tipo de Servicio -->
                <div>
                    <label for="service_type" class="block text-sm font-medium text-gray-700 mb-2">Tipo de Servicio *</label>
                    <select id="service_type" name="service_type" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 @error("service_type") border-red-500 @enderror">
                        <option value="">Seleccione el tipo</option>
                        <option value="desratizacion" {{ old("service_type", $service->service_type) == "desratizacion" ? "selected" : "" }}>Desratización</option>
                        <option value="desinsectacion" {{ old("service_type", $service->service_type) == "desinsectacion" ? "selected" : "" }}>Desinsectación</option>
                        <option value="sanitizacion" {{ old("service_type", $service->service_type) == "sanitizacion" ? "selected" : "" }}>Sanitización</option>
                    </select>
                    @error("service_type")
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Fecha Programada -->
                <div>
                    <label for="scheduled_date" class="block text-sm font-medium text-gray-700 mb-2">Fecha Programada *</label>
                    <input type="datetime-local" id="scheduled_date" name="scheduled_date" required
                           value="{{ old("scheduled_date", $service->scheduled_date->format("Y-m-d\TH:i")) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 @error("scheduled_date") border-red-500 @enderror">
                    @error("scheduled_date")
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Prioridad -->
                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">Prioridad *</label>
                    <select id="priority" name="priority" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 @error("priority") border-red-500 @enderror">
                        <option value="">Seleccione la prioridad</option>
                        <option value="alta" {{ old("priority", $service->priority) == "alta" ? "selected" : "" }}>Alta</option>
                        <option value="media" {{ old("priority", $service->priority) == "media" ? "selected" : "" }}>Media</option>
                        <option value="baja" {{ old("priority", $service->priority) == "baja" ? "selected" : "" }}>Baja</option>
                    </select>
                    @error("priority")
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Dirección -->
            <div>
                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Dirección *</label>
                <input type="text" id="address" name="address" required
                       value="{{ old("address", $service->address) }}"
                       placeholder="Ingrese la dirección completa del servicio"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 @error("address") border-red-500 @enderror">
                @error("address")
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Estado -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Estado *</label>
                <select id="status" name="status" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 @error("status") border-red-500 @enderror">
                    <option value="pendiente" {{ old("status", $service->status) == "pendiente" ? "selected" : "" }}>Pendiente</option>
                    <option value="en_progreso" {{ old("status", $service->status) == "en_progreso" ? "selected" : "" }}>En Progreso</option>
                    <option value="vencido" {{ old("status", $service->status) == "vencido" ? "selected" : "" }}>Vencido</option>
                    <option value="finalizado" {{ old("status", $service->status) == "finalizado" ? "selected" : "" }}>Finalizado</option>
                </select>
                @error("status")
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Técnico Asignado -->
            <div>
                <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-2">Técnico Asignado</label>
                <select id="assigned_to" name="assigned_to"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 @error("assigned_to") border-red-500 @enderror">
                    <option value="">Seleccione un técnico</option>
                    @foreach($technicians as $technician)
                    <option value="{{ $technician->id }}" {{ old("assigned_to", $service->assigned_to) == $technician->id ? "selected" : "" }}>{{ $technician->name }} ({{ $technician->email }})</option>
                    @endforeach
                </select>
                @error("assigned_to")
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <!-- Descripción -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                <textarea id="description" name="description" rows="4"
                          placeholder="Descripción adicional del servicio (opcional)"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 @error("description") border-red-500 @enderror">{{ old("description", $service->description) }}</textarea>
                @error("description")
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Botones -->
            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route("admin.services.show", $service) }}" 
                   class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancelar
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                    Actualizar Servicio
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
