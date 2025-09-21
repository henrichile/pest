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
}
