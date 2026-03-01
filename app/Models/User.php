<?php
// app/Models/User.php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes; // Optional: if you want soft deletes

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, 
        Notifiable, 
        HasRoles,
        SoftDeletes; // Optional: add soft deletes if needed

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'profile_photo',
        'bio',
        'password',
        'is_active',
        'is_suspended',
        'suspended_until',
        'suspension_reason',
        'last_login_at',
        'last_login_ip',
        'metadata',
        'timezone',
        'language',
        'verification_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'verification_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'is_suspended' => 'boolean',
        'suspended_until' => 'datetime',
        'last_login_at' => 'datetime',
        'metadata' => 'array',
        'deleted_at' => 'datetime', // If using soft deletes
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
        'account_status',
        'is_online',
    ];

    /**
     * Get the user's profile photo URL.
     */
    public function getProfilePhotoUrlAttribute(): string
    {
        if ($this->profile_photo) {
            return asset('storage/' . $this->profile_photo);
        }
        
        // Return default avatar based on name initials
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }

    /**
     * Get the user's account status.
     */
    public function getAccountStatusAttribute(): string
    {
        if ($this->is_suspended) {
            if ($this->suspended_until && $this->suspended_until->isFuture()) {
                return 'suspended_until_' . $this->suspended_until->format('Y-m-d');
            } elseif ($this->suspended_until && $this->suspended_until->isPast()) {
                // Auto-unsuspend if suspension period has passed
                $this->update(['is_suspended' => false, 'suspended_until' => null]);
                return 'active';
            }
            return 'suspended';
        }
        
        return $this->is_active ? 'active' : 'inactive';
    }

    /**
     * Check if user is online (last activity within 5 minutes).
     */
    public function getIsOnlineAttribute(): bool
    {
        return $this->last_login_at && $this->last_login_at->gt(now()->subMinutes(5));
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
     * Scope a query to only include suspended users.
     */
    public function scopeSuspended($query)
    {
        return $query->where('is_suspended', true);
    }

    /**
     * Scope a query to only include users with a specific role.
     */
    public function scopeWithRole($query, $role)
    {
        return $query->whereHas('roles', function ($q) use ($role) {
            $q->where('name', $role);
        });
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
     * Check if user has specific permission through role.
     */
    public function hasPermissionToModule(string $module, string $action = 'view'): bool
    {
        $permission = $action . ' ' . $module;
        return $this->can($permission);
    }

    /**
     * Get all permissions grouped by module for this user.
     */
    public function getGroupedPermissions(): array
    {
        $permissions = $this->getAllPermissions();
        $grouped = [];
        
        foreach ($permissions as $permission) {
            $parts = explode(' ', $permission->name);
            $action = $parts[0];
            $module = implode(' ', array_slice($parts, 1));
            
            if (!isset($grouped[$module])) {
                $grouped[$module] = [];
            }
            
            $grouped[$module][] = $action;
        }
        
        return $grouped;
    }

    /**
     * Get users count by role.
     */
    public static function getCountByRole(): array
    {
        $roles = \Spatie\Permission\Models\Role::withCount('users')->get();
        
        return $roles->pluck('users_count', 'name')->toArray();
    }

    /**
     * Route notifications for the mail channel.
     */
    public function routeNotificationForMail(): string
    {
        return $this->email;
    }

    /**
     * Route notifications for the SMS channel (if using).
     */
    public function routeNotificationForSms(): ?string
    {
        return $this->phone;
    }
}