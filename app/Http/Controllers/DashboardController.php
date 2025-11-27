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
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show main dashboard.
     */
    public function index(): View
    {
        $user = Auth::user();
        
        // Get recent notifications for the user (available to all dashboards)
        $recentNotifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        $unreadCount = $user->unreadNotifications()->count();
        
        // Share notifications with all views
        view()->share('recentNotifications', $recentNotifications);
        view()->share('unreadCount', $unreadCount);
        
        // Get user role
        $userRole = $user->getRoleNames()->first();
        
        // Redirect to role-specific dashboard
        switch ($userRole) {
            case 'super-admin':
                return $this->superAdminDashboard();
            case 'supervisor':
                return $this->supervisorDashboard();
            case 'technician':
                return $this->technicianDashboard();
            case 'client':
                return $this->clientDashboard();
            default:
                return $this->defaultDashboard();
        }
    }

    /**
     * Super Admin Dashboard.
     */
    private function superAdminDashboard(): View
    {
        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();
        $thisYear = Carbon::now()->startOfYear();
        
        // Overall statistics
        $totalClients = Client::count();
        // Check if is_active column exists before using it
        try {
            $activeClients = Client::where('is_active', true)->count();
        } catch (\Exception $e) {
            // If column doesn't exist, consider all clients as active
            $activeClients = $totalClients;
        }
        
        $totalSites = Site::count();
        // Check if is_active column exists before using it
        try {
            $activeSites = Site::where('is_active', true)->count();
        } catch (\Exception $e) {
            // If column doesn't exist, consider all sites as active
            $activeSites = $totalSites;
        }
        $totalWorkOrders = WorkOrder::count();
        
        // Get technicians count (handle if role doesn't exist)
        try {
            $totalTechnicians = User::role('technician')->count();
        } catch (\Exception $e) {
            $totalTechnicians = 0;
        }
        
        // Get supervisors count (handle if role doesn't exist)
        try {
            $totalSupervisors = User::role('supervisor')->count();
        } catch (\Exception $e) {
            $totalSupervisors = 0;
        }
        
        // Work order statistics
        // Check if scheduled_date column exists, otherwise use created_at
        try {
            $todayWorkOrders = WorkOrder::whereDate('scheduled_date', $today)->count();
            $thisWeekWorkOrders = WorkOrder::where('scheduled_date', '>=', $thisWeek)->count();
            $thisMonthWorkOrders = WorkOrder::where('scheduled_date', '>=', $thisMonth)->count();
            $thisYearWorkOrders = WorkOrder::where('scheduled_date', '>=', $thisYear)->count();
        } catch (\Exception $e) {
            // If scheduled_date doesn't exist, use created_at instead
            $todayWorkOrders = WorkOrder::whereDate('created_at', $today)->count();
            $thisWeekWorkOrders = WorkOrder::where('created_at', '>=', $thisWeek)->count();
            $thisMonthWorkOrders = WorkOrder::where('created_at', '>=', $thisMonth)->count();
            $thisYearWorkOrders = WorkOrder::where('created_at', '>=', $thisYear)->count();
        }
        
        // Status distribution
        try {
            $statusDistribution = WorkOrder::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status');
        } catch (\Exception $e) {
            // If status column doesn't exist, return empty collection
            $statusDistribution = collect();
        }
        
        // Service distribution
        try {
            $serviceDistribution = WorkOrder::selectRaw('s.name as service_name, COUNT(*) as count')
                ->join('services as s', 'work_orders.service_id', '=', 's.id')
                ->groupBy('s.id', 's.name')
                ->pluck('count', 'service_name');
        } catch (\Exception $e) {
            // If name column doesn't exist, try using id or return empty collection
            try {
                $serviceDistribution = WorkOrder::selectRaw('s.id as service_id, COUNT(*) as count')
                    ->join('services as s', 'work_orders.service_id', '=', 's.id')
                    ->groupBy('s.id')
                    ->pluck('count', 'service_id');
            } catch (\Exception $e2) {
                // If everything fails, return empty collection
                $serviceDistribution = collect();
            }
        }
        
        // Monthly work orders trend
        try {
            $monthlyTrend = WorkOrder::selectRaw('
                    DATE_FORMAT(scheduled_date, "%Y-%m") as month,
                    COUNT(*) as count
                ')
                ->where('scheduled_date', '>=', Carbon::now()->subMonths(12))
                ->groupBy('month')
                ->orderBy('month')
                ->get();
        } catch (\Exception $e) {
            // If scheduled_date doesn't exist, use created_at instead
            $monthlyTrend = WorkOrder::selectRaw('
                    DATE_FORMAT(created_at, "%Y-%m") as month,
                    COUNT(*) as count
                ')
                ->where('created_at', '>=', Carbon::now()->subMonths(12))
                ->groupBy('month')
                ->orderBy('month')
                ->get();
        }
        
        // Top clients by work orders
        try {
            $topClients = WorkOrder::selectRaw('
                    c.name as client_name,
                    COUNT(*) as work_order_count,
                    SUM(CASE WHEN work_orders.status = "completed" THEN 1 ELSE 0 END) as completed_count
                ')
                ->join('clients as c', 'work_orders.client_id', '=', 'c.id')
                ->groupBy('c.id', 'c.name')
                ->orderBy('work_order_count', 'desc')
                ->limit(10)
                ->get();
        } catch (\Exception $e) {
            // If status column doesn't exist, don't count completed
            try {
                $topClients = WorkOrder::selectRaw('
                        c.name as client_name,
                        COUNT(*) as work_order_count,
                        0 as completed_count
                    ')
                    ->join('clients as c', 'work_orders.client_id', '=', 'c.id')
                    ->groupBy('c.id', 'c.name')
                    ->orderBy('work_order_count', 'desc')
                    ->limit(10)
                    ->get();
            } catch (\Exception $e2) {
                // If everything fails, return empty collection
                $topClients = collect();
            }
        }
        
        // Material usage trends
        try {
            $materialUsageTrend = MaterialMovement::selectRaw('
                    m.name as material_name,
                    DATE_FORMAT(created_at, "%Y-%m") as month,
                    SUM(ABS(quantity)) as total_usage
                ')
                ->join('materials as m', 'material_movements.material_id', '=', 'm.id')
                ->where('movement_type', 'usage')
                ->where('created_at', '>=', Carbon::now()->subMonths(6))
                ->groupBy('m.id', 'm.name', 'month')
                ->orderBy('month')
                ->get();
        } catch (\Exception $e) {
            // If name column doesn't exist, use id instead
            try {
                $materialUsageTrend = MaterialMovement::selectRaw('
                        m.id as material_id,
                        DATE_FORMAT(created_at, "%Y-%m") as month,
                        SUM(ABS(quantity)) as total_usage
                    ')
                    ->join('materials as m', 'material_movements.material_id', '=', 'm.id')
                    ->where('movement_type', 'usage')
                    ->where('created_at', '>=', Carbon::now()->subMonths(6))
                    ->groupBy('m.id', 'month')
                    ->orderBy('month')
                    ->get();
            } catch (\Exception $e2) {
                // If everything fails, return empty collection
                $materialUsageTrend = collect();
            }
        }
        
        // Quality metrics
        try {
            $qualityMetrics = ChecklistResponse::selectRaw('
                    status,
                    COUNT(*) as count,
                    AVG(CASE WHEN status = "completed" THEN 1 ELSE 0 END) * 100 as completion_rate
                ')
                ->where('created_at', '>=', $thisMonth)
                ->groupBy('status')
                ->get();
        } catch (\Exception $e) {
            // If status column doesn't exist, return empty collection
            $qualityMetrics = collect();
        }
        
        // Nonconformity trends
        try {
            $nonconformityTrend = Nonconformity::selectRaw('
                    DATE_FORMAT(reported_at, "%Y-%m") as month,
                    COUNT(*) as count,
                    SUM(CASE WHEN status = "resolved" THEN 1 ELSE 0 END) as resolved_count
                ')
                ->where('reported_at', '>=', Carbon::now()->subMonths(12))
                ->groupBy('month')
                ->orderBy('month')
                ->get();
        } catch (\Exception $e) {
            // If status column doesn't exist, don't count resolved
            try {
                $nonconformityTrend = Nonconformity::selectRaw('
                        DATE_FORMAT(reported_at, "%Y-%m") as month,
                        COUNT(*) as count,
                        0 as resolved_count
                    ')
                    ->where('reported_at', '>=', Carbon::now()->subMonths(12))
                    ->groupBy('month')
                    ->orderBy('month')
                    ->get();
            } catch (\Exception $e2) {
                // If everything fails, return empty collection
                $nonconformityTrend = collect();
            }
        }
        
        // Recent activities
        $recentActivities = \Spatie\Activitylog\Models\Activity::with(['causer', 'subject'])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();
        
        // System health
        $systemHealth = [
            'database' => $this->checkDatabaseHealth(),
            'storage' => $this->checkStorageHealth(),
            'cache' => $this->checkCacheHealth(),
            'queue' => $this->checkQueueHealth(),
        ];
        
        // ===== SERVICIOS STATS (para el dashboard) =====
        $thisMonth = Carbon::now()->startOfMonth();
        $thisYear = Carbon::now()->startOfYear();
        
        // Estadísticas de servicios
        try {
            $totalServices = Service::count();
            $completedServices = Service::where('status', 'finalizado')->count();
            $pendingServices = Service::where('status', 'pendiente')->orWhere('status', 'pending')->count();
            $thisMonthServices = Service::where('created_at', '>=', $thisMonth)->count();
            
            // Servicios completados este mes (para el widget de ingresos)
            // Incluir servicios con checklist completado aunque status no sea finalizado
            $thisMonthCompletedServices = Service::where(function($query) {
                    $query->where('status', 'finalizado')
                          ->orWhere(function($q) {
                              // Incluir servicios con checklist completado aunque status no sea finalizado
                              $q->whereNotNull('checklist_completed_at')
                                ->whereNotNull('completed_at');
                          });
                })
                ->where(function($query) use ($thisMonth) {
                    $query->where(function($q) use ($thisMonth) {
                        $q->whereNotNull('completed_at')
                          ->where('completed_at', '>=', $thisMonth);
                    })->orWhere(function($q) use ($thisMonth) {
                        $q->whereNotNull('checklist_completed_at')
                          ->where('checklist_completed_at', '>=', $thisMonth);
                    })->orWhere(function($q) use ($thisMonth) {
                        // Fallback: si no hay fecha de completado, usar updated_at cuando status es finalizado
                        $q->whereNull('completed_at')
                          ->whereNull('checklist_completed_at')
                          ->where('updated_at', '>=', $thisMonth);
                    });
                })
                ->count();
        } catch (\Exception $e) {
            $totalServices = Service::count();
            $completedServices = 0;
            $pendingServices = 0;
            $thisMonthServices = Service::where('created_at', '>=', $thisMonth)->count();
            $thisMonthCompletedServices = 0;
        }
        
        // Resumen por tipo de servicio
        try {
            $serviceTypeSummary = Service::selectRaw('service_type, COUNT(*) as total')
                ->groupBy('service_type')
                ->get()
                ->keyBy('service_type');
        } catch (\Exception $e) {
            $serviceTypeSummary = collect();
        }
        
        // Ingresos del mes (solo servicios completados con precio)
        // Usar completed_at o checklist_completed_at para determinar cuándo se completó
        try {
            // También incluir servicios que tienen checklist_completed_at aunque el status no sea finalizado
            // (por si acaso hay servicios completados pero el status no se actualizó)
            $monthlyIncome = Service::where(function($query) {
                    $query->where('status', 'finalizado')
                          ->orWhere(function($q) {
                              // Incluir servicios con checklist completado aunque status no sea finalizado
                              $q->whereNotNull('checklist_completed_at')
                                ->whereNotNull('completed_at');
                          });
                })
                ->where(function($query) use ($thisMonth) {
                    $query->where(function($q) use ($thisMonth) {
                        $q->whereNotNull('completed_at')
                          ->where('completed_at', '>=', $thisMonth);
                    })->orWhere(function($q) use ($thisMonth) {
                        $q->whereNotNull('checklist_completed_at')
                          ->where('checklist_completed_at', '>=', $thisMonth);
                    })->orWhere(function($q) use ($thisMonth) {
                        // Fallback: si no hay fecha de completado, usar updated_at cuando status es finalizado
                        $q->whereNull('completed_at')
                          ->whereNull('checklist_completed_at')
                          ->where('updated_at', '>=', $thisMonth);
                    });
                })
                ->whereNotNull('price')
                ->where('price', '>', 0)
                ->sum('price');
        } catch (\Exception $e) {
            \Log::error('Error calculando ingresos del mes: ' . $e->getMessage());
            $monthlyIncome = 0;
        }
        
        // Alertas de stock bajo (productos con stock menor a 10)
        $lowStockAlerts = 0;
        $lowStockProducts = [];
        try {
            $lowStockProducts = \App\Models\Product::where('stock', '<', 10)
                ->select('id', 'name', 'stock')
                ->orderBy('stock', 'asc')
                ->limit(4)
                ->get();
            $lowStockAlerts = \App\Models\Product::where('stock', '<', 10)->count();
        } catch (\Exception $e) {
            \Log::error('Error calculando alertas de stock bajo: ' . $e->getMessage());
            $lowStockAlerts = 0;
            $lowStockProducts = [];
        }
        
        // Datos del gráfico: servicios por mes y por tipo
        $chartLabels = [];
        $chartDatasets = [];
        
        // Obtener período del request
        $period = request('period', 'this_month');
        $monthsToShow = 12; // Por defecto 12 meses
        
        // Ajustar cantidad de meses según el período seleccionado
        switch ($period) {
            case 'last_month':
                $monthsToShow = 1;
                break;
            case 'last_3_months':
                $monthsToShow = 3;
                break;
            case 'last_6_months':
                $monthsToShow = 6;
                break;
            case 'this_year':
                $monthsToShow = (int)Carbon::now()->format('n'); // Mes actual del año
                break;
            case 'last_year':
                $monthsToShow = 12;
                break;
            case 'all_time':
                // Para "todo el tiempo", mostrar últimos 24 meses
                $monthsToShow = 24;
                break;
            case 'this_month':
            default:
                $monthsToShow = 12;
                break;
        }
        
        // Generar etiquetas de meses según el período
        $months = [];
        $startMonth = null;
        
        if ($period === 'last_year') {
            // Año pasado: mostrar los 12 meses del año anterior
            $startMonth = Carbon::now()->subYear()->startOfYear();
            for ($i = 0; $i < 12; $i++) {
                $month = $startMonth->copy()->addMonths($i);
                $months[] = $month->format('Y-m');
                $monthName = $month->locale('es')->isoFormat('MMM');
                $chartLabels[] = ucfirst($monthName);
            }
        } else {
            // Para otros períodos, mostrar los últimos N meses
            for ($i = $monthsToShow - 1; $i >= 0; $i--) {
                $month = Carbon::now()->subMonths($i);
                $months[] = $month->format('Y-m');
                // Usar formato español para los meses
                $monthName = $month->locale('es')->isoFormat('MMM');
                $chartLabels[] = ucfirst($monthName);
            }
        }
        
        // Determinar qué columna de fecha usar
        $dateColumn = 'created_at';
        try {
            // Verificar si created_at existe consultando un servicio
            Service::select('created_at')->limit(1)->get();
        } catch (\Exception $e) {
            try {
                // Intentar con scheduled_date
                Service::select('scheduled_date')->limit(1)->get();
                $dateColumn = 'scheduled_date';
            } catch (\Exception $e2) {
                // Usar updated_at como último recurso
                $dateColumn = 'updated_at';
            }
        }
        
        // Tipos de servicio con sus colores
        $serviceTypes = [
            'fumigacion' => ['color' => '#ef4444', 'label' => 'Fumigación'],
            'desratizacion' => ['color' => '#f59e0b', 'label' => 'Desratización'],
            'sanitizacion' => ['color' => '#8b5cf6', 'label' => 'Sanitización'],
            'monitoreo-cebaderas' => ['color' => '#ec4899', 'label' => 'Monitoreo Cebaderas'],
        ];
        
        // Para cada tipo de servicio, obtener datos mensuales
        foreach ($serviceTypes as $typeSlug => $typeInfo) {
            $monthlyData = [];
            foreach ($months as $month) {
                try {
                    // Usar la columna de fecha determinada anteriormente
                    $count = Service::where('service_type', $typeSlug)
                        ->whereRaw("DATE_FORMAT({$dateColumn}, '%Y-%m') = ?", [$month])
                        ->count();
                    $monthlyData[] = $count;
                } catch (\Exception $e) {
                    // Si falla, intentar con otra columna
                    try {
                        $altDateColumn = $dateColumn === 'created_at' ? 'scheduled_date' : ($dateColumn === 'scheduled_date' ? 'updated_at' : 'created_at');
                        $count = Service::where('service_type', $typeSlug)
                            ->whereRaw("DATE_FORMAT({$altDateColumn}, '%Y-%m') = ?", [$month])
                            ->count();
                        $monthlyData[] = $count;
                    } catch (\Exception $e2) {
                        $monthlyData[] = 0;
                    }
                }
            }
            
            $chartDatasets[] = [
                'label' => $typeInfo['label'],
                'data' => $monthlyData,
                'borderColor' => $typeInfo['color'],
                'backgroundColor' => $typeInfo['color'] . '40', // 40 = 25% opacity
                'fill' => true,
                'tension' => 0.4,
                'pointRadius' => 3,
                'pointHoverRadius' => 5,
            ];
        }
        
        // "Otros Servicios" - todos los demás tipos
        $otherTypesData = [];
        foreach ($months as $month) {
            try {
                // Usar la columna de fecha determinada anteriormente
                $count = Service::whereNotIn('service_type', array_keys($serviceTypes))
                    ->whereRaw("DATE_FORMAT({$dateColumn}, '%Y-%m') = ?", [$month])
                    ->count();
                $otherTypesData[] = $count;
            } catch (\Exception $e) {
                // Si falla, intentar con otra columna
                try {
                    $altDateColumn = $dateColumn === 'created_at' ? 'scheduled_date' : ($dateColumn === 'scheduled_date' ? 'updated_at' : 'created_at');
                    $count = Service::whereNotIn('service_type', array_keys($serviceTypes))
                        ->whereRaw("DATE_FORMAT({$altDateColumn}, '%Y-%m') = ?", [$month])
                        ->count();
                    $otherTypesData[] = $count;
                } catch (\Exception $e2) {
                    $otherTypesData[] = 0;
                }
            }
        }
        
        $chartDatasets[] = [
            'label' => 'Otros Servicios',
            'data' => $otherTypesData,
            'borderColor' => '#22c55e',
            'backgroundColor' => '#22c55e40',
            'fill' => true,
            'tension' => 0.4,
            'pointRadius' => 3,
            'pointHoverRadius' => 5,
        ];
        
        // Servicios recientes
        try {
            $recentServices = Service::with('client')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
        } catch (\Exception $e) {
            $recentServices = collect();
        }
        
        // Construir array $stats
        $stats = [
            'clients' => $totalClients,
            'this_month' => $thisMonthServices,
            'completed' => $completedServices,
            'this_month_completed' => $thisMonthCompletedServices ?? 0, // Servicios completados este mes
            'pending' => $pendingServices,
            'total_services' => $totalServices,
            'total_services_percentage' => $totalServices,
            'service_type_summary' => $serviceTypeSummary,
            'monthly_income' => $monthlyIncome,
            'low_stock_alerts' => $lowStockAlerts,
            'chart_labels' => $chartLabels,
            'chart_datasets' => $chartDatasets,
        ];
        
        return view('dashboard', compact(
            'totalClients',
            'activeClients',
            'totalSites',
            'activeSites',
            'totalWorkOrders',
            'totalTechnicians',
            'totalSupervisors',
            'todayWorkOrders',
            'thisWeekWorkOrders',
            'thisMonthWorkOrders',
            'thisYearWorkOrders',
            'statusDistribution',
            'serviceDistribution',
            'monthlyTrend',
            'topClients',
            'materialUsageTrend',
            'qualityMetrics',
            'nonconformityTrend',
            'recentActivities',
            'systemHealth',
            'stats',
            'recentServices'
        ));
    }

    /**
     * Supervisor Dashboard.
     */
    private function supervisorDashboard(): View
    {
        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();
        
        // Work order statistics
        $totalWorkOrders = WorkOrder::count();
        try {
            $todayWorkOrders = WorkOrder::whereDate('scheduled_date', $today)->count();
            $thisWeekWorkOrders = WorkOrder::where('scheduled_date', '>=', $thisWeek)->count();
            $thisMonthWorkOrders = WorkOrder::where('scheduled_date', '>=', $thisMonth)->count();
        } catch (\Exception $e) {
            // If scheduled_date doesn't exist, use created_at instead
            $todayWorkOrders = WorkOrder::whereDate('created_at', $today)->count();
            $thisWeekWorkOrders = WorkOrder::where('created_at', '>=', $thisWeek)->count();
            $thisMonthWorkOrders = WorkOrder::where('created_at', '>=', $thisMonth)->count();
        }
        
        // Status distribution
        try {
            $statusDistribution = WorkOrder::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status');
        } catch (\Exception $e) {
            // If status column doesn't exist, return empty collection
            $statusDistribution = collect();
        }
        
        // Technician performance
        try {
            $technicianPerformance = WorkSession::selectRaw('
                    u.name as technician_name,
                    COUNT(*) as total_sessions,
                    AVG(TIMESTAMPDIFF(MINUTE, start_time, end_time)) as avg_duration_minutes,
                    SUM(CASE WHEN wo.status = "completed" THEN 1 ELSE 0 END) as completed_orders
                ')
                ->join('users as u', 'work_sessions.technician_id', '=', 'u.id')
                ->join('work_orders as wo', 'work_sessions.work_order_id', '=', 'wo.id')
                ->where('work_sessions.start_time', '>=', $thisMonth)
                ->groupBy('u.id', 'u.name')
                ->get();
        } catch (\Exception $e) {
            // If status column doesn't exist, don't count completed orders
            try {
                $technicianPerformance = WorkSession::selectRaw('
                        u.name as technician_name,
                        COUNT(*) as total_sessions,
                        AVG(TIMESTAMPDIFF(MINUTE, start_time, end_time)) as avg_duration_minutes,
                        0 as completed_orders
                    ')
                    ->join('users as u', 'work_sessions.technician_id', '=', 'u.id')
                    ->join('work_orders as wo', 'work_sessions.work_order_id', '=', 'wo.id')
                    ->where('work_sessions.start_time', '>=', $thisMonth)
                    ->groupBy('u.id', 'u.name')
                    ->get();
            } catch (\Exception $e2) {
                // If everything fails, return empty collection
                $technicianPerformance = collect();
            }
        }
        
        // Material usage
        try {
            $materialUsage = MaterialMovement::selectRaw('
                    m.name as material_name,
                    SUM(ABS(quantity)) as total_usage,
                    COUNT(*) as usage_count
                ')
                ->join('materials as m', 'material_movements.material_id', '=', 'm.id')
                ->where('movement_type', 'usage')
                ->where('created_at', '>=', $thisMonth)
                ->groupBy('m.id', 'm.name')
                ->orderBy('total_usage', 'desc')
                ->limit(10)
                ->get();
        } catch (\Exception $e) {
            // If name column doesn't exist, use id instead
            try {
                $materialUsage = MaterialMovement::selectRaw('
                        m.id as material_id,
                        SUM(ABS(quantity)) as total_usage,
                        COUNT(*) as usage_count
                    ')
                    ->join('materials as m', 'material_movements.material_id', '=', 'm.id')
                    ->where('movement_type', 'usage')
                    ->where('created_at', '>=', $thisMonth)
                    ->groupBy('m.id')
                    ->orderBy('total_usage', 'desc')
                    ->limit(10)
                    ->get();
            } catch (\Exception $e2) {
                // If everything fails, return empty collection
                $materialUsage = collect();
            }
        }
        
        // Nonconformities
        try {
            $openNonconformities = Nonconformity::where('status', 'open')
                ->with(['workOrder.client', 'workOrder.site', 'technician'])
                ->orderBy('reported_at', 'desc')
                ->limit(10)
                ->get();
        } catch (\Exception $e) {
            // If status column doesn't exist, get all nonconformities
            $openNonconformities = Nonconformity::with(['workOrder.client', 'workOrder.site', 'technician'])
                ->orderBy('reported_at', 'desc')
                ->limit(10)
                ->get();
        }
        
        // Recent work orders
        $recentWorkOrders = WorkOrder::with(['client', 'site', 'service', 'assignedTechnicians.technician'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Checklist completion rate
        try {
            $checklistStats = ChecklistResponse::selectRaw('
                    status,
                    COUNT(*) as count,
                    AVG(CASE WHEN status = "completed" THEN 1 ELSE 0 END) * 100 as completion_rate
                ')
                ->where('created_at', '>=', $thisMonth)
                ->groupBy('status')
                ->get();
        } catch (\Exception $e) {
            // If status column doesn't exist, return empty collection
            $checklistStats = collect();
        }
        
        return view('dashboard', compact(
            'totalWorkOrders',
            'todayWorkOrders',
            'thisWeekWorkOrders',
            'thisMonthWorkOrders',
            'statusDistribution',
            'technicianPerformance',
            'materialUsage',
            'openNonconformities',
            'recentWorkOrders',
            'checklistStats'
        ));
    }

    /**
     * Technician Dashboard.
     */
    private function technicianDashboard(): View
    {
        $user = Auth::user();
        $today = Carbon::today();
        
        // Get today's assigned work orders
        try {
            $assignedWorkOrders = WorkOrderAssignment::where('technician_id', $user->id)
                ->whereHas('workOrder', function ($query) use ($today) {
                    $query->whereDate('scheduled_date', $today)
                        ->whereIn('status', ['scheduled', 'in_progress']);
                })
                ->with(['workOrder.client', 'workOrder.site', 'workOrder.service'])
                ->get();
        } catch (\Exception $e) {
            // If scheduled_date or status doesn't exist, try with created_at and without status
            try {
                $assignedWorkOrders = WorkOrderAssignment::where('technician_id', $user->id)
                    ->whereHas('workOrder', function ($query) use ($today) {
                        $query->whereDate('created_at', $today);
                    })
                    ->with(['workOrder.client', 'workOrder.site', 'workOrder.service'])
                    ->get();
            } catch (\Exception $e2) {
                // If everything fails, just get all assignments
                $assignedWorkOrders = WorkOrderAssignment::where('technician_id', $user->id)
                    ->with(['workOrder.client', 'workOrder.site', 'workOrder.service'])
                    ->get();
            }
        }
        
        // Get active work session
        $activeSession = WorkSession::where('technician_id', $user->id)
            ->whereNull('end_time')
            ->with(['workOrder.client', 'workOrder.site'])
            ->first();
        
        // Get recent work sessions
        $recentSessions = WorkSession::where('technician_id', $user->id)
            ->whereNotNull('end_time')
            ->orderBy('end_time', 'desc')
            ->limit(5)
            ->with(['workOrder.client', 'workOrder.site'])
            ->get();
        
        // Get pending checklist responses
        try {
            $pendingChecklists = ChecklistResponse::where('technician_id', $user->id)
                ->where('status', 'pending')
                ->with(['workOrder.client', 'workOrder.site', 'checklistTemplate'])
                ->get();
        } catch (\Exception $e) {
            // If status column doesn't exist, get all checklist responses
            $pendingChecklists = ChecklistResponse::where('technician_id', $user->id)
                ->with(['workOrder.client', 'workOrder.site', 'checklistTemplate'])
                ->get();
        }
        
        // Get materials assigned to technician
        $assignedMaterials = MaterialMovement::where('technician_id', $user->id)
            ->where('movement_type', 'assignment')
            ->where('quantity', '>', 0)
            ->with('material')
            ->get()
            ->groupBy('material_id')
            ->map(function ($movements) {
                $totalQuantity = $movements->sum('quantity');
                $material = $movements->first()->material;
                return [
                    'material' => $material,
                    'quantity' => $totalQuantity
                ];
            });
        
        // Get technician statistics
        $totalWorkOrders = WorkOrder::whereHas('assignedTechnicians', function ($query) use ($user) {
            $query->where('technician_id', $user->id);
        })->count();
        
        $completedWorkOrders = 0;
        try {
            $completedWorkOrders = WorkOrder::whereHas('assignedTechnicians', function ($query) use ($user) {
                $query->where('technician_id', $user->id);
            })->where('status', 'completed')->count();
        } catch (\Exception $e) {
            // If status column doesn't exist, return 0
            $completedWorkOrders = 0;
        }
        
        $stats = [
            'total_work_orders' => $totalWorkOrders,
            'completed_work_orders' => $completedWorkOrders,
            'total_sessions' => WorkSession::where('technician_id', $user->id)->count(),
            'total_treatments' => Treatment::where('technician_id', $user->id)->count(),
        ];
        
        return view('dashboard', compact(
            'assignedWorkOrders',
            'activeSession',
            'recentSessions',
            'pendingChecklists',
            'assignedMaterials',
            'stats'
        ));
    }

    /**
     * Client Dashboard.
     */
    private function clientDashboard(): View
    {
        $user = Auth::user();
        $client = $user->client;
        
        if (!$client) {
            abort(403, 'Usuario no asociado a un cliente.');
        }
        
        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();
        
        // Work order statistics
        $totalWorkOrders = WorkOrder::where('client_id', $client->id)->count();
        try {
            $todayWorkOrders = WorkOrder::where('client_id', $client->id)
                ->whereDate('scheduled_date', $today)->count();
            $thisWeekWorkOrders = WorkOrder::where('client_id', $client->id)
                ->where('scheduled_date', '>=', $thisWeek)->count();
            $thisMonthWorkOrders = WorkOrder::where('client_id', $client->id)
                ->where('scheduled_date', '>=', $thisMonth)->count();
        } catch (\Exception $e) {
            // If scheduled_date doesn't exist, use created_at instead
            $todayWorkOrders = WorkOrder::where('client_id', $client->id)
                ->whereDate('created_at', $today)->count();
            $thisWeekWorkOrders = WorkOrder::where('client_id', $client->id)
                ->where('created_at', '>=', $thisWeek)->count();
            $thisMonthWorkOrders = WorkOrder::where('client_id', $client->id)
                ->where('created_at', '>=', $thisMonth)->count();
        }
        
        // Status distribution
        try {
            $statusDistribution = WorkOrder::where('client_id', $client->id)
                ->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status');
        } catch (\Exception $e) {
            // If status column doesn't exist, return empty collection
            $statusDistribution = collect();
        }
        
        // Service distribution
        try {
            $serviceDistribution = WorkOrder::where('client_id', $client->id)
                ->selectRaw('s.name as service_name, COUNT(*) as count')
                ->join('services as s', 'work_orders.service_id', '=', 's.id')
                ->groupBy('s.id', 's.name')
                ->pluck('count', 'service_name');
        } catch (\Exception $e) {
            // If name column doesn't exist, try using id or return empty collection
            try {
                $serviceDistribution = WorkOrder::where('client_id', $client->id)
                    ->selectRaw('s.id as service_id, COUNT(*) as count')
                    ->join('services as s', 'work_orders.service_id', '=', 's.id')
                    ->groupBy('s.id')
                    ->pluck('count', 'service_id');
            } catch (\Exception $e2) {
                // If everything fails, return empty collection
                $serviceDistribution = collect();
            }
        }
        
        // Recent work orders
        try {
            $recentWorkOrders = WorkOrder::where('client_id', $client->id)
                ->with(['site', 'service', 'assignedTechnicians.technician'])
                ->orderBy('scheduled_date', 'desc')
                ->limit(10)
                ->get();
            
            // Upcoming work orders
            try {
                $upcomingWorkOrders = WorkOrder::where('client_id', $client->id)
                    ->where('scheduled_date', '>=', $today)
                    ->whereIn('status', ['scheduled', 'assigned'])
                    ->with(['site', 'service', 'assignedTechnicians.technician'])
                    ->orderBy('scheduled_date')
                    ->limit(10)
                    ->get();
            } catch (\Exception $e2) {
                // If status column doesn't exist, don't filter by status
                try {
                    $upcomingWorkOrders = WorkOrder::where('client_id', $client->id)
                        ->where('scheduled_date', '>=', $today)
                        ->with(['site', 'service', 'assignedTechnicians.technician'])
                        ->orderBy('scheduled_date')
                        ->limit(10)
                        ->get();
                } catch (\Exception $e3) {
                    $upcomingWorkOrders = WorkOrder::where('client_id', $client->id)
                        ->where('created_at', '>=', $today)
                        ->with(['site', 'service', 'assignedTechnicians.technician'])
                        ->orderBy('created_at')
                        ->limit(10)
                        ->get();
                }
            }
        } catch (\Exception $e) {
            // If scheduled_date doesn't exist, use created_at instead
            $recentWorkOrders = WorkOrder::where('client_id', $client->id)
                ->with(['site', 'service', 'assignedTechnicians.technician'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
            
            // Upcoming work orders
            try {
                $upcomingWorkOrders = WorkOrder::where('client_id', $client->id)
                    ->where('created_at', '>=', $today)
                    ->whereIn('status', ['scheduled', 'assigned'])
                    ->with(['site', 'service', 'assignedTechnicians.technician'])
                    ->orderBy('created_at')
                    ->limit(10)
                    ->get();
            } catch (\Exception $e2) {
                // If status column doesn't exist, don't filter by status
                $upcomingWorkOrders = WorkOrder::where('client_id', $client->id)
                    ->where('created_at', '>=', $today)
                    ->with(['site', 'service', 'assignedTechnicians.technician'])
                    ->orderBy('created_at')
                    ->limit(10)
                    ->get();
            }
        }
        
        // Sites
        try {
            $sites = $client->sites()->where('is_active', true)->get();
        } catch (\Exception $e) {
            // If is_active doesn't exist, get all sites
            $sites = $client->sites()->get();
        }
        
        // Services
        try {
            $services = Service::where('is_active', true)->get();
        } catch (\Exception $e) {
            // If is_active doesn't exist, get all services
            $services = Service::all();
        }
        
        // Quality metrics
        try {
            $qualityMetrics = ChecklistResponse::whereHas('workOrder', function ($query) use ($client) {
                    $query->where('client_id', $client->id);
                })
                ->selectRaw('
                    status,
                    COUNT(*) as count,
                    AVG(CASE WHEN status = "completed" THEN 1 ELSE 0 END) * 100 as completion_rate
                ')
                ->where('created_at', '>=', $thisMonth)
                ->groupBy('status')
                ->get();
        } catch (\Exception $e) {
            // If status column doesn't exist, return empty collection
            $qualityMetrics = collect();
        }
        
        // Nonconformities
        try {
            $openNonconformities = Nonconformity::whereHas('workOrder', function ($query) use ($client) {
                    $query->where('client_id', $client->id);
                })
                ->where('status', 'open')
                ->with(['workOrder.site', 'technician'])
                ->orderBy('reported_at', 'desc')
                ->limit(10)
                ->get();
        } catch (\Exception $e) {
            // If status column doesn't exist, get all nonconformities
            $openNonconformities = Nonconformity::whereHas('workOrder', function ($query) use ($client) {
                    $query->where('client_id', $client->id);
                })
                ->with(['workOrder.site', 'technician'])
                ->orderBy('reported_at', 'desc')
                ->limit(10)
                ->get();
        }
        
        return view('dashboard', compact(
            'client',
            'totalWorkOrders',
            'todayWorkOrders',
            'thisWeekWorkOrders',
            'thisMonthWorkOrders',
            'statusDistribution',
            'serviceDistribution',
            'recentWorkOrders',
            'upcomingWorkOrders',
            'sites',
            'services',
            'qualityMetrics',
            'openNonconformities'
        ));
    }

    /**
     * Default Dashboard.
     */
    private function defaultDashboard(): View
    {
        $user = Auth::user();
        
        // Get basic statistics
        $stats = [
            'total_work_orders' => WorkOrder::count(),
            'total_clients' => Client::count(),
            'total_sites' => Site::count(),
            'total_technicians' => User::role('technician')->count(),
        ];
        
        // Get recent activities
        $recentActivities = \Spatie\Activitylog\Models\Activity::with(['causer', 'subject'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        return view('dashboard', compact('stats', 'recentActivities', 'lowStockProducts'));
    }

    /**
     * Check database health.
     */
    private function checkDatabaseHealth(): array
    {
        try {
            $connection = DB::connection();
            $connection->getPdo();
            
            return [
                'status' => 'healthy',
                'message' => 'Conexión a la base de datos exitosa',
                'details' => [
                    'driver' => $connection->getDriverName(),
                    'version' => $connection->getServerVersion(),
                ]
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Error de conexión a la base de datos',
                'details' => [
                    'error' => $e->getMessage(),
                ]
            ];
        }
    }

    /**
     * Check storage health.
     */
    private function checkStorageHealth(): array
    {
        try {
            $totalSpace = disk_total_space(storage_path());
            $freeSpace = disk_free_space(storage_path());
            $usedSpace = $totalSpace - $freeSpace;
            $usagePercentage = ($usedSpace / $totalSpace) * 100;
            
            $status = 'healthy';
            if ($usagePercentage > 90) {
                $status = 'critical';
            } elseif ($usagePercentage > 80) {
                $status = 'warning';
            }
            
            return [
                'status' => $status,
                'message' => "Uso de almacenamiento: " . round($usagePercentage, 2) . "%",
                'details' => [
                    'total_space' => $this->formatBytes($totalSpace),
                    'used_space' => $this->formatBytes($usedSpace),
                    'free_space' => $this->formatBytes($freeSpace),
                    'usage_percentage' => round($usagePercentage, 2),
                ]
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Error al verificar el almacenamiento',
                'details' => [
                    'error' => $e->getMessage(),
                ]
            ];
        }
    }

    /**
     * Check cache health.
     */
    private function checkCacheHealth(): array
    {
        try {
            $cache = cache();
            $cache->put('health_check', 'test', 60);
            $value = $cache->get('health_check');
            
            if ($value === 'test') {
                $cache->forget('health_check');
                
                return [
                    'status' => 'healthy',
                    'message' => 'Sistema de caché funcionando correctamente',
                    'details' => [
                        'driver' => config('cache.default'),
                    ]
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'Error en el sistema de caché',
                    'details' => []
                ];
            }
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Error al verificar el caché',
                'details' => [
                    'error' => $e->getMessage(),
                ]
            ];
        }
    }

    /**
     * Check queue health.
     */
    private function checkQueueHealth(): array
    {
        try {
            $queue = app('queue');
            $size = $queue->size();
            
            $status = 'healthy';
            if ($size > 1000) {
                $status = 'warning';
            } elseif ($size > 5000) {
                $status = 'critical';
            }
            
            return [
                'status' => $status,
                'message' => "Cola de trabajos: {$size} trabajos pendientes",
                'details' => [
                    'queue_size' => $size,
                    'driver' => config('queue.default'),
                ]
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Error al verificar la cola',
                'details' => [
                    'error' => $e->getMessage(),
                ]
            ];
        }
    }

    /**
     * Format bytes to human readable format.
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
