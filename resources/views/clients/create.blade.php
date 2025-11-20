@extends("layouts.app")

@section("title", "Nuevo Cliente - Pest Controller SAT")
@section("page-title", "Registrar Cliente")

@section("content")
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Información del Cliente</h2>
            <p class="text-gray-600">Complete los datos para registrar un nuevo cliente</p>
        </div>

        <form method="POST" action="{{ route("admin.clients.store") }}" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nombre -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Razón Social *</label>
                    <input type="text" id="name" name="name" required
                           value="{{ old("name") }}"
                           placeholder="Ingrese la razón social"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 @error("name") border-red-500 @enderror">
                    @error("name")
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- RUT -->
                <div>
                    <label for="rut" class="block text-sm font-medium text-gray-700 mb-2">RUT *</label>
                    <input type="text" id="rut" name="rut" required
                           value="{{ old("rut") }}"
                           placeholder="12.345.678-9"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 @error("rut") border-red-500 @enderror">
                    @error("rut")
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                    <input type="email" id="email" name="email" required
                           value="{{ old("email") }}"
                           placeholder="cliente@empresa.cl"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 @error("email") border-red-500 @enderror">
                    @error("email")
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Teléfono -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Teléfono *</label>
                    <input type="tel" id="phone" name="phone" required
                           value="{{ old("phone") }}"
                           placeholder="+56 9 1234 5678"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 @error("phone") border-red-500 @enderror">
                    @error("phone")
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tipo de Negocio -->
                <div>
                    <label for="business_type" class="block text-sm font-medium text-gray-700 mb-2">Tipo de Negocio</label>
                    <input type="text" id="business_type" name="business_type"
                           value="{{ old("business_type") }}"
                           placeholder="Ej: Restaurante, Oficina, Fábrica"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 @error("business_type") border-red-500 @enderror">
                    @error("business_type")
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Persona de Contacto -->
                <div>
                    <label for="contact_person" class="block text-sm font-medium text-gray-700 mb-2">Persona de Contacto</label>
                    <input type="text" id="contact_person" name="contact_person"
                           value="{{ old("contact_person") }}"
                           placeholder="Nombre del contacto principal"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 @error("contact_person") border-red-500 @enderror">
                    @error("contact_person")
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Dirección -->
            <div>
                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Dirección *</label>
                <input type="text" id="address" name="address" required
                       value="{{ old("address") }}"
                       placeholder="Dirección completa del cliente"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 @error("address") border-red-500 @enderror">
                @error("address")
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Botones -->
            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route("admin.clients.index") }}" 
                   class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancelar
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                    Registrar Cliente
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
