<?php

use App\Models\User;
use App\Models\Entrepreneur;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

new class extends Component {
    use WithFileUploads;

    // Account Information
    #[Rule('required|email|unique:users,email')]
    public string $email = '';
    
    #[Rule('required|string|max:20')]
    public string $phone = '';
    
    #[Rule('required|min:10|confirmed')]
    public string $password = '';
    
    public string $password_confirmation = '';
    
    #[Rule('accepted')]
    public bool $terms = false;
    
    // Entrepreneur specific fields
    #[Rule('required|string|max:255')]
    public string $first_name = '';
    
    #[Rule('required|string|max:255')]
    public string $surname = '';
    
    #[Rule('required|in:male,female,other')]
    public string $gender = '';
    
    #[Rule('required|date|before:18 years ago')]
    public string $date_of_birth = '';
    
    #[Rule('required|string')]
    public string $country = 'Lesotho';
    
    #[Rule('required|string|max:255')]
    public string $area_of_operation = '';
    
    #[Rule('required|string')]
    public string $industry_or_interest = '';
    
    #[Rule('required|integer|min:0|max:100')]
    public ?int $years_of_operation = null;
    
    #[Rule('nullable|string|max:1000')]
    public string $short_bio = '';
    
    #[Rule('required|string|max:255')]
    public string $organization_name = '';
    
    // File uploads
    #[Rule('nullable|image|max:2048')]
    public $profile_image = null;
    
    #[Rule('required|file|mimes:pdf,jpg,jpeg,png|max:5120')]
    public $tax_clearance = null;
    
    #[Rule('required|file|mimes:pdf,jpg,jpeg,png|max:5120')]
    public $traders_license = null;
    
    // Step management
    public int $currentStep = 1;
    public int $totalSteps = 4;
    
    // Custom error messages
    protected $messages = [
        // Profile image
        'profile_image.image' => 'The profile image must be a valid image file (JPG, PNG, GIF).',
        'profile_image.max' => 'The profile image size must not exceed 2MB.',
        
        // Tax clearance
        'tax_clearance.required' => 'The tax clearance certificate is required.',
        'tax_clearance.file' => 'The tax clearance certificate must be a valid file.',
        'tax_clearance.mimes' => 'The tax clearance certificate must be a PDF, JPG, or PNG file.',
        'tax_clearance.max' => 'The tax clearance certificate size must not exceed 5MB.',
        
        // Trader's license
        'traders_license.required' => 'The trader\'s license is required.',
        'traders_license.file' => 'The trader\'s license must be a valid file.',
        'traders_license.mimes' => 'The trader\'s license must be a PDF, JPG, or PNG file.',
        'traders_license.max' => 'The trader\'s license size must not exceed 5MB.',
        
        // Email unique
        'email.unique' => 'This email address is already registered. Please use a different email or login.',
        
        // Password
        'password.min' => 'Password must be at least 10 characters.',
        'password.confirmed' => 'Password confirmation does not match.',
        
        // Date of birth
        'date_of_birth.before' => 'You must be at least 18 years old.',
        
        // Required fields
        'first_name.required' => 'First name is required.',
        'surname.required' => 'Surname is required.',
        'gender.required' => 'Please select your gender.',
        'date_of_birth.required' => 'Date of birth is required.',
        'country.required' => 'Country is required.',
        'area_of_operation.required' => 'Area of operation is required.',
        'industry_or_interest.required' => 'Industry or area of interest is required.',
        'years_of_operation.required' => 'Years of operation is required.',
        'organization_name.required' => 'Organization/Company name is required.',
        'email.required' => 'Email address is required.',
        'phone.required' => 'Phone number is required.',
        'password.required' => 'Password is required.',
        'terms.accepted' => 'You must agree to the Terms & Privacy policy.',
    ];
    
    // Industry options
    public array $industries = [
        'Agriculture',
        'Technology',
        'Manufacturing',
        'Banking',
        'Education',
        'Healthcare',
        'Retail',
        'Construction',
        'Tourism',
        'Transportation',
        'Energy',
        'Mining',
        'Financial Services',
        'Real Estate',
        'Creative Arts',
        'Food & Beverage',
        'Consulting',
        'Other'
    ];
    
    // Country options
    public array $countries = [];

    public function mount()
    {
        // Initialize countries list with Lesotho first
        $this->countries = [
            'Lesotho',
            'South Africa',
            'Botswana',
            'Eswatini',
            'Namibia',
            'Zimbabwe',
            'Mozambique',
            'Zambia',
            'Malawi',
            'Angola',
            'Tanzania',
            'Kenya',
            'Uganda',
            'Rwanda',
            'Burundi',
            'Ethiopia',
            'Nigeria',
            'Ghana',
            'Egypt',
            'Morocco',
            'United States',
            'United Kingdom',
            'Canada',
            'Australia',
            'Germany',
            'France',
            'Italy',
            'Spain',
            'Portugal',
            'Netherlands',
            'Belgium',
            'Switzerland',
            'Sweden',
            'Norway',
            'Denmark',
            'Finland',
            'China',
            'India',
            'Japan',
            'South Korea',
            'Singapore',
            'Malaysia',
            'Indonesia',
            'Philippines',
            'Vietnam',
            'Thailand',
            'Brazil',
            'Argentina',
            'Mexico',
            'Colombia',
            'Chile',
            'Peru'
        ];
        sort($this->countries);
        // Move Lesotho to the top
        $this->countries = array_merge(['Lesotho'], array_diff($this->countries, ['Lesotho']));
    }

    public function register()
    {
        // Validate all steps before final submission
        try {
            $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Get all errors
            $errors = $e->validator->errors()->all();
            $firstError = $e->validator->errors()->first();
            $failedFields = array_keys($e->validator->failed());
            
            // Determine which step has the error
            $stepWithError = $this->getStepForField($failedFields[0] ?? '');
            
            // Navigate to the step with error
            if ($stepWithError) {
                $this->currentStep = $stepWithError;
            }
            
            // Show popup with specific error message
            $this->dispatch('show-validation-error', [
                'message' => 'Please fix the following error: ' . $firstError,
                'field' => $failedFields[0] ?? 'unknown',
                'step' => $stepWithError
            ]);
            
            return;
        }
        
        // Generate username from first name and surname
        $username = $this->first_name . ' ' . $this->surname;
        
        try {
            // Handle file uploads
            $profileImagePath = $this->profile_image 
                ? $this->profile_image->store('profile-images', 'public') 
                : null;
                
            $taxClearancePath = $this->tax_clearance
                ? $this->tax_clearance->store('documents/tax-clearance', 'public')
                : null;
                
            $tradersLicensePath = $this->traders_license
                ? $this->traders_license->store('documents/traders-license', 'public')
                : null;
            
            // Create entrepreneur profile first
            $entrepreneur = Entrepreneur::create([
                'first_name' => $this->first_name,
                'surname' => $this->surname,
                'gender' => $this->gender,
                'date_of_birth' => $this->date_of_birth,
                'country' => $this->country,
                'area_of_operation' => $this->area_of_operation,
                'industry_or_interest' => $this->industry_or_interest,
                'years_of_operation' => $this->years_of_operation,
                'short_bio' => $this->short_bio,
                'organization_name' => $this->organization_name,
                'tax_clearance_path' => $taxClearancePath,
                'traders_license_path' => $tradersLicensePath,
            ]);
            
            // Create user account linked to entrepreneur
            $user = User::create([
                'email' => $this->email,
                'phone' => $this->phone,
                'password' => Hash::make($this->password),
                'profile_photo' => $profileImagePath,
                'username' => $username,
                'userable_type' => Entrepreneur::class,
                'userable_id' => $entrepreneur->id,
                'is_active' => false,
            ]);
            
            $user->assignRole('Applicant');
            
            // Send welcome email
            \Mail::to($user->email)->send(new \App\Mail\WelcomeApplicant($user, $username));
            
            $this->dispatch('notify', type: 'success', message: 'Your application has been submitted successfully! A confirmation email has been sent to your email address. Your account will be reviewed by an administrator.');
            return $this->redirect('/', navigate: true);
            
        } catch (\Illuminate\Database\QueryException $e) {
            // Log the full error for debugging
            \Log::error('Registration database error: ' . $e->getMessage());
            
            // Check for duplicate entry error (1062)
            if ($e->errorInfo[1] == 1062) {
                $errorMessage = $e->getMessage();
                
                if (str_contains($errorMessage, 'users_email_unique')) {
                    $this->dispatch('notify', type: 'error', message: 'Registration failed. The email address "' . $this->email . '" is already registered. Please use a different email or login.');
                    $this->currentStep = 1; // Navigate to step 1
                } elseif (str_contains($errorMessage, 'users_phone_unique')) {
                    $this->dispatch('notify', type: 'error', message: 'Registration failed. The phone number "' . $this->phone . '" is already registered. Please use a different phone number.');
                    $this->currentStep = 1; // Navigate to step 1
                } else {
                    $this->dispatch('notify', type: 'error', message: 'Registration failed: ' . $e->getMessage());
                }
            } else {
                $this->dispatch('notify', type: 'error', message: 'Database error: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Registration failed: ' . $e->getMessage());
        }
    }
    
    public function getStepForField($field)
    {
        $step1Fields = ['email', 'phone', 'password', 'terms'];
        $step2Fields = ['first_name', 'surname', 'gender', 'date_of_birth', 'profile_image'];
        $step3Fields = ['country', 'area_of_operation', 'industry_or_interest', 'years_of_operation'];
        $step4Fields = ['short_bio', 'organization_name', 'tax_clearance', 'traders_license'];
        
        if (in_array($field, $step1Fields)) return 1;
        if (in_array($field, $step2Fields)) return 2;
        if (in_array($field, $step3Fields)) return 3;
        if (in_array($field, $step4Fields)) return 4;
        
        return 1; // Default to step 1
    }

    public function nextStep()
    {
        // Get rules for current step
        $rules = $this->getStepValidationRules($this->currentStep);
        
        // Validate the current step before proceeding
        if (!empty($rules)) {
            try {
                $this->validate($rules);
                
                if ($this->currentStep < $this->totalSteps) {
                    $this->currentStep++;
                }
            } catch (\Illuminate\Validation\ValidationException $e) {
                // Get the first error message
                $firstError = $e->validator->errors()->first();
                $failedFields = array_keys($e->validator->failed());
                $fieldName = $failedFields[0] ?? 'unknown';
                
                // Dispatch error with field information
                $this->dispatch('show-validation-error', [
                    'message' => 'Please fix: ' . $firstError,
                    'field' => $fieldName,
                    'step' => $this->currentStep
                ]);
                return;
            }
        } else {
            if ($this->currentStep < $this->totalSteps) {
                $this->currentStep++;
            }
        }
    }

    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function getStepValidationRules(int $step): array
    {
        return match($step) {
            1 => [
                'email' => 'required|email|unique:users,email',
                'phone' => 'required|string|max:20',
                'password' => 'required|min:10|confirmed',
                'terms' => 'accepted',
            ],
            2 => [
                'first_name' => 'required|string|max:255',
                'surname' => 'required|string|max:255',
                'gender' => 'required|in:male,female,other',
                'date_of_birth' => 'required|date|before:18 years ago',
                'profile_image' => 'nullable|image|max:2048',
            ],
            3 => [
                'country' => 'required|string',
                'area_of_operation' => 'required|string|max:255',
                'industry_or_interest' => 'required|string',
                'years_of_operation' => 'required|integer|min:0|max:100',
            ],
            4 => [
                'short_bio' => 'nullable|string|max:1000',
                'organization_name' => 'required|string|max:255',
                'tax_clearance' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
                'traders_license' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            ],
            default => []
        };
    }

    public function updated($propertyName)
    {
        // Real-time validation for the current step only
        $stepRules = $this->getStepValidationRules($this->currentStep);
        
        if (isset($stepRules[$propertyName])) {
            $this->validateOnly($propertyName);
        }
    }

    #[Computed]
    public function progressWidth()
    {
        return (($this->currentStep - 1) / max($this->totalSteps - 1, 1)) * 100;
    }
};
?>

<div class="form-view visible scrollable-view" id="register-view" x-data="{ step: @entangle('currentStep') }">
    <div class="form-header">
        <div class="form-eyebrow">Start Your Journey</div>
        <h2 class="form-title">Create account</h2>
        <p class="form-sub">Join Lesotho's enterprise ecosystem. Connect with mentors, secure investment, and access trade opportunities.</p>
    </div>

    {{-- Progress Indicator --}}
    <div class="progress-bar-container" style="margin-bottom: 2rem;">
        <div class="progress-steps" style="display: flex; justify-content: space-between; position: relative; margin-bottom: 1rem;">
            @for ($i = 1; $i <= $this->totalSteps; $i++)
                <div class="step-indicator" style="flex: 1; text-align: center; position: relative;">
                    <div class="step-circle" 
                         style="width: 32px; height: 32px; border-radius: 50%; 
                                background-color: {{ $this->currentStep >= $i ? 'var(--green)' : 'var(--border)' }};
                                color: {{ $this->currentStep >= $i ? 'white' : 'var(--smoke)' }}; 
                                display: flex; align-items: center; 
                                justify-content: center; margin: 0 auto; font-weight: 600;
                                font-size: 0.85rem; transition: all 0.3s ease;">
                        {{ $i }}
                    </div>
                    <div class="step-label" style="font-size: 0.65rem; margin-top: 0.35rem; color: var(--smoke); font-weight: 500;">
                        @switch($i)
                            @case(1) Account @break
                            @case(2) Personal @break
                            @case(3) Business @break
                            @case(4) Details @break
                        @endswitch
                    </div>
                </div>
            @endfor
            
            {{-- Progress Line --}}
            <div class="progress-line" 
                 style="position: absolute; top: 16px; left: 0; right: 0; height: 2px; 
                        background-color: var(--border); z-index: -1;">
                <div class="progress-fill" 
                     style="height: 100%; background-color: var(--green); width: {{ $this->progressWidth }}%; 
                            transition: width 0.3s ease;">
                </div>
            </div>
        </div>
    </div>

    <form wire:submit="register">
        @csrf

        {{-- Step 1: Account Information --}}
        <div x-show="step === 1" x-cloak x-transition:enter.duration.300ms>
            <div class="field">
                <label for="email">Email Address <span class="required-asterisk">*</span></label>
                <input wire:model="email" id="email" type="email" placeholder="you@example.com" autocomplete="email" class="@error('email') error-border @enderror">
                @error('email') <span class="error">{{ $message }}</span> @enderror
            </div>
            
            <div class="field">
                <label for="phone">Phone Number <span class="required-asterisk">*</span></label>
                <input wire:model="phone" id="phone" type="tel" placeholder="+266 1234 5678" autocomplete="tel" class="@error('phone') error-border @enderror">
                @error('phone') <span class="error">{{ $message }}</span> @enderror
            </div>
            
            <div class="field-row">
                <div class="field">
                    <label for="password">Password <span class="required-asterisk">*</span></label>
                    <input wire:model="password" id="password" type="password" placeholder="Min. 10 characters" autocomplete="new-password" class="@error('password') error-border @enderror">
                    @error('password') <span class="error">{{ $message }}</span> @enderror
                </div>
                
                <div class="field">
                    <label for="password_confirmation">Confirm Password <span class="required-asterisk">*</span></label>
                    <input wire:model="password_confirmation" id="password_confirmation" type="password" placeholder="Repeat password" autocomplete="new-password" class="@error('password_confirmation') error-border @enderror">
                    @error('password_confirmation') <span class="error">{{ $message }}</span> @enderror
                </div>
            </div>
            
            <div class="field-aux" style="margin-bottom: 0;">
                <label class="check-wrap">
                    <input wire:model="terms" type="checkbox" class="@error('terms') error-border @enderror">
                    I agree to the <a href="#" style="color: var(--navy); text-decoration: underline; text-underline-offset: 2px;">Terms & Privacy</a> <span class="required-asterisk">*</span>
                </label>
                @error('terms') <span class="error" style="display: block; margin-top: 0.5rem;">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- Step 2: Personal Information --}}
        <div x-show="step === 2" x-cloak x-transition:enter.duration.300ms>
            <div class="field-row">
                <div class="field">
                    <label for="first_name">First Name <span class="required-asterisk">*</span></label>
                    <input wire:model="first_name" id="first_name" type="text" placeholder="John" class="@error('first_name') error-border @enderror">
                    @error('first_name') <span class="error">{{ $message }}</span> @enderror
                </div>
                
                <div class="field">
                    <label for="surname">Surname <span class="required-asterisk">*</span></label>
                    <input wire:model="surname" id="surname" type="text" placeholder="Doe" class="@error('surname') error-border @enderror">
                    @error('surname') <span class="error">{{ $message }}</span> @enderror
                </div>
            </div>
            
            <div class="field-row">
                <div class="field">
                    <label for="gender">Gender <span class="required-asterisk">*</span></label>
                    <select wire:model="gender" id="gender" class="@error('gender') error-border @enderror">
                        <option value="">Select Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                    @error('gender') <span class="error">{{ $message }}</span> @enderror
                </div>
                
                <div class="field">
                    <label for="date_of_birth">Date of Birth <span class="required-asterisk">*</span></label>
                    <input wire:model="date_of_birth" id="date_of_birth" type="date" max="{{ now()->subYears(18)->format('Y-m-d') }}" class="@error('date_of_birth') error-border @enderror">
                    @error('date_of_birth') <span class="error">{{ $message }}</span> @enderror
                </div>
            </div>
            
            <div class="field">
                <label for="profile_image">Profile Image</label>
                <input wire:model="profile_image" id="profile_image" type="file" accept="image/jpeg,image/png,image/gif,image/jpg" style="padding: 0.5rem 0;" class="@error('profile_image') error-border @enderror">
                <small style="color: var(--smoke); font-size: 0.65rem;">
                    Max size: <strong>2MB</strong>. Accepted formats: JPG, PNG, GIF
                </small>
                @error('profile_image') 
                    <span class="error">{{ $message }}</span> 
                @enderror
                
                @if ($profile_image && !$errors->has('profile_image'))
                    <div class="preview" style="margin-top: 0.5rem;">
                        <img src="{{ $profile_image->temporaryUrl() }}" style="max-width: 80px; border-radius: 8px; border: 1px solid var(--border);">
                        <div style="font-size: 0.7rem; color: var(--green); margin-top: 0.25rem;">
                            ✓ {{ $profile_image->getClientOriginalName() }} ({{ round($profile_image->getSize() / 1024) }} KB)
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Step 3: Business Information --}}
        <div x-show="step === 3" x-cloak x-transition:enter.duration.300ms>
            <div class="field-row">
                <div class="field">
                    <label for="country">Country <span class="required-asterisk">*</span></label>
                    <select wire:model="country" id="country" class="@error('country') error-border @enderror">
                        @foreach($this->countries as $countryOption)
                            <option value="{{ $countryOption }}">{{ $countryOption }}</option>
                        @endforeach
                    </select>
                    @error('country') <span class="error">{{ $message }}</span> @enderror
                </div>
                
                <div class="field">
                    <label for="area_of_operation">Area of Operation <span class="required-asterisk">*</span></label>
                    <input wire:model="area_of_operation" id="area_of_operation" type="text" placeholder="City/District/Region" class="@error('area_of_operation') error-border @enderror">
                    @error('area_of_operation') <span class="error">{{ $message }}</span> @enderror
                </div>
            </div>
            
            <div class="field">
                <label for="industry_or_interest">Industry or Area of Interest <span class="required-asterisk">*</span></label>
                <select wire:model="industry_or_interest" id="industry_or_interest" class="@error('industry_or_interest') error-border @enderror">
                    <option value="">Select Industry</option>
                    @foreach($this->industries as $industry)
                        <option value="{{ $industry }}">{{ $industry }}</option>
                    @endforeach
                </select>
                @error('industry_or_interest') <span class="error">{{ $message }}</span> @enderror
            </div>
            
            <div class="field">
                <label for="years_of_operation">Years of Operation <span class="required-asterisk">*</span></label>
                <input wire:model="years_of_operation" id="years_of_operation" type="number" min="0" max="100" step="1" placeholder="e.g., 5" class="@error('years_of_operation') error-border @enderror">
                @error('years_of_operation') <span class="error">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- Step 4: Additional Details --}}
        <div x-show="step === 4" x-cloak x-transition:enter.duration.300ms>
            <div class="field">
                <label for="organization_name">Organization/Company Name <span class="required-asterisk">*</span></label>
                <input wire:model="organization_name" id="organization_name" type="text" placeholder="Your Business Name" class="@error('organization_name') error-border @enderror">
                @error('organization_name') <span class="error">{{ $message }}</span> @enderror
            </div>
            
            <div class="field">
                <label for="short_bio">Short Profile or Bio</label>
                <textarea wire:model="short_bio" id="short_bio" rows="3" placeholder="Tell us about yourself and your business..." class="@error('short_bio') error-border @enderror"></textarea>
                <small style="color: var(--smoke); font-size: 0.65rem;">
                    Max 1000 characters. Current: <span style="color: var(--green); font-weight: 600;">{{ strlen($short_bio) }}</span>
                </small>
                @error('short_bio') <span class="error">{{ $message }}</span> @enderror
            </div>
            
            {{-- Document uploads --}}
            <div class="documents-section" style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border);">
                <h4 style="font-family: var(--display); font-size: 0.9rem; margin-bottom: 1rem; color: var(--navy);">Required Documents</h4>
                
                <div class="field">
                    <label for="tax_clearance">Tax Clearance Certificate <span class="required-asterisk">*</span></label>
                    <input wire:model="tax_clearance" id="tax_clearance" type="file" accept=".pdf,.jpg,.jpeg,.png" style="padding: 0.5rem 0;" class="@error('tax_clearance') error-border @enderror">
                    <small style="color: var(--smoke); font-size: 0.65rem;">
                        Max size: <strong>5MB</strong>. Accepted formats: PDF, JPG, PNG
                    </small>
                    @error('tax_clearance') 
                        <span class="error">{{ $message }}</span> 
                    @enderror
                    
                    @if ($tax_clearance && !$errors->has('tax_clearance'))
                        <div class="file-name" style="margin-top: 0.25rem; font-size: 0.75rem; color: var(--green);">
                            ✓ {{ $tax_clearance->getClientOriginalName() }} ({{ round($tax_clearance->getSize() / 1024) }} KB)
                        </div>
                    @endif
                </div>
                
                <div class="field">
                    <label for="traders_license">Trader's License <span class="required-asterisk">*</span></label>
                    <input wire:model="traders_license" id="traders_license" type="file" accept=".pdf,.jpg,.jpeg,.png" style="padding: 0.5rem 0;" class="@error('traders_license') error-border @enderror">
                    <small style="color: var(--smoke); font-size: 0.65rem;">
                        Max size: <strong>5MB</strong>. Accepted formats: PDF, JPG, PNG
                    </small>
                    @error('traders_license') 
                        <span class="error">{{ $message }}</span> 
                    @enderror
                    
                    @if ($traders_license && !$errors->has('traders_license'))
                        <div class="file-name" style="margin-top: 0.25rem; font-size: 0.75rem; color: var(--green);">
                            ✓ {{ $traders_license->getClientOriginalName() }} ({{ round($traders_license->getSize() / 1024) }} KB)
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Navigation Buttons --}}
        <div class="form-navigation" style="display: flex; gap: 0.75rem; margin-top: 2rem;">
            @if($this->currentStep > 1)
                <button type="button" class="btn-secondary" wire:click="previousStep" style="flex: 1;">
                    ← Back
                </button>
            @endif
            
            @if($this->currentStep < $this->totalSteps)
                <button type="button" class="btn-green" wire:click="nextStep" style="flex: {{ $this->currentStep > 1 ? '2' : '1' }};">
                    Continue →
                </button>
            @else
                <button type="submit" class="btn-green" wire:loading.attr="disabled" style="flex: {{ $this->currentStep > 1 ? '2' : '1' }};">
                    <span wire:loading.remove>Create Account</span>
                    <span wire:loading>Processing...</span>
                    <svg width="15" height="15" viewBox="0 0 15 15" fill="none" style="margin-left: 0.5rem;">
                        <path d="M2.5 7.5H12.5M9 4L12.5 7.5L9 11" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            @endif
        </div>
    </form>

    <div class="switch-link">
        <p>Already have an account? 
            <button type="button" onclick="showLogin()">Sign in</button>
        </p>
    </div>
    <livewire:notify />
