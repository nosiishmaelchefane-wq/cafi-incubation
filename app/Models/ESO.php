<?php
// app/Models/ESO.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class ESO extends Model
{
    use SoftDeletes;

    protected $table = 'esos';

    protected $fillable = [
        // Organisation Information
        'organisation_name',
        'website',
        'official_email_address',
        'contact_telephone_number',
        'short_bio',
        'country',
        'area_of_operation',
        'address',
        'area_of_focus',
        
        // Representative Information
        'representative_email',
        'representative_name',
        'representative_surname',
        'representative_contact_number',
        
        // Files/Documents
        'organisation_logo',
        'trading_license',
        'tax_clearance_certificate',
        
        // Metadata for any additional data
        'metadata',
        
        // Approval Status
        'verified_at',
        'approved_by',
        'status', // pending, approved, rejected
        'rejection_reason',
    ];

    protected $casts = [
        'metadata' => 'array',
        'verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns this ESO profile.
     */
    public function user(): MorphOne
    {
        return $this->morphOne(User::class, 'userable');
    }

    /**
     * Get the admin who approved this ESO.
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Check if ESO is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved' && !is_null($this->verified_at);
    }

    /**
     * Check if ESO is pending approval.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Get full representative name.
     */
    public function getRepresentativeFullNameAttribute(): string
    {
        return $this->representative_name . ' ' . $this->representative_surname;
    }

    /**
     * Get organisation logo URL.
     */
    public function getOrganisationLogoUrlAttribute(): string
    {
        if ($this->organisation_logo) {
            return asset('storage/' . $this->organisation_logo);
        }
        
        return '';
    }

    /**
     * Scope a query to only include approved ESOs.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope a query to only include pending ESOs.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include ESOs by area of focus.
     */
    public function scopeByAreaOfFocus($query, $focus)
    {
        return $query->where('area_of_focus', 'like', '%' . $focus . '%');
    }
}