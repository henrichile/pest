@extends("layouts.app")

@section("title", "Nuevo Usuario - Pest Controller SAT")
@section("page-title", "Crear Usuario")

@section("content")
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Información del Usuario</h2>
            <p class="text-gray-600">Complete los datos para crear un nuevo usuario</p>
        </div>

        <form method="POST" action="{{ route("admin.users.store") }}" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nombre -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nombre Completo *</label>
                    <input type="text" id="name" name="name" required
                           value="{{ old("name") }}"
                           placeholder="Ingrese el nombre completo"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 @error("name") border-red-500 @enderror">
                    @error("name")
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                    <input type="email" id="email" name="email" required
                           value="{{ old("email") }}"
                           placeholder="usuario@empresa.cl"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 @error("email") border-red-500 @enderror">
                    @error("email")
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Contraseña -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Contraseña *</label>
                    <input type="password" id="password" name="password" required
                           placeholder="Mínimo 8 caracteres"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 @error("password") border-red-500 @enderror">
                    @error("password")
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirmar Contraseña -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirmar Contraseña *</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                           placeholder="Repita la contraseña"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 @error("password_confirmation") border-red-500 @enderror">
                    @error("password_confirmation")
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Rol -->
                <div class="md:col-span-2">
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-2">Rol *</label>
                    <select id="role" name="role" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 @error("role") border-red-500 @enderror">
                        <option value="">Seleccione un rol</option>
                        <option value="super-admin" {{ old("role") == "super-admin" ? "selected" : "" }}>Super Administrador</option>
                        <option value="technician" {{ old("role") == "technician" ? "selected" : "" }}>Técnico</option>
                    </select>
                    @error("role")
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Botones -->
            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route("admin.users.index") }}" 
                   class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancelar
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                    Crear Usuario
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
