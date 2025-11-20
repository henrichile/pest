@extends("layouts.app")

@section("title", "Nuevo Producto - Pest Controller SAT")
@section("page-title", "Crear Producto")

@section("content")
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Información del Producto</h2>
            <p class="text-gray-600">Complete los datos para crear un nuevo producto</p>
        </div>

        <form method="POST" action="{{ route("admin.products.store") }}" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nombre -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nombre Comercial *</label>
                    <input type="text" id="name" name="name" required
                           value="{{ old("name") }}"
                           placeholder="Ingrese el nombre comercial"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 @error("name") border-red-500 @enderror">
                    @error("name")
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Ingrediente Activo -->
                <div>
                    <label for="active_ingredient" class="block text-sm font-medium text-gray-700 mb-2">Ingrediente Activo *</label>
                    <input type="text" id="active_ingredient" name="active_ingredient" required
                           value="{{ old("active_ingredient") }}"
                           placeholder="Ej: Brodifacoum, Deltametrina"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 @error("active_ingredient") border-red-500 @enderror">
                    @error("active_ingredient")
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tipo de Servicio -->
                <div>
                    <label for="service_type" class="block text-sm font-medium text-gray-700 mb-2">Tipo de Servicio *</label>
                    <select id="service_type" name="service_type" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 @error("service_type") border-red-500 @enderror">
                        <option value="">Seleccione el tipo</option>
                        <option value="desratizacion" {{ old("service_type") == "desratizacion" ? "selected" : "" }}>Desratización</option>
                        <option value="desinsectacion" {{ old("service_type") == "desinsectacion" ? "selected" : "" }}>Desinsectación</option>
                        <option value="sanitizacion" {{ old("service_type") == "sanitizacion" ? "selected" : "" }}>Sanitización</option>
                    </select>
                    @error("service_type")
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Stock -->
                <div>
                    <label for="stock" class="block text-sm font-medium text-gray-700 mb-2">Stock Inicial *</label>
                    <input type="number" id="stock" name="stock" required min="0"
                           value="{{ old("stock", 0) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 @error("stock") border-red-500 @enderror">
                    @error("stock")
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Unidad -->
                <div>
                    <label for="unit" class="block text-sm font-medium text-gray-700 mb-2">Unidad *</label>
                    <select id="unit" name="unit" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 @error("unit") border-red-500 @enderror">
                        <option value="">Seleccione la unidad</option>
                        <option value="kg" {{ old("unit") == "kg" ? "selected" : "" }}>Kilogramos (kg)</option>
                        <option value="litros" {{ old("unit") == "litros" ? "selected" : "" }}>Litros</option>
                        <option value="unidades" {{ old("unit") == "unidades" ? "selected" : "" }}>Unidades</option>
                        <option value="gramos" {{ old("unit") == "gramos" ? "selected" : "" }}>Gramos (g)</option>
                    </select>
                    @error("unit")
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Registro SAG -->
                <div>
                    <label for="sag_registration" class="block text-sm font-medium text-gray-700 mb-2">Registro SAG</label>
                    <input type="text" id="sag_registration" name="sag_registration"
                           value="{{ old("sag_registration") }}"
                           placeholder="Ej: SAG-12345"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 @error("sag_registration") border-red-500 @enderror">
                    @error("sag_registration")
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Registro ISP -->
                <div>
                    <label for="isp_registration" class="block text-sm font-medium text-gray-700 mb-2">Registro ISP</label>
                    <input type="text" id="isp_registration" name="isp_registration"
                           value="{{ old("isp_registration") }}"
                           placeholder="Ej: ISP-54321"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 @error("isp_registration") border-red-500 @enderror">
                    @error("isp_registration")
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Descripción -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                <textarea id="description" name="description" rows="4"
                          placeholder="Descripción del producto y uso recomendado"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 @error("description") border-red-500 @enderror">{{ old("description") }}</textarea>
                @error("description")
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Botones -->
            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route("admin.products.index") }}" 
                   class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancelar
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                    Crear Producto
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