</div>

<script>
    document.addEventListener('livewire:initialized', () => {
        // Listen for validation error with field information
        Livewire.on('show-validation-error', (data) => {
            const payload = data[0] || data;
            
            // Create a friendly field name
            const fieldNames = {
                'email': 'Email Address',
                'phone': 'Phone Number',
                'password': 'Password',
                'terms': 'Terms & Conditions',
                'first_name': 'First Name',
                'surname': 'Surname',
                'gender': 'Gender',
                'date_of_birth': 'Date of Birth',
                'profile_image': 'Profile Image',
                'country': 'Country',
                'area_of_operation': 'Area of Operation',
                'industry_or_interest': 'Industry',
                'years_of_operation': 'Years of Operation',
                'organization_name': 'Organization Name',
                'short_bio': 'Short Bio',
                'tax_clearance': 'Tax Clearance Certificate',
                'traders_license': 'Trader\'s License'
            };
            
            const friendlyFieldName = fieldNames[payload.field] || payload.field;
            const stepNumber = payload.step;
            
            // Show SweetAlert with specific error
            Swal.fire({
                title: 'Validation Error',
                html: `
                    <div style="text-align: left;">
                        <p><strong>Error in Section ${stepNumber}:</strong> ${friendlyFieldName}</p>
                        <p class="text-danger">${payload.message}</p>
                        <hr>
                        <p class="text-muted small">You have been redirected to the section with the error. Please fix the issue and try again.</p>
                    </div>
                `,
                icon: 'error',
                confirmButtonText: 'OK, Go to Error',
                confirmButtonColor: '#dc2626',
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    // Scroll to the error field
                    setTimeout(() => {
                        const errorField = document.querySelector('.error-border');
                        if (errorField) {
                            errorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            errorField.focus();
                        }
                    }, 300);
                }
            });
        });
    });
