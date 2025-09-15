<?php $__env->startSection("title", "Detalle del Cliente - Pest Controller SAT"); ?>
<?php $__env->startSection("page-title", "Detalle del Cliente"); ?>

<?php $__env->startSection("content"); ?>
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Client Header -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900"><?php echo e($client->name); ?></h2>
                <p class="text-gray-600"><?php echo e($client->address); ?></p>
            </div>
            <div class="text-right">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                    Cliente Activo
                </span>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <h3 class="text-sm font-medium text-gray-500">RUT</h3>
                <p class="text-lg font-semibold text-gray-900"><?php echo e($client->rut); ?></p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Tipo de Negocio</h3>
                <p class="text-lg font-semibold text-gray-900"><?php echo e($client->business_type ?? "No especificado"); ?></p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Servicios Totales</h3>
                <p class="text-lg font-semibold text-gray-900"><?php echo e($client->services->count()); ?></p>
            </div>
        </div>
    </div>

    <!-- Client Details -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Contact Information -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Información de Contacto</h3>
            <div class="space-y-3">
                <div>
                    <span class="text-sm font-medium text-gray-500">Email:</span>
                    <p class="text-gray-900"><?php echo e($client->email); ?></p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Teléfono:</span>
                    <p class="text-gray-900"><?php echo e($client->phone); ?></p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Dirección:</span>
                    <p class="text-gray-900"><?php echo e($client->address); ?></p>
                </div>
                <?php if($client->contact_person): ?>
                <div>
                    <span class="text-sm font-medium text-gray-500">Persona de Contacto:</span>
                    <p class="text-gray-900"><?php echo e($client->contact_person); ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Service Statistics -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Estadísticas de Servicios</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-500">Total Servicios:</span>
                    <span class="text-gray-900 font-semibold"><?php echo e($client->services->count()); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-500">Servicios Completados:</span>
                    <span class="text-gray-900 font-semibold"><?php echo e($client->services->where("status", "finalizado")->count()); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-500">Servicios Pendientes:</span>
                    <span class="text-gray-900 font-semibold"><?php echo e($client->services->where("status", "pendiente")->count()); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-500">Servicios en Progreso:</span>
                    <span class="text-gray-900 font-semibold"><?php echo e($client->services->where("status", "en_progreso")->count()); ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Services History -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Historial de Servicios</h3>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check("create-services")): ?>
            <a href="<?php echo e(route("services.create")); ?>?client_id=<?php echo e($client->id); ?>" 
               class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"></path>
                </svg>
                <span>Nuevo Servicio</span>
            </a>
            <?php endif; ?>
        </div>
        
        <?php if($client->services->count() > 0): ?>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prioridad</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $__currentLoopData = $client->services->sortByDesc("created_at"); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-gray-50">
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
                            <a href="<?php echo e(route("services.show", $service)); ?>" 
                               class="text-green-600 hover:text-green-900">Ver</a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <p class="text-gray-500 text-center py-4">No hay servicios registrados para este cliente</p>
        <?php endif; ?>
    </div>

    <!-- Actions -->
    <div class="flex justify-between items-center">
        <a href="<?php echo e(route("clients.index")); ?>" 
           class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
            Volver a Clientes
        </a>
        
        <div class="flex space-x-4">
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check("edit-clients")): ?>
            <a href="<?php echo e(route("clients.edit", $client)); ?>" 
               class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                Editar Cliente
            </a>
            <?php endif; ?>
            
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check("create-services")): ?>
            <a href="<?php echo e(route("services.create")); ?>?client_id=<?php echo e($client->id); ?>" 
               class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                Nuevo Servicio
            </a>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make("layouts.app", array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/pest-controller/resources/views/clients/show.blade.php ENDPATH**/ ?>