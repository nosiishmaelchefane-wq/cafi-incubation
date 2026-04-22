<?php

use App\Models\User;
use App\Models\Entrepreneur;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeApplicant;

new class extends Component {
    use WithFileUploads;

    // Account Information
    public string $email = '';
    public string $phone = '';
    public string $password = '';
    public string $password_confirmation = '';
    public bool $terms = false;
    
    // Entrepreneur specific fields
    public string $first_name = '';
    public string $surname = '';
    public string $gender = '';
    public string $date_of_birth = '';
    public string $country = 'Lesotho';
    public string $area_of_operation = '';
    public string $industry_or_interest = '';
    public ?int $years_of_operation = null;
    public string $short_bio = '';
    public string $organization_name = '';
    
    // File uploads
    public $profile_image = null;
    public $tax_clearance = null;
    public $traders_license = null;
    
    // Step management
    public int $currentStep = 1;
    public int $totalSteps = 4;
    
    // Industry options
    public array $industries = [
        'Agriculture', 'Technology', 'Manufacturing', 'Banking', 'Education',
        'Healthcare', 'Retail', 'Construction', 'Tourism', 'Transportation',
        'Energy', 'Mining', 'Financial Services', 'Real Estate', 'Creative Arts',
        'Food & Beverage', 'Consulting', 'Other'
    ];
    
    // Country options
    public array $countries = [];

    public function mount()
    {
        $this->countries = [
            'Lesotho', 'South Africa', 'Botswana', 'Eswatini', 'Namibia',
            'Zimbabwe', 'Mozambique', 'Zambia', 'Malawi', 'Angola', 'Tanzania',
            'Kenya', 'Uganda', 'Rwanda', 'Burundi', 'Ethiopia', 'Nigeria',
            'Ghana', 'Egypt', 'Morocco', 'United States', 'United Kingdom',
            'Canada', 'Australia', 'Germany', 'France', 'Italy', 'Spain',
            'Portugal', 'Netherlands', 'Belgium', 'Switzerland', 'Sweden',
            'Norway', 'Denmark', 'Finland', 'China', 'India', 'Japan',
            'South Korea', 'Singapore', 'Malaysia', 'Indonesia', 'Philippines',
            'Vietnam', 'Thailand', 'Brazil', 'Argentina', 'Mexico', 'Colombia',
            'Chile', 'Peru'
        ];
        sort($this->countries);
        $this->countries = array_merge(['Lesotho'], array_diff($this->countries, ['Lesotho']));
    }

    public function getStepValidationRules(int $step): array
    {
        if ($step == 1) {
            return [
                'email' => 'required|email|unique:users,email',
                'phone' => 'required|string|max:20',
                'password' => 'required|min:10|confirmed',
                'terms' => 'accepted',
            ];
        } elseif ($step == 2) {
            return [
                'first_name' => 'required|string|max:255',
                'surname' => 'required|string|max:255',
                'gender' => 'required|in:male,female,other',
                'date_of_birth' => 'required|date|before:18 years ago',
                'profile_image' => 'nullable|image|max:2048',
            ];
        } elseif ($step == 3) {
            return [
                'country' => 'required|string',
                'area_of_operation' => 'required|string|max:255',
                'industry_or_interest' => 'required|string',
                'years_of_operation' => 'required|integer|min:0|max:100',
            ];
        } elseif ($step == 4) {
            return [
                'short_bio' => 'nullable|string|max:1000',
                'organization_name' => 'required|string|max:255',
                'tax_clearance' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
                'traders_license' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            ];
        }
        
        return [];
    }
    
    public function getStepMessages(int $step): array
    {
        return [
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already registered.',
            'phone.required' => 'Phone number is required.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 10 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
            'terms.accepted' => 'You must agree to the Terms & Privacy policy.',
            'first_name.required' => 'First name is required.',
            'surname.required' => 'Surname is required.',
            'gender.required' => 'Please select your gender.',
            'date_of_birth.required' => 'Date of birth is required.',
            'date_of_birth.before' => 'You must be at least 18 years old.',
            'profile_image.image' => 'File must be an image (JPG, PNG, GIF).',
            'profile_image.max' => 'Image size must not exceed 2MB.',
            'country.required' => 'Country is required.',
            'area_of_operation.required' => 'Area of operation is required.',
            'industry_or_interest.required' => 'Industry is required.',
            'years_of_operation.required' => 'Years of operation is required.',
            'years_of_operation.integer' => 'Please enter a valid number.',
            'organization_name.required' => 'Organization name is required.',
            'tax_clearance.required' => 'Tax clearance certificate is required.',
            'tax_clearance.mimes' => 'File must be PDF, JPG, or PNG.',
            'tax_clearance.max' => 'File size must not exceed 5MB.',
            'traders_license.required' => 'Trader\'s license is required.',
            'traders_license.mimes' => 'File must be PDF, JPG, or PNG.',
            'traders_license.max' => 'File size must not exceed 5MB.',
        ];
    }

    public function nextStep()
    {
        $rules = $this->getStepValidationRules($this->currentStep);
        $messages = $this->getStepMessages($this->currentStep);
        
        if (!empty($rules)) {
            $this->validate($rules, $messages);
        }
        
        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
        }
    }

    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function register()
    {
        // Validate all steps
        $allRules = [];
        $allMessages = [];
        for ($i = 1; $i <= $this->totalSteps; $i++) {
            $allRules = array_merge($allRules, $this->getStepValidationRules($i));
            $allMessages = array_merge($allMessages, $this->getStepMessages($i));
        }
        
        $this->validate($allRules, $allMessages);
        
        $username = $this->first_name . ' ' . $this->surname;
        
        try {
            $profileImagePath = $this->profile_image 
                ? $this->profile_image->store('profile-images', 'public') 
                : null;
                
            $taxClearancePath = $this->tax_clearance
                ? $this->tax_clearance->store('documents/tax-clearance', 'public')
                : null;
                
            $tradersLicensePath = $this->traders_license
                ? $this->traders_license->store('documents/traders-license', 'public')
                : null;
            
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
            
            Mail::to($user->email)->send(new WelcomeApplicant($user, $username));
            
            session()->flash('success', 'Your application has been submitted successfully!');
            return $this->redirect('/', navigate: true);
            
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                if (str_contains($e->getMessage(), 'users_email_unique')) {
                    $this->addError('email', 'This email address is already registered.');
                    $this->currentStep = 1;
                } else {
                    $this->addError('email', 'Registration failed: ' . $e->getMessage());
                }
            } else {
                $this->addError('email', 'Database error: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            $this->addError('email', 'Registration failed: ' . $e->getMessage());
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
    <div class="progress-bar-container">
        <div class="progress-steps">
            @for ($i = 1; $i <= $this->totalSteps; $i++)
                <div class="step-indicator">
                    <div class="step-circle" style="background-color: {{ $this->currentStep >= $i ? 'var(--green)' : 'var(--border)' }};
                                color: {{ $this->currentStep >= $i ? 'white' : 'var(--smoke)' }};">
                        {{ $i }}
                    </div>
                    <div class="step-label">
                        @switch($i)
                            @case(1) Account @break
                            @case(2) Personal @break
                            @case(3) Business @break
                            @case(4) Details @break
                        @endswitch
                    </div>
                </div>
            @endfor
            <div class="progress-line">
                <div class="progress-fill" style="width: {{ $this->progressWidth }}%;"></div>
            </div>
        </div>
    </div>

    <form wire:submit="register">
        @csrf

        {{-- Step 1: Account Information --}}
        <div x-show="step === 1" x-cloak>
            <div class="field">
                <label>Email Address <span class="required-asterisk">*</span></label>
                <input type="email" wire:model.live="email" placeholder="you@example.com">
                @error('email') <span class="error">{{ $message }}</span> @enderror
            </div>
            
            <div class="field">
                <label>Phone Number <span class="required-asterisk">*</span></label>
                <input type="tel" wire:model.live="phone" placeholder="+266 1234 5678">
                @error('phone') <span class="error">{{ $message }}</span> @enderror
            </div>
            
            <div class="field-row">
                <div class="field">
                    <label>Password <span class="required-asterisk">*</span></label>
                    <input type="password" wire:model.live="password" placeholder="Min. 10 characters">
                    @error('password') <span class="error">{{ $message }}</span> @enderror
                </div>
                
                <div class="field">
                    <label>Confirm Password <span class="required-asterisk">*</span></label>
                    <input type="password" wire:model.live="password_confirmation" placeholder="Repeat password">
                    @error('password_confirmation') <span class="error">{{ $message }}</span> @enderror
                </div>
            </div>
            
            <div class="field-aux">
                <label class="check-wrap">
                    <input type="checkbox" wire:model.live="terms">
                    I agree to the <a href="#">Terms & Privacy</a> <span class="required-asterisk">*</span>
                </label>
                @error('terms') <span class="error">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- Step 2: Personal Information --}}
        <div x-show="step === 2" x-cloak>
            <div class="field-row">
                <div class="field">
                    <label>First Name <span class="required-asterisk">*</span></label>
                    <input type="text" wire:model.live="first_name" placeholder="John">
                    @error('first_name') <span class="error">{{ $message }}</span> @enderror
                </div>
                
                <div class="field">
                    <label>Surname <span class="required-asterisk">*</span></label>
                    <input type="text" wire:model.live="surname" placeholder="Doe">
                    @error('surname') <span class="error">{{ $message }}</span> @enderror
                </div>
            </div>
            
            <div class="field-row">
                <div class="field">
                    <label>Gender <span class="required-asterisk">*</span></label>
                    <select wire:model.live="gender">
                        <option value="">Select Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                    @error('gender') <span class="error">{{ $message }}</span> @enderror
                </div>
                
                <div class="field">
                    <label>Date of Birth <span class="required-asterisk">*</span></label>
                    <input type="date" wire:model.live="date_of_birth" max="{{ now()->subYears(18)->format('Y-m-d') }}">
                    @error('date_of_birth') <span class="error">{{ $message }}</span> @enderror
                </div>
            </div>
            
            <div class="field">
                <label>Profile Image</label>
                <input type="file" wire:model.live="profile_image" accept="image/jpeg,image/png,image/gif,image/jpg">
                <small>Max size: <strong>2MB</strong>. Accepted: JPG, PNG, GIF</small>
                @error('profile_image') <span class="error">{{ $message }}</span> @enderror
                
                @if ($profile_image && !$errors->has('profile_image'))
                    <div class="preview">
                        <img src="{{ $profile_image->temporaryUrl() }}">
                        <div class="file-info">✓ {{ $profile_image->getClientOriginalName() }} ({{ round($profile_image->getSize() / 1024) }} KB)</div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Step 3: Business Information --}}
        <div x-show="step === 3" x-cloak>
            <div class="field-row">
                <div class="field">
                    <label>Country <span class="required-asterisk">*</span></label>
                    <select wire:model.live="country">
                        @foreach($this->countries as $countryOption)
                            <option value="{{ $countryOption }}">{{ $countryOption }}</option>
                        @endforeach
                    </select>
                    @error('country') <span class="error">{{ $message }}</span> @enderror
                </div>
                
                <div class="field">
                    <label>Area of Operation <span class="required-asterisk">*</span></label>
                    <input type="text" wire:model.live="area_of_operation" placeholder="City/District/Region">
                    @error('area_of_operation') <span class="error">{{ $message }}</span> @enderror
                </div>
            </div>
            
            <div class="field">
                <label>Industry <span class="required-asterisk">*</span></label>
                <select wire:model.live="industry_or_interest">
                    <option value="">Select Industry</option>
                    @foreach($this->industries as $industry)
                        <option value="{{ $industry }}">{{ $industry }}</option>
                    @endforeach
                </select>
                @error('industry_or_interest') <span class="error">{{ $message }}</span> @enderror
            </div>
            
            <div class="field">
                <label>Years of Operation <span class="required-asterisk">*</span></label>
                <input type="number" wire:model.live="years_of_operation" min="0" max="100" placeholder="e.g., 5">
                @error('years_of_operation') <span class="error">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- Step 4: Additional Details --}}
        <div x-show="step === 4" x-cloak>
            <div class="field">
                <label>Organization Name <span class="required-asterisk">*</span></label>
                <input type="text" wire:model.live="organization_name" placeholder="Your Business Name">
                @error('organization_name') <span class="error">{{ $message }}</span> @enderror
            </div>
            
            <div class="field">
                <label>Short Bio</label>
                <textarea wire:model.live="short_bio" rows="3" placeholder="Tell us about yourself..."></textarea>
                <small>Max 1000 characters. Current: <strong>{{ strlen($short_bio) }}</strong></small>
                @error('short_bio') <span class="error">{{ $message }}</span> @enderror
            </div>
            
            <div class="documents-section">
                <h4>Required Documents</h4>
                
                <div class="field">
                    <label>Tax Clearance Certificate <span class="required-asterisk">*</span></label>
                    <input type="file" wire:model.live="tax_clearance" accept=".pdf,.jpg,.jpeg,.png">
                    <small>Max size: <strong>5MB</strong>. Accepted: PDF, JPG, PNG</small>
                    @error('tax_clearance') <span class="error">{{ $message }}</span> @enderror
                    
                    @if ($tax_clearance && !$errors->has('tax_clearance'))
                        <div class="file-info">✓ {{ $tax_clearance->getClientOriginalName() }} ({{ round($tax_clearance->getSize() / 1024) }} KB)</div>
                    @endif
                </div>
                
                <div class="field">
                    <label>Trader's License <span class="required-asterisk">*</span></label>
                    <input type="file" wire:model.live="traders_license" accept=".pdf,.jpg,.jpeg,.png">
                    <small>Max size: <strong>5MB</strong>. Accepted: PDF, JPG, PNG</small>
                    @error('traders_license') <span class="error">{{ $message }}</span> @enderror
                    
                    @if ($traders_license && !$errors->has('traders_license'))
                        <div class="file-info">✓ {{ $traders_license->getClientOriginalName() }} ({{ round($traders_license->getSize() / 1024) }} KB)</div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Navigation Buttons --}}
        <div class="form-navigation">
            @if($this->currentStep > 1)
                <button type="button" class="btn-secondary" wire:click="previousStep">← Back</button>
            @endif
            
            @if($this->currentStep < $this->totalSteps)
                <button type="button" class="btn-green" wire:click="nextStep">Continue →</button>
            @else
                <button type="submit" class="btn-green" wire:loading.attr="disabled">
                    <span wire:loading.remove>Create Account</span>
                    <span wire:loading>Processing...</span>
                </button>
            @endif
        </div>
    </form>

    <div class="switch-link">
        <p>Already have an account? 
            <button type="button" onclick="showLogin()">Sign in</button>
        </p>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
    
    .required-asterisk {
        color: #dc2626;
        font-size: 1rem;
        font-weight: bold;
        margin-left: 2px;
    }
    
    .field-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }
    
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
        outline: none;
        transition: all 0.2s;
    }
    
    .field input:focus,
    .field select:focus,
    .field textarea:focus {
        border-color: var(--green);
        box-shadow: 0 0 0 3px rgba(5,146,59,0.1);
    }
    
    select {
        cursor: pointer;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 1rem center;
        background-size: 16px;
    }
    
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
    
    .error {
        color: #dc2626;
        font-size: 0.7rem;
        margin-top: 0.25rem;
        display: block;
    }
    
    input[type="file"] {
        padding: 0.5rem 0 !important;
        border: none !important;
        background: transparent !important;
    }
    
    .progress-bar-container {
        margin-bottom: 2rem;
    }
    
    .progress-steps {
        display: flex;
        justify-content: space-between;
        position: relative;
        margin-bottom: 1rem;
    }
    
    .step-indicator {
        flex: 1;
        text-align: center;
        position: relative;
    }
    
    .step-circle {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        font-weight: 600;
        font-size: 0.85rem;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    
    .step-label {
        font-size: 0.65rem;
        margin-top: 0.35rem;
        color: var(--smoke);
        font-weight: 500;
    }
    
    .progress-line {
        position: absolute;
        top: 16px;
        left: 0;
        right: 0;
        height: 2px;
        background-color: var(--border);
        z-index: -1;
    }
    
    .progress-fill {
        height: 100%;
        background-color: var(--green);
        transition: width 0.3s ease;
    }
    
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
        flex: 1;
    }
    
    .btn-secondary:hover {
        background: var(--green);
        border-color: var(--green);
        color: var(--white);
        box-shadow: 0 4px 16px rgba(5,146,59,0.3);
        transform: translateY(-1px);
    }
    
    .btn-green {
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
        flex: 2;
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
    
    .form-navigation {
        display: flex;
        gap: 0.75rem;
        margin-top: 2rem;
    }
    
    .preview {
        margin-top: 0.5rem;
    }
    
    .preview img {
        max-width: 80px;
        border-radius: 8px;
        border: 1px solid var(--border);
    }
    
    .file-info {
        font-size: 0.75rem;
        color: var(--green);
        margin-top: 0.25rem;
    }
    
    .documents-section {
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid var(--border);
    }
    
    .documents-section h4 {
        font-size: 0.9rem;
        margin-bottom: 1rem;
        color: var(--navy);
    }
    
    .switch-link {
        text-align: center;
        margin-top: 1.5rem;
        padding-top: 1rem;
        border-top: 1px solid var(--border);
    }
    
    .switch-link button {
        background: none;
        border: none;
        color: var(--green);
        font-weight: 600;
        cursor: pointer;
        text-decoration: underline;
    }
    
    @media (max-width: 768px) {
        .field-row {
            grid-template-columns: 1fr;
            gap: 0;
        }
    }
</style>