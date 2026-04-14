<?php

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use App\Models\User;
use App\Models\Entrepreneur;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    use WithFileUploads;
    
    public $userId;
    public $user;
    public $entrepreneur;
    
    // Personal Information
    public $first_name = '';
    public $surname = '';
    public $gender = '';
    public $date_of_birth = '';
    public $profile_image = null;
    public $existing_profile_photo = null;
    
    // Contact Information
    public $email = '';
    public $phone = '';
    
    // User Bio (Personal)
    public $user_bio = '';
    
    // Business Information
    public $country = 'Lesotho';
    public $area_of_operation = '';
    public $industry_or_interest = '';
    public $years_of_operation = '';
    public $organization_name = '';
    public $short_bio = ''; // Company bio
    
    // Documents
    public $tax_clearance = null;
    public $traders_license = null;
    public $existing_tax_clearance = null;
    public $existing_traders_license = null;
    
    public $showModal = false;
    
    public array $industries = [
        'Agriculture', 'Technology', 'Manufacturing', 'Banking', 'Education',
        'Healthcare', 'Retail', 'Construction', 'Tourism', 'Transportation',
        'Energy', 'Mining', 'Financial Services', 'Real Estate', 'Creative Arts',
        'Food & Beverage', 'Consulting', 'Other'
    ];
    
    public array $countries = ['Lesotho', 'South Africa', 'Botswana', 'Eswatini', 'Namibia', 
        'Zimbabwe', 'Mozambique', 'Zambia', 'Malawi', 'Angola', 'Tanzania', 'Kenya'];
    
    #[On('openEditRegistrationModal')]
    public function openEditRegistrationModal($userId)
    {
        $this->userId = $userId;
        $this->loadUserData();
        $this->showModal = true;
    }
    
    public function loadUserData()
    {
        $this->user = User::with('userable')->findOrFail($this->userId);
        
        if ($this->user->isEntrepreneur()) {
            $this->entrepreneur = $this->user->userable;
            
            // Load personal data
            $this->first_name = $this->entrepreneur->first_name ?? '';
            $this->surname = $this->entrepreneur->surname ?? '';
            $this->gender = $this->entrepreneur->gender ?? '';
            $this->date_of_birth = $this->entrepreneur->date_of_birth ? $this->entrepreneur->date_of_birth->format('Y-m-d') : '';
            $this->existing_profile_photo = $this->user->profile_photo;
            
            // Load contact information
            $this->email = $this->user->email ?? '';
            $this->phone = $this->user->phone ?? '';
            
            // Load user personal bio
            $this->user_bio = $this->user->bio ?? '';
            
            // Load business data
            $this->country = $this->entrepreneur->country ?? 'Lesotho';
            $this->area_of_operation = $this->entrepreneur->area_of_operation ?? '';
            $this->industry_or_interest = $this->entrepreneur->industry_or_interest ?? '';
            $this->years_of_operation = $this->entrepreneur->years_of_operation ?? '';
            $this->organization_name = $this->entrepreneur->organization_name ?? '';
            $this->short_bio = $this->entrepreneur->short_bio ?? '';
            
            // Load document paths
            $this->existing_tax_clearance = $this->entrepreneur->tax_clearance_path;
            $this->existing_traders_license = $this->entrepreneur->traders_license_path;
        }
    }
    
    public function closeModal()
    {
        $this->showModal = false;
        $this->resetErrorBag();
        $this->reset(['first_name', 'surname', 'gender', 'date_of_birth', 'profile_image',
            'email', 'phone', 'user_bio', 'country', 'area_of_operation', 'industry_or_interest', 
            'years_of_operation', 'organization_name', 'short_bio', 'tax_clearance', 'traders_license']);
    }
    
    public function updateRegistration()
    {
        $this->validate([
            'first_name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'gender' => 'required',
            'date_of_birth' => 'required|date',
            'email' => 'required|email|unique:users,email,' . $this->userId,
            'phone' => 'required|string|max:20',
            'user_bio' => 'nullable|string|max:1000',
            'country' => 'required',
            'area_of_operation' => 'required|string|max:255',
            'industry_or_interest' => 'required',
            'short_bio' => 'nullable|string|max:1000',
            'profile_image' => 'nullable|image|max:2048',
            'tax_clearance' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'traders_license' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);
        
        try {
            // Handle profile image upload
            if ($this->profile_image) {
                if ($this->existing_profile_photo && Storage::disk('public')->exists($this->existing_profile_photo)) {
                    Storage::disk('public')->delete($this->existing_profile_photo);
                }
                $profileImagePath = $this->profile_image->store('profile-images', 'public');
                $this->user->profile_photo = $profileImagePath;
            }
            
            // Update user contact information and personal bio
            $this->user->email = $this->email;
            $this->user->phone = $this->phone;
            $this->user->bio = $this->user_bio;
            $this->user->save();
            
            // Handle tax clearance upload
            if ($this->tax_clearance) {
                if ($this->existing_tax_clearance && Storage::disk('public')->exists($this->existing_tax_clearance)) {
                    Storage::disk('public')->delete($this->existing_tax_clearance);
                }
                $taxPath = $this->tax_clearance->store('documents/tax-clearance', 'public');
                $this->entrepreneur->tax_clearance_path = $taxPath;
            }
            
            // Handle traders license upload
            if ($this->traders_license) {
                if ($this->existing_traders_license && Storage::disk('public')->exists($this->existing_traders_license)) {
                    Storage::disk('public')->delete($this->existing_traders_license);
                }
                $licensePath = $this->traders_license->store('documents/traders-license', 'public');
                $this->entrepreneur->traders_license_path = $licensePath;
            }
            
            // Update entrepreneur
            $this->entrepreneur->update([
                'first_name' => $this->first_name,
                'surname' => $this->surname,
                'gender' => $this->gender,
                'date_of_birth' => $this->date_of_birth,
                'country' => $this->country,
                'area_of_operation' => $this->area_of_operation,
                'industry_or_interest' => $this->industry_or_interest,
                'years_of_operation' => $this->years_of_operation ? (int)$this->years_of_operation : null,
                'organization_name' => $this->organization_name,
                'short_bio' => $this->short_bio,
            ]);
            
            $this->dispatch('notify', type: 'success', message: 'Profile updated successfully!');
            $this->dispatch('user-updated');
            $this->closeModal();
            
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Update failed: ' . $e->getMessage());
        }
    }
};

