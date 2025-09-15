<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Service;
use App\Models\Client;
use App\Models\User;

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
}
