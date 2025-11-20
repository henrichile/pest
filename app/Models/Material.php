<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Material extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'type',
        'msds_url',
        'batch_number',
        'expires_at',
        'stock',
        'unit',
        'manufacturer',
        'base_concentration',
        'notes',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'expires_at' => 'date',
            'stock' => 'decimal:2',
            'base_concentration' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the activity log options for the model.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'type', 'stock', 'batch_number', 'expires_at'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Get the material movements for this material.
     */
    public function movements(): HasMany
    {
        return $this->hasMany(MaterialMovement::class);
    }

    /**
     * Get the treatments for this material.
     */
    public function treatments(): HasMany
    {
        return $this->hasMany(Treatment::class);
    }

    /**
     * Check if material is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if material is expiring soon (within 30 days).
     */
    public function isExpiringSoon(): bool
    {
        return $this->expires_at && $this->expires_at->isBefore(now()->addDays(30));
    }

    /**
     * Check if material is low stock (less than 10% of typical usage).
     */
    public function isLowStock(): bool
    {
        return $this->stock < 10; // Configurable threshold
    }

    /**
     * Get formatted stock with unit.
     */
    public function getFormattedStockAttribute(): string
    {
        return "{$this->stock} {$this->unit}";
    }

    /**
     * Scope to get only active materials.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get materials by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to get expired materials.
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }

    /**
     * Scope to get expiring soon materials.
     */
    public function scopeExpiringSoon($query)
    {
        return $query->where('expires_at', '<', now()->addDays(30))
            ->where('expires_at', '>', now());
    }

    /**
     * Scope to get low stock materials.
     */
    public function scopeLowStock($query)
    {
        return $query->where('stock', '<', 10);
    }
}
