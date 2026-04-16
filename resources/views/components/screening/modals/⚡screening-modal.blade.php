<?php

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\IncubationApplication;
use App\Models\Screening;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

new class extends Component
{
    public $applicationId = null;
    public $application = null;
    public $screening = null;
    public $activeTab = 'overview';
    public $screeningNotes = '';
    public $rejectionCategory = '';
    public $rejectionDetails = '';
    public $eligibilityChecklist = [];
    public $showRejectionSection = false;
    public $isOpen = false;
    
    // Document properties
    public $taxClearancePath = null;
    public $tradersLicensePath = null;
    public $taxClearanceVerified = false;
    public $tradersLicenseVerified = false;
    
    #[On('openReviewPanel')]
    public function loadApplication($applicationId)
    {
        $this->applicationId = $applicationId;
        $this->application = IncubationApplication::with(['user.userable', 'call'])->find($applicationId);
        $this->loadDocuments();
        $this->loadScreening();
        $this->isOpen = true;
    }
    
    public function closePanel()
    {
        $this->isOpen = false;
        $this->reset(['applicationId', 'application', 'screening', 'activeTab', 'screeningNotes', 'rejectionCategory', 'rejectionDetails', 'showRejectionSection', 'taxClearancePath', 'tradersLicensePath', 'taxClearanceVerified', 'tradersLicenseVerified']);
    }
    
    public function loadDocuments()
    {
        if ($this->application && $this->application->user && $this->application->user->userable) {
            $entrepreneur = $this->application->user->userable;
            
            if ($entrepreneur instanceof \App\Models\Entrepreneur) {
                $this->taxClearancePath = $entrepreneur->tax_clearance_path ?? null;
                $this->tradersLicensePath = $entrepreneur->traders_license_path ?? null;
                $this->taxClearanceVerified = $entrepreneur->metadata['tax_clearance_verified'] ?? false;
                $this->tradersLicenseVerified = $entrepreneur->metadata['traders_license_verified'] ?? false;
            }
        }
    }
    
    public function verifyTaxClearance($verified)
    {
        // Don't allow verification if application is already eligible or rejected
        if ($this->application && in_array($this->application->status, ['eligible', 'rejected'])) {
            $this->dispatch('notify', type: 'error', message: 'Cannot modify document verification. Application already ' . $this->application->status);
            return;
        }
        
        $this->taxClearanceVerified = $verified;
        $this->saveDocumentVerification();
        $this->dispatch('notify', type: 'success', message: $verified ? 'Tax Clearance Certificate verified' : 'Tax Clearance Certificate marked as not verified');
    }
    
    public function verifyTradersLicense($verified)
    {
        // Don't allow verification if application is already eligible or rejected
        if ($this->application && in_array($this->application->status, ['eligible', 'rejected'])) {
            $this->dispatch('notify', type: 'error', message: 'Cannot modify document verification. Application already ' . $this->application->status);
            return;
        }
        
        $this->tradersLicenseVerified = $verified;
        $this->saveDocumentVerification();
        $this->dispatch('notify', type: 'success', message: $verified ? "Trader's License verified" : "Trader's License marked as not verified");
    }
    
    public function saveDocumentVerification()
    {
        if ($this->application && $this->application->user && $this->application->user->userable) {
            $entrepreneur = $this->application->user->userable;
            
            if ($entrepreneur instanceof \App\Models\Entrepreneur) {
                $metadata = $entrepreneur->metadata ?? [];
                $metadata['tax_clearance_verified'] = $this->taxClearanceVerified;
                $metadata['traders_license_verified'] = $this->tradersLicenseVerified;
                $metadata['tax_clearance_verified_at'] = $this->taxClearanceVerified ? now()->toDateTimeString() : null;
                $metadata['traders_license_verified_at'] = $this->tradersLicenseVerified ? now()->toDateTimeString() : null;
                $metadata['tax_clearance_verified_by'] = $this->taxClearanceVerified ? Auth::id() : null;
                $metadata['traders_license_verified_by'] = $this->tradersLicenseVerified ? Auth::id() : null;
                
                $entrepreneur->metadata = $metadata;
                $entrepreneur->save();
            }
        }
    }
    
    public function getDocumentStatsProperty()
    {
        $hasTaxClearance = !empty($this->taxClearancePath);
        $hasTradersLicense = !empty($this->tradersLicensePath);
        $uploaded = ($hasTaxClearance ? 1 : 0) + ($hasTradersLicense ? 1 : 0);
        $verified = ($this->taxClearanceVerified ? 1 : 0) + ($this->tradersLicenseVerified ? 1 : 0);
        $total = 2;
        
        return [
            'total' => $total,
            'uploaded' => $uploaded,
            'verified' => $verified,
            'missing' => $total - $uploaded,
            'allUploaded' => $uploaded === $total,
            'allVerified' => $verified === $total,
            'verificationProgress' => $total > 0 ? round(($verified / $total) * 100) : 0
        ];
    }
    
    public function loadScreening()
    {
        $this->screening = Screening::where('application_id', $this->applicationId)->first();
        
        if ($this->screening) {
            $this->screeningNotes = $this->screening->screening_notes ?? '';
            $this->rejectionCategory = $this->screening->rejection_category ?? '';
            $this->rejectionDetails = $this->screening->rejection_details ?? '';
            $this->eligibilityChecklist = $this->screening->eligibility_checklist ?? $this->getDefaultChecklist();
        } else {
            $this->eligibilityChecklist = $this->getDefaultChecklist();
        }
    }
    
    public function getDefaultChecklist()
    {
        return [
            ['label' => 'Business is formally registered', 'hint' => "Valid Trader's License present", 'passed' => null],
            ['label' => 'Valid Tax Clearance Certificate submitted', 'hint' => 'Not expired at time of submission', 'passed' => null],
            ['label' => 'Business operates in a target sector', 'hint' => 'Aligned with NSDP II priority sectors', 'passed' => null],
            ['label' => 'Business is within the geographic focus area', 'hint' => 'Registered or operating in Lesotho', 'passed' => null],
            ['label' => 'Application form fully completed', 'hint' => 'All mandatory fields filled', 'passed' => null],
            ['label' => 'No duplicate application for this call', 'hint' => 'One application per enterprise per call', 'passed' => null],
            ['label' => 'Business is not in a conflict of interest', 'hint' => 'Owner not related to LEHSFF/CAFI programme staff', 'passed' => null],
        ];
    }
    
    public function getChecklistStatsProperty()
    {
        $total = count($this->eligibilityChecklist);
        $passed = collect($this->eligibilityChecklist)->where('passed', true)->count();
        $failed = collect($this->eligibilityChecklist)->where('passed', false)->count();
        $pending = $total - $passed - $failed;
        
        return [
            'total' => $total,
            'passed' => $passed,
            'failed' => $failed,
            'pending' => $pending,
            'allPassed' => $passed === $total && $total > 0,
            'progress' => $total > 0 ? round(($passed / $total) * 100) : 0
        ];
    }
    
    public function updateChecklist($index, $value)
    {
        // Don't allow checklist updates if application is already eligible or rejected
        if ($this->application && in_array($this->application->status, ['eligible', 'rejected'])) {
            $this->dispatch('notify', type: 'error', message: 'Cannot modify eligibility checklist. Application already ' . $this->application->status);
            return;
        }
        
        $this->eligibilityChecklist[$index]['passed'] = $value;
    }
    
    public function isLocked()
    {
        return $this->application && in_array($this->application->status, ['eligible', 'rejected']);
    }
    
    public function markAsEligible()
    {
        if (!$this->application) {
            return;
        }
        
        // Check if all documents are verified
        if (!$this->document_stats['allVerified']) {
            $this->dispatch('notify', type: 'error', message: 'Cannot mark as eligible: Both documents must be verified');
            return;
        }
        
        // Check if all checklist items are passed
        if (!$this->checklist_stats['allPassed']) {
            $this->dispatch('notify', type: 'error', message: 'Cannot mark as eligible: Not all eligibility criteria are met');
            return;
        }
        
        // Update or create screening record
        Screening::updateOrCreate(
            ['application_id' => $this->applicationId],
            [
                'call_id' => $this->application->call_id,
                'user_id' => Auth::id(),
                'status' => 'eligible',
                'screening_notes' => $this->screeningNotes,
                'eligibility_checklist' => $this->eligibilityChecklist,
                'screened_at' => now(),
                'screened_by' => Auth::id(),
            ]
        );
        
        // Update application status
        $this->application->status = 'eligible';
        $this->application->save();
        
        $this->dispatch('notify', type: 'success', message: 'Application marked as eligible');
        $this->dispatch('applicationUpdated');
        $this->closePanel();
    }
    
    public function confirmRejection()
    {
        $this->showRejectionSection = true;
    }
    
    public function submitRejection()
    {
        if (!$this->rejectionCategory) {
            $this->dispatch('notify', type: 'error', message: 'Please select a rejection reason');
            return;
        }
        
        // Update or create screening record
        Screening::updateOrCreate(
            ['application_id' => $this->applicationId],
            [
                'call_id' => $this->application->call_id,
                'user_id' => Auth::id(),
                'status' => 'rejected',
                'screening_notes' => $this->screeningNotes,
                'rejection_category' => $this->rejectionCategory,
                'rejection_details' => $this->rejectionDetails,
                'eligibility_checklist' => $this->eligibilityChecklist,
                'screened_at' => now(),
                'screened_by' => Auth::id(),
            ]
        );
        
        // Update application status
        $this->application->status = 'rejected';
        $this->application->save();
        
        $this->dispatch('notify', type: 'success', message: 'Application rejected');
        $this->dispatch('applicationUpdated');
        $this->closePanel();
    }
};
?>

