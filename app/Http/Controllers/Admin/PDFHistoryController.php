<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PDFHistoryController extends Controller
{
    public function index()
    {
        // Solo Super Admin puede acceder
        if (!Auth::user()->hasRole('super-admin')) {
            abort(403, 'No tienes permisos para acceder a esta sección');
        }

        $services = Service::whereNotNull('pdf_generated_at')
            ->with(['client', 'serviceType', 'assignedUser'])
            ->orderBy('pdf_generated_at', 'desc')
            ->paginate(20);

        return view('admin.pdf-history', compact('services'));
    }

    public function show(Service $service)
    {
        // Solo Super Admin puede acceder
        if (!Auth::user()->hasRole('super-admin')) {
            abort(403, 'No tienes permisos para acceder a esta sección');
        }

        // Verificar que el servicio tenga PDF generado
        if (!$service->pdf_generated_at) {
            abort(404, 'Este servicio no tiene PDF generado');
        }

        $service->load(['client', 'serviceType', 'assignedUser']);

        return view('admin.pdf-detail', compact('service'));
    }

    public function regenerate(Service $service)
    {
        // Solo Super Admin puede acceder
        if (!Auth::user()->hasRole('super-admin')) {
            abort(403, 'No tienes permisos para acceder a esta sección');
        }

        // Verificar que el servicio esté completado
        if ($service->status !== 'finalizado') {
            abort(403, 'Solo se pueden regenerar PDFs de servicios completados');
        }

        // Generar nuevo ID de validación
        $validationId = 'PC-' . $service->id . '-' . now()->format('YmdHis') . '-' . substr(md5($service->id . now()), 0, 8);
        
        // Generar nuevo hash de integridad
        $integrityData = $service->id . $service->client->name . $service->checklist_completed_at . json_encode($service->checklist_data);
        $integrityHash = hash('sha256', $integrityData);
        
        // Generar QR Code
        $qrData = json_encode([
            'service_id' => $service->id,
            'validation_id' => $validationId,
            'integrity_hash' => $integrityHash,
            'generated_at' => now()->toISOString(),
            'client' => $service->client->name,
            'technician' => $service->assignedUser->name ?? 'N/A',
            'regenerated_by' => Auth::user()->name
        ]);
        
        $qrCode = base64_encode(file_get_contents('https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($qrData)));
        
        // Actualizar información de trazabilidad
        $service->update([
            'pdf_generated_at' => now(),
            'pdf_validation_id' => $validationId,
            'pdf_integrity_hash' => $integrityHash
        ]);

        $service->load(['client', 'serviceType', 'assignedUser']);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('technician.service-pdf', compact('service', 'validationId', 'integrityHash', 'qrCode'));
        
        $filename = "servicio-{$service->id}-{$service->client->name}-{$validationId}-REGENERADO.pdf";
        
        return $pdf->download($filename);
    }

    public function validate(Request $request)
    {
        $validationId = $request->input('validation_id');
        
        if (!$validationId) {
            return response()->json(['valid' => false, 'message' => 'ID de validación requerido']);
        }

        $service = Service::where('pdf_validation_id', $validationId)->first();
        
        if (!$service) {
            return response()->json(['valid' => false, 'message' => 'PDF no encontrado o inválido']);
        }

        // Verificar integridad
        $integrityData = $service->id . $service->client->name . $service->checklist_completed_at . json_encode($service->checklist_data);
        $calculatedHash = hash('sha256', $integrityData);
        
        $isValid = $calculatedHash === $service->pdf_integrity_hash;

        return response()->json([
            'valid' => $isValid,
            'service' => $isValid ? [
                'id' => $service->id,
                'client' => $service->client->name,
                'technician' => $service->assignedUser->name ?? 'N/A',
                'generated_at' => $service->pdf_generated_at,
                'status' => $service->status
            ] : null,
            'message' => $isValid ? 'PDF válido' : 'PDF alterado o inválido'
        ]);
    }
}
