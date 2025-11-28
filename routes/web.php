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
    
    // Perfil de usuario (para todos los usuarios autenticados)
    Route::get('/profile', [App\Http\Controllers\UserController::class, 'profile'])->name('profile');
    Route::post('/profile', [App\Http\Controllers\UserController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/change-password', [App\Http\Controllers\UserController::class, 'changePassword'])->name('profile.change-password');
    
    // Rutas de notificaciones globales (para todos los usuarios autenticados)
    Route::get('/notifications/count', [App\Http\Controllers\NotificationController::class, 'getUnreadCount'])->name('notifications.count');
    Route::get('/api/notifications/unread-count', [App\Http\Controllers\NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
    Route::get('/api/notifications/recent', [App\Http\Controllers\NotificationController::class, 'getRecentNotifications'])->name('notifications.recent');
});

// Rutas de admin
Route::middleware(['auth', 'role:super-admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/statistics', [App\Http\Controllers\DashboardController::class, 'statistics'])->name('statistics');
    
    // Búsqueda global
    Route::get('/search', [App\Http\Controllers\SearchController::class, 'search'])->name('search');
    
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
    Route::get('/settings', [App\Http\Controllers\ConfigurationController::class, 'settings'])->name('settings');
    Route::put('/settings/update', [App\Http\Controllers\ConfigurationController::class, 'updateCompanySettings'])->name('settings.update');
    Route::put('/settings/smtp', [App\Http\Controllers\ConfigurationController::class, 'updateSmtpSettings'])->name('settings.smtp');
    
    // Ver como Técnico - Rutas que muestran la vista de técnico para super-admins
    Route::post('/view-as-technician', [App\Http\Controllers\AdminController::class, 'viewAsTechnician'])->name('view-as-technician');
    Route::post('/stop-viewing-as-technician', [App\Http\Controllers\AdminController::class, 'stopViewingAsTechnician'])->name('stop-viewing-as-technician');
    
    // Rutas de vista de técnico para super-admins (sin requerir rol de técnico)
    Route::get('/technician-view/dashboard', [App\Http\Controllers\TechnicianController::class, 'dashboard'])->name('technician-view.dashboard');
    Route::get('/technician-view/services', [App\Http\Controllers\TechnicianController::class, 'services'])->name('technician-view.services');
    Route::get('/technician-view/services/{service}/detail', [App\Http\Controllers\TechnicianController::class, 'showServiceDetail'])->name('technician-view.service.detail');
    Route::get('/technician-view/services/{service}/pdf', [App\Http\Controllers\TechnicianController::class, 'generatePDF'])->name('technician-view.service.pdf');
    Route::get('/technician-view/services/{service}/checklist-details', [App\Http\Controllers\TechnicianController::class, 'showChecklistDetails'])->name('technician-view.service.checklist-details');
    Route::get('/technician-view/profile', [App\Http\Controllers\TechnicianController::class, 'profile'])->name('technician-view.profile');
    
    // Servicios del técnico (solo lectura para admin en modo view_as_technician)
    Route::match(['GET', 'POST'], '/technician-view/services/{service}/start', [App\Http\Controllers\TechnicianController::class, 'startService'])->name('technician-view.service.start');
    Route::post('/technician-view/services/{service}/complete', [App\Http\Controllers\TechnicianController::class, 'completeService'])->name('technician-view.service.complete');
    Route::get('/technician-view/services/{service}/checklist', [App\Http\Controllers\TechnicianController::class, 'showChecklist'])->name('technician-view.service.checklist');
    Route::get('/technician-view/services/{service}/checklist/{stage}', [App\Http\Controllers\TechnicianController::class, 'showChecklistStage'])->where('stage', 'points|products|results|observations|sites|description|monitoreo-datos|monitoreo-croquis|monitoreo-completo|monitoreo-estadisticas|monitoreo-analisis|monitoreo-firma')->name('technician-view.service.checklist.stage');
    Route::get('/technician-view/services/{service}/checklist/location', [App\Http\Controllers\TechnicianController::class, 'showLocationCapture'])->name('technician-view.service.checklist.location');
    Route::post('/technician-view/services/{service}/checklist/location', [App\Http\Controllers\TechnicianController::class, 'captureLocation'])->name('technician-view.service.checklist.location.post');
    Route::post('/technician-view/services/{service}/checklist/process-location', [App\Http\Controllers\TechnicianController::class, 'processLocation'])->name('technician-view.service.checklist.process-location');
    Route::post('/technician-view/services/{service}/checklist/submit', [App\Http\Controllers\TechnicianController::class, 'saveChecklistStage'])->name('technician-view.service.checklist.submit');
    Route::get('/notification-center', [App\Http\Controllers\NotificationController::class, 'index'])->name('notification-center');
    Route::post('/notifications/send', [App\Http\Controllers\NotificationController::class, 'sendNotification'])->name('notifications.send');
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
    
    // Plagas
    Route::get('/plagas', [App\Http\Controllers\AdminController::class, 'pests'])->name('pests');
    Route::get('/plagas/create', [App\Http\Controllers\AdminController::class, 'createPest'])->name('pests.create');
    Route::post('/plagas', [App\Http\Controllers\AdminController::class, 'storePest'])->name('pests.store');
    
    // Reportes
    Route::get('/reports', [App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [App\Http\Controllers\ReportController::class, 'export'])->name('reports.export');
    Route::get('/reports/scheduled', [App\Http\Controllers\ReportController::class, 'scheduled'])->name('reports.scheduled');
    Route::get('/reports/scheduled/create', [App\Http\Controllers\ReportController::class, 'createScheduled'])->name('reports.scheduled.create');
    Route::post('/reports/scheduled', [App\Http\Controllers\ReportController::class, 'storeScheduled'])->name('reports.scheduled.store');
    Route::get('/reports/scheduled/{scheduledReport}/edit', [App\Http\Controllers\ReportController::class, 'editScheduled'])->name('reports.scheduled.edit');
    Route::put('/reports/scheduled/{scheduledReport}', [App\Http\Controllers\ReportController::class, 'updateScheduled'])->name('reports.scheduled.update');
    Route::delete('/reports/scheduled/{scheduledReport}', [App\Http\Controllers\ReportController::class, 'destroyScheduled'])->name('reports.scheduled.destroy');
    Route::patch('/reports/scheduled/{scheduledReport}/toggle', [App\Http\Controllers\ReportController::class, 'toggleScheduled'])->name('reports.scheduled.toggle');
    Route::get('/reports/config', [App\Http\Controllers\ReportController::class, 'config'])->name('reports.config');
});

// Rutas de técnico
Route::middleware(['auth', \App\Http\Middleware\RedirectTechnicianRoutes::class, 'role:technician'])->prefix('technician')->name('technician.')->group(function () {
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
    Route::get('/services/{service}/checklist/{stage}', [App\Http\Controllers\TechnicianController::class, 'showChecklistStage'])->where('stage', 'points|products|results|observations|sites|description|monitoreo-datos|monitoreo-croquis|monitoreo-completo|monitoreo-estadisticas|monitoreo-analisis|monitoreo-firma')->name('service.checklist.stage');
    Route::get('/services/{service}/checklist/observations/{index}', [App\Http\Controllers\TechnicianController::class, 'handleObservation'])->name('service.checklist.observation.handle');
    Route::delete('/services/{service}/checklist/observations/{index}', [App\Http\Controllers\TechnicianController::class, 'deleteObservation'])->name('service.checklist.observation.delete');
    Route::post('/services/{service}/checklist/observations/{index}', [App\Http\Controllers\TechnicianController::class, 'updateObservation'])->name('service.checklist.observation.update');
    Route::get('/services/{service}/checklist/observations/{index}/edit', [App\Http\Controllers\TechnicianController::class, 'editObservation'])->name('service.checklist.observation.edit');
    
    // Notificaciones para técnicos
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{notification}/mark-read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::patch('/notifications/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
});

// Rutas adicionales autenticadas
Route::middleware(['auth'])->group(function () {
    Route::patch('products/{product}/update-stock', [App\Http\Controllers\ProductController::class, 'updateStock'])->name('products.update-stock');
});

