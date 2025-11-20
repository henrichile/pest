<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceObservation extends Model
{
    use HasFactory;

    protected $fillable = [
        "service_id",
        "bait_station_code",
        "observation_number",
        "detail",
        "photo_path",
        "complementary_observation",
    ];

    protected $casts = [
        "observation_number" => "integer",
    ];

    /**
     * Relación con el servicio
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
