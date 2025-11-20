@extends("layouts.app")

@section("title", "Estadísticas - Pest Controller SAT")
@section("page-title", "Panel de Estadísticas")

@section("content")
<div class="space-y-6">
    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow-lg p-6 hover-scale">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Servicios</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats["total_services"] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6 hover-scale">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Servicios Completados</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats["completed_services"] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6 hover-scale">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-yellow-500 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Servicios Pendientes</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats["pending_services"] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6 hover-scale">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Clientes Activos</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats["active_clients"] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6 hover-scale">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-red-500 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">En Progreso</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats["in_progress_services"] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6 hover-scale">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-indigo-500 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Clientes</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats["total_clients"] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Services by Type Chart -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Servicios por Tipo</h3>
            <div class="h-64">
                <canvas id="servicesByTypeChart"></canvas>
            </div>
        </div>

        <!-- Services by Status Chart -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Servicios por Estado</h3>
            <div class="h-64">
                <canvas id="servicesByStatusChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Services by Priority Chart -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Servicios por Prioridad</h3>
        <div class="h-64">
            <canvas id="servicesByPriorityChart"></canvas>
        </div>
    </div>

    <!-- Monthly Trend Chart -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Tendencia Mensual (Últimos 6 meses)</h3>
        <div class="h-64">
            <canvas id="monthlyTrendChart"></canvas>
        </div>
    </div>
</div>

@endsection

@section("scripts")
<script>
// Services by Type Chart
const servicesByTypeCtx = document.getElementById("servicesByTypeChart").getContext("2d");
new Chart(servicesByTypeCtx, {
    type: "doughnut",
    data: {
        labels: ["Desratización", "Desinsectación", "Sanitización"],
        datasets: [{
            data: [
                {{ $servicesByType["desratizacion"] ?? 0 }},
                {{ $servicesByType["desinsectacion"] ?? 0 }},
                {{ $servicesByType["sanitizacion"] ?? 0 }}
            ],
            backgroundColor: ["#EF4444", "#F59E0B", "#3B82F6"],
            borderWidth: 2,
            borderColor: "#fff"
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: "bottom"
            }
        }
    }
});

// Services by Status Chart
const servicesByStatusCtx = document.getElementById("servicesByStatusChart").getContext("2d");
new Chart(servicesByStatusCtx, {
    type: "pie",
    data: {
        labels: ["Pendiente", "En Progreso", "Finalizado", "Vencido"],
        datasets: [{
            data: [
                {{ $servicesByStatus["pendiente"] ?? 0 }},
                {{ $servicesByStatus["en_progreso"] ?? 0 }},
                {{ $servicesByStatus["finalizado"] ?? 0 }},
                {{ $servicesByStatus["vencido"] ?? 0 }}
            ],
            backgroundColor: ["#6B7280", "#3B82F6", "#10B981", "#EF4444"],
            borderWidth: 2,
            borderColor: "#fff"
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: "bottom"
            }
        }
    }
});

// Services by Priority Chart
const servicesByPriorityCtx = document.getElementById("servicesByPriorityChart").getContext("2d");
new Chart(servicesByPriorityCtx, {
    type: "bar",
    data: {
        labels: ["Alta", "Media", "Baja"],
        datasets: [{
            label: "Cantidad de Servicios",
            data: [
                {{ $servicesByPriority["alta"] ?? 0 }},
                {{ $servicesByPriority["media"] ?? 0 }},
                {{ $servicesByPriority["baja"] ?? 0 }}
            ],
            backgroundColor: ["#EF4444", "#F59E0B", "#10B981"],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Monthly Trend Chart
const monthlyTrendCtx = document.getElementById("monthlyTrendChart").getContext("2d");
new Chart(monthlyTrendCtx, {
    type: "line",
    data: {
        labels: {!! json_encode(array_keys($servicesByMonth)) !!},
        datasets: [{
            label: "Servicios",
            data: {!! json_encode(array_values($servicesByMonth)) !!},
            borderColor: "#10B981",
            backgroundColor: "rgba(16, 185, 129, 0.1)",
            borderWidth: 2,
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>
@endsection
