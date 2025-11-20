<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaterialMovement extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'material_id',
        'type',
        'quantity',
        'work_order_id',
        'batch_number',
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
            'quantity' => 'decimal:2',
        ];
    }

    /**
     * Get the material that owns this movement.
     */
    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    /**
     * Get the work order that owns this movement.
     */
    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    /**
     * Check if movement is incoming.
     */
    public function isIncoming(): bool
    {
        return $this->type === 'in';
    }

    /**
     * Check if movement is outgoing.
     */
    public function isOutgoing(): bool
    {
        return $this->type === 'out';
    }

    /**
     * Get formatted quantity with unit.
     */
    public function getFormattedQuantityAttribute(): string
    {
        return "{$this->quantity} {$this->material->unit}";
    }

    /**
     * Scope to get movements by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to get movements for specific work order.
     */
    public function scopeForWorkOrder($query, $workOrderId)
    {
        return $query->where('work_order_id', $workOrderId);
    }
}
