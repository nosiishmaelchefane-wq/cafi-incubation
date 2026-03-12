<?php
// app/Models/ESO.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class ESO extends Model
{
    protected $table = 'esos';

    protected $fillable = [
        'organisation_name',
        'website',
        'official_email_address',
        'contact_telephone_number',
        'short_bio',
        'country',
        'area_of_operation',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Get the user that owns this ESO profile.
     */
    public function user(): MorphOne
    {
        return $this->morphOne(User::class, 'userable');
    }
}