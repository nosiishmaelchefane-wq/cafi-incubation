<?php

use Livewire\Component;
use App\Models\IncubationApplication;
use App\Models\Call;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On; 
use Illuminate\Support\Facades\Mail;
use App\Mail\ApplicationSubmittedMail;

new class extends Component
{
    public $id;
    public $call;
    public $application_id; 
    public $isEditMode = false; 
    
    // System generated
    public $application_number;
    public $applied_date;
    public $applied_time;
    
    // Company Information
    public $company_name;
    public $registered_company_name;
    public $business_description;
    public $business_model;
    public $offering;
    public $revenue_model;
    public $website;
    public $company_type;
    public $industry;
    public $sector;
    public $country = 'Lesotho';
    public $district;
    public $year_of_establishment;
    public $company_stage;
    public $company_size;
    
    // Social Media
    public $social_media_facebook;
    public $social_media_twitter;
    public $social_media_linkedin;
    public $social_media_instagram;
    
    // Applicant Information
    public $applicant_name;
    public $applicant_email;
    public $applicant_title;
    public $applicant_gender;
    public $applicant_nationality = 'Lesotho';
    public $applicant_contact_number;
    public $applicant_about;
    public $applicant_twitter;
    public $applicant_linkedin;
    
    // Financial & Support Questions
    public $received_financial_support;
    public $participated_competitions;
    public $willing_to_commit;
    
    // Numeric Fields
    public $number_of_shareholders = 0;
    public $number_of_customers = 0;
    public $average_monthly_sales = 0;
    public $jobs_to_create_12_months = 0;
    public $number_women_shareholders = 0;
    public $number_youth_shareholders = 0;
    
    // Additional Fields
    public $industry_other_elaboration;
    
    public function mount($id = null)
    {
        if ($id) {
            $this->id = $id;
            $this->call = Call::findOrFail($id);
        }
    }
    
    protected function rules()
    {
        return [
            'company_name' => 'required|string|max:255',
            'registered_company_name' => 'required|string|max:255',
            'business_description' => 'required|string|min:50',
            'business_model' => 'required|string|max:100',
            'offering' => 'required|string|max:100',
            'revenue_model' => 'required|string|max:100',
            'website' => 'nullable|url|max:255',
            'company_type' => 'required|string|max:100',
            'industry' => 'required|string|max:100',
            'sector' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'year_of_establishment' => 'required|integer|min:1900|max:' . date('Y'),
            'company_stage' => 'required|string|max:100',
            'company_size' => 'required|string|max:100',
            'social_media_facebook' => 'nullable|url|max:255',
            'social_media_twitter' => 'nullable|url|max:255',
            'social_media_linkedin' => 'nullable|url|max:255',
            'social_media_instagram' => 'nullable|url|max:255',
            'applicant_name' => 'required|string|max:255',
            'applicant_email' => 'required|email|max:255',
            'applicant_title' => 'required|string|max:100',
            'applicant_gender' => 'required|string|max:20',
            'applicant_nationality' => 'required|string|max:100',
            'applicant_contact_number' => 'required|string|max:20',
            'applicant_about' => 'required|string|max:1000',
            'applicant_twitter' => 'nullable|url|max:255',
            'applicant_linkedin' => 'nullable|url|max:255',
            'received_financial_support' => 'required|string|max:50',
            'participated_competitions' => 'required|string|max:50',
            'willing_to_commit' => 'required|boolean',
            'number_of_shareholders' => 'required|integer|min:0',
            'number_of_customers' => 'required|integer|min:0',
            'average_monthly_sales' => 'required|numeric|min:0',
            'jobs_to_create_12_months' => 'required|integer|min:0',
            'number_women_shareholders' => 'required|integer|min:0',
            'number_youth_shareholders' => 'required|integer|min:0',
            'industry_other_elaboration' => 'nullable|string|max:500',
        ];
    }
    
    protected $messages = [
        'company_name.required' => 'Please enter your company name',
        'business_description.required' => 'Please provide a business description',
        'business_description.min' => 'Business description must be at least 50 characters',
        'applicant_name.required' => 'Please enter your full name',
        'applicant_email.required' => 'Please enter your email address',
        'applicant_email.email' => 'Please enter a valid email address',
        'applicant_contact_number.required' => 'Please enter your contact number',
        'willing_to_commit.required' => 'Please indicate if you are willing to commit to the program',
    ];
    
     #[On('edit-application')]
    public function editApplication($applicationId)
    {
        $this->application_id = $applicationId;
        $this->isEditMode = true;
        
        $application = IncubationApplication::findOrFail($applicationId);
        
        // Load application data into form fields
        $this->company_name = $application->company_name;
        $this->registered_company_name = $application->registered_company_name;
        $this->business_description = $application->business_description;
        $this->business_model = $application->business_model;
        $this->offering = $application->offering;
        $this->revenue_model = $application->revenue_model;
        $this->website = $application->website;
        $this->company_type = $application->company_type;
        $this->industry = $application->industry;
        $this->sector = $application->sector;
        $this->country = $application->country;
        $this->district = $application->district;
        $this->year_of_establishment = $application->year_of_establishment;
        $this->company_stage = $application->company_stage;
        $this->company_size = $application->company_size;
        
        // Load social media
        if ($application->social_media) {
            $this->social_media_facebook = $application->social_media['facebook'] ?? '';
            $this->social_media_twitter = $application->social_media['twitter'] ?? '';
            $this->social_media_linkedin = $application->social_media['linkedin'] ?? '';
            $this->social_media_instagram = $application->social_media['instagram'] ?? '';
        }
        
        $this->applicant_name = $application->applicant_name;
        $this->applicant_email = $application->applicant_email;
        $this->applicant_title = $application->applicant_title;
        $this->applicant_gender = $application->applicant_gender;
        $this->applicant_nationality = $application->applicant_nationality;
        $this->applicant_contact_number = $application->applicant_contact_number;
        $this->applicant_about = $application->applicant_about;
        
        // Load applicant social media
        if ($application->applicant_social_media) {
            $this->applicant_twitter = $application->applicant_social_media['twitter'] ?? '';
            $this->applicant_linkedin = $application->applicant_social_media['linkedin'] ?? '';
        }
        
        $this->received_financial_support = $application->received_financial_support;
        $this->participated_competitions = $application->participated_competitions;
        $this->willing_to_commit = (bool)$application->willing_to_commit;
        $this->number_of_shareholders = $application->number_of_shareholders;
        $this->number_of_customers = $application->number_of_customers;
        $this->average_monthly_sales = $application->average_monthly_sales;
        $this->jobs_to_create_12_months = $application->jobs_to_create_12_months;
        $this->number_women_shareholders = $application->number_women_shareholders;
        $this->number_youth_shareholders = $application->number_youth_shareholders;
        $this->industry_other_elaboration = $application->industry_other_elaboration;
        
        // Dispatch event to open modal
        $this->dispatch('open-application-modal');
    }
    
    public function submitApplication()
    {
        $this->validate();
       
        
        
        
        if ($this->isEditMode) {
            // Update existing application
            $application = IncubationApplication::findOrFail($this->application_id);
            
            // Prepare social media JSON
            $socialMedia = [];
            if ($this->social_media_facebook) $socialMedia['facebook'] = $this->social_media_facebook;
            if ($this->social_media_twitter) $socialMedia['twitter'] = $this->social_media_twitter;
            if ($this->social_media_linkedin) $socialMedia['linkedin'] = $this->social_media_linkedin;
            if ($this->social_media_instagram) $socialMedia['instagram'] = $this->social_media_instagram;
            
            // Prepare applicant social media JSON
            $applicantSocialMedia = [];
            if ($this->applicant_twitter) $applicantSocialMedia['twitter'] = $this->applicant_twitter;
            if ($this->applicant_linkedin) $applicantSocialMedia['linkedin'] = $this->applicant_linkedin;
            
            // Update application
            $application->update([
                'company_name' => $this->company_name,
                'registered_company_name' => $this->registered_company_name,
                'business_description' => $this->business_description,
                'business_model' => $this->business_model,
                'offering' => $this->offering,
                'revenue_model' => $this->revenue_model,
                'website' => $this->website,
                'company_type' => $this->company_type,
                'industry' => $this->industry,
                'sector' => $this->sector,
                'country' => $this->country,
                'district' => $this->district,
                'year_of_establishment' => $this->year_of_establishment,
                'company_stage' => $this->company_stage,
                'company_size' => $this->company_size,
                'social_media' => !empty($socialMedia) ? $socialMedia : null,
                'applicant_name' => $this->applicant_name,
                'applicant_email' => $this->applicant_email,
                'applicant_title' => $this->applicant_title,
                'applicant_gender' => $this->applicant_gender,
                'applicant_nationality' => $this->applicant_nationality,
                'applicant_contact_number' => $this->applicant_contact_number,
                'applicant_about' => $this->applicant_about,
                'applicant_social_media' => !empty($applicantSocialMedia) ? $applicantSocialMedia : null,
                'received_financial_support' => $this->received_financial_support,
                'participated_competitions' => $this->participated_competitions,
                'willing_to_commit' => $this->willing_to_commit,
                'number_of_shareholders' => $this->number_of_shareholders,
                'number_of_customers' => $this->number_of_customers,
                'average_monthly_sales' => $this->average_monthly_sales,
                'jobs_to_create_12_months' => $this->jobs_to_create_12_months,
                'number_women_shareholders' => $this->number_women_shareholders,
                'number_youth_shareholders' => $this->number_youth_shareholders,
                'industry_other_elaboration' => $this->industry_other_elaboration,
                'updated_at' => now(),
            ]);
            
            $this->resetForm();
            $this->dispatch('close-application-modal');
            $this->dispatch('application-updated');
            $this->dispatch('notify', type: 'success', message: 'Application updated successfully!');
            
        } else {
            // Create new application
            $existingApplication = IncubationApplication::where('call_id', $this->id)
                ->where('user_id', Auth::id())
                ->first();
                
            if ($existingApplication) {
                $this->dispatch('notify', type: 'error', message: 'You have already applied for this call');
                return;
            }
            
            // Prepare social media JSON
            $socialMedia = [];
            if ($this->social_media_facebook) $socialMedia['facebook'] = $this->social_media_facebook;
            if ($this->social_media_twitter) $socialMedia['twitter'] = $this->social_media_twitter;
            if ($this->social_media_linkedin) $socialMedia['linkedin'] = $this->social_media_linkedin;
            if ($this->social_media_instagram) $socialMedia['instagram'] = $this->social_media_instagram;
            
            // Prepare applicant social media JSON
            $applicantSocialMedia = [];
            if ($this->applicant_twitter) $applicantSocialMedia['twitter'] = $this->applicant_twitter;
            if ($this->applicant_linkedin) $applicantSocialMedia['linkedin'] = $this->applicant_linkedin;
            
            // Create application
            $application = IncubationApplication::create([
                'application_number' => $this->generateApplicationNumber(),
                'applied_date' => now(),
                'applied_time' => now(),
                'company_name' => $this->company_name,
                'registered_company_name' => $this->registered_company_name,
                'business_description' => $this->business_description,
                'business_model' => $this->business_model,
                'offering' => $this->offering,
                'revenue_model' => $this->revenue_model,
                'website' => $this->website,
                'company_type' => $this->company_type,
                'industry' => $this->industry,
                'sector' => $this->sector,
                'country' => $this->country,
                'district' => $this->district,
                'year_of_establishment' => $this->year_of_establishment,
                'company_stage' => $this->company_stage,
                'company_size' => $this->company_size,
                'social_media' => !empty($socialMedia) ? $socialMedia : null,
                'applicant_name' => $this->applicant_name,
                'applicant_email' => $this->applicant_email,
                'applicant_title' => $this->applicant_title,
                'applicant_gender' => $this->applicant_gender,
                'applicant_nationality' => $this->applicant_nationality,
                'applicant_contact_number' => $this->applicant_contact_number,
                'applicant_about' => $this->applicant_about,
                'applicant_social_media' => !empty($applicantSocialMedia) ? $applicantSocialMedia : null,
                'received_financial_support' => $this->received_financial_support,
                'participated_competitions' => $this->participated_competitions,
                'willing_to_commit' => $this->willing_to_commit,
                'number_of_shareholders' => $this->number_of_shareholders,
                'number_of_customers' => $this->number_of_customers,
                'average_monthly_sales' => $this->average_monthly_sales,
                'jobs_to_create_12_months' => $this->jobs_to_create_12_months,
                'number_women_shareholders' => $this->number_women_shareholders,
                'number_youth_shareholders' => $this->number_youth_shareholders,
                'industry_other_elaboration' => $this->industry_other_elaboration,
                'call_id' => $this->id,
                'user_id' => Auth::id(),
                'status' => 'pending',
                'submitted_at' => now(),
            ]);

            try {
                    Mail::to($this->applicant_email)->send(new ApplicationSubmittedMail($application));
                } catch (\Exception $e) {
                    // Log error but don't stop the application process
                    \Log::error('Failed to send application email: ' . $e->getMessage());
            }
            
            $this->resetForm();
            $this->dispatch('close-application-modal');
            $this->dispatch('notify', type: 'success', message: 'Application submitted successfully! Application Number: ' . $application->application_number);
        }
    }
    
    public function generateApplicationNumber()
    {
        $prefix = 'INC';
        $year = date('Y');
        $month = date('m');
        
        $lastApplication = IncubationApplication::whereYear('created_at', $year)
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
    
    public function resetForm()
    {
        $this->isEditMode = false;
        $this->application_id = null;
        $this->company_name = '';
        $this->registered_company_name = '';
        $this->business_description = '';
        $this->business_model = '';
        $this->offering = '';
        $this->revenue_model = '';
        $this->website = '';
        $this->company_type = '';
        $this->industry = '';
        $this->sector = '';
        $this->country = 'Lesotho';
        $this->district = '';
        $this->year_of_establishment = '';
        $this->company_stage = '';
        $this->company_size = '';
        $this->social_media_facebook = '';
        $this->social_media_twitter = '';
        $this->social_media_linkedin = '';
        $this->social_media_instagram = '';
        $this->applicant_name = '';
        $this->applicant_email = '';
        $this->applicant_title = '';
        $this->applicant_gender = '';
        $this->applicant_nationality = 'Lesotho';
        $this->applicant_contact_number = '';
        $this->applicant_about = '';
        $this->applicant_twitter = '';
        $this->applicant_linkedin = '';
        $this->received_financial_support = '';
        $this->participated_competitions = '';
        $this->willing_to_commit = '';
        $this->number_of_shareholders = 0;
        $this->number_of_customers = 0;
        $this->average_monthly_sales = 0;
        $this->jobs_to_create_12_months = 0;
        $this->number_women_shareholders = 0;
        $this->number_youth_shareholders = 0;
        $this->industry_other_elaboration = '';
        $this->resetErrorBag();
    }
    
    public function openApplicationModal()
    {
        $this->resetForm();
        $this->dispatch('open-application-modal');
    }
    
    public function closeApplicationModal()
    {
        $this->resetForm();
        $this->dispatch('close-application-modal');
    }
}
?>

<div>
    <div class="modal fade" id="incubationApplicationModal" tabindex="-1" aria-labelledby="incubationApplicationModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="fw-bold mb-0">
                            @if($isEditMode)
                                Edit Incubation Application
                            @else
                                Apply for Incubation Program
                            @endif
                        </h5>
                        <small class="text-muted">
                            @if($isEditMode)
                                Update your application details for the {{ $call->title ?? 'Incubation' }} program
                            @else
                                Fill in your details to apply for the {{ $call->title ?? 'Incubation' }} program
                            @endif
                        </small>
                    </div>
                    <button type="button" class="btn-close" wire:click="closeApplicationModal" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                    <form wire:submit.prevent="submitApplication">
                        <!-- Company Information Section -->
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3 bg-light p-2 rounded">Company Information</h6>
                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-medium small">Company Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('company_name') is-invalid @enderror" wire:model="company_name">
                                    @error('company_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-medium small">Registered Company Name</label>
                                    <input type="text" class="form-control @error('registered_company_name') is-invalid @enderror" wire:model="registered_company_name">
                                    @error('registered_company_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-medium small">Business Description <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('business_description') is-invalid @enderror" rows="3" wire:model="business_description" placeholder="Describe your business, products, and services..."></textarea>
                                    @error('business_description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-medium small">Business Model</label>
                                    <select class="form-select @error('business_model') is-invalid @enderror" wire:model="business_model">
                                        <option value="">Select business model</option>
                                        <option>B2B</option>
                                        <option>B2C</option>
                                        <option>B2B2C</option>
                                        <option>Marketplace</option>
                                        <option>Subscription</option>
                                        <option>Freemium</option>
                                        <option>E-commerce</option>
                                    </select>
                                    @error('business_model') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-medium small">Offering</label>
                                    <select class="form-select @error('offering') is-invalid @enderror" wire:model="offering">
                                        <option value="">Select offering type</option>
                                        <option>Product</option>
                                        <option>Service</option>
                                        <option>Both Product & Service</option>
                                        <option>Platform</option>
                                        <option>Solution</option>
                                    </select>
                                    @error('offering') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-medium small">Revenue Model</label>
                                    <select class="form-select @error('revenue_model') is-invalid @enderror" wire:model="revenue_model">
                                        <option value="">Select revenue model</option>
                                        <option>Direct Sales</option>
                                        <option>Subscription</option>
                                        <option>Freemium</option>
                                        <option>Commission</option>
                                        <option>Advertising</option>
                                        <option>Licensing</option>
                                    </select>
                                    @error('revenue_model') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-medium small">Website</label>
                                    <input type="url" class="form-control @error('website') is-invalid @enderror" wire:model="website" placeholder="https://yourcompany.com">
                                    @error('website') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Company Classification -->
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3 bg-light p-2 rounded">Company Classification</h6>
                            <div class="row g-3">
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-medium small">Company Type</label>
                                    <select class="form-select @error('company_type') is-invalid @enderror" wire:model="company_type">
                                        <option value="">Select type</option>
                                        <option>Sole Proprietorship</option>
                                        <option>Partnership</option>
                                        <option>Private Limited Company</option>
                                        <option>Public Limited Company</option>
                                        <option>Non-Profit Organization</option>
                                        <option>Cooperative</option>
                                    </select>
                                    @error('company_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-medium small">Industry</label>
                                    <select class="form-select @error('industry') is-invalid @enderror" wire:model="industry">
                                        <option value="">Select industry</option>
                                        <option>Agriculture</option>
                                        <option>Technology</option>
                                        <option>Manufacturing</option>
                                        <option>Retail & Trade</option>
                                        <option>Textile & Garments</option>
                                        <option>Food & Beverage</option>
                                        <option>Health & Wellness</option>
                                        <option>Education</option>
                                        <option>Finance & Fintech</option>
                                        <option>Construction</option>
                                        <option>Tourism & Hospitality</option>
                                        <option>Transport & Logistics</option>
                                        <option>Other</option>
                                    </select>
                                    @error('industry') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-medium small">Sector</label>
                                    <select class="form-select @error('sector') is-invalid @enderror" wire:model="sector">
                                        <option value="">Select sector</option>
                                        <option>Primary</option>
                                        <option>Secondary</option>
                                        <option>Tertiary</option>
                                        <option>Quaternary</option>
                                    </select>
                                    @error('sector') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-medium small">Country</label>
                                    <select class="form-select @error('country') is-invalid @enderror" wire:model="country">
                                        <option value="Lesotho">Lesotho</option>
                                        <option>South Africa</option>
                                        <option>Botswana</option>
                                        <option>Eswatini</option>
                                        <option>Namibia</option>
                                        <option>Zimbabwe</option>
                                        <option>Other</option>
                                    </select>
                                    @error('country') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-medium small">District</label>
                                    <select class="form-select @error('district') is-invalid @enderror" wire:model="district">
                                        <option value="">Select district</option>
                                        <option>Maseru</option>
                                        <option>Leribe</option>
                                        <option>Berea</option>
                                        <option>Mafeteng</option>
                                        <option>Mohale's Hoek</option>
                                        <option>Quthing</option>
                                        <option>Qacha's Nek</option>
                                        <option>Mokhotlong</option>
                                        <option>Butha-Buthe</option>
                                        <option>Thaba-Tseka</option>
                                    </select>
                                    @error('district') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-medium small">Year of Establishment</label>
                                    <select class="form-select @error('year_of_establishment') is-invalid @enderror" wire:model="year_of_establishment">
                                        <option value="">Select year</option>
                                        @for($year = date('Y'); $year >= 1980; $year--)
                                            <option value="{{ $year }}">{{ $year }}</option>
                                        @endfor
                                    </select>
                                    @error('year_of_establishment') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-medium small">Company Stage</label>
                                    <select class="form-select @error('company_stage') is-invalid @enderror" wire:model="company_stage">
                                        <option value="">Select stage</option>
                                        <option>Idea Stage</option>
                                        <option>Startup</option>
                                        <option>Growth</option>
                                        <option>Expansion</option>
                                        <option>Mature</option>
                                    </select>
                                    @error('company_stage') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-medium small">Company Size</label>
                                    <select class="form-select @error('company_size') is-invalid @enderror" wire:model="company_size">
                                        <option value="">Select size</option>
                                        <option>1-10 employees</option>
                                        <option>11-50 employees</option>
                                        <option>51-200 employees</option>
                                        <option>201-500 employees</option>
                                        <option>500+ employees</option>
                                    </select>
                                    @error('company_size') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Social Media -->
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3 bg-light p-2 rounded">Social Media Profiles</h6>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-medium small">Social Media</label>
                                    <div class="border rounded p-3">
                                        <div class="row g-2">
                                            <div class="col-12 col-md-6">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text"><i class="bi bi-facebook"></i></span>
                                                    <input type="text" class="form-control @error('social_media_facebook') is-invalid @enderror" wire:model="social_media_facebook" placeholder="Facebook URL">
                                                </div>
                                                @error('social_media_facebook') <div class="text-danger small">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text"><i class="bi bi-twitter-x"></i></span>
                                                    <input type="text" class="form-control @error('social_media_twitter') is-invalid @enderror" wire:model="social_media_twitter" placeholder="Twitter/X URL">
                                                </div>
                                                @error('social_media_twitter') <div class="text-danger small">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text"><i class="bi bi-linkedin"></i></span>
                                                    <input type="text" class="form-control @error('social_media_linkedin') is-invalid @enderror" wire:model="social_media_linkedin" placeholder="LinkedIn URL">
                                                </div>
                                                @error('social_media_linkedin') <div class="text-danger small">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text"><i class="bi bi-instagram"></i></span>
                                                    <input type="text" class="form-control @error('social_media_instagram') is-invalid @enderror" wire:model="social_media_instagram" placeholder="Instagram URL">
                                                </div>
                                                @error('social_media_instagram') <div class="text-danger small">{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Applicant Information -->
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3 bg-light p-2 rounded">Applicant Information</h6>
                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-medium small">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('applicant_name') is-invalid @enderror" wire:model="applicant_name">
                                    @error('applicant_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-medium small">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('applicant_email') is-invalid @enderror" wire:model="applicant_email">
                                    @error('applicant_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-medium small">Title/Position</label>
                                    <input type="text" class="form-control @error('applicant_title') is-invalid @enderror" wire:model="applicant_title">
                                    @error('applicant_title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-medium small">Gender</label>
                                    <select class="form-select @error('applicant_gender') is-invalid @enderror" wire:model="applicant_gender">
                                        <option value="">Select gender</option>
                                        <option>Male</option>
                                        <option>Female</option>
                                        <option>Non-binary</option>
                                        <option>Prefer not to say</option>
                                    </select>
                                    @error('applicant_gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-medium small">Nationality</label>
                                    <select class="form-select @error('applicant_nationality') is-invalid @enderror" wire:model="applicant_nationality">
                                        <option value="Lesotho">Lesotho</option>
                                        <option>South Africa</option>
                                        <option>Other</option>
                                    </select>
                                    @error('applicant_nationality') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-medium small">Contact Number <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control @error('applicant_contact_number') is-invalid @enderror" wire:model="applicant_contact_number" placeholder="+266 XXXX XXXX">
                                    @error('applicant_contact_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-medium small">About You</label>
                                    <textarea class="form-control @error('applicant_about') is-invalid @enderror" rows="2" wire:model="applicant_about" placeholder="Brief background about yourself and your role in the company..."></textarea>
                                    @error('applicant_about') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-medium small">Applicant Social Media</label>
                                    <div class="border rounded p-3">
                                        <div class="row g-2">
                                            <div class="col-12 col-md-6">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text"><i class="bi bi-twitter-x"></i></span>
                                                    <input type="text" class="form-control @error('applicant_twitter') is-invalid @enderror" wire:model="applicant_twitter" placeholder="Twitter/X URL">
                                                </div>
                                                @error('applicant_twitter') <div class="text-danger small">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text"><i class="bi bi-linkedin"></i></span>
                                                    <input type="text" class="form-control @error('applicant_linkedin') is-invalid @enderror" wire:model="applicant_linkedin" placeholder="LinkedIn URL">
                                                </div>
                                                @error('applicant_linkedin') <div class="text-danger small">{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Financial & Support Questions -->
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3 bg-light p-2 rounded">Financial & Support Information</h6>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-medium small">Have you received any financial support or funding before?</label>
                                    <select class="form-select @error('received_financial_support') is-invalid @enderror" wire:model="received_financial_support">
                                        <option value="">Select option</option>
                                        <option>Yes</option>
                                        <option>No</option>
                                    </select>
                                    @error('received_financial_support') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-medium small">Have you participated in any business competitions, incubators, or accelerators before?</label>
                                    <select class="form-select @error('participated_competitions') is-invalid @enderror" wire:model="participated_competitions">
                                        <option value="">Select option</option>
                                        <option>Yes</option>
                                        <option>No</option>
                                    </select>
                                    @error('participated_competitions') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-medium small">Are you willing to commit the necessary time and resources to participate fully in the program? <span class="text-danger">*</span></label>
                                    <select class="form-select @error('willing_to_commit') is-invalid @enderror" wire:model="willing_to_commit">
                                        <option value="">Select option</option>
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                    @error('willing_to_commit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Numeric Information -->
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3 bg-light p-2 rounded">Business Metrics</h6>
                            <div class="row g-3">
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-medium small">Number of Shareholders</label>
                                    <input type="number" class="form-control @error('number_of_shareholders') is-invalid @enderror" wire:model="number_of_shareholders">
                                    @error('number_of_shareholders') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-medium small">Number of Women Shareholders</label>
                                    <input type="number" class="form-control @error('number_women_shareholders') is-invalid @enderror" wire:model="number_women_shareholders">
                                    @error('number_women_shareholders') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-medium small">Number of Youth Shareholders</label>
                                    <input type="number" class="form-control @error('number_youth_shareholders') is-invalid @enderror" wire:model="number_youth_shareholders">
                                    @error('number_youth_shareholders') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-medium small">How many customers/clients do you serve currently?</label>
                                    <input type="number" class="form-control @error('number_of_customers') is-invalid @enderror" wire:model="number_of_customers">
                                    @error('number_of_customers') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-medium small">Average monthly sales (in Maloti)</label>
                                    <input type="number" step="0.01" class="form-control @error('average_monthly_sales') is-invalid @enderror" wire:model="average_monthly_sales">
                                    @error('average_monthly_sales') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-medium small">How many jobs do you anticipate creating within 12 months?</label>
                                    <input type="number" class="form-control @error('jobs_to_create_12_months') is-invalid @enderror" wire:model="jobs_to_create_12_months">
                                    @error('jobs_to_create_12_months') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3 bg-light p-2 rounded">Additional Information</h6>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-medium small">If Industry is "Other", please elaborate</label>
                                    <textarea class="form-control @error('industry_other_elaboration') is-invalid @enderror" rows="2" wire:model="industry_other_elaboration" placeholder="Please specify your industry if you selected 'Other'..."></textarea>
                                    @error('industry_other_elaboration') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeApplicationModal" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" wire:click="submitApplication" wire:loading.attr="disabled">
                            <span wire:loading.remove>
                                @if($isEditMode)
                                    Update Application
                                @else
                                    Submit Application
                                @endif
                            </span>
                            <span wire:loading>
                                <span class="spinner-border spinner-border-sm me-1"></span> 
                                @if($isEditMode) Updating... @else Submitting... @endif
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
  <script>
    document.addEventListener('livewire:initialized', () => {
        // Listen for open application modal event
        Livewire.on('open-application-modal', () => {
            var modal = new bootstrap.Modal(document.getElementById('incubationApplicationModal'));
            modal.show();
        });
        
        // Listen for close application modal event
        Livewire.on('close-application-modal', () => {
            var modalElement = document.getElementById('incubationApplicationModal');
            var modal = bootstrap.Modal.getInstance(modalElement);
            if (modal) {
                modal.hide();
            }
            
            // Clean up modal backdrop and restore scrolling
            setTimeout(() => {
                const backdrops = document.querySelectorAll('.modal-backdrop');
                backdrops.forEach(backdrop => backdrop.remove());
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
            }, 150);
        });
    });
</script>
</div>