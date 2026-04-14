<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use Carbon\Carbon;

class Call extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'cohort',
        'description',
        'details',
        'eligibility',
        'sectors',
        'geography',
        'publish_date',
        'open_date',
        'close_date',
        'duration_months',
        'allow_late_submissions',
        'status',
        'published_by',
        'published_at',
        'applications_count',
    ];

    protected $casts = [
        'sectors' => 'array',
        'publish_date' => 'date',
        'open_date' => 'date',
        'close_date' => 'date',
        'published_at' => 'datetime',
        'allow_late_submissions' => 'boolean',
    ];

    /**
     * Boot the model and add event listeners
     */
    protected static function booted()
    {
        static::retrieved(function ($call) {
            // Auto-close the call if it's open and the close date has passed
            if ($call->status === 'open' && $call->close_date && $call->close_date <= Carbon::now()) {
                $call->close();
            }
        });
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'published_by');
    }

    public function publish(int $userId): void
    {
        $this->status = 'published';
        $this->published_by = $userId;
        $this->published_at = now();
        $this->save();
    }

    public function open(): void
    {
        $this->status = 'open';
        $this->save();
    }

    public function close(): void
    {
        // Only update if status is not already closed
        if ($this->status !== 'closed') {
            $this->status = 'closed';
            $this->save();
        }
    }

    /**
     * Applications linked to this call
     */
    public function applications(): HasMany
    {
        return $this->hasMany(\App\Models\IncubationApplication::class, 'call_id');
    }

    /**
     * Check if the call is currently open for applications
     */
    public function isOpen(): bool
    {
        return $this->status === 'open' && 
               (!$this->close_date || $this->close_date > Carbon::now());
    }

    /**
     * Get days remaining until close date
     */
    public function getDaysRemainingAttribute(): ?int
    {
        if ($this->status === 'open' && $this->close_date && $this->close_date > Carbon::now()) {
            return (int) Carbon::now()->diffInDays($this->close_date, false);
        }
        return null;
    }

    /**
     * Scope for open calls
     */
    public function scopeOpen($query)
    {
        return $query->where('status', 'open')
                     ->where('close_date', '>', Carbon::now());
    }

    /**
     * Scope for published calls (visible to applicants)
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope for active calls (published or open)
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['published', 'open']);
    }
}