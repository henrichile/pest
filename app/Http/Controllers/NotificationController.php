<?php

namespace App\Http\Controllers;

use App\Models\SystemNotification;
use App\Models\User;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $query = SystemNotification::with(['user', 'service'])
            ->active()
            ->orderBy('created_at', 'desc');

        // Si es técnico, solo mostrar sus notificaciones
        if (Auth::user()->hasRole('technician')) {
            $query->where('user_id', Auth::id());
        }

        // Filtros
        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        if ($request->filled('priority')) {
            $query->byPriority($request->priority);
        }

        if ($request->filled('status')) {
            if ($request->status === 'read') {
                $query->read();
            } elseif ($request->status === 'unread') {
                $query->unread();
            }
        }

        $notifications = $query->paginate(15);

        // Estadísticas
        $baseQuery = SystemNotification::active();
        if (Auth::user() && Auth::user()->hasRole('technician')) {
            $baseQuery->where('user_id', Auth::id());
        }

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'unread' => (clone $baseQuery)->unread()->count(),
            'read' => (clone $baseQuery)->read()->count(),
            'urgent' => (clone $baseQuery)->byPriority('urgent')->unread()->count(),
            'high' => (clone $baseQuery)->byPriority('high')->unread()->count(),
            'medium' => (clone $baseQuery)->byPriority('medium')->unread()->count(),
            'low' => (clone $baseQuery)->byPriority('low')->unread()->count(),
        ];

        $viewName = Auth::user() && Auth::user()->hasRole('technician') ? 'technician.notifications.index' : 'admin.notification-center';
        
        return view($viewName, compact('notifications', 'stats'));
    }

    public function show(SystemNotification $notification)
    {
        $notification->markAsRead();
        return view('admin.notifications.show', compact('notification'));
    }

    public function markAsRead(SystemNotification $notification)
    {
        $notification->markAsRead();
        return response()->json(['success' => true]);
    }

    public function markAsUnread(SystemNotification $notification)
    {
        $notification->markAsUnread();
        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        $query = SystemNotification::active()->unread();
        
        // Si es técnico, solo marcar sus notificaciones
        if (Auth::user() && Auth::user()->hasRole('technician')) {
            $query->where('user_id', Auth::id());
        }
        
        $query->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy(SystemNotification $notification)
    {
        $notification->delete();
        return redirect()->back()->with('success', 'Notificación eliminada exitosamente');
    }

    public function create()
    {
        $users = User::where('is_active', true)->get();
        $services = Service::whereIn('status', ['pendiente', 'en_progreso'])->get();
        
        return view('admin.notifications.create', compact('users', 'services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:info,success,warning,error',
            'priority' => 'required|in:low,medium,high,urgent',
            'user_id' => 'nullable|exists:users,id',
            'service_id' => 'nullable|exists:services,id',
            'expires_at' => 'nullable|date|after:now',
        ]);

        $notification = SystemNotification::create([
            'title' => $request->title,
            'message' => $request->message,
            'type' => $request->type,
            'priority' => $request->priority,
            'user_id' => $request->user_id,
            'service_id' => $request->service_id,
            'metadata' => $request->metadata ?? [],
            'expires_at' => $request->expires_at,
        ]);

        return redirect()->route('admin.notification-center')
            ->with('success', 'Notificación creada exitosamente');
    }

    public function getUnreadCount()
    {
        $query = SystemNotification::active()->unread();
        
        // Si es técnico, solo contar sus notificaciones
        if (Auth::user() && Auth::user()->hasRole('technician')) {
            $query->where('user_id', Auth::id());
        }
        
        $count = $query->count();
        return response()->json(['count' => $count]);
    }

    public function getRecentNotifications()
    {
        $query = SystemNotification::active()
            ->unread()
            ->with(['user', 'service'])
            ->orderBy('created_at', 'desc')
            ->limit(5);
            
        // Si es técnico, solo mostrar sus notificaciones
        if (Auth::user() && Auth::user()->hasRole('technician')) {
            $query->where('user_id', Auth::id());
        }

        $notifications = $query->get();

        return response()->json(['notifications' => $notifications]);
    }
}
