<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "rut",
        "email",
        "phone",
        "address",
        "city",
        "region",
        "country",
        "postal_code",
        "business_type",
        "industry",
        "notes",
        "contact_person",
    ];

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function sites()
    {
        return $this->hasMany(Site::class);
    }

    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class);
    }
}
