<?php

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Request;
use Livewire\Attributes\On;

new class extends Component {
    public $user;
    public $activeTab = 'profile-details';
    public $userId;
    
    // Mount method to receive user data
    public function mount($id = null)
    {
        if (!$id) {
            $id = request()->segment(3);
        }
        
        $this->userId = $id;
        
        if ($id) {
            $this->user = User::with('roles', 'userable')->findOrFail($id);
        } 
    }

    #[On('user-updated')]
    public function refreshUserData()
    {
        // Refresh the user data when user-updated event is received
        if ($this->userId) {
            $this->user = User::with('roles', 'userable')->findOrFail($this->userId);
        }
    }
    
    public function getRoleNameProperty()
    {
        return $this->user->roles->first()?->name ?? 'No Role Assigned';
    }
    
    public function getRoleDescriptionProperty()
    {
        $role = $this->user->roles->first();
        if ($role) {
            $descriptions = [
                'Super Administrator' => 'Full system access to all modules, settings, and user data',
                'Administrator' => 'Administrative access with limited system configuration',
                'ESO' => 'Access to manage incubation calls and applications',
                'Entrepreneur' => 'Access to apply for incubation programs',
            ];
            return $descriptions[$role->name] ?? 'Custom role with specific permissions';
        }
        return 'No role assigned to this user';
    }

    public function openEditUserModal()
    {
        $this->dispatch('openEditUserModal', userId: $this->user->id);
    }
    
    public function getKeyPermissionsProperty()
    {
        $role = $this->user->roles->first();
        if ($role) {
            $permissions = $role->permissions->pluck('name')->toArray();
            return array_slice($permissions, 0, 6);
        }
        return ['No permissions assigned'];
    }
    
    public function getTotalPermissionsCountProperty()
    {
        $role = $this->user->roles->first();
        return $role ? $role->permissions->count() : 0;
    }
    
    public function getInitialsProperty()
    {
        $name = $this->user->display_name;
        $words = explode(' ', trim($name));
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }
        return strtoupper(substr($name, 0, 2));
    }
    
    public function getApplicationsCountProperty()
    {
        return \App\Models\IncubationApplication::where('user_id', $this->user->id)->count();
    }
    
    public function getApprovalsCountProperty()
    {
        return \App\Models\IncubationApplication::where('user_id', $this->user->id)
            ->where('status', 'approved')
            ->count();
    }
    
    public function getReportsCountProperty()
    {
        return 0;
    }
    
    public function getLoginDaysCountProperty()
    {
        if ($this->user->created_at) {
            return (int) $this->user->created_at->diffInDays(now());
        }
        return 0;
    }
    
    public function getFormattedLastActiveProperty()
    {
        if ($this->user->last_login_at) {
            return $this->user->last_login_at->format('d M Y, h:i A');
        }
        return 'Never logged in';
    }
    
    public function getFormattedJoinedDateProperty()
    {
        if ($this->user->created_at) {
            return $this->user->created_at->format('d M Y');
        }
        return 'Unknown';
    }
    
    public function getFormattedDateOfBirthProperty()
    {
        if ($this->user->userable && $this->user->isEntrepreneur()) {
            return $this->user->userable->date_of_birth ? $this->user->userable->date_of_birth->format('d M Y') : 'Not provided';
        }
        return 'Not provided';
    }
    
    public function getFormattedGenderProperty()
    {
        if ($this->user->userable && $this->user->isEntrepreneur()) {
            return ucfirst($this->user->userable->gender ?? 'Not provided');
        }
        return 'Not provided';
    }
    
    public function getPersonalBioProperty()
    {
        return $this->user->bio ?? 'No personal bio provided';
    }
    
    public function getCompanyBioProperty()
    {
        if ($this->user->userable && $this->user->isEntrepreneur()) {
            return $this->user->userable->short_bio ?? 'No company bio provided';
        }
        return 'Not applicable';
    }
    
    public function getOrganisationNameProperty()
    {
        if ($this->user->userable) {
            if ($this->user->isEntrepreneur()) {
                return $this->user->userable->organization_name ?? 'Individual Entrepreneur';
            }
            if ($this->user->isESO()) {
                return $this->user->userable->organisation_name ?? 'Not specified';
            }
        }
        return 'Not specified';
    }
    
    public function getDepartmentProperty()
    {
        if ($this->user->userable && $this->user->isESO()) {
            return $this->user->userable->department ?? 'Not specified';
        }
        return 'Not specified';
    }
    
    public function getJobTitleProperty()
    {
        if ($this->user->userable && $this->user->isEntrepreneur()) {
            return $this->user->userable->position ?? 'Entrepreneur';
        }
        if ($this->user->userable && $this->user->isESO()) {
            return $this->user->userable->job_title ?? 'Staff Member';
        }
        return 'Not specified';
    }
    
    public function getLocationProperty()
    {
        if ($this->user->userable) {
            $city = $this->user->userable->area_of_operation ?? '';
            $country = $this->user->userable->country ?? '';
            if ($city && $country) {
                return $city . ', ' . $country;
            }
            return $city ?: $country ?: 'Not specified';
        }
        return 'Not specified';
    }
    
    public function getUserIdProperty()
    {
        return '#USR-' . str_pad($this->user->id, 5, '0', STR_PAD_LEFT);
    }
    
    public function getFormattedAccountCreatedProperty()
    {
        if ($this->user->created_at) {
            return $this->user->created_at->format('M d, Y');
        }
        return 'Unknown';
    }
    
    public function getApprovedByProperty()
    {
        if ($this->user->email_verified_at) {
            return 'System · Auto-approved';
        }
        return 'Pending verification';
    }
    
    public function getStreetAddressProperty()
    {
        if ($this->user->userable && $this->user->userable->address) {
            return $this->user->userable->address;
        }
        return 'Not provided';
    }
    
    public function getCityProperty()
    {
        if ($this->user->userable && $this->user->userable->area_of_operation) {
            return $this->user->userable->area_of_operation;
        }
        return 'Not provided';
    }

    public function getOrganizationNameProperty()
    {
        if ($this->user->userable && $this->user->isEntrepreneur()) {
            return $this->user->userable->organization_name ?? 'Not provided';
        }
        return 'Not applicable';
    }

    public function getIndustryOrInterestProperty()
    {
        if ($this->user->userable && $this->user->isEntrepreneur()) {
            return $this->user->userable->industry_or_interest ?? 'Not specified';
        }
        return 'Not specified';
    }

    public function getYearsOfOperationProperty()
    {
        if ($this->user->userable && $this->user->isEntrepreneur()) {
            $years = $this->user->userable->years_of_operation;
            return $years ? $years . ' years' : 'Not specified';
        }
        return 'Not specified';
    }

    public function getAreaOfOperationProperty()
    {
        if ($this->user->userable && $this->user->isEntrepreneur()) {
            return $this->user->userable->area_of_operation ?? 'Not specified';
        }
        return 'Not specified';
    }

    public function getCountryProperty()
    {
        if ($this->user->userable) {
            return $this->user->userable->country ?? 'Not specified';
        }
        return 'Not specified';
    }

    public function getTaxClearancePathProperty()
    {
        if ($this->user->userable && $this->user->isEntrepreneur()) {
            return $this->user->userable->tax_clearance_path ?? null;
        }
        return null;
    }

    public function getTradersLicensePathProperty()
    {
        if ($this->user->userable && $this->user->isEntrepreneur()) {
            return $this->user->userable->traders_license_path ?? null;
        }
        return null;
    }

    public function getShortBioProperty()
    {
        if ($this->user->userable && $this->user->isEntrepreneur()) {
            return $this->user->userable->short_bio ?? 'No bio provided';
        }
        return $this->user->bio ?? 'No bio provided';
    }

    #[On('confirmDeleteUser')]
    public function confirmDeleteUser($userId)
    {
        $user = User::findOrFail($userId);
        if (auth()->user()->id === $userId) {
            $this->dispatch('notify', type: 'error', message: 'You cannot delete your own account');
            return;
        }
        
        $applicationsCount = \App\Models\IncubationApplication::where('user_id', $userId)->count();
        
        if ($applicationsCount > 0) {
            $this->dispatch('notify', type: 'warning', message: "This user has {$applicationsCount} applications. Please handle them before deleting.");
            return;
        }
        
        $userName = $user->display_name;
        $user->delete();
        
        $this->dispatch('notify', type: 'success', message: "User '{$userName}' has been deleted successfully");
        $this->dispatch('user-deleted');
    
        return redirect()->route('users.index');
    }
    
    public function getProvinceProperty()
    {
        if ($this->user->userable && $this->user->userable->province) {
            return $this->user->userable->province;
        }
        return 'Not provided';
    }
    
    public function getPostalCodeProperty()
    {
        if ($this->user->userable && $this->user->userable->postal_code) {
            return $this->user->userable->postal_code;
        }
        return 'Not provided';
    }
    
    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }
};

