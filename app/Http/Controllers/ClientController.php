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
use App\Http\Requests\CreateSiteRequest;
use App\Http\Requests\UpdateSiteRequest;
use App\Http\Requests\CreateWorkOrderRequest;
use App\Http\Requests\UpdateWorkOrderRequest;
use App\Http\Requests\RateWorkOrderRequest;
use App\Http\Requests\GenerateReportRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ClientController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // El middleware de rol se aplicará solo en métodos específicos
        $this->middleware('role:client')->except(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);
    }
    
    /**
     * Display a listing of clients (for admin).
     */
    public function index(Request $request): View
    {
        // Si el usuario es admin, usar AdminController logic
        if (Auth::user()->hasRole('super-admin')) {
            $query = Client::query();

            // Filters
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('rut', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            // Nota: is_active no existe en la tabla de producción
            // if ($request->filled('is_active')) {
            //     $query->where('is_active', $request->is_active);
            // }

            $clients = $query->orderBy('name')
                ->paginate(20);

            return view('admin.clients', compact('clients'));
        }
        
        // Si es cliente, redirigir a su dashboard
        abort(403, 'No tienes acceso a esta página.');
    }

    /**
     * Show the form for creating a new client.
     */
    public function create(): View
    {
        // Solo super-admin puede crear clientes
        if (!Auth::user()->hasRole('super-admin')) {
            abort(403, 'No tienes permisos para acceder a esta página');
        }
        
        return view('admin.clients.create');
    }

    /**
     * Store a newly created client.
     */
    public function store(\App\Http\Requests\Api\CreateClientRequest $request): RedirectResponse
    {
        // Solo super-admin puede crear clientes
        if (!Auth::user()->hasRole('super-admin')) {
            abort(403, 'No tienes permisos para acceder a esta página');
        }

        try {
            DB::beginTransaction();

            $data = $request->validated();
            
            // Preparar datos para crear el cliente (solo campos que existen en la BD)
            $clientData = [
                'name' => $data['name'],
                'rut' => $data['rut'],
                'email' => $data['email'] ?? '',
                'phone' => $data['phone'],
                'address' => $data['address'],
                'business_type' => $data['business_type'] ?? null,
                'contact_person' => $data['contact_person'] ?? null,
            ];

            $client = Client::create($clientData);

            // Log activity
            activity()
                ->performedOn($client)
                ->causedBy(Auth::user())
                ->log('Cliente creado');

            DB::commit();

            return redirect()->route('admin.clients.index')
                ->with('success', 'Cliente creado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating client: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear el cliente: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified client.
     */
    public function edit(Client $client): View
    {
        // Solo super-admin puede editar clientes
        if (!Auth::user()->hasRole('super-admin')) {
            abort(403, 'No tienes permisos para acceder a esta página');
        }
        
        return view('admin.clients.edit', compact('client'));
    }

    /**
     * Update the specified client in storage.
     */
    public function update(\App\Http\Requests\Api\UpdateClientRequest $request, Client $client): RedirectResponse
    {
        // Solo super-admin puede actualizar clientes
        if (!Auth::user()->hasRole('super-admin')) {
            abort(403, 'No tienes permisos para acceder a esta página');
        }

        try {
            DB::beginTransaction();

            $data = $request->validated();
            
            // Preparar datos para actualizar el cliente (solo campos que existen en la BD)
            $clientData = [
                'name' => $data['name'],
                'rut' => $data['rut'],
                'email' => $data['email'] ?? '',
                'phone' => $data['phone'],
                'address' => $data['address'],
                'business_type' => $data['business_type'] ?? null,
                'contact_person' => $data['contact_person'] ?? null,
            ];

            $client->update($clientData);

            // Log activity
            activity()
                ->performedOn($client)
                ->causedBy(Auth::user())
                ->log('Cliente actualizado');

            DB::commit();

            return redirect()->route('admin.clients.index')
                ->with('success', 'Cliente actualizado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating client: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar el cliente: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified client.
     */
    public function show(Client $client): View
    {
        // Solo super-admin puede ver clientes
        if (!Auth::user()->hasRole('super-admin')) {
            abort(403, 'No tienes permisos para acceder a esta página');
        }
        
        $client->load(['sites', 'workOrders.service']);
        
        return view('admin.clients-show', compact('client'));
    }

    /**
     * Remove the specified client from storage.
     */
    public function destroy(Client $client): RedirectResponse
    {
        // Solo super-admin puede eliminar clientes
        if (!Auth::user()->hasRole('super-admin')) {
            abort(403, 'No tienes permisos para acceder a esta página');
        }

        try {
            DB::beginTransaction();

            // Verificar si el cliente tiene servicios asociados
            $servicesCount = Service::where('client_id', $client->id)->count();
            if ($servicesCount > 0) {
                return redirect()->back()
                    ->with('error', 'No se puede eliminar el cliente porque tiene servicios asociados.');
            }

            // Log activity
            activity()
                ->performedOn($client)
                ->causedBy(Auth::user())
                ->log('Cliente eliminado: ' . $client->name);

            $client->delete();

            DB::commit();

            return redirect()->route('admin.clients.index')
                ->with('success', 'Cliente eliminado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting client: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Error al eliminar el cliente: ' . $e->getMessage());
        }
    }

    /**
     * Show client dashboard.
     */
    public function dashboard(): View
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
        $todayWorkOrders = WorkOrder::where('client_id', $client->id)
            ->whereDate('scheduled_date', $today)->count();
        $thisWeekWorkOrders = WorkOrder::where('client_id', $client->id)
            ->where('scheduled_date', '>=', $thisWeek)->count();
        $thisMonthWorkOrders = WorkOrder::where('client_id', $client->id)
            ->where('scheduled_date', '>=', $thisMonth)->count();
        
        // Status distribution
        $statusDistribution = WorkOrder::where('client_id', $client->id)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');
        
        // Service distribution
        $serviceDistribution = WorkOrder::where('client_id', $client->id)
            ->selectRaw('s.name as service_name, COUNT(*) as count')
            ->join('services as s', 'work_orders.service_id', '=', 's.id')
            ->groupBy('s.id', 's.name')
            ->pluck('count', 'service_name');
        
        // Recent work orders
        $recentWorkOrders = WorkOrder::where('client_id', $client->id)
            ->with(['site', 'service', 'assignedTechnicians.technician'])
            ->orderBy('scheduled_date', 'desc')
            ->limit(10)
            ->get();
        
        // Upcoming work orders
        $upcomingWorkOrders = WorkOrder::where('client_id', $client->id)
            ->where('scheduled_date', '>=', $today)
            ->whereIn('status', ['scheduled', 'assigned'])
            ->with(['site', 'service', 'assignedTechnicians.technician'])
            ->orderBy('scheduled_date')
            ->limit(10)
            ->get();
        
        // Sites
        $sites = $client->sites()->where('is_active', true)->get();
        
        // Services
        $services = Service::where('is_active', true)->get();
        
        // Quality metrics
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
        
        // Nonconformities
        $openNonconformities = Nonconformity::whereHas('workOrder', function ($query) use ($client) {
                $query->where('client_id', $client->id);
            })
            ->where('status', 'open')
            ->with(['workOrder.site', 'technician'])
            ->orderBy('reported_at', 'desc')
            ->limit(10)
            ->get();
        
        return view('client.dashboard', compact(
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
     * Show client profile.
     */
    public function profile(): View
    {
        $user = Auth::user();
        $client = $user->client;
        
        if (!$client) {
            abort(403, 'Usuario no asociado a un cliente.');
        }

        $client->load(['sites', 'workOrders.service']);

        return view('client.profile', compact('client'));
    }

    /**
     * Update client profile.
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $client = $user->client;
        
        if (!$client) {
            abort(403, 'Usuario no asociado a un cliente.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'region' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'business_type' => 'nullable|string|max:100',
            'industry' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $client->update($request->only([
                'name',
                'email',
                'phone',
                'address',
                'city',
                'region',
                'country',
                'postal_code',
                'business_type',
                'industry',
                'notes'
            ]));

            // Log activity
            activity()
                ->performedOn($client)
                ->causedBy($user)
                ->log('Perfil de cliente actualizado');

            DB::commit();

            return redirect()->back()
                ->with('success', 'Perfil actualizado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating client profile: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al actualizar el perfil.');
        }
    }

    /**
     * Show sites management.
     */
    public function sites(Request $request): View
    {
        $user = Auth::user();
        $client = $user->client;
        
        if (!$client) {
            abort(403, 'Usuario no asociado a un cliente.');
        }

        $query = $client->sites();

        // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $sites = $query->orderBy('name')
            ->paginate(20);

        return view('client.sites', compact('sites'));
    }

    /**
     * Show site details.
     */
    public function showSite(Site $site): View
    {
        $user = Auth::user();
        $client = $user->client;
        
        if (!$client || $site->client_id !== $client->id) {
            abort(403, 'No tienes acceso a este sitio.');
        }

        $site->load(['workOrders.service', 'workOrders.assignedTechnicians.technician']);
        
        $workOrders = $site->workOrders()->with(['service', 'assignedTechnicians.technician'])->paginate(20);

        return view('client.site', compact('site', 'workOrders'));
    }

    /**
     * Create new site.
     */
    public function createSite(CreateSiteRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $client = $user->client;
        
        if (!$client) {
            abort(403, 'Usuario no asociado a un cliente.');
        }

        try {
            DB::beginTransaction();

            $site = Site::create(array_merge($request->validated(), [
                'client_id' => $client->id
            ]));

            // Log activity
            activity()
                ->performedOn($site)
                ->causedBy($user)
                ->log('Sitio creado');

            DB::commit();

            return redirect()->route('client.sites')
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
        $user = Auth::user();
        $client = $user->client;
        
        if (!$client || $site->client_id !== $client->id) {
            abort(403, 'No tienes acceso a este sitio.');
        }

        try {
            DB::beginTransaction();

            $site->update($request->validated());

            // Log activity
            activity()
                ->performedOn($site)
                ->causedBy($user)
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
     * Show work orders list.
     */
    public function workOrders(Request $request): View
    {
        $user = Auth::user();
        $client = $user->client;
        
        if (!$client) {
            abort(403, 'Usuario no asociado a un cliente.');
        }

        $query = WorkOrder::where('client_id', $client->id)
            ->with(['site', 'service', 'assignedTechnicians.technician']);

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('site_id')) {
            $query->where('site_id', $request->site_id);
        }

        if ($request->filled('service_id')) {
            $query->where('service_id', $request->service_id);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('scheduled_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('scheduled_date', '<=', $request->end_date);
        }

        $workOrders = $query->orderBy('scheduled_date', 'desc')
            ->paginate(20);

        $sites = $client->sites()->where('is_active', true)->get();
        $services = Service::where('is_active', true)->get();

        return view('client.work-orders', compact('workOrders', 'sites', 'services'));
    }

    /**
     * Show work order details.
     */
    public function showWorkOrder(WorkOrder $workOrder): View
    {
        $user = Auth::user();
        $client = $user->client;
        
        if (!$client || $workOrder->client_id !== $client->id) {
            abort(403, 'No tienes acceso a esta orden de trabajo.');
        }

        $workOrder->load([
            'client',
            'site',
            'service',
            'assignedTechnicians.technician',
            'sessions',
            'treatments.material',
            'checklistResponses.items.checklistItem',
            'nonconformities',
            'ratings'
        ]);

        $checklistTemplate = ChecklistTemplate::where('service_id', $workOrder->service_id)
            ->where('site_type', $workOrder->site->type)
            ->first();

        if ($checklistTemplate) {
            $checklistTemplate->load('items');
        }

        return view('client.work-order', compact('workOrder', 'checklistTemplate'));
    }

    /**
     * Create new work order request.
     */
    public function createWorkOrder(CreateWorkOrderRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $client = $user->client;
        
        if (!$client) {
            abort(403, 'Usuario no asociado a un cliente.');
        }

        try {
            DB::beginTransaction();

            $workOrder = WorkOrder::create(array_merge($request->validated(), [
                'client_id' => $client->id,
                'status' => 'pending',
                'folio' => $this->generateFolio(),
            ]));

            // Log activity
            activity()
                ->performedOn($workOrder)
                ->causedBy($user)
                ->log('Solicitud de orden de trabajo creada');

            // Send notification to supervisors
            $notificationService = app(NotificationService::class);
            $notificationService->notifyWorkOrderRequested($workOrder);

            DB::commit();

            return redirect()->route('client.work-orders')
                ->with('success', 'Solicitud de orden de trabajo creada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating work order: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al crear la solicitud de orden de trabajo.');
        }
    }

    /**
     * Update work order request.
     */
    public function updateWorkOrder(UpdateWorkOrderRequest $request, WorkOrder $workOrder): RedirectResponse
    {
        $user = Auth::user();
        $client = $user->client;
        
        if (!$client || $workOrder->client_id !== $client->id) {
            abort(403, 'No tienes acceso a esta orden de trabajo.');
        }

        // Only allow updates if work order is pending or scheduled
        if (!in_array($workOrder->status, ['pending', 'scheduled'])) {
            return redirect()->back()
                ->with('error', 'No se puede modificar una orden de trabajo en progreso o completada.');
        }

        try {
            DB::beginTransaction();

            $workOrder->update($request->validated());

            // Log activity
            activity()
                ->performedOn($workOrder)
                ->causedBy($user)
                ->log('Solicitud de orden de trabajo actualizada');

            DB::commit();

            return redirect()->back()
                ->with('success', 'Solicitud de orden de trabajo actualizada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating work order: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al actualizar la solicitud de orden de trabajo.');
        }
    }

    /**
     * Rate work order.
     */
    public function rateWorkOrder(RateWorkOrderRequest $request, WorkOrder $workOrder): RedirectResponse
    {
        $user = Auth::user();
        $client = $user->client;
        
        if (!$client || $workOrder->client_id !== $client->id) {
            abort(403, 'No tienes acceso a esta orden de trabajo.');
        }

        // Only allow rating if work order is completed
        if ($workOrder->status !== 'completed') {
            return redirect()->back()
                ->with('error', 'Solo se puede calificar una orden de trabajo completada.');
        }

        try {
            DB::beginTransaction();

            $rating = $workOrder->ratings()->create([
                'client_id' => $client->id,
                'rating' => $request->rating,
                'comment' => $request->comment,
                'rated_at' => now(),
            ]);

            // Log activity
            activity()
                ->performedOn($workOrder)
                ->causedBy($user)
                ->log('Orden de trabajo calificada');

            DB::commit();

            return redirect()->back()
                ->with('success', 'Orden de trabajo calificada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error rating work order: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al calificar la orden de trabajo.');
        }
    }

    /**
     * Generate work order report.
     */
    public function generateWorkOrderReport(WorkOrder $workOrder): RedirectResponse
    {
        $user = Auth::user();
        $client = $user->client;
        
        if (!$client || $workOrder->client_id !== $client->id) {
            abort(403, 'No tienes acceso a esta orden de trabajo.');
        }

        try {
            $pdfService = app(PdfService::class);
            $pdfPath = $pdfService->generateWorkOrderReport($workOrder);
            $downloadUrl = $pdfService->getDownloadUrl($pdfPath);

            return redirect($downloadUrl);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al generar el reporte: ' . $e->getMessage());
        }
    }

    /**
     * Generate client report.
     */
    public function generateClientReport(GenerateReportRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $client = $user->client;
        
        if (!$client) {
            abort(403, 'Usuario no asociado a un cliente.');
        }

        try {
            $pdfService = app(PdfService::class);
            $pdfPath = $pdfService->generateClientReport($client, $request->all());
            $downloadUrl = $pdfService->getDownloadUrl($pdfPath);

            return redirect($downloadUrl);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al generar el reporte: ' . $e->getMessage());
        }
    }

    /**
     * Show quality metrics.
     */
    public function qualityMetrics(Request $request): View
    {
        $user = Auth::user();
        $client = $user->client;
        
        if (!$client) {
            abort(403, 'Usuario no asociado a un cliente.');
        }

        $query = ChecklistResponse::whereHas('workOrder', function ($q) use ($client) {
            $q->where('client_id', $client->id);
        });

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->where('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('created_at', '<=', $request->end_date);
        }

        $metrics = $query->selectRaw('
                status,
                COUNT(*) as count,
                AVG(CASE WHEN status = "completed" THEN 1 ELSE 0 END) * 100 as completion_rate
            ')
            ->groupBy('status')
            ->get();

        // Additional quality metrics
        $totalChecklists = ChecklistResponse::whereHas('workOrder', function ($q) use ($client) {
            $q->where('client_id', $client->id);
        })->count();

        $completedChecklists = ChecklistResponse::whereHas('workOrder', function ($q) use ($client) {
            $q->where('client_id', $client->id);
        })->where('status', 'completed')->count();

        $approvedChecklists = ChecklistResponse::whereHas('workOrder', function ($q) use ($client) {
            $q->where('client_id', $client->id);
        })->where('status', 'approved')->count();

        $overallCompletionRate = $totalChecklists > 0 ? ($completedChecklists / $totalChecklists) * 100 : 0;
        $overallApprovalRate = $completedChecklists > 0 ? ($approvedChecklists / $completedChecklists) * 100 : 0;

        return view('client.quality-metrics', compact(
            'metrics',
            'totalChecklists',
            'completedChecklists',
            'approvedChecklists',
            'overallCompletionRate',
            'overallApprovalRate'
        ));
    }

    /**
     * Show nonconformities.
     */
    public function nonconformities(Request $request): View
    {
        $user = Auth::user();
        $client = $user->client;
        
        if (!$client) {
            abort(403, 'Usuario no asociado a un cliente.');
        }

        $query = Nonconformity::whereHas('workOrder', function ($q) use ($client) {
            $q->where('client_id', $client->id);
        })->with(['workOrder.site', 'technician']);

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

        return view('client.nonconformities', compact('nonconformities'));
    }

    /**
     * Generate unique folio for work order.
     */
    private function generateFolio(): string
    {
        do {
            $folio = 'OT-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (WorkOrder::where('folio', $folio)->exists());

        return $folio;
    }
}
