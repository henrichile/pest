<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validación de Reporte - {{ $report->report_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl w-full space-y-8">
            <!-- Header -->
            <div class="text-center">
                <div class="mx-auto h-16 w-16 bg-green-600 rounded-full flex items-center justify-center">
                    <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h1 class="mt-4 text-3xl font-bold text-gray-900">PEST CONTROLLER SAT</h1>
                <h2 class="mt-2 text-xl text-gray-600">Validación de Reporte</h2>
            </div>

            <!-- Report Validation Card -->
            <div class="bg-white shadow-xl rounded-lg overflow-hidden">
                <!-- Header -->
                <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold text-white">Reporte Válido</h3>
                            <p class="text-green-100">{{ $report->report_number }}</p>
                        </div>
                        <div class="text-right">
                            <div class="text-green-100 text-sm">Generado</div>
                            <div class="text-white font-semibold">{{ $report->generated_at->format("d/m/Y H:i") }}</div>
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-6">
                    <!-- Service Information -->
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4">Información del Servicio</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">Cliente</p>
                                <p class="font-medium text-gray-900">{{ $report->service->client->name ?? "N/A" }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Dirección</p>
                                <p class="font-medium text-gray-900">{{ $report->service->address ?? "N/A" }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Tipo de Servicio</p>
                                <p class="font-medium text-gray-900">{{ $report->service->service_type ?? "Desratización" }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Técnico</p>
                                <p class="font-medium text-gray-900">{{ $report->service->assignedUser->name ?? "N/A" }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Report Details -->
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4">Detalles del Reporte</h4>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-600">Número de Reporte</p>
                                    <p class="font-medium text-gray-900">{{ $report->report_number }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Código QR</p>
                                    <p class="font-medium text-gray-900 font-mono">{{ $report->qr_code }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Generado por</p>
                                    <p class="font-medium text-gray-900">{{ $report->generatedBy->name ?? "N/A" }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Fecha de Generación</p>
                                    <p class="font-medium text-gray-900">{{ $report->generated_at->format("d/m/Y H:i:s") }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Checklist Summary -->
                    @if($report->report_data && isset($report->report_data["checklist_data"]))
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4">Resumen del Checklist</h4>
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                @if(isset($report->report_data["checklist_data"]["products"]))
                                <div>
                                    <p class="text-sm text-gray-600">Producto Aplicado</p>
                                    <p class="font-medium text-gray-900">{{ $report->report_data["checklist_data"]["products"]["applied_product"] ?? "N/A" }}</p>
                                </div>
                                @endif
                                <div>
                                    <p class="text-sm text-gray-600">Observaciones</p>
                                    <p class="font-medium text-gray-900">{{ $report->report_data["observations_count"] ?? 0 }} registradas</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Estado</p>
                                    <p class="font-medium text-green-600">✓ Completado</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Security Notice -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex">
                            <svg class="h-5 w-5 text-blue-400 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <h5 class="text-sm font-medium text-blue-800">Información de Seguridad</h5>
                                <p class="text-sm text-blue-700 mt-1">
                                    Este reporte ha sido validado digitalmente y contiene un código QR único para verificación. 
                                    La información mostrada corresponde al servicio realizado por PEST CONTROLLER SAT.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="bg-gray-50 px-6 py-4 border-t">
                    <div class="flex justify-between items-center">
                        <div class="text-sm text-gray-500">
                            PEST CONTROLLER SAT - Sistema de Validación Digital
                        </div>
                        <div class="text-sm text-gray-500">
                            Validado el {{ now()->format("d/m/Y H:i") }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="text-center">
                <button onclick="window.print()" 
                        class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Imprimir Validación
                </button>
            </div>
        </div>
    </div>
</body>
</html>
