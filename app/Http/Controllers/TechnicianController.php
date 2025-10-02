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
        $nextStage = $this->getNextStage($service->checklist_stage, $service->service_type);
        
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

        return view('technician.checklist-staged', compact('service', 'products', 'stageInstruction', 'nextStage'));
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
       
        return view("technician.checklist-stages." . $stage, compact("service", "products", "stageInstruction", "nextStage"));
    }


     public function saveChecklistStage(Request $request, Service $service)
    {
        // Verificar permisos
        if ($service->assigned_to !== auth()->id() && !auth()->user()->hasRole("super-admin")) {
            return response()->json(['success' => false, 'message' => 'No tienes permisos para modificar este servicio'], 403);
        }

        // Verificar estado del servicio
        if ($service->status !== "en_progreso") {
            return response()->json(['success' => false, 'message' => 'El servicio debe estar en progreso para guardar datos'], 403);
        }

        // Obtener la etapa actual del formulario
        $stage = $request->input('current_stage')?? $request->input('stage')?? $request->input('data_stage') ?? 'unknown';
        if($stage==="unknown"){
            $stage="points";
        }
        //dd($stage);
        // Validar que la etapa sea válida
        $validStages = ["points", "products", "results", "observations", "sites", "description"];
        if (!in_array($stage, $validStages)) {
            return response()->json(['success' => false, 'message' => 'Etapa no válida ('.$stage.')'], 400);
        }
        //dd("paso");

        try {
            $nextStage = ($request->input('next_stage') ?? $request->input('data_next_stage') ?? $this->getNextStage($stage, $service->service_type));
            // Obtener datos existentes del checklist
            $checklistData = $service->checklist_data ?? [];

            // Procesar datos según la etapa
                    // Actualizar la etapa actual del servicio
            $service->update(["checklist_stage" => $stage]);

            $service->load(["client", "serviceType"]);

            switch ($stage) {
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
                    $newObservations = $this->processObservationsData($request);
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
            // Actualizar la base de datos
            $service->update(['checklist_data' => $checklistData]);
            $stageInstruction = $this->getProductStageInstruction($service->service_type);
            // Determinar la siguiente etapa
             // Asegurar que las variables siempre estén definidas
            $products = $products ?? collect();
            $stageInstruction = $stageInstruction ?? '';
            $nextStage = ($nextStage!==null || $nextStage!=="")?$nextStage:$stage;
            if($nextStage==="completed"){
                $service->update(["status" => "finalizado"]);
                return redirect()->route("technician.service.detail", $service);
            }


            //return view("technician.checklist-stages." .$nextStage, compact("service", "products", "stageInstruction"));
            return redirect()->route('technician.service.checklist.stage', ['service' => $service, 'stage' => $nextStage])
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

    private function processObservationsData(Request $request)
    {
        $observations = [];
        
        // Si es una nueva observación desde el formulario
        if ($request->has('cebadera_code') || $request->has('detail')) {
            $newObservation = [
                'cebadera_code' => $request->input('cebadera_code', ''),
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
                    $observations[] = [
                        'cebadera_code' => $obs['cebadera_code'] ?? '',
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

    private function getNextStage($currentStage, $serviceType)
    {
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
        return redirect()->route('technician.service.checklist.stage', ['service' => $service, 'stage' => 'observations'])
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
        return redirect()->route('technician.service.checklist.stage', ['service' => $service, 'stage' => 'observations'])
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