<div>
    <!-- REVIEW PANEL (right drawer) -->
    @if($isOpen)
    <div class="panel-backdrop" wire:click="closePanel"></div>
    
    <div class="review-panel">
        <div class="review-panel-inner">
            
            <!-- Panel Header -->
            <div class="d-flex align-items-start justify-content-between p-4 border-bottom">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        @if($application)
                            @php
                                $statusColors = [
                                    'eligible' => 'bg-success',
                                    'rejected' => 'bg-danger',
                                    'in_review' => 'bg-info',
                                    'submitted' => 'bg-warning text-dark',
                                ];
                            @endphp
                            <span class="badge rounded-pill {{ $statusColors[$application->status] ?? 'bg-secondary' }}">
                                {{ ucfirst(str_replace('_', ' ', $application->status ?? 'N/A')) }}
                            </span>
                            <span class="text-muted small">{{ $application->application_number ?? 'N/A' }}</span>
                        @else
                            <span class="badge rounded-pill bg-secondary">Loading...</span>
                        @endif
                    </div>
                    <h5 class="fw-bold mb-0">{{ $application->company_name ?? 'Loading...' }}</h5>
                    <small class="text-muted">
                        {{ $application->applicant_name ?? '' }} · 
                        {{ $application->sector ?? '' }} · 
                        {{ $application->district ?? '' }}
                    </small>
                </div>
                <button class="btn btn-sm btn-light rounded-circle p-2 lh-1" wire:click="closePanel">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <!-- Locked Banner -->
            @if($this->isLocked())
            <div class="alert alert-secondary m-3 mb-0 d-flex align-items-center gap-2">
                <i class="bi bi-lock-fill fs-5"></i>
                <div>
                    <strong>Application {{ ucfirst($application->status) }}</strong> - 
                    This application has been finalized. Document verification and eligibility checklist are locked and cannot be modified.
                </div>
            </div>
            @endif

            <!-- Progress Summary Bar -->
            <div class="px-4 py-3 bg-light border-bottom">
                <div class="row g-2 small">
                    <div class="col-6">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Documents</span>
                            <span class="fw-semibold">{{ $this->document_stats['verified'] }}/{{ $this->document_stats['total'] }}</span>
                        </div>
                        <div class="progress" style="height: 4px;">
                            <div class="progress-bar bg-success" style="width: {{ $this->document_stats['verificationProgress'] }}%"></div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Eligibility</span>
                            <span class="fw-semibold">{{ $this->checklist_stats['passed'] }}/{{ $this->checklist_stats['total'] }}</span>
                        </div>
                        <div class="progress" style="height: 4px;">
                            <div class="progress-bar bg-primary" style="width: {{ $this->checklist_stats['progress'] }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <div class="px-4 pt-3 border-bottom">
                <ul class="nav nav-tabs border-0 gap-1">
                    <li class="nav-item">
                        <button class="nav-link px-3 py-2 small fw-medium {{ $activeTab == 'overview' ? 'active' : '' }}" wire:click="$set('activeTab', 'overview')">
                            <i class="bi bi-info-circle me-1"></i>Overview
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link px-3 py-2 small fw-medium {{ $activeTab == 'documents' ? 'active' : '' }}" wire:click="$set('activeTab', 'documents')">
                            <i class="bi bi-file-earmark-text me-1"></i>Documents
                            @if($this->document_stats['verified'] < $this->document_stats['total'])
                                <span class="badge bg-warning text-dark ms-1">{{ $this->document_stats['missing'] }}</span>
                            @endif
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link px-3 py-2 small fw-medium {{ $activeTab == 'eligibility' ? 'active' : '' }}" wire:click="$set('activeTab', 'eligibility')">
                            <i class="bi bi-check2-square me-1"></i>Eligibility
                            @if($this->checklist_stats['pending'] > 0)
                                <span class="badge bg-secondary ms-1">{{ $this->checklist_stats['pending'] }}</span>
                            @endif
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link px-3 py-2 small fw-medium {{ $activeTab == 'history' ? 'active' : '' }}" wire:click="$set('activeTab', 'history')">
                            <i class="bi bi-clock-history me-1"></i>History
                        </button>
                    </li>
                </ul>
            </div>

            <!-- Tab Content -->
            <div class="p-4 overflow-auto flex-grow-1" style="max-height: calc(100vh - 280px); overflow-y: auto;">
                
                <!-- Overview Tab -->
                <div style="{{ $activeTab != 'overview' ? 'display: none;' : '' }}">
                    @if($application)
                    <!-- Business Information Section -->
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3 pb-2 border-bottom">
                            <i class="bi bi-building me-2"></i>Business Information
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="info-block p-3 bg-light rounded">
                                    <div class="info-label text-muted small mb-1">Company Name</div>
                                    <div class="info-val fw-semibold">{{ $application->company_name ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-block p-3 bg-light rounded">
                                    <div class="info-label text-muted small mb-1">Registered Company Name</div>
                                    <div class="info-val fw-semibold">{{ $application->registered_company_name ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="info-block p-3 bg-light rounded">
                                    <div class="info-label text-muted small mb-1">Business Description</div>
                                    <div class="info-val">{{ $application->business_description ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-block p-3 bg-light rounded">
                                    <div class="info-label text-muted small mb-1">Business Model</div>
                                    <div class="info-val">{{ $application->business_model ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-block p-3 bg-light rounded">
                                    <div class="info-label text-muted small mb-1">Products/Services Offering</div>
                                    <div class="info-val">{{ $application->offering ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-block p-3 bg-light rounded">
                                    <div class="info-label text-muted small mb-1">Revenue Model</div>
                                    <div class="info-val">{{ $application->revenue_model ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-block p-3 bg-light rounded">
                                    <div class="info-label text-muted small mb-1">Website</div>
                                    <div class="info-val">
                                        @if($application->website)
                                            <a href="{{ $application->website }}" target="_blank">{{ $application->website }}</a>
                                        @else
                                            N/A
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-block p-3 bg-light rounded">
                                    <div class="info-label text-muted small mb-1">Company Type</div>
                                    <div class="info-val">{{ ucfirst(str_replace('_', ' ', $application->company_type ?? 'N/A')) }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-block p-3 bg-light rounded">
                                    <div class="info-label text-muted small mb-1">Industry/Sector</div>
                                    <div class="info-val">{{ $application->industry ?? $application->sector ?? 'N/A' }}</div>
                                </div>
                            </div>
                            @if($application->industry_other_elaboration)
                            <div class="col-md-12">
                                <div class="info-block p-3 bg-light rounded">
                                    <div class="info-label text-muted small mb-1">Other Industry Elaboration</div>
                                    <div class="info-val">{{ $application->industry_other_elaboration }}</div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Location & Operations Section -->
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3 pb-2 border-bottom">
                            <i class="bi bi-geo-alt me-2"></i>Location & Operations
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="info-block p-3 bg-light rounded">
                                    <div class="info-label text-muted small mb-1">Country</div>
                                    <div class="info-val">{{ $application->country ?? 'Lesotho' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-block p-3 bg-light rounded">
                                    <div class="info-label text-muted small mb-1">District</div>
                                    <div class="info-val">{{ $application->district ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-block p-3 bg-light rounded">
                                    <div class="info-label text-muted small mb-1">Year of Establishment</div>
                                    <div class="info-val">{{ $application->year_of_establishment ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-block p-3 bg-light rounded">
                                    <div class="info-label text-muted small mb-1">Years in Operation</div>
                                    <div class="info-val">{{ $application->year_of_establishment ? date('Y') - $application->year_of_establishment : 'N/A' }} years</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-block p-3 bg-light rounded">
                                    <div class="info-label text-muted small mb-1">Business Stage</div>
                                    <div class="info-val">{{ ucfirst(str_replace('_', ' ', $application->company_stage ?? 'N/A')) }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-block p-3 bg-light rounded">
                                    <div class="info-label text-muted small mb-1">Company Size</div>
                                    <div class="info-val">{{ $application->company_size ?? 'N/A' }} employees</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Financial & Employment Section -->
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3 pb-2 border-bottom">
                            <i class="bi bi-graph-up me-2"></i>Financial & Employment
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="info-block p-3 bg-light rounded">
                                    <div class="info-label text-muted small mb-1">Number of Shareholders</div>
                                    <div class="info-val">{{ $application->number_of_shareholders ?? '0' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-block p-3 bg-light rounded">
                                    <div class="info-label text-muted small mb-1">Number of Women Shareholders</div>
                                    <div class="info-val">{{ $application->number_women_shareholders ?? '0' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-block p-3 bg-light rounded">
                                    <div class="info-label text-muted small mb-1">Number of Youth Shareholders</div>
                                    <div class="info-val">{{ $application->number_youth_shareholders ?? '0' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-block p-3 bg-light rounded">
                                    <div class="info-label text-muted small mb-1">Number of Customers</div>
                                    <div class="info-val">{{ number_format($application->number_of_customers ?? 0) }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-block p-3 bg-light rounded">
                                    <div class="info-label text-muted small mb-1">Average Monthly Sales</div>
                                    <div class="info-val">M {{ number_format($application->average_monthly_sales ?? 0, 2) }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-block p-3 bg-light rounded">
                                    <div class="info-label text-muted small mb-1">Jobs to Create (12 months)</div>
                                    <div class="info-val">{{ $application->jobs_to_create_12_months ?? '0' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Applicant Information Section -->
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3 pb-2 border-bottom">
                            <i class="bi bi-person-badge me-2"></i>Applicant Information
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="info-block p-3 bg-light rounded">
                                    <div class="info-label text-muted small mb-1">Full Name</div>
                                    <div class="info-val fw-semibold">{{ $application->applicant_name ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-block p-3 bg-light rounded">
                                    <div class="info-label text-muted small mb-1">Email Address</div>
                                    <div class="info-val">{{ $application->applicant_email ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-block p-3 bg-light rounded">
                                    <div class="info-label text-muted small mb-1">Title/Position</div>
                                    <div class="info-val">{{ $application->applicant_title ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-block p-3 bg-light rounded">
                                    <div class="info-label text-muted small mb-1">Gender</div>
                                    <div class="info-val">{{ ucfirst($application->applicant_gender ?? 'N/A') }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-block p-3 bg-light rounded">
                                    <div class="info-label text-muted small mb-1">Nationality</div>
                                    <div class="info-val">{{ $application->applicant_nationality ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-block p-3 bg-light rounded">
                                    <div class="info-label text-muted small mb-1">Contact Number</div>
                                    <div class="info-val">{{ $application->applicant_contact_number ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="info-block p-3 bg-light rounded">
                                    <div class="info-label text-muted small mb-1">About Applicant</div>
                                    <div class="info-val">{{ $application->applicant_about ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information Section -->
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3 pb-2 border-bottom">
                            <i class="bi bi-info-circle me-2"></i>Additional Information
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="info-block p-3 bg-light rounded">
                                    <div class="info-label text-muted small mb-1">Received Financial Support</div>
                                    <div class="info-val">
                                        @if($application->received_financial_support)
                                            <span class="badge bg-success">Yes</span>
                                            @if(is_array($application->received_financial_support))
                                                <div class="mt-1 small">{{ implode(', ', $application->received_financial_support) }}</div>
                                            @else
                                                <div class="mt-1 small">{{ $application->received_financial_support }}</div>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary">No</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-block p-3 bg-light rounded">
                                    <div class="info-label text-muted small mb-1">Participated in Competitions</div>
                                    <div class="info-val">
                                        @if($application->participated_competitions)
                                            <span class="badge bg-success">Yes</span>
                                            @if(is_array($application->participated_competitions))
                                                <div class="mt-1 small">{{ implode(', ', $application->participated_competitions) }}</div>
                                            @else
                                                <div class="mt-1 small">{{ $application->participated_competitions }}</div>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary">No</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="info-block p-3 bg-light rounded">
                                    <div class="info-label text-muted small mb-1">Willing to Commit to Programme</div>
                                    <div class="info-val">
                                        @if($application->willing_to_commit)
                                            <span class="badge bg-success">Yes</span>
                                        @else
                                            <span class="badge bg-danger">No</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @if($application->social_media)
                            <div class="col-md-12">
                                <div class="info-block p-3 bg-light rounded">
                                    <div class="info-label text-muted small mb-1">Business Social Media</div>
                                    <div class="info-val">
                                        @foreach($application->social_media as $platform => $url)
                                            <a href="{{ $url }}" target="_blank" class="me-2">
                                                <i class="bi bi-{{ $platform }}"></i> {{ ucfirst($platform) }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endif
                            @if($application->applicant_social_media)
                            <div class="col-md-12">
                                <div class="info-block p-3 bg-light rounded">
                                    <div class="info-label text-muted small mb-1">Applicant Social Media</div>
                                    <div class="info-val">
                                        @foreach($application->applicant_social_media as $platform => $url)
                                            <a href="{{ $url }}" target="_blank" class="me-2">
                                                <i class="bi bi-{{ $platform }}"></i> {{ ucfirst($platform) }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- PDO Target Group Flags -->
                    @if($application->applicant_gender == 'female' || 
                        ($application->year_of_establishment && (date('Y') - $application->year_of_establishment) <= 5) ||
                        ($application->number_women_shareholders ?? 0) > 0 ||
                        ($application->number_youth_shareholders ?? 0) > 0)
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3 pb-2 border-bottom">
                            <i class="bi bi-flag me-2"></i>PDO Target Group Flags
                        </h6>
                        <div class="d-flex flex-wrap gap-2">
                            @if($application->applicant_gender == 'female')
                            <span class="badge px-3 py-2" style="background-color: #ec4899; color: white;">
                                <i class="bi bi-gender-female me-1"></i>Women-owned
                            </span>
                            @endif
                            @if($application->year_of_establishment && (date('Y') - $application->year_of_establishment) <= 5)
                            <span class="badge px-3 py-2" style="background-color: #0d6efd; color: white;">
                                <i class="bi bi-rocket me-1"></i>Startup (≤5 years)
                            </span>
                            @endif
                            @if(($application->number_women_shareholders ?? 0) > 0)
                            <span class="badge px-3 py-2" style="background-color: #d63384; color: white;">
                                <i class="bi bi-people-fill me-1"></i>Women Shareholders ({{ $application->number_women_shareholders }})
                            </span>
                            @endif
                            @if(($application->number_youth_shareholders ?? 0) > 0)
                            <span class="badge px-3 py-2" style="background-color: #198754; color: white;">
                                <i class="bi bi-person-heart me-1"></i>Youth Shareholders ({{ $application->number_youth_shareholders }})
                            </span>
                            @endif
                        </div>
                    </div>
                    @endif
                    @else
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted mt-2">Loading application data...</p>
                    </div>
                    @endif
                </div>

                <!-- Documents Tab -->
                <div style="{{ $activeTab != 'documents' ? 'display: none;' : '' }}">
                    <div class="d-flex flex-column gap-3">
                        <!-- Tax Clearance Certificate -->
                        <div class="d-flex align-items-center justify-content-between p-3 rounded-3 border 
                            {{ $taxClearanceVerified ? 'border-success bg-success bg-opacity-10' : ($taxClearancePath ? 'border-warning bg-warning bg-opacity-10' : 'border-secondary bg-light') }}">
                            <div class="d-flex align-items-center gap-3">
                                <i class="bi {{ $taxClearanceVerified ? 'bi-file-earmark-check-fill text-success' : 'bi-file-earmark-text' }} fs-4"></i>
                                <div>
                                    <div class="fw-medium small">Tax Clearance Certificate</div>
                                    <div class="text-muted" style="font-size:0.72rem;">
                                        @if($taxClearancePath)
                                            <a href="{{ Storage::url($taxClearancePath) }}" target="_blank" class="text-decoration-none">
                                                {{ basename($taxClearancePath) }}
                                            </a>
                                        @else
                                            <span class="text-danger">Not uploaded</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex gap-2 align-items-center">
                                @if($taxClearancePath)
                                    <a href="{{ Storage::url($taxClearancePath) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                    <a href="{{ Storage::url($taxClearancePath) }}" download class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-download"></i>
                                    </a>
                                    <div class="form-check form-switch ms-2">
                                        <input class="form-check-input" type="checkbox" 
                                               id="tax_doc"
                                               wire:change="verifyTaxClearance($event.target.checked)"
                                               {{ $taxClearanceVerified ? 'checked' : '' }}
                                               {{ $this->isLocked() ? 'disabled' : '' }}>
                                        <label class="form-check-label small" for="tax_doc">
                                            Verified
                                        </label>
                                    </div>
                                @else
                                    <span class="badge bg-danger">Missing Required</span>
                                @endif
                            </div>
                        </div>

                        <!-- Trader's License -->
                        <div class="d-flex align-items-center justify-content-between p-3 rounded-3 border 
                            {{ $tradersLicenseVerified ? 'border-success bg-success bg-opacity-10' : ($tradersLicensePath ? 'border-warning bg-warning bg-opacity-10' : 'border-secondary bg-light') }}">
                            <div class="d-flex align-items-center gap-3">
                                <i class="bi {{ $tradersLicenseVerified ? 'bi-file-earmark-check-fill text-success' : 'bi-file-earmark-text' }} fs-4"></i>
                                <div>
                                    <div class="fw-medium small">Trader's License</div>
                                    <div class="text-muted" style="font-size:0.72rem;">
                                        @if($tradersLicensePath)
                                            <a href="{{ Storage::url($tradersLicensePath) }}" target="_blank" class="text-decoration-none">
                                                {{ basename($tradersLicensePath) }}
                                            </a>
                                        @else
                                            <span class="text-danger">Not uploaded</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex gap-2 align-items-center">
                                @if($tradersLicensePath)
                                    <a href="{{ Storage::url($tradersLicensePath) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                    <a href="{{ Storage::url($tradersLicensePath) }}" download class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-download"></i>
                                    </a>
                                    <div class="form-check form-switch ms-2">
                                        <input class="form-check-input" type="checkbox" 
                                               id="traders_doc"
                                               wire:change="verifyTradersLicense($event.target.checked)"
                                               {{ $tradersLicenseVerified ? 'checked' : '' }}
                                               {{ $this->isLocked() ? 'disabled' : '' }}>
                                        <label class="form-check-label small" for="traders_doc">
                                            Verified
                                        </label>
                                    </div>
                                @else
                                    <span class="badge bg-danger">Missing Required</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    @if(!$this->document_stats['allUploaded'])
                    <div class="alert alert-warning small mt-3 d-flex gap-2 align-items-start">
                        <i class="bi bi-exclamation-triangle-fill mt-1 flex-shrink-0"></i>
                        <span>Missing required documents. Both Tax Clearance Certificate and Trader's License are required for eligibility.</span>
                    </div>
                    @elseif(!$this->document_stats['allVerified'] && !$this->isLocked())
                    <div class="alert alert-info small mt-3 d-flex gap-2 align-items-start">
                        <i class="bi bi-info-circle-fill mt-1 flex-shrink-0"></i>
                        <span>Please review and verify both documents before marking as eligible.</span>
                    </div>
                    @endif
                </div>

                <!-- Eligibility Tab -->
                <div style="{{ $activeTab != 'eligibility' ? 'display: none;' : '' }}">
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="small fw-semibold text-muted">Eligibility Checklist</div>
                            <div class="small">
                                <span class="text-success">{{ $this->checklist_stats['passed'] }} passed</span> · 
                                <span class="text-danger">{{ $this->checklist_stats['failed'] }} failed</span> · 
                                <span class="text-muted">{{ $this->checklist_stats['pending'] }} pending</span>
                            </div>
                        </div>
                        <div class="d-flex flex-column gap-2">
                            @foreach($eligibilityChecklist as $index => $criterion)
                            <div class="d-flex align-items-start gap-3 p-3 rounded-3 border 
                                {{ $criterion['passed'] === true ? 'bg-success bg-opacity-10 border-success' : ($criterion['passed'] === false ? 'bg-danger bg-opacity-10 border-danger' : 'bg-light') }}">
                                <div class="pt-1">
                                    <input class="form-check-input" type="checkbox" 
                                        {{ $criterion['passed'] === true ? 'checked' : '' }}
                                        wire:change="updateChecklist({{ $index }}, $event.target.checked)"
                                        {{ $this->isLocked() ? 'disabled' : '' }}>
                                </div>
                                <div class="flex-grow-1">
                                    <label class="form-check-label small fw-medium">{{ $criterion['label'] }}</label>
                                    <div class="text-muted" style="font-size:0.72rem;">{{ $criterion['hint'] }}</div>
                                </div>
                                <i class="bi fs-5 flex-shrink-0 
                                    {{ $criterion['passed'] === true ? 'bi-check-circle-fill text-success' : ($criterion['passed'] === false ? 'bi-x-circle-fill text-danger' : 'bi-circle text-muted') }}">
                                </i>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-medium small">Screening Notes</label>
                        <textarea class="form-control small" rows="3" placeholder="Add notes about this application's eligibility…" wire:model="screeningNotes" {{ $this->isLocked() ? 'disabled' : '' }}></textarea>
                    </div>
                    
                    @if(!$this->checklist_stats['allPassed'] && $this->checklist_stats['passed'] > 0 && !$this->isLocked())
                    <div class="alert alert-warning small d-flex gap-2 align-items-start mb-3">
                        <i class="bi bi-exclamation-triangle-fill mt-1 flex-shrink-0"></i>
                        <span>Some eligibility criteria have not been met. Please address all requirements before marking as eligible.</span>
                    </div>
                    @endif
                    
                    <div style="{{ $showRejectionSection ? 'display: block;' : 'display: none;' }}">
                        <hr>
                        <h6 class="text-danger mb-3"><i class="bi bi-x-octagon me-1"></i>Rejection Details</h6>
                        <div class="mb-3">
                            <label class="form-label fw-medium small text-danger">Rejection Reason <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm mb-2" wire:model="rejectionCategory" {{ $this->isLocked() ? 'disabled' : '' }}>
                                <option value="">— Select primary reason —</option>
                                <option value="incomplete_docs">Incomplete / missing documents</option>
                                <option value="not_registered">Business not formally registered (No Trader's License)</option>
                                <option value="no_tax_clearance">No valid Tax Clearance Certificate</option>
                                <option value="outside_sector">Outside target sectors</option>
                                <option value="outside_geography">Outside geographic focus</option>
                                <option value="revenue_too_high">Revenue exceeds programme threshold</option>
                                <option value="duplicate">Duplicate application</option>
                                <option value="failed_checklist">Failed eligibility checklist</option>
                                <option value="other">Other (specify below)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-medium small">Additional Details</label>
                            <textarea class="form-control small" rows="2" placeholder="Provide a specific rejection reason that will be communicated to the applicant…" wire:model="rejectionDetails" {{ $this->isLocked() ? 'disabled' : '' }}></textarea>
                        </div>
                    </div>
                </div>

                <!-- History Tab -->
                <!-- History Tab -->
                <div style="{{ $activeTab != 'history' ? 'display: none;' : '' }}">
                    <div class="timeline">
                        @if($screening && $screening->status)
                        <div class="timeline-item d-flex gap-3 mb-3">
                            <div class="timeline-dot bg-{{ $screening->status == 'eligible' ? 'success' : ($screening->status == 'rejected' ? 'danger' : 'info') }} rounded-circle" style="width: 10px; height: 10px; margin-top: 6px;"></div>
                            <div>
                                <div class="fw-semibold small">Marked {{ ucfirst($screening->status) }}</div>
                                <div class="text-muted" style="font-size:0.72rem;">
                                    {{ $screening->screenedBy->name ?? $screening->screenedBy->username ?? 'System' }} · 
                                    {{ $screening->screened_at ? $screening->screened_at->format('d M Y H:i') : 'N/A' }}
                                </div>
                                @if($screening->screening_notes)
                                <div class="text-dark small mt-1 bg-light p-2 rounded">{{ $screening->screening_notes }}</div>
                                @endif
                                @if($screening->status == 'rejected' && $screening->rejection_category)
                                <div class="text-danger small mt-1">
                                    <strong>Rejection reason:</strong> {{ ucfirst(str_replace('_', ' ', $screening->rejection_category)) }}
                                    @if($screening->rejection_details)
                                    <br>{{ $screening->rejection_details }}
                                    @endif
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif
                        
                        @if($application)
                        <div class="timeline-item d-flex gap-3">
                            <div class="timeline-dot bg-primary rounded-circle" style="width: 10px; height: 10px; margin-top: 6px;"></div>
                            <div>
                                <div class="fw-semibold small">Application Submitted</div>
                                <div class="text-muted" style="font-size:0.72rem;">
                                    {{ $application->applicant_name ?? 'Applicant' }} · 
                                    {{ $application->submitted_at ? $application->submitted_at->format('d M Y H:i') : 'Date not available' }}
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Panel Footer -->
            <div class="p-4 border-top bg-light d-flex flex-wrap gap-2 justify-content-between align-items-center">
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-secondary" wire:click="closePanel">
                        <i class="bi bi-chevron-left me-1"></i>Close
                    </button>
                    <small class="text-muted align-self-center">Application Details</small>
                </div>
                <div class="d-flex gap-2">
                    @if($this->isLocked())
                        <div class="text-muted">
                            <i class="bi bi-lock-fill me-1"></i>
                            <strong>Finalized - No changes allowed</strong>
                        </div>
                    @else
                        @if($showRejectionSection)
                            <button class="btn btn-sm btn-danger px-3" 
                                    wire:click="submitRejection"
                                    wire:confirm="⚠️ WARNING: This action will mark the application as REJECTED.\n\nOnce rejected, this decision CANNOT be changed.\n\nAre you absolutely sure you want to reject this application?">
                                <i class="bi bi-send me-1"></i>Confirm Rejection
                            </button>
                            <button class="btn btn-sm btn-outline-secondary" wire:click="$set('showRejectionSection', false)">
                                <i class="bi bi-arrow-left me-1"></i>Back
                            </button>
                        @else
                            <button class="btn btn-sm btn-outline-danger px-3" 
                                    wire:click="confirmRejection"
                                    wire:confirm="⚠️ WARNING: You are about to reject this application.\n\nPlease ensure you have reviewed all documents and checklist items.\n\nOnce rejected, this decision will be final and cannot be changed.\n\nDo you want to continue?">
                                <i class="bi bi-x-circle me-1"></i>Reject
                            </button>
                            <button class="btn btn-sm btn-success px-3" 
                                    wire:click="markAsEligible"
                                    wire:confirm="✅ IMPORTANT DECISION: You are about to mark this application as ELIGIBLE.\n\nThis means:\n• The applicant will be notified of their eligibility\n• The application will move to the next stage\n• This decision will be recorded in the system\n\nOnce marked eligible, you cannot change this decision.\n\nPlease verify all documents and checklist items are complete.\n\nAre you sure you want to mark this application as ELIGIBLE?"
                                    @if(!$this->document_stats['allVerified'] || !$this->checklist_stats['allPassed']) disabled @endif>
                                <i class="bi bi-check-circle me-1"></i>Mark Eligible
                            </button>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
