<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChecklistStage extends Model
{
    use HasFactory;

    protected $fillable = [
        'checklist_template_id',
        'name',
        'description',
        'order_index',
        'is_required',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'order_index' => 'integer',
    ];

    /**
     * Relación con ChecklistTemplate
     */
    public function checklistTemplate(): BelongsTo
    {
        return $this->belongsTo(ChecklistTemplate::class);
    }

    /**
     * Relación con ChecklistItems
     */
    public function items(): HasMany
    {
        return $this->hasMany(ChecklistItem::class)->orderBy('order_index');
    }

    /**
     * Scope para ordenar por orden
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order_index');
    }
}
