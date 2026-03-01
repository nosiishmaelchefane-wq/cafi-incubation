<?php

use App\Rules\RegisterValidationRules;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

new class extends Component
{
    public string $name = '';  // Changed from first_name/last_name to name
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public bool $terms = false;
    
    public function register()
    {
        // Validate using our rules class
        $validated = $this->validate(
            RegisterValidationRules::rules(),
            RegisterValidationRules::messages()
        );
        
        try {
            // Create the user - using 'name' field only
            $user = User::create([
                'name' => $this->name,  // Using name field
                'email' => $this->email,
                'password' => Hash::make($this->password),
            ]);
            
            // Log the user in
            Auth::login($user);
            
            // Redirect to dashboard or intended page
            return redirect()->intended('/dashboard');
            
        } catch (\Exception $e) {
            $this->addError('registration', 'Registration failed. Please try again.');
        }
    }

    // Optional: Add real-time validation
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName, RegisterValidationRules::rules());
    }
};
?>

<div>
    <div class="form-view {{ $errors->any() ? '' : 'hidden' }}" id="register-view">
        <div class="form-header">
            <div class="form-eyebrow">CAFI Enterprise Portal</div>
            <h2 class="form-title">Register your business</h2>
            <p class="form-sub">Join Lesotho's enterprise ecosystem. Connect with mentors, secure investment, and access trade opportunities through the Ministry of Trade.</p>
        </div>

        {{-- Show general error message --}}
        @if ($errors->has('registration'))
            <div class="alert alert-error" style="color: red; margin-bottom: 1rem;">
                {{ $errors->first('registration') }}
            </div>
        @endif

        <form wire:submit.prevent="register">
            @csrf
            {{-- Single name field instead of first/last name --}}
            <div class="field">
                <label for="reg_name">Full Name</label>
                <input 
                    wire:model="name"
                    id="reg_name" 
                    type="text" 
                    placeholder="Jane Doe"
                    value="{{ old('name') }}"
                    autocomplete="name">
                @error('name')
                    <span style="color: red; font-size: 0.8rem;">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="field">
                <label for="reg_email">Email Address</label>
                <input 
                    wire:model="email"
                    id="reg_email" 
                    type="email" 
                    placeholder="you@example.com"
                    value="{{ old('email') }}"
                    autocomplete="email">
                @error('email')
                    <span style="color: red; font-size: 0.8rem;">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="field-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="field">
                    <label for="reg_password">Password</label>
                    <input 
                        wire:model="password"
                        id="reg_password" 
                        type="password" 
                        placeholder="Min. 10 characters"
                        autocomplete="new-password">
                    @error('password')
                        <span style="color: red; font-size: 0.8rem;">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="field">
                    <label for="reg_password_confirm">Confirm Password</label>
                    <input 
                        wire:model="password_confirmation"
                        id="reg_password_confirm" 
                        type="password" 
                        placeholder="Repeat password"
                        autocomplete="new-password">
                </div>
            </div>
            
            <div style="margin-bottom:1.2rem;">
                <label class="check-wrap" style="font-size:.78rem;color:var(--smoke);cursor:pointer;">
                    <input 
                        wire:model="terms"
                        type="checkbox" 
                        style="width:15px;height:15px;accent-color:var(--green);"
                        >
                    I agree to the <a href="#" style="color:var(--navy);text-underline-offset:2px;">Terms &amp; Privacy Policy</a>
                </label>
                @error('terms')
                    <span style="color: red; font-size: 0.8rem; display: block;">{{ $message }}</span>
                @enderror
            </div>
            
            <button type="submit" class="btn-green" wire:loading.attr="disabled" wire:target="register">
                <span wire:loading.remove wire:target="register">Create Account</span>
                <span wire:loading wire:target="register">Creating Account...</span>
                <svg width="15" height="15" viewBox="0 0 15 15" fill="none">
                    <path d="M2.5 7.5H12.5M9 4L12.5 7.5L9 11" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        </form>

        <div class="switch-link">
            <p>Already have an account? <button type="button" onclick="showLogin()">Sign in</button></p>
        </div>

        <div class="form-footer" style="margin-top:4px;">
            <p>
                Operated under the CAFI Project, Ministry of Trade,<br>
                Industry, Business Development &amp; Tourism · Lesotho.<br>
                <a href="#">Privacy Policy</a> &nbsp;·&nbsp; <a href="#">Contact Support</a>
            </p>
        </div>
    </div>
</div>