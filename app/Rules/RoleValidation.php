<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Spatie\Permission\Models\Role;

class RoleValidation implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (strlen($value) < 3) {
            $fail('The role name must be at least 3 characters.');
        }

        if (Role::where('name', $value)->exists()) {
            $fail('This role name already exists.');
        }
    }
}