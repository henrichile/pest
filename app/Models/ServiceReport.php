<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceReport extends Model
{
    use HasFactory;

    protected $fillable = [
        "service_id",
        "generated_by",
        "report_number",
        "file_path",
        "qr_code",
        "report_data",
        "generated_at",
    ];

    protected $casts = [
        "report_data" => "array",
        "generated_at" => "datetime",
    ];

    /**
     * Relación con el servicio
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Relación con el usuario que generó el reporte
     */
    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, "generated_by");
    }

    /**
     * Generar número de reporte único
     */
    public static function generateReportNumber(): string
    {
        $year = date("Y");
        $month = date("m");
        $day = date("d");
        
        $lastReport = self::whereDate("created_at", today())->latest()->first();
        $sequence = $lastReport ? (int) substr($lastReport->report_number, -4) + 1 : 1;
        
        return sprintf("RPT-%s%s%s-%04d", $year, $month, $day, $sequence);
    }

    /**
     * Generar código QR único
     */
    public static function generateQRCode(): string
    {
        return "PEST-" . uniqid() . "-" . time();
    }

    /**
     * Obtener URL de validación del reporte
     */
    public function getValidationUrl(): string
    {
        return url("/validate-report/" . $this->qr_code);
    }
}
