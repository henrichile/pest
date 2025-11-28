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
use App\Http\Requests\SendNotificationRequest;
use App\Http\Requests\UpdateNotificationSettingsRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show notification center (admin) or notifications for technicians.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        
        // Si es técnico (no super-admin), mostrar solo sus notificaciones
        if ($user->hasRole('technician') && !$user->hasRole('super-admin')) {
            return $this->dashboard();
        }
        
        // Get all notifications from all users (admin view)
        $query = DB::table('notifications');
        
        // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('data', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        if ($request->filled('read_status')) {
            if ($request->read_status === 'read') {
                $query->whereNotNull('read_at');
            } elseif ($request->read_status === 'unread') {
                $query->whereNull('read_at');
            }
        }
        
        if ($request->filled('start_date')) {
            $query->where('created_at', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->where('created_at', '<=', $request->end_date . ' 23:59:59');
        }
        
        // Get notifications with user info
        $notifications = $query->orderBy('created_at', 'desc')
            ->paginate(20);
        
        // Get statistics
        $totalNotifications = DB::table('notifications')->count();
        $unreadNotifications = DB::table('notifications')->whereNull('read_at')->count();
        $todayNotifications = DB::table('notifications')
            ->whereDate('created_at', Carbon::today())
            ->count();
        
        // Get notifications by type
        $notificationsByType = DB::table('notifications')
            ->selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type');
        
        // Get all users for sending notifications
        $users = User::orderBy('name')->get();
        
        return view('admin.notification-center', compact(
            'notifications',
            'totalNotifications',
            'unreadNotifications',
            'todayNotifications',
            'notificationsByType',
            'users'
        ));
    }

    /**
     * Show notifications dashboard.
     */
    public function dashboard(): View
    {
        $user = Auth::user();
        
        // Get user's notifications
        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        // Get notification statistics
        $totalNotifications = $user->notifications()->count();
        $unreadNotifications = $user->unreadNotifications()->count();
        $todayNotifications = $user->notifications()
            ->whereDate('created_at', Carbon::today())
            ->count();
        
        // Get notification types (usando consulta directa para evitar conflictos con GROUP BY)
        $notificationTypes = DB::table('notifications')
            ->where('notifiable_type', 'App\Models\User')
            ->where('notifiable_id', $user->id)
            ->selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->orderBy('count', 'desc')
            ->pluck('count', 'type');
        
        return view('notifications.dashboard', compact(
            'notifications',
            'totalNotifications',
            'unreadNotifications',
            'todayNotifications',
            'notificationTypes'
        ));
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(string $id): RedirectResponse
    {
        try {
            $user = Auth::user();
            
            // Si es admin, buscar la notificación directamente en la tabla
            if ($user->hasRole('super-admin')) {
                $notification = DB::table('notifications')->where('id', $id)->first();
                
                if ($notification) {
                    // Obtener el usuario al que pertenece la notificación
                    $notifiableType = $notification->notifiable_type;
                    $notifiableId = $notification->notifiable_id;
                    
                    if ($notifiableType === 'App\Models\User') {
                        $targetUser = User::find($notifiableId);
                        if ($targetUser) {
                            $userNotification = $targetUser->notifications()->find($id);
                            if ($userNotification) {
                                $userNotification->markAsRead();
                                
                                return redirect()->back()
                                    ->with('success', 'Notificación marcada como leída.');
                            }
                        }
                    }
                }
            } else {
                // Para técnicos, buscar solo en sus notificaciones
                $notification = $user->notifications()->find($id);
                
                if ($notification) {
                    $notification->markAsRead();
                    
                    return redirect()->back()
                        ->with('success', 'Notificación marcada como leída.');
                }
            }
            
            return redirect()->back()
                ->with('error', 'Notificación no encontrada.');
                
        } catch (\Exception $e) {
            Log::error('Error marking notification as read: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al marcar la notificación como leída.');
        }
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(): RedirectResponse
    {
        try {
            $user = Auth::user();
            
            // Si es admin, marcar todas las notificaciones no leídas de todos los usuarios
            if ($user->hasRole('super-admin')) {
                DB::table('notifications')
                    ->whereNull('read_at')
                    ->update(['read_at' => now()]);
                
                return redirect()->back()
                    ->with('success', 'Todas las notificaciones marcadas como leídas.');
            } else {
                // Para técnicos, marcar solo sus notificaciones
                $user->unreadNotifications()->update(['read_at' => now()]);
                
                return redirect()->back()
                    ->with('success', 'Todas las notificaciones marcadas como leídas.');
            }
                
        } catch (\Exception $e) {
            Log::error('Error marking all notifications as read: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al marcar todas las notificaciones como leídas.');
        }
    }

    /**
     * Delete notification.
     */
    public function delete(string $id): RedirectResponse
    {
        try {
            $user = Auth::user();
            $notification = $user->notifications()->find($id);
            
            if ($notification) {
                $notification->delete();
                
                return redirect()->back()
                    ->with('success', 'Notificación eliminada.');
            }
            
            return redirect()->back()
                ->with('error', 'Notificación no encontrada.');
                
        } catch (\Exception $e) {
            Log::error('Error deleting notification: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al eliminar la notificación.');
        }
    }

    /**
     * Delete all notifications.
     */
    public function deleteAll(): RedirectResponse
    {
        try {
            $user = Auth::user();
            $user->notifications()->delete();
            
            return redirect()->back()
                ->with('success', 'Todas las notificaciones eliminadas.');
                
        } catch (\Exception $e) {
            Log::error('Error deleting all notifications: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al eliminar todas las notificaciones.');
        }
    }

    /**
     * Show notification settings.
     */
    public function settings(): View
    {
        $user = Auth::user();
        
        // Get user's notification preferences
        $preferences = $user->notificationPreferences ?? [];
        
        return view('notifications.settings', compact('preferences'));
    }

    /**
     * Update notification settings.
     */
    public function updateSettings(UpdateNotificationSettingsRequest $request): RedirectResponse
    {
        try {
            $user = Auth::user();
            
            $preferences = [
                'email_notifications' => $request->boolean('email_notifications'),
                'push_notifications' => $request->boolean('push_notifications'),
                'sms_notifications' => $request->boolean('sms_notifications'),
                'work_order_assigned' => $request->boolean('work_order_assigned'),
                'work_order_completed' => $request->boolean('work_order_completed'),
                'work_order_cancelled' => $request->boolean('work_order_cancelled'),
                'checklist_pending' => $request->boolean('checklist_pending'),
                'checklist_completed' => $request->boolean('checklist_completed'),
                'nonconformity_reported' => $request->boolean('nonconformity_reported'),
                'nonconformity_resolved' => $request->boolean('nonconformity_resolved'),
                'material_low_stock' => $request->boolean('material_low_stock'),
                'system_maintenance' => $request->boolean('system_maintenance'),
                'daily_summary' => $request->boolean('daily_summary'),
                'weekly_summary' => $request->boolean('weekly_summary'),
                'monthly_summary' => $request->boolean('monthly_summary'),
            ];
            
            $user->update(['notification_preferences' => $preferences]);
            
            // Log activity
            activity()
                ->performedOn($user)
                ->causedBy($user)
                ->log('Configuración de notificaciones actualizada');
            
            return redirect()->back()
                ->with('success', 'Configuración de notificaciones actualizada correctamente.');
                
        } catch (\Exception $e) {
            Log::error('Error updating notification settings: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al actualizar la configuración de notificaciones.');
        }
    }

    /**
     * Send notification to specific users.
     */
    public function sendNotification(Request $request): RedirectResponse
    {
        try {
            $request->validate([
                'user_ids' => 'required|array|min:1',
                'user_ids.*' => 'exists:users,id',
                'title' => 'required|string|max:255',
                'message' => 'required|string',
                'type' => 'nullable|in:info,success,warning,error',
                'url' => 'nullable|url',
            ]);

            $notificationService = app(NotificationService::class);
            
            $users = User::whereIn('id', $request->user_ids)->get();
            
            foreach ($users as $user) {
                $notificationService->sendCustomNotification(
                    $user,
                    $request->title,
                    $request->message,
                    $request->type ?? 'info',
                    $request->url ?? null
                );
            }
            
            // Log activity
            activity()
                ->causedBy(Auth::user())
                ->log('Notificación personalizada enviada');
            
            return redirect()->route('admin.notification-center')
                ->with('success', 'Notificación enviada correctamente a ' . count($users) . ' usuario(s).');
                
        } catch (\Exception $e) {
            Log::error('Error sending notification: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Error al enviar la notificación: ' . $e->getMessage()]);
        }
    }

    /**
     * Show notification templates.
     */
    public function templates(): View
    {
        $templates = [
            'work_order_assigned' => [
                'title' => 'Nueva Orden de Trabajo Asignada',
                'message' => 'Se te ha asignado una nueva orden de trabajo: {work_order_folio}',
                'type' => 'info'
            ],
            'work_order_completed' => [
                'title' => 'Orden de Trabajo Completada',
                'message' => 'La orden de trabajo {work_order_folio} ha sido completada',
                'type' => 'success'
            ],
            'work_order_cancelled' => [
                'title' => 'Orden de Trabajo Cancelada',
                'message' => 'La orden de trabajo {work_order_folio} ha sido cancelada',
                'type' => 'warning'
            ],
            'checklist_pending' => [
                'title' => 'Checklist Pendiente',
                'message' => 'Tienes un checklist pendiente para la orden de trabajo {work_order_folio}',
                'type' => 'warning'
            ],
            'checklist_completed' => [
                'title' => 'Checklist Completado',
                'message' => 'El checklist para la orden de trabajo {work_order_folio} ha sido completado',
                'type' => 'success'
            ],
            'nonconformity_reported' => [
                'title' => 'No Conformidad Reportada',
                'message' => 'Se ha reportado una no conformidad en la orden de trabajo {work_order_folio}',
                'type' => 'error'
            ],
            'nonconformity_resolved' => [
                'title' => 'No Conformidad Resuelta',
                'message' => 'La no conformidad en la orden de trabajo {work_order_folio} ha sido resuelta',
                'type' => 'success'
            ],
            'material_low_stock' => [
                'title' => 'Stock Bajo de Material',
                'message' => 'El material {material_name} tiene stock bajo',
                'type' => 'warning'
            ],
            'system_maintenance' => [
                'title' => 'Mantenimiento del Sistema',
                'message' => 'El sistema estará en mantenimiento el {maintenance_date}',
                'type' => 'info'
            ],
            'daily_summary' => [
                'title' => 'Resumen Diario',
                'message' => 'Resumen de actividades del día: {summary}',
                'type' => 'info'
            ],
            'weekly_summary' => [
                'title' => 'Resumen Semanal',
                'message' => 'Resumen de actividades de la semana: {summary}',
                'type' => 'info'
            ],
            'monthly_summary' => [
                'title' => 'Resumen Mensual',
                'message' => 'Resumen de actividades del mes: {summary}',
                'type' => 'info'
            ]
        ];
        
        return view('notifications.templates', compact('templates'));
    }

    /**
     * Show notification history.
     */
    public function history(Request $request): View
    {
        $user = Auth::user();
        
        $query = $user->notifications();
        
        // Filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        if ($request->filled('read_status')) {
            if ($request->read_status === 'read') {
                $query->whereNotNull('read_at');
            } elseif ($request->read_status === 'unread') {
                $query->whereNull('read_at');
            }
        }
        
        if ($request->filled('start_date')) {
            $query->where('created_at', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->where('created_at', '<=', $request->end_date);
        }
        
        $notifications = $query->orderBy('created_at', 'desc')
            ->paginate(50);
        
        return view('notifications.history', compact('notifications'));
    }

    /**
     * Show notification statistics.
     */
    public function statistics(Request $request): View
    {
        $user = Auth::user();
        
        $query = $user->notifications();
        
        // Filter by date range
        if ($request->filled('start_date')) {
            $query->where('created_at', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->where('created_at', '<=', $request->end_date);
        }
        
        // Get statistics
        $totalNotifications = $query->count();
        $readNotifications = $query->whereNotNull('read_at')->count();
        $unreadNotifications = $query->whereNull('read_at')->count();
        
        // Get notifications by type
        $notificationsByType = $query->selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type');
        
        // Get notifications by day
        $notificationsByDay = $query->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Get notifications by hour
        $notificationsByHour = $query->selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();
        
        return view('notifications.statistics', compact(
            'totalNotifications',
            'readNotifications',
            'unreadNotifications',
            'notificationsByType',
            'notificationsByDay',
            'notificationsByHour'
        ));
    }

    /**
     * Test notification.
     */
    public function testNotification(): RedirectResponse
    {
        try {
            $user = Auth::user();
            $notificationService = app(NotificationService::class);
            
            $notificationService->sendCustomNotification(
                $user,
                'Notificación de Prueba',
                'Esta es una notificación de prueba para verificar que el sistema funciona correctamente.',
                'info'
            );
            
            return redirect()->back()
                ->with('success', 'Notificación de prueba enviada correctamente.');
                
        } catch (\Exception $e) {
            Log::error('Error sending test notification: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al enviar la notificación de prueba.');
        }
    }

    /**
     * Get unread notifications count (AJAX).
     */
    public function getUnreadCount(): \Illuminate\Http\JsonResponse
    {
        try {
            $user = Auth::user();
            $count = $user->unreadNotifications()->count();
            
            return response()->json(['count' => $count]);
            
        } catch (\Exception $e) {
            Log::error('Error getting unread notifications count: ' . $e->getMessage());
            
            return response()->json(['count' => 0]);
        }
    }

    /**
     * Get recent notifications (AJAX).
     */
    public function getRecent(): \Illuminate\Http\JsonResponse
    {
        try {
            $user = Auth::user();
            $notifications = $user->notifications()
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($notification) {
                    return [
                        'id' => $notification->id,
                        'type' => $notification->type,
                        'title' => $notification->data['title'] ?? 'Notificación',
                        'message' => $notification->data['message'] ?? '',
                        'read_at' => $notification->read_at,
                        'created_at' => $notification->created_at->diffForHumans(),
                    ];
                });
            
            return response()->json($notifications);
            
        } catch (\Exception $e) {
            Log::error('Error getting recent notifications: ' . $e->getMessage());
            
            return response()->json([]);
        }
    }

    /**
     * Mark notification as read (AJAX).
     */
    public function markAsReadAjax(string $id): \Illuminate\Http\JsonResponse
    {
        try {
            $user = Auth::user();
            $notification = $user->notifications()->find($id);
            
            if ($notification) {
                $notification->markAsRead();
                
                return response()->json(['success' => true]);
            }
            
            return response()->json(['success' => false, 'message' => 'Notificación no encontrada']);
            
        } catch (\Exception $e) {
            Log::error('Error marking notification as read: ' . $e->getMessage());
            
            return response()->json(['success' => false, 'message' => 'Error al marcar la notificación como leída']);
        }
    }
}
