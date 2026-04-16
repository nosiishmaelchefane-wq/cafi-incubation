<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Screening extends Model
{
    use SoftDeletes;

    protected $table = 'screenings';

    protected $fillable = [
        'application_id',
        'call_id',
        'user_id',
        'status', 
        'screening_notes',
        'rejection_reason',
        'rejection_category',
        'rejection_details',
        'eligibility_checklist',
        'screened_at',
        'screened_by',
    ];

    protected $casts = [
        'eligibility_checklist' => 'array',
        'screened_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the application that was screened
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(IncubationApplication::class, 'application_id');
    }

    /**
     * Get the call associated with this screening
     */
    public function call(): BelongsTo
    {
        return $this->belongsTo(Call::class, 'call_id');
    }

    /**
     * Get the user who performed the screening
     */
    public function screener(): BelongsTo
    {
        return $this->belongsTo(User::class, 'screened_by');
    }
    
    public function screenedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'screened_by');
    }

    /**
     * Get the user who performed the screening (alias)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope for pending screenings
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for screenings in review
     */
    public function scopeInReview($query)
    {
        return $query->where('status', 'in_review');
    }

    /**
     * Scope for eligible applications
     */
    public function scopeEligible($query)
    {
        return $query->where('status', 'eligible');
    }

    /**
     * Scope for rejected applications
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Check if screening is complete (eligible or rejected)
     */
    public function isComplete(): bool
    {
        return in_array($this->status, ['eligible', 'rejected']);
    }

    /**
     * Get the eligibility checklist summary
     */
    public function getChecklistSummaryAttribute(): array
    {
        if (!$this->eligibility_checklist) {
            return [
                'total' => 0,
                'passed' => 0,
                'failed' => 0,
                'pending' => 0,
                'percentage' => 0,
            ];
        }

        $total = count($this->eligibility_checklist);
        $passed = collect($this->eligibility_checklist)->where('passed', true)->count();
        $failed = collect($this->eligibility_checklist)->where('passed', false)->count();
        $pending = $total - ($passed + $failed);

        return [
            'total' => $total,
            'passed' => $passed,
            'failed' => $failed,
            'pending' => $pending,
            'percentage' => $total > 0 ? round(($passed / $total) * 100) : 0,
        ];
    }

    /**
     * Get formatted rejection reason for display
     */
    public function getFormattedRejectionReasonAttribute(): string
    {
        if (!$this->rejection_reason) {
            return 'No rejection reason provided';
        }

        $categories = [
            'incomplete_docs' => 'Incomplete / Missing Documents',
            'not_registered' => 'Business Not Formally Registered',
            'outside_sector' => 'Outside Target Sectors',
            'outside_geography' => 'Outside Geographic Focus',
            'revenue_too_high' => 'Revenue Exceeds Programme Threshold',
            'duplicate' => 'Duplicate Application',
            'other' => 'Other',
        ];

        $category = $categories[$this->rejection_category] ?? $this->rejection_category;
        
        if ($this->rejection_details) {
            return $category . ': ' . $this->rejection_details;
        }
        
        return $category;
    }
}