<?php

namespace App\Http\Controllers;

use App\Helpers\ImageHelper;

class TechnicianController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        
        // Servicios completados hoy
        $completedToday = Service::where('assigned_to', $user->id)
            ->where('status', 'finalizado')
            ->whereDate('checklist_completed_at', today())
            ->count();
            
        // Servicios pendientes
        $pendingServices = Service::where('assigned_to', $user->id)
            ->where('status', 'pendiente')
            ->count();
            
        // Servicios en progreso
        $inProgressServices = Service::where('assigned_to', $user->id)
            ->where('status', 'en_progreso')
            ->count();
            
        // Servicios vencidos
        $overdueServices = Service::where('assigned_to', $user->id)
            ->where('status', 'pendiente')
            ->where('scheduled_date', '<', now())
            ->count();

        // Próximos servicios asignados (pendientes y en progreso)
        $assignedServices = Service::where('assigned_to', $user->id)
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
        $services = Service::where('assigned_to', auth()->id())
            ->with(['client', 'serviceType', 'assignedUser'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('technician.services', compact('services'));
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
        
        return view('technician.service-detail', compact('service'));
    }

    public function startService(Service $service)
    {
        // Verificar permisos
        if ($service->assigned_to !== auth()->id() && !auth()->user()->hasRole("super-admin")) {
            abort(403, "No tienes permisos para iniciar este servicio");
        }

        // Verificar estado del servicio
        if ($service->status !== "pendiente" && $service->status !== "en_progreso") {
            return redirect()->back()->with("error", "Este servicio no puede ser iniciado");
        }

        // Redirigir a página profesional de captura de geolocalización
        return redirect()->route("technician.service.checklist.location", $service);
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

        return view("technician.capture-location-simple", compact("service"));
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
            case 'fumigacion-de-jardines':
                $nextStage = "points";
                break;
            case 'desinfeccion':
                $nextStage = "products";
                break;
            case 'desratizacion':
                $nextStage = "points";
                break;
            case 'desinsectacion':
                $nextStage = "products";
                break;
            default:
                $nextStage = "points";
                break;
        }
        //dd($nextStage);
        return redirect()->route('technician.service.checklist.stage', ['service' => $service, 'stage' => $nextStage])
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

        $service->load(['client', 'serviceType']);

        // Preparar variables para la etapa de productos (si es la etapa actual)
        $products = collect();
        $stageInstruction = '';
        
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

        return view('technician.checklist-staged', compact('service', 'products', 'stageInstruction'));
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

        // Validar que la etapa sea válida
        $validStages = ["points", "products", "results", "observations", "sites", "description"];
        if (!in_array($stage, $validStages)) {
            abort(404, "Etapa no válida");
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
                'fumigacion-de-jardines' => 'desinsectacion', // Mapear fumigación a desinsectación
                'servicios-especiales' => 'sanitizacion' // Mapear servicios especiales a sanitización
            ];
            
            

            $productServiceType = $serviceTypeMapping[$service->service_type] ?? null;
            
            if ($productServiceType) {
                $products = \App\Models\Product::where('service_type', $productServiceType)
                    ->where('stock', '>', 0) // Solo productos con stock disponible
                    ->orderBy('name')
                    ->get();
            }
            
            // Obtener el texto de instrucción específico para el tipo de servicio
            $stageInstruction = $this->getProductStageInstruction($service->service_type);
        }
        
        // Asegurar que las variables siempre estén definidas
        $products = $products ?? collect();
        $stageInstruction = $stageInstruction ?? '';
        
        return view("technician.checklist-stages." . $stage, compact("service", "products", "stageInstruction"));
        //return view("technician.checklist-staged", compact("service"));
      }
    public function saveChecklistStage(Request $request, Service $service)
    {
        // Verificar permisos
        if ($service->assigned_to !== auth()->id() && !auth()->user()->hasRole("super-admin")) {
            abort(403, "No tienes permisos para acceder a este servicio");
        }

        $currentStage = $request->input('current_stage');
        $nextStage = $request->input('next_stage');

        // Obtener datos existentes del checklist
        $checklistData = $service->checklist_data ?? [];

        // Guardar datos de la etapa actual
        switch ($currentStage) {
            case 'points':
                $checklistData['points'] = $request->input('points', []);
                break;
            case 'products':
                $appliedProduct = $request->input('applied_product');
                $productId = $request->input('product_id'); // ID del producto seleccionado
                
                $checklistData['products'] = [
                    'applied_product' => $appliedProduct,
                    'product_id' => $productId, // Guardar también el ID para futuras referencias
                    'applied_at' => now()->format('Y-m-d H:i:s')
                ];
                break;
            case 'results':
                $resultsData = [];
                if ($service->service_type === 'desratizacion') {
                    $resultsData = [
                        'observed_results' => $request->input('observed_results', []),
                        'total_installed_points' => $request->input('total_installed_points'),
                        'total_consumption_activity' => $request->input('total_consumption_activity')
                    ];
                }elseif ($service->service_type === 'desinsectacion') {
                    $resultsData['uv_lamps'] = $request->input('uv_lamps');
                    $resultsData['tuv'] = $request->input('tuv');
                    $resultsData['devices_installed'] = $request->input('devices_installed');
                    $resultsData['devices_existing'] = $request->input('devices_existing');
                    $resultsData['devices_replaced'] = $request->input('devices_replaced');
                }
                
                $checklistData['results'] = $resultsData;
                break;
            case 'observations':
                // Obtener observaciones existentes
                $existingObservations = $service->checklist_data["observations"] ?? [];
                Log::info("Existing observations: " . json_encode($existingObservations));
                
                // Crear nueva observación
                $newObservation = [
                    'cebadera_code' => $request->input('cebadera_code'),
                    'observation_number' => $request->input('observation_number'),
                    'detail' => $request->input('detail'),
                    'complementary' => $request->input('complementary'),
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
                        Log::warning('No se pudo comprimir la imagen, guardando original');
                        $originalFilename = time() . '_' . $photo->getClientOriginalName();
                        $photo->storeAs('observations', $originalFilename, 'public');
                        $newObservation['photo'] = 'storage/observations/' . $originalFilename;
                    }
                }
                
                // Agregar nueva observación al array
                $existingObservations[] = $newObservation;
                $checklistData['observations'] = $existingObservations;
                break;
            case 'sites':
                $checklistData['sites'] = [
                    'treated_sites' => $request->input('treated_sites', '')
                ];
                break;
            case 'description':
                $checklistData['description'] = [
                    'service_description' => $request->input('service_description', ''),
                    'service_sugerencia' => $request->input('service_sugerencia', ''),
                    'technician_signature' => $request->input('technician_signature'),
                    'client_signature' => $request->input('client_signature'),
                    'completion_date' => now()->format('Y-m-d H:i:s')
                ];
                break;
        }

        // Si es la etapa final, marcar como completado
        if ($nextStage === "completed") {
            $service->update([
                'checklist_data' => $checklistData,
                'status' => 'finalizado',
                'checklist_completed_at' => now(),
            ]);

            return redirect()->route('technician.service.detail', $service)
                ->with('success', 'Checklist completado exitosamente');
        }

        // Actualizar datos del checklist y etapa actual
        $service->update([
                'checklist_data' => $checklistData,
                'checklist_stage' => $nextStage
            ]);

        if (in_array($nextStage, ["results"]) && count($checklistData['results'] ?? [])===0) {
            $nextStage = "observations";
        }    

        return redirect()->route('technician.service.checklist.stage', ['service' => $service, 'stage' => $nextStage])
            ->with('success', 'Etapa guardada correctamente');
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
        
        $filename = "servicio-{$service->id}-{$service->client->name}-{$validationId}.pdf";
        
        return $pdf->download($filename);
    }
    public function showChecklistDetails(Service $service)
    {
        // Verificar permisos
        if ($service->assigned_to !== auth()->id() && !auth()->user()->hasRole("super-admin")) {
            abort(403, "No tienes permisos para acceder a este servicio");
        }

        $service->load(["client", "serviceType", "assignedUser"]);

        return view("technician.service-checklist-details", compact("service"));
    }

    public function completeService(Service $service)
    {
        // Verificar permisos
        if ($service->assigned_to !== auth()->id() && !auth()->user()->hasRole("super-admin")) {
            abort(403, "No tienes permisos para completar este servicio");
        }

        // Verificar estado del servicio
        if ($service->status !== "en_progreso") {
            return redirect()->back()->with("error", "Este servicio no puede ser completado");
        }

        // Actualizar estado
        $service->update(["status" => "finalizado"]);
        
        return redirect()->back()->with("success", "Servicio completado exitosamente");
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
