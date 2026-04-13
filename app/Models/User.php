<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Role;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $fillable = [
        // Authentication fields
        'email',
        'password',
        'email_verified_at',
        
        // Profile fields
        'username',
        'phone',
        'profile_photo',
        'bio',
        
        // Polymorphic relationship fields
        'userable_type',
        'userable_id',
        
        // Account status fields
        'is_active',
        'is_suspended',
        'suspended_until',
        'suspension_reason',
        
        // Login tracking
        'last_login_at',
        'last_login_ip',
        
        // User preferences and metadata
        'metadata',
        'timezone',
        'language',
        'verification_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'verification_token', // Also hide verification token
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'is_suspended' => 'boolean',
        'suspended_until' => 'datetime',
        'last_login_at' => 'datetime',
        'metadata' => 'array', // Cast metadata to array
    ];

    /**
     * The attributes that should have default values.
     */
    protected $attributes = [
        'is_active' => true,
        'is_suspended' => false,
        'language' => 'en',
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
        
        return '';
    }

    /**
     * Get user's full name or display name.
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->username) {
            return $this->username;
        }
        
        if ($this->userable) {
            if ($this->isEntrepreneur()) {
                return $this->userable->first_name . ' ' . $this->userable->surname;
            }
            if ($this->isESO()) {
                return $this->userable->organisation_name;
            }
        }
        
        return explode('@', $this->email)[0];
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

    /**
     * Check if user is an admin or super admin.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole(['admin', 'super-admin']);
    }

    /**
     * Check if user is suspended.
     */
    public function isSuspended(): bool
    {
        if (!$this->is_suspended) {
            return false;
        }
        
        if ($this->suspended_until && $this->suspended_until->isPast()) {
            // Auto-unsuspend if suspension period has passed
            $this->update(['is_suspended' => false, 'suspended_until' => null]);
            return false;
        }
        
        return true;
    }

    /**
     * Suspend a user.
     */
    public function suspend(string $reason = null, $until = null): bool
    {
        return $this->update([
            'is_suspended' => true,
            'suspension_reason' => $reason,
            'suspended_until' => $until,
        ]);
    }

    /**
     * Unsuspend a user.
     */
    public function unsuspend(): bool
    {
        return $this->update([
            'is_suspended' => false,
            'suspension_reason' => null,
            'suspended_until' => null,
        ]);
    }

    /**
     * Update last login information.
     */
    public function updateLastLogin(string $ip = null): void
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $ip,
        ]);
    }

    /**
     * Scope a query to only include active users.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                     ->where('is_suspended', false);
    }

    /**
     * Scope a query to only include users with a specific role.
     */
    public function scopeWithRole($query, $role)
    {
        if (is_array($role)) {
            return $query->whereHas('roles', function ($q) use ($role) {
                $q->whereIn('name', $role);
            });
        }
        
        return $query->whereHas('roles', function ($q) use ($role) {
            $q->where('name', $role);
        });
    }

    public static function getCountByRole()
    {
        return self::with('roles')
            ->get()
            ->flatMap(function ($user) {
                return $user->roles->pluck('name');
            })
            ->countBy();
    }

    /**
     * Scope a query to only include suspended users.
     */
    public function scopeSuspended($query)
    {
        return $query->where('is_suspended', true);
    }

    /**
     * Scope a query to only include entrepreneurs.
     */
    public function scopeEntrepreneurs($query)
    {
        return $query->where('userable_type', Entrepreneur::class);
    }

    /**
     * Scope a query to only include ESOs.
     */
    public function scopeEsos($query)
    {
        return $query->where('userable_type', ESO::class);
    }

    
}