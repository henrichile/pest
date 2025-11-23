<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'name',
        'address',
        'city',
        'region',
        'country',
        'postal_code',
        'type',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class);
    }
}
