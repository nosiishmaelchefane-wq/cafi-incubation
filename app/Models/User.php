<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $fillable = [
        'email',
        'password',
        'profile_photo',
        'userable_type',
        'userable_id',
        'is_active',
        'last_login_at',
        'last_login_ip',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    /**
     * Get the parent userable model (entrepreneur or eso).
     */
    public function userable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get profile photo URL.
     */
    public function getProfilePhotoUrlAttribute(): string
    {
        if ($this->profile_photo) {
            return asset('storage/' . $this->profile_photo);
        }
        
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->email) . '&color=7F9CF5&background=EBF4FF';
    }

    /**
     * Check if user is an entrepreneur.
     */
    public function isEntrepreneur(): bool
    {
        return $this->userable_type === Entrepreneur::class;
    }

    /**
     * Check if user is an ESO.
     */
    public function isESO(): bool
    {
        return $this->userable_type === ESO::class;
    }
}