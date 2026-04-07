<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;


class Call extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'cohort',
        'target_applications',
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
        $this->status = 'closed';
        $this->save();
    }
}