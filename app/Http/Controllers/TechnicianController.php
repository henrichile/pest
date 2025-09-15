<?php

namespace App\Http\Controllers;

use App\Models\Service;
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

        return view('technician.dashboard', compact(
            'completedToday', 
            'pendingServices', 
            'inProgressServices', 
            'overdueServices'
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
        ]);

        // Actualizar servicio con ubicación y cambiar estado
        $service->update([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'address' => $request->address,
            'status' => 'en_progreso',
            'started_at' => now(),
        ]);

        // Definir la siguiente etapa del checklist
        $nextStage = "points";
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

        return view('technician.checklist-staged', compact('service'));
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
        return view("technician.checklist-stages." . $stage, compact("service"));
        return view("technician.checklist-staged", compact("service"));
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
                $checklistData['products'] = [
                    'applied_product' => $request->input('applied_product')
                ];
                break;
            case 'results':
                $checklistData['results'] = [
                    'observed_results' => $request->input('observed_results', []),
                    'total_installed_points' => $request->input('total_installed_points'),
                    'total_consumption_activity' => $request->input('total_consumption_activity')
                ];
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
                    $filename = time() . '_' . $photo->getClientOriginalName();
                    $photo->storeAs('public/observations', $filename);
                    $newObservation['photo'] = 'storage/observations/' . $filename;
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
}
