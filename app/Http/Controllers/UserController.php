<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;
use App\Models\WorkSession;
use App\Models\MaterialMovement;
use App\Models\ChecklistResponse;
use App\Models\Nonconformity;
use App\Models\Treatment;
use App\Models\Material;
use App\Models\Pest;
use App\Models\ChecklistTemplate;
use App\Models\ChecklistItem;
use App\Models\Site;
use App\Models\Client;
use App\Models\Service;
use App\Models\WorkOrderAssignment;
use App\Models\User;
use App\Services\PdfService;
use App\Services\NotificationService;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\AssignRoleRequest;
use App\Http\Requests\UpdateUserSettingsRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show user profile.
     */
    public function profile(): View
    {
        $user = Auth::user();
        
        // Get user statistics (with error handling)
        $totalWorkOrders = 0;
        $completedWorkOrders = 0;
        
        try {
            $totalWorkOrders = WorkOrder::whereHas('assignedTechnicians', function ($query) use ($user) {
                $query->where('technician_id', $user->id);
            })->count();
            
            try {
                $completedWorkOrders = WorkOrder::whereHas('assignedTechnicians', function ($query) use ($user) {
                    $query->where('technician_id', $user->id);
                })->where('status', 'completed')->count();
            } catch (\Exception $e) {
                $completedWorkOrders = 0;
            }
        } catch (\Exception $e) {
            // If work orders don't exist, set to 0
        }
        
        $totalSessions = 0;
        $totalTreatments = 0;
        
        try {
            $totalSessions = WorkSession::where('technician_id', $user->id)->count();
        } catch (\Exception $e) {
            $totalSessions = 0;
        }
        
        try {
            $totalTreatments = Treatment::where('technician_id', $user->id)->count();
        } catch (\Exception $e) {
            $totalTreatments = 0;
        }
        
        $stats = [
            'total_work_orders' => $totalWorkOrders,
            'completed_work_orders' => $completedWorkOrders,
            'total_sessions' => $totalSessions,
            'total_treatments' => $totalTreatments,
        ];
        
        // Get recent activities
        $recentActivities = collect();
        try {
            $recentActivities = \Spatie\Activitylog\Models\Activity::where('causer_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
        } catch (\Exception $e) {
            // If activity log doesn't work, use empty collection
        }
        
        // Get user roles
        $roles = collect();
        try {
            $roles = $user->roles;
        } catch (\Exception $e) {
            // If roles don't exist, use empty collection
        }
        
        // Get user permissions
        $permissions = collect();
        try {
            $permissions = $user->getAllPermissions();
        } catch (\Exception $e) {
            // If permissions don't exist, use empty collection
        }
        
        return view('user.profile', compact('user', 'stats', 'recentActivities', 'roles', 'permissions'));
    }

    /**
     * Update user profile.
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email,' . Auth::id(),
                'phone' => 'nullable|string|max:20',
                'timezone' => 'nullable|string|max:100',
            ]);
            
            DB::beginTransaction();
            
            $user = Auth::user();
            $user->update($request->only(['name', 'email', 'phone', 'timezone']));
            
            // Log activity
            try {
                activity()
                    ->performedOn($user)
                    ->causedBy($user)
                    ->log('Perfil de usuario actualizado');
            } catch (\Exception $e) {
                // If activity log fails, continue anyway
            }
            
            DB::commit();
            
            return redirect()->back()
                ->with('success', 'Perfil actualizado correctamente.');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating user profile: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al actualizar el perfil.');
        }
    }

    /**
     * Change password.
     */
    public function changePassword(Request $request): RedirectResponse
    {
        try {
            $request->validate([
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:8|confirmed',
            ]);
            
            DB::beginTransaction();
            
            $user = Auth::user();
            
            // Verify current password
            if (!Hash::check($request->current_password, $user->password)) {
                return redirect()->back()
                    ->with('error', 'La contraseña actual es incorrecta.');
            }
            
            // Update password
            $user->update([
                'password' => Hash::make($request->new_password)
            ]);
            
            // Log activity
            try {
                activity()
                    ->performedOn($user)
                    ->causedBy($user)
                    ->log('Contraseña cambiada');
            } catch (\Exception $e) {
                // If activity log fails, continue anyway
            }
            
            DB::commit();
            
            return redirect()->back()
                ->with('success', 'Contraseña cambiada correctamente.');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error changing password: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al cambiar la contraseña.');
        }
    }

    /**
     * Show user settings.
     */
    public function settings(): View
    {
        $user = Auth::user();
        
        // Get user preferences
        $preferences = $user->preferences ?? [];
        
        // Get notification preferences
        $notificationPreferences = $user->notification_preferences ?? [];
        
        return view('user.settings', compact('user', 'preferences', 'notificationPreferences'));
    }

    /**
     * Update user settings.
     */
    public function updateSettings(UpdateUserSettingsRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            
            $user = Auth::user();
            
            $settings = $request->validated();
            
            // Update user preferences
            if (isset($settings['preferences'])) {
                $user->update(['preferences' => $settings['preferences']]);
            }
            
            // Update notification preferences
            if (isset($settings['notification_preferences'])) {
                $user->update(['notification_preferences' => $settings['notification_preferences']]);
            }
            
            // Log activity
            activity()
                ->performedOn($user)
                ->causedBy($user)
                ->log('Configuración de usuario actualizada');
            
            DB::commit();
            
            return redirect()->back()
                ->with('success', 'Configuración actualizada correctamente.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating user settings: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al actualizar la configuración.');
        }
    }

    /**
     * Show users management (admin only).
     */
    public function index(Request $request): View
    {
        // Verificar que el usuario sea super-admin (ya verificado por middleware, pero por seguridad)
        if (!auth()->user()->hasRole('super-admin')) {
            abort(403, 'No tienes permisos para acceder a esta página');
        }
        
        $query = User::with(['roles']);
        
        // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('role')) {
            $query->role($request->role);
        }
        
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }
        
        $users = $query->orderBy('name')
            ->paginate(20);
        
        $roles = \Spatie\Permission\Models\Role::all();
        
        return view('user.index', compact('users', 'roles'));
    }

    /**
     * Show user details (admin only).
     */
    public function show(User $user): View
    {
        // Verificar que el usuario sea super-admin
        if (!auth()->user()->hasRole('super-admin')) {
            abort(403, 'No tienes permisos para acceder a esta página');
        }
        
        $user->load(['roles', 'permissions']);
        
        // Get user statistics
        $stats = [
            'total_work_orders' => WorkOrder::whereHas('assignedTechnicians', function ($query) use ($user) {
                $query->where('technician_id', $user->id);
            })->count(),
            'completed_work_orders' => WorkOrder::whereHas('assignedTechnicians', function ($query) use ($user) {
                $query->where('technician_id', $user->id);
            })->where('status', 'completed')->count(),
            'total_sessions' => WorkSession::where('technician_id', $user->id)->count(),
            'total_treatments' => Treatment::where('technician_id', $user->id)->count(),
        ];
        
        // Get recent activities
        $recentActivities = \Spatie\Activitylog\Models\Activity::where('causer_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();
        
        // Get work sessions
        $workSessions = WorkSession::where('technician_id', $user->id)
            ->with(['workOrder.client', 'workOrder.site'])
            ->orderBy('start_time', 'desc')
            ->limit(10)
            ->get();
        
        return view('user.show', compact('user', 'stats', 'recentActivities', 'workSessions'));
    }

    /**
     * Create new user (admin only).
     */
    public function create(): View
    {
        // Verificar que el usuario sea super-admin
        if (!auth()->user()->hasRole('super-admin')) {
            abort(403, 'No tienes permisos para acceder a esta página');
        }
        
        $roles = \Spatie\Permission\Models\Role::all();
        $permissions = \Spatie\Permission\Models\Permission::all();
        
        return view('user.create', compact('roles', 'permissions'));
    }

    /**
     * Store new user (admin only).
     */
    public function store(CreateUserRequest $request): RedirectResponse
    {
        // Verificar que el usuario sea super-admin
        if (!auth()->user()->hasRole('super-admin')) {
            abort(403, 'No tienes permisos para acceder a esta página');
        }
        
        try {
            DB::beginTransaction();
            
            $userData = $request->validated();
            $userData['password'] = Hash::make($request->password);
            
            // Convertir is_active a booleano si viene como string
            if (isset($userData['is_active'])) {
                $userData['is_active'] = filter_var($userData['is_active'], FILTER_VALIDATE_BOOLEAN);
            } else {
                $userData['is_active'] = true; // Por defecto activo
            }
            
            $user = User::create($userData);
            
            // Assign roles
            if ($request->filled('roles')) {
                $user->assignRole($request->roles);
            }
            
            // Assign permissions
            if ($request->filled('permissions')) {
                $user->givePermissionTo($request->permissions);
            }
            
            // Log activity
            activity()
                ->performedOn($user)
                ->causedBy(Auth::user())
                ->log('Usuario creado');
            
            DB::commit();
            
            return redirect()->route('admin.users.show', $user)
                ->with('success', 'Usuario creado correctamente.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating user: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al crear el usuario.');
        }
    }

    /**
     * Edit user (admin only).
     */
    public function edit(User $user): View
    {
        // Verificar que el usuario sea super-admin
        if (!auth()->user()->hasRole('super-admin')) {
            abort(403, 'No tienes permisos para acceder a esta página');
        }
        
        $roles = \Spatie\Permission\Models\Role::all();
        $permissions = \Spatie\Permission\Models\Permission::all();
        
        return view('user.edit', compact('user', 'roles', 'permissions'));
    }

    /**
     * Update user (admin only).
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        // Verificar que el usuario sea super-admin
        if (!auth()->user()->hasRole('super-admin')) {
            abort(403, 'No tienes permisos para acceder a esta página');
        }
        
        try {
            DB::beginTransaction();
            
            $userData = $request->validated();
            
            // Update password if provided
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }
            
            $user->update($userData);
            
            // Update roles
            if ($request->filled('roles')) {
                $user->syncRoles($request->roles);
            }
            
            // Update permissions
            if ($request->filled('permissions')) {
                $user->syncPermissions($request->permissions);
            }
            
            // Log activity
            activity()
                ->performedOn($user)
                ->causedBy(Auth::user())
                ->log('Usuario actualizado');
            
            DB::commit();
            
            return redirect()->route('admin.users.show', $user)
                ->with('success', 'Usuario actualizado correctamente.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating user: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al actualizar el usuario.');
        }
    }

    /**
     * Delete user (admin only).
     */
    public function destroy(User $user): RedirectResponse
    {
        // Verificar que el usuario sea super-admin
        if (!auth()->user()->hasRole('super-admin')) {
            abort(403, 'No tienes permisos para acceder a esta página');
        }
        
        try {
            DB::beginTransaction();
            
            // Prevent deletion of own account
            if ($user->id === Auth::id()) {
                return redirect()->back()
                    ->with('error', 'No puedes eliminar tu propia cuenta.');
            }
            
            // Log activity
            activity()
                ->causedBy(Auth::user())
                ->log('Usuario eliminado: ' . $user->name);
            
            $user->delete();
            
            DB::commit();
            
            return redirect()->route('admin.users.index')
                ->with('success', 'Usuario eliminado correctamente.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting user: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al eliminar el usuario.');
        }
    }

    /**
     * Assign role to user (admin only).
     */
    public function assignRole(AssignRoleRequest $request, User $user): RedirectResponse
    {
        // Verificar que el usuario sea super-admin
        if (!auth()->user()->hasRole('super-admin')) {
            abort(403, 'No tienes permisos para acceder a esta página');
        }
        
        try {
            DB::beginTransaction();
            
            $user->assignRole($request->role);
            
            // Log activity
            activity()
                ->performedOn($user)
                ->causedBy(Auth::user())
                ->log('Rol asignado: ' . $request->role);
            
            DB::commit();
            
            return redirect()->back()
                ->with('success', 'Rol asignado correctamente.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error assigning role: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al asignar el rol.');
        }
    }

    /**
     * Remove role from user (admin only).
     */
    public function removeRole(User $user, string $role): RedirectResponse
    {
        // Verificar que el usuario sea super-admin
        if (!auth()->user()->hasRole('super-admin')) {
            abort(403, 'No tienes permisos para acceder a esta página');
        }
        
        try {
            DB::beginTransaction();
            
            $user->removeRole($role);
            
            // Log activity
            activity()
                ->performedOn($user)
                ->causedBy(Auth::user())
                ->log('Rol removido: ' . $role);
            
            DB::commit();
            
            return redirect()->back()
                ->with('success', 'Rol removido correctamente.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error removing role: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al remover el rol.');
        }
    }

    /**
     * Toggle user active status (admin only).
     */
    public function toggleActive(User $user): RedirectResponse
    {
        // Verificar que el usuario sea super-admin
        if (!auth()->user()->hasRole('super-admin')) {
            abort(403, 'No tienes permisos para acceder a esta página');
        }
        
        try {
            DB::beginTransaction();
            
            // Prevent deactivating own account
            if ($user->id === Auth::id()) {
                return redirect()->back()
                    ->with('error', 'No puedes desactivar tu propia cuenta.');
            }
            
            $user->update(['is_active' => !$user->is_active]);
            
            // Log activity
            activity()
                ->performedOn($user)
                ->causedBy(Auth::user())
                ->log('Estado de usuario cambiado: ' . ($user->is_active ? 'Activo' : 'Inactivo'));
            
            DB::commit();
            
            return redirect()->back()
                ->with('success', 'Estado de usuario actualizado correctamente.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error toggling user active status: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al actualizar el estado del usuario.');
        }
    }

    /**
     * Show user activity logs (admin only).
     */
    public function activityLogs(Request $request, User $user): View
    {
        // Verificar que el usuario sea super-admin
        if (!auth()->user()->hasRole('super-admin')) {
            abort(403, 'No tienes permisos para acceder a esta página');
        }
        
        $query = \Spatie\Activitylog\Models\Activity::where('causer_id', $user->id)
            ->with(['subject']);
        
        // Filters
        if ($request->filled('log_name')) {
            $query->where('log_name', $request->log_name);
        }
        
        if ($request->filled('start_date')) {
            $query->where('created_at', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->where('created_at', '<=', $request->end_date);
        }
        
        $activities = $query->orderBy('created_at', 'desc')
            ->paginate(50);
        
        $logNames = \Spatie\Activitylog\Models\Activity::where('causer_id', $user->id)
            ->select('log_name')
            ->distinct()
            ->pluck('log_name');
        
        return view('user.activity-logs', compact('user', 'activities', 'logNames'));
    }

    /**
     * Show user work sessions (admin only).
     */
    public function workSessions(Request $request, User $user): View
    {
        // Verificar que el usuario sea super-admin
        if (!auth()->user()->hasRole('super-admin')) {
            abort(403, 'No tienes permisos para acceder a esta página');
        }
        
        $query = WorkSession::where('technician_id', $user->id)
            ->with(['workOrder.client', 'workOrder.site', 'workOrder.service']);
        
        // Filters
        if ($request->filled('start_date')) {
            $query->where('start_time', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->where('start_time', '<=', $request->end_date);
        }
        
        $sessions = $query->orderBy('start_time', 'desc')
            ->paginate(20);
        
        return view('user.work-sessions', compact('user', 'sessions'));
    }

    /**
     * Show user work orders (admin only).
     */
    public function workOrders(Request $request, User $user): View
    {
        // Verificar que el usuario sea super-admin
        if (!auth()->user()->hasRole('super-admin')) {
            abort(403, 'No tienes permisos para acceder a esta página');
        }
        
        $query = WorkOrder::whereHas('assignedTechnicians', function ($q) use ($user) {
            $q->where('technician_id', $user->id);
        })->with(['client', 'site', 'service']);
        
        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('start_date')) {
            $query->where('scheduled_date', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->where('scheduled_date', '<=', $request->end_date);
        }
        
        $workOrders = $query->orderBy('scheduled_date', 'desc')
            ->paginate(20);
        
        return view('user.work-orders', compact('user', 'workOrders'));
    }

    /**
     * Show user treatments (admin only).
     */
    public function treatments(Request $request, User $user): View
    {
        // Verificar que el usuario sea super-admin
        if (!auth()->user()->hasRole('super-admin')) {
            abort(403, 'No tienes permisos para acceder a esta página');
        }
        
        $query = Treatment::where('technician_id', $user->id)
            ->with(['pest', 'material', 'workOrder.client', 'workOrder.site']);
        
        // Filters
        if ($request->filled('pest_id')) {
            $query->where('pest_id', $request->pest_id);
        }
        
        if ($request->filled('material_id')) {
            $query->where('material_id', $request->material_id);
        }
        
        if ($request->filled('start_date')) {
            $query->where('applied_at', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->where('applied_at', '<=', $request->end_date);
        }
        
        $treatments = $query->orderBy('applied_at', 'desc')
            ->paginate(20);
        
        $pests = Pest::where('is_active', true)->get();
        $materials = Material::where('is_active', true)->get();
        
        return view('user.treatments', compact('user', 'treatments', 'pests', 'materials'));
    }

    /**
     * Show user checklist responses (admin only).
     */
    public function checklistResponses(Request $request, User $user): View
    {
        // Verificar que el usuario sea super-admin
        if (!auth()->user()->hasRole('super-admin')) {
            abort(403, 'No tienes permisos para acceder a esta página');
        }
        
        $query = ChecklistResponse::where('technician_id', $user->id)
            ->with(['checklistTemplate', 'workOrder.client', 'workOrder.site']);
        
        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('checklist_template_id')) {
            $query->where('checklist_template_id', $request->checklist_template_id);
        }
        
        if ($request->filled('start_date')) {
            $query->where('submitted_at', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->where('submitted_at', '<=', $request->end_date);
        }
        
        $responses = $query->orderBy('submitted_at', 'desc')
            ->paginate(20);
        
        $checklistTemplates = ChecklistTemplate::where('is_active', true)->get();
        
        return view('user.checklist-responses', compact('user', 'responses', 'checklistTemplates'));
    }

    /**
     * Show user nonconformities (admin only).
     */
    public function nonconformities(Request $request, User $user): View
    {
        // Verificar que el usuario sea super-admin
        if (!auth()->user()->hasRole('super-admin')) {
            abort(403, 'No tienes permisos para acceder a esta página');
        }
        
        $query = Nonconformity::where('technician_id', $user->id)
            ->with(['workOrder.client', 'workOrder.site']);
        
        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }
        
        if ($request->filled('start_date')) {
            $query->where('reported_at', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->where('reported_at', '<=', $request->end_date);
        }
        
        $nonconformities = $query->orderBy('reported_at', 'desc')
            ->paginate(20);
        
        return view('user.nonconformities', compact('user', 'nonconformities'));
    }
}
