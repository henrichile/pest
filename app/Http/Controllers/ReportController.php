<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    /**
     * Generar PDF del servicio
     */
    public function generatePdf(Service $service)
    {
        if ($service->assigned_to !== auth()->id() && !auth()->user()->hasRole("super-admin")) {
            abort(403, "No tienes acceso a este servicio");
        }

        if (!$service->checklist_completed) {
            return redirect()->back()->with("error", "El checklist debe estar completado para generar el PDF");                                                                                                         
        }

        // Verificar si ya existe un reporte para este servicio
        $existingReport = ServiceReport::where("service_id", $service->id)->first();
        
        if ($existingReport) {
            return redirect()->route("reports.download", $existingReport);
        }

        // Generar datos del reporte
        $reportNumber = ServiceReport::generateReportNumber();
        $qrCode = ServiceReport::generateQRCode();
        
        // Generar código QR
        $qrCodeSvg = QrCode::size(80)
            ->format("svg")
            ->generate($this->getValidationUrl($qrCode));

        // Preparar datos para el PDF
        $checklistData = $service->checklist_data ?? [];
        
        // Cargar relaciones necesarias
        $service->load(["client", "assignedUser", "observations"]);

        // Generar PDF
        $pdf = Pdf::loadView("reports.service-report", [
            "service" => $service,
            "checklistData" => $checklistData,
            "qrCode" => $qrCodeSvg,
            "report" => (object) [
                "report_number" => $reportNumber,
                "qr_code" => $qrCode,
                "generated_at" => now(),
            ]
        ]);

        $pdf->setPaper("A4", "portrait");
        $pdf->setOptions([
            "isHtml5ParserEnabled" => true,
            "isRemoteEnabled" => true,
            "defaultFont" => "Arial",
        ]);

        // Guardar PDF en storage
        $fileName = "service-report-{$service->id}-{$reportNumber}.pdf";
        $filePath = "reports/{$fileName}";
        
        Storage::disk("public")->put($filePath, $pdf->output());

        // Crear registro en base de datos
        $report = ServiceReport::create([
            "service_id" => $service->id,
            "generated_by" => auth()->id(),
            "report_number" => $reportNumber,
            "file_path" => $filePath,
            "qr_code" => $qrCode,
            "report_data" => [
                "service_id" => $service->id,
                "client_name" => $service->client->name ?? "N/A",
                "service_type" => $service->service_type ?? "Desratización",
                "address" => $service->address ?? "N/A",
                "technician_name" => $service->assignedUser->name ?? "N/A",
                "checklist_data" => $checklistData,
                "observations_count" => $service->observations->count(),
                "generated_at" => now()->toISOString(),
            ],
            "generated_at" => now(),
        ]);

        return redirect()->route("reports.download", $report)
            ->with("success", "PDF generado exitosamente");
    }

    /**
     * Descargar PDF del reporte - VERSIÓN CORREGIDA SIN MODEL BINDING
     */
    public function downloadPdf($id)
    {
        Log::info("Iniciando descarga de PDF", [
            'report_id' => $id,
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email ?? 'unknown'
        ]);

        try {
            // Verificar que el usuario esté autenticado
            if (!auth()->check()) {
                Log::warning("Usuario no autenticado intentando descargar PDF", ['report_id' => $id]);
                abort(401, "Debes estar autenticado para descargar este archivo");
            }

            // Buscar el reporte manualmente
            $report = ServiceReport::find($id);
            
            if (!$report) {
                Log::error("Reporte no encontrado", ['report_id' => $id]);
                abort(404, "Reporte no encontrado");
            }

            Log::info("Reporte encontrado", [
                'report_id' => $report->id,
                'file_path' => $report->file_path ?? 'unknown'
            ]);

            // Verificar permisos
            if ($report->service && $report->service->assigned_to !== auth()->id() && !auth()->user()->hasRole("super-admin")) {                                                                                            
                Log::warning("Usuario sin permisos intentando descargar PDF", [
                    'report_id' => $report->id,
                    'user_id' => auth()->id(),
                    'service_assigned_to' => $report->service->assigned_to ?? 'unknown'
                ]);
                abort(403, "No tienes acceso a este reporte");
            }

            // Verificar que el archivo existe
            if (empty($report->file_path)) {
                Log::error("Ruta de archivo vacía", ['report_id' => $report->id]);
                abort(404, "Archivo PDF no encontrado");
            }

            $filePath = storage_path("app/public/{$report->file_path}");
            
            Log::info("Verificando archivo", [
                'report_id' => $report->id,
                'file_path' => $filePath,
                'file_exists' => file_exists($filePath),
                'is_file' => is_file($filePath)
            ]);
            
            if (!file_exists($filePath)) {
                Log::error("Archivo PDF no encontrado en filesystem", [
                    'report_id' => $report->id,
                    'file_path' => $filePath
                ]);
                abort(404, "Archivo PDF no encontrado en: " . $filePath);
            }

            // Verificar que es un archivo válido
            if (!is_file($filePath)) {
                Log::error("El archivo no es válido", [
                    'report_id' => $report->id,
                    'file_path' => $filePath
                ]);
                abort(404, "El archivo no es válido");
            }

            // Obtener el contenido del archivo
            $fileContent = file_get_contents($filePath);
            
            if ($fileContent === false) {
                Log::error("Error al leer el archivo PDF", [
                    'report_id' => $report->id,
                    'file_path' => $filePath
                ]);
                abort(500, "Error al leer el archivo PDF");
            }

            // Verificar que el contenido no esté vacío
            if (empty($fileContent)) {
                Log::error("Archivo PDF vacío", [
                    'report_id' => $report->id,
                    'file_path' => $filePath
                ]);
                abort(500, "El archivo PDF está vacío");
            }

            // Verificar que es un PDF válido
            if (strpos($fileContent, '%PDF') !== 0) {
                Log::error("Archivo no es un PDF válido", [
                    'report_id' => $report->id,
                    'file_path' => $filePath,
                    'content_start' => substr($fileContent, 0, 20)
                ]);
                abort(500, "El archivo no es un PDF válido");
            }

            // Generar nombre de archivo seguro
            $safeFileName = preg_replace('/[^a-zA-Z0-9\-_\.]/', '_', $report->report_number) . '.pdf';
            
            Log::info("Preparando descarga", [
                'report_id' => $report->id,
                'file_size' => strlen($fileContent),
                'safe_filename' => $safeFileName
            ]);

            // Crear respuesta con headers correctos
            $response = response($fileContent, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $safeFileName . '"',
                'Content-Length' => strlen($fileContent),
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
                'X-Content-Type-Options' => 'nosniff',
                'X-Frame-Options' => 'DENY',
            ]);

            Log::info("Descarga exitosa", [
                'report_id' => $report->id,
                'file_size' => strlen($fileContent),
                'user_id' => auth()->id()
            ]);

            return $response;

        } catch (\Exception $e) {
            Log::error("Error downloading PDF: " . $e->getMessage(), [
                'report_id' => $id ?? 'unknown',
                'user_id' => auth()->id(),
                'exception' => $e->getTraceAsString()
            ]);
            
            abort(500, "Error interno del servidor al descargar el PDF: " . $e->getMessage());
        }
    }

    /**
     * Ver PDF en el navegador - VERSIÓN CORREGIDA SIN MODEL BINDING
     */
    public function viewPdf($id)
    {
        Log::info("Iniciando visualización de PDF", [
            'report_id' => $id,
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email ?? 'unknown'
        ]);

        try {
            // Verificar que el usuario esté autenticado
            if (!auth()->check()) {
                Log::warning("Usuario no autenticado intentando visualizar PDF", ['report_id' => $id]);
                abort(401, "Debes estar autenticado para visualizar este archivo");
            }

            // Buscar el reporte manualmente
            $report = ServiceReport::find($id);
            
            if (!$report) {
                Log::error("Reporte no encontrado", ['report_id' => $id]);
                abort(404, "Reporte no encontrado");
            }

            // Verificar permisos
            if ($report->service && $report->service->assigned_to !== auth()->id() && !auth()->user()->hasRole("super-admin")) {                                                                                            
                Log::warning("Usuario sin permisos intentando visualizar PDF", [
                    'report_id' => $report->id,
                    'user_id' => auth()->id(),
                    'service_assigned_to' => $report->service->assigned_to ?? 'unknown'
                ]);
                abort(403, "No tienes acceso a este reporte");
            }

            // Verificar que el archivo existe
            if (empty($report->file_path)) {
                Log::error("Ruta de archivo vacía", ['report_id' => $report->id]);
                abort(404, "Archivo PDF no encontrado");
            }

            $filePath = storage_path("app/public/{$report->file_path}");
            
            if (!file_exists($filePath)) {
                Log::error("Archivo PDF no encontrado en filesystem", [
                    'report_id' => $report->id,
                    'file_path' => $filePath
                ]);
                abort(404, "Archivo PDF no encontrado en: " . $filePath);
            }

            // Verificar que es un archivo válido
            if (!is_file($filePath)) {
                Log::error("El archivo no es válido", [
                    'report_id' => $report->id,
                    'file_path' => $filePath
                ]);
                abort(404, "El archivo no es válido");
            }

            // Obtener el contenido del archivo
            $fileContent = file_get_contents($filePath);
            
            if ($fileContent === false) {
                Log::error("Error al leer el archivo PDF", [
                    'report_id' => $report->id,
                    'file_path' => $filePath
                ]);
                abort(500, "Error al leer el archivo PDF");
            }

            // Verificar que el contenido no esté vacío
            if (empty($fileContent)) {
                Log::error("Archivo PDF vacío", [
                    'report_id' => $report->id,
                    'file_path' => $filePath
                ]);
                abort(500, "El archivo PDF está vacío");
            }

            // Verificar que es un PDF válido
            if (strpos($fileContent, '%PDF') !== 0) {
                Log::error("Archivo no es un PDF válido", [
                    'report_id' => $report->id,
                    'file_path' => $filePath,
                    'content_start' => substr($fileContent, 0, 20)
                ]);
                abort(500, "El archivo no es un PDF válido");
            }

            Log::info("Preparando visualización", [
                'report_id' => $report->id,
                'file_size' => strlen($fileContent)
            ]);

            // Crear respuesta para visualización en navegador
            $response = response($fileContent, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $report->report_number . '.pdf"',
                'Content-Length' => strlen($fileContent),
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
                'X-Content-Type-Options' => 'nosniff',
                'X-Frame-Options' => 'DENY',
            ]);

            Log::info("Visualización exitosa", [
                'report_id' => $report->id,
                'file_size' => strlen($fileContent),
                'user_id' => auth()->id()
            ]);

            return $response;

        } catch (\Exception $e) {
            Log::error("Error viewing PDF: " . $e->getMessage(), [
                'report_id' => $id ?? 'unknown',
                'user_id' => auth()->id(),
                'exception' => $e->getTraceAsString()
            ]);
            
            abort(500, "Error interno del servidor al visualizar el PDF: " . $e->getMessage());
        }
    }

    /**
     * Validar reporte mediante QR
     */
    public function validateReport($qrCode)
    {
        $report = ServiceReport::where("qr_code", $qrCode)->first();
        
        if (!$report) {
            abort(404, "Reporte no encontrado");
        }

        $report->load(["service.client", "service.assignedUser", "generatedBy"]);

        return view("reports.validation", compact("report"));
    }

    /**
     * Historial de reportes (Solo Super Admin)
     */
    public function history()
    {
        if (!auth()->user()->hasRole("super-admin")) {
            abort(403, "No tienes permisos para acceder al historial");
        }

        $reports = ServiceReport::with(["service.client", "generatedBy"])
            ->latest()
            ->paginate(20);

        return view("reports.history", compact("reports"));
    }

    /**
     * Obtener URL de validación
     */
    private function getValidationUrl($qrCode)
    {
        return url("/validate-report/{$qrCode}");
    }

    /**
     * Compartir reporte por email (futuro)
     */
    public function shareReport(Request $request, ServiceReport $report)
    {
        // TODO: Implementar envío por email
        return redirect()->back()->with("info", "Funcionalidad de compartir por email próximamente");
    }
}
