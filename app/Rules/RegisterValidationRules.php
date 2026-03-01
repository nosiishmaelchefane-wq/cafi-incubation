<?php

namespace App\Rules;

use Illuminate\Validation\Rules\Password;

class RegisterValidationRules
{
    /**
     * Get the validation rules for registration
     */
    public static function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],  // Changed from first_name/last_name
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::min(10)],
            'terms' => ['required', 'accepted'],
        ];
    }

    /**
     * Get custom validation messages
     */
    public static function messages(): array
    {
        return [
            'name.required' => 'Please enter your full name.',
            'email.required' => 'An email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already registered.',
            'password.required' => 'Please create a password.',
            'password.min' => 'Password must be at least 10 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
            'terms.accepted' => 'You must accept the Terms & Privacy Policy.',
        ];
    }
}