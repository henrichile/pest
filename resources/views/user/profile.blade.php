@extends('layouts.app')

@section('title', 'Mi Perfil')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="md:flex md:items-center md:justify-between mb-6">
        <div class="min-w-0 flex-1">
            <h2 class="text-3xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight" style="color: #111827; font-weight: 700;">
                Mi Perfil
            </h2>
            <p class="mt-1 text-sm" style="color: #6b7280;">
                Gestiona tu información personal y configuración de cuenta
            </p>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-md bg-green-50 p-4 mb-6" style="background: #f0fdf4; border: 1px solid #22c55e;">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" style="color: #22c55e;" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.236 4.53L7.53 10.53a.75.75 0 00-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium" style="color: #166534;">
                        {{ session('success') }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-md bg-red-50 p-4 mb-6" style="background: #fef2f2; border: 1px solid #ef4444;">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" style="color: #ef4444;" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium" style="color: #991b1b;">
                        {{ session('error') }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Profile Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Información Personal -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm" style="border: 1px solid #e5e7eb; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);">
                <div class="px-6 py-4 border-b border-gray-200" style="border-bottom: 1px solid #e5e7eb;">
                    <h3 class="text-lg font-semibold" style="color: #111827;">Información Personal</h3>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label for="name" class="block text-sm font-medium mb-2" style="color: #111827;">Nombre</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500" 
                                    style="border: 1px solid #d1d5db; border-radius: 8px;">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium mb-2" style="color: #111827;">Correo Electrónico</label>
                                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                    style="border: 1px solid #d1d5db; border-radius: 8px;">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-medium mb-2" style="color: #111827;">Teléfono</label>
                                <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                    style="border: 1px solid #d1d5db; border-radius: 8px;">
                                @error('phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="timezone" class="block text-sm font-medium mb-2" style="color: #111827;">Zona Horaria</label>
                                <select name="timezone" id="timezone"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                    style="border: 1px solid #d1d5db; border-radius: 8px;">
                                    <option value="America/Santiago" {{ $user->timezone === 'America/Santiago' ? 'selected' : '' }}>Santiago (GMT-3)</option>
                                    <option value="America/Lima" {{ $user->timezone === 'America/Lima' ? 'selected' : '' }}>Lima (GMT-5)</option>
                                    <option value="America/Mexico_City" {{ $user->timezone === 'America/Mexico_City' ? 'selected' : '' }}>Ciudad de México (GMT-6)</option>
                                    <option value="America/Bogota" {{ $user->timezone === 'America/Bogota' ? 'selected' : '' }}>Bogotá (GMT-5)</option>
                                    <option value="America/Buenos_Aires" {{ $user->timezone === 'America/Buenos_Aires' ? 'selected' : '' }}>Buenos Aires (GMT-3)</option>
                                </select>
                                @error('timezone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="submit" 
                                class="px-6 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors"
                                style="background: #22c55e; color: white; border-radius: 8px;">
                                Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Cambiar Contraseña -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm" style="border: 1px solid #e5e7eb; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);">
                <div class="px-6 py-4 border-b border-gray-200" style="border-bottom: 1px solid #e5e7eb;">
                    <h3 class="text-lg font-semibold" style="color: #111827;">Cambiar Contraseña</h3>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('profile.change-password') }}">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label for="current_password" class="block text-sm font-medium mb-2" style="color: #111827;">Contraseña Actual</label>
                                <input type="password" name="current_password" id="current_password" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                    style="border: 1px solid #d1d5db; border-radius: 8px;">
                                @error('current_password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="new_password" class="block text-sm font-medium mb-2" style="color: #111827;">Nueva Contraseña</label>
                                <input type="password" name="new_password" id="new_password" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                    style="border: 1px solid #d1d5db; border-radius: 8px;">
                                @error('new_password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="new_password_confirmation" class="block text-sm font-medium mb-2" style="color: #111827;">Confirmar Nueva Contraseña</label>
                                <input type="password" name="new_password_confirmation" id="new_password_confirmation" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                    style="border: 1px solid #d1d5db; border-radius: 8px;">
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="submit" 
                                class="px-6 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors"
                                style="background: #22c55e; color: white; border-radius: 8px;">
                                Cambiar Contraseña
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Column: Stats and Info -->
        <div class="space-y-6">
            <!-- Profile Card -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm" style="border: 1px solid #e5e7eb; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);">
                <div class="p-6 text-center">
                    <img class="h-24 w-24 rounded-full mx-auto mb-4 border-4 border-gray-200" 
                         src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQ?:jEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" 
                         alt="{{ $user->name }}">
                    <h3 class="text-xl font-semibold mb-1" style="color: #111827;">{{ $user->name }}</h3>
                    <p class="text-sm mb-4" style="color: #6b7280;">{{ $user->email }}</p>
                    
                    @if($roles->count() > 0)
                        <div class="flex flex-wrap justify-center gap-2 mb-4">
                            @foreach($roles as $role)
                                <span class="px-3 py-1 text-xs font-semibold rounded-full" 
                                      style="background: #dcfce7; color: #166534;">
                                    {{ ucfirst($role->name) }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Statistics -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm" style="border: 1px solid #e5e7eb; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);">
                <div class="px-6 py-4 border-b border-gray-200" style="border-bottom: 1px solid #e5e7eb;">
                    <h3 class="text-lg font-semibold" style="color: #111827;">Estadísticas</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm" style="color: #6b7280;">Órdenes de Trabajo</span>
                        <span class="text-lg font-semibold" style="color: #111827;">{{ $stats['total_work_orders'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm" style="color: #6b7280;">Completadas</span>
                        <span class="text-lg font-semibold" style="color: #22c55e;">{{ $stats['completed_work_orders'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm" style="color: #6b7280;">Sesiones</span>
                        <span class="text-lg font-semibold" style="color: #111827;">{{ $stats['total_sessions'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm" style="color: #6b7280;">Tratamientos</span>
                        <span class="text-lg font-semibold" style="color: #111827;">{{ $stats['total_treatments'] ?? 0 }}</span>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            @if($recentActivities->count() > 0)
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm" style="border: 1px solid #e5e7eb; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);">
                <div class="px-6 py-4 border-b border-gray-200" style="border-bottom: 1px solid #e5e7eb;">
                    <h3 class="text-lg font-semibold" style="color: #111827;">Actividad Reciente</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($recentActivities->take(5) as $activity)
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0 w-2 h-2 rounded-full mt-2" style="background: #22c55e;"></div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm" style="color: #111827;">{{ $activity->description ?? 'Actividad' }}</p>
                                    <p class="text-xs mt-1" style="color: #9ca3af;">{{ $activity->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

