<?php $__env->startSection('title', 'Perfil'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Mi Perfil</h1>
        
        <div class="bg-white shadow rounded-lg p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nombre</label>
                    <p class="mt-1 text-sm text-gray-900"><?php echo e($user->name); ?></p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <p class="mt-1 text-sm text-gray-900"><?php echo e($user->email); ?></p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Rol</label>
                    <p class="mt-1 text-sm text-gray-900"><?php echo e($user->roles->first()->name ?? 'Sin rol asignado'); ?></p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Fecha de registro</label>
                    <p class="mt-1 text-sm text-gray-900"><?php echo e($user->created_at->format('d/m/Y')); ?></p>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end">
                <a href="<?php echo e(route('technician.dashboard')); ?>" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Volver al Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/pest-controller/resources/views/technician/profile.blade.php ENDPATH**/ ?>