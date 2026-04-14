<?php

use Livewire\Component;
use App\Models\IncubationApplication;
use Livewire\Attributes\On;

new class extends Component
{
    public $id;
    public $application;
    
    public function mount($id = null)
    {
        if ($id) {
            $this->id = $id;
            $this->application = IncubationApplication::with('call')->findOrFail($id);
        }
    }


    #[On('application-updated')]
    public function refreshApplication()
    {
        $this->application = $this->application->fresh();
    }
    
    public function openEditModal($applicationId)
    {
        $this->dispatch('edit-application', applicationId: $applicationId);
    }
    
    public function getSocialMediaProperty()
    {
        return $this->application->social_media ?? [];
    }
    
    public function getApplicantSocialMediaProperty()
    {
        return $this->application->applicant_social_media ?? [];
    }
    
    public function getTotalShareholdersProperty()
    {
        return $this->application->number_of_shareholders ?? 0;
    }
    
    public function getWomenPercentageProperty()
    {
        $total = $this->totalShareholders;
        if ($total == 0) return 0;
        return round(($this->application->number_women_shareholders ?? 0) / $total * 100);
    }
    
    public function getYouthPercentageProperty()
    {
        $total = $this->totalShareholders;
        if ($total == 0) return 0;
        return round(($this->application->number_youth_shareholders ?? 0) / $total * 100);
    }
    
    public function getIsRuralBasedProperty()
    {
        $ruralDistricts = ['Leribe', 'Berea', 'Mafeteng', 'Mohale\'s Hoek', 'Quthing', 'Qacha\'s Nek', 'Mokhotlong', 'Butha-Buthe', 'Thaba-Tseka'];
        return in_array($this->application->district, $ruralDistricts);
    }
    
    public function getCompanyInitialsProperty()
    {
        return strtoupper(substr($this->application->company_name, 0, 2));
    }

    public function getEntrepreneurDocumentsProperty()
    {
        $user = $this->application->user;
        if ($user && $user->isEntrepreneur()) {
            return [
                'tax_clearance' => $user->userable->tax_clearance_path ?? null,
                'traders_license' => $user->userable->traders_license_path ?? null,
                'profile_photo' => $user->profile_photo_url ?? null,
            ];
        }
        return [];
    }

   public function deleteApplication($applicationId)
    {
       
        $application = IncubationApplication::findOrFail($applicationId);
        
        // Check if user owns this application
        if ($application->user_id !== Auth::id()) {
            $this->dispatch('notify', type: 'error', message: 'You are not authorized to delete this application');
            return;
        }
        
        // Check status - only pending applications can be deleted
        if ($application->status !== 'pending') {
            $this->dispatch('notify', type: 'error', message: 'Only pending applications can be deleted');
            return;
        }
        
        // Delete the application
        $application->delete();
        
        // Just navigate back
        $this->dispatch('go-back');
    }
    
    public function getApplicantInitialsProperty()
    {
        $name = $this->application->applicant_name ?? '';
        $parts = explode(' ', $name);
        if (count($parts) >= 2) {
            return strtoupper(substr($parts[0], 0, 1) . substr($parts[1], 0, 1));
        }
        return strtoupper(substr($name, 0, 2));
    }
}
?>

