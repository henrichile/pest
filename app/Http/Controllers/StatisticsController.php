<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Client;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    public function index()
    {
        // Estadísticas generales
        $stats = [
            "total_services" => Service::count(),
            "completed_services" => Service::where("status", "finalizado")->count(),
            "pending_services" => Service::where("status", "pendiente")->count(),
            "in_progress_services" => Service::where("status", "en_progreso")->count(),
            "total_clients" => Client::count(),
            "active_clients" => Client::whereHas("services")->count(),
        ];

        // Servicios por tipo
        $servicesByType = Service::select("service_type", DB::raw("count(*) as total"))
            ->groupBy("service_type")
            ->get()
            ->pluck("total", "service_type")
            ->toArray();

        // Servicios por estado
        $servicesByStatus = Service::select("status", DB::raw("count(*) as total"))
            ->groupBy("status")
            ->get()
            ->pluck("total", "status")
            ->toArray();

        // Servicios por prioridad
        $servicesByPriority = Service::select("priority", DB::raw("count(*) as total"))
            ->groupBy("priority")
            ->get()
            ->pluck("total", "priority")
            ->toArray();

        // Servicios por mes (últimos 6 meses)
        $servicesByMonth = Service::select(
                DB::raw("DATE_FORMAT(created_at, \"%Y-%m\") as month"),
                DB::raw("count(*) as total")
            )
            ->where("created_at", ">=", now()->subMonths(6))
            ->groupBy("month")
            ->orderBy("month")
            ->get()
            ->pluck("total", "month")
            ->toArray();

        return view("statistics.index", compact(
            "stats",
            "servicesByType",
            "servicesByStatus", 
            "servicesByPriority",
            "servicesByMonth"
        ));
    }

    public function services()
    {
        $services = Service::with("client")
            ->latest()
            ->paginate(20);

        return view("statistics.services", compact("services"));
    }

    public function clients()
    {
        $clients = Client::withCount("services")
            ->orderBy("services_count", "desc")
            ->paginate(20);

        return view("statistics.clients", compact("clients"));
    }
}
