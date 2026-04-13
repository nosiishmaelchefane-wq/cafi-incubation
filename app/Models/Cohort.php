<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cohort extends Model
{
    use SoftDeletes;

    protected $table = 'cohorts';

    protected $fillable = [
        'cohort_number',
        'name',
        'year',
        'duration_months',
        'start_date',
        'end_date',
        'target_enterprises',
        'status',
        'description',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'target_enterprises' => 'integer',
    ];

    /**
     * Get the calls associated with this cohort
     */
    public function calls()
    {
        return $this->hasMany(Call::class, 'cohort', 'cohort_number');
    }

    /**
     * Get total applications across all calls in this cohort
     */
    public function getTotalApplicationsAttribute()
    {
        return $this->calls()->sum('applications_count');
    }

    /**
     * Get active calls in this cohort
     */
    public function getActiveCallsAttribute()
    {
        return $this->calls()->where('status', 'open')->count();
    }

    /**
     * Get the creator of the cohort
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}