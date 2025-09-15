@extends("layouts.app")

@section("title", "Gestión de Usuarios")

@section("content")
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Gestión de Usuarios</h1>
        <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
            <svg class="w-5 h-5 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"/>
            </svg>
            Nuevo Usuario
        </button>
    </div>
    
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <h3 class="text-lg font-semibold text-blue-800 mb-2">Gestión de Usuarios</h3>
        <p class="text-blue-700">Aquí podrás crear, editar y administrar usuarios del sistema, asignando roles y permisos específicos.</p>
        <ul class="mt-3 text-blue-700 space-y-1">
            <li>• Crear nuevos usuarios con diferentes roles</li>
            <li>• Asignar permisos específicos</li>
            <li>• Gestionar estado activo/inactivo</li>
            <li>• Cambiar contraseñas y datos de usuario</li>
        </ul>
    </div>
    
    <div class="text-center py-8">
        <p class="text-gray-500">Funcionalidad en desarrollo...</p>
    </div>
</div>
@endsection
