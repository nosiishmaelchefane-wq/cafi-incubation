<?php
// app/Models/Shortlist.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shortlist extends Model
{
    protected $table = 'shortlists';

    protected $fillable = [
        'call_id',
        'cohort_id',
        'applications_count',
        'status',
        'confirmed_by',
        'confirmed_at',
    ];

    protected $casts = [
        'confirmed_at' => 'datetime',
        'applications_count' => 'integer',
    ];

    /**
     * Get the call this shortlist belongs to
     */
    public function call(): BelongsTo
    {
        return $this->belongsTo(Call::class);
    }

    /**
     * Get the cohort this shortlist belongs to
     */
    public function cohort(): BelongsTo
    {
        return $this->belongsTo(Cohort::class);
    }

    /**
     * Get the user who confirmed the shortlist
     */
    public function confirmedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }
}