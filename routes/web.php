<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Rutas públicas
Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        $user = Auth::user();
        if ($user->hasRole('super-admin')) {
            return redirect('/admin/dashboard');
        } else {
            return redirect('/technician/dashboard');
        }
    }

    return back()->withErrors([
        'email' => 'Las credenciales no coinciden.'
    ])->onlyInput('email');
})->name('login.post');

Route::post('/logout', function () {
    auth()->logout();
    return redirect('/login');
})->name('logout');

// Rutas comunes autenticadas
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        if (auth()->check()) {
            $user = auth()->user();
            return $user->hasRole('super-admin')
                ? redirect('/admin/dashboard')
                : redirect('/technician/dashboard');
        }
        return redirect('/login');
    });
    
    // Rutas de notificaciones globales (para todos los usuarios autenticados)
    Route::get('/notifications/count', [App\Http\Controllers\NotificationController::class, 'getUnreadCount'])->name('notifications.count');
    Route::get('/api/notifications/unread-count', [App\Http\Controllers\NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
    Route::get('/api/notifications/recent', [App\Http\Controllers\NotificationController::class, 'getRecentNotifications'])->name('notifications.recent');
});

// Rutas de admin
Route::middleware(['auth', 'role:super-admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/statistics', [App\Http\Controllers\DashboardController::class, 'statistics'])->name('statistics');
    
    // Servicios
    Route::resource('services', App\Http\Controllers\ServiceController::class);
    
    // Clientes
    Route::resource('clients', App\Http\Controllers\ClientController::class);
    
    // Productos
    Route::resource('products', App\Http\Controllers\ProductController::class);
    Route::patch('users/{user}/toggle-status', [App\Http\Controllers\UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::resource("users", App\Http\Controllers\UserController::class);
    
    // Roles y Permisos
    Route::get('/roles-permissions', [App\Http\Controllers\RolePermissionController::class, 'index'])->name('roles-permissions');
    Route::post('/roles-permissions', [App\Http\Controllers\RolePermissionController::class, 'store'])->name('roles-permissions.store');
    Route::put('/roles-permissions/{role}', [App\Http\Controllers\RolePermissionController::class, 'update'])->name('roles-permissions.update');
    Route::delete('/roles-permissions/{role}', [App\Http\Controllers\RolePermissionController::class, 'destroy'])->name('roles-permissions.destroy');
    Route::post('/roles-permissions/assign', [App\Http\Controllers\RolePermissionController::class, 'assignRole'])->name('roles-permissions.assign');
    Route::post('/permissions', [App\Http\Controllers\RolePermissionController::class, 'createPermission'])->name('permissions.store');
    Route::delete('/permissions/{permission}', [App\Http\Controllers\RolePermissionController::class, 'deletePermission'])->name('permissions.destroy');
    
    // Configuraciones
    Route::get('/notification-center', [App\Http\Controllers\NotificationController::class, 'index'])->name('notification-center');
    Route::resource('notifications', App\Http\Controllers\NotificationController::class)->except(['index']);
    Route::patch('/notifications/{notification}/mark-read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::patch('/notifications/{notification}/mark-unread', [App\Http\Controllers\NotificationController::class, 'markAsUnread'])->name('notifications.mark-unread');
    Route::patch('/notifications/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::get('/checklist-management', function() { return view('admin.checklist-management'); })->name('checklist-management');
    
    // Checklist Templates
    Route::resource('checklist-templates', App\Http\Controllers\ChecklistTemplateController::class);
    Route::post('/checklist-templates/{checklistTemplate}/duplicate', [App\Http\Controllers\ChecklistTemplateController::class, 'duplicate'])->name('checklist-templates.duplicate');
    
    // TODO: Arreglar estas rutas cuando el controlador ChecklistManagementController esté disponible
    /*
    Route::post("/checklist-management/templates", [App\Http\Controllers\ChecklistManagementController::class, "createTemplate"])->name("checklist-management.templates.create");
    Route::put("/checklist-management/templates/{template}", [App\Http\Controllers\ChecklistManagementController::class, "updateTemplate"])->name("checklist-management.templates.update");
    Route::delete("/checklist-management/templates/{template}", [App\Http\Controllers\ChecklistManagementController::class, "deleteTemplate"])->name("checklist-management.templates.delete");
    Route::patch("/checklist-management/templates/{template}/toggle", [App\Http\Controllers\ChecklistManagementController::class, "toggleTemplateStatus"])->name("checklist-management.templates.toggle");
    Route::post("/checklist-management/items", [App\Http\Controllers\ChecklistManagementController::class, "createItem"])->name("checklist-management.items.create");
    Route::put("/checklist-management/items/{item}", [App\Http\Controllers\ChecklistManagementController::class, "updateItem"])->name("checklist-management.items.update");
    Route::delete("/checklist-management/items/{item}", [App\Http\Controllers\ChecklistManagementController::class, "deleteItem"])->name("checklist-management.items.delete");
    */
    Route::resource('service-types', App\Http\Controllers\ServiceTypeController::class);
});

// Rutas de técnico
Route::middleware(['auth', 'role:technician'])->prefix('technician')->name('technician.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\TechnicianController::class, 'dashboard'])->name('dashboard');
    Route::get('/services', [App\Http\Controllers\TechnicianController::class, 'services'])->name('services');
    Route::get('/services/{service}/detail', [App\Http\Controllers\TechnicianController::class, 'showServiceDetail'])->name('service.detail');
    Route::get('/services/{service}/pdf', [App\Http\Controllers\TechnicianController::class, 'generatePDF'])->name('service.pdf');
    Route::get('/services/{service}/checklist-details', [App\Http\Controllers\TechnicianController::class, 'showChecklistDetails'])->name('service.checklist-details');
    Route::get('/profile', [App\Http\Controllers\TechnicianController::class, 'profile'])->name('profile');
    
    // Servicios del técnico
    Route::post('/services/{service}/start', [App\Http\Controllers\TechnicianController::class, 'startService'])->name('service.start');
    Route::post('/services/{service}/complete', [App\Http\Controllers\TechnicianController::class, 'completeService'])->name('service.complete');
    
    // Checklist
    Route::get('/services/{service}/checklist', [App\Http\Controllers\TechnicianController::class, 'showChecklist'])->name('service.checklist');
    Route::get('/services/{service}/checklist/location', [App\Http\Controllers\TechnicianController::class, 'showLocationCapture'])->name('service.checklist.location');
    Route::post('/services/{service}/checklist/location', [App\Http\Controllers\TechnicianController::class, 'captureLocation'])->name('service.checklist.location.post');
    Route::post('/services/{service}/checklist/process-location', [App\Http\Controllers\TechnicianController::class, 'processLocation'])->name('service.checklist.process-location');
    Route::post('/services/{service}/checklist/submit', [App\Http\Controllers\TechnicianController::class, 'saveChecklistStage'])->name('service.checklist.submit');
    Route::get('/services/{service}/checklist/{stage}', [App\Http\Controllers\TechnicianController::class, 'showChecklistStage'])->where('stage', 'points|products|results|observations|sites|description')->name('service.checklist.stage');
    
    // Notificaciones para técnicos
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{notification}/mark-read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::patch('/notifications/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
});

// Rutas adicionales autenticadas
Route::middleware(['auth'])->group(function () {
    Route::patch('products/{product}/update-stock', [App\Http\Controllers\ProductController::class, 'updateStock'])->name('products.update-stock');
});

