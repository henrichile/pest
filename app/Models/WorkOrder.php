<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class WorkOrder extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, LogsActivity, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'folio',
        'client_id',
        'site_id',
        'service_id',
        'status',
        'sla_at',
        'priority',
        'notes',
        'infestation_level_before',
        'infestation_level_after',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sla_at' => 'datetime',
            'infestation_level_before' => 'integer',
            'infestation_level_after' => 'integer',
        ];
    }

    /**
     * Get the activity log options for the model.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['folio', 'status', 'priority', 'sla_at'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Get the client that owns this work order.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the site that owns this work order.
     */
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * Get the service that owns this work order.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the assignments for this work order.
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(WorkOrderAssignment::class);
    }

    /**
     * Get the work sessions for this work order.
     */
    public function workSessions(): HasMany
    {
        return $this->hasMany(WorkSession::class);
    }

    /**
     * Get the treatments for this work order.
     */
    public function treatments(): HasMany
    {
        return $this->hasMany(Treatment::class);
    }

    /**
     * Get the checklist responses for this work order.
     */
    public function checklistResponses(): HasMany
    {
        return $this->hasMany(ChecklistResponse::class);
    }

    /**
     * Get the nonconformities for this work order.
     */
    public function nonconformities(): HasMany
    {
        return $this->hasMany(Nonconformity::class);
    }

    /**
     * Get the ratings for this work order.
     */
    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    /**
     * Get the invoices for this work order.
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Get assigned users.
     */
    public function assignedUsers()
    {
        return $this->belongsToMany(User::class, 'work_order_assignments')
            ->withPivot('role_in_ot')
            ->withTimestamps();
    }

    /**
     * Check if work order is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->sla_at && $this->sla_at->isPast() && $this->status !== 'completed';
    }

    /**
     * Get current work session.
     */
    public function getCurrentWorkSession()
    {
        return $this->workSessions()
            ->whereNull('end_at')
            ->first();
    }

    /**
     * Get total duration in seconds.
     */
    public function getTotalDurationAttribute(): int
    {
        return $this->workSessions()->sum('duration_seconds');
    }

    /**
     * Get formatted duration.
     */
    public function getFormattedDurationAttribute(): string
    {
        $seconds = $this->total_duration;
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        
        return sprintf('%02d:%02d', $hours, $minutes);
    }

    /**
     * Scope to get work orders by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get overdue work orders.
     */
    public function scopeOverdue($query)
    {
        return $query->where('sla_at', '<', now())
            ->where('status', '!=', 'completed');
    }

    /**
     * Scope to get work orders assigned to user.
     */
    public function scopeAssignedTo($query, $userId)
    {
        return $query->whereHas('assignments', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }
}
