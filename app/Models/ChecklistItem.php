<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'type',
        'options',
        'is_required',
        'order',
        'order_index',
        'checklist_template_id',
        'checklist_stage_id',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'options' => 'array',
    ];

    public function template()
    {
        return $this->belongsTo(ChecklistTemplate::class, 'checklist_template_id');
    }

    /**
     * Relación con ChecklistStage
     */
    public function stage()
    {
        return $this->belongsTo(ChecklistStage::class, 'checklist_stage_id');
    }
}
