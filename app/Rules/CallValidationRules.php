<?php

namespace App\Rules;

class CallValidationRules
{
    public static function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'cohort' => 'required|integer',
            'target_applications' => 'nullable|integer|min:1|max:1000000',
            'description' => 'required|string|min:10|max:1000',
            'details' => 'nullable|string|max:5000',
            'eligibility' => 'required|string|min:10|max:2000',
            'sectors' => 'nullable|array',
            'geography' => 'nullable|string|max:255',
            'publish_date' => 'nullable|date',
            'open_date' => 'required|date',
            'close_date' => 'required|date|after:open_date',
            'duration_months' => 'required|integer|in:6,9,12',
            'allow_late_submissions' => 'boolean',
        ];
    }

    public static function messages()
    {
        return [
            'title.required' => 'Call title is required.',
            'cohort.required' => 'Please select a cohort.',
            'description.required' => 'Please provide a short description.',
            'description.min' => 'Description must be at least 10 characters.',
            'eligibility.required' => 'Please specify the eligibility criteria.',
            'open_date.required' => 'Please select the opening date.',
            'close_date.required' => 'Please select the closing date.',
            'close_date.after' => 'Closing date must be after opening date.',
        ];
    }
}