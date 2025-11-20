<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkOrderAssignment extends Model
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
        'role_in_ot',
    ];

    /**
     * Get the work order that owns this assignment.
     */
    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    /**
     * Get the user that owns this assignment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if assignment is for technician role.
     */
    public function isTechnicianRole(): bool
    {
        return $this->role_in_ot === 'technician';
    }

    /**
     * Check if assignment is for supervisor role.
     */
    public function isSupervisorRole(): bool
    {
        return $this->role_in_ot === 'supervisor';
    }
}
