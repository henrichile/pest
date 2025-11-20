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
use App\Models\ScheduledReport;
use App\Services\PdfService;
use App\Services\NotificationService;
use App\Http\Requests\GenerateReportRequest;
use App\Http\Requests\ExportDataRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Permitir acceso a super-admin (el middleware role:super-admin se aplica en la ruta)
    }

    /**
     * Show reports index page with filters and statistics.
     */
    public function index(Request $request): View
    {
        // Obtener filtros del request
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        $serviceType = $request->input('service_type', 'all');
        $clientId = $request->input('client_id', 'all');
        $technicianId = $request->input('technician_id', 'all');
        $status = $request->input('status', 'all');
        
        // Construir query base
        $query = Service::with(['client', 'assignedUser']);
        
        // Si el usuario es técnico (no super-admin), solo mostrar sus servicios
        if (auth()->check() && auth()->user()->hasRole('technician') && !auth()->user()->hasRole('super-admin')) {
            $query->where('assigned_to', auth()->id());
        }
        
        // Aplicar filtros de fecha
        try {
            $query->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ]);
        } catch (\Exception $e) {
            // Si no hay created_at, usar scheduled_date
            try {
                $query->whereBetween('scheduled_date', [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay()
                ]);
            } catch (\Exception $e2) {
                // Si tampoco hay scheduled_date, no filtrar por fecha
            }
        }
        
        // Aplicar filtro de tipo de servicio
        if ($serviceType !== 'all') {
            $query->where('service_type', $serviceType);
        }
        
        // Aplicar filtro de cliente
        if ($clientId !== 'all') {
            $query->where('client_id', $clientId);
        }
        
        // Aplicar filtro de técnico
        if ($technicianId !== 'all') {
            $query->where('assigned_to', $technicianId);
        }
        
        // Aplicar filtro de estado
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        
        $services = $query->get();
        
        // ===== ESTADÍSTICAS =====
        $totalServices = $services->count();
        $completedServices = $services->where('status', 'finalizado')->count();
        $completedPercentage = $totalServices > 0 ? round(($completedServices / $totalServices) * 100, 1) : 0;
        
        // Ingresos del período (solo servicios completados con precio)
        $periodIncome = $services->where('status', 'finalizado')
            ->whereNotNull('price')
            ->where('price', '>', 0)
            ->sum('price');
        
        // Clientes únicos activos
        $uniqueClients = $services->pluck('client_id')->unique()->count();
        
        // Técnicos activos en el período
        $activeTechnicians = $services->whereNotNull('assigned_to')
            ->pluck('assigned_to')
            ->unique()
            ->count();
        
        // ===== GRÁFICOS =====
        
        // 1. Servicios por Estado (Gráfico de barras)
        $statusDistribution = $services->groupBy('status')->map->count();
        $statusLabels = $statusDistribution->keys()->map(function($status) {
            return strtoupper($status);
        })->toArray();
        $statusData = $statusDistribution->values()->toArray();
        
        // 2. Distribución por Tipo (Gráfico de pastel)
        $typeDistribution = $services->groupBy('service_type')->map->count();
        $typeLabels = $typeDistribution->keys()->map(function($type) {
            return ucfirst(str_replace('-', ' ', $type));
        })->toArray();
        $typeData = $typeDistribution->values()->toArray();
        $typeColors = [
            '#3b82f6', '#ef4444', '#f59e0b', '#8b5cf6', '#ec4899', '#22c55e', '#06b6d4', '#f97316'
        ];
        
        // 3. Evolución Temporal (Gráfico de líneas)
        $temporalData = [];
        $months = [];
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        
        // Generar meses entre las fechas
        $current = $start->copy()->startOfMonth();
        while ($current <= $end) {
            $monthKey = $current->format('Y-m');
            $months[] = $current->locale('es')->isoFormat('MMM YYYY');
            
            $monthServices = $services->filter(function($service) use ($current) {
                try {
                    $serviceDate = $service->created_at ?? $service->scheduled_date;
                    return $serviceDate && Carbon::parse($serviceDate)->format('Y-m') === $current->format('Y-m');
                } catch (\Exception $e) {
                    return false;
                }
            });
            
            $temporalData[] = [
                'total' => $monthServices->count(),
                'completed' => $monthServices->where('status', 'completed')->count(),
                'pending' => $monthServices->whereIn('status', ['pendiente', 'pending', 'in_progress'])->count(),
            ];
            
            $current->addMonth();
        }
        
        // ===== TOP 5 =====
        
        // Top 5 Clientes
        $topClients = $services->groupBy('client_id')
            ->map(function($clientServices) {
                return [
                    'client' => $clientServices->first()->client ?? null,
                    'count' => $clientServices->count()
                ];
            })
            ->filter(function($item) {
                return $item['client'] !== null;
            })
            ->sortByDesc('count')
            ->take(5)
            ->values();
        
        // Top 5 Técnicos
        $topTechnicians = $services->whereNotNull('assigned_to')
            ->groupBy('assigned_to')
            ->map(function($techServices) {
                return [
                    'technician' => $techServices->first()->assignedUser ?? null,
                    'count' => $techServices->count()
                ];
            })
            ->filter(function($item) {
                return $item['technician'] !== null;
            })
            ->sortByDesc('count')
            ->take(5)
            ->values();
        
        // ===== DATOS PARA FILTROS =====
        try {
            // Intentar ordenar por business_name primero
            $allClients = Client::orderBy('business_name')->get();
        } catch (\Exception $e) {
            try {
                // Si no existe business_name, intentar con name
                $allClients = Client::orderBy('name')->get();
            } catch (\Exception $e2) {
                // Si tampoco existe name, ordenar por ID
                try {
                    $allClients = Client::orderBy('id')->get();
                } catch (\Exception $e3) {
                    $allClients = collect();
                }
            }
        }
        $allTechnicians = User::whereHas('roles', function($q) {
            $q->where('name', 'technician');
        })->get();
        
        $serviceTypes = Service::distinct()->pluck('service_type')->filter()->map(function($type) {
            return [
                'value' => $type,
                'label' => ucfirst(str_replace('-', ' ', $type))
            ];
        })->values();
        
        return view('reports.index', compact(
            'startDate',
            'endDate',
            'serviceType',
            'clientId',
            'technicianId',
            'status',
            'totalServices',
            'completedServices',
            'completedPercentage',
            'periodIncome',
            'uniqueClients',
            'activeTechnicians',
            'statusLabels',
            'statusData',
            'typeLabels',
            'typeData',
            'typeColors',
            'months',
            'temporalData',
            'topClients',
            'topTechnicians',
            'allClients',
            'allTechnicians',
            'serviceTypes'
        ));
    }

    /**
     * Export report data based on current filters.
     */
    public function export(Request $request)
    {
        $format = $request->input('format', 'csv'); // csv, pdf, excel
        
        // Obtener los mismos filtros que en index()
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        $serviceType = $request->input('service_type', 'all');
        $clientId = $request->input('client_id', 'all');
        $technicianId = $request->input('technician_id', 'all');
        $status = $request->input('status', 'all');
        
        // Construir query base (mismo que en index)
        $query = Service::with(['client', 'assignedUser']);
        
        try {
            $query->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ]);
        } catch (\Exception $e) {
            try {
                $query->whereBetween('scheduled_date', [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay()
                ]);
            } catch (\Exception $e2) {
                // No filtrar por fecha
            }
        }
        
        if ($serviceType !== 'all') {
            $query->where('service_type', $serviceType);
        }
        if ($clientId !== 'all') {
            $query->where('client_id', $clientId);
        }
        if ($technicianId !== 'all') {
            $query->where('assigned_to', $technicianId);
        }
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        
        $services = $query->get();
        
        // Preparar datos para exportación
        $exportData = [];
        foreach ($services as $service) {
            $exportData[] = [
                'ID' => $service->id,
                'Cliente' => $service->client->business_name ?? $service->client->name ?? 'N/A',
                'Tipo de Servicio' => ucfirst(str_replace('-', ' ', $service->service_type ?? 'N/A')),
                'Fecha Programada' => $service->scheduled_date ? $service->scheduled_date->format('d/m/Y') : 'N/A',
                'Fecha Creación' => $service->created_at->format('d/m/Y H:i'),
                'Estado' => ucfirst($service->status ?? 'N/A'),
                'Prioridad' => ucfirst($service->priority ?? 'N/A'),
                'Técnico' => $service->assignedUser->name ?? 'Sin asignar',
                'Dirección' => $service->address ?? 'N/A',
                'Precio' => $service->price ? '$' . number_format($service->price, 2, ',', '.') : 'N/A',
                'Completado' => $service->completed_at ? $service->completed_at->format('d/m/Y H:i') : 'N/A',
            ];
        }
        
        if ($format === 'csv') {
            return $this->exportToCsv($exportData, $startDate, $endDate);
        } elseif ($format === 'pdf') {
            return $this->exportToPdf($services, $startDate, $endDate, $request->all());
        } else {
            return redirect()->back()->with('error', 'Formato no soportado');
        }
    }

    /**
     * Export to CSV.
     */
    private function exportToCsv(array $data, string $startDate, string $endDate)
    {
        $filename = 'reporte-servicios-' . $startDate . '-al-' . $endDate . '-' . now()->format('Y-m-d-His') . '.csv';
        $filepath = storage_path('app/exports/' . $filename);
        
        if (!file_exists(dirname($filepath))) {
            mkdir(dirname($filepath), 0755, true);
        }
        
        $file = fopen($filepath, 'w');
        
        // BOM para UTF-8 (Excel compatibility)
        fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
        
        if (!empty($data)) {
            // Headers
            fputcsv($file, array_keys($data[0]));
            
            // Data
            foreach ($data as $row) {
                fputcsv($file, $row);
            }
        }
        
        fclose($file);
        
        return response()->download($filepath, $filename)->deleteFileAfterSend(true);
    }

    /**
     * Export to PDF.
     */
    private function exportToPdf($services, string $startDate, string $endDate, array $filters)
    {
        try {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.export-pdf', compact('services', 'startDate', 'endDate', 'filters'))
                ->setPaper('a4', 'landscape')
                ->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true,
                    'defaultFont' => 'Arial',
                ]);
            
            $filename = 'reporte-servicios-' . $startDate . '-al-' . $endDate . '-' . now()->format('Y-m-d-His') . '.pdf';
            
            return $pdf->download($filename);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al generar PDF: ' . $e->getMessage());
        }
    }

    /**
     * Show scheduled reports.
     */
    public function scheduled(): View
    {
        $scheduledReports = ScheduledReport::with('creator')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('reports.scheduled', compact('scheduledReports'));
    }

    /**
     * Show form to create scheduled report.
     */
    public function createScheduled(): View
    {
        try {
            $allClients = Client::orderBy('business_name')->get();
        } catch (\Exception $e) {
            try {
                $allClients = Client::orderBy('name')->get();
            } catch (\Exception $e2) {
                $allClients = Client::orderBy('id')->get();
            }
        }
        
        $allTechnicians = User::whereHas('roles', function($q) {
            $q->where('name', 'technician');
        })->get();
        
        return view('reports.create-scheduled', compact('allClients', 'allTechnicians'));
    }

    /**
     * Store a new scheduled report.
     */
    public function storeScheduled(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:services,clients,technicians,financial',
            'format' => 'required|string|in:pdf,csv,excel',
            'frequency' => 'required|string|in:daily,weekly,monthly,quarterly,yearly',
            'recipients' => 'nullable|string',
            'filters' => 'nullable|array',
        ]);

        $scheduledReport = new ScheduledReport();
        $scheduledReport->name = $validated['name'];
        $scheduledReport->type = $validated['type'];
        $scheduledReport->format = $validated['format'];
        $scheduledReport->frequency = $validated['frequency'];
        $scheduledReport->created_by = auth()->id();
        
        // Procesar destinatarios (separados por comas)
        if ($request->filled('recipients')) {
            $recipients = array_map('trim', explode(',', $request->recipients));
            $scheduledReport->recipients = array_filter($recipients);
        }
        
        // Guardar filtros
        $filters = [];
        if ($request->filled('start_date')) {
            $filters['start_date'] = $request->start_date;
        }
        if ($request->filled('end_date')) {
            $filters['end_date'] = $request->end_date;
        }
        if ($request->filled('service_type') && $request->service_type !== 'all') {
            $filters['service_type'] = $request->service_type;
        }
        if ($request->filled('client_id') && $request->client_id !== 'all') {
            $filters['client_id'] = $request->client_id;
        }
        if ($request->filled('technician_id') && $request->technician_id !== 'all') {
            $filters['technician_id'] = $request->technician_id;
        }
        if ($request->filled('status') && $request->status !== 'all') {
            $filters['status'] = $request->status;
        }
        $scheduledReport->filters = $filters;
        
        $scheduledReport->calculateNextRun();
        $scheduledReport->save();

        return redirect()->route('admin.reports.scheduled')
            ->with('success', 'Reporte programado creado exitosamente');
    }

    /**
     * Show form to edit scheduled report.
     */
    public function editScheduled(ScheduledReport $scheduledReport): View
    {
        try {
            $allClients = Client::orderBy('business_name')->get();
        } catch (\Exception $e) {
            try {
                $allClients = Client::orderBy('name')->get();
            } catch (\Exception $e2) {
                $allClients = Client::orderBy('id')->get();
            }
        }
        
        $allTechnicians = User::whereHas('roles', function($q) {
            $q->where('name', 'technician');
        })->get();
        
        return view('reports.edit-scheduled', compact('scheduledReport', 'allClients', 'allTechnicians'));
    }

    /**
     * Update scheduled report.
     */
    public function updateScheduled(Request $request, ScheduledReport $scheduledReport): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:services,clients,technicians,financial',
            'format' => 'required|string|in:pdf,csv,excel',
            'frequency' => 'required|string|in:daily,weekly,monthly,quarterly,yearly',
            'recipients' => 'nullable|string',
            'filters' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $scheduledReport->name = $validated['name'];
        $scheduledReport->type = $validated['type'];
        $scheduledReport->format = $validated['format'];
        $scheduledReport->frequency = $validated['frequency'];
        $scheduledReport->is_active = $request->has('is_active') ? (bool)$request->is_active : false;
        
        // Procesar destinatarios
        if ($request->filled('recipients')) {
            $recipients = array_map('trim', explode(',', $request->recipients));
            $scheduledReport->recipients = array_filter($recipients);
        }
        
        // Guardar filtros
        $filters = [];
        if ($request->filled('start_date')) {
            $filters['start_date'] = $request->start_date;
        }
        if ($request->filled('end_date')) {
            $filters['end_date'] = $request->end_date;
        }
        if ($request->filled('service_type') && $request->service_type !== 'all') {
            $filters['service_type'] = $request->service_type;
        }
        if ($request->filled('client_id') && $request->client_id !== 'all') {
            $filters['client_id'] = $request->client_id;
        }
        if ($request->filled('technician_id') && $request->technician_id !== 'all') {
            $filters['technician_id'] = $request->technician_id;
        }
        if ($request->filled('status') && $request->status !== 'all') {
            $filters['status'] = $request->status;
        }
        $scheduledReport->filters = $filters;
        
        $scheduledReport->calculateNextRun();
        $scheduledReport->save();

        return redirect()->route('admin.reports.scheduled')
            ->with('success', 'Reporte programado actualizado exitosamente');
    }

    /**
     * Delete scheduled report.
     */
    public function destroyScheduled(ScheduledReport $scheduledReport): RedirectResponse
    {
        $scheduledReport->delete();

        return redirect()->route('admin.reports.scheduled')
            ->with('success', 'Reporte programado eliminado exitosamente');
    }

    /**
     * Toggle scheduled report active status.
     */
    public function toggleScheduled(ScheduledReport $scheduledReport): RedirectResponse
    {
        $scheduledReport->is_active = !$scheduledReport->is_active;
        $scheduledReport->calculateNextRun();
        $scheduledReport->save();

        return redirect()->route('admin.reports.scheduled')
            ->with('success', $scheduledReport->is_active ? 'Reporte activado' : 'Reporte desactivado');
    }

    /**
     * Show report configuration.
     */
    public function config(): View
    {
        return view('reports.config');
    }

    /**
     * Show reports dashboard.
     */
    public function dashboard(): View
    {
        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();
        $thisYear = Carbon::now()->startOfYear();
        
        // Work order statistics
        $totalWorkOrders = WorkOrder::count();
        $todayWorkOrders = WorkOrder::whereDate('scheduled_date', $today)->count();
        $thisWeekWorkOrders = WorkOrder::where('scheduled_date', '>=', $thisWeek)->count();
        $thisMonthWorkOrders = WorkOrder::where('scheduled_date', '>=', $thisMonth)->count();
        $thisYearWorkOrders = WorkOrder::where('scheduled_date', '>=', $thisYear)->count();
        
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
        
        return view('reports.dashboard', compact(
            'totalWorkOrders',
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
     * Generate work order report.
     */
    public function generateWorkOrderReport(GenerateReportRequest $request): RedirectResponse
    {
        try {
            $pdfService = app(PdfService::class);
            $pdfPath = $pdfService->generateWorkOrderReport($request->all());
            $downloadUrl = $pdfService->getDownloadUrl($pdfPath);

            return redirect($downloadUrl);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al generar el reporte: ' . $e->getMessage());
        }
    }

    /**
     * Generate performance report.
     */
    public function generatePerformanceReport(GenerateReportRequest $request): RedirectResponse
    {
        try {
            $pdfService = app(PdfService::class);
            $pdfPath = $pdfService->generatePerformanceReport($request->all());
            $downloadUrl = $pdfService->getDownloadUrl($pdfPath);

            return redirect($downloadUrl);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al generar el reporte: ' . $e->getMessage());
        }
    }

    /**
     * Generate material usage report.
     */
    public function generateMaterialUsageReport(GenerateReportRequest $request): RedirectResponse
    {
        try {
            $pdfService = app(PdfService::class);
            $pdfPath = $pdfService->generateMaterialUsageReport($request->all());
            $downloadUrl = $pdfService->getDownloadUrl($pdfPath);

            return redirect($downloadUrl);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al generar el reporte: ' . $e->getMessage());
        }
    }

    /**
     * Generate quality report.
     */
    public function generateQualityReport(GenerateReportRequest $request): RedirectResponse
    {
        try {
            $pdfService = app(PdfService::class);
            $pdfPath = $pdfService->generateQualityReport($request->all());
            $downloadUrl = $pdfService->getDownloadUrl($pdfPath);

            return redirect($downloadUrl);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al generar el reporte: ' . $e->getMessage());
        }
    }

    /**
     * Generate nonconformity report.
     */
    public function generateNonconformityReport(GenerateReportRequest $request): RedirectResponse
    {
        try {
            $pdfService = app(PdfService::class);
            $pdfPath = $pdfService->generateNonconformityReport($request->all());
            $downloadUrl = $pdfService->getDownloadUrl($pdfPath);

            return redirect($downloadUrl);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al generar el reporte: ' . $e->getMessage());
        }
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
     * Generate client report.
     */
    public function generateClientReport(GenerateReportRequest $request): RedirectResponse
    {
        try {
            $pdfService = app(PdfService::class);
            $pdfPath = $pdfService->generateClientReport($request->all());
            $downloadUrl = $pdfService->getDownloadUrl($pdfPath);

            return redirect($downloadUrl);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al generar el reporte: ' . $e->getMessage());
        }
    }

    /**
     * Generate technician report.
     */
    public function generateTechnicianReport(GenerateReportRequest $request): RedirectResponse
    {
        try {
            $pdfService = app(PdfService::class);
            $pdfPath = $pdfService->generateTechnicianReport($request->all());
            $downloadUrl = $pdfService->getDownloadUrl($pdfPath);

            return redirect($downloadUrl);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al generar el reporte: ' . $e->getMessage());
        }
    }

    /**
     * Generate service report.
     */
    public function generateServiceReport(GenerateReportRequest $request): RedirectResponse
    {
        try {
            $pdfService = app(PdfService::class);
            $pdfPath = $pdfService->generateServiceReport($request->all());
            $downloadUrl = $pdfService->getDownloadUrl($pdfPath);

            return redirect($downloadUrl);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al generar el reporte: ' . $e->getMessage());
        }
    }

    /**
     * Generate site report.
     */
    public function generateSiteReport(GenerateReportRequest $request): RedirectResponse
    {
        try {
            $pdfService = app(PdfService::class);
            $pdfPath = $pdfService->generateSiteReport($request->all());
            $downloadUrl = $pdfService->getDownloadUrl($pdfPath);

            return redirect($downloadUrl);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al generar el reporte: ' . $e->getMessage());
        }
    }

    /**
     * Generate pest report.
     */
    public function generatePestReport(GenerateReportRequest $request): RedirectResponse
    {
        try {
            $pdfService = app(PdfService::class);
            $pdfPath = $pdfService->generatePestReport($request->all());
            $downloadUrl = $pdfService->getDownloadUrl($pdfPath);

            return redirect($downloadUrl);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al generar el reporte: ' . $e->getMessage());
        }
    }

    /**
     * Generate treatment report.
     */
    public function generateTreatmentReport(GenerateReportRequest $request): RedirectResponse
    {
        try {
            $pdfService = app(PdfService::class);
            $pdfPath = $pdfService->generateTreatmentReport($request->all());
            $downloadUrl = $pdfService->getDownloadUrl($pdfPath);

            return redirect($downloadUrl);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al generar el reporte: ' . $e->getMessage());
        }
    }

    /**
     * Generate checklist report.
     */
    public function generateChecklistReport(GenerateReportRequest $request): RedirectResponse
    {
        try {
            $pdfService = app(PdfService::class);
            $pdfPath = $pdfService->generateChecklistReport($request->all());
            $downloadUrl = $pdfService->getDownloadUrl($pdfPath);

            return redirect($downloadUrl);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al generar el reporte: ' . $e->getMessage());
        }
    }

    /**
     * Generate financial report.
     */
    public function generateFinancialReport(GenerateReportRequest $request): RedirectResponse
    {
        try {
            $pdfService = app(PdfService::class);
            $pdfPath = $pdfService->generateFinancialReport($request->all());
            $downloadUrl = $pdfService->getDownloadUrl($pdfPath);

            return redirect($downloadUrl);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al generar el reporte: ' . $e->getMessage());
        }
    }

    /**
     * Generate compliance report.
     */
    public function generateComplianceReport(GenerateReportRequest $request): RedirectResponse
    {
        try {
            $pdfService = app(PdfService::class);
            $pdfPath = $pdfService->generateComplianceReport($request->all());
            $downloadUrl = $pdfService->getDownloadUrl($pdfPath);

            return redirect($downloadUrl);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al generar el reporte: ' . $e->getMessage());
        }
    }

    /**
     * Generate audit report.
     */
    public function generateAuditReport(GenerateReportRequest $request): RedirectResponse
    {
        try {
            $pdfService = app(PdfService::class);
            $pdfPath = $pdfService->generateAuditReport($request->all());
            $downloadUrl = $pdfService->getDownloadUrl($pdfPath);

            return redirect($downloadUrl);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al generar el reporte: ' . $e->getMessage());
        }
    }

    /**
     * Export data to CSV.
     */
    public function exportData(ExportDataRequest $request): RedirectResponse
    {
        try {
            $data = $this->getExportData($request->all());
            $filename = $this->generateExportFilename($request->all());
            $filepath = storage_path('app/exports/' . $filename);
            
            // Ensure directory exists
            if (!file_exists(dirname($filepath))) {
                mkdir(dirname($filepath), 0755, true);
            }
            
            // Write CSV file
            $file = fopen($filepath, 'w');
            
            if (!empty($data)) {
                // Write headers
                fputcsv($file, array_keys($data[0]));
                
                // Write data
                foreach ($data as $row) {
                    fputcsv($file, $row);
                }
            }
            
            fclose($file);
            
            // Return download URL
            $downloadUrl = route('reports.download-export', ['filename' => $filename]);
            
            return redirect($downloadUrl);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al exportar los datos: ' . $e->getMessage());
        }
    }

    /**
     * Download exported file.
     */
    public function downloadExport(string $filename): \Symfony\Component\HttpFoundation\Response
    {
        $filepath = storage_path('app/exports/' . $filename);
        
        if (!file_exists($filepath)) {
            abort(404, 'Archivo no encontrado.');
        }
        
        return response()->download($filepath)->deleteFileAfterSend(true);
    }

    /**
     * Get export data based on request parameters.
     */
    private function getExportData(array $params): array
    {
        $type = $params['type'] ?? 'work_orders';
        $startDate = $params['start_date'] ?? null;
        $endDate = $params['end_date'] ?? null;
        
        switch ($type) {
            case 'work_orders':
                return $this->getWorkOrdersExportData($startDate, $endDate);
            case 'materials':
                return $this->getMaterialsExportData($startDate, $endDate);
            case 'treatments':
                return $this->getTreatmentsExportData($startDate, $endDate);
            case 'checklists':
                return $this->getChecklistsExportData($startDate, $endDate);
            case 'nonconformities':
                return $this->getNonconformitiesExportData($startDate, $endDate);
            case 'clients':
                return $this->getClientsExportData();
            case 'sites':
                return $this->getSitesExportData();
            case 'services':
                return $this->getServicesExportData();
            case 'pests':
                return $this->getPestsExportData();
            case 'technicians':
                return $this->getTechniciansExportData();
            default:
                return [];
        }
    }

    /**
     * Generate export filename.
     */
    private function generateExportFilename(array $params): string
    {
        $type = $params['type'] ?? 'work_orders';
        $startDate = $params['start_date'] ?? null;
        $endDate = $params['end_date'] ?? null;
        $timestamp = now()->format('Y-m-d_H-i-s');
        
        $filename = "{$type}_{$timestamp}.csv";
        
        if ($startDate && $endDate) {
            $filename = "{$type}_{$startDate}_to_{$endDate}_{$timestamp}.csv";
        }
        
        return $filename;
    }

    /**
     * Get work orders export data.
     */
    private function getWorkOrdersExportData(?string $startDate, ?string $endDate): array
    {
        $query = WorkOrder::with(['client', 'site', 'service', 'assignedTechnicians.technician']);
        
        if ($startDate) {
            $query->where('scheduled_date', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->where('scheduled_date', '<=', $endDate);
        }
        
        $workOrders = $query->get();
        
        $data = [];
        foreach ($workOrders as $workOrder) {
            $data[] = [
                'folio' => $workOrder->folio,
                'client_name' => $workOrder->client->name,
                'site_name' => $workOrder->site->name,
                'service_name' => $workOrder->service->name,
                'status' => $workOrder->status,
                'scheduled_date' => $workOrder->scheduled_date,
                'started_at' => $workOrder->started_at,
                'completed_at' => $workOrder->completed_at,
                'technicians' => $workOrder->assignedTechnicians->pluck('technician.name')->join(', '),
                'notes' => $workOrder->notes,
            ];
        }
        
        return $data;
    }

    /**
     * Get materials export data.
     */
    private function getMaterialsExportData(?string $startDate, ?string $endDate): array
    {
        $query = MaterialMovement::with(['material', 'technician', 'workOrder']);
        
        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }
        
        $movements = $query->get();
        
        $data = [];
        foreach ($movements as $movement) {
            $data[] = [
                'material_name' => $movement->material->name,
                'movement_type' => $movement->movement_type,
                'quantity' => $movement->quantity,
                'technician_name' => $movement->technician->name ?? 'N/A',
                'work_order_folio' => $movement->workOrder->folio ?? 'N/A',
                'notes' => $movement->notes,
                'created_at' => $movement->created_at,
            ];
        }
        
        return $data;
    }

    /**
     * Get treatments export data.
     */
    private function getTreatmentsExportData(?string $startDate, ?string $endDate): array
    {
        $query = Treatment::with(['pest', 'material', 'technician', 'workOrder']);
        
        if ($startDate) {
            $query->where('applied_at', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->where('applied_at', '<=', $endDate);
        }
        
        $treatments = $query->get();
        
        $data = [];
        foreach ($treatments as $treatment) {
            $data[] = [
                'pest_name' => $treatment->pest->name,
                'material_name' => $treatment->material->name,
                'quantity_used' => $treatment->quantity_used,
                'application_method' => $treatment->application_method,
                'location' => $treatment->location,
                'technician_name' => $treatment->technician->name,
                'work_order_folio' => $treatment->workOrder->folio,
                'applied_at' => $treatment->applied_at,
                'notes' => $treatment->notes,
            ];
        }
        
        return $data;
    }

    /**
     * Get checklists export data.
     */
    private function getChecklistsExportData(?string $startDate, ?string $endDate): array
    {
        $query = ChecklistResponse::with(['checklistTemplate', 'technician', 'workOrder']);
        
        if ($startDate) {
            $query->where('submitted_at', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->where('submitted_at', '<=', $endDate);
        }
        
        $responses = $query->get();
        
        $data = [];
        foreach ($responses as $response) {
            $data[] = [
                'checklist_template_name' => $response->checklistTemplate->name,
                'status' => $response->status,
                'technician_name' => $response->technician->name,
                'work_order_folio' => $response->workOrder->folio,
                'submitted_at' => $response->submitted_at,
                'approved_at' => $response->approved_at,
                'approval_notes' => $response->approval_notes,
            ];
        }
        
        return $data;
    }

    /**
     * Get nonconformities export data.
     */
    private function getNonconformitiesExportData(?string $startDate, ?string $endDate): array
    {
        $query = Nonconformity::with(['technician', 'workOrder']);
        
        if ($startDate) {
            $query->where('reported_at', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->where('reported_at', '<=', $endDate);
        }
        
        $nonconformities = $query->get();
        
        $data = [];
        foreach ($nonconformities as $nonconformity) {
            $data[] = [
                'type' => $nonconformity->type,
                'description' => $nonconformity->description,
                'severity' => $nonconformity->severity,
                'status' => $nonconformity->status,
                'technician_name' => $nonconformity->technician->name,
                'work_order_folio' => $nonconformity->workOrder->folio,
                'reported_at' => $nonconformity->reported_at,
                'resolved_at' => $nonconformity->resolved_at,
                'resolution' => $nonconformity->resolution,
            ];
        }
        
        return $data;
    }

    /**
     * Get clients export data.
     */
    private function getClientsExportData(): array
    {
        $clients = Client::all();
        
        $data = [];
        foreach ($clients as $client) {
            $data[] = [
                'name' => $client->name,
                'rut' => $client->rut,
                'email' => $client->email,
                'phone' => $client->phone,
                'address' => $client->address,
                'city' => $client->city,
                'region' => $client->region,
                'country' => $client->country,
                'postal_code' => $client->postal_code,
                'business_type' => $client->business_type,
                'industry' => $client->industry,
                'is_active' => $client->is_active ? 'Sí' : 'No',
                'created_at' => $client->created_at,
            ];
        }
        
        return $data;
    }

    /**
     * Get sites export data.
     */
    private function getSitesExportData(): array
    {
        $sites = Site::with('client')->get();
        
        $data = [];
        foreach ($sites as $site) {
            $data[] = [
                'name' => $site->name,
                'client_name' => $site->client->name,
                'address' => $site->address,
                'city' => $site->city,
                'region' => $site->region,
                'country' => $site->country,
                'postal_code' => $site->postal_code,
                'type' => $site->type,
                'industry' => $site->industry,
                'risk_level' => $site->risk_level,
                'sensitivity' => $site->sensitivity,
                'is_active' => $site->is_active ? 'Sí' : 'No',
                'created_at' => $site->created_at,
            ];
        }
        
        return $data;
    }

    /**
     * Get services export data.
     */
    private function getServicesExportData(): array
    {
        $services = Service::all();
        
        $data = [];
        foreach ($services as $service) {
            $data[] = [
                'name' => $service->name,
                'description' => $service->description,
                'category' => $service->category,
                'is_active' => $service->is_active ? 'Sí' : 'No',
                'created_at' => $service->created_at,
            ];
        }
        
        return $data;
    }

    /**
     * Get pests export data.
     */
    private function getPestsExportData(): array
    {
        $pests = Pest::all();
        
        $data = [];
        foreach ($pests as $pest) {
            $data[] = [
                'name' => $pest->name,
                'scientific_name' => $pest->scientific_name,
                'description' => $pest->description,
                'control_methods' => $pest->control_methods,
                'is_active' => $pest->is_active ? 'Sí' : 'No',
                'created_at' => $pest->created_at,
            ];
        }
        
        return $data;
    }

    /**
     * Get technicians export data.
     */
    private function getTechniciansExportData(): array
    {
        $technicians = User::role('technician')->get();
        
        $data = [];
        foreach ($technicians as $technician) {
            $data[] = [
                'name' => $technician->name,
                'email' => $technician->email,
                'phone' => $technician->phone,
                'is_active' => $technician->is_active ? 'Sí' : 'No',
                'created_at' => $technician->created_at,
            ];
        }
        
        return $data;
    }
}
