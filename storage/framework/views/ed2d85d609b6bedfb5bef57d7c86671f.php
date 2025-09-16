<?php $__env->startSection("title", "Mis Servicios - Pest Controller SAT"); ?>
<?php $__env->startSection("page-title", "Mis Servicios"); ?>

<?php $__env->startSection("content"); ?>
<div class="max-w-7xl mx-auto space-y-6">

    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex items-center space-x-4">
            <div class="flex items-center space-x-2">
                <label class="text-sm font-medium text-gray-700">Estado:</label>
                <select class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos</option>
                    <option value="pendiente">Pendientes</option>
                    <option value="en_progreso">En Progreso</option>
                    <option value="finalizado">Finalizados</option>
                    <option value="vencido">Vencidos</option>
                </select>
            </div>
            <div class="flex items-center space-x-2">
                <label class="text-sm font-medium text-gray-700">Tipo:</label>
                <select class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos</option>
                    <option value="desratizacion">Sanitización</option>
                    <option value="desinsectacion">Desinsectación</option>
                    <option value="sanitizacion">Sanitización</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Lista de Servicios -->
    <div class="bg-white rounded-lg shadow-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Servicios Asignados</h3>
        </div>
        
        <div class="overflow-x-auto">
            <?php if($services->count() > 0): ?>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Programada</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prioridad</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900"><?php echo e($service->client->name ?? "N/A"); ?></div>
                            <?php if($service->address): ?>
                            <div class="text-sm text-gray-500"><?php echo e(Str::limit($service->address, 30)); ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                <?php if($service->service_type == "desratizacion"): ?> bg-red-100 text-red-800
                                <?php elseif($service->service_type == "desinsectacion"): ?> bg-yellow-100 text-yellow-800
                                <?php else: ?> bg-blue-100 text-blue-800
                                <?php endif; ?>">
                                <?php echo e(ucfirst($service->service_type)); ?>

                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <?php echo e($service->scheduled_date->format("d/m/Y H:i")); ?>

                            <?php if($service->scheduled_date < now() && $service->status == "pendiente"): ?>
                            <div class="text-xs text-red-600 font-medium">Vencido</div>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                <?php if($service->status == "pendiente"): ?> bg-gray-100 text-gray-800
                                <?php elseif($service->status == "en_progreso"): ?> bg-blue-100 text-blue-800
                                <?php elseif($service->status == "vencido"): ?> bg-red-100 text-red-800
                                <?php else: ?> bg-green-100 text-green-800
                                <?php endif; ?>">
                                <?php echo e(ucfirst(str_replace("_", " ", $service->status))); ?>

                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                <?php if($service->priority == "alta"): ?> bg-red-100 text-red-800
                                <?php elseif($service->priority == "media"): ?> bg-yellow-100 text-yellow-800
                                <?php else: ?> bg-green-100 text-green-800
                                <?php endif; ?>">
                                <?php echo e(ucfirst($service->priority)); ?>

                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <?php if($service->status == "pendiente"): ?>
                                <form method="POST" action="<?php echo e(route("technician.service.start", $service)); ?>" class="inline" onsubmit="return startServiceWithLocation(this, <?php echo e($service->id); ?>)">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="latitude" id="latitude_<?php echo e($service->id); ?>">
                                    <input type="hidden" name="longitude" id="longitude_<?php echo e($service->id); ?>">
                                    <input type="hidden" name="location_accuracy" id="location_accuracy_<?php echo e($service->id); ?>">
                                    <button type="submit" class="text-blue-600 hover:text-blue-900 font-medium">
                                        Iniciar
                                    </button>
                                </form>
                                <?php elseif($service->status == "en_progreso"): ?>
                                <a href="<?php echo e(route("technician.service.detail", $service)); ?>" class="text-green-600 hover:text-green-900 font-medium">
                                    Completar
                                </a>
                                <?php elseif($service->status == "finalizado"): ?>
                                <a href="<?php echo e(route("technician.service.pdf", $service)); ?>" class="text-blue-600 hover:text-blue-900 font-medium">
                                    📄 Descargar PDF
                                </a>
                                <?php endif; ?>
                                <a href="<?php echo e(route("technician.service.detail", $service)); ?>" class="text-gray-600 hover:text-gray-900 font-medium">
                                    Ver
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
            
            <!-- Paginación -->
            <div class="px-6 py-4 border-t border-gray-200">
                <?php echo e($services->links()); ?>

            </div>
            <?php else: ?>
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No hay servicios asignados</h3>
                <p class="mt-1 text-sm text-gray-500">No tienes servicios asignados en este momento.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make("layouts.app", array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /media/kike/Linux/pest/resources/views/technician/services.blade.php ENDPATH**/ ?>