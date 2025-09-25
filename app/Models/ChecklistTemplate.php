<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'service_type_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function items()
    {
        return $this->hasMany(ChecklistItem::class)->orderBy('order');
    }

    /**
     * Relación con ChecklistStages
     */
    public function stages()
    {
        return $this->hasMany(ChecklistStage::class)->orderBy('order_index');
    }

    /**
     * Items agrupados por etapas
     */
    public function itemsByStages()
    {
        return $this->stages()->with(['items' => function($query) {
            $query->orderBy('order_index');
        }]);
    }
}
