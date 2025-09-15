<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistTemplate extends Model
{
    use HasFactory;

    protected  = [
        'name',
        'description',
        'service_type_id',
        'is_active',
    ];

    protected  = [
        'is_active' => 'boolean',
    ];

    public function serviceType()
    {
        return ->belongsTo(ServiceType::class);
    }

    public function items()
    {
        return ->hasMany(ChecklistItem::class)->orderBy('order');
    }
}
