<?php
// app/Models/Entrepreneur.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Entrepreneur extends Model
{
    protected $table = 'entrepreneurs';

    protected $fillable = [
        'first_name',
        'surname',
        'gender',
        'date_of_birth',
        'country',
        'area_of_operation',
        'industry_or_interest',
        'years_of_operation',
        'short_bio',
        'organization_name',
        'tax_clearance_path',
        'traders_license_path',
        'metadata',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'years_of_operation' => 'integer',
        'metadata' => 'array',
    ];

    /**
     * Get the user that owns this entrepreneur profile.
     */
    public function user(): MorphOne
    {
        return $this->morphOne(User::class, 'userable');
    }

    /**
     * Get full name attribute.
     */
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->surname;
    }
}

