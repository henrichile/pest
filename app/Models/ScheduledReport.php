<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduledReport extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'format',
        'frequency',
        'filters',
        'recipients',
        'is_active',
        'next_run_at',
        'last_run_at',
        'created_by',
    ];

    protected $casts = [
        'filters' => 'array',
        'recipients' => 'array',
        'is_active' => 'boolean',
        'next_run_at' => 'datetime',
        'last_run_at' => 'datetime',
    ];

    /**
     * Get the user who created this scheduled report.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Calculate next run date based on frequency.
     */
    public function calculateNextRun(): void
    {
        if (!$this->is_active) {
            $this->next_run_at = null;
            return;
        }

        $now = now();
        
        switch ($this->frequency) {
            case 'daily':
                $this->next_run_at = $now->addDay()->startOfDay();
                break;
            case 'weekly':
                $this->next_run_at = $now->addWeek()->startOfWeek();
                break;
            case 'monthly':
                $this->next_run_at = $now->addMonth()->startOfMonth();
                break;
            case 'quarterly':
                $this->next_run_at = $now->addMonths(3)->startOfQuarter();
                break;
            case 'yearly':
                $this->next_run_at = $now->addYear()->startOfYear();
                break;
            default:
                $this->next_run_at = $now->addMonth()->startOfMonth();
        }
    }
}

