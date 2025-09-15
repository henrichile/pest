@extends('layouts.app')

@section('title', 'Roles y Permisos - Pest Controller SAT')
@section('page-title', 'Gestión de Roles y Permisos')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
        <h3 class="text-lg font-semibold text-green-800">Administración de Roles y Permisos</h3>
        <p class="text-green-600 mt-1">Gestiona los roles del sistema y asigna permisos específicos a cada usuario.</p>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <!-- Tabs -->
    <div x-data="{ activeTab: 'roles' }" class="space-y-6">
        <!-- Tab Navigation -->
        <div class="flex space-x-1 bg-gray-100 p-1 rounded-lg">
            <button @click="activeTab = 'roles'" 
                    :class="activeTab === 'roles' ? 'bg-white shadow text-green-600' : 'text-gray-600'"
                    class="px-4 py-2 rounded-md text-sm font-medium transition-colors">
                Gestión de Roles
            </button>
            <button @click="activeTab = 'permissions'" 
                    :class="activeTab === 'permissions' ? 'bg-white shadow text-green-600' : 'text-gray-600'"
                    class="px-4 py-2 rounded-md text-sm font-medium transition-colors">
                Gestión de Permisos
            </button>
            <button @click="activeTab = 'users'" 
                    :class="activeTab === 'users' ? 'bg-white shadow text-green-600' : 'text-gray-600'"
                    class="px-4 py-2 rounded-md text-sm font-medium transition-colors">
                Asignar Roles a Usuarios
            </button>
        </div>

        <!-- Roles Tab -->
        <div x-show="activeTab === 'roles'" class="space-y-6">
            <!-- Create Role Form -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Crear Nuevo Rol</h3>
                <form action="{{ route('admin.roles-permissions.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nombre del Rol</label>
                        <input type="text" name="name" required 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500"
                               placeholder="Ej: supervisor, coordinador">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Permisos</label>
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 max-h-60 overflow-y-auto border rounded-lg p-4">
                            @foreach($permissions as $permission)
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                       class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                <span class="text-sm text-gray-700">{{ $permission->name }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors">
                        Crear Rol
                    </button>
                </form>
            </div>

            <!-- Roles List -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold">Roles Existentes</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rol</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Permisos</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuarios</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($roles as $role)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $role->name }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($role->permissions as $permission)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                {{ $permission->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $role->users()->count() }} usuarios
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    @if($role->name !== 'super-admin')
                                    <form method="POST" action="{{ route('admin.roles-permissions.destroy', $role) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" 
                                                onclick="return confirm('¿Estás seguro de eliminar este rol?')">Eliminar</button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Permissions Tab -->
        <div x-show="activeTab === 'permissions'" class="space-y-6">
            <!-- Create Permission Form -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Crear Nuevo Permiso</h3>
                <form action="{{ route('admin.permissions.store') }}" method="POST" class="flex gap-4 items-end">
                    @csrf
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nombre del Permiso</label>
                        <input type="text" name="name" required 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500"
                               placeholder="Ej: manage-inventory, view-reports">
                    </div>
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors">
                        Crear Permiso
                    </button>
                </form>
            </div>

            <!-- Permissions List -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold">Permisos Existentes</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 p-6">
                    @foreach($permissions as $permission)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-900">{{ $permission->name }}</span>
                            <form method="POST" action="{{ route('admin.permissions.destroy', $permission) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 text-sm"
                                        onclick="return confirm('¿Estás seguro de eliminar este permiso?')">Eliminar</button>
                            </form>
                        </div>
                        <div class="text-xs text-gray-500 mt-1">
                            Usado en {{ $permission->roles()->count() }} roles
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Users Tab -->
        <div x-show="activeTab === 'users'" class="space-y-6">
            <!-- Assign Role Form -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Asignar Rol a Usuario</h3>
                <form action="{{ route('admin.roles-permissions.assign') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Usuario</label>
                        <select name="user_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                            <option value="">Seleccionar usuario</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Rol</label>
                        <select name="role" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                            <option value="">Seleccionar rol</option>
                            @foreach($roles as $role)
                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors">
                        Asignar Rol
                    </button>
                </form>
            </div>

            <!-- Users List -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold">Usuarios y sus Roles</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Roles</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($users as $user)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $user->email }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($user->roles as $role)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                    {{ $role->name === 'super-admin' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' }}">
                                                {{ $role->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            {{ $user->email_verified_at ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $user->email_verified_at ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endsection
