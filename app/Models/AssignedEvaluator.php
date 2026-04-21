<?php
// app/Models/AssignedEvaluator.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssignedEvaluator extends Model
{
    protected $table = 'assigned_evaluators';

    protected $fillable = [
        'call_id',
        'user_id',
        'assigned_by',
        'evaluation_deadline',
        'status',
        'assigned_applications_count',
        'scored_applications_count',
        'assigned_at',
    ];

    protected $casts = [
        'evaluation_deadline' => 'date',
        'assigned_at' => 'datetime',
        'assigned_applications_count' => 'integer',
        'scored_applications_count' => 'integer',
    ];

    /**
     * Get the call this assignment belongs to
     */
    public function call(): BelongsTo
    {
        return $this->belongsTo(Call::class);
    }

    /**
     * Get the evaluator user
     */
    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the user who assigned the evaluator
     */
    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Check if evaluation is overdue
     */
    public function isOverdue(): bool
    {
        return $this->evaluation_deadline && 
               $this->evaluation_deadline->isPast() && 
               $this->status !== 'completed';
    }
}