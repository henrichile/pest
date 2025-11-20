<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkSession extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'work_order_id',
        'user_id',
        'start_at',
        'end_at',
        'start_geo',
        'end_geo',
        'duration_seconds',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_at' => 'datetime',
            'end_at' => 'datetime',
            'start_geo' => 'array',
            'end_geo' => 'array',
        ];
    }

    /**
     * Get the work order that owns this session.
     */
    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    /**
     * Get the user that owns this session.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if session is active (not ended).
     */
    public function isActive(): bool
    {
        return is_null($this->end_at);
    }

    /**
     * Get formatted duration.
     */
    public function getFormattedDurationAttribute(): string
    {
        if (!$this->duration_seconds) {
            return '00:00';
        }

        $hours = floor($this->duration_seconds / 3600);
        $minutes = floor(($this->duration_seconds % 3600) / 60);
        
        return sprintf('%02d:%02d', $hours, $minutes);
    }

    /**
     * Calculate duration automatically.
     */
    public function calculateDuration(): void
    {
        if ($this->start_at && $this->end_at) {
            $this->duration_seconds = $this->end_at->diffInSeconds($this->start_at);
        }
    }
}
