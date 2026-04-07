<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // First, check if user exists
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        // Check if account is active
        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'email' => 'Your account is pending approval. You will receive an email once your account is activated. Please contact support if you have questions.',
            ]);
        }

        // Check if account is suspended
        if ($user->is_suspended) {
            // Check if it's a temporary suspension
            if ($user->suspended_until && $user->suspended_until->isFuture()) {
                $suspendedUntil = $user->suspended_until->format('F j, Y');
                throw ValidationException::withMessages([
                    'email' => "Your account is suspended until {$suspendedUntil}. Reason: {$user->suspension_reason}",
                ]);
            } 
            // Auto-unsuspend if suspension period has passed
            elseif ($user->suspended_until && $user->suspended_until->isPast()) {
                $user->update([
                    'is_suspended' => false, 
                    'suspended_until' => null, 
                    'suspension_reason' => null
                ]);
            } 
            // Indefinite suspension
            else {
                throw ValidationException::withMessages([
                    'email' => 'Your account has been suspended. Please contact support for more information.',
                ]);
            }
        }

        // Attempt to authenticate
        $request->authenticate();

        // Regenerate session to prevent session fixation
        $request->session()->regenerate();

        // Update last login information
        Auth::user()->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        // Redirect based on user's permissions
        return $this->redirectBasedOnPermissions(Auth::user());
    }

    /**
     * Redirect users based on their permissions
     */
    protected function redirectBasedOnPermissions($user): RedirectResponse
    {
        // Define permission-based redirects
        $redirectRoutes = [
            'view User Management' => route('users.index', absolute: false),
            'view Calls for Applications' => route('calls.index', absolute: false),
            'view Screening & Eligibility' => route('screening.index', absolute: false),
            'view Evaluation & Scoring' => route('evaluation.index', absolute: false),
        ];

        // Check each permission and redirect to the appropriate page
        foreach ($redirectRoutes as $permission => $route) {
            if ($user->can($permission)) {
                return redirect()->intended($route);
            }
        }

        // Default redirect to dashboard
        return redirect()->intended(route('dashboard.index', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}