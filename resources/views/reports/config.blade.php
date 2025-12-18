@extends('layouts.app')

@section('title', 'Configuración de Reportes')

@section('content')
<div class="max-w-full mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Título -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Configuración de Reportes</h1>
        <p class="text-sm mt-1 text-gray-600 dark:text-white">Personaliza la configuración predeterminada de los reportes</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Configuración General -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Formato Predeterminado -->
            <div class="bg-white rounded-lg shadow-md border p-6" style="border: 1px solid #e5e7eb;">
                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Formato Predeterminado</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-2 text-gray-600 dark:text-white">Formato de Exportación</label>
                        <select class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 dark:text-white dark:bg-gray-700 dark:border-gray-600">
                            <option value="csv">CSV</option>
                            <option value="pdf">PDF</option>
                            <option value="excel">Excel</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2 text-gray-600 dark:text-white">Incluir Gráficos</label>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="include_charts" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                            <label for="include_charts" class="text-sm text-gray-600 dark:text-white">Incluir gráficos en reportes PDF</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Configuración de Filtros -->
            <div class="bg-white rounded-lg shadow-md border p-6" style="border: 1px solid #e5e7eb;">
                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Filtros Predeterminados</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-2 text-gray-600 dark:text-white">Rango de Fechas Predeterminado</label>
                        <select class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 dark:text-white dark:bg-gray-700 dark:border-gray-600">
                            <option value="this-month">Este Mes</option>
                            <option value="last-month">Último Mes</option>
                            <option value="last-3-months">Últimos 3 Meses</option>
                            <option value="this-year">Este Año</option>
                            <option value="custom">Personalizado</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2 text-gray-600 dark:text-white">Incluir Servicios Completados</label>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="include_completed" checked class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                            <label for="include_completed" class="text-sm text-gray-600 dark:text-white">Incluir servicios completados por defecto</label>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2 text-gray-600 dark:text-white">Incluir Servicios Pendientes</label>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="include_pending" checked class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                            <label for="include_pending" class="text-sm text-gray-600 dark:text-white">Incluir servicios pendientes por defecto</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Configuración de Notificaciones -->
            <div class="bg-white rounded-lg shadow-md border p-6" style="border: 1px solid #e5e7eb;">
                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Notificaciones</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-2 text-gray-600 dark:text-white">Notificar al Completar Exportación</label>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="notify_export" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                            <label for="notify_export" class="text-sm text-gray-600 dark:text-white">Enviar notificación cuando se complete una exportación</label>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2 text-gray-600 dark:text-white">Email para Notificaciones</label>
                        <input type="email" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 placeholder:text-gray-400 dark:placeholder:text-gray-500 dark:text-white dark:bg-gray-700 dark:border-gray-600" placeholder="email@ejemplo.com">
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel Lateral -->
        <div class="space-y-6">
            <!-- Información -->
            <div class="bg-white rounded-lg shadow-md border p-6" style="border: 1px solid #e5e7eb;">
                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Información</h3>
                <div class="space-y-3 text-sm text-gray-600 dark:text-white">
                    <p>Las configuraciones aquí establecidas se aplicarán como valores predeterminados para todos los nuevos reportes.</p>
                    <p>Puedes sobrescribir estas configuraciones al generar reportes individuales.</p>
                </div>
            </div>

            <!-- Acciones -->
            <div class="bg-white rounded-lg shadow-md border p-6" style="border: 1px solid #e5e7eb;">
                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Acciones</h3>
                <div class="space-y-3">
                    <button type="button" class="w-full px-4 py-2 rounded-lg text-sm font-medium transition-colors bg-green-500 text-white hover:bg-green-600">
                        Guardar Configuración
                    </button>
                    <button type="button" class="w-full px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        Restaurar Valores por Defecto
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