?>

<div>
    <!-- Registration Edit Modal -->
    @if($showModal)
    <div class="modal fade show d-block" tabindex="-1" aria-hidden="true" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <div>
                        <div class="text-success fw-semibold small mb-1">Edit Profile</div>
                        <h5 class="modal-title fw-bold">Edit Entrepreneur Registration</h5>
                        <p class="text-muted small mb-0">Update your personal and business information</p>
                    </div>
                    <button type="button" class="btn-close" wire:click="closeModal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4" style="max-height: 70vh; overflow-y: auto;">
                    <form wire:submit.prevent="updateRegistration">
                        <div class="row g-3">
                            <!-- Personal Information -->
                            <div class="col-12">
                                <h6 class="fw-bold mb-3">Personal Information</h6>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">First Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('first_name') is-invalid @enderror" wire:model="first_name">
                                @error('first_name') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Surname <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('surname') is-invalid @enderror" wire:model="surname">
                                @error('surname') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Gender <span class="text-danger">*</span></label>
                                <select class="form-select @error('gender') is-invalid @enderror" wire:model="gender">
                                    <option value="">Select Gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                                @error('gender') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Date of Birth <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" wire:model="date_of_birth">
                                @error('date_of_birth') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Personal Bio</label>
                                <textarea class="form-control @error('user_bio') is-invalid @enderror" wire:model="user_bio" rows="3" placeholder="Tell us about yourself..."></textarea>
                                <small class="text-muted">Max 1000 characters. Current: {{ strlen($user_bio) }}/1000</small>
                                @error('user_bio') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Profile Image</label>
                                <input type="file" class="form-control @error('profile_image') is-invalid @enderror" wire:model="profile_image" accept="image/*">
                                <small class="text-muted">Max size: 2MB. Accepted: JPG, PNG, GIF</small>
                                @error('profile_image') <span class="text-danger small">{{ $message }}</span> @enderror
                                
                                @if($existing_profile_photo && !$profile_image)
                                    <div class="mt-2">
                                        <img src="{{ Storage::url($existing_profile_photo) }}" class="rounded" style="width: 60px; height: 60px; object-fit: cover;">
                                    </div>
                                @endif
                                
                                @if($profile_image)
                                    <div class="mt-2">
                                        <img src="{{ $profile_image->temporaryUrl() }}" class="rounded" style="width: 60px; height: 60px; object-fit: cover;">
                                    </div>
                                @endif
                            </div>

                            <!-- Business Information -->
                            <div class="col-12 mt-3">
                                <h6 class="fw-bold mb-3">Business Information</h6>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Country <span class="text-danger">*</span></label>
                                <select class="form-select @error('country') is-invalid @enderror" wire:model="country">
                                    @foreach($countries as $countryOption)
                                        <option value="{{ $countryOption }}">{{ $countryOption }}</option>
                                    @endforeach
                                </select>
                                @error('country') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Area of Operation <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('area_of_operation') is-invalid @enderror" wire:model="area_of_operation" placeholder="City/District/Region">
                                @error('area_of_operation') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Industry or Area of Interest <span class="text-danger">*</span></label>
                                <select class="form-select @error('industry_or_interest') is-invalid @enderror" wire:model="industry_or_interest">
                                    <option value="">Select Industry</option>
                                    @foreach($industries as $industry)
                                        <option value="{{ $industry }}">{{ $industry }}</option>
                                    @endforeach
                                </select>
                                @error('industry_or_interest') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Years of Operation</label>
                                <input type="number" class="form-control" wire:model="years_of_operation" min="0" max="100" placeholder="e.g., 5">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Organization/Company Name</label>
                                <input type="text" class="form-control" wire:model="organization_name" placeholder="Your Business Name">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Short Profile or Business Description</label>
                                <textarea class="form-control" wire:model="short_bio" rows="3" placeholder="Tell us about yourself and your business..."></textarea>
                                <small class="text-muted">Max 1000 characters. Current: {{ strlen($short_bio) }}/1000</small>
                            </div>

                            <!-- Required Documents -->
                            <div class="col-12 mt-3">
                                <h6 class="fw-bold mb-3">Required Documents</h6>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Tax Clearance Certificate</label>
                                <input type="file" class="form-control @error('tax_clearance') is-invalid @enderror" wire:model="tax_clearance" accept=".pdf,.jpg,.jpeg,.png">
                                <small class="text-muted">Accepted: PDF, JPG, PNG (Max 5MB)</small>
                                @error('tax_clearance') <span class="text-danger small">{{ $message }}</span> @enderror
                                
                                @if($existing_tax_clearance && !$tax_clearance)
                                    <div class="mt-1">
                                        <small class="text-success">✓ Current file: {{ basename($existing_tax_clearance) }}</small>
                                    </div>
                                @endif
                                
                                @if($tax_clearance)
                                    <div class="mt-1">
                                        <small class="text-info">New file selected: {{ $tax_clearance->getClientOriginalName() }}</small>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Trader's License</label>
                                <input type="file" class="form-control @error('traders_license') is-invalid @enderror" wire:model="traders_license" accept=".pdf,.jpg,.jpeg,.png">
                                <small class="text-muted">Accepted: PDF, JPG, PNG (Max 5MB)</small>
                                @error('traders_license') <span class="text-danger small">{{ $message }}</span> @enderror
                                
                                @if($existing_traders_license && !$traders_license)
                                    <div class="mt-1">
                                        <small class="text-success">✓ Current file: {{ basename($existing_traders_license) }}</small>
                                    </div>
                                @endif
                                
                                @if($traders_license)
                                    <div class="mt-1">
                                        <small class="text-info">New file selected: {{ $traders_license->getClientOriginalName() }}</small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary" wire:click="closeModal">Cancel</button>
                    <button type="button" class="btn btn-success" wire:click="updateRegistration" wire:loading.attr="disabled">
                        <span wire:loading.remove>Save Changes</span>
                        <span wire:loading>Saving...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>