?>

<div>
    <div class="container-fluid py-4 px-3 px-md-4" style="max-width: 1300px;">

        <!-- ── Breadcrumb ── -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb small mb-0">
                <li class="breadcrumb-item">
                    <a href="#" class="text-decoration-none text-muted">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('users.index') }}" class="text-decoration-none text-muted">
                        User Management
                    </a>
                </li>
                <li class="breadcrumb-item active text-dark fw-semibold">
                    {{ $this->user->display_name }}
                </li>
            </ol>
        </nav>

        <div class="row g-4">

            <!-- LEFT COLUMN – Profile Card -->
            <div class="col-12 col-lg-4 col-xl-3 pb-4">

                <!-- Profile Hero Card -->
                <div class="card border-0 shadow-sm overflow-hidden">
                    <div class="position-relative mb-3" style="height:50px; margin-top: 20px;">
    
                        @if($this->user->profile_photo_url)
                            <a href="{{ $this->user->profile_photo_url }}" target="_blank">
                                <img 
                                    src="{{ $this->user->profile_photo_url }}" 
                                    class="profile-avatar rounded-circle"
                                    style="width:100px;height:100px;object-fit:cover;position:absolute;bottom:-30px;left:50%;transform:translateX(-50%);border:3px solid white;box-shadow:0 2px 4px rgba(0,0,0,0.1);cursor:pointer;"
                                    alt="{{ $this->user->display_name }}">
                            </a>
                        @else
                            <div class="profile-avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                style="position:absolute;bottom:-30px;left:50%;transform:translateX(-50%);width:100px;height:100px;">
                                {{ $this->initials }}
                            </div>
                        @endif

                    </div>

                    <div class="card-body pt-4 mt-2 px-4 pb-3">
                        <div class="d-flex align-items-start justify-content-center flex-wrap gap-2">
                            <div>
                                <h5 class="fw-bold  mb-0">{{ $this->user->display_name }}</h5>
                                <p class="text-muted small mb-0">{{ $this->user->email }}</p>
                            </div>
                            <span class="badge {{ $this->user->is_active && !$this->user->isSuspended() ? 'badge-active' : 'badge-inactive' }} rounded-pill px-2 py-1 small mt-1">
                                <i class="bi bi-circle-fill me-1" style="font-size:.4rem;"></i>
                                {{ $this->user->isSuspended() ? 'Suspended' : ($this->user->is_active ? 'Active' : 'Inactive') }}
                            </span>
                        </div>

                        <div class="mt-3 justify-content-center d-flex flex-wrap gap-2">
                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2 py-1 small fw-semibold">
                                <i class="bi bi-shield-fill me-1"></i>{{ $this->roleName }}
                            </span>
                        </div>

                        <hr class="my-3">

                        <div class="d-flex flex-column gap-2">
                            <div class="d-flex align-items-center gap-2 text-muted small">
                                <i class="bi bi-building text-primary" style="width:16px;"></i>
                                {{ $this->organisationName }}
                            </div>
                            <div class="d-flex align-items-center gap-2 text-muted small">
                                <i class="bi bi-geo-alt-fill text-primary" style="width:16px;"></i>
                                {{ $this->location }}
                            </div>
                            <div class="d-flex align-items-center gap-2 text-muted small">
                                <i class="bi bi-calendar3 text-primary" style="width:16px;"></i>
                                Joined {{ $this->formattedJoinedDate }}
                            </div>
                            <div class="d-flex align-items-center gap-2 text-muted small">
                                <i class="bi bi-clock-fill text-primary" style="width:16px;"></i>
                                Last active: {{ $this->formattedLastActive }}
                            </div>
                        </div>

                        <hr class="my-3">

                        <div class="d-grid gap-2">
                            <button class="btn btn-primary btn-sm action-btn d-flex align-items-center justify-content-center gap-2"
                                    wire:click="$dispatch('openEditRegistrationModal', { userId: {{ $this->user->id }} })">
                                <i class="bi bi-pencil-fill"></i> Edit Profile
                            </button>
                            @role('Super Administrator')
                            <button class="btn btn-outline-secondary btn-sm action-btn d-flex align-items-center justify-content-center gap-2"
                                    wire:click="$dispatch('confirmChangeRole', { userId: {{ $this->user->id }} })">
                                <i class="bi bi-shield-fill"></i> Change Role
                            </button>
                            @endrole
                            <a href="{{ route('profile.edit') }}" 
                            class="btn btn-outline-warning btn-sm action-btn d-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-lock-fill"></i> Reset Password
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Assigned Role Card -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3 px-4">
                        <h6 class="fw-bold mb-0 small"><i class="bi bi-shield-lock-fill text-primary me-2"></i>Assigned Role</h6>
                    </div>
                    <div class="card-body px-4 py-3">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="stat-icon bg-primary bg-opacity-10 text-primary rounded-3">
                                <i class="bi bi-shield-fill fs-5"></i>
                            </div>
                            <div>
                                <div class="fw-semibold small">{{ $this->roleName }}</div>
                                <div class="text-muted" style="font-size:.72rem;">{{ $this->roleDescription }}</div>
                            </div>
                        </div>
                        <p class="text-muted" style="font-size:.78rem;">
                            This role grants specific access permissions based on system configuration.
                        </p>
                        <div class="section-label mb-2">Key Permissions</div>
                        <div class="d-flex flex-wrap gap-1">
                            @foreach($this->keyPermissions as $permission)
                                <span class="perm-chip">{{ $permission }}</span>
                            @endforeach
                            @if($this->totalPermissionsCount > 6)
                                <span class="perm-chip">+{{ $this->totalPermissionsCount - 6 }} more</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN – Tabs Content -->
            <div class="col-12 col-lg-8 col-xl-9">

                <!-- Summary Stats -->
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-3">
                        <div class="card border-0 shadow-sm h-100 stat-card">
                            <div class="card-body d-flex align-items-center gap-3 p-3">
                                <div class="stat-icon bg-primary bg-opacity-10 text-primary rounded-3">
                                    <i class="bi bi-file-earmark-text-fill fs-5"></i>
                                </div>
                                <div>
                                    <div class="fw-bold fs-5 lh-1">{{ $this->applicationsCount }}</div>
                                    <small class="text-muted">Applications</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card border-0 shadow-sm h-100 stat-card">
                            <div class="card-body d-flex align-items-center gap-3 p-3">
                                <div class="stat-icon bg-success bg-opacity-10 text-white rounded-3">
                                    <i class="bi bi-check-circle-fill fs-5"></i>
                                </div>
                                <div>
                                    <div class="fw-bold fs-5 lh-1">{{ $this->approvalsCount }}</div>
                                    <small class="text-muted">Approvals</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card border-0 shadow-sm h-100 stat-card">
                            <div class="card-body d-flex align-items-center gap-3 p-3">
                                <div class="stat-icon bg-warning bg-opacity-10 text-warning rounded-3">
                                    <i class="bi bi-bar-chart-fill fs-5"></i>
                                </div>
                                <div>
                                    <div class="fw-bold fs-5 lh-1">{{ $this->reportsCount }}</div>
                                    <small class="text-muted">Reports</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card border-0 shadow-sm h-100 stat-card">
                            <div class="card-body d-flex align-items-center gap-3 p-3">
                                <div class="stat-icon bg-info bg-opacity-10 text-info rounded-3">
                                    <i class="bi bi-people-fill fs-5"></i>
                                </div>
                                <div>
                                    <div class="fw-bold fs-5 lh-1">{{ $this->loginDaysCount }}</div>
                                    <small class="text-muted">Account Age (Days)</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabbed Card -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom px-4 pt-3 pb-0">
                        <ul class="nav nav-tabs">
                            <li class="nav-item">
                                <a class="nav-link {{ $activeTab === 'profile-details' ? 'active' : '' }}" 
                                   href="#" 
                                   wire:click.prevent="setActiveTab('profile-details')">
                                    Profile Details
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $activeTab === 'applications' ? 'active' : '' }}" 
                                   href="#" 
                                   wire:click.prevent="setActiveTab('applications')">
                                    Applications
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body px-4 py-4">
                        <!-- TAB: Profile Details -->
                        <div style="{{ $activeTab !== 'profile-details' ? 'display: none;' : '' }}">
                            @if($activeTab === 'profile-details')
                            <div class="row g-4">
                                <!-- Personal Information -->
                                <div class="col-12 col-md-6">
                                    <p class="section-label mb-3">Personal Information</p>
                                    <div class="info-row d-flex align-items-start">
                                        <span class="info-label text-muted">Full Name</span>
                                        <span class="fw-semibold small">{{ $this->user->display_name }}</span>
                                    </div>
                                    <div class="info-row d-flex align-items-start">
                                        <span class="info-label text-muted">Email</span>
                                        <span class="small">{{ $this->user->email }}</span>
                                    </div>
                                    <div class="info-row d-flex align-items-start">
                                        <span class="info-label text-muted">Username</span>
                                        <span class="small">{{ $this->user->username ?? 'Not set' }}</span>
                                    </div>
                                    <div class="info-row d-flex align-items-start">
                                        <span class="info-label text-muted">Phone Number</span>
                                        <span class="small">{{ $this->user->phone ?? 'Not set' }}</span>
                                    </div>
                                    <div class="info-row d-flex align-items-start">
                                        <span class="info-label text-muted">Gender</span>
                                        <span class="small">{{ $this->formattedGender }}</span>
                                    </div>
                                    <div class="info-row d-flex align-items-start">
                                        <span class="info-label text-muted">Date of Birth</span>
                                        <span class="small">{{ $this->formattedDateOfBirth }}</span>
                                    </div>
                                    <div class="info-row d-flex align-items-start">
                                        <span class="info-label text-muted">Personal Bio</span>
                                        <span class="small">{{ $this->personalBio }}</span>
                                    </div>
                                </div>

                                <!-- Business Information -->
                                <div class="col-12 col-md-6">
                                    <p class="section-label mb-3">Business Information</p>
                                    <div class="info-row d-flex align-items-start">
                                        <span class="info-label text-muted">Organization Name</span>
                                        <span class="small">{{ $this->organizationName }}</span>
                                    </div>
                                    <div class="info-row d-flex align-items-start">
                                        <span class="info-label text-muted">Industry/Interest</span>
                                        <span class="small">{{ $this->industryOrInterest }}</span>
                                    </div>
                                    <div class="info-row d-flex align-items-start">
                                        <span class="info-label text-muted">Years of Operation</span>
                                        <span class="small">{{ $this->yearsOfOperation }}</span>
                                    </div>
                                    <div class="info-row d-flex align-items-start">
                                        <span class="info-label text-muted">Area of Operation</span>
                                        <span class="small">{{ $this->areaOfOperation }}</span>
                                    </div>
                                    <div class="info-row d-flex align-items-start">
                                        <span class="info-label text-muted">Country</span>
                                        <span class="small">{{ $this->country }}</span>
                                    </div>
                                    <div class="info-row d-flex align-items-start">
                                        <span class="info-label text-muted">Company Bio</span>
                                        <span class="small">{{ $this->companyBio }}</span>
                                    </div>
                                </div>

                                <!-- System Information -->
                                <div class="col-12 col-md-6">
                                    <p class="section-label mb-3">System Information</p>
                                    <div class="info-row d-flex align-items-start">
                                        <span class="info-label text-muted">User ID</span>
                                        <span class="small text-muted">{{ $this->userId }}</span>
                                    </div>
                                    <div class="info-row d-flex align-items-start">
                                        <span class="info-label text-muted">Account Created</span>
                                        <span class="small">{{ $this->formattedAccountCreated }}</span>
                                    </div>
                                    <div class="info-row d-flex align-items-start">
                                        <span class="info-label text-muted">Approved By</span>
                                        <span class="small">{{ $this->approvedBy }}</span>
                                    </div>
                                </div>

                                <!-- Address Information -->
                                <div class="col-12 col-md-6">
                                    <p class="section-label mb-3">Address Information</p>
                                    <div class="info-row d-flex align-items-start">
                                        <span class="info-label text-muted">Street Address</span>
                                        <span class="small">{{ $this->streetAddress }}</span>
                                    </div>
                                    <div class="info-row d-flex align-items-start">
                                        <span class="info-label text-muted">City/Area</span>
                                        <span class="small">{{ $this->city }}</span>
                                    </div>
                                    <div class="info-row d-flex align-items-start">
                                        <span class="info-label text-muted">Province</span>
                                        <span class="small">{{ $this->province }}</span>
                                    </div>
                                    <div class="info-row d-flex align-items-start">
                                        <span class="info-label text-muted">Postal Code</span>
                                        <span class="small">{{ $this->postalCode }}</span>
                                    </div>
                                </div>

                                <!-- Documents (if entrepreneur) -->
                                @if($this->user->isEntrepreneur())
                                <div class="col-12">
                                    <hr class="my-1">
                                    <p class="section-label mb-3 mt-3">Documents</p>
                                    <div class="row g-3">
                                        <div class="col-12 col-sm-6">
                                            <div class="info-row d-flex align-items-start">
                                                <span class="info-label text-muted">Tax Clearance</span>
                                                <span class="small">
                                                    @if($this->taxClearancePath)
                                                        <a href="{{ asset('storage/' . $this->taxClearancePath) }}" target="_blank" class="text-primary">
                                                            <i class="bi bi-file-pdf-fill me-1"></i> View Document
                                                        </a>
                                                    @else
                                                        Not uploaded
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6">
                                            <div class="info-row d-flex align-items-start">
                                                <span class="info-label text-muted">Traders License</span>
                                                <span class="small">
                                                    @if($this->tradersLicensePath)
                                                        <a href="{{ asset('storage/' . $this->tradersLicensePath) }}" target="_blank" class="text-primary">
                                                            <i class="bi bi-file-pdf-fill me-1"></i> View Document
                                                        </a>
                                                    @else
                                                        Not uploaded
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <!-- Suspension Info (if suspended) -->
                                @if($this->user->is_suspended)
                                <div class="col-12">
                                    <hr class="my-1">
                                    <p class="section-label mb-3 mt-3 text-danger">Suspension Information</p>
                                    <div class="row g-3">
                                        <div class="col-12 col-sm-6">
                                            <div class="info-row d-flex align-items-start">
                                                <span class="info-label text-muted">Suspension Reason</span>
                                                <span class="small text-danger">{{ $this->user->suspension_reason ?? 'No reason provided' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6">
                                            <div class="info-row d-flex align-items-start">
                                                <span class="info-label text-muted">Suspended Until</span>
                                                <span class="small text-danger">{{ $this->user->suspended_until ? $this->user->suspended_until->format('d M Y') : 'Permanent' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>

                            <hr class="my-4">

                            <p class="section-label mb-3 text-danger">Danger Zone</p>
                            <div class="danger-zone p-4">
                                <!-- Suspend/Reinstate -->
                                <div class="row g-3 align-items-center">
                                    <div class="col-12 col-md-8">
                                        <div class="fw-semibold small mb-1">
                                            @if($this->user->is_suspended)
                                                Reinstate Account
                                            @else
                                                Suspend Account
                                            @endif
                                        </div>
                                        <p class="text-muted mb-0" style="font-size:.78rem;">
                                            @if($this->user->is_suspended)
                                                Reinstating this account will restore all access and permissions. The user will be able to log in again.
                                            @else
                                                Suspending this account will immediately revoke all active sessions and block login access. The user's data will be retained.
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-12 col-md-4 d-flex justify-content-md-end">
                                        @if($this->user->is_suspended)
                                            <button class="btn btn-outline-success btn-sm action-btn"
                                                    wire:click="$dispatch('unsuspendUser', { userId: {{ $this->user->id }} })"
                                                    wire:confirm="Are you sure you want to reinstate this user? They will regain full access to the system.">
                                                <i class="bi bi-check-circle-fill me-1"></i> Reinstate User
                                            </button>
                                        @else
                                            <button class="btn btn-outline-danger btn-sm action-btn"
                                                    wire:click="$dispatch('confirmSuspend', { userId: {{ $this->user->id }} })"
                                                    wire:confirm="Are you sure you want to suspend this user? They will lose access to the system.">
                                                <i class="bi bi-slash-circle-fill me-1"></i> Suspend User
                                            </button>
                                        @endif
                                    </div>

                                    <div class="col-12"><hr class="my-1"></div>

                                    <!-- Delete Account -->
                                    <div class="col-12 col-md-8">
                                        <div class="fw-semibold small mb-1">Delete Account</div>
                                        <p class="text-muted mb-0" style="font-size:.78rem;">
                                            Permanently removes this user and all associated data. This action <strong>cannot be undone</strong>.
                                        </p>
                                    </div>
                                    <div class="col-12 col-md-4 d-flex justify-content-md-end">
                                        <button class="btn btn-danger btn-sm action-btn"
                                                wire:click="$dispatch('confirmDeleteUser', { userId: {{ $this->user->id }} })"
                                                wire:confirm="Are you sure you want to delete this user? This action cannot be undone.">
                                            <i class="bi bi-trash-fill me-1"></i> Delete Account
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- TAB: Applications -->
                        <div style="{{ $activeTab !== 'applications' ? 'display: none;' : '' }}">
                            @if($activeTab === 'applications')
                                @php
                                    $applications = \App\Models\IncubationApplication::where('user_id', $this->user->id)
                                        ->with('call')
                                        ->orderBy('created_at', 'desc')
                                        ->get();
                                    $statusColors = [
                                        'pending' => 'warning',
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                        'under_review' => 'info',
                                    ];
                                @endphp
                                
                                @if($applications->count() > 0)
                                    <div class="row g-3">
                                        @foreach($applications as $app)
                                        <div class="col-12 col-md-6 col-xl-4">
                                            <div class="card border-0 shadow-sm h-100">
                                                <div class="card-body p-3">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <span class="badge bg-{{ $statusColors[$app->status] ?? 'secondary' }} bg-opacity-10 text-{{ $statusColors[$app->status] ?? 'secondary' }} rounded-pill px-2 py-1 small">
                                                            {{ ucfirst($app->status ?? 'Pending') }}
                                                        </span>
                                                        <span class="text-muted small">{{ $app->application_number }}</span>
                                                    </div>
                                                    
                                                    <h6 class="fw-bold mb-1">{{ $app->company_name }}</h6>
                                                    <p class="text-muted small mb-2">{{ $app->call->title ?? 'N/A' }}</p>
                                                    
                                                    <div class="d-flex align-items-center gap-3 mb-3">
                                                        <div class="d-flex align-items-center gap-1">
                                                            <i class="bi bi-calendar3 text-muted" style="font-size: 0.7rem;"></i>
                                                            <span class="small text-muted">{{ $app->submitted_at ? $app->submitted_at->format('d M Y') : '—' }}</span>
                                                        </div>
                                                        <div class="d-flex align-items-center gap-1">
                                                            <i class="bi bi-building text-muted" style="font-size: 0.7rem;"></i>
                                                            <span class="small text-muted">{{ $app->company_type ?? 'N/A' }}</span>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="d-flex gap-2">
                                                        <a href="{{ route('incubation.show', $app->id) }}" 
                                                           class="btn btn-sm btn-outline-primary flex-grow-1">
                                                            <i class="bi bi-eye me-1"></i> View Details
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-5">
                                        <i class="bi bi-file-earmark-text fs-1 text-muted opacity-50"></i>
                                        <p class="text-muted mt-3 mb-0">No applications found for this user.</p>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>