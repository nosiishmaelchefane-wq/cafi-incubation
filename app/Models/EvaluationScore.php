<?php
// app/Models/EvaluationScore.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluationScore extends Model
{
    protected $table = 'evaluation_scores';

    protected $fillable = [
        'application_id',
        'evaluator_id',
        'call_id',
        'innovation_uniqueness',
        'innovation_development',
        'commercial_vision',
        'commercial_disruption',
        'commercial_market_size',
        'team_experience',
        'team_diversity',
        'team_size',
        'team_women_shareholders',
        'team_youth_shareholders',
        'operation_sustainability',
        'social_safeguards',
        'social_risk_mitigation',
        'total_score',
        'evaluator_comments',
        'evaluator_name',
        'evaluation_date',
        'evaluation_location',
        'status',
        'submitted_at',
        'submitted_by',
    ];

    protected $casts = [
        'team_women_shareholders' => 'boolean',
        'team_youth_shareholders' => 'boolean',
        'submitted_at' => 'datetime',
        'evaluation_date' => 'date',
    ];

    /**
     * Get the application being evaluated
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(IncubationApplication::class, 'application_id');
    }

    /**
     * Get the evaluator user
     */
    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    /**
     * Get the call
     */
    public function call(): BelongsTo
    {
        return $this->belongsTo(Call::class);
    }

    /**
     * Get the user who submitted the score
     */
    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    /**
     * Calculate total score from all sections
     */
    public function calculateTotalScore(): int
    {
        $total = 0;
        
        // Section 1: Innovation (max 15)
        $total += min($this->innovation_uniqueness, 10);
        $total += min($this->innovation_development, 5);
        
        // Section 2: Commercial (max 25)
        $total += min($this->commercial_vision, 10);
        $total += min($this->commercial_disruption, 10);
        $total += min($this->commercial_market_size, 5);
        
        // Section 3: Team (max 30)
        $total += min($this->team_experience, 6);
        $total += min($this->team_diversity, 6);
        $total += min($this->team_size, 10);
        $total += $this->team_women_shareholders ? 4 : 0;
        $total += $this->team_youth_shareholders ? 4 : 0;
        
        // Section 4: Operation (max 20)
        $total += min($this->operation_sustainability, 20);
        
        // Section 5: Social (max 10)
        $total += min($this->social_safeguards, 5);
        $total += min($this->social_risk_mitigation, 5);
        
        return min($total, 100);
    }

    /**
     * Scope for submitted scores
     */
    public function scopeSubmitted($query)
    {
        return $query->where('status', 'submitted');
    }

    /**
     * Scope for draft scores
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Check if score is submitted
     */
    public function isSubmitted(): bool
    {
        return $this->status === 'submitted';
    }
}