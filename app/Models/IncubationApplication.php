<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class IncubationApplication extends Model
{
    use SoftDeletes;

    protected $table = 'incubation_applications';

    protected $fillable = [
        'application_number',
        'applied_date',
        'applied_time',
        'company_name',
        'registered_company_name',
        'business_description',
        'business_model',
        'offering',
        'revenue_model',
        'website',
        'company_type',
        'industry',
        'sector',
        'country',
        'district',
        'year_of_establishment',
        'company_stage',
        'company_size',
        'social_media',
        'applicant_name',
        'applicant_email',
        'applicant_title',
        'applicant_gender',
        'applicant_nationality',
        'applicant_contact_number',
        'applicant_about',
        'applicant_social_media',
        'received_financial_support',
        'participated_competitions',
        'willing_to_commit',
        'number_of_shareholders',
        'number_of_customers',
        'average_monthly_sales',
        'jobs_to_create_12_months',
        'number_women_shareholders',
        'number_youth_shareholders',
        'industry_other_elaboration',
        'call_id',
        'user_id',
        'status',
        'submitted_at',
    ];

    protected $casts = [
        'applied_date' => 'date',
        'applied_time' => 'datetime',
        'submitted_at' => 'datetime',
        'willing_to_commit' => 'boolean',
        'number_of_shareholders' => 'integer',
        'number_of_customers' => 'integer',
        'average_monthly_sales' => 'decimal:2',
        'jobs_to_create_12_months' => 'integer',
        'number_women_shareholders' => 'integer',
        'number_youth_shareholders' => 'integer',
        'social_media' => 'array',
        'applicant_social_media' => 'array',
    ];

    /**
     * Get the call this application belongs to
     */
    public function call(): BelongsTo
    {
        return $this->belongsTo(Call::class);
    }

    /**
     * Get evaluation scores for this application
     */
    public function evaluationScores(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(EvaluationScore::class, 'application_id');
    }

    /**
     * Get the current user's evaluation score for this application
     */
    public function myEvaluationScore()
    {
        return $this->evaluationScores()
            ->where('evaluator_id', Auth::id())
            ->first();
    }

    /**
     * Get the user who submitted this application
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    public function screening(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Screening::class, 'application_id');
    }

    /**
     * Boot method to generate application number automatically
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($application) {
            $application->application_number = static::generateApplicationNumber();
            $application->applied_date = now();
            $application->applied_time = now();
            $application->submitted_at = now();
        });
    }

    /**
     * Generate a unique application number
     */
    public static function generateApplicationNumber(): string
    {
        $prefix = 'INC';
        $year = date('Y');
        $month = date('m');
        
        $lastApplication = static::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastApplication) {
            $lastNumber = intval(substr($lastApplication->application_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return "{$prefix}-{$year}{$month}-{$newNumber}";
    }

    /**
     * Helper method to add social media platform
     */
    public function addSocialMedia($platform, $url)
    {
        $socialMedia = $this->social_media ?? [];
        $socialMedia[$platform] = $url;
        $this->social_media = $socialMedia;
        $this->save();
    }

    public function getSocialMedia($platform)
    {
        return $this->social_media[$platform] ?? null;
    }
}