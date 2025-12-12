@extends('layouts.app')

@section('title', 'Reportes Programados')

@section('content')
<div class="max-w-full mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Título -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Reportes Programados</h1>
                <p class="text-sm mt-1 text-gray-600 dark:text-gray-300">Gestiona y visualiza los reportes programados automáticamente</p>
            </div>
            <a href="{{ route('admin.reports.scheduled.create') }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2 bg-green-500 text-white hover:bg-green-600">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Programar Nuevo Reporte
            </a>
        </div>
    </div>

    <!-- Mensajes -->
    @if(session('success'))
        <div class="mb-4 rounded-md p-4" style="background: #f0fdf4; border: 1px solid #22c55e;">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 rounded-md bg-red-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Lista de Reportes Programados -->
    <div class="bg-white rounded-lg shadow-md border" style="border: 1px solid #e5e7eb;">
        <div class="p-6">
            @if($scheduledReports->isEmpty())
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5a2.25 2.25 0 002.25-2.25m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5a2.25 2.25 0 012.25 2.25v7.5" />
                    </svg>
                    <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">No hay reportes programados</h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">Comienza programando tu primer reporte automático.</p>
                    <div class="mt-6">
                        <a href="{{ route('admin.reports.scheduled.create') }}" class="inline-flex items-center rounded-md bg-green-500 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-600">
                            <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            Programar Reporte
                        </a>
                    </div>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Nombre</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Tipo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Frecuencia</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Próxima Ejecución</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Destinatarios</th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-white divide-y divide-gray-200">
                            @foreach($scheduledReports as $report)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $report->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    @if($report->type === 'services')
                                        Servicios
                                    @elseif($report->type === 'clients')
                                        Clientes
                                    @elseif($report->type === 'technicians')
                                        Técnicos
                                    @elseif($report->type === 'financial')
                                        Financiero
                                    @else
                                        {{ ucfirst($report->type) }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    @if($report->frequency === 'daily')
                                        Diario
                                    @elseif($report->frequency === 'weekly')
                                        Semanal
                                    @elseif($report->frequency === 'monthly')
                                        Mensual
                                    @elseif($report->frequency === 'quarterly')
                                        Trimestral
                                    @elseif($report->frequency === 'yearly')
                                        Anual
                                    @else
                                        {{ ucfirst($report->frequency) }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    @if($report->next_run_at)
                                        {{ $report->next_run_at->format('d/m/Y H:i') }}
                                    @else
                                        <span class="text-gray-600 dark:text-gray-300">No programado</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($report->is_active)
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Activo</span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">Inactivo</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    @if($report->recipients && count($report->recipients) > 0)
                                        {{ count($report->recipients) }} destinatario(s)
                                    @else
                                        <span class="text-gray-600 dark:text-gray-300">Sin destinatarios</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.reports.scheduled.edit', $report) }}" class="text-green-600 hover:text-green-900">Editar</a>
                                        <form action="{{ route('admin.reports.scheduled.toggle', $report) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-blue-600 hover:text-blue-900">
                                                {{ $report->is_active ? 'Desactivar' : 'Activar' }}
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.reports.scheduled.destroy', $report) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar este reporte programado?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

