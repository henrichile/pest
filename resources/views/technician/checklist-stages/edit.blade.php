@extends('layouts.app')

@section('title', 'Editar Cliente')

@section('content')
<div class="space-y-4 sm:space-y-6 pt-12 md:pt-0">
    <!-- Header -->
    <div class="md:flex md:items-center md:justify-between mb-6">
        <div class="min-w-0 flex-1">
            <h2 class="text-3xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight text-gray-900 dark:text-white" class="font-bold">
                Editar Cliente
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                Actualice los datos del cliente
            </p>
        </div>
        <div class="mt-4 md:mt-0 md:ml-4">
            <a href="{{ route('admin.clients.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                ← Volver
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white dark:bg-gray-800 border dark:border-gray-700 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
        <form method="POST" action="{{ route('admin.clients.update', $client) }}">
            @csrf
            @method('PUT')

            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="mb-4 p-4 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 p-4 rounded-lg">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Información Básica -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Información Básica</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nombre -->
                    <div>
                        <label for="name" class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">
                            Nombre <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name', $client->name) }}" required
                               class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                               style="border: 1px solid #e5e7eb !important; color: #111827;">
                    </div>

                    <!-- RUT -->
                    <div>
                        <label for="rut" class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">
                            RUT <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="rut" id="rut" value="{{ old('rut', $client->rut) }}" required
                               class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                               style="border: 1px solid #e5e7eb !important; color: #111827;">
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">
                            Email
                        </label>
                        <input type="email" name="email" id="email" value="{{ old('email', $client->email) }}"
                               class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                               style="border: 1px solid #e5e7eb !important; color: #111827;">
                    </div>

                    <!-- Teléfono -->
                    <div>
                        <label for="phone" class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">
                            Teléfono <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $client->phone) }}" required
                               class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                               style="border: 1px solid #e5e7eb !important; color: #111827;">
                    </div>

                    <!-- Dirección -->
                    <div class="md:col-span-2">
                        <label for="address" class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">
                            Dirección <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="address" id="address" value="{{ old('address', $client->address) }}" required
                               placeholder="Calle, número, piso, depto."
                               class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                               style="border: 1px solid #e5e7eb !important; color: #111827;">
                    </div>

                    <!-- Tipo de Negocio -->
                    <div>
                        <label for="business_type" class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">
                            Tipo de Negocio
                        </label>
                        <input type="text" name="business_type" id="business_type" value="{{ old('business_type', $client->business_type) }}"
                               placeholder="Ej: Restaurante, Retail, etc."
                               class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                               style="border: 1px solid #e5e7eb !important; color: #111827;">
                    </div>

                    <!-- Persona de Contacto -->
                    <div>
                        <label for="contact_person" class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">
                            Persona de Contacto
                        </label>
                        <input type="text" name="contact_person" id="contact_person" value="{{ old('contact_person', $client->contact_person) }}"
                               placeholder="Ej: Juan Pérez - Gerente"
                               class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                               style="border: 1px solid #e5e7eb !important; color: #111827;">
                    </div>
                </div>
            </div>


            <!-- Botones -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                <a href="{{ route('admin.clients.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
                    Actualizar Cliente
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

