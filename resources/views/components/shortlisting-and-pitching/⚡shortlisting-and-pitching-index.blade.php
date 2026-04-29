<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\IncubationApplication;
use App\Models\EvaluationScore;
use App\Models\Cohort;
use App\Models\Call;
use App\Models\Shortlist;
use Illuminate\Support\Facades\Auth;

new class extends Component
{
    use WithPagination;

    public $selectedCohort = null;
    public $selectedCall = null;
    public $perPage = 10;
    public $search = '';
    public $industryFilter = '';
    public $shortlistingConfirmed = false;
    public $showStepperLegend = false;
    
    protected $queryString = [
        'selectedCohort' => ['except' => null],
        'selectedCall' => ['except' => null],
        'search' => ['except' => ''],
        'industryFilter' => ['except' => ''],
    ];

    public function mount()
    {
        // Get the latest cohort by default
        $latestCohort = Cohort::orderBy('year', 'desc')->orderBy('cohort_number', 'desc')->first();
        if ($latestCohort) {
            $this->selectedCohort = $latestCohort->id;
            
            // Get the latest call for this cohort
            $latestCall = Call::where('cohort', $latestCohort->cohort_number)
                ->orderBy('created_at', 'desc')
                ->first();
            if ($latestCall) {
                $this->selectedCall = $latestCall->id;
            }
        }
        
        // Check if shortlisting is already confirmed for this call
        $this->checkShortlistingStatus();
    }
    
    /**
     * Toggle stepper legend visibility
     */
    public function toggleStepperLegend()
    {
        $this->showStepperLegend = !$this->showStepperLegend;
    }
    
    /**
     * Check if shortlisting has been confirmed for this call
     */
    public function checkShortlistingStatus()
    {
        if ($this->selectedCall) {
            $shortlist = Shortlist::where('call_id', $this->selectedCall)->first();
            $this->shortlistingConfirmed = $shortlist && $shortlist->status === 'confirmed';
        }
    }
    
    /**
     * Confirm the Top 20 shortlist
     */
    public function confirmShortlist()
    {
        if (!$this->selectedCall) {
            $this->dispatch('notify', type: 'error', message: 'No call selected');
            return;
        }
        
        $applications = $this->applications;
        if ($applications->count() == 0) {
            $this->dispatch('notify', type: 'error', message: 'No applications to shortlist');
            return;
        }
        
        // Create or update shortlist record
        $shortlist = Shortlist::updateOrCreate(
            ['call_id' => $this->selectedCall],
            [
                'cohort_id' => $this->selectedCohort,
                'applications_count' => $applications->count(),
                'status' => 'confirmed',
                'confirmed_by' => Auth::id(),
                'confirmed_at' => now(),
            ]
        );
        
        // Update application status to 'shortlisted' for top 20
        foreach ($applications as $app) {
            $app->status = 'shortlisted';
            $app->save();
        }
        
        $this->shortlistingConfirmed = true;
        $this->dispatch('notify', type: 'success', message: 'Top 20 shortlist confirmed successfully! Pitch scheduling is now available.');
    }
    
    /**
     * Unconfirm the Top 20 shortlist (revert to draft)
     */
    public function unconfirmShortlist()
    {
        if (!$this->selectedCall) {
            $this->dispatch('notify', type: 'error', message: 'No call selected');
            return;
        }
        
        $shortlist = Shortlist::where('call_id', $this->selectedCall)->first();
        
        if ($shortlist) {
            $shortlist->status = 'draft';
            $shortlist->confirmed_by = null;
            $shortlist->confirmed_at = null;
            $shortlist->save();
        }
        
        // Update application status back to 'eligible' for shortlisted apps
        $applications = IncubationApplication::where('call_id', $this->selectedCall)
            ->where('status', 'shortlisted')
            ->get();
        
        foreach ($applications as $app) {
            $app->status = 'eligible';
            $app->save();
        }
        
        $this->shortlistingConfirmed = false;
        $this->dispatch('notify', type: 'warning', message: 'Shortlist has been unpublished and moved back to draft.');
    }

    /**
     * Get the Top 20 applications based on evaluation scores
     */
    public function getApplicationsProperty()
    {
        if (!$this->selectedCall) {
            return collect();
        }
        
        $query = IncubationApplication::query()
            ->with(['user', 'call', 'evaluationScores'])
            ->where('call_id', $this->selectedCall);
        
        if ($this->shortlistingConfirmed) {
            $query->where('status', 'shortlisted');
        } else {
            $query->where('status', 'eligible');
            $query->whereHas('evaluationScores', function($q) {
                $q->where('status', 'submitted');
            });
        }
        
        if ($this->search) {
            $query->where(function($q) {
                $q->where('company_name', 'like', '%' . $this->search . '%')
                    ->orWhere('application_number', 'like', '%' . $this->search . '%');
            });
        }
        
        if ($this->industryFilter) {
            $query->where('industry', $this->industryFilter);
        }
        
        $applications = $query->get();
        
        foreach ($applications as $app) {
            $avgScore = $app->evaluationScores()
                ->where('status', 'submitted')
                ->avg('total_score');
            $app->average_score = round($avgScore);
        }
        
        if (!$this->shortlistingConfirmed) {
            $topApplications = $applications->sortByDesc('average_score')->take(20);
        } else {
            $topApplications = $applications->sortByDesc('average_score');
        }
        
        $rank = 1;
        foreach ($topApplications as $app) {
            $app->rank = $rank++;
        }
        
        return $topApplications;
    }

    public function getCohortsProperty()
    {
        return Cohort::orderBy('year', 'desc')->orderBy('cohort_number', 'desc')->get();
    }

    public function getCallsProperty()
    {
        if (!$this->selectedCohort) {
            return collect();
        }
        
        $cohort = Cohort::find($this->selectedCohort);
        if (!$cohort) {
            return collect();
        }
        
        return Call::where('cohort', $cohort->cohort_number)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getStatsProperty()
    {
        if (!$this->selectedCall) {
            return [
                'total_evaluated' => 0,
                'top_20_count' => 0,
                'pitched_count' => 0,
                'top_10_confirmed' => 0,
                'dd_passed' => 0,
                'dd_failed' => 0,
                'final_accepted' => 0,
            ];
        }
        
        $totalEvaluated = IncubationApplication::where('call_id', $this->selectedCall)
            ->where('status', 'eligible')
            ->whereHas('evaluationScores', function($q) {
                $q->where('status', 'submitted');
            })
            ->count();
        
        $top20Count = Shortlist::where('call_id', $this->selectedCall)
            ->where('status', 'confirmed')
            ->value('applications_count') ?? min($totalEvaluated, 20);
        
        $pitchedCount = IncubationApplication::where('call_id', $this->selectedCall)
            ->where('status', 'shortlisted')
            ->whereNotNull('pitch_scheduled_at')
            ->count();
        
        return [
            'total_evaluated' => $totalEvaluated,
            'top_20_count' => $top20Count,
            'pitched_count' => $pitchedCount,
            'top_10_confirmed' => 0,
            'dd_passed' => 0,
            'dd_failed' => 0,
            'final_accepted' => 0,
        ];
    }

    public function getIndustriesProperty()
    {
        if (!$this->selectedCall) {
            return collect();
        }
        
        return IncubationApplication::where('call_id', $this->selectedCall)
            ->whereNotNull('industry')
            ->distinct()
            ->pluck('industry');
    }

    public function updatedSelectedCohort()
    {
        $this->selectedCall = null;
        $this->resetPage();
        
        if ($this->calls->isNotEmpty()) {
            $this->selectedCall = $this->calls->first()->id;
            $this->checkShortlistingStatus();
        }
    }

    public function updatedSelectedCall()
    {
        $this->resetPage();
        $this->checkShortlistingStatus();
    }
    
    public function updatedSearch()
    {
        $this->resetPage();
    }
    
    public function updatedIndustryFilter()
    {
        $this->resetPage();
    }
    
    public function resetFilters()
    {
        $this->search = '';
        $this->industryFilter = '';
        $this->resetPage();
    }
    
    public function schedulePitch($applicationId)
    {
        $this->dispatch('notify', type: 'info', message: 'Pitch scheduling will be available soon');
    }
    
    public function scorePitch($applicationId)
    {
        $this->dispatch('notify', type: 'info', message: 'Pitch scoring will be available soon');
    }
    
    public function startDueDiligence($applicationId)
    {
        $this->dispatch('notify', type: 'info', message: 'Due diligence will be available soon');
    }
    
    public function viewApplication($applicationId)
    {
        $this->dispatch('notify', type: 'info', message: 'Application details will be available soon');
    }
};
?>

<div>
    <div class="sl-page p-4">

    {{-- BREADCRUMB --}}
    <nav aria-label="breadcrumb" class="mb-2">
        <ol class="breadcrumb mb-0 small">
            <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-muted"><i class="bi bi-house-fill" style="font-size:.7rem"></i> Home</a></li>
            <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-muted">Incubation</a></li>
            <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-muted">Applications</a></li>
            <li class="breadcrumb-item active fw-semibold">Shortlisting &amp; Pitches</li>
        </ol>
    </nav>

    {{-- COHORT & CALL SELECTORS --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <label class="form-label small fw-medium mb-1">Cohort</label>
            <select class="form-select form-select-sm" wire:model.live="selectedCohort">
                <option value="">Select Cohort</option>
                @foreach($this->cohorts as $cohort)
                    <option value="{{ $cohort->id }}">{{ $cohort->name }} ({{ $cohort->year }})</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label small fw-medium mb-1">Call for Applications</label>
            <select class="form-select form-select-sm" wire:model.live="selectedCall" {{ $this->calls->isEmpty() ? 'disabled' : '' }}>
                <option value="">Select Call</option>
                @foreach($this->calls as $call)
                    <option value="{{ $call->id }}">{{ $call->title }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- PAGE HEADER --}}
    <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="bi bi-trophy-fill text-warning me-2"></i>Shortlisting &amp; Pitches
            </h4>
            <p class="text-muted small mb-0">
                Manage Top 20 shortlist, pitch schedule, scores and due diligence
            </p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <button class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-download me-1"></i>Export
            </button>
            <button class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-envelope me-1"></i>Send Pitch Invitations
            </button>
        </div>
    </div>

    {{-- WORKFLOW PROGRESS STEPPER WITH LEGEND --}}
    <div class="card border-0 shadow-sm mb-3 p-0">
        <div class="d-flex align-items-stretch">
            <!-- Step 1: Top 20 Shortlist -->
            <div class="flex-grow-1 p-3 text-center border-end stepper-step 
                {{ $shortlistingConfirmed ? 'step-completed' : ($this->stats['top_20_count'] > 0 ? 'step-active' : 'step-pending') }}">
                <div class="d-flex align-items-center justify-content-center gap-2 mb-1">
                    <div class="rounded-circle d-flex align-items-center justify-content-center step-icon" style="width: 28px; height: 28px; font-size: 14px;">
                        <i class="bi {{ $shortlistingConfirmed ? 'bi-check2' : 'bi-list-check' }}"></i>
                    </div>
                    <span class="fw-semibold small">Top 20 Shortlist</span>
                </div>
                <div class="small text-muted">Confirm ranked shortlist</div>
                <div class="fw-bold step-count">{{ $this->stats['top_20_count'] }} apps</div>
            </div>
            
            <!-- Step 2: Pitch Event -->
            <div class="flex-grow-1 p-3 text-center border-end stepper-step 
                {{ $shortlistingConfirmed ? 'step-active' : 'step-pending' }}">
                <div class="d-flex align-items-center justify-content-center gap-2 mb-1">
                    <div class="rounded-circle d-flex align-items-center justify-content-center step-icon" style="width: 28px; height: 28px; font-size: 14px;">
                        <i class="bi bi-mic-fill"></i>
                    </div>
                    <span class="fw-semibold small">Pitch Event</span>
                </div>
                <div class="small text-muted">Schedule & score pitches</div>
                <div class="fw-bold step-count">{{ $this->stats['top_20_count'] }} apps</div>
            </div>
            
            <!-- Step 3: Due Diligence -->
            <div class="flex-grow-1 p-3 text-center border-end stepper-step step-pending">
                <div class="d-flex align-items-center justify-content-center gap-2 mb-1">
                    <div class="rounded-circle d-flex align-items-center justify-content-center step-icon" style="width: 28px; height: 28px; font-size: 14px;">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <span class="fw-semibold small">Due Diligence</span>
                </div>
                <div class="small text-muted">DD checks for Top 10</div>
                <div class="fw-bold step-count">0 apps</div>
            </div>
            
            <!-- Step 4: Final Cohort -->
            <div class="flex-grow-1 p-3 text-center stepper-step step-pending">
                <div class="d-flex align-items-center justify-content-center gap-2 mb-1">
                    <div class="rounded-circle d-flex align-items-center justify-content-center step-icon" style="width: 28px; height: 28px; font-size: 14px;">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <span class="fw-semibold small">Final Cohort</span>
                </div>
                <div class="small text-muted">Confirm final 10 enterprises</div>
                <div class="fw-bold step-count">0 apps</div>
            </div>
        </div>
        
        <!-- Stepper Legend Toggle Button -->
        <div class="border-top px-3 py-2 bg-light d-flex justify-content-between align-items-center">
            <button class="btn btn-sm btn-link text-muted" wire:click="toggleStepperLegend">
                <i class="bi bi-info-circle me-1"></i>
                {{ $showStepperLegend ? 'Hide Legend' : 'Show Legend' }}
            </button>
            
            @if($shortlistingConfirmed)
                <button class="btn btn-sm btn-outline-warning py-1 px-2" 
                        title="Unpublish Shortlist"
                        wire:click="unconfirmShortlist"
                        wire:confirm="⚠️ WARNING: You are about to UNPUBLISH the shortlist.\n\nThis action will:\n• Move the shortlist back to draft status\n• All applications will be reverted to eligible status\n• Pitch scheduling will be disabled\n\nAre you sure you want to continue?">
                    <i class="bi bi-pause-circle me-1"></i> Unpublish Shortlist
                </button>
            @else
                <button class="btn btn-sm btn-success py-1 px-2" 
                        wire:click="confirmShortlist"
                        wire:confirm="✅ IMPORTANT DECISION: You are about to CONFIRM the Top 20 shortlist.\n\nThis action will:\n• Lock the Top 20 rankings\n• Enable pitch scheduling for these applications\n• Notify selected applicants\n\nOnce confirmed, you can still unpublish if needed.\n\nDo you want to continue?">
                    <i class="bi bi-check2-circle me-1"></i> Confirm Shortlist
                </button>
            @endif
        </div>
        
        <!-- Stepper Legend -->
        @if($showStepperLegend)
        <div class="border-top p-3 bg-light">
            <div class="row g-3 small">
                <div class="col-md-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width: 20px; height: 20px; font-size: 10px;">
                            <i class="bi bi-check2"></i>
                        </div>
                        <span><strong class="text-success">Completed</strong> - Step has been finalized</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 20px; height: 20px; font-size: 10px;">
                            <i class="bi bi-arrow-right"></i>
                        </div>
                        <span><strong class="text-primary">Active/In Progress</strong> - Current step to work on</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" style="width: 20px; height: 20px; font-size: 10px;">
                            <i class="bi bi-hourglass"></i>
                        </div>
                        <span><strong class="text-muted">Pending</strong> - Not yet started</span>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- KPI STRIP - ROW 1 --}}
    <div class="row g-3 mb-3">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <div class="fw-bold fs-4 text-primary">{{ $this->stats['total_evaluated'] }}</div>
                <small class="text-muted">Total Evaluated</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <div class="fw-bold fs-4 text-warning">{{ $this->stats['top_20_count'] }}</div>
                <small class="text-muted">Top 20 Shortlisted</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <div class="fw-bold fs-4 text-info">{{ $this->stats['pitched_count'] }}</div>
                <small class="text-muted">Pitched</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <div class="fw-bold fs-4" style="color:#8b5cf6;">{{ $this->stats['top_10_confirmed'] }}</div>
                <small class="text-muted">Top 10 Confirmed</small>
            </div>
        </div>
    </div>

    {{-- KPI STRIP - ROW 2 --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <div class="fw-bold fs-4 text-success">{{ $this->stats['dd_passed'] }}</div>
                <small class="text-muted">DD Passed</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <div class="fw-bold fs-4 text-danger">{{ $this->stats['dd_failed'] }}</div>
                <small class="text-muted">DD Failed</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <div class="fw-bold fs-4 text-success">{{ $this->stats['final_accepted'] }}</div>
                <small class="text-muted">Final Accepted</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center p-3 bg-light">
                <div class="fw-bold fs-4 text-muted">0</div>
                <small class="text-muted">In Progress</small>
            </div>
        </div>
    </div>

    {{-- RANKED TABLE --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex flex-wrap align-items-center justify-content-between gap-2">
            <h6 class="fw-bold mb-0">Top 20 Shortlist</h6>
            <div class="d-flex gap-2">
                <div class="input-group input-group-sm" style="width: 250px;">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" class="form-control border-start-0" placeholder="Search enterprise, ID…" wire:model.live.debounce.300ms="search">
                </div>
                <select class="form-select form-select-sm" style="width: 150px;" wire:model.live="industryFilter">
                    <option value="">All Industries</option>
                    @foreach($this->industries as $industry)
                        <option value="{{ $industry }}">{{ $industry }}</option>
                    @endforeach
                </select>
                <button class="btn btn-sm btn-outline-secondary" wire:click="resetFilters">Reset</button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small">
                <thead class="table-light">
                    <tr>
                        <th class="px-4" style="width:70px;">Rank</th>
                        <th style="min-width:220px;">Enterprise</th>
                        <th>Industry</th>
                        <th class="text-center">Eval Score</th>
                        <th class="text-center">Pitch Score</th>
                        <th class="text-center">Total Score</th>
                        <th>Pitch Slot</th>
                        <th>DD Status</th>
                        <th>Status</th>
                        <th class="text-center pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($this->applications as $app)
                    @php
                        $evalScore = $app->average_score ?? 0;
                        
                        $rankClass = 'bg-secondary';
                        if ($app->rank == 1) $rankClass = 'bg-warning text-dark';
                        elseif ($app->rank <= 3) $rankClass = 'bg-primary';
                        elseif ($app->rank <= 10) $rankClass = 'bg-info';
                        
                        $status = $shortlistingConfirmed ? 'Shortlisted' : 'Top 20';
                    @endphp
                    <tr>
                        <td class="px-4 py-3">
                            <div class="rounded-circle {{ $rankClass }} d-flex align-items-center justify-content-center text-white fw-bold" style="width: 32px; height: 32px;">
                                {{ $app->rank }}
                            </div>
                        </div>
                        <td class="py-3">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center text-dark fw-semibold" style="width: 36px; height: 36px;">
                                    <span>{{ substr($app->company_name, 0, 2) }}</span>
                                </div>
                                <div>
                                    <div class="fw-semibold text-dark">{{ $app->company_name }}</div>
                                    <div class="text-muted" style="font-size:0.7rem;">{{ $app->application_number }} · {{ $app->district ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </div>
                        <td><span class="badge bg-light text-dark border">{{ $app->industry ?? 'N/A' }}</span></div>
                        <td class="text-center"><span class="fw-bold {{ $evalScore >= 70 ? 'text-success' : ($evalScore >= 50 ? 'text-warning' : 'text-danger') }}">{{ $evalScore }}%</span></div>
                        <td class="text-center"><span class="text-muted">—</span></div>
                        <td class="text-center"><div class="fw-bold {{ $evalScore >= 70 ? 'text-success' : ($evalScore >= 50 ? 'text-warning' : 'text-danger') }}">{{ $evalScore }}%</div></div>
                        <td>
                            @if($shortlistingConfirmed)
                                <button class="btn btn-sm btn-outline-secondary" wire:click="schedulePitch({{ $app->id }})"><i class="bi bi-calendar-plus me-1"></i>Schedule</button>
                            @else
                                <span class="text-muted small">Pending</span>
                            @endif
                        </div>
                        <td><span class="text-muted small">—</span></div>
                        <td><span class="badge rounded-pill {{ $shortlistingConfirmed ? 'bg-success' : 'bg-warning text-dark' }}">{{ $status }}</span></div>
                        <td class="text-center pe-4">
                            <div class="d-flex justify-content-center gap-1">
                                <button class="btn btn-sm btn-outline-secondary" title="View Application" wire:click="viewApplication({{ $app->id }})">
                                    <i class="bi bi-eye"></i>
                                </button>

                                @if($shortlistingConfirmed)
                                    <button class="btn btn-sm btn-outline-primary" title="Schedule Pitch" wire:click="schedulePitch({{ $app->id }})">
                                        <i class="bi bi-calendar-plus"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-success" title="Score Pitch" wire:click="scorePitch({{ $app->id }})">
                                        <i class="bi bi-mic-fill"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-info" title="Due Diligence" wire:click="startDueDiligence({{ $app->id }})">
                                        <i class="bi bi-clipboard-check"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-2 d-block mb-2 opacity-50"></i>
                            No evaluated applications found. Please ensure applications have been evaluated and marked as eligible.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-top py-3 d-flex flex-wrap align-items-center justify-content-between">
            <small class="text-muted">Showing {{ $this->applications->count() }} of {{ $this->stats['top_20_count'] }} applications</small>
            <div class="d-flex gap-2 align-items-center">
                <select class="form-select form-select-sm" style="width: auto;" wire:model.live="perPage">
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                </select>
            </div>
        </div>
    </div>

</div>
</div>

<style>
/* Stepper Styles */
.stepper-step {
    transition: all 0.3s ease;
    cursor: pointer;
}

.stepper-step:hover {
    background-color: #f8f9fa;
}

.step-completed .step-icon {
    background-color: #198754;
    color: white;
}

.step-active .step-icon {
    background-color: #0d6efd;
    color: white;
}

.step-pending .step-icon {
    background-color: #6c757d;
    color: white;
}

.step-completed .step-count {
    color: #198754;
}

.step-active .step-count {
    color: #0d6efd;
}

.step-pending .step-count {
    color: #6c757d;
}
</style>