<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use Carbon\Carbon;

class Call extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'cohort',
        'description',
        'details',
        'eligibility',
        'sectors',
        'geography',
        'publish_date',
        'open_date',
        'close_date',
        'duration_months',
        'allow_late_submissions',
        'status',
        'published_by',
        'published_at',
        'applications_count',
    ];

    protected $casts = [
        'sectors' => 'array',
        'publish_date' => 'date',
        'open_date' => 'date',
        'close_date' => 'date',
        'published_at' => 'datetime',
        'allow_late_submissions' => 'boolean',
    ];

    /**
     * Boot the model and add event listeners
     */
    protected static function booted()
    {
        static::retrieved(function ($call) {
            // Auto-open the call if it's published and open date has been reached
            if ($call->status === 'published' && $call->open_date && $call->open_date <= Carbon::now()) {
                $call->open();
            }
            
            // Auto-close the call if it's open and the close date has passed
            if ($call->status === 'open' && $call->close_date && $call->close_date <= Carbon::now()) {
                $call->close();
            }
        });
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'published_by');
    }

    public function publish(int $userId): void
    {
        $this->status = 'published';
        $this->published_by = $userId;
        $this->published_at = now();
        $this->save();
    }

    public function open(): void
    {
        if ($this->status !== 'open') {
            $this->status = 'open';
            $this->save();
        }
    }

    public function close(): void
    {
        // Only update if status is not already closed
        if ($this->status !== 'closed') {
            $this->status = 'closed';
            $this->save();
        }
    }

    /**
     * Applications linked to this call
     */
    public function applications(): HasMany
    {
        return $this->hasMany(\App\Models\IncubationApplication::class, 'call_id');
    }

    /**
     * Check if the call is currently open for applications
     */
    public function isOpen(): bool
    {
        return $this->status === 'open' && 
               (!$this->close_date || $this->close_date > Carbon::now());
    }

    /**
     * Check if the call is published but not yet open
     */
    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    /**
     * Get days remaining until open date
     */
    public function getDaysUntilOpenAttribute(): ?int
    {
        if ($this->status === 'published' && $this->open_date && $this->open_date > Carbon::now()) {
            return (int) Carbon::now()->diffInDays($this->open_date, false);
        }
        return null;
    }

    /**
     * Get days remaining until close date
     */
    public function getDaysRemainingAttribute(): ?int
    {
        if ($this->status === 'open' && $this->close_date && $this->close_date > Carbon::now()) {
            return (int) Carbon::now()->diffInDays($this->close_date, false);
        }
        return null;
    }

    /**
     * Scope for open calls
     */
    public function scopeOpen($query)
    {
        return $query->where('status', 'open')
                     ->where('close_date', '>', Carbon::now());
    }

    /**
     * Scope for published calls (visible to applicants)
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope for active calls (published or open)
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['published', 'open']);
    }

        
    /**
     * Get assigned evaluators for this call
     */
    public function assignedEvaluators(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AssignedEvaluator::class);
    }

    /**
     * Get evaluators assigned to this call
     */
    public function evaluators()
    {
        return $this->belongsToMany(User::class, 'assigned_evaluators', 'call_id', 'user_id')
                    ->withPivot('status', 'evaluation_deadline', 'assigned_applications_count', 'scored_applications_count', 'assigned_at')
                    ->withTimestamps();
    }

    /**
     * Get eligible applications for evaluation (submitted and in_review applications)
     */
    public function getEligibleApplicationsForEvaluation()
    {
        return $this->applications()
            ->whereIn('status', ['submitted', 'in_review', 'eligible'])
            ->get();
    }

    /**
     * Update assigned applications count for an evaluator
     */
    public function updateAssignedApplicationsCount($evaluatorId)
    {
        $assignment = $this->assignedEvaluators()->where('user_id', $evaluatorId)->first();
        if ($assignment) {
            $eligibleCount = $this->getEligibleApplicationsForEvaluation()->count();
            $assignment->update(['assigned_applications_count' => $eligibleCount]);
        }
    }


    /**
     * Get all evaluation windows for this call
     */
    public function evaluationWindows(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(EvaluationWindow::class);
    }

    /**
     * Get the current active evaluation window
     */
    public function currentEvaluationWindow(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(EvaluationWindow::class, 'current_evaluation_window_id');
    }

    /**
     * Get the latest evaluation window
     */
    public function latestEvaluationWindow(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(EvaluationWindow::class)->latest();
    }
}