</script>

<style>
    [x-cloak] { display: none !important; }
    
    /* Required asterisk styling */
    .required-asterisk {
        color: #dc2626;
        font-size: 1rem;
        font-weight: bold;
        margin-left: 2px;
    }
    
    /* Error border styling */
    .error-border {
        border-color: #dc2626 !important;
        background-color: #fef2f2 !important;
    }
    
    /* Field row styling */
    .field-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-bottom: 0;
    }
    
    /* Field styling */
    .field {
        margin-bottom: 1rem;
    }
    
    .field label {
        display: block;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--smoke);
        margin-bottom: 0.5rem;
    }
    
    .field input:not([type="checkbox"]):not([type="file"]),
    .field select,
    .field textarea {
        width: 100%;
        padding: 0.7rem 1rem;
        background: var(--white);
        border: 1.5px solid var(--border);
        border-radius: 12px;
        font-family: var(--body);
        font-size: 0.88rem;
        font-weight: 400;
        color: var(--charcoal);
        outline: none;
        transition: all 0.2s;
    }
    
    .field input:focus,
    .field select:focus,
    .field textarea:focus {
        border-color: var(--green);
        box-shadow: 0 0 0 3px rgba(5,146,59,0.1);
    }
    
    /* Select field styling */
    select {
        cursor: pointer;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 1rem center;
        background-size: 16px;
    }
    
    /* Checkbox styling */
    .check-wrap {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
    }
    
    .check-wrap input {
        width: auto;
        margin: 0;
        cursor: pointer;
    }
    
    /* Error message styling */
    .error {
        color: #dc2626;
        font-size: 0.7rem;
        margin-top: 0.25rem;
        display: block;
        font-weight: 400;
    }
    
    /* File input styling */
    input[type="file"] {
        padding: 0.5rem 0 !important;
        border: none !important;
        background: transparent !important;
    }
    
    /* Progress bar styling */
    .progress-bar-container {
        margin-bottom: 2rem;
    }
    
    .step-circle {
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        font-family: var(--display);
    }
    
    /* Button variants */
    .btn-secondary {
        background: var(--green-pale);
        border: 1.5px solid rgba(5,146,59,0.2);
        color: var(--green);
        font-family: var(--display);
        font-size: 0.83rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        padding: 0.82rem;
        border-radius: 50px;
        cursor: pointer;
        transition: all 0.22s;
    }
    
    .btn-secondary:hover {
        background: var(--green);
        border-color: var(--green);
        color: var(--white);
        box-shadow: 0 4px 16px rgba(5,146,59,0.3);
        transform: translateY(-1px);
    }
    
    .btn-green {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        background: var(--green);
        border: none;
        border-radius: 50px;
        color: var(--white);
        font-family: var(--display);
        font-size: 0.83rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        padding: 0.88rem;
        cursor: pointer;
        box-shadow: 0 4px 16px rgba(5,146,59,0.28);
        transition: all 0.22s;
    }
    
    .btn-green:hover {
        background: var(--green-light);
        box-shadow: 0 6px 20px rgba(5,146,59,0.38);
        transform: translateY(-1px);
    }
    
    .btn-green:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
    }
    
    .btn-green svg {
        transition: transform 0.2s;
    }
    
    .btn-green:hover svg {
        transform: translateX(3px);
    }
    
    /* Ensure the form view is visible */
    .form-view.visible {
        display: block !important;
        opacity: 1 !important;
        visibility: visible !important;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .scrollable-view {
            max-height: calc(100vh - 100px);
            padding-right: 4px;
        }
        
        .field-row {
            grid-template-columns: 1fr;
            gap: 0;
        }
    }
</style>