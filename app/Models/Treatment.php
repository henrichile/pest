<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Treatment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'work_order_id',
        'pest_id',
        'material_id',
        'dose',
        'unit',
        'method',
        'safety_time_hours',
        'evidence_media_id',
        'notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'dose' => 'decimal:2',
            'safety_time_hours' => 'integer',
        ];
    }

    /**
     * Get the work order that owns this treatment.
     */
    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    /**
     * Get the pest that owns this treatment.
     */
    public function pest(): BelongsTo
    {
        return $this->belongsTo(Pest::class);
    }

    /**
     * Get the material that owns this treatment.
     */
    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    /**
     * Get the evidence media for this treatment.
     */
    public function evidenceMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'evidence_media_id');
    }

    /**
     * Get formatted dose with unit.
     */
    public function getFormattedDoseAttribute(): string
    {
        return "{$this->dose} {$this->unit}";
    }

    /**
     * Get safety time in hours.
     */
    public function getSafetyTimeInHoursAttribute(): int
    {
        return $this->safety_time_hours ?? 0;
    }
}
