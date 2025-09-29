<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

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
        
        return view("technician.checklist-stages." . $stage, compact("service", "products", "stageInstruction"));
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
                        $oldPhotoPath = storage_path('app/public/' . $currentObservation['photo']);
                        if (file_exists($oldPhotoPath)) {
                            try {
                                unlink($oldPhotoPath);
                            } catch (\Exception $e) {
                                Log::warning('No se pudo eliminar la foto anterior: ' . $oldPhotoPath . '. Error: ' . $e->getMessage());
                            }
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
            $photoPath = storage_path('app/public/' . $observation['photo']);
            if (file_exists($photoPath)) {
                try {
                    unlink($photoPath);
                } catch (\Exception $e) {
                    Log::warning('No se pudo eliminar el archivo físico de la observación: ' . $photoPath . '. Error: ' . $e->getMessage());
                    // Continuar con la eliminación de la observación aunque no se pueda eliminar el archivo
                }
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
