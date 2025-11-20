<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChecklistResponse extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'work_order_id',
        'template_id',
        'compliance_percentage',
        'signed_by',
        'signed_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'compliance_percentage' => 'decimal:2',
            'signed_at' => 'datetime',
        ];
    }

    /**
     * Get the work order that owns this response.
     */
    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    /**
     * Get the template that owns this response.
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(ChecklistTemplate::class, 'template_id');
    }

    /**
     * Get the response items for this response.
     */
    public function responseItems(): HasMany
    {
        return $this->hasMany(ChecklistResponseItem::class, 'response_id');
    }

    /**
     * Check if response is signed.
     */
    public function isSigned(): bool
    {
        return !is_null($this->signed_at);
    }

    /**
     * Check if response meets compliance threshold.
     */
    public function meetsComplianceThreshold($threshold = 80): bool
    {
        return $this->compliance_percentage >= $threshold;
    }

    /**
     * Get non-compliant items.
     */
    public function getNonCompliantItems()
    {
        return $this->responseItems()->where('is_ok', false)->get();
    }

    /**
     * Calculate compliance percentage.
     */
    public function calculateCompliancePercentage(): void
    {
        $totalItems = $this->responseItems()->count();
        $compliantItems = $this->responseItems()->where('is_ok', true)->count();
        
        if ($totalItems > 0) {
            $this->compliance_percentage = ($compliantItems / $totalItems) * 100;
        } else {
            $this->compliance_percentage = 0;
        }
    }
}
