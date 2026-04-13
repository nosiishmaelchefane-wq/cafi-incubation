<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Call;
use App\Models\IncubationApplication;

new class extends Component
{
    use WithPagination;
    
    // Call Data
    public $call;
    public $callId;
    
    // Application Data
    public $applications = [];
    
    // Filter properties
    public $search = '';
    public $sectorFilter = '';
    public $districtFilter = '';
    public $stageFilter = '';
    public $statusFilter = '';
    public $perPage = 10;
    
    // Bulk actions
    public $selectedApplications = [];
    public $selectAll = false;
    
    // Statistics
    public $applicationsCount = 0;
    public $screenedCount = 0;
    public $shortlistedCount = 0;
    public $eligibleCount = 0;
    public $inReviewCount = 0;
    public $rejectedCount = 0;
    public $pendingCount = 0;
    
    
    public function mount($id = null)
    {
        if ($id) {
            $this->callId = $id;
            $this->loadCallData();
             $this->loadApplications();
        }
    }
    
    public function loadCallData()
    {
        $this->call = Call::with('publisher')->findOrFail($this->callId);
        
        // Calculate days remaining if call is open
        if ($this->call->status === 'open' && $this->call->close_date) {
            $this->call->days_remaining = now()->diffInDays($this->call->close_date, false);
            if ($this->call->days_remaining < 0) {
                $this->call->days_remaining = 0;
            }
        } else {
            $this->call->days_remaining = null;
        }
        
    }
    
    
    public function calculatePipeline()
    {
        $this->pipeline = [
            ['label' => 'Submitted', 'count' => $this->applicationsCount],
            ['label' => 'Screened', 'count' => $this->screenedCount],
            ['label' => 'Eligible', 'count' => $this->eligibleCount],
            ['label' => 'Evaluated', 'count' => $this->inReviewCount],
            ['label' => 'Top 20', 'count' => min(20, $this->shortlistedCount)],
            ['label' => 'Top 10', 'count' => min(10, $this->shortlistedCount)],
            ['label' => 'Confirmed', 'count' => 0],
        ];
    }
    
    public function updateApplicationStatus($applicationId, $newStatus)
    {
        $application = Application::findOrFail($applicationId);
        $application->status = $newStatus;
        $application->save();
        
        // Recalculate all statistics
      //  $this->calculateStatistics();
        $this->calculatePipeline();
        $this->dispatch(
            'notify',
            type: 'success',
            message: 'Application moved to ' . ucfirst(str_replace('_', ' ', $newStatus))
        );
 
        // Refresh the applications list
        $this->loadApplications();
    }
    

    
    public function publishCall()
    {
        $this->call->publish(auth()->id());
        $this->call->status = 'published';
        $this->dispatch('notify', type: 'success', message: 'Call published successfully!');
        $this->loadCallData();
    }
    
    public function openCall()
    {
        $this->call->open();
        $this->call->status = 'open';
        $this->dispatch('notify', type: 'success', message: 'Call is now open for applications');
        $this->loadCallData();
    }
    
    public function closeCall()
    {
        $this->call->close();
        $this->call->status = 'closed';
        $this->dispatch('notify', type: 'success', message: 'Call has been closed');
        $this->loadCallData();
    }

    
    public function getStatusCountsProperty()
    {
        return [
            'all' => $this->applicationsCount,
            'pending' => $this->pendingCount,
            'in_review' => $this->inReviewCount,
            'eligible' => $this->eligibleCount,
            'shortlisted' => $this->shortlistedCount,
            'rejected' => $this->rejectedCount,
        ];
    }


    public function loadApplications()
    {
        // Get total applications count for this call
        $this->applicationsCount = IncubationApplication::where('call_id', $this->callId)->count();
        
        // Get counts by status
        $this->pendingCount = IncubationApplication::where('call_id', $this->callId)
            ->where('status', 'pending')
            ->count();
        
        $this->inReviewCount = IncubationApplication::where('call_id', $this->callId)
            ->where('status', 'in_review')
            ->count();
        
        $this->eligibleCount = IncubationApplication::where('call_id', $this->callId)
            ->where('status', 'eligible')
            ->count();
        
        $this->shortlistedCount = IncubationApplication::where('call_id', $this->callId)
            ->where('status', 'shortlisted')
            ->count();
        
        $this->rejectedCount = IncubationApplication::where('call_id', $this->callId)
            ->where('status', 'rejected')
            ->count();
        
        // Screened count (applications that have been reviewed)
        $this->screenedCount = IncubationApplication::where('call_id', $this->callId)
            ->whereIn('status', ['eligible', 'in_review', 'shortlisted', 'rejected'])
            ->count();
    }
    
    public function getEligibilityLinesProperty()
    {
        if ($this->call && $this->call->eligibility) {
            return explode("\n", $this->call->eligibility);
        }
        return [];
    }
    
    public function getInitials($name)
    {
        $words = explode(' ', $name);
        $initials = '';
        foreach (array_slice($words, 0, 2) as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
        }
        return $initials;
    }
    
    public function getAvColor($sector)
    {
        $colors = [
            'Agriculture' => 'av-green',
            'Technology' => 'av-blue',
            'Textile' => 'av-pink',
            'Manufacturing' => 'av-orange',
            'Food & Beverage' => 'av-teal',
        ];
        return $colors[$sector] ?? 'av-purple';
    }
    
    public function getScoreColor($score)
    {
        if ($score === null) return '';
        return $score >= 70 ? 'text-success' : ($score >= 50 ? 'text-warning' : 'text-danger');
    }
    
    public function getAppStatusClass($status)
    {
        $classes = [
            'pending' => 'st-pending',
            'in_review' => 'st-in-review',
            'eligible' => 'st-eligible',
            'shortlisted' => 'st-shortlisted',
            'rejected' => 'st-rejected',
        ];
        return $classes[$status] ?? '';
    }
}
?>

