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
use App\Models\Invoice;
use Illuminate\Support\Facades\Schema;
use App\Services\PdfService;
use App\Services\NotificationService;
use App\Http\Requests\CreateClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Http\Requests\CreateSiteRequest;
use App\Http\Requests\UpdateSiteRequest;
use App\Http\Requests\CreateServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Http\Requests\CreateMaterialRequest;
use App\Http\Requests\UpdateMaterialRequest;
use App\Http\Requests\CreatePestRequest;
use App\Http\Requests\UpdatePestRequest;
use App\Http\Requests\CreateChecklistTemplateRequest;
use App\Http\Requests\UpdateChecklistTemplateRequest;
use App\Http\Requests\CreateChecklistItemRequest;
use App\Http\Requests\UpdateChecklistItemRequest;
use App\Http\Requests\GenerateReportRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:super-admin');
    }

    /**
     * Show admin dashboard.
     */
    public function dashboard(): View
    {
        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();
        $thisYear = Carbon::now()->startOfYear();
        
        // Overall statistics
        $totalClients = Client::count();
        $activeClients = Client::where('is_active', true)->count();
        $totalSites = Site::count();
        $activeSites = Site::where('is_active', true)->count();
        $totalWorkOrders = WorkOrder::count();
        $totalTechnicians = User::role('technician')->count();
        $totalSupervisors = User::role('supervisor')->count();
        
        // Work order statistics
        $todayWorkOrders = WorkOrder::whereDate('created_at', $today)->count();
        $thisWeekWorkOrders = WorkOrder::where('created_at', '>=', $thisWeek)->count();
        $thisMonthWorkOrders = WorkOrder::where('created_at', '>=', $thisMonth)->count();
        $thisYearWorkOrders = WorkOrder::where('created_at', '>=', $thisYear)->count();
        
        // Status distribution
        $statusDistribution = WorkOrder::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');
        
        // Service distribution
        $serviceDistribution = WorkOrder::selectRaw('s.name as service_name, COUNT(*) as count')
            ->join('services as s', 'work_orders.service_id', '=', 's.id')
            ->groupBy('s.id', 's.name')
            ->pluck('count', 'service_name');
        
        // Monthly work orders trend
        $monthlyTrend = WorkOrder::selectRaw('
                DATE_FORMAT(scheduled_date, "%Y-%m") as month,
                COUNT(*) as count
            ')
            ->where('scheduled_date', '>=', Carbon::now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        // Top clients by work orders
        $topClients = WorkOrder::selectRaw('
                c.name as client_name,
                COUNT(*) as work_order_count,
                SUM(CASE WHEN wo.status = "completed" THEN 1 ELSE 0 END) as completed_count
            ')
            ->join('clients as c', 'work_orders.client_id', '=', 'c.id')
            ->groupBy('c.id', 'c.name')
            ->orderBy('work_order_count', 'desc')
            ->limit(10)
            ->get();
        
        // Material usage trends
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
        
        // Quality metrics
        $qualityMetrics = ChecklistResponse::selectRaw('
                status,
                COUNT(*) as count,
                AVG(CASE WHEN status = "completed" THEN 1 ELSE 0 END) * 100 as completion_rate
            ')
            ->where('created_at', '>=', $thisMonth)
            ->groupBy('status')
            ->get();
        
        // Nonconformity trends
        $nonconformityTrend = Nonconformity::selectRaw('
                DATE_FORMAT(reported_at, "%Y-%m") as month,
                COUNT(*) as count,
                SUM(CASE WHEN status = "resolved" THEN 1 ELSE 0 END) as resolved_count
            ')
            ->where('reported_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        // Prepare stats array for dashboard view
        $completed = WorkOrder::where('status', 'completed')
            ->where('created_at', '>=', $thisMonth)
            ->count();
        $pending = WorkOrder::whereIn('status', ['pending', 'in_progress'])
            ->where('created_at', '>=', $thisMonth)
            ->count();
        $totalServices = WorkOrder::where('created_at', '>=', $thisMonth)->count();
        
        // Calculate monthly income from invoices related to completed work orders
        try {
            $monthlyIncome = Invoice::whereHas('workOrder', function ($query) use ($thisMonth) {
                    $query->where('status', 'completed')
                          ->where('created_at', '>=', $thisMonth);
                })
                ->sum('total_amount') ?? 0;
        } catch (\Exception $e) {
            // If Invoice model or relationship doesn't exist, set to 0
            $monthlyIncome = 0;
        }
        
        // Get low stock alerts - check if stock_quantity exists, otherwise use a default query
        try {
            $lowStockAlerts = Material::whereColumn('stock_quantity', '<=', 'minimum_stock')
                ->count();
        } catch (\Exception $e) {
            // If columns don't exist, set to 0
            $lowStockAlerts = 0;
        }
        
        // Get monthly statistics by service type for the current year (for chart)
        $startOfYear = Carbon::now()->startOfYear();
        $endOfYear = Carbon::now()->endOfYear();
        
        // Get monthly data by service type
        // Check if service_type column exists in services table
        $hasServiceType = Schema::hasColumn('services', 'service_type');
        
        if ($hasServiceType) {
            $monthlyStatsByType = WorkOrder::selectRaw('
                    DATE_FORMAT(work_orders.created_at, "%Y-%m") as month,
                    s.service_type,
                    COUNT(*) as count
                ')
                ->join('services as s', 'work_orders.service_id', '=', 's.id')
                ->whereBetween('work_orders.created_at', [$startOfYear, $endOfYear])
                ->groupBy('month', 's.service_type')
                ->orderBy('month')
                ->get();
        } else {
            // If service_type doesn't exist, use slug from services
            $monthlyStatsByType = WorkOrder::selectRaw('
                    DATE_FORMAT(work_orders.created_at, "%Y-%m") as month,
                    s.slug as service_type,
                    COUNT(*) as count
                ')
                ->join('services as s', 'work_orders.service_id', '=', 's.id')
                ->whereBetween('work_orders.created_at', [$startOfYear, $endOfYear])
                ->groupBy('month', 's.slug')
                ->orderBy('month')
                ->get();
        }
        
        // Define service types with colors
        $serviceTypes = [
            'fumigacion' => ['name' => 'Fumigación', 'color' => '#ef4444', 'bgColor' => 'rgba(239, 68, 68, 0.1)'],
            'desratizacion' => ['name' => 'Desratización', 'color' => '#f59e0b', 'bgColor' => 'rgba(245, 158, 11, 0.1)'],
            'sanitizacion' => ['name' => 'Sanitización', 'color' => '#8b5cf6', 'bgColor' => 'rgba(139, 92, 246, 0.1)'],
            'monitoreo-cebaderas' => ['name' => 'Monitoreo Cebaderas', 'color' => '#ec4899', 'bgColor' => 'rgba(236, 72, 153, 0.1)'],
        ];
        
        // Build chart data for all months of the year
        $chartLabels = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        $chartDatasets = [];
        
        foreach ($serviceTypes as $typeSlug => $typeInfo) {
            $data = [];
            for ($month = 1; $month <= 12; $month++) {
                $monthKey = Carbon::now()->year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);
                $count = $monthlyStatsByType
                    ->where('month', $monthKey)
                    ->where('service_type', $typeSlug)
                    ->sum('count');
                $data[] = $count;
            }
            $chartDatasets[] = [
                'label' => $typeInfo['name'],
                'data' => $data,
                'borderColor' => $typeInfo['color'],
                'backgroundColor' => $typeInfo['bgColor'],
                'tension' => 0.4,
                'fill' => true
            ];
        }
        
        // Add "Otros Servicios" for any other service types
        $otherData = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthKey = Carbon::now()->year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);
            $count = $monthlyStatsByType
                ->where('month', $monthKey)
                ->whereNotIn('service_type', array_keys($serviceTypes))
                ->sum('count');
            $otherData[] = $count;
        }
        $chartDatasets[] = [
            'label' => 'Otros Servicios',
            'data' => $otherData,
            'borderColor' => '#22c55e',
            'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
            'tension' => 0.4,
            'fill' => true
        ];
        
        // Get service type summary for cards
        if ($hasServiceType) {
            $serviceTypeSummary = WorkOrder::selectRaw('
                    s.service_type,
                    COUNT(*) as total,
                    SUM(CASE WHEN work_orders.status = "completed" THEN 1 ELSE 0 END) as completed
                ')
                ->join('services as s', 'work_orders.service_id', '=', 's.id')
                ->where('work_orders.created_at', '>=', $thisMonth)
                ->groupBy('s.service_type')
                ->get()
                ->keyBy('service_type');
        } else {
            // If service_type doesn't exist, use slug
            $serviceTypeSummary = WorkOrder::selectRaw('
                    s.slug as service_type,
                    COUNT(*) as total,
                    SUM(CASE WHEN work_orders.status = "completed" THEN 1 ELSE 0 END) as completed
                ')
                ->join('services as s', 'work_orders.service_id', '=', 's.id')
                ->where('work_orders.created_at', '>=', $thisMonth)
                ->groupBy('s.slug')
                ->get()
                ->keyBy('service_type');
        }
        
        $totalServicesForPercentage = $serviceTypeSummary->sum('total');
        
        // Prepare stats array
        $stats = [
            'clients' => $totalClients,
            'this_month' => $thisMonthWorkOrders,
            'completed' => $completed,
            'pending' => $pending,
            'total_services' => $totalServices,
            'monthly_income' => $monthlyIncome,
            'low_stock_alerts' => $lowStockAlerts,
            'chart_labels' => $chartLabels,
            'chart_datasets' => $chartDatasets,
            'service_type_summary' => $serviceTypeSummary,
            'total_services_percentage' => $totalServicesForPercentage,
        ];
        
        // Get recent services
        $recent_services = WorkOrder::with(['client', 'site', 'service'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        return view('dashboard', compact(
            'stats',
            'recent_services',
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
            'nonconformityTrend'
        ));
    }

    /**
     * Show clients management.
     */
    public function clients(Request $request): View
    {
        $query = Client::with(['sites', 'workOrders']);

        // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('rut', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $clients = $query->orderBy('name')
            ->paginate(20);

        return view('admin.clients', compact('clients'));
    }

    /**
     * Show client details.
     */
    public function showClient(Client $client): View
    {
        $client->load(['sites', 'workOrders.service', 'workOrders.assignedTechnicians.technician']);
        
        $sites = $client->sites()->paginate(10);
        $workOrders = $client->workOrders()->with(['service', 'assignedTechnicians.technician'])->paginate(10);

        return view('admin.client', compact('client', 'sites', 'workOrders'));
    }

    /**
     * Create new client.
     */
    public function createClient(CreateClientRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $client = Client::create($request->validated());

            // Log activity
            activity()
                ->performedOn($client)
                ->causedBy(Auth::user())
                ->log('Cliente creado');

            DB::commit();

            return redirect()->route('admin.clients')
                ->with('success', 'Cliente creado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating client: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al crear el cliente.');
        }
    }

    /**
     * Update client.
     */
    public function updateClient(UpdateClientRequest $request, Client $client): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $client->update($request->validated());

            // Log activity
            activity()
                ->performedOn($client)
                ->causedBy(Auth::user())
                ->log('Cliente actualizado');

            DB::commit();

            return redirect()->back()
                ->with('success', 'Cliente actualizado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating client: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al actualizar el cliente.');
        }
    }

    /**
     * Show sites management.
     */
    public function sites(Request $request): View
    {
        $query = Site::with(['client', 'workOrders']);

        // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $sites = $query->orderBy('name')
            ->paginate(20);

        $clients = Client::where('is_active', true)->get();

        return view('admin.sites', compact('sites', 'clients'));
    }

    /**
     * Show site details.
     */
    public function showSite(Site $site): View
    {
        $site->load(['client', 'workOrders.service', 'workOrders.assignedTechnicians.technician']);
        
        $workOrders = $site->workOrders()->with(['service', 'assignedTechnicians.technician'])->paginate(10);

        return view('admin.site', compact('site', 'workOrders'));
    }

    /**
     * Create new site.
     */
    public function createSite(CreateSiteRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $site = Site::create($request->validated());

            // Log activity
            activity()
                ->performedOn($site)
                ->causedBy(Auth::user())
                ->log('Sitio creado');

            DB::commit();

            return redirect()->route('admin.sites')
                ->with('success', 'Sitio creado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating site: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al crear el sitio.');
        }
    }

    /**
     * Update site.
     */
    public function updateSite(UpdateSiteRequest $request, Site $site): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $site->update($request->validated());

            // Log activity
            activity()
                ->performedOn($site)
                ->causedBy(Auth::user())
                ->log('Sitio actualizado');

            DB::commit();

            return redirect()->back()
                ->with('success', 'Sitio actualizado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating site: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al actualizar el sitio.');
        }
    }

    /**
     * Show services management.
     */
    public function services(Request $request): View
    {
        $query = Service::with(['checklistTemplates', 'workOrders']);

        // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $services = $query->orderBy('name')
            ->paginate(20);

        return view('admin.services', compact('services'));
    }

    /**
     * Show service details.
     */
    public function showService(Service $service): View
    {
        $service->load(['checklistTemplates.items', 'workOrders.client', 'workOrders.site']);
        
        $checklistTemplates = $service->checklistTemplates()->paginate(10);
        $workOrders = $service->workOrders()->with(['client', 'site'])->paginate(10);

        return view('admin.service', compact('service', 'checklistTemplates', 'workOrders'));
    }

    /**
     * Create new service.
     */
    public function createService(CreateServiceRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $service = Service::create($request->validated());

            // Log activity
            activity()
                ->performedOn($service)
                ->causedBy(Auth::user())
                ->log('Servicio creado');

            DB::commit();

            return redirect()->route('admin.services')
                ->with('success', 'Servicio creado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating service: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al crear el servicio.');
        }
    }

    /**
     * Update service.
     */
    public function updateService(UpdateServiceRequest $request, Service $service): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $service->update($request->validated());

            // Log activity
            activity()
                ->performedOn($service)
                ->causedBy(Auth::user())
                ->log('Servicio actualizado');

            DB::commit();

            return redirect()->back()
                ->with('success', 'Servicio actualizado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating service: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al actualizar el servicio.');
        }
    }

    /**
     * Show materials management.
     */
    public function materials(Request $request): View
    {
        $query = Material::with(['movements']);

        // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $materials = $query->orderBy('name')
            ->paginate(20);

        return view('admin.materials', compact('materials'));
    }

    /**
     * Show material details.
     */
    public function showMaterial(Material $material): View
    {
        $material->load(['movements.technician', 'movements.workOrder']);
        
        $movements = $material->movements()->with(['technician', 'workOrder'])->paginate(20);

        return view('admin.material', compact('material', 'movements'));
    }

    /**
     * Create new material.
     */
    public function createMaterial(CreateMaterialRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $material = Material::create($request->validated());

            // Log activity
            activity()
                ->performedOn($material)
                ->causedBy(Auth::user())
                ->log('Material creado');

            DB::commit();

            return redirect()->route('admin.materials')
                ->with('success', 'Material creado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating material: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al crear el material.');
        }
    }

    /**
     * Update material.
     */
    public function updateMaterial(UpdateMaterialRequest $request, Material $material): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $material->update($request->validated());

            // Log activity
            activity()
                ->performedOn($material)
                ->causedBy(Auth::user())
                ->log('Material actualizado');

            DB::commit();

            return redirect()->back()
                ->with('success', 'Material actualizado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating material: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al actualizar el material.');
        }
    }

    /**
     * Show pests management.
     */
    public function pests(Request $request): View
    {
        $query = Pest::query();

        // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
                if (Schema::hasColumn('pests', 'technical_notes')) {
                    $q->orWhere('technical_notes', 'like', "%{$search}%");
                }
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Check if is_active column exists before filtering
        if (Schema::hasColumn('pests', 'is_active')) {
            $query->where('is_active', true);
        }

        // Order by name if column exists, otherwise by id
        if (Schema::hasColumn('pests', 'name')) {
            $query->orderBy('name');
        } else {
            $query->orderBy('id');
        }

        $pests = $query->get();

        return view('admin.pests', compact('pests'));
    }

    /**
     * Show form to create a new pest.
     */
    public function createPest(): View
    {
        $categories = [
            'Roedores',
            'Cucarachas',
            'Moscas',
            'Termitas',
            'Hormigas',
            'Aves',
            'Arañas',
            'Otros'
        ];
        
        return view('admin.create-pest', compact('categories'));
    }

    /**
     * Store a new pest.
     */
    public function storePest(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'scientific_name' => 'nullable|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'nullable|string',
            'technical_notes' => 'nullable|string',
            'control_methods' => 'nullable|array',
            'control_methods.*' => 'string|max:255',
            'risks' => 'nullable|array',
            'risks.*' => 'string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        try {
            DB::beginTransaction();

            // Convertir control_methods de array a formato correcto
            if (isset($validated['control_methods']) && is_array($validated['control_methods'])) {
                $validated['control_methods'] = array_filter($validated['control_methods']);
            }

            // Convertir risks de array a formato correcto
            if (isset($validated['risks']) && is_array($validated['risks'])) {
                $validated['risks'] = array_filter($validated['risks']);
            }

            // Establecer is_active por defecto
            if (!isset($validated['is_active'])) {
                $validated['is_active'] = true;
            }

            $pest = Pest::create($validated);

            // Log activity
            activity()
                ->performedOn($pest)
                ->causedBy(Auth::user())
                ->log('Plaga creada');

            DB::commit();

            return redirect()->route('admin.pests')
                ->with('success', 'Plaga creada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating pest: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear la plaga: ' . $e->getMessage());
        }
    }

    /**
     * Show pest details.
     */
    public function showPest(Pest $pest): View
    {
        $pest->load(['treatments.material', 'treatments.workOrder']);
        
        $treatments = $pest->treatments()->with(['material', 'workOrder'])->paginate(20);

        return view('admin.pest', compact('pest', 'treatments'));
    }

    /**
     * Create new pest (deprecated - use storePest instead).
     */
    public function createPestOld(CreatePestRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $pest = Pest::create($request->validated());

            // Log activity
            activity()
                ->performedOn($pest)
                ->causedBy(Auth::user())
                ->log('Plaga creada');

            DB::commit();

            return redirect()->route('admin.pests')
                ->with('success', 'Plaga creada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating pest: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al crear la plaga.');
        }
    }

    /**
     * Update pest.
     */
    public function updatePest(UpdatePestRequest $request, Pest $pest): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $pest->update($request->validated());

            // Log activity
            activity()
                ->performedOn($pest)
                ->causedBy(Auth::user())
                ->log('Plaga actualizada');

            DB::commit();

            return redirect()->back()
                ->with('success', 'Plaga actualizada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating pest: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al actualizar la plaga.');
        }
    }

    /**
     * Show checklist templates management.
     */
    public function checklistTemplates(Request $request): View
    {
        $query = ChecklistTemplate::with(['service', 'items']);

        // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('service_id')) {
            $query->where('service_id', $request->service_id);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $checklistTemplates = $query->orderBy('name')
            ->paginate(20);

        $services = Service::where('is_active', true)->get();

        return view('admin.checklist-templates', compact('checklistTemplates', 'services'));
    }

    /**
     * Show checklist template details.
     */
    public function showChecklistTemplate(ChecklistTemplate $checklistTemplate): View
    {
        $checklistTemplate->load(['service', 'items', 'responses.workOrder']);
        
        $items = $checklistTemplate->items()->paginate(20);
        $responses = $checklistTemplate->responses()->with(['workOrder'])->paginate(20);

        return view('admin.checklist-template', compact('checklistTemplate', 'items', 'responses'));
    }

    /**
     * Create new checklist template.
     */
    public function createChecklistTemplate(CreateChecklistTemplateRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $checklistTemplate = ChecklistTemplate::create($request->validated());

            // Log activity
            activity()
                ->performedOn($checklistTemplate)
                ->causedBy(Auth::user())
                ->log('Plantilla de checklist creada');

            DB::commit();

            return redirect()->route('admin.checklist-templates')
                ->with('success', 'Plantilla de checklist creada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating checklist template: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al crear la plantilla de checklist.');
        }
    }

    /**
     * Update checklist template.
     */
    public function updateChecklistTemplate(UpdateChecklistTemplateRequest $request, ChecklistTemplate $checklistTemplate): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $checklistTemplate->update($request->validated());

            // Log activity
            activity()
                ->performedOn($checklistTemplate)
                ->causedBy(Auth::user())
                ->log('Plantilla de checklist actualizada');

            DB::commit();

            return redirect()->back()
                ->with('success', 'Plantilla de checklist actualizada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating checklist template: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al actualizar la plantilla de checklist.');
        }
    }

    /**
     * Show checklist items management.
     */
    public function checklistItems(Request $request): View
    {
        $query = ChecklistItem::with(['checklistTemplate']);

        // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('question', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('checklist_template_id')) {
            $query->where('checklist_template_id', $request->checklist_template_id);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $checklistItems = $query->orderBy('order')
            ->paginate(20);

        $checklistTemplates = ChecklistTemplate::where('is_active', true)->get();

        return view('admin.checklist-items', compact('checklistItems', 'checklistTemplates'));
    }

    /**
     * Show checklist item details.
     */
    public function showChecklistItem(ChecklistItem $checklistItem): View
    {
        $checklistItem->load(['checklistTemplate', 'responses.checklistResponse']);
        
        $responses = $checklistItem->responses()->with(['checklistResponse.workOrder'])->paginate(20);

        return view('admin.checklist-item', compact('checklistItem', 'responses'));
    }

    /**
     * Create new checklist item.
     */
    public function createChecklistItem(CreateChecklistItemRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $checklistItem = ChecklistItem::create($request->validated());

            // Log activity
            activity()
                ->performedOn($checklistItem)
                ->causedBy(Auth::user())
                ->log('Item de checklist creado');

            DB::commit();

            return redirect()->route('admin.checklist-items')
                ->with('success', 'Item de checklist creado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating checklist item: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al crear el item de checklist.');
        }
    }

    /**
     * Update checklist item.
     */
    public function updateChecklistItem(UpdateChecklistItemRequest $request, ChecklistItem $checklistItem): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $checklistItem->update($request->validated());

            // Log activity
            activity()
                ->performedOn($checklistItem)
                ->causedBy(Auth::user())
                ->log('Item de checklist actualizado');

            DB::commit();

            return redirect()->back()
                ->with('success', 'Item de checklist actualizado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating checklist item: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al actualizar el item de checklist.');
        }
    }

    /**
     * Show users management.
     */
    public function users(Request $request): View
    {
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

        return view('admin.users', compact('users'));
    }

    /**
     * Show user details.
     */
    public function showUser(User $user): View
    {
        $user->load(['roles', 'workSessions', 'workOrderAssignments']);
        
        $workSessions = $user->workSessions()->with(['workOrder.client', 'workOrder.site'])->paginate(20);
        $workOrderAssignments = $user->workOrderAssignments()->with(['workOrder.client', 'workOrder.site'])->paginate(20);

        return view('admin.user', compact('user', 'workSessions', 'workOrderAssignments'));
    }

    /**
     * Generate comprehensive report.
     */
    public function generateComprehensiveReport(GenerateReportRequest $request): RedirectResponse
    {
        try {
            $pdfService = app(PdfService::class);
            $pdfPath = $pdfService->generateComprehensiveReport($request->all());
            $downloadUrl = $pdfService->getDownloadUrl($pdfPath);

            return redirect($downloadUrl);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al generar el reporte: ' . $e->getMessage());
        }
    }

    /**
     * Show system logs.
     */
    public function systemLogs(Request $request): View
    {
        $query = \Spatie\Activitylog\Models\Activity::with(['causer', 'subject']);

        // Filters
        if ($request->filled('log_name')) {
            $query->where('log_name', $request->log_name);
        }

        if ($request->filled('causer_id')) {
            $query->where('causer_id', $request->causer_id);
        }

        if ($request->filled('start_date')) {
            $query->where('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('created_at', '<=', $request->end_date);
        }

        $logs = $query->orderBy('created_at', 'desc')
            ->paginate(50);

        $users = User::all();

        return view('admin.system-logs', compact('logs', 'users'));
    }

    /**
     * Show system settings.
     */
    public function systemSettings(): View
    {
        return view('admin.system-settings');
    }

    /**
     * Update system settings.
     */
    public function updateSystemSettings(Request $request): RedirectResponse
    {
        try {
            // Update configuration values
            $settings = $request->only([
                'app_name',
                'app_url',
                'mail_from_address',
                'mail_from_name',
                'notification_email',
                'backup_retention_days',
                'session_timeout_minutes'
            ]);

            foreach ($settings as $key => $value) {
                if ($value !== null) {
                    config([$key => $value]);
                }
            }

            // Log activity
            activity()
                ->causedBy(Auth::user())
                ->log('Configuración del sistema actualizada');

            return redirect()->back()
                ->with('success', 'Configuración del sistema actualizada correctamente.');

        } catch (\Exception $e) {
            Log::error('Error updating system settings: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al actualizar la configuración del sistema.');
        }
    }

    /**
     * Activate "View as Technician" mode
     */
    public function viewAsTechnician(): RedirectResponse
    {
        if (!auth()->check() || !auth()->user()->hasRole('super-admin')) {
            return redirect()->back()->with('error', 'No tienes permisos para esta acción.');
        }

        $user = auth()->user();
        $request = request();
        
        // Guardar en sesión de forma explícita y forzar guardado
        // Usar tanto request()->session() como session() helper para asegurar persistencia
        $request->session()->put('view_as_technician', true);
        session(['view_as_technician' => true]);
        
        // Forzar guardado inmediato
        $request->session()->save();
        
        // Verificar que se guardó correctamente
        $sessionValue = $request->session()->get('view_as_technician', false) || session('view_as_technician', false);
        
        Log::info('View as technician activated', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'session_id' => $request->session()->getId(),
            'view_as_technician_from_request' => $request->session()->get('view_as_technician', false),
            'view_as_technician_from_helper' => session('view_as_technician', false),
            'view_as_technician_final' => $sessionValue,
            'is_super_admin' => $user->hasRole('super-admin'),
            'all_session_keys' => array_keys($request->session()->all())
        ]);
        
        // Asegurar que la sesión persista
        if (!$sessionValue) {
            Log::error('Failed to set view_as_technician session', [
                'user_id' => $user->id,
                'session_id' => $request->session()->getId(),
                'session_driver' => config('session.driver'),
                'all_session_data' => $request->session()->all()
            ]);
            return redirect()->back()->with('error', 'Error al activar la vista de técnico. Intenta nuevamente.');
        }
        
        return redirect()->route('admin.technician-view.dashboard')
            ->with('success', 'Ahora estás viendo el sistema como técnico.');
    }

    /**
     * Deactivate "View as Technician" mode
     */
    public function stopViewingAsTechnician(): RedirectResponse
    {
        session()->forget('view_as_technician');
        
        return redirect()->route('admin.dashboard')
            ->with('success', 'Has vuelto a la vista de administrador.');
    }
}
