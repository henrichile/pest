@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Crear Nuevo Servicio</h2>
            <p class="text-gray-600">Complete los datos del nuevo servicio</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.services.store') }}" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Cliente -->
                <div>
                    <label for="client_id" class="block text-sm font-medium text-gray-700 mb-2">Cliente *</label>
                    <select id="client_id" name="client_id" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 @error('client_id') border-red-500 @enderror">
                        <option value="">Seleccione un cliente</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                {{ $client->name }} - {{ $client->rut }}
                            </option>
                        @endforeach
                    </select>
                    @error('client_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tipo de Servicio -->
                <div>
                    <label for="service_type" class="block text-sm font-medium text-gray-700 mb-2">Tipo de Servicio *</label>
                    <select id="service_type" name="service_type" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 @error('service_type') border-red-500 @enderror">
                        <option value="">Seleccione el tipo</option>
                        @foreach($serviceTypes as $serviceType)
                            <option value="{{ $serviceType->slug }}" {{ old('service_type') == $serviceType->slug ? 'selected' : '' }}>{{ $serviceType->name }}</option>
                        @endforeach
                    </select>
                    @error('service_type')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Fecha Programada -->
                <div>
                    <label for="scheduled_date" class="block text-sm font-medium text-gray-700 mb-2">Fecha Programada *</label>
                    <input type="datetime-local" name="scheduled_date" id="scheduled_date" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 @error('scheduled_date') border-red-500 @enderror"
                           value="{{ old('scheduled_date') }}">
                    @error('scheduled_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Prioridad -->
                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">Prioridad *</label>
                    <select id="priority" name="priority" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 @error('priority') border-red-500 @enderror">
                        <option value="">Seleccione la prioridad</option>
                        <option value="alta" {{ old('priority') == 'alta' ? 'selected' : '' }}>Alta</option>
                        <option value="media" {{ old('priority') == 'media' ? 'selected' : '' }}>Media</option>
                        <option value="baja" {{ old('priority') == 'baja' ? 'selected' : '' }}>Baja</option>
                    </select>
                    @error('priority')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Técnico Asignado -->
                <div>
                    <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-2">Técnico Asignado</label>
                    <select id="assigned_to" name="assigned_to"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 @error('assigned_to') border-red-500 @enderror">
                        <option value="">Seleccione un técnico</option>
                        @foreach($technicians as $technician)
                            <option value="{{ $technician->id }}" {{ old('assigned_to') == $technician->id ? 'selected' : '' }}>
                                {{ $technician->name }} - {{ $technician->email }}
                            </option>
                        @endforeach
                    </select>
                    @error('assigned_to')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Dirección -->
                <div class="md:col-span-2">
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Dirección *</label>
                    <input type="text" name="address" id="address" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 @error('address') border-red-500 @enderror"
                           value="{{ old('address') }}" placeholder="Ingrese la dirección del servicio">
                    @error('address')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Descripción -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                    <textarea name="description" id="description" rows="4"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 @error('description') border-red-500 @enderror"
                              placeholder="Descripción adicional del servicio">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Botones -->
            <div class="flex justify-end space-x-4 mt-6">
                <a href="{{ route('admin.services.index') }}" 
                   class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancelar
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    Crear Servicio
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
