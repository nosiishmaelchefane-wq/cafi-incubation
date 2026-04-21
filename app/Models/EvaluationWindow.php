<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class EvaluationWindow extends Model
{
    protected $table = 'evaluation_windows';
    
    protected $fillable = [
        'call_id',
        'open_date',
        'close_date',
        'status',
        'locked_at',
        'locked_by',
        'notes',
    ];

    protected $casts = [
        'open_date' => 'date',
        'close_date' => 'date',
        'locked_at' => 'datetime',
    ];

    /**
     * Get the call that owns this evaluation window
     */
    public function call(): BelongsTo
    {
        return $this->belongsTo(Call::class);
    }

    /**
     * Get the user who locked this evaluation window
     */
    public function lockedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    /**
     * Check if the evaluation window is currently active
     */
    public function isActive(): bool
    {
        $now = Carbon::now();
        return $this->status === 'active' && 
               $this->open_date <= $now && 
               $this->close_date >= $now;
    }

    /**
     * Check if the evaluation window is expired
     */
    public function isExpired(): bool
    {
        return $this->close_date < Carbon::now();
    }

    /**
     * Check if the evaluation window is upcoming
     */
    public function isUpcoming(): bool
    {
        return $this->open_date > Carbon::now();
    }

    /**
     * Get days until evaluation window opens
     */
    public function getDaysUntilOpenAttribute(): ?int
    {
        if ($this->open_date > Carbon::now()) {
            return (int) Carbon::now()->diffInDays($this->open_date, false);
        }
        return null;
    }

    /**
     * Get days remaining until evaluation window closes
     */
    public function getDaysRemainingAttribute(): ?int
    {
        if ($this->close_date > Carbon::now()) {
            return (int) Carbon::now()->diffInDays($this->close_date, false);
        }
        return null;
    }

    /**
     * Get duration in days
     */
    public function getDurationInDaysAttribute(): int
    {
        return (int) $this->open_date->diffInDays($this->close_date);
    }

    /**
     * Auto-update status based on dates
     */
    public function updateStatus(): void
    {
        $now = Carbon::now();
        
        if ($this->close_date < $now) {
            $this->status = 'expired';
        } elseif ($this->open_date <= $now && $this->close_date >= $now) {
            $this->status = 'active';
        } else {
            $this->status = 'draft';
        }
        
        $this->saveQuietly();
    }

    /**
     * Lock the evaluation window
     */
    public function lock(int $userId): void
    {
        $this->locked_at = now();
        $this->locked_by = $userId;
        $this->save();
    }

    /**
     * Check if evaluation window is locked
     */
    public function isLocked(): bool
    {
        return !is_null($this->locked_at);
    }

    /**
     * Scope for active evaluation windows
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                     ->where('open_date', '<=', Carbon::now())
                     ->where('close_date', '>=', Carbon::now());
    }

    /**
     * Scope for upcoming evaluation windows
     */
    public function scopeUpcoming($query)
    {
        return $query->where('open_date', '>', Carbon::now())
                     ->where('status', 'draft');
    }
}