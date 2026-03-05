<?php
// app/Observers/UserObserver.php

namespace App\Observers;

use App\Models\User;
use Spatie\Permission\Models\Role;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        // Assign default role to new users (e.g., 'Entrepreneur')
        $defaultRole = Role::where('name', 'Entrepreneur')->first();
        if ($defaultRole) {
            $user->assignRole($defaultRole);
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        // Check if suspension status changed
        if ($user->isDirty('is_suspended')) {
            // You can dispatch events or send notifications here
        }
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        // Optional: Clean up related data
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "forceDeleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}