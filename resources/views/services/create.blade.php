@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Crear Nuevo Servicio</h2>
            <p class="text-gray-600 dark:text-white">Complete los datos del nuevo servicio</p>
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
                            <option value="{{ $client->id }}"
                                    data-address="{{ $client->address ?? '' }}"
                                    {{ old('client_id') == $client->id ? 'selected' : '' }}>
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

                <!-- Título del Servicio Especial (solo visible para servicios-especiales) -->
                <div id="special-service-title-container" style="display: none;">
                    <label for="special_service_title" class="block text-sm font-medium text-gray-700 mb-2">
                        Título del Servicio Especial *
                    </label>
                    <input type="text" id="special_service_title" name="special_service_title"
                           value="{{ old('special_service_title') }}"
                           placeholder="Ej: Desinfección COVID-19, Fumigación de Bodega, etc."
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 @error('special_service_title') border-red-500 @enderror">
                    @error('special_service_title')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-sm text-gray-500 mt-1">Este título aparecerá en el detalle y PDF del servicio</p>
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

                <!-- Precio de Venta (Solo Super Admin) -->
                @if(auth()->check() && auth()->user()->hasRole('super-admin'))
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 mb-2">Precio de Venta</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">$</span>
                        <input type="number" name="price" id="price" step="0.01" min="0"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 pl-8 focus:outline-none focus:ring-2 focus:ring-green-500 @error('price') border-red-500 @enderror"
                               placeholder="0.00" value="{{ old('price') }}">
                    </div>
                    @error('price')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                @endif

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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const serviceTypeSelect = document.getElementById('service_type');
    const specialTitleContainer = document.getElementById('special-service-title-container');
    const specialTitleInput = document.getElementById('special_service_title');
    const clientSelect = document.getElementById('client_id');
    const addressInput = document.getElementById('address');

    function toggleSpecialServiceTitle() {
        const selectedValue = serviceTypeSelect.value;

        console.log('Service type seleccionado:', selectedValue); // Debug

        if (selectedValue === 'servicios-especiales') {
            console.log('Mostrando campo de título especial'); // Debug
            specialTitleContainer.style.display = 'block';
            specialTitleInput.setAttribute('required', 'required');
        } else {
            console.log('Ocultando campo de título especial'); // Debug
            specialTitleContainer.style.display = 'none';
            specialTitleInput.removeAttribute('required');
            specialTitleInput.value = ''; // Limpiar el valor
        }
    }

    // Ejecutar al cargar la página
    toggleSpecialServiceTitle();

    // Ejecutar cuando cambia el select de tipo de servicio
    serviceTypeSelect.addEventListener('change', toggleSpecialServiceTitle);

    // Autocompletar dirección cuando se selecciona un cliente
    clientSelect.addEventListener('change', function() {
        console.log('Cliente seleccionado, index:', this.selectedIndex);
        const selectedOption = this.options[this.selectedIndex];
        console.log('Opción seleccionada:', selectedOption);
        const clientAddress = selectedOption.getAttribute('data-address');
        console.log('Dirección obtenida:', clientAddress);

        if (clientAddress && clientAddress.trim() !== '') {
            console.log('Asignando dirección al input:', clientAddress);
            addressInput.value = clientAddress;
        } else {
            console.log('No hay dirección, limpiando campo');
            addressInput.value = '';
        }
    });

    // Si hay un cliente pre-seleccionado (por ejemplo, desde old()), llenar la dirección
    if (clientSelect.value) {
        console.log('Cliente pre-seleccionado detectado:', clientSelect.value);
        const selectedOption = clientSelect.options[clientSelect.selectedIndex];
        const clientAddress = selectedOption.getAttribute('data-address');
        console.log('Dirección del cliente pre-seleccionado:', clientAddress);
        if (clientAddress && clientAddress.trim() !== '') {
            addressInput.value = clientAddress;
        }
    }
});
</script>
@endpush
