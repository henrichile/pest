<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "active_ingredient",
        "service_type",
        "sag_registration",
        "isp_registration",
        "stock",
        "unit",
        "description",
    ];

    public function services()
    {
        return $this->belongsToMany(Service::class, "service_products")
            ->withPivot("quantity", "used_at")
            ->withTimestamps();
    }
}
