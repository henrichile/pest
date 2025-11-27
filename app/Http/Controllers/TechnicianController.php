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

        // Si está en modo "view_as_technician" y es super-admin, mostrar todos los servicios
        // para que pueda ver cómo funciona el sistema
        $isViewingAsTechnician = session('view_as_technician', false) && $user->hasRole('super-admin');

        if ($isViewingAsTechnician) {
            // Mostrar todos los servicios para que el admin pueda ver el flujo completo
            $query = Service::query();
        } else {
            // Filtrar por técnico asignado
            $query = Service::where('assigned_to', $user->id);
        }

        // Servicios completados hoy
        $completedToday = (clone $query)
            ->where('status', 'finalizado')
            ->whereDate('checklist_completed_at', today())
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
            'completedToday',
            'pendingServices',
            'inProgressServices',
            'overdueServices',
            'assignedServices'
        ));
    }

    public function services()
    {
        $user = auth()->user();
        $isViewingAsTechnician = session('view_as_technician', false) && $user->hasRole('super-admin');

        if ($isViewingAsTechnician) {
            // Mostrar todos los servicios para que el admin pueda ver el flujo completo
            $services = Service::with(['client', 'serviceType', 'assignedUser'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } else {
            // Filtrar por técnico asignado
            $services = Service::where('assigned_to', auth()->id())
                ->with(['client', 'serviceType', 'assignedUser'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        }

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


     public function submitChecklist(Request $request, Service $service)
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
               // Validar archivo de croquis si existe
               if ($request->hasFile('croquis_file')) {
                   $request->validate([
                       'croquis_file' => 'required|file|mimes:jpeg,jpg,png,pdf|max:10240', // 10MB max
                   ], [
                       'croquis_file.max' => 'El archivo de croquis no puede superar los 10MB.',
                       'croquis_file.mimes' => 'El archivo debe ser una imagen (JPEG, JPG, PNG) o PDF.',
                   ]);
               }
               // Merge with existing data to preserve file if not updated
               $currentCroquisData = $checklistData['monitoreo_croquis'] ?? [];
               $newCroquisData = $this->processMonitoreoCroquisData($request);
               $checklistData['monitoreo_croquis'] = array_merge($currentCroquisData, $newCroquisData);
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
                Log::info('Saving checklist_data to database', [
                    'service_id' => $service->id,
                    'stage' => $stage,
                    'checklist_data' => $checklistData
                ]);
                $service->update(['checklist_data' => $checklistData]);

                // Verificar que se guardó correctamente
                $service->refresh();
                Log::info('Checklist data saved, verifying', [
                    'service_id' => $service->id,
                    'saved_data' => $service->checklist_data
                ]);
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
        $data = [];

        // Guardar checkboxes de puntos de control
        $checkboxFields = [
            'installed_points_check',
            'existing_points_check',
            'spare_points_check',
            'bait_weight_check',
            'physical_installed_check',
            'physical_existing_check',
            'physical_spare_check'
        ];

        foreach ($checkboxFields as $field) {
            $data[$field] = $request->has($field) ? true : false;
        }

        // Guardar puntos de ubicación si existen
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

        if (!empty($points)) {
            $data['locations'] = $points;
        }

        return $data;
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

        // Log para debug
        Log::info('Processing croquis data', [
            'has_file' => $request->hasFile('croquis_file'),
            'all_files' => $request->allFiles(),
            'all_input' => $request->except(['_token'])
        ]);

        // Procesar archivo de croquis si hay
        if ($request->hasFile('croquis_file')) {
            $file = $request->file('croquis_file');

            // Validar que el archivo sea válido
            if ($file->isValid()) {
                try {
                    // Asegurar que el directorio existe
                    $directory = 'services/croquis';
                    if (!Storage::disk('public')->exists($directory)) {
                        Storage::disk('public')->makeDirectory($directory);
                        Log::info('Created croquis directory', ['directory' => $directory]);
                    }

                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs($directory, $filename, 'public');

                if ($path) {
                    $fullPath = storage_path('app/public/' . $path);
                    if (file_exists($fullPath)) {
                        $data['croquis_file'] = 'storage/services/croquis/' . $filename;
                        Log::info('Croquis file saved successfully', [
                            'filename' => $filename,
                            'path' => $path,
                            'full_path' => $data['croquis_file']
                        ]);
                    } else {
                        Log::error('Croquis file stored but not found on disk', ['path' => $fullPath]);
                    }
                } else {
                    Log::error('Failed to store croquis file');
                }
                } catch (\Exception $e) {
                    Log::error('Error saving croquis file', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    throw $e;
                }
            } else {
                Log::error('Croquis file is not valid', [
                    'error' => $file->getErrorMessage()
                ]);
            }
        } else {
            Log::warning('No croquis_file in request', [
                'content_length' => $_SERVER['CONTENT_LENGTH'] ?? 'unknown',
                'post_max_size' => ini_get('post_max_size'),
                'upload_max_filesize' => ini_get('upload_max_filesize')
            ]);
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
        // Debug raw request data
        Log::info('Raw Monitoreo Completo Request', [
            'files_keys' => array_keys($_FILES),
            'post_keys' => array_keys($_POST),
            'bait_stations_files' => $_FILES['bait_stations'] ?? 'null',
            'traps_files' => $_FILES['traps'] ?? 'null',
            'content_length' => $_SERVER['CONTENT_LENGTH'] ?? 'unknown'
        ]);

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

                    // Procesar fotos de la cebadera
                    $photos = [];
                    
                    // 1. Recuperar fotos existentes
                    if (isset($station['existing_photos']) && is_array($station['existing_photos'])) {
                        $photos = $station['existing_photos'];
                    }

                    // 2. Agregar nuevas fotos si se enviaron
                    if ($request->hasFile("bait_stations.$index.photos")) {
                        foreach ($request->file("bait_stations.$index.photos") as $photo) {
                            if ($photo && $photo->isValid()) {
                                $filename = time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
                                $path = $photo->storeAs('services/bait-stations', $filename, 'public');
                                
                                if ($path && file_exists(storage_path('app/public/' . $path))) {
                                    $photos[] = 'storage/services/bait-stations/' . $filename;
                                } else {
                                    Log::error('Failed to save bait station photo', ['filename' => $filename]);
                                }
                            } else {
                                Log::warning('Invalid bait station photo', [
                                    'error' => $photo ? $photo->getErrorMessage() : 'Photo object is null',
                                    'size' => $photo ? $photo->getSize() : 0
                                ]);
                            }
                        }
                    } else {
                         Log::warning('No bait_stations files in request for index ' . $index, [
                            'content_length' => $_SERVER['CONTENT_LENGTH'] ?? 'unknown',
                            'has_file_root' => $request->hasFile("bait_stations")
                        ]);
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

                    // Procesar fotos de la trampa
                    $photos = [];
                    
                    // 1. Recuperar fotos existentes
                    if (isset($trap['existing_photos']) && is_array($trap['existing_photos'])) {
                        $photos = $trap['existing_photos'];
                    }

                    // 2. Agregar nuevas fotos si se enviaron
                    if ($request->hasFile("traps.$index.photos")) {
                        foreach ($request->file("traps.$index.photos") as $photo) {
                            if ($photo && $photo->isValid()) {
                                $filename = time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
                                $path = $photo->storeAs('services/traps', $filename, 'public');
                                
                                if ($path && file_exists(storage_path('app/public/' . $path))) {
                                    $photos[] = 'storage/services/traps/' . $filename;
                                } else {
                                    Log::error('Failed to save trap photo', ['filename' => $filename]);
                                }
                            } else {
                                Log::warning('Invalid trap photo', [
                                    'error' => $photo ? $photo->getErrorMessage() : 'Photo object is null',
                                    'size' => $photo ? $photo->getSize() : 0
                                ]);
                            }
                        }
                    } else {
                         Log::warning('No traps files in request for index ' . $index, [
                            'content_length' => $_SERVER['CONTENT_LENGTH'] ?? 'unknown',
                            'has_file_root' => $request->hasFile("traps")
                        ]);
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

        // Configurar opciones de DomPDF para manejar imágenes correctamente y codificación UTF-8
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true, // Necesario para cargar imágenes desde rutas del sistema
            'isPhpEnabled' => true, // Necesario para funciones PHP en las vistas
            'defaultFont' => 'DejaVu Sans', // DejaVu Sans tiene mejor soporte UTF-8 que Arial
            'fontHeightRatio' => 1.1,
            'enable_font_subsetting' => true,
            'pdf_backend' => 'CPDF',
            'debugPng' => false,
            'debugKeepTemp' => false,
            'debugCss' => false,
            'debugLayout' => false,
            'debugLayoutLines' => false,
            'debugLayoutBlocks' => false,
            'debugLayoutInline' => false,
            'debugLayoutPaddingBox' => false,
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