<div x-data="callDetailApp()" x-init="init()" class="cds p-4">
   

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb mb-0 small">
            <li class="breadcrumb-item">
                <a href="" class="text-decoration-none text-muted">
                    <i class="bi bi-house-fill me-1" style="font-size:0.7rem;"></i> Home
                </a>
            </li>
            <li class="breadcrumb-item"><a href="" class="text-decoration-none text-muted">Calls</a></li>
            <li class="breadcrumb-item active text-dark fw-medium">{{ $call->title ?? 'Loading...' }}</li>
        </ol>
    </nav>

    @if($call)
    <!-- Hero Header -->
    <div class="cds-hero mb-4">
        <div class="cds-hero-inner">
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <span class="cds-status-pill pill-{{ strtolower(str_replace(' ', '-', $call->status)) }}">
                        {{ ucfirst($call->status) }}
                    </span>
                    <span class="cds-meta-tag">
                        <i class="bi bi-layers-fill me-1"></i>Cohort {{ $call->cohort }}
                    </span>
                    <span class="cds-meta-tag">
                        <i class="bi bi-calendar3 me-1"></i>{{ $call->year ?? date('Y', strtotime($call->created_at)) }}
                    </span>
                    @if($call->allow_late_submissions)
                        <span class="cds-meta-tag">
                            <i class="bi bi-clock-history me-1 text-warning"></i>Late submissions allowed
                        </span>
                    @endif
                </div>
                <div class="d-flex gap-2 flex-wrap">
                     @if(auth()->check() && auth()->user()->hasRole('Super Administrator'))

                        @if($call->status === 'draft')
                            <button class="btn cds-btn-outline-success btn-sm"
                                    onclick="if(confirm('Are you sure you want to publish this call? It will become visible to applicants.')) { @this.publishCall({{ $call->id }}) }">
                                <i class="bi bi-broadcast me-1"></i>
                                Publish
                            </button>
                        @endif

                        @if($call->status === 'published')
                            <button class="btn cds-btn-outline-success btn-sm"
                                    onclick="if(confirm('Are you sure you want to open this call? Applications will be accepted.')) { @this.openCall({{ $call->id }}) }">
                                <i class="bi bi-play-fill me-1"></i>
                                Open Call
                            </button>
                        @endif

                        @if($call->status === 'open')
                            <button class="btn cds-btn-outline-warning btn-sm"
                                    onclick="if(confirm('Are you sure you want to close this call? Applications will no longer be accepted.')) { @this.closeCall({{ $call->id }}) }">
                                <i class="bi bi-lock me-1"></i>
                                Close Call
                            </button>
                        @endif

                        @if($call->status === 'open')
                            <button class="btn cds-btn-primary btn-sm" wire:click="$dispatch('edit-call', { id: {{ $call->id }} })">
                                <i class="bi bi-pencil-fill me-1"></i>Edit Call
                            </button>
                        @endif

                    @endif
                    @if($call->status === 'open')
                        @if(auth()->check() && auth()->user()->hasRole('Applicant'))
                            <button class="btn cds-btn-primary btn-sm" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#incubationApplicationModal">
                                <i class="bi bi-link-45deg me-1"></i> Apply
                            </button>
                        @endif
                    @endif
               
                </div>
            </div>

            <h2 class="cds-hero-title mb-2">{{ $call->title }}</h2>
            <p class="cds-hero-desc mb-3">{{ $call->description }}</p>

            <div class="d-flex flex-wrap gap-4 cds-hero-meta">
                <span><i class="bi bi-person-fill me-1"></i>Published by <strong>{{ $call->publisher->username ?? 'Not published yet' }}</strong></span>
                <span><i class="bi bi-clock-fill me-1"></i>{{ $call->published_at ? $call->published_at->format('d M Y, H:i') : 'Not published' }}</span>
                <span><i class="bi bi-geo-alt-fill me-1"></i>{{ $call->geography ?? 'All Districts' }}</span>
                <span><i class="bi bi-hourglass-split me-1"></i>{{ $call->duration_months }}-month programme</span>
            </div>

            <div class="cds-hero-divider my-4"></div>

            <!-- KPI Cards -->
            <div class="row g-3">
                <div class="col-6 col-md-3">
                    <div class="cds-kpi">
                        <div class="cds-kpi-icon" style="background:rgba(59,130,246,0.12); color:#3b82f6;">
                            <i class="bi bi-file-earmark-text-fill"></i>
                        </div>
                        <div>
                            <div class="cds-kpi-val text-primary">{{ $applicationsCount }}</div>
                            <div class="cds-kpi-label">Applications</div>
                            <div class="cds-kpi-sub">Received</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="cds-kpi">
                        <div class="cds-kpi-icon" style="background:rgba(16,185,129,0.12); color:#10b981;">
                            <i class="bi bi-calendar-check-fill"></i>
                        </div>
                        <div>
                            <div class="cds-kpi-val text-success">
                                @if($call->status === 'open' && $call->days_remaining)
                                    {{ (int) $call->days_remaining }}d
                                @else
                                    —
                                @endif
                            </div>
                            <div class="cds-kpi-label">
                                {{ $call->status === 'open' ? 'Days Remaining' : 'Window Closed' }}
                            </div>
                            <div class="cds-kpi-sub">Closes {{ $call->close_date ? $call->close_date->format('d M Y') : 'N/A' }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="cds-kpi">
                        <div class="cds-kpi-icon" style="background:rgba(245,158,11,0.12); color:#f59e0b;">
                            <i class="bi bi-funnel-fill"></i>
                        </div>
                        <div>
                            <div class="cds-kpi-val text-warning">{{ $screenedCount }}</div>
                            <div class="cds-kpi-label">Screened</div>
                            <div class="cds-kpi-sub">of total submitted</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="cds-kpi">
                        <div class="cds-kpi-icon" style="background:rgba(139,92,246,0.12); color:#8b5cf6;">
                            <i class="bi bi-trophy-fill"></i>
                        </div>
                        <div>
                            <div class="cds-kpi-val" style="color:#8b5cf6;">{{ $shortlistedCount }}</div>
                            <div class="cds-kpi-label">Shortlisted</div>
                            <div class="cds-kpi-sub">proceeding to evaluation</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    @if(auth()->check() && auth()->user()->hasRole('Super Administrator'))
        <!-- PIPELINE STRIP (static) -->
        <div class="cds-card mb-4">
            <div class="cds-card-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <div class="cds-icon-sm"><i class="bi bi-funnel-fill"></i></div>
                    <span class="fw-semibold">Application Pipeline</span>
                </div>
                <span class="cds-badge-muted small">Real-time stage counts</span>
            </div>
            <div class="cds-card-body px-4 py-3">
                <div class="d-flex align-items-center gap-1 overflow-auto pb-1">
                    <div class="d-flex align-items-center gap-1 flex-shrink-0">
                        <div class="cds-pipe-step cds-pipe-active">
                            <div class="cds-pipe-num">210</div>
                            <div class="cds-pipe-name">Submitted</div>
                        </div>
                        <i class="bi bi-chevron-right cds-pipe-arrow flex-shrink-0"></i>
                    </div>
                    <div class="d-flex align-items-center gap-1 flex-shrink-0">
                        <div class="cds-pipe-step cds-pipe-active">
                            <div class="cds-pipe-num">145</div>
                            <div class="cds-pipe-name">Screened</div>
                        </div>
                        <i class="bi bi-chevron-right cds-pipe-arrow flex-shrink-0"></i>
                    </div>
                    <div class="d-flex align-items-center gap-1 flex-shrink-0">
                        <div class="cds-pipe-step cds-pipe-active">
                            <div class="cds-pipe-num">98</div>
                            <div class="cds-pipe-name">Eligible</div>
                        </div>
                        <i class="bi bi-chevron-right cds-pipe-arrow flex-shrink-0"></i>
                    </div>
                    <div class="d-flex align-items-center gap-1 flex-shrink-0">
                        <div class="cds-pipe-step cds-pipe-active">
                            <div class="cds-pipe-num">60</div>
                            <div class="cds-pipe-name">Evaluated</div>
                        </div>
                        <i class="bi bi-chevron-right cds-pipe-arrow flex-shrink-0"></i>
                    </div>
                    <div class="d-flex align-items-center gap-1 flex-shrink-0">
                        <div class="cds-pipe-step cds-pipe-active">
                            <div class="cds-pipe-num">20</div>
                            <div class="cds-pipe-name">Top 20</div>
                        </div>
                        <i class="bi bi-chevron-right cds-pipe-arrow flex-shrink-0"></i>
                    </div>
                    <div class="d-flex align-items-center gap-1 flex-shrink-0">
                        <div class="cds-pipe-step cds-pipe-zero">
                            <div class="cds-pipe-num">0</div>
                            <div class="cds-pipe-name">Top 10</div>
                        </div>
                        <i class="bi bi-chevron-right cds-pipe-arrow flex-shrink-0"></i>
                    </div>
                    <div class="d-flex align-items-center gap-1 flex-shrink-0">
                        <div class="cds-pipe-step cds-pipe-zero">
                            <div class="cds-pipe-num">0</div>
                            <div class="cds-pipe-name">Confirmed</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif


    <!-- Main 2-Column Layout -->
    <div class="row g-4 mb-4">
        <!-- Left Column -->
        <div class="col-12 col-xl-8">
            <div class="cds-card mb-4">
                <div class="cds-card-header">
                    <div class="d-flex align-items-center gap-2">
                        <div class="cds-icon-sm"><i class="bi bi-file-earmark-text-fill"></i></div>
                        <span class="fw-semibold">Programme Details &amp; Guidelines</span>
                    </div>
                </div>
                <div class="cds-card-body p-4">
                    <div class="cds-prose" style="white-space:pre-line;">{{ $call->details }}</div>
                </div>
            </div>
            
            <div class="cds-card mb-4">
                <div class="cds-card-header">
                    <div class="d-flex align-items-center gap-2">
                        <div class="cds-icon-sm" style="background:rgba(16,185,129,0.12); color:#10b981;">
                            <i class="bi bi-check2-circle"></i>
                        </div>
                        <span class="fw-semibold">Eligibility Criteria</span>
                    </div>
                </div>
                <div class="cds-card-body p-4">
                    <div class="cds-eligibility-list">
                        @foreach($this->eligibilityLines as $line)
                            @if(trim($line))
                                <div class="d-flex align-items-start gap-2 cds-eligibility-item">
                                    <i class="bi bi-check-circle-fill mt-1 flex-shrink-0" style="color:#10b981; font-size:0.8rem;"></i>
                                    <span class="small">{{ preg_replace('/^[•\-]\s*/', '', $line) }}</span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-12 col-xl-4">
            <div class="cds-card mb-4">
                <div class="cds-card-header">
                    <div class="d-flex align-items-center gap-2">
                        <div class="cds-icon-sm"><i class="bi bi-info-circle-fill"></i></div>
                        <span class="fw-semibold">Call Details</span>
                    </div>
                </div>
                <div class="cds-card-body p-0">
                    <dl class="cds-dl">
                        <div class="cds-dl-row">
                            <dt>Status</dt>
                            <dd><span class="cds-status-pill cds-status-pill-sm pill-{{ strtolower(str_replace(' ', '-', $call->status)) }}">{{ ucfirst($call->status) }}</span></dd>
                        </div>
                        <div class="cds-dl-row">
                            <dt>Cohort</dt>
                            <dd class="fw-semibold">Cohort {{ $call->cohort }}</dd>
                        </div>
                        <div class="cds-dl-row">
                            <dt>Target Applications</dt>
                            <dd class="fw-semibold">{{ $call->target_applications }}</dd>
                        </div>
                        <div class="cds-dl-row">
                            <dt>Publish Date</dt>
                            <dd>{{ $call->publish_date ? $call->publish_date->format('d M Y') : '—' }}</dd>
                        </div>
                        <div class="cds-dl-row">
                            <dt>Window Opens</dt>
                            <dd class="fw-semibold text-success">{{ $call->open_date ? $call->open_date->format('d M Y') : '—' }}</dd>
                        </div>
                        <div class="cds-dl-row">
                            <dt>Window Closes</dt>
                            <dd class="fw-semibold text-danger">{{ $call->close_date ? $call->close_date->format('d M Y') : '—' }}</dd>
                        </div>
                        <div class="cds-dl-row">
                            <dt>Duration</dt>
                            <dd>{{ $call->duration_months }} months</dd>
                        </div>
                        <div class="cds-dl-row">
                            <dt>Late Submissions</dt>
                            <dd :class="call.allowLate ? 'text-warning fw-semibold' : 'text-muted'" class="{{ $call->allow_late_submissions ? 'text-warning fw-semibold' : 'text-muted' }}">
                                {{ $call->allow_late_submissions ? 'Allowed' : 'Not allowed' }}
                            </dd>
                        </div>
                        <div class="cds-dl-row">
                            <dt>Geography</dt>
                            <dd>{{ $call->geography ?? 'All Districts' }}</dd>
                        </div>
                        <div class="cds-dl-row">
                            <dt>Created</dt>
                            <dd>{{ $call->created_at->format('d M Y') }}</dd>
                        </div>
                        <div class="cds-dl-row border-0">
                            <dt>Last Updated</dt>
                            <dd>{{ $call->updated_at->format('d M Y') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <div class="cds-card mb-4">
                <div class="cds-card-header">
                    <div class="d-flex align-items-center gap-2">
                        <div class="cds-icon-sm"><i class="bi bi-diagram-3-fill"></i></div>
                        <span class="fw-semibold">Target Sectors</span>
                    </div>
                </div>
                <div class="cds-card-body p-4">
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($call->sectors ?? [] as $sector)
                            <span class="cds-sector-tag">
                                <i class="bi bi-tag-fill me-1" style="font-size:0.65rem;"></i>
                                {{ $sector }}
                            </span>
                        @endforeach
                        @if(empty($call->sectors))
                            <span class="text-muted small">All sectors accepted</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @endif
</div>