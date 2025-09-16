<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Service;
use App\Models\Client;
use App\Models\User;
use App\Models\Site;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Si el usuario es técnico real (no super-admin), mostrar dashboard de técnico
        if ($user->hasRole("technician") && !$user->hasRole("super-admin")) {
            return redirect()->route("technician.dashboard");
        }
        
        // Si es super-admin en vista de técnico, mostrar dashboard de técnico
        if (session("technician_view", false) && $user->hasRole("super-admin")) {
            return redirect()->route("technician.dashboard");
        }
        
        // Por defecto, mostrar dashboard de admin para super-admins
        $stats = [
            "work_orders" => Service::count(),
            "clients" => Client::count(),
            "sites" => Service::where("status", "completado")->count(),
            "technicians" => User::whereHas("roles", function($Q) { $Q->where("name", "technician"); })->count(),
        ];

        return view("dashboard", compact("user", "stats"));
    }

    public function statistics()
    {
        $user = auth()->user();
        
        // Estadísticas detalladas
        $stats = [
            "work_orders" => Service::count(),
            "work_orders_pending" => Service::where("status", "pendiente")->count(),
            "work_orders_in_progress" => Service::where("status", "en_progreso")->count(),
            "work_orders_completed" => Service::where("status", "completado")->count(),
            "clients" => Client::count(),
            "sites" => Site::count(),
            "technicians" => User::whereHas("roles", function($Q) { $Q->where("name", "technician"); })->count(),
        ];

        // Servicios por mes (últimos 6 meses)
        $monthlyServices = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = Service::whereYear('created_at', $date->year)
                           ->whereMonth('created_at', $date->month)
                           ->count();
            $monthlyServices[] = [
                'month' => $date->format('M Y'),
                'count' => $count
            ];
        }

        // Servicios recientes
        $recentServices = Service::with(['client', 'assignedUser'])
                                 ->latest()
                                 ->limit(10)
                                 ->get();

        return view("admin.statistics", compact("user", "stats", "monthlyServices", "recentServices"));
    }
}
