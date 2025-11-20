<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServicePhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'description',
        'photo_type',
        'taken_at',
    ];

    protected $casts = [
        'taken_at' => 'datetime',
        'file_size' => 'integer',
    ];

    /**
     * Relación con el servicio
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Obtener la URL completa del archivo
     */
    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }

    /**
     * Obtener el tamaño del archivo formateado
     */
    public function getFormattedSizeAttribute(): string
    {
        if (!$this->file_size) {
            return 'N/A';
        }

        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Obtener el tipo de foto formateado
     */
    public function getFormattedTypeAttribute(): string
    {
        return match($this->photo_type) {
            'before' => 'Antes del Servicio',
            'during' => 'Durante el Servicio',
            'after' => 'Después del Servicio',
            'evidence' => 'Evidencia',
            default => 'General'
        };
    }
}
