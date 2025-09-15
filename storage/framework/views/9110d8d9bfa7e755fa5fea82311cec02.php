<?php $__env->startSection("title", "Dashboard - Pest Controller SAT"); ?>
<?php $__env->startSection("page-title", "Dashboard"); ?>

<?php $__env->startSection("content"); ?>
<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <a href="<?php echo e(route("admin.services.index")); ?>" class="block bg-white rounded-lg shadow-lg p-6 hover:shadow-xl hover:scale-105 transition-all duration-200 cursor-pointer">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Servicios Totales</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo e($stats["work_orders"]); ?></p>
                </div>
            </div>
        </a>

        <a href="<?php echo e(route("admin.services.index")); ?>" class="block bg-white rounded-lg shadow-lg p-6 hover:shadow-xl hover:scale-105 transition-all duration-200 cursor-pointer">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Clientes Activos</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo e($stats["clients"]); ?></p>
                </div>
            </div>
        </a>

        <a href="<?php echo e(route("admin.services.index")); ?>" class="block bg-white rounded-lg shadow-lg p-6 hover:shadow-xl hover:scale-105 transition-all duration-200 cursor-pointer">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-yellow-500 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Sitios Atendidos</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo e($stats["sites"]); ?></p>
                </div>
            </div>
        </a>

        <a href="<?php echo e(route("admin.services.index")); ?>" class="block bg-white rounded-lg shadow-lg p-6 hover:shadow-xl hover:scale-105 transition-all duration-200 cursor-pointer">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Técnicos Activos</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo e($stats["technicians"]); ?></p>
                </div>
            </div>
        </a>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Acciones Rápidas</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="<?php echo e(route("admin.services.create")); ?>" class="bg-red-50 border border-red-200 rounded-lg p-4 hover:bg-red-100 transition-colors hover-scale">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-red-500 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-900">Nuevo Servicio</h3>
                        <p class="text-sm text-gray-500">Crear orden de trabajo</p>
                    </div>
                </div>
            </a>

            <a href="<?php echo e(route("admin.clients.create")); ?>" class="bg-green-50 border border-green-200 rounded-lg p-4 hover:bg-green-100 transition-colors hover-scale">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-900">Nuevo Cliente</h3>
                        <p class="text-sm text-gray-500">Registrar cliente</p>
                    </div>
                </div>
            </a>

            <a href="<?php echo e(route("admin.services.index")); ?>" class="bg-blue-50 border border-blue-200 rounded-lg p-4 hover:bg-blue-100 transition-colors hover-scale">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-900">Ver Estadísticas</h3>
                        <p class="text-sm text-gray-500">Reportes y gráficos</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Recent Services -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Servicios Recientes</h2>
            <a href="<?php echo e(route("admin.services.index")); ?>" class="text-green-600 hover:text-green-700 text-sm font-medium">Ver todos</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prioridad</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            No hay servicios registrados aún
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->startSection("scripts"); ?><script src="/js/dashboard-kpis.js?v=<?php echo e(time()); ?>"></script><?php $__env->stopSection(); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection("scripts"); ?><script>    document.addEventListener("DOMContentLoaded", function() {        const kpiCards = document.querySelectorAll(".bg-white.rounded-lg.shadow-lg.p-6");        kpiCards.forEach((card, index) => {            card.style.cursor = "pointer";            card.style.transition = "all 0.2s";            card.addEventListener("click", function() {                let url = "";                switch(index) {                    case 0: url = "/services"; break;                    case 1: url = "/clients"; break;                    case 2: url = "/services?filter=completed"; break;                    case 3: url = "/admin/users?role=technician"; break;                }                if (url) window.location.href = url;            });        });    });</script><?php $__env->stopSection(); ?>

<?php echo $__env->make("layouts.app", array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/pest-controller/resources/views/dashboard.blade.php ENDPATH**/ ?>