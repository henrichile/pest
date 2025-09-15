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
        $stats = [
            'total' => SystemNotification::active()->count(),
            'unread' => SystemNotification::active()->unread()->count(),
            'read' => SystemNotification::active()->read()->count(),
            'urgent' => SystemNotification::active()->byPriority('urgent')->unread()->count(),
            'high' => SystemNotification::active()->byPriority('high')->unread()->count(),
            'medium' => SystemNotification::active()->byPriority('medium')->unread()->count(),
            'low' => SystemNotification::active()->byPriority('low')->unread()->count(),
        ];

        return view('admin.notification-center', compact('notifications', 'stats'));
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
        SystemNotification::active()->unread()->update([
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
        $count = SystemNotification::active()->unread()->count();
        return response()->json(['count' => $count]);
    }

    public function getRecentNotifications()
    {
        $notifications = SystemNotification::active()
            ->unread()
            ->with(['user', 'service'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return response()->json(['notifications' => $notifications]);
    }
}
