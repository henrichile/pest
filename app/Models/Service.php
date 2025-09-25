<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Log;

class Service extends Model
{
    use HasFactory;

    protected static function boot()
    {
        parent::boot();
        
        // Mantener consistencia entre service_type y service_type_id
        static::saving(function ($service) {
            // Si hay service_type_id, sincronizar service_type desde la relación
            if ($service->service_type_id && $service->serviceType) {
                $service->service_type = $service->serviceType->slug;
            }
            // Si hay service_type pero no service_type_id, buscar y asignar el ID
            elseif ($service->service_type && !$service->service_type_id) {
                $serviceType = ServiceType::where('slug', $service->service_type)->first();
                if ($serviceType) {
                    $service->service_type_id = $serviceType->id;
                }
            }
        });
    }

    protected $fillable = [
        "client_id",
        "service_type",
        "service_type_id",
        "scheduled_date",
        "address",
        "latitude",
        "longitude", 
        "location_accuracy",
        "location_captured_at",
        "priority",
        "status",
        "description",
        "assigned_to",
        "started_at",
        "completed_at",
        // Campos del checklist originales
        "installed_points",
        "existing_points",
        "spare_points",
        "bait_weight",
        "applied_products",
        "observed_results",
        "total_installed_points",
        "total_consumption_activity",
        "treated_sites",
        "checklist_completed",
        "checklist_completed_at",
        // Campos adicionales del checklist
        "installed_points_check",
        "existing_points_check",
        "spare_points_check",
        "bait_weight_check",
        "physical_installed_check",
        "physical_existing_check",
        "physical_spare_check",
        "applied_product",
        "service_description",
        // Campos de etapas
        "checklist_stage",
        "checklist_data",
    ];

    protected $casts = [
        "scheduled_date" => "datetime",
        "started_at" => "datetime",
        "completed_at" => "datetime",
        "checklist_completed_at" => "datetime",
        "location_captured_at" => "datetime",
        "observed_results" => "array",
        "checklist_data" => "array",
        "bait_weight" => "decimal:2",
        "total_consumption_activity" => "decimal:2",
        // Casts para campos booleanos
        "installed_points_check" => "boolean",
        "existing_points_check" => "boolean",
        "spare_points_check" => "boolean",
        "bait_weight_check" => "boolean",
        "physical_installed_check" => "boolean",
        "physical_existing_check" => "boolean",
        "physical_spare_check" => "boolean",
        "checklist_completed" => "boolean",
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, "assigned_to");
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, "service_products")
            ->withPivot("quantity", "used_at")
            ->withTimestamps();
    }

    public function observations(): HasMany
    {
        return $this->hasMany(ServiceObservation::class);
    }

    /**
     * Obtener la etapa siguiente del checklist
     */
    public function getNextStage(): string
    {
        $stages = ["points", "products", "results", "observations", "sites", "description"];
        $currentIndex = array_search($this->checklist_stage, $stages);
        
        if ($currentIndex !== false && $currentIndex < count($stages) - 1) {
            return $stages[$currentIndex + 1];
        }
        
        return $this->checklist_stage;
    }

    /**
     * Obtener la etapa anterior del checklist
     */
    public function getPreviousStage(): string
    {
        $stages = ["points", "products", "results", "observations", "sites", "description"];
        $currentIndex = array_search($this->checklist_stage, $stages);
        
        if ($currentIndex !== false && $currentIndex > 0) {
            return $stages[$currentIndex - 1];
        }
        
        return $this->checklist_stage;
    }

    /**
     * Verificar si es la última etapa
     */
    public function isLastStage(): bool
    {
        return $this->checklist_stage === "description";
    }

    /**
     * Verificar si es la primera etapa
     */
    public function isFirstStage(): bool
    {
        return $this->checklist_stage === "points";
    }

    /**
     * Obtener el número de etapa actual
     */
    public function getStageNumber(): int
    {
        $stages = ["points", "products", "results", "observations", "sites", "description"];
        $currentIndex = array_search($this->checklist_stage, $stages);
        
        return $currentIndex !== false ? $currentIndex + 1 : 1;
    }

    /**
     * Obtener el porcentaje de progreso
     */
    public function getProgressPercentage(): int
    {
        $stages = ["points", "products", "results", "observations", "sites", "description"];
        $currentIndex = array_search($this->checklist_stage, $stages);
        
        if ($currentIndex !== false) {
            return round((($currentIndex + 1) / count($stages)) * 100);
        }
        
        return 0;
    }

    /**
     * Obtener el nombre de la etapa actual
     */
    public function getStageName(): string
    {
        $stageNames = [
            "points" => "Check de Puntos",
            "products" => "Productos Aplicados",
            "results" => "Resultados Observados",
            "observations" => "Observaciones Detalladas",
            "sites" => "Sitios Tratados",
            "description" => "Descripción del Servicio"
        ];
        
        return $stageNames[$this->checklist_stage] ?? "Etapa Desconocida";
    }
    public function photos()
    {
        return $this->hasMany(ServicePhoto::class);
    }

    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class);
    }

    /**
     * Generar imagen de mapa del servicio usando Mapbox
     *
     * @param int $width Ancho de la imagen
     * @param int $height Alto de la imagen
     * @param int $zoom Nivel de zoom
     * @param string $style Estilo del mapa
     * @return string|null URL de la imagen o null si no hay coordenadas
     */
    public function generateMapImage(
        int $width = 600, 
        int $height = 400, 
        int $zoom = 15, 
        string $style = 'streets-v11'
    ): ?string {
        if (!$this->latitude || !$this->longitude) {
            return null;
        }

        try {
            return \App\Helpers\MapboxHelper::generateMapboxImageUrl(
                $this->latitude,
                $this->longitude,
                $width,
                $height,
                $zoom,
                $style
            );
        } catch (\Exception $e) {
            Log::warning('Error generando mapa para servicio ' . $this->id . ': ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Verificar si el servicio tiene coordenadas para generar mapa
     *
     * @return bool
     */
    public function hasCoordinates(): bool
    {
        return !empty($this->latitude) && !empty($this->longitude);
    }

    /**
     * Obtener las coordenadas como string formateado
     *
     * @return string|null
     */
    public function getCoordinatesString(): ?string
    {
        if (!$this->hasCoordinates()) {
            return null;
        }

        return "{$this->latitude}, {$this->longitude}";
    }
}
