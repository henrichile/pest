<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Nonconformity extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'work_order_id',
        'item_id',
        'severity',
        'description',
        'corrective_action',
        'resolved_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'resolved_at' => 'datetime',
        ];
    }

    /**
     * Get the work order that owns this nonconformity.
     */
    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    /**
     * Get the checklist item that owns this nonconformity.
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(ChecklistItem::class, 'item_id');
    }

    /**
     * Check if nonconformity is resolved.
     */
    public function isResolved(): bool
    {
        return !is_null($this->resolved_at);
    }

    /**
     * Check if nonconformity is critical.
     */
    public function isCritical(): bool
    {
        return $this->severity === 'critical';
    }

    /**
     * Check if nonconformity is high severity.
     */
    public function isHighSeverity(): bool
    {
        return in_array($this->severity, ['high', 'critical']);
    }

    /**
     * Get severity color for UI.
     */
    public function getSeverityColorAttribute(): string
    {
        return match($this->severity) {
            'low' => 'green',
            'medium' => 'yellow',
            'high' => 'orange',
            'critical' => 'red',
            default => 'gray'
        };
    }

    /**
     * Scope to get unresolved nonconformities.
     */
    public function scopeUnresolved($query)
    {
        return $query->whereNull('resolved_at');
    }

    /**
     * Scope to get resolved nonconformities.
     */
    public function scopeResolved($query)
    {
        return $query->whereNotNull('resolved_at');
    }

    /**
     * Scope to get nonconformities by severity.
     */
    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }
}
