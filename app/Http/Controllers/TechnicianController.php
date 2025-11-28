<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Helpers\ImageHelper;

class TechnicianController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        
        // Detectar si estamos en modo technician-view (igual que en services())
        $isViewingAsTechnician = session('view_as_technician', false) && $user->hasRole('super-admin');
        $isTechnicianViewRoute = request()->is('admin/technician-view/*') || 
                                 request()->routeIs('technician-view.*');
        
        // Si está en modo "view_as_technician" o en ruta technician-view, mostrar todos los servicios
        // Si es un técnico real, filtrar por técnico asignado
        if ($isViewingAsTechnician || $isTechnicianViewRoute) {
            $query = Service::query();
        } else {
            $query = Service::where('assigned_to', $user->id);
        }

        // Servicios finalizados (todos los finalizados, no solo los de hoy)
        $finalizedServices = (clone $query)
            ->where('status', 'finalizado')
            ->count();

        // Servicios pendientes
        $pendingServices = (clone $query)
            ->where('status', 'pendiente')
            ->count();

        // Servicios en progreso
        $inProgressServices = (clone $query)
            ->where('status', 'en_progreso')
            ->count();

        // Servicios vencidos
        $overdueServices = (clone $query)
            ->where('status', 'pendiente')
            ->where('scheduled_date', '<', now())
            ->count();

        // Próximos servicios asignados (pendientes y en progreso)
        $assignedServices = (clone $query)
            ->whereIn('status', ['pendiente', 'en_progreso'])
            ->with(['client', 'serviceType'])
            ->orderBy('scheduled_date', 'asc')
            ->limit(5)
            ->get();

        return view('technician.dashboard', compact(
            'finalizedServices',
            'pendingServices',
            'inProgressServices',
            'overdueServices',
            'assignedServices'
        ));
    }

    public function services(Request $request)
    {
        $user = auth()->user();
        $isViewingAsTechnician = session('view_as_technician', false) && $user->hasRole('super-admin');
        
        // Construir query base
        if ($isViewingAsTechnician) {
            // Mostrar todos los servicios para que el admin pueda ver el flujo completo
            $query = Service::with(['client', 'serviceType', 'assignedUser']);
        } else {
            // Filtrar por técnico asignado
            $query = Service::where('assigned_to', auth()->id())
                ->with(['client', 'serviceType', 'assignedUser']);
        }
        
        // Aplicar filtros
        if ($request->filled('estado')) {
            $query->where('status', $request->estado);
        }
        
        if ($request->filled('tipo')) {
            $query->where('service_type', $request->tipo);
        }
        
        // Ordenar y paginar
        $services = $query->orderBy('created_at', 'desc')->paginate(10);
        
        // Mantener los parámetros de filtro en la paginación
        $services->appends($request->query());

        // Detectar si estamos en modo technician-view
        $isTechnicianView = request()->is('admin/technician-view/*') || 
                           request()->routeIs('technician-view.*') ||
                           (session('view_as_technician', false) && auth()->check() && auth()->user()->hasRole('super-admin'));

        return view('technician.services', compact('services', 'isTechnicianView'));
    }

    public function profile()
    {
        $user = auth()->user();
        return view("technician.profile", compact("user"));
    }

    public function showServiceDetail(Service $service)
    {
        // Verificar que el servicio pertenece al técnico autenticado
        if ($service->assigned_to !== auth()->id() && !auth()->user()->hasRole("super-admin")) {
            abort(403, 'No tienes permisos para ver este servicio');
        }

        $service->load(['client', 'serviceType', 'assignedUser']);
        
        // Detectar si estamos en modo technician-view
        $isTechnicianView = request()->is('admin/technician-view/*') || 
                           request()->routeIs('technician-view.*') ||
                           (session('view_as_technician', false) && auth()->check() && auth()->user()->hasRole('super-admin'));

        return view('technician.service-detail', compact('service', 'isTechnicianView'));
    }

    public function showChecklistDetails(Service $service)
    {
        // Verificar permisos
        if ($service->assigned_to !== auth()->id() && !auth()->user()->hasRole("super-admin")) {
            abort(403, "No tienes permisos para ver este servicio");
        }

        $service->load(['client', 'serviceType', 'assignedUser']);

        // Obtener datos del checklist
        $checklistData = $service->checklist_data ?? [];

        // Preparar datos para la vista
        $checklistStages = [
            'points' => $checklistData['points'] ?? [],
            'products' => $checklistData['products'] ?? [],
            'results' => $checklistData['results'] ?? [],
            'observations' => $checklistData['observations'] ?? [],
            'sites' => $checklistData['sites'] ?? [],
            'description' => $checklistData['description'] ?? ''
        ];

        return view('technician.service-checklist-details', compact('service', 'checklistStages'));
    }

    public function startService(Service $service)
    {
        // Si es GET, redirigir al formulario POST
        if (request()->isMethod('GET')) {
            // Usar route() con el nombre correcto
            try {
                return redirect('/admin/technician-view/services/' . $service->id . '/detail')
                    ->with('error', 'Por favor, usa el botón "Iniciar Servicio" para iniciar el servicio.');
            } catch (\Exception $e) {
                // Fallback si la ruta no existe
                return redirect('/admin/technician-view/services/' . $service->id . '/detail')
                    ->with('error', 'Por favor, usa el botón "Iniciar Servicio" para iniciar el servicio.');
            }
        }

        // Verificar permisos
        if ($service->assigned_to !== auth()->id() && !auth()->user()->hasRole("super-admin")) {
            abort(403, "No tienes permisos para iniciar este servicio");
        }

        // Verificar estado del servicio
        if ($service->status !== "pendiente" && $service->status !== "en_progreso") {
            return redirect()->back()->with("error", "Este servicio no puede ser iniciado");
        }

        // Detectar si estamos en modo technician-view (admin viendo como técnico)
        $isTechnicianView = request()->is('admin/technician-view/*') || 
                           request()->routeIs('technician-view.*') ||
                           (request()->header('referer') && strpos(request()->header('referer'), '/admin/technician-view/') !== false);

        // Redirigir a página profesional de captura de geolocalización
        // Usar URL directa para evitar problemas con route()
        if ($isTechnicianView) {
            return redirect('/admin/technician-view/services/' . $service->id . '/checklist/location');
        }
        
        return redirect('/technician/services/' . $service->id . '/checklist/location');
    }

    public function showLocationCapture(Service $service)
    {
        // Verificar permisos
        if ($service->assigned_to !== auth()->id() && !auth()->user()->hasRole("super-admin")) {
            abort(403, "No tienes permisos para acceder a este servicio");
        }

        // Verificar estado del servicio
        if ($service->status !== "pendiente" && $service->status !== "en_progreso") {
            return redirect()->back()->with("error", "Este servicio no puede ser iniciado");
        }

        // Detectar si estamos en modo technician-view
        $isTechnicianView = request()->is('admin/technician-view/*') || 
                           request()->routeIs('technician-view.*');

        return view("technician.capture-location-simple", compact("service", "isTechnicianView"));
    }

    public function captureLocation(Service $service)
    {
        // Verificar permisos
        if ($service->assigned_to !== auth()->id() && !auth()->user()->hasRole("super-admin")) {
            abort(403, "No tienes permisos para acceder a este servicio");
        }

        // Verificar estado del servicio
        if ($service->status !== "pendiente" && $service->status !== "en_progreso") {
            return redirect()->back()->with("error", "Este servicio no puede ser iniciado");
        }

        return view("technician.capture-location-alternative", compact("service"));
    }

    public function processLocation(Request $request, Service $service)
    {
        // Verificar permisos
        if ($service->assigned_to !== auth()->id() && !auth()->user()->hasRole("super-admin")) {
            abort(403, "No tienes permisos para acceder a este servicio");
        }

        // Verificar estado del servicio
        if ($service->status !== "pendiente" && $service->status !== "en_progreso") {
            return redirect()->back()->with("error", "Este servicio no puede ser iniciado");
        }

        // Validar datos de ubicación
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'address' => 'required|string|max:255',
            'location_accuracy' => 'nullable|numeric',
        ]);

        // Actualizar servicio con ubicación y cambiar estado
        $service->update([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'location_accuracy' => $request->location_accuracy,
            'location_captured_at' => now(),
            'address' => $request->address,
            'status' => 'en_progreso',
            'started_at' => now(),
        ]);

        // Definir la siguiente etapa del checklist basada en service_type
        switch ($service->service_type) {
            case 'servicios-especiales':
                $nextStage = "observations";
                break;
            case 'fumigacion-de-jardines':
                $nextStage = "points";
                break;
            case 'desinfeccion':
                $nextStage = "products";
                break;
            case 'desratizacion':
                $nextStage = "products";
                break;
            case 'desinsectacion':
                $nextStage = "products";
                break;
            case 'monitoreo-cebaderas':
                $nextStage = "monitoreo-datos";
                break;
            default:
                $nextStage = "points";
                break;
        }

        // Detectar si estamos en modo technician-view
        $isTechnicianView = request()->is('admin/technician-view/*') || 
                           request()->routeIs('technician-view.*') ||
                           (request()->header('referer') && strpos(request()->header('referer'), '/admin/technician-view/') !== false);

        if ($isTechnicianView) {
            return redirect('/admin/technician-view/services/' . $service->id . '/checklist/' . $nextStage)
                ->with('success', 'Ubicación capturada correctamente. Puedes comenzar el checklist.');
        }

        return redirect('/technician/services/' . $service->id . '/checklist/' . $nextStage)
            ->with('success', 'Ubicación capturada correctamente. Puedes comenzar el checklist.');
    }

    public function showChecklist(Service $service)
    {
        // Verificar permisos
        if ($service->assigned_to !== auth()->id() && !auth()->user()->hasRole("super-admin")) {
            abort(403, "No tienes permisos para acceder a este servicio");
        }

        // Verificar estado del servicio
        if ($service->status !== "en_progreso") {
            return redirect()->back()->with("error", "Este servicio debe estar en progreso para realizar el checklist");
        }

        // Detectar si estamos en modo technician-view
        $isTechnicianView = request()->is('admin/technician-view/*') || 
                           request()->routeIs('technician-view.*');

        // Verificar que la ubicación haya sido capturada
        if (empty($service->latitude) || empty($service->longitude) || !$service->location_captured_at) {
            if ($isTechnicianView) {
                return redirect('/admin/technician-view/services/' . $service->id . '/checklist/location')
                    ->with('warning', 'Debes capturar la ubicación antes de iniciar el checklist.');
            }
            return redirect('/technician/services/' . $service->id . '/checklist/location')
                ->with('warning', 'Debes capturar la ubicación antes de iniciar el checklist.');
        }

        $service->load(['client', 'serviceType']);

        // Si no hay etapa definida, redirigir a la primera etapa
        if (empty($service->checklist_stage)) {
            $firstStage = $this->getFirstStage($service->service_type);
            if ($isTechnicianView) {
                return redirect('/admin/technician-view/services/' . $service->id . '/checklist/' . $firstStage);
            }
            return redirect('/technician/services/' . $service->id . '/checklist/' . $firstStage);
        }

        // Preparar variables para la etapa de productos (si es la etapa actual)
        $products = collect();
        $stageInstruction = '';

        $nextStage = $this->getNextStage($service->checklist_stage, $service->service_type);
        $previousStage = $this->getPreviousStage($service->checklist_stage, $service->service_type);

        if ($service->checklist_stage === 'products') {
            $serviceTypeMapping = [
                'desratizacion' => 'desratizacion',
                'desinsectacion' => 'desinsectacion',
                'sanitizacion' => 'sanitizacion',
                'desinfeccion' => 'desinfeccion',
                'fumigacion-de-jardines' => 'desinsectacion',
                'servicios-especiales' => 'sanitizacion'
            ];

            $productServiceType = $serviceTypeMapping[$service->service_type] ?? null;

            if ($productServiceType) {
                $products = \App\Models\Product::where('service_type', $productServiceType)
                    ->where('stock', '>', 0)
                    ->orderBy('name')
                    ->get();
            }

            $stageInstruction = $this->getProductStageInstruction($service->service_type);
        }

        return view('technician.checklist-staged', compact('service', 'products', 'stageInstruction', 'nextStage', 'previousStage'));
    }

    public function showChecklistStage(Request $request, Service $service, $stage)
    {
        // Verificar permisos
        if ($service->assigned_to !== auth()->id() && !auth()->user()->hasRole("super-admin")) {
            abort(403, "No tienes permisos para acceder a este servicio");
        }

        // Verificar estado del servicio
        if ($service->status !== "en_progreso") {
            return redirect()->back()->with("error", "Este servicio debe estar en progreso para realizar el checklist");
        }

        // Validar que la etapa sea válida según el tipo de servicio
        if ($service->service_type === 'monitoreo-cebaderas') {
            $validStages = ["monitoreo-datos", "monitoreo-croquis", "monitoreo-completo", "monitoreo-estadisticas", "monitoreo-analisis", "monitoreo-firma"];
        } else {
            // Desratización ya no incluye "points"
            if ($service->service_type === 'desratizacion') {
                $validStages = ["products", "results", "observations", "sites", "description"];
            } else {
                $validStages = ["points", "products", "results", "observations", "sites", "description"];
            }
        }
        
        if (!in_array($stage, $validStages)) {
            abort(404, "Etapa no válida");
        }

        // Detectar si estamos en modo technician-view
        $isTechnicianView = request()->is('admin/technician-view/*') || 
                           request()->routeIs('technician-view.*');

        // ✅ NUEVO: Para sanitización, saltarse la etapa de results
        if (($service->service_type === 'sanitizacion' || $service->service_type === 'desinfeccion')  && $stage === 'results') {
            if ($isTechnicianView) {
                return redirect('/admin/technician-view/services/' . $service->id . '/checklist/observations')
                    ->with('info', 'La etapa de resultados no aplica para servicios de sanitización');
            }
            return redirect('/technician/services/' . $service->id . '/checklist/observations')
                ->with('info', 'La etapa de resultados no aplica para servicios de sanitización');
        }

        // Actualizar la etapa actual del servicio
        $service->update(["checklist_stage" => $stage]);

        $service->load(["client", "serviceType"]);

        // Si es la etapa de productos, cargar productos filtrados por service_type
        $products = null;
        $stageInstruction = null;

        if ($stage === 'products') {
            // Mapear el service_type del servicio con los valores del enum de productos
            $serviceTypeMapping = [
                'desratizacion' => 'desratizacion',
                'desinsectacion' => 'desinsectacion',
                'sanitizacion' => 'sanitizacion',
                'desinfeccion' => 'desinfeccion',
                'fumigacion-de-jardines' => 'desinsectacion',
                'servicios-especiales' => 'sanitizacion'
            ];

            $productServiceType = $serviceTypeMapping[$service->service_type] ?? null;

            if ($productServiceType) {
                $products = \App\Models\Product::where('service_type', $productServiceType)
                    ->where('stock', '>', 0)
                    ->orderBy('name')
                    ->get();
            }

            $stageInstruction = $this->getProductStageInstruction($service->service_type);
        }

        // Asegurar que las variables siempre estén definidas
        $products = $products ?? collect();
        $stageInstruction = $stageInstruction ?? '';
        $nextStage = $this->getNextStage($service->checklist_stage, $service->service_type);
        $previousStage = $this->getPreviousStage($service->checklist_stage, $service->service_type);

        // Para monitoreo-cebaderas, usar la vista principal que incluye las etapas
        if ($service->service_type === 'monitoreo-cebaderas') {
            return view('technician.checklist-staged', compact('service', 'products', 'stageInstruction', 'nextStage', 'previousStage'));
        }

        return view("technician.checklist-stages." . $stage, compact("service", "products", "stageInstruction", "nextStage", "previousStage"));
    }


     public function saveChecklistStage(Request $request, Service $service)
    {
        // Verificar permisos
        if ($service->assigned_to !== auth()->id() && !auth()->user()->hasRole("super-admin")) {
            return response()->json(['success' => false, 'message' => 'No tienes permisos para modificar este servicio'], 403);
        }

        // Detectar si estamos en modo technician-view
        $isTechnicianView = request()->is('admin/technician-view/*') || 
                           request()->routeIs('technician-view.*') ||
                           (request()->header('referer') && strpos(request()->header('referer'), '/admin/technician-view/') !== false);

        // Obtener la etapa actual del formulario
        $stage = $request->input('checklist_stage') ?? $request->input('current_stage') ?? $request->input('stage') ?? $request->input('data_stage') ?? 'unknown';
        $nextStage = $request->input('next_stage') ?? $request->input('data_next_stage') ?? null;
        
        // Verificar estado del servicio (permitir si se está completando el servicio)
        $isCompletingService = ($stage === 'monitoreo-firma' || $nextStage === 'completed' || $stage === 'description');
        if ($service->status !== "en_progreso" && !$isCompletingService) {
            return response()->json(['success' => false, 'message' => 'El servicio debe estar en progreso para guardar datos'], 403);
        }
        if($stage==="unknown"){
            // Determinar etapa por defecto según tipo de servicio
            if ($service->service_type === 'monitoreo-cebaderas') {
                $stage = 'monitoreo-datos';
            } elseif ($service->service_type === 'desratizacion') {
                $stage = 'products';
            } else {
                $stage = 'points';
            }
        }

        // Validar que la etapa sea válida según el tipo de servicio
        if ($service->service_type === 'monitoreo-cebaderas') {
            $validStages = ["monitoreo-datos", "monitoreo-croquis", "monitoreo-completo", "monitoreo-estadisticas", "monitoreo-analisis", "monitoreo-firma"];
        } else {
            // Desratización ya no incluye "points"
            if ($service->service_type === 'desratizacion') {
                $validStages = ["products", "results", "observations", "sites", "description"];
            } else {
                $validStages = ["points", "products", "results", "observations", "sites", "description"];
            }
        }
        
        if (!in_array($stage, $validStages)) {
            return response()->json(['success' => false, 'message' => 'Etapa no válida ('.$stage.')'], 400);
        }

        try {
            $nextStage = ($request->input('next_stage') ?? $request->input('data_next_stage') ?? $this->getNextStage($stage, $service->service_type));


            // ✅ NUEVO: Si es sanitización y se intenta procesar results, omitir y pasar a observations
            if (($service->service_type === 'sanitizacion' || $service->service_type === 'desinfeccion') && $stage === 'results') {
                if ($isTechnicianView) {
                    return redirect('/admin/technician-view/services/' . $service->id . '/checklist/observations');
                }
                return redirect('/technician/services/' . $service->id . '/checklist/observations');
            }

            // Obtener datos existentes del checklist
            $checklistData = $service->checklist_data ?? [];

            // Procesar datos según la etapa
                    // Actualizar la etapa actual del servicio
            $service->update(["checklist_stage" => $stage]);

            $service->load(["client", "serviceType"]);

            switch ($stage) {
                case 'monitoreo-datos':
                    $checklistData['monitoreo_datos'] = $this->processMonitoreoDatosData($request);
                    break;
                case 'monitoreo-croquis':
                    $checklistData['monitoreo_croquis'] = $this->processMonitoreoCroquisData($request);
                    break;
                case 'monitoreo-completo':
                    $checklistData['monitoreo_completo'] = $this->processMonitoreoCompletoData($request);
                    break;
                case 'monitoreo-estadisticas':
                    $checklistData['monitoreo_estadisticas'] = $this->processMonitoreoEstadisticasData($request);
                    break;
                case 'monitoreo-analisis':
                    $checklistData['monitoreo_analisis'] = $this->processMonitoreoAnalisisData($request);
                    break;
                case 'monitoreo-firma':
                    $checklistData['monitoreo_firma'] = $this->processMonitoreoFirmaData($request);
                    // Marcar servicio como completado
                    $service->update([
                        'checklist_data' => $checklistData,
                        'status' => 'finalizado',
                        'checklist_completed_at' => now(),
                        'completed_at' => now()
                    ]);
                    // Redirigir al detalle del servicio
                    if ($isTechnicianView) {
                        try {
                            return redirect('/admin/technician-view/services/' . $service->id . '/detail')
                                ->with('success', 'Servicio completado exitosamente. Puedes descargar el informe en PDF.');
                        } catch (\Exception $e) {
                            // Si la ruta no está disponible, usar URL directa
                            return redirect('/admin/technician-view/services/' . $service->id . '/detail')
                                ->with('success', 'Servicio completado exitosamente. Puedes descargar el informe en PDF.');
                        }
                    }
                    return redirect('/technician/services/' . $service->id . '/detail')
                        ->with('success', 'Servicio completado exitosamente. Puedes descargar el informe en PDF.');
                    break;
                case 'points':
                    $checklistData['points'] = $this->processPointsData($request);
                    break;
                case 'products':
                    $checklistData['products'] = $this->processProductsData($request);
                    break;
                case 'results':
                    $checklistData['results'] = $this->processResultsData($request);
                    break;
                case 'observations':
                    // IMPORTANTE: Agregar nuevas observaciones a las existentes, no reemplazar
                    $existingObservations = $checklistData['observations'] ?? [];
                    $newObservations = $this->processObservationsData($request, $existingObservations);
                    $checklistData['observations'] = array_merge($existingObservations, $newObservations);
                    break;
                case 'sites':
                    $checklistData['sites'] = $this->processSitesData($request);
                    break;
                case 'description':
                    $checklistData['description'] = $this->processDescriptionData($request);
                    break;
            }

            if ($nextStage === 'products') {
                // Mapear el service_type del servicio con los valores del enum de productos
                $serviceTypeMapping = [
                    'desratizacion' => 'desratizacion',
                    'desinsectacion' => 'desinsectacion',
                    'sanitizacion' => 'sanitizacion',
                    'desinfeccion' => 'desinfeccion',
                    'fumigacion-de-jardines' => 'desinsectacion',
                    'servicios-especiales' => 'sanitizacion'
                ];

                $productServiceType = $serviceTypeMapping[$service->service_type] ?? null;

                if ($productServiceType) {
                    $products = \App\Models\Product::where('service_type', $productServiceType)
                        ->where('stock', '>', 0)
                        ->orderBy('name')
                        ->get();
                }

            }
            // Actualizar la base de datos (solo si no se completó en monitoreo-firma)
            if ($stage !== 'monitoreo-firma') {
                $service->update(['checklist_data' => $checklistData]);
            }
            $stageInstruction = $this->getProductStageInstruction($service->service_type);
            // Determinar la siguiente etapa
             // Asegurar que las variables siempre estén definidas
            $products = $products ?? collect();
            $stageInstruction = $stageInstruction ?? '';
            $nextStage = ($nextStage!==null || $nextStage!=="")?$nextStage:$stage;
            if($nextStage==="completed"){
                $service->update([
                    "status" => "finalizado",
                    "checklist_completed_at" => now(),
                    "completed_at" => now()
                ]);
                if ($isTechnicianView) {
                    try {
                        return redirect('/admin/technician-view/services/' . $service->id . '/detail')
                            ->with('success', 'Servicio completado exitosamente. Puedes descargar el informe en PDF.');
                    } catch (\Exception $e) {
                        // Si la ruta no está disponible, usar URL directa
                        return redirect('/admin/technician-view/services/' . $service->id . '/detail')
                            ->with('success', 'Servicio completado exitosamente. Puedes descargar el informe en PDF.');
                    }
                }
                return redirect('/technician/services/' . $service->id . '/detail')
                    ->with('success', 'Servicio completado exitosamente. Puedes descargar el informe en PDF.');
            }


            //return view("technician.checklist-stages." .$nextStage, compact("service", "products", "stageInstruction"));
            if ($isTechnicianView) {
                return redirect('/admin/technician-view/services/' . $service->id . '/checklist/' . $nextStage)
                    ->with('success', 'Datos de la etapa guardados correctamente.');
            }
            return redirect('/technician/services/' . $service->id . '/checklist/' . $nextStage)
                ->with('success', 'Datos de la etapa guardados correctamente.');
        }catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al guardar los datos del checklist: ' . $e->getMessage()], 500);
        }
    }

    private function processPointsData(Request $request)
    {
        $points = [];
        $pointsData = $request->input('points', []);

        foreach ($pointsData as $point) {
            if (!empty($point['address']) || !empty($point['latitude']) || !empty($point['longitude'])) {
                $points[] = [
                    'address' => $point['address'] ?? '',
                    'latitude' => $point['latitude'] ?? null,
                    'longitude' => $point['longitude'] ?? null,
                    'notes' => $point['notes'] ?? '',
                    'created_at' => now()->format('Y-m-d H:i:s')
                ];
            }
        }

        return $points;
    }

    private function processProductsData(Request $request)
    {
        $data = [];

        // Capturar el producto seleccionado del radio button
        if ($request->has('applied_product')) {
            $data['applied_product'] = $request->input('applied_product');
        }

        // Capturar el ID del producto si está disponible
        if ($request->has('product_id')) {
            $data['product_id'] = $request->input('product_id');
        }

        // Capturar cantidad si está disponible
        if ($request->has('quantity')) {
            $data['quantity'] = $request->input('quantity');
        }

        // Capturar dosis y agua para desinfección y sanitización
        if ($request->has('dosis')) {
            $data['dosis'] = $request->input('dosis');
        }

        if ($request->has('agua')) {
            $data['agua'] = $request->input('agua');
        }

        $data['applied_at'] = now()->format('Y-m-d H:i:s');

        return $data;
    }

    private function processResultsData(Request $request)
    {
        $data = [];

        // Campos comunes
        if ($request->has('efficacy')) {
            $data['efficacy'] = $request->input('efficacy');
        }

        // Campos para desratización
        if ($request->has('observed_results')) {
            $data['observed_results'] = $request->input('observed_results', []);
        }

        if ($request->has('total_installed_points')) {
            $data['total_installed_points'] = $request->input('total_installed_points');
        }

        if ($request->has('total_consumption_activity')) {
            $data['total_consumption_activity'] = $request->input('total_consumption_activity');
        }

        // Campos para desinsectación
        if ($request->has('uv_lamps')) {
            $data['uv_lamps'] = $request->input('uv_lamps');
        }

        if ($request->has('tuv')) {
            $data['tuv'] = $request->input('tuv');
        }

        if ($request->has('devices_installed')) {
            $data['devices_installed'] = $request->input('devices_installed');
        }

        if ($request->has('devices_existing')) {
            $data['devices_existing'] = $request->input('devices_existing');
        }

        if ($request->has('devices_replaced')) {
            $data['devices_replaced'] = $request->input('devices_replaced');
        }

        $data['completed_at'] = now()->format('Y-m-d H:i:s');

        return $data;
    }

    /**
     * Genera el siguiente código de cebadera basándose en las observaciones existentes
     * Formato: CE-001, CE-002, CE-003, etc.
     */
    private function generateNextCebaderaCode($existingObservations)
    {
        $maxNumber = 0;
        
        // Buscar el número más alto en los códigos existentes
        foreach ($existingObservations as $observation) {
            if (isset($observation['cebadera_code']) && !empty($observation['cebadera_code'])) {
                // Extraer el número del código (ej: CE-001 -> 1)
                if (preg_match('/CE-(\d+)/i', $observation['cebadera_code'], $matches)) {
                    $number = (int)$matches[1];
                    if ($number > $maxNumber) {
                        $maxNumber = $number;
                    }
                }
            }
        }
        
        // Generar el siguiente código
        $nextNumber = $maxNumber + 1;
        return sprintf('CE-%03d', $nextNumber);
    }

    private function processObservationsData(Request $request, $existingObservations = [])
    {
        $observations = [];

        // Si es una nueva observación desde el formulario
        if ($request->has('cebadera_code') || $request->has('detail')) {
            // Obtener el código de cebadera del request o generar uno automáticamente
            $cebaderaCode = trim($request->input('cebadera_code', ''));
            
            // Si no se proporcionó un código o está vacío, generar uno automáticamente
            if (empty($cebaderaCode)) {
                $cebaderaCode = $this->generateNextCebaderaCode($existingObservations);
            }
            
            $newObservation = [
                'cebadera_code' => $cebaderaCode,
                'observation_number' => $request->input('observation_number', 1),
                'detail' => $request->input('detail', ''),
                'complementary' => $request->input('complementary', ''),
                'created_at' => now()->format('Y-m-d H:i:s')
            ];

            // Manejar foto si se subió
            if ($request->hasFile('photo')) {
                $photo = $request->file('photo');
                $filename = time() . '_' . uniqid();

                // Comprimir y guardar la imagen
                $compressedImagePath = ImageHelper::compressAndStoreImage($photo, 'observations', $filename);

                if ($compressedImagePath) {
                    $newObservation['photo'] = $compressedImagePath;
                } else {
                    $originalFilename = time() . '_' . $photo->getClientOriginalName();
                    $photo->storeAs('observations', $originalFilename, 'public');
                    $newObservation['photo'] = 'storage/observations/' . $originalFilename;
                }
            }

            $observations[] = $newObservation;
        }

        // Si hay observaciones adicionales desde checkboxes o campos múltiples
        $additionalObservations = $request->input('observations', []);
        if (is_array($additionalObservations)) {
            foreach ($additionalObservations as $obs) {
                if (!empty($obs['detail'])) {
                    // Generar código automáticamente si no se proporciona
                    $cebaderaCode = trim($obs['cebadera_code'] ?? '');
                    if (empty($cebaderaCode)) {
                        $cebaderaCode = $this->generateNextCebaderaCode(array_merge($existingObservations, $observations));
                    }
                    
                    $observations[] = [
                        'cebadera_code' => $cebaderaCode,
                        'observation_number' => $obs['observation_number'] ?? count($observations) + 1,
                        'detail' => $obs['detail'],
                        'complementary' => $obs['complementary'] ?? '',
                        'created_at' => now()->format('Y-m-d H:i:s')
                    ];
                }
            }
        }

        return $observations;
    }

    private function processSitesData(Request $request)
    {
        $data = [];

        // Capturar el campo de sitios tratados
        if ($request->has('treated_sites')) {
            $data['treated_sites'] = $request->input('treated_sites');
        }

        $data['completed_at'] = now()->format('Y-m-d H:i:s');

        return $data;
    }

    private function processDescriptionData(Request $request)
    {
        $data = [
            'service_description' => $request->input('service_description', ''),
            'service_sugerencia' => $request->input('service_sugerencia', ''),
            'completion_date' => $request->input('completion_date', now()->format('Y-m-d')),
            'completed_at' => now()->format('Y-m-d H:i:s')
        ];

        // Guardar firmas digitales si están presentes
        if ($request->input('technician_signature')) {
            $data['technician_signature'] = $request->input('technician_signature');
        }

        if ($request->input('client_signature')) {
            $data['client_signature'] = $request->input('client_signature');
        }

        return $data;
    }

    // Métodos de procesamiento para Monitoreo de Cebaderas
    private function processMonitoreoDatosData(Request $request)
    {
        $data = [
            'pests_detected' => $request->input('pests_detected', ''),
            'pests_detected_list' => json_decode($request->input('pests_detected_list', '[]'), true) ?? [],
            'infestation_level' => $request->input('infestation_level', ''),
            'technician_observations' => $request->input('technician_observations', ''),
            'client_recommendations' => $request->input('client_recommendations', ''),
        ];

        // Procesar fotos si hay
        if ($request->hasFile('service_photos')) {
            $photos = [];
            foreach ($request->file('service_photos') as $photo) {
                $filename = time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
                $photo->storeAs('services/photos', $filename, 'public');
                $photos[] = 'storage/services/photos/' . $filename;
            }
            $data['service_photos'] = $photos;
        }

        return $data;
    }

    private function processMonitoreoCroquisData(Request $request)
    {
        $data = [
            'croquis_notes' => $request->input('croquis_notes', ''),
        ];

        // Procesar archivo de croquis si hay
        if ($request->hasFile('croquis_file')) {
            $file = $request->file('croquis_file');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('services/croquis', $filename, 'public');
            $data['croquis_file'] = 'storage/services/croquis/' . $filename;
        }

        return $data;
    }

    private function processMonitoreoCompletoData(Request $request)
    {
        $data = [
            'monitoring_date' => $request->input('monitoring_date', date('Y-m-d')),
            'total_bait_stations' => $request->input('total_bait_stations', 0),
            'bait_stations' => [],
            'traps' => [],
            'general_observations' => $request->input('general_observations', ''),
            'client_recommendations_monitoring' => $request->input('client_recommendations_monitoring', ''),
        ];

        // Procesar cebaderas
        $baitStationsInput = $request->input('bait_stations', []);
        if (!empty($baitStationsInput)) {
            foreach ($baitStationsInput as $index => $station) {
                if (is_array($station)) {
                    $stationData = [
                        'code' => $station['code'] ?? '',
                        'location' => $station['location'] ?? '',
                        'product_type' => $station['product_type'] ?? '',
                        'quantity' => $station['quantity'] ?? 0,
                        'unit' => $station['unit'] ?? 'g',
                        'observations' => is_array($station['observations'] ?? null) ? $station['observations'] : [],
                    ];

                    // Procesar fotos de la cebadera si se enviaron
                    $photos = [];
                    if ($request->hasFile("bait_stations")) {
                        $allFiles = $request->file("bait_stations");
                        if (isset($allFiles[$index]['photos']) && is_array($allFiles[$index]['photos'])) {
                            foreach ($allFiles[$index]['photos'] as $photo) {
                                if ($photo && $photo->isValid()) {
                                    $filename = time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
                                    $photo->storeAs('services/bait-stations', $filename, 'public');
                                    $photos[] = 'storage/services/bait-stations/' . $filename;
                                }
                            }
                        }
                    }
                    $stationData['photos'] = $photos;

                    $data['bait_stations'][] = $stationData;
                }
            }
        }

        // Procesar trampas
        $trapsInput = $request->input('traps', []);
        if (!empty($trapsInput)) {
            foreach ($trapsInput as $index => $trap) {
                if (is_array($trap)) {
                    $trapData = [
                        'code' => $trap['code'] ?? '',
                        'location' => $trap['location'] ?? '',
                        'product_type' => $trap['product_type'] ?? '',
                        'quantity' => $trap['quantity'] ?? 1,
                        'status' => $trap['status'] ?? '',
                        'notes' => $trap['notes'] ?? '',
                    ];

                    // Procesar fotos de la trampa si se enviaron
                    $photos = [];
                    if ($request->hasFile("traps")) {
                        $allFiles = $request->file("traps");
                        if (isset($allFiles[$index]['photos']) && is_array($allFiles[$index]['photos'])) {
                            foreach ($allFiles[$index]['photos'] as $photo) {
                                if ($photo && $photo->isValid()) {
                                    $filename = time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
                                    $photo->storeAs('services/traps', $filename, 'public');
                                    $photos[] = 'storage/services/traps/' . $filename;
                                }
                            }
                        }
                    }
                    $trapData['photos'] = $photos;

                    $data['traps'][] = $trapData;
                }
            }
        }

        return $data;
    }

    private function processMonitoreoEstadisticasData(Request $request)
    {
        return [
            'total_monitored' => $request->input('total_monitored', 0),
            'total_active' => $request->input('total_active', 0),
            'total_problems' => $request->input('total_problems', 0),
            'total_traps' => $request->input('total_traps', 0),
            'total_consumption' => $request->input('total_consumption', 0),
            'average_consumption_percent' => $request->input('average_consumption_percent', 0),
            'detected_species' => $request->input('detected_species', ''),
            'activity_level' => $request->input('activity_level', ''),
            'executive_summary' => $request->input('executive_summary', ''),
        ];
    }

    private function processMonitoreoAnalisisData(Request $request)
    {
        return [
            'ai_analysis_data' => json_decode($request->input('ai_analysis_data', '{}'), true) ?? [],
            'technician_ai_notes' => $request->input('technician_ai_notes', ''),
            'ai_analysis_validated' => $request->has('ai_analysis_validated'),
        ];
    }

    private function processMonitoreoFirmaData(Request $request)
    {
        $data = [
            'signer_name' => $request->input('signer_name', ''),
            'signer_position' => $request->input('signer_position', ''),
            'service_completed' => $request->has('service_completed'),
        ];

        // Procesar firma del técnico si se proporcionó
        if ($request->has('technician_signature') && !empty($request->input('technician_signature'))) {
            $signatureData = $request->input('technician_signature');
            // Si es una imagen en base64, guardarla
            if (strpos($signatureData, 'data:image') === 0) {
                $image = str_replace('data:image/png;base64,', '', $signatureData);
                $image = str_replace(' ', '+', $image);
                $imageData = base64_decode($image);
                $filename = 'signature_' . time() . '_' . uniqid() . '.png';
                Storage::disk('public')->put('signatures/' . $filename, $imageData);
                $data['technician_signature'] = 'storage/signatures/' . $filename;
            }
        }

        return $data;
    }

    private function getNextStage($currentStage, $serviceType)
    {
        // Flujo para Monitoreo de Cebaderas: 6 etapas específicas
        if ($serviceType === 'monitoreo-cebaderas') {
            $stageFlow = [
                'monitoreo-datos' => 'monitoreo-croquis',
                'monitoreo-croquis' => 'monitoreo-completo',
                'monitoreo-completo' => 'monitoreo-estadisticas',
                'monitoreo-estadisticas' => 'monitoreo-analisis',
                'monitoreo-analisis' => 'monitoreo-firma',
                'monitoreo-firma' => null // Final stage
            ];

            return $stageFlow[$currentStage] ?? null;
        }

        // Flujo especial para servicios especiales: observations → sites → description
        if ($serviceType === 'servicios-especiales') {
            $stageFlow = [
                'observations' => 'sites',
                'sites' => 'description',
                'description' => null // Final stage
            ];

            return $stageFlow[$currentStage] ?? null;
        }

        // Flujo especial para sanitización/desinfección: products → observations (saltarse results)
        if ($serviceType === 'sanitizacion' || $serviceType === 'desinfeccion') {
            $stageFlow = [
                'products' => 'observations',
                'observations' => 'sites',
                'sites' => 'description',
                'description' => null // Final stage
            ];

            return $stageFlow[$currentStage] ?? null;
        }

        // Flujo para desratización: products → results → observations → sites → description (sin points)
        if ($serviceType === 'desratizacion') {
            $stageFlow = [
                'products' => 'results',
                'results' => 'observations',
                'observations' => 'sites',
                'sites' => 'description',
                'description' => null // Final stage
            ];

            return $stageFlow[$currentStage] ?? null;
        }

        // Flujo estándar para otros tipos de servicio (que incluyen points)
        $stageFlow = [
            'points' => 'products',
            'products' => 'results',
            'results' => 'observations',
            'observations' => 'sites',
            'sites' => 'description',
            'description' => null // Final stage
        ];

        return $stageFlow[$currentStage] ?? null;
    }

    private function getPreviousStage($currentStage, $serviceType)
    {
        // Flujo para Monitoreo de Cebaderas: 6 etapas específicas
        if ($serviceType === 'monitoreo-cebaderas') {
            $stageFlow = [
                'monitoreo-croquis' => 'monitoreo-datos',
                'monitoreo-completo' => 'monitoreo-croquis',
                'monitoreo-estadisticas' => 'monitoreo-completo',
                'monitoreo-analisis' => 'monitoreo-estadisticas',
                'monitoreo-firma' => 'monitoreo-analisis',
                'monitoreo-datos' => null // Primera etapa
            ];

            return $stageFlow[$currentStage] ?? null;
        }

        // Flujo especial para servicios especiales: observations → sites → description
        if ($serviceType === 'servicios-especiales') {
            $stageFlow = [
                'sites' => 'observations',
                'description' => 'sites',
                'observations' => null // Primera etapa
            ];

            return $stageFlow[$currentStage] ?? null;
        }

        // Flujo especial para sanitización/desinfección: products → observations (saltarse results)
        if ($serviceType === 'sanitizacion' || $serviceType === 'desinfeccion') {
            $stageFlow = [
                'observations' => 'products',
                'sites' => 'observations',
                'description' => 'sites',
                'products' => null // Primera etapa
            ];

            return $stageFlow[$currentStage] ?? null;
        }

        // Flujo para desratización: products → results → observations → sites → description (sin points)
        if ($serviceType === 'desratizacion') {
            $stageFlow = [
                'results' => 'products',
                'observations' => 'results',
                'sites' => 'observations',
                'description' => 'sites',
                'products' => null // Primera etapa
            ];

            return $stageFlow[$currentStage] ?? null;
        }

        // Flujo estándar para otros tipos de servicio (que incluyen points)
        $stageFlow = [
            'products' => 'points',
            'results' => 'products',
            'observations' => 'results',
            'sites' => 'observations',
            'description' => 'sites',
            'points' => null // Primera etapa
        ];

        return $stageFlow[$currentStage] ?? null;
    }

    private function getFirstStage($serviceType)
    {
        // Flujo para Monitoreo de Cebaderas: empieza en monitoreo-datos
        if ($serviceType === 'monitoreo-cebaderas') {
            return 'monitoreo-datos';
        }

        // Flujo especial para servicios especiales: empieza en observations
        if ($serviceType === 'servicios-especiales') {
            return 'observations';
        }

        // Flujo especial para sanitización/desinfección: empieza en products
        if ($serviceType === 'sanitizacion' || $serviceType === 'desinfeccion') {
            return 'products';
        }

        // Flujo para desratización: empieza en products (sin points)
        if ($serviceType === 'desratizacion') {
            return 'products';
        }

        // Flujo estándar para otros tipos de servicio: empieza en points
        return 'points';
    }

    public function handleObservation(Service $service, $index)
    {
        // Verificar permisos
        if ($service->assigned_to !== auth()->id() && !auth()->user()->hasRole("super-admin")) {
            abort(403, "No tienes permisos para acceder a este servicio");
        }

        // Verificar estado del servicio
        if ($service->status !== "en_progreso") {
            return redirect()->back()->with("error", "Este servicio debe estar en progreso para editar observaciones");
        }

        // Redirigir a la página de observations con el índice específico para editar
        return redirect('/technician/services/' . $service->id . '/checklist/observations')
            ->with('edit_observation_index', $index);
    }

    public function editObservation(Service $service, $index)
    {
        // Verificar permisos
        if ($service->assigned_to !== auth()->id() && !auth()->user()->hasRole("super-admin")) {
            abort(403, "No tienes permisos para acceder a este servicio");
        }

        // Verificar estado del servicio
        if ($service->status !== "en_progreso") {
            return redirect()->back()->with("error", "Este servicio debe estar en progreso para editar observaciones");
        }

        // Redirigir a la página de observations con el índice específico
        return redirect('/technician/services/' . $service->id . '/checklist/observations')
            ->with('edit_observation_index', $index);
    }

    public function updateObservation(Request $request, Service $service, $index)
    {
        try {
            Log::info('updateObservation llamado', ['service_id' => $service->id, 'index' => $index]);

            // Verificar permisos
            if ($service->assigned_to !== auth()->id() && !auth()->user()->hasRole("super-admin")) {
                return response()->json(['success' => false, 'message' => 'No tienes permisos para editar esta observación'], 403);
            }

            // Verificar estado del servicio
            if ($service->status !== "en_progreso") {
                return response()->json(['success' => false, 'message' => 'El servicio debe estar en progreso para modificar observaciones'], 403);
            }

            // Validar datos de entrada
            $request->validate([
                'cebadera_code' => 'nullable|string|max:255',
                'observation_number' => 'nullable|integer|min:1',
                'detail' => 'required|string|max:1000',
                'complementary' => 'nullable|string|max:500',
            ]);

            // Obtener datos existentes del checklist
            $checklistData = $service->checklist_data ?? [];

            // Verificar que existan observaciones
            if (!isset($checklistData['observations']) || !is_array($checklistData['observations'])) {
                return response()->json(['success' => false, 'message' => 'No hay observaciones para editar'], 404);
            }

            // Verificar que el índice sea válido
            if (!isset($checklistData['observations'][$index])) {
                return response()->json(['success' => false, 'message' => 'Observación no encontrada'], 404);
            }

            // Obtener la observación actual
            $currentObservation = $checklistData['observations'][$index];

            // Preparar los datos actualizados
            $updatedObservation = [
                'cebadera_code' => $request->input('cebadera_code', $currentObservation['cebadera_code'] ?? ''),
                'observation_number' => $request->input('observation_number', $currentObservation['observation_number'] ?? ($index + 1)),
                'detail' => $request->input('detail'),
                'complementary' => $request->input('complementary', $currentObservation['complementary'] ?? ''),
                'created_at' => $currentObservation['created_at'] ?? now()->format('Y-m-d H:i:s'), // Preservar fecha de creación
                'updated_at' => now()->format('Y-m-d H:i:s')
            ];

            // Manejar nueva foto si se subió
            if ($request->hasFile('photo')) {
                $photo = $request->file('photo');
                $filename = time() . '_' . uniqid();

                // Comprimir y guardar la nueva imagen
                $compressedImagePath = ImageHelper::compressAndStoreImage($photo, 'observations', $filename);

                if ($compressedImagePath) {
                    $updatedObservation['photo'] = $compressedImagePath;

                    // Eliminar la foto anterior si existe
                    if (isset($currentObservation['photo']) && !empty($currentObservation['photo'])) {
                        $photoPath = $currentObservation['photo'];
                        // Eliminar 'storage/' del inicio si existe
                        $photoPath = preg_replace('/^storage\//', '', $photoPath);
                        $oldPhotoPath = storage_path('app/public/' . $photoPath);

                        if (file_exists($oldPhotoPath)) {
                            try {
                                unlink($oldPhotoPath);
                                Log::info('Foto anterior eliminada: ' . $oldPhotoPath);
                            } catch (\Exception $e) {
                                Log::warning('No se pudo eliminar la foto anterior: ' . $oldPhotoPath . '. Error: ' . $e->getMessage());
                            }
                        } else {
                            Log::warning('Foto anterior no encontrada: ' . $oldPhotoPath);
                        }
                    }
                } else {
                    Log::warning('No se pudo comprimir la nueva imagen, guardando original');
                    $originalFilename = time() . '_' . $photo->getClientOriginalName();
                    $photo->storeAs('observations', $originalFilename, 'public');
                    $updatedObservation['photo'] = 'storage/observations/' . $originalFilename;
                }
            } else {
                // Mantener la foto actual si no se sube una nueva
                $updatedObservation['photo'] = $currentObservation['photo'] ?? null;
            }

            // Actualizar la observación en el array
            $checklistData['observations'][$index] = $updatedObservation;

            // Actualizar la base de datos
            $service->update(['checklist_data' => $checklistData]);

            return response()->json([
                'success' => true,
                'message' => 'Observación actualizada exitosamente',
                'observation' => $updatedObservation
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación: ' . implode(', ', $e->errors()['detail'] ?? ['Datos inválidos'])
            ], 422);

        } catch (\Exception $e) {
            Log::error('Error al actualizar observación: ' . $e->getMessage() . ' en línea ' . $e->getLine() . ' del archivo ' . $e->getFile());
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor. No se pudo actualizar la observación.'
            ], 500);
        }
    }

    public function deleteObservation(Service $service, $index)
    {
        Log::info('deleteObservation llamado', ['service_id' => $service->id, 'index' => $index]);

        // Verificar permisos
        if ($service->assigned_to !== auth()->id() && !auth()->user()->hasRole("super-admin")) {
            return response()->json(['success' => false, 'message' => 'No tienes permisos para eliminar esta observación'], 403);
        }

        // Verificar estado del servicio
        if ($service->status !== "en_progreso") {
            return response()->json(['success' => false, 'message' => 'El servicio debe estar en progreso para modificar observaciones'], 403);
        }

        // Obtener datos existentes del checklist
        $checklistData = $service->checklist_data ?? [];

        // Verificar que existan observaciones
        if (!isset($checklistData['observations']) || !is_array($checklistData['observations'])) {
            return response()->json(['success' => false, 'message' => 'No hay observaciones para eliminar'], 404);
        }

        // Verificar que el índice sea válido
        if (!isset($checklistData['observations'][$index])) {
            return response()->json(['success' => false, 'message' => 'Observación no encontrada'], 404);
        }

        // Obtener la observación antes de eliminarla (para el archivo físico)
        $observation = $checklistData['observations'][$index];

        // Eliminar el archivo físico si existe
        if (isset($observation['photo']) && !empty($observation['photo'])) {
            $photoPath = $observation['photo'];
            // Eliminar 'storage/' del inicio si existe
            $photoPath = preg_replace('/^storage\//', '', $photoPath);
            $fullPhotoPath = storage_path('app/public/' . $photoPath);

            if (file_exists($fullPhotoPath)) {
                try {
                    unlink($fullPhotoPath);
                    Log::info('Foto de observación eliminada: ' . $fullPhotoPath);
                } catch (\Exception $e) {
                    Log::warning('No se pudo eliminar el archivo físico de la observación: ' . $fullPhotoPath . '. Error: ' . $e->getMessage());
                    // Continuar con la eliminación de la observación aunque no se pueda eliminar el archivo
                }
            } else {
                Log::warning('Foto de observación no encontrada: ' . $fullPhotoPath);
            }
        }

        // Eliminar la observación del array
        unset($checklistData['observations'][$index]);

        // Reindexar el array para mantener índices numéricos consecutivos
        $checklistData['observations'] = array_values($checklistData['observations']);

        // Actualizar la base de datos
        try {
            $service->update(['checklist_data' => $checklistData]);
        } catch (\Exception $e) {
            Log::error('Error al actualizar servicio después de eliminar observación: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor. La observación fue eliminada del archivo pero no se pudo actualizar la base de datos.'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Observación eliminada exitosamente',
            'remaining_observations' => count($checklistData['observations'])
        ]);
    }

    public function generatePDF(Service $service)
    {
        // Verificar permisos
        if ($service->assigned_to !== auth()->id() && !auth()->user()->hasRole("super-admin")) {
            abort(403, "No tienes permisos para acceder a este servicio");
        }

        // Verificar que el servicio esté completado
        if ($service->status !== 'finalizado') {
            abort(403, "Solo se pueden generar PDFs de servicios completados");
        }

        $service->load(['client', 'serviceType', 'assignedUser']);

        // Generar ID de validación único
        $validationId = 'PC-' . $service->id . '-' . now()->format('YmdHis') . '-' . substr(md5($service->id . now()), 0, 8);

        // Generar hash de integridad
        $integrityData = $service->id . $service->client->name . $service->checklist_completed_at . json_encode($service->checklist_data);
        $integrityHash = hash('sha256', $integrityData);

        // Generar QR Code
        $qrData = json_encode([
            'service_id' => $service->id,
            'validation_id' => $validationId,
            'integrity_hash' => $integrityHash,
            'generated_at' => now()->toISOString(),
            'client' => $service->client->name,
            'technician' => $service->assignedUser->name ?? 'N/A'
        ]);

        $qrCode = base64_encode(file_get_contents('https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($qrData)));

        // Guardar información de trazabilidad en la base de datos
        $service->update([
            'pdf_generated_at' => now(),
            'pdf_validation_id' => $validationId,
            'pdf_integrity_hash' => $integrityHash
        ]);

        $pdf = Pdf::loadView('technician.service-pdf', compact('service', 'validationId', 'integrityHash', 'qrCode'));
        
        // Configurar opciones de DomPDF para manejar imágenes correctamente
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true, // Necesario para cargar imágenes desde rutas del sistema
            'isPhpEnabled' => true, // Necesario para funciones PHP en las vistas
            'defaultFont' => 'Arial',
        ]);

        $filename = "servicio-{$service->id}-{$service->client->name}-{$validationId}.pdf";

        return $pdf->download($filename);
    }

    /**
     * Obtener texto de instrucción específico para la etapa de productos según el tipo de servicio
     */
    private function getProductStageInstruction($serviceType)
    {
        $instructions = [
            'desratizacion' => 'Seleccione el rodenticida utilizado para el control de roedores',
            'desinsectacion' => 'Seleccione el insecticida utilizado para el control de insectos',
            'sanitizacion' => 'Seleccione el desinfectante utilizado para la sanitización',
            'fumigacion-de-jardines' => 'Seleccione el producto utilizado para la fumigación de jardines',
            'servicios-especiales' => 'Seleccione el producto utilizado para este servicio especial'
        ];

        return $instructions[$serviceType] ?? 'Seleccione el producto utilizado para este servicio';
    }
}