<div>
    @if($application)
    <div class="app-detail p-4" style="max-width: 1400px; margin: 0 auto;">

        <!-- BREADCRUMB -->
        <nav aria-label="breadcrumb" class="mb-4">
           <ol class="breadcrumb mb-0 small">
                <!-- Home -->
                <li class="breadcrumb-item">
                    <a href="{{ route('calls.index') }}" class="text-decoration-none text-muted">
                        <i class="bi bi-house-fill" style="font-size:.7rem"></i> Home
                    </a>
                </li>

                <!-- Current Application -->
                <li class="breadcrumb-item active fw-semibold">
                    {{ $application->application_number }}
                </li>
            </ol>
        </nav>

        <!-- HERO HEADER -->
        <div class="app-hero mb-4">

            <!-- Top bar -->
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <span class="app-status-pill pill-{{ strtolower(str_replace(' ', '-', $application->status ?? 'pending')) }}">
                        {{ ucfirst($application->status ?? 'Pending') }}
                    </span>
                    <span class="app-meta-tag"><i class="bi bi-hash me-1"></i>{{ $application->application_number }}</span>
                    <span class="app-meta-tag"><i class="bi bi-megaphone me-1"></i>{{ $application->call->title ?? 'N/A' }}</span>
                    <span class="app-meta-tag"><i class="bi bi-calendar3 me-1"></i>Submitted {{ $application->submitted_at?->format('d M Y, H:i') ?? '—' }}</span>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="#" class="btn app-btn-ghost btn-sm"><i class="bi bi-download me-1"></i>Export PDF</a>
                    <!-- Only Super Admin -->
                    @if(auth()->check() && auth()->user()->hasRole('Super Administrator'))
                        <a href="#" class="btn app-btn-ghost btn-sm">
                            <i class="bi bi-funnel me-1"></i>Go to Screening
                        </a>
                    @endif

                    <!-- Only Applicant -->
                    @if(auth()->check() && auth()->user()->hasRole('Applicant'))
                        <a href="#"
                        class="btn app-btn-primary btn-sm"
                        wire:click="openEditModal({{ $application->id }})">
                            <i class="bi bi-pencil-fill me-1"></i>
                            Edit Application
                        </a>
                    @endif
                </div>
            </div>

            <!-- Company name + description -->
            <div class="d-flex align-items-start gap-4 mb-4">
                <div class="app-company-avatar flex-shrink-0">{{ $this->companyInitials }}</div>
                <div class="flex-grow-1">
                    <h2 class="app-hero-title mb-1">{{ $application->company_name }}</h2>
                    @if($application->registered_company_name && $application->registered_company_name !== $application->company_name)
                        <div class="text-muted small mb-1">Registered as: <span class="fw-medium text-dark">{{ $application->registered_company_name }}</span></div>
                    @endif
                    <p class="app-hero-desc mb-3">{{ $application->business_description }}</p>
                    <div class="d-flex flex-wrap gap-3 app-hero-meta">
                        @if($application->industry)
                            <span><i class="bi bi-grid-3x3 me-1"></i>{{ $application->industry }}</span>
                        @endif
                        @if($application->sector)
                            <span><i class="bi bi-tag me-1"></i>{{ $application->sector }}</span>
                        @endif
                        @if($application->district || $application->country)
                            <span><i class="bi bi-geo-alt me-1"></i>{{ collect([$application->district, $application->country])->filter()->implode(', ') }}</span>
                        @endif
                        @if($application->company_stage)
                            <span><i class="bi bi-ladder me-1"></i>{{ $application->company_stage }}</span>
                        @endif
                        @if($application->year_of_establishment)
                            <span><i class="bi bi-building me-1"></i>Est. {{ $application->year_of_establishment }}</span>
                        @endif
                        @if($application->website)
                            <a href="{{ $application->website }}" target="_blank" class="text-primary text-decoration-none"><i class="bi bi-link-45deg me-1"></i>{{ $application->website }}</a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- KPI Strip -->
            <div class="app-hero-divider mb-4"></div>
            <div class="row g-3">
                <div class="col-6 col-md-3">
                    <div class="app-kpi">
                        <div class="app-kpi-icon" style="background:rgba(59,130,246,.1);color:#3b82f6;"><i class="bi bi-people-fill"></i></div>
                        <div>
                            <div class="app-kpi-val text-primary">{{ $application->number_of_shareholders ?? 0 }}</div>
                            <div class="app-kpi-label">Shareholders</div>
                            <div class="app-kpi-sub">{{ $application->number_women_shareholders ?? 0 }} women · {{ $application->number_youth_shareholders ?? 0 }} youth</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="app-kpi">
                        <div class="app-kpi-icon" style="background:rgba(16,185,129,.1);color:#10b981;"><i class="bi bi-person-check-fill"></i></div>
                        <div>
                            <div class="app-kpi-val" style="color:#10b981;">{{ number_format($application->number_of_customers ?? 0) }}</div>
                            <div class="app-kpi-label">Customers</div>
                            <div class="app-kpi-sub">Current customer base</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="app-kpi">
                        <div class="app-kpi-icon" style="background:rgba(245,158,11,.1);color:#f59e0b;"><i class="bi bi-currency-exchange"></i></div>
                        <div>
                            <div class="app-kpi-val text-warning">M {{ number_format($application->average_monthly_sales ?? 0, 0) }}</div>
                            <div class="app-kpi-label">Avg Monthly Sales</div>
                            <div class="app-kpi-sub">Estimated revenue</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="app-kpi">
                        <div class="app-kpi-icon" style="background:rgba(139,92,246,.1);color:#8b5cf6;"><i class="bi bi-briefcase-fill"></i></div>
                        <div>
                            <div class="app-kpi-val" style="color:#8b5cf6;">{{ $application->jobs_to_create_12_months ?? 0 }}</div>
                            <div class="app-kpi-label">Jobs to Create</div>
                            <div class="app-kpi-sub">Next 12 months</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MAIN 2-COLUMN LAYOUT -->
        <div class="row g-4">

            <!-- LEFT COLUMN (8/12) -->
            <div class="col-12 col-xl-8">

                <!-- 1. Business Information -->
                <!-- 1. Business Information -->
                <div class="app-card mb-4">
                    <div class="app-card-header">
                        <div class="app-section-icon" style="background:rgba(59,130,246,.1);color:#3b82f6;"><i class="bi bi-building-fill"></i></div>
                        <div>
                            <div class="fw-semibold">Business Information</div>
                            <div class="app-section-sub">Company profile and classification</div>
                        </div>
                    </div>
                    <div class="app-card-body">
                        <div class="row g-0">
                            <div class="col-12 col-md-6">
                                <div class="app-field">
                                    <div class="app-field-label">Company Name</div>
                                    <div class="app-field-val">{{ $application->company_name ?? '—' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="app-field">
                                    <div class="app-field-label">Registered Name</div>
                                    <div class="app-field-val">{{ $application->registered_company_name ?? '—' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="app-field">
                                    <div class="app-field-label">Company Type</div>
                                    <div class="app-field-val">{{ $application->company_type ?? '—' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="app-field">
                                    <div class="app-field-label">Company Stage</div>
                                    <div class="app-field-val">
                                        @if($application->company_stage)
                                            <span class="app-stage-tag">{{ $application->company_stage }}</span>
                                        @else —
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="app-field">
                                    <div class="app-field-label">Industry</div>
                                    <div class="app-field-val">{{ $application->industry ?? '—' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="app-field">
                                    <div class="app-field-label">Sector</div>
                                    <div class="app-field-val">
                                        @if($application->sector)
                                            <span class="app-sector-tag">{{ $application->sector }}</span>
                                        @else —
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @if($application->industry_other_elaboration)
                            <div class="col-12">
                                <div class="app-field">
                                    <div class="app-field-label">Industry Elaboration</div>
                                    <div class="app-field-val">{{ $application->industry_other_elaboration }}</div>
                                </div>
                            </div>
                            @endif
                            <div class="col-12 col-md-6">
                                <div class="app-field">
                                    <div class="app-field-label">Company Size</div>
                                    <div class="app-field-val">{{ $application->company_size ?? '—' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="app-field">
                                    <div class="app-field-label">Year of Establishment</div>
                                    <div class="app-field-val">{{ $application->year_of_establishment ?? '—' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="app-field">
                                    <div class="app-field-label">Country</div>
                                    <div class="app-field-val">{{ $application->country ?? '—' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="app-field">
                                    <div class="app-field-label">District</div>
                                    <div class="app-field-val">{{ $application->district ?? '—' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="app-field">
                                    <div class="app-field-label">Website</div>
                                    <div class="app-field-val">
                                        @if($application->website)
                                            <a href="{{ $application->website }}" target="_blank" class="text-primary text-decoration-none small">
                                                <i class="bi bi-link-45deg me-1"></i>{{ $application->website }}
                                            </a>
                                        @else —
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @if(!empty($this->socialMedia))
                            <div class="col-12">
                                <div class="app-field border-bottom-0">
                                    <div class="app-field-label">Social Media</div>
                                    <div class="d-flex flex-wrap gap-2 mt-1">
                                        @foreach($this->socialMedia as $platform => $url)
                                            @if($url)
                                            <a href="{{ $url }}" target="_blank" class="app-social-chip">
                                                @switch(strtolower($platform))
                                                    @case('facebook')  <i class="bi bi-facebook text-primary me-1"></i> @break
                                                    @case('twitter')   <i class="bi bi-twitter-x me-1"></i> @break
                                                    @case('instagram') <i class="bi bi-instagram text-danger me-1"></i> @break
                                                    @case('linkedin')  <i class="bi bi-linkedin text-primary me-1"></i> @break
                                                    @default           <i class="bi bi-globe2 me-1"></i>
                                                @endswitch
                                                {{ ucfirst($platform) }}
                                            </a>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Add Documents Section Right After Business Information -->
                <div class="app-card mb-4">
                    <div class="app-card-header">
                        <div class="app-section-icon" style="background:rgba(245,158,11,.1);color:#f59e0b;"><i class="bi bi-file-earmark-text-fill"></i></div>
                        <div>
                            <div class="fw-semibold">Business Documents</div>
                            <div class="app-section-sub">Registration and compliance documents</div>
                        </div>
                    </div>
                    <div class="app-card-body">
                        <div class="row g-0">
                            <!-- Tax Clearance Certificate -->
                           <!-- Tax Clearance Certificate -->
                            <div class="col-12 col-md-6">
                                <div class="app-field">
                                    <div class="app-field-label">
                                        <i class="bi bi-file-pdf-fill text-danger me-1"></i>Tax Clearance Certificate
                                    </div>
                                    <div class="app-field-val">
                                        @php
                                            $documents = $this->entrepreneurDocuments;
                                        @endphp

                                        @if(isset($documents['tax_clearance']) && $documents['tax_clearance'])
                                            <a href="{{ Storage::url($documents['tax_clearance']) }}" target="_blank" class="btn btn-sm btn-outline-primary mt-1">
                                                <i class="bi bi-eye me-1"></i> View Document
                                            </a>
                                            <span class="text-muted small d-block mt-1">{{ basename($documents['tax_clearance']) }}</span>
                                        @else
                                            <span class="text-muted">— Not uploaded —</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <!-- Trader's License -->
                            <div class="col-12 col-md-6">
                                <div class="app-field">
                                    <div class="app-field-label">
                                        <i class="bi bi-file-pdf-fill text-danger me-1"></i>Trader's License
                                    </div>
                                    <div class="app-field-val">
                                        @php
                                            $licensePath = $application->user->userable->traders_license_path ?? null;
                                        @endphp
                                        @if($licensePath)
                                            <a href="{{ Storage::url($licensePath) }}" target="_blank" class="btn btn-sm btn-outline-primary mt-1">
                                                <i class="bi bi-eye me-1"></i> View Document
                                            </a>
                                            <span class="text-muted small d-block mt-1">{{ basename($licensePath) }}</span>
                                        @else
                                            <span class="text-muted">— Not uploaded —</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Profile Image/Logo -->
                            <div class="col-12">
                                <div class="app-field border-bottom-0">
                                    <div class="app-field-label">
                                        <i class="bi bi-image-fill text-primary me-1"></i>Company Logo / Profile Image
                                    </div>
                                    <div class="app-field-val">
                                        @php
                                            $profilePhoto = $application->user->profile_photo_url ?? null;
                                        @endphp
                                        @if($profilePhoto)
                                            <a href="{{ $profilePhoto }}" target="_blank" class="btn btn-sm btn-outline-primary mt-1">
                                                <i class="bi bi-eye me-1"></i> View Image
                                            </a>
                                            <div class="mt-2">
                                                <img src="{{ $profilePhoto }}" class="rounded border" style="width: 80px; height: 80px; object-fit: cover;">
                                            </div>
                                        @else
                                            <span class="text-muted">— No image uploaded —</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Business Model & Offering -->
                <div class="app-card mb-4">
                    <div class="app-card-header">
                        <div class="app-section-icon" style="background:rgba(16,185,129,.1);color:#10b981;"><i class="bi bi-diagram-2-fill"></i></div>
                        <div>
                            <div class="fw-semibold">Business Model &amp; Offering</div>
                            <div class="app-section-sub">Value proposition and commercial approach</div>
                        </div>
                    </div>
                    <div class="app-card-body">
                        <div class="app-field-prose">
                            <div class="app-field-label mb-1">Business Description</div>
                            <div class="app-prose-text">{{ $application->business_description ?? '—' }}</div>
                        </div>
                        @if($application->business_model)
                        <div class="app-field-prose border-top">
                            <div class="app-field-label mb-1">Business Model</div>
                            <div class="app-prose-text">{{ $application->business_model }}</div>
                        </div>
                        @endif
                        @if($application->offering)
                        <div class="app-field-prose border-top">
                            <div class="app-field-label mb-1">Product / Offering</div>
                            <div class="app-prose-text">{{ $application->offering }}</div>
                        </div>
                        @endif
                        @if($application->revenue_model)
                        <div class="app-field-prose border-top">
                            <div class="app-field-label mb-1">Revenue Model</div>
                            <div class="app-prose-text">{{ $application->revenue_model }}</div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- 3. Financial & Impact Metrics -->
                <div class="app-card mb-4">
                    <div class="app-card-header">
                        <div class="app-section-icon" style="background:rgba(245,158,11,.1);color:#f59e0b;"><i class="bi bi-graph-up-arrow"></i></div>
                        <div>
                            <div class="fw-semibold">Financial &amp; Impact Metrics</div>
                            <div class="app-section-sub">Quantitative business performance data</div>
                        </div>
                    </div>
                    <div class="app-card-body">
                        <div class="row g-0">
                            <div class="col-12 col-md-6">
                                <div class="app-field">
                                    <div class="app-field-label">Average Monthly Sales</div>
                                    <div class="app-field-val fw-bold text-success">M {{ number_format($application->average_monthly_sales ?? 0, 2) }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="app-field">
                                    <div class="app-field-label">Number of Customers</div>
                                    <div class="app-field-val fw-bold">{{ number_format($application->number_of_customers ?? 0) }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="app-field">
                                    <div class="app-field-label">Jobs to Create (12 months)</div>
                                    <div class="app-field-val fw-bold" style="color:#8b5cf6;">{{ $application->jobs_to_create_12_months ?? 0 }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="app-field">
                                    <div class="app-field-label">Willing to Commit</div>
                                    <div class="app-field-val">
                                        @if($application->willing_to_commit)
                                            <span class="app-yes-badge"><i class="bi bi-check-circle-fill me-1"></i>Yes</span>
                                        @else
                                            <span class="app-no-badge"><i class="bi bi-x-circle-fill me-1"></i>No</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="app-field">
                                    <div class="app-field-label">Received Financial Support Before</div>
                                    <div class="app-field-val">
                                        @if($application->received_financial_support == 'Yes')
                                            <span class="app-yes-badge"><i class="bi bi-check-circle-fill me-1"></i>Yes</span>
                                        @elseif($application->received_financial_support == 'No')
                                            <span class="app-no-badge"><i class="bi bi-dash-circle me-1"></i>No</span>
                                        @else
                                            —
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="app-field border-bottom-0">
                                    <div class="app-field-label">Participated in Competitions</div>
                                    <div class="app-field-val">
                                        @if($application->participated_competitions == 'Yes')
                                            <span class="app-yes-badge"><i class="bi bi-check-circle-fill me-1"></i>Yes</span>
                                        @elseif($application->participated_competitions == 'No')
                                            <span class="app-no-badge"><i class="bi bi-dash-circle me-1"></i>No</span>
                                        @else
                                            —
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. Shareholders & PDO -->
                <div class="app-card mb-4">
                    <div class="app-card-header">
                        <div class="app-section-icon" style="background:rgba(236,72,153,.1);color:#ec4899;"><i class="bi bi-people-fill"></i></div>
                        <div>
                            <div class="fw-semibold">Shareholders &amp; PDO Indicators</div>
                            <div class="app-section-sub">Ownership structure and development objectives</div>
                        </div>
                    </div>
                    <div class="app-card-body">
                        <div class="row g-0">
                            <div class="col-12 col-md-4">
                                <div class="app-field">
                                    <div class="app-field-label">Total Shareholders</div>
                                    <div class="app-field-val fw-bold fs-5">{{ $application->number_of_shareholders ?? 0 }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="app-field">
                                    <div class="app-field-label">Women Shareholders</div>
                                    <div class="d-flex align-items-center gap-2 mt-1">
                                        <div class="fw-bold fs-5" style="color:#ec4899;">{{ $application->number_women_shareholders ?? 0 }}</div>
                                        <span class="app-pdo-pct-badge" style="background:#fce7f3;color:#be185d;">{{ $this->womenPercentage }}%</span>
                                    </div>
                                    <div class="app-pdo-bar mt-2"><div class="app-pdo-fill" style="width:{{ $this->womenPercentage }}%;background:#ec4899;"></div></div>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="app-field border-bottom-0">
                                    <div class="app-field-label">Youth Shareholders</div>
                                    <div class="d-flex align-items-center gap-2 mt-1">
                                        <div class="fw-bold fs-5 text-success">{{ $application->number_youth_shareholders ?? 0 }}</div>
                                        <span class="app-pdo-pct-badge" style="background:#dcfce7;color:#15803d;">{{ $this->youthPercentage }}%</span>
                                    </div>
                                    <div class="app-pdo-bar mt-2"><div class="app-pdo-fill" style="width:{{ $this->youthPercentage }}%;background:#10b981;"></div></div>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 pb-4">
                            <div class="app-pdo-flags-bar p-3 rounded-3">
                                <div class="small fw-semibold text-muted mb-2">PDO Target Group Flags</div>
                                <div class="d-flex flex-wrap gap-2">
                                    <span class="app-pdo-flag {{ ($application->number_women_shareholders ?? 0) > 0 ? 'pdo-flag-active-pink' : 'pdo-flag-inactive' }}">
                                        <i class="bi bi-gender-female me-1"></i>Women-owned
                                    </span>
                                    <span class="app-pdo-flag {{ ($application->number_youth_shareholders ?? 0) > 0 ? 'pdo-flag-active-green' : 'pdo-flag-inactive' }}">
                                        <i class="bi bi-person-fill me-1"></i>Youth-owned
                                    </span>
                                    <span class="app-pdo-flag {{ $this->isRuralBased ? 'pdo-flag-active-amber' : 'pdo-flag-inactive' }}">
                                        <i class="bi bi-tree me-1"></i>Rural-based
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN (4/12) -->
            <div class="col-12 col-xl-4">

                <!-- Application Metadata -->
                <div class="app-card mb-4">
                    <div class="app-card-header">
                        <div class="app-section-icon"><i class="bi bi-info-circle-fill"></i></div>
                        <div><div class="fw-semibold">Application Details</div></div>
                    </div>
                    <div class="app-card-body p-0">
                        <dl class="app-dl">
                            <div class="app-dl-row"><dt>Application No.</dt><dd class="fw-bold text-primary">{{ $application->application_number }}</dd></div>
                            <div class="app-dl-row"><dt>Status</dt><dd><span class="app-status-pill app-status-pill-sm pill-{{ strtolower(str_replace(' ', '-', $application->status ?? 'pending')) }}">{{ ucfirst($application->status ?? 'Pending') }}</span></dd></div>
                            <div class="app-dl-row"><dt>Call</dt><dd class="fw-semibold">{{ $application->call->title ?? '—' }}</dd></div>
                            <div class="app-dl-row"><dt>Applied Date</dt><dd>{{ $application->applied_date?->format('d M Y') ?? '—' }}</dd></div>
                            <div class="app-dl-row"><dt>Applied Time</dt><dd>{{ $application->applied_time?->format('H:i') ?? '—' }}</dd></div>
                            <div class="app-dl-row"><dt>Submitted At</dt><dd>{{ $application->submitted_at?->format('d M Y, H:i') ?? '—' }}</dd></div>
                            <div class="app-dl-row"><dt>Created</dt><dd>{{ $application->created_at?->format('d M Y') ?? '—' }}</dd></div>
                            <div class="app-dl-row border-bottom-0"><dt>Last Updated</dt><dd>{{ $application->updated_at?->format('d M Y') ?? '—' }}</dd></div>
                        </dl>
                    </div>
                </div>

                <!-- Applicant (Owner) -->
                <div class="app-card mb-4">
                    <div class="app-card-header">
                        <div class="app-section-icon" style="background:rgba(139,92,246,.1);color:#8b5cf6;"><i class="bi bi-person-badge-fill"></i></div>
                        <div>
                            <div class="fw-semibold">Applicant / Owner</div>
                            <div class="app-section-sub">Primary contact person</div>
                        </div>
                    </div>
                    <div class="app-card-body p-4">
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="app-person-avatar">{{ $this->applicantInitials }}</div>
                            <div>
                                <div class="fw-bold text-dark" style="font-size:.95rem;">{{ $application->applicant_name ?? '—' }}</div>
                                <div class="text-muted small">{{ $application->applicant_title ?? '' }}</div>
                            </div>
                        </div>
                        <div class="d-flex flex-column gap-3 small">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-envelope-fill text-muted mt-1 flex-shrink-0" style="font-size:.78rem;"></i>
                                <div>
                                    <div class="text-muted" style="font-size:.68rem;text-transform:uppercase;letter-spacing:.04em;font-weight:600;">Email</div>
                                    <a href="mailto:{{ $application->applicant_email }}" class="text-primary text-decoration-none fw-medium">{{ $application->applicant_email ?? '—' }}</a>
                                </div>
                            </div>
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-telephone-fill text-muted mt-1 flex-shrink-0" style="font-size:.78rem;"></i>
                                <div>
                                    <div class="text-muted" style="font-size:.68rem;text-transform:uppercase;letter-spacing:.04em;font-weight:600;">Phone</div>
                                    <div class="fw-medium">{{ $application->applicant_contact_number ?? '—' }}</div>
                                </div>
                            </div>
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-flag-fill text-muted mt-1 flex-shrink-0" style="font-size:.78rem;"></i>
                                <div>
                                    <div class="text-muted" style="font-size:.68rem;text-transform:uppercase;letter-spacing:.04em;font-weight:600;">Nationality</div>
                                    <div class="fw-medium">{{ $application->applicant_nationality ?? '—' }}</div>
                                </div>
                            </div>
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-gender-ambiguous text-muted mt-1 flex-shrink-0" style="font-size:.78rem;"></i>
                                <div>
                                    <div class="text-muted" style="font-size:.68rem;text-transform:uppercase;letter-spacing:.04em;font-weight:600;">Gender</div>
                                    <div class="fw-medium">{{ $application->applicant_gender ?? '—' }}</div>
                                </div>
                            </div>
                            @if($application->applicant_about)
                            <div class="border-top pt-3 mt-1">
                                <div class="text-muted mb-1" style="font-size:.68rem;text-transform:uppercase;letter-spacing:.04em;font-weight:600;">About</div>
                                <p class="small text-dark mb-0 lh-lg">{{ $application->applicant_about }}</p>
                            </div>
                            @endif
                            @if(!empty($this->applicantSocialMedia))
                            <div class="border-top pt-3 mt-1">
                                <div class="text-muted mb-2" style="font-size:.68rem;text-transform:uppercase;letter-spacing:.04em;font-weight:600;">Social Media</div>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($this->applicantSocialMedia as $platform => $url)
                                        @if($url)
                                        <a href="{{ $url }}" target="_blank" class="app-social-chip">
                                            <i class="bi bi-{{ strtolower($platform) === 'linkedin' ? 'linkedin text-primary' : (strtolower($platform) === 'twitter' ? 'twitter-x' : 'globe2') }} me-1"></i>
                                            {{ ucfirst($platform) }}
                                        </a>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="app-card">
                    <div class="app-card-header">
                        <div class="app-section-icon"><i class="bi bi-lightning-charge-fill"></i></div>
                        <div class="fw-semibold">Quick Actions</div>
                    </div>
                    <div class="app-card-body p-3">
                        <div class="d-flex flex-column gap-2">
                            @if(auth()->check() && auth()->user()->hasRole('Super Administrator'))
                                <a href="#" class="btn app-action-link text-start">
                                    <i class="bi bi-funnel-fill me-2 text-warning"></i>
                                    <span>Review Screening</span>
                                    <i class="bi bi-chevron-right ms-auto text-muted"></i>
                                </a>

                                <a href="#" class="btn app-action-link text-start">
                                    <i class="bi bi-clipboard2-data-fill me-2 text-primary"></i>
                                    <span>Go to Evaluation</span>
                                    <i class="bi bi-chevron-right ms-auto text-muted"></i>
                                </a>

                            @endif
                            @if(auth()->check() && auth()->user()->hasRole('Applicant') && $application->status === 'Draft')
                                <a href="#" 
                                class="btn app-action-link text-start"
                                wire:click.prevent="openEditModal({{ $application->id }})">
                                    <i class="bi bi-pencil-fill me-2 text-info"></i>
                                    <span>Edit Application</span>
                                    <i class="bi bi-chevron-right ms-auto text-muted"></i>
                                </a>
                            @endif
                            <hr class="my-1">
                           @if(auth()->check() && auth()->user()->hasAnyRole(['Super Administrator', 'Applicant']))

                                @if($application->status === 'draft')
                                    <button 
                                        class="btn app-action-link app-action-danger text-start w-100"
                                        wire:click="deleteApplication({{ $application->id }})"
                                        wire:confirm="Delete this application? This cannot be undone.">
                                        
                                        <i class="bi bi-trash3-fill me-2"></i>
                                        <span>Delete Application</span>
                                        <i class="bi bi-chevron-right ms-auto text-muted"></i>
                                    </button>
                                @else
                                    <button 
                                        class="btn app-action-link text-start w-100 disabled"
                                        style="opacity: 0.5; cursor: not-allowed;"
                                        disabled
                                        title="Only pending applications can be deleted">
                                        
                                        <i class="bi bi-trash3-fill me-2 text-secondary"></i>
                                        <span>Delete Application (Only Draft)</span>
                                        <i class="bi bi-chevron-right ms-auto text-muted"></i>
                                    </button>
                                @endif

                            @endif
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>
    @endif
    <livewire:applications.modals.apply-for-incubation/>
    <script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('go-back', () => {
            window.history.back();
        });
    });
</script>
</div>