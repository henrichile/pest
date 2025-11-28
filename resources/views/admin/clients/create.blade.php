@extends('layouts.app')

@section('title', 'Crear Nuevo Cliente')

@section('content')
<div class="space-y-4 sm:space-y-6 pt-12 md:pt-0">
    <!-- Header -->
    <div class="md:flex md:items-center md:justify-between mb-6">
        <div class="min-w-0 flex-1">
            <h2 class="text-3xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight" style="color: #111827; font-weight: 700;">
                Crear Nuevo Cliente
            </h2>
            <p class="mt-1 text-sm" style="color: #6b7280;">
                Complete los datos del nuevo cliente
            </p>
        </div>
        <div class="mt-4 md:mt-0 md:ml-4">
            <a href="{{ route('admin.clients.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium transition-colors" style="color: #374151; border-color: #d1d5db; hover:background: #f9fafb;">
                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                ← Volver
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white border dark:border-gray-700 rounded-lg p-6" style="border: 1px solid #e5e7eb !important;">
        <form method="POST" action="{{ route('admin.clients.store') }}">
            @csrf

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

            <!-- Información Básica -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold mb-4" style="color: #111827;">Información Básica</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nombre de la Empresa -->
                    <div>
                        <label for="business_name" class="block text-sm font-medium mb-1" style="color: #374151;">
                            Nombre de la Empresa <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="business_name" id="business_name" value="{{ old('business_name') }}" required
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                               style="border: 1px solid #e5e7eb !important; color: #111827;">
                    </div>

                    <!-- RUT -->
                    <div>
                        <label for="rut" class="block text-sm font-medium mb-1" style="color: #374151;">
                            RUT <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="rut" id="rut" value="{{ old('rut') }}" required
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                               style="border: 1px solid #e5e7eb !important; color: #111827;">
                    </div>

                    <!-- Tipo de Negocio -->
                    <div>
                        <label for="business_type" class="block text-sm font-medium mb-1" style="color: #374151;">
                            Tipo de Negocio
                        </label>
                        <input type="text" name="business_type" id="business_type" value="{{ old('business_type') }}"
                               placeholder="Ej: Restaurante, Retail, etc."
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                               style="border: 1px solid #e5e7eb !important; color: #111827;">
                    </div>

                    <!-- Método de Pago -->
                    <div>
                        <label for="payment_method" class="block text-sm font-medium mb-1" style="color: #374151;">
                            Método de Pago
                        </label>
                        <input type="text" name="payment_method" id="payment_method" value="{{ old('payment_method') }}"
                               placeholder="Ej: Transferencia, Factura 30 días, etc."
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                               style="border: 1px solid #e5e7eb !important; color: #111827;">
                    </div>
                </div>
            </div>

            <!-- Contacto Principal -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold mb-4" style="color: #111827;">Contacto Principal</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nombre del Contacto -->
                    <div>
                        <label for="contact_name" class="block text-sm font-medium mb-1" style="color: #374151;">
                            Nombre del Contacto
                        </label>
                        <input type="text" name="contact_name" id="contact_name" value="{{ old('contact_name') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                               style="border: 1px solid #e5e7eb !important; color: #111827;">
                    </div>

                    <!-- Cargo -->
                    <div>
                        <label for="contact_position" class="block text-sm font-medium mb-1" style="color: #374151;">
                            Cargo
                        </label>
                        <input type="text" name="contact_position" id="contact_position" value="{{ old('contact_position') }}"
                               placeholder="Ej: Gerente, Jefe de Operaciones"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                               style="border: 1px solid #e5e7eb !important; color: #111827;">
                    </div>

                    <!-- Email del Contacto -->
                    <div>
                        <label for="contact_email" class="block text-sm font-medium mb-1" style="color: #374151;">
                            Email del Contacto
                        </label>
                        <input type="email" name="contact_email" id="contact_email" value="{{ old('contact_email') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                               style="border: 1px solid #e5e7eb !important; color: #111827;">
                    </div>

                    <!-- Teléfono del Contacto -->
                    <div>
                        <label for="contact_phone" class="block text-sm font-medium mb-1" style="color: #374151;">
                            Teléfono del Contacto
                        </label>
                        <input type="text" name="contact_phone" id="contact_phone" value="{{ old('contact_phone') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                               style="border: 1px solid #e5e7eb !important; color: #111827;">
                    </div>
                </div>
            </div>

            <!-- Información Adicional -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold mb-4" style="color: #111827;">Información Adicional</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Notas -->
                    <div class="md:col-span-2">
                        <label for="notes" class="block text-sm font-medium mb-1" style="color: #374151;">
                            Notas
                        </label>
                        <textarea name="notes" id="notes" rows="3"
                                  class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                                  style="border: 1px solid #e5e7eb !important; color: #111827;">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                <a href="{{ route('admin.clients.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium transition-colors" style="color: #374151; border-color: #d1d5db; hover:background: #f9fafb;">
                    Cancelar
                </a>
                <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
                    Crear Cliente
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

