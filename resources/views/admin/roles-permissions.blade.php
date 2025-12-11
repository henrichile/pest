@extends('layouts.app')

@section('title', 'Roles y Permisos - Pest Controller SAT')
@section('page-title', 'Gestión de Roles y Permisos')

@section('content')
<div class="space-y-6 pt-3 md:pt-0">
    <!-- Header con hamburguesa y título -->
    <div class="mb-4 sm:mb-6">
        <!-- Primera fila: Hamburguesa + Título (móvil) -->
        <div class="flex items-center gap-3 mb-4 md:hidden" style="padding-top: 2.5rem;">
            <!-- Hamburguesa (solo móvil) -->
            <button id="page-mobile-menu-button" class="flex-shrink-0 p-2 rounded-lg bg-white border border-gray-300 shadow-md hover:bg-gray-50 transition-colors" style="z-index: 1000; position: relative;">
                <svg id="page-menu-icon" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="text-gray-900 dark:text-white">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
                <svg id="page-close-icon" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="text-gray-900 dark:text-white">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            
            <!-- Título -->
            <div class="flex-1">
                <h2 class="text-2xl font-bold" class="text-gray-900 dark:text-white" style="font-weight: 700;">
                    Roles y Permisos
                </h2>
            </div>

            <!-- Iconos Header Móvil -->
            <div class="flex items-center gap-4">
                <!-- Notificaciones -->
                <a href="{{ route('admin.notification-center') ?? '#' }}" class="text-gray-500 hover:text-gray-700 relative">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                    </svg>
                    @php
                        $unreadCount = auth()->check() ? auth()->user()->unreadNotifications()->count() : 0;
                    @endphp
                    @if($unreadCount > 0)
                    <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-500 ring-2 ring-white transform translate-x-1/4 -translate-y-1/4"></span>
                    @endif
                </a>

                <!-- Perfil -->
                <a href="{{ Route::has('admin.profile') ? route('admin.profile') : (Route::has('profile') ? route('profile') : '#') }}" class="flex-shrink-0">
                    <div class="h-10 w-10 rounded-full bg-green-600 flex items-center justify-center shadow-sm flex-shrink-0">
                        <span class="text-white font-medium text-base">{{ substr(auth()->user()->name ?? 'U', 0, 1) }}</span>
                    </div>
                </a>
                <!-- Logout -->
                <form method="POST" action="{{ route('logout') }}" class="flex-shrink-0">
                    @csrf
                    <button type="submit" class="text-gray-500 hover:text-red-600 transition-colors" title="Cerrar Sesión">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
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
    <div x-data="{ 
        activeTab: 'roles', 
        editRole: null,
        closeEditModal() { this.editRole = null; }
    }" class="space-y-6">
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
                                    <button @click="editRole = {{ $role->id }}" 
                                            class="text-blue-600 hover:text-blue-900">Editar</button>
                                    <form method="POST" action="{{ route('admin.roles-permissions.destroy', $role) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" 
                                                onclick="return confirm('¿Estás seguro de eliminar este rol?')">Eliminar</button>
                                    </form>
                                    @else
                                    <span class="text-gray-400">No editable</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Edit Role Modal -->
            <div x-show="editRole !== null" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-50 overflow-y-auto"
                 @keydown.escape.window="closeEditModal()">
                <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeEditModal()"></div>
                    
                    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                        @foreach($roles as $role)
                        <div x-show="editRole === {{ $role->id }}" class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                Editar Rol: {{ $role->name }}
                            </h3>
                            
                            <form action="{{ route('admin.roles-permissions.update', $role) }}" method="POST" class="space-y-4">
                                @csrf
                                @method('PUT')
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nombre del Rol</label>
                                    <input type="text" name="name" value="{{ $role->name }}" required 
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Permisos</label>
                                    <div class="grid grid-cols-1 gap-2 max-h-60 overflow-y-auto border rounded-lg p-4">
                                        @foreach($permissions as $permission)
                                        <label class="flex items-center space-x-2">
                                            <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                                   {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}
                                                   class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                            <span class="text-sm text-gray-700">{{ $permission->name }}</span>
                                        </label>
                                        @endforeach
                                    </div>
                                </div>
                                
                                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                    <button type="submit" 
                                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                                        Actualizar Rol
                                    </button>
                                    <button type="button" @click="closeEditModal()"
                                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                        Cancelar
                                    </button>
                                </div>
                            </form>
                        </div>
                        @endforeach
                    </div>
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

@push('scripts')
<script>
(function() {
    function initPageMenuButton() {
        const pageMenuButton = document.getElementById('page-mobile-menu-button');
        
        if (!pageMenuButton) {
            console.warn('[PAGE MENU] Botón page-mobile-menu-button no encontrado, reintentando...');
            setTimeout(initPageMenuButton, 100);
            return;
        }
        
        console.log('[PAGE MENU] Botón encontrado, configurando listener...');
        
        pageMenuButton.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('[PAGE MENU] Click detectado, llamando a window.openMobileMenu()');
            
            if (typeof window.openMobileMenu === 'function') {
                window.openMobileMenu();
            } else {
                console.error('[PAGE MENU] window.openMobileMenu no está definida!');
            }
        });
        
        console.log('[PAGE MENU] Listener configurado correctamente');
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPageMenuButton);
    } else {
        initPageMenuButton();
    }
})();
</script>
@endpush

@endsection
