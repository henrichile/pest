@extends('layouts.app')

@section('title', 'Editar Usuario')

@section('content')
<div class="space-y-4 sm:space-y-6 pt-12 md:pt-0">
    <!-- Header -->
    <div class="md:flex md:items-center md:justify-between mb-6">
        <div class="min-w-0 flex-1">
            <h2 class="text-3xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight" style="color: #111827; font-weight: 700;">
                Editar Usuario: {{ $user->name }}
            </h2>
            <p class="mt-1 text-sm" style="color: #6b7280;">
                Modifica la información del usuario
            </p>
        </div>
        <div class="mt-4 md:mt-0 md:ml-4">
            <a href="{{ route('admin.users.show', $user) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium transition-colors" style="color: #374151; border-color: #d1d5db; hover:background: #f9fafb;">
                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Volver al Usuario
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white border dark:border-gray-700 rounded-lg p-6" style="border: 1px solid #e5e7eb !important;">
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf
            @method('PUT')

            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="mb-4 p-4 rounded-lg" style="background: #d1fae5; color: #065f46;">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 rounded-lg" style="background: #fee2e2; color: #991b1b;">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 p-4 rounded-lg" style="background: #fee2e2; color: #991b1b;">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nombre -->
                <div>
                    <label for="name" class="block text-sm font-medium mb-1" style="color: #374151;">
                        Nombre <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                           style="border: 1px solid #e5e7eb !important; color: #111827;">
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium mb-1" style="color: #374151;">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                           style="border: 1px solid #e5e7eb !important; color: #111827;">
                </div>

                <!-- Teléfono -->
                <div>
                    <label for="phone" class="block text-sm font-medium mb-1" style="color: #374151;">
                        Teléfono
                    </label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                           style="border: 1px solid #e5e7eb !important; color: #111827;">
                </div>

                <!-- Estado -->
                <div>
                    <label for="is_active" class="block text-sm font-medium mb-1" style="color: #374151;">
                        Estado <span class="text-red-500">*</span>
                    </label>
                    <select name="is_active" id="is_active" required
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                            style="border: 1px solid #e5e7eb !important; color: #111827;">
                        <option value="1" {{ old('is_active', $user->is_active) == 1 ? 'selected' : '' }}>Activo</option>
                        <option value="0" {{ old('is_active', $user->is_active) == 0 ? 'selected' : '' }}>Inactivo</option>
                    </select>
                </div>

                <!-- Contraseña (Opcional) -->
                <div>
                    <label for="password" class="block text-sm font-medium mb-1" style="color: #374151;">
                        Nueva Contraseña (opcional)
                    </label>
                    <input type="password" name="password" id="password" minlength="8"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                           style="border: 1px solid #e5e7eb !important; color: #111827;">
                    <p class="mt-1 text-xs" style="color: #6b7280;">Dejar en blanco para mantener la contraseña actual. Mínimo 8 caracteres si se cambia.</p>
                </div>

                <!-- Confirmar Contraseña -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium mb-1" style="color: #374151;">
                        Confirmar Nueva Contraseña
                    </label>
                    <input type="password" name="password_confirmation" id="password_confirmation" minlength="8"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                           style="border: 1px solid #e5e7eb !important; color: #111827;">
                </div>

                <!-- Dirección -->
                <div class="md:col-span-2">
                    <label for="address" class="block text-sm font-medium mb-1" style="color: #374151;">
                        Dirección
                    </label>
                    <textarea name="address" id="address" rows="3"
                              class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                              style="border: 1px solid #e5e7eb !important; color: #111827;">{{ old('address', $user->address) }}</textarea>
                </div>

                <!-- Roles -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-2" style="color: #374151;">
                        Roles
                    </label>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        @foreach($roles as $role)
                            <label class="flex items-center">
                                <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                                       {{ in_array($role->name, old('roles', $user->roles->pluck('name')->toArray())) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                <span class="ml-2 text-sm" style="color: #374151;">
                                    @if($role->name === 'technician')
                                        Técnico
                                    @elseif($role->name === 'super-admin')
                                        Super Admin
                                    @else
                                        {{ ucfirst($role->name) }}
                                    @endif
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Permisos (Opcional) -->
                @if($permissions->count() > 0)
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-2" style="color: #374151;">
                        Permisos (Opcional)
                    </label>
                    <div class="max-h-48 overflow-y-auto border border-gray-200 rounded-lg p-3" style="border: 1px solid #e5e7eb !important;">
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                            @foreach($permissions as $permission)
                                <label class="flex items-center">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                           {{ in_array($permission->name, old('permissions', $user->permissions->pluck('name')->toArray())) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                    <span class="ml-2 text-xs" style="color: #374151;">{{ $permission->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Submit Buttons -->
            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('admin.users.show', $user) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium transition-colors" style="color: #374151; border-color: #d1d5db; hover:background: #f9fafb;">
                    Cancelar
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white transition-colors" style="background: #22c55e; hover:background: #16a34a;">
                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
