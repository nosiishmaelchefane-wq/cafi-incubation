<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Cohort;
use App\Models\Call;
use App\Models\IncubationApplication;
use App\Models\AssignedEvaluator;
use App\Models\EvaluationScore;
use App\Models\EvaluationWindow;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Carbon\Carbon;


new class extends Component
{
    use WithPagination;

    public $selectedCohort = null;
    public $selectedCall = null;
    public $search = '';
    public $scoreStatusFilter = '';
    public $evaluationWindow = null;
    
    protected $queryString = [
        'selectedCohort' => ['except' => null],
        'selectedCall' => ['except' => null],
        'search' => ['except' => ''],
        'scoreStatusFilter' => ['except' => ''],
    ];

    public function mount()
    {
        $latestCohort = Cohort::orderBy('year', 'desc')->orderBy('cohort_number', 'desc')->first();
        if ($latestCohort) {
            $this->selectedCohort = $latestCohort->id;
      
            if ($this->calls->isNotEmpty()) {
                $this->selectedCall = $this->calls->first()->id;
                $this->loadEvaluationWindow();
            }
        }
    }

    public function loadEvaluationWindow()
    {
        if ($this->selectedCall) {
            $call = Call::find($this->selectedCall);
            $this->evaluationWindow = $call?->latestEvaluationWindow;
        }
    }

    public function lockWindow()
    {
        if ($this->selectedCall) {
            $this->dispatch('open-evaluation-window-modal', callId: $this->selectedCall);
        } else {
            $this->dispatch('notify', type: 'error', message: 'No call selected to lock window.');
        }
    }
    
    #[On('evaluation-window-saved')]
    public function refreshEvaluationWindow()
    {
        $this->loadEvaluationWindow();
        $this->dispatch('notify', type: 'success', message: 'Evaluation window updated successfully!');
    }
    
    /**
     * Listen for score updated event and refresh the component
     */
    #[On('scoreUpdated')]
    public function refreshWithScoreUpdated()
    {
        $this->resetPage();
        // Refresh the component to show updated data
        $this->dispatch('$refresh');
    }

    /**
     * Open the scoring modal for an application
     */
    public function openScoreModal($applicationId)
    {
        $this->dispatch('openScoreModal', applicationId: $applicationId);
    }

    /**
     * Get all cohorts for the dropdown
     */
    public function getCohortsProperty()
    {
        return Cohort::orderBy('year', 'desc')->orderBy('cohort_number', 'desc')->get();
    }

    /**
     * Get calls for the selected cohort
     */
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

    /**
 * Get eligible applications for evaluation
 */
    public function getApplicationsProperty()
    {
        if (!$this->selectedCall) {
            return collect();
        }
        
        $currentEvaluatorId = Auth::id();
        $totalEvaluators = AssignedEvaluator::where('call_id', $this->selectedCall)->count();
        
        $query = IncubationApplication::query()
            ->with(['user', 'call', 'evaluationScores'])
            ->where('call_id', $this->selectedCall)
            ->whereIn('status', ['eligible', 'in_review']);
        
        // Apply KPI score status filter based on ALL evaluators
        if ($this->scoreStatusFilter && $this->scoreStatusFilter !== 'all') {
            switch ($this->scoreStatusFilter) {
                case 'fully_scored':
                    $query->whereHas('evaluationScores', function($q) use ($totalEvaluators) {
                        $q->where('status', 'submitted');
                    }, '=', $totalEvaluators);
                    break;
                case 'partially_scored':
                    $query->whereHas('evaluationScores', function($q) {
                        $q->where('status', 'submitted');
                    }, '>', 0)
                    ->whereHas('evaluationScores', function($q) use ($totalEvaluators) {
                        $q->where('status', 'submitted');
                    }, '<', $totalEvaluators);
                    break;
                case 'not_scored':
                    $query->whereDoesntHave('evaluationScores', function($q) {
                        $q->where('status', 'submitted');
                    });
                    break;
            }
        }
        
        // Search filter
        if ($this->search) {
            $query->where(function($q) {
                $q->where('company_name', 'like', '%' . $this->search . '%')
                    ->orWhere('application_number', 'like', '%' . $this->search . '%')
                    ->orWhere('applicant_name', 'like', '%' . $this->search . '%');
            });
        }
        
        return $query->orderBy('created_at', 'desc')->paginate(10);
    }

    /**
     * Get statistics for the selected call
     */
    public function getStatsProperty()
    {
        if (!$this->selectedCall) {
            return [
                'total_applications' => 0,
                'fully_scored_count' => 0,
                'partially_scored_count' => 0,
                'not_scored_count' => 0,
                'evaluators_count' => 0,
                'average_score' => 0,
                'evaluation_deadline' => null,
                'is_window_open' => false,
                'window_status' => 'no_window',
                'window_message' => 'No evaluation window set',
                'window' => null,
                'days_remaining' => null,
            ];
        }
        
        $currentEvaluatorId = Auth::id();
        $call = Call::find($this->selectedCall);
        $applications = IncubationApplication::where('call_id', $this->selectedCall)
            ->whereIn('status', ['eligible', 'in_review'])
            ->get();
        
        // Get total number of evaluators assigned to this call
        $totalEvaluators = AssignedEvaluator::where('call_id', $this->selectedCall)->count();
        
        $totalApps = $applications->count();
        
        // Calculate scoring progress based on ALL evaluators
        $fullyScoredCount = 0;
        $partiallyScoredCount = 0;
        $notScoredCount = 0;
        $totalScores = 0;
        $submittedCount = 0;
        
        foreach ($applications as $app) {
            // Get count of evaluators who have submitted scores for this application
            $submittedEvaluatorsCount = $app->evaluationScores()
                ->where('status', 'submitted')
                ->count();
            
            if ($submittedEvaluatorsCount == 0) {
                $notScoredCount++;
            } elseif ($submittedEvaluatorsCount >= $totalEvaluators && $totalEvaluators > 0) {
                $fullyScoredCount++;
                // Calculate average score for fully scored applications
                $avgScore = $app->evaluationScores()
                    ->where('status', 'submitted')
                    ->avg('total_score');
                $totalScores += $avgScore;
                $submittedCount++;
            } else {
                $partiallyScoredCount++;
            }
        }
        
        $averageScore = $submittedCount > 0 ? round($totalScores / $submittedCount) : 0;
        
        // Get assigned evaluators for this call
        $assignedEvaluators = AssignedEvaluator::where('call_id', $this->selectedCall)->get();
        $evaluatorsCount = $assignedEvaluators->count();
        
        // Get evaluation window status
        $isWindowOpen = false;
        $windowStatus = 'no_window';
        $windowMessage = 'No evaluation window set';
        $daysRemaining = null;
        
        if ($this->evaluationWindow) {
            $now = Carbon::now();
            if ($this->evaluationWindow->status === 'active') {
                $isWindowOpen = true;
                $windowStatus = 'open';
                $daysRemaining = $this->evaluationWindow->getDaysRemainingAttribute();
                $windowMessage = "Evaluation window is OPEN";
                if ($daysRemaining !== null) {
                    $windowMessage .= " · {$daysRemaining} days remaining";
                }
            } elseif ($this->evaluationWindow->status === 'draft') {
                $windowStatus = 'upcoming';
                $daysUntilOpen = $this->evaluationWindow->getDaysUntilOpenAttribute();
                $windowMessage = "Evaluation window opens on " . $this->evaluationWindow->open_date->format('M d, Y');
                if ($daysUntilOpen !== null) {
                    $windowMessage .= " · {$daysUntilOpen} days from now";
                }
            } elseif ($this->evaluationWindow->status === 'expired') {
                $windowStatus = 'expired';
                $windowMessage = "Evaluation window closed on " . $this->evaluationWindow->close_date->format('M d, Y');
            }
        }
        
        return [
            'total_applications' => $totalApps,
            'fully_scored_count' => $fullyScoredCount,
            'partially_scored_count' => $partiallyScoredCount,
            'not_scored_count' => $notScoredCount,
            'evaluators_count' => $evaluatorsCount,
            'average_score' => $averageScore,
            'evaluation_deadline' => $assignedEvaluators->first()?->evaluation_deadline,
            'is_window_open' => $isWindowOpen,
            'window_status' => $windowStatus,
            'window_message' => $windowMessage,
            'window' => $this->evaluationWindow,
            'days_remaining' => $daysRemaining,
        ];
    }
    
    /**
     * Get the selected cohort name
     */
    public function getSelectedCohortNameProperty()
    {
        if (!$this->selectedCohort) {
            return 'Select Cohort';
        }
        
        $cohort = Cohort::find($this->selectedCohort);
        return $cohort ? $cohort->name . ' (' . $cohort->year . ')' : 'Select Cohort';
    }

    /**
     * Get the selected call title
     */
    public function getSelectedCallTitleProperty()
    {
        if (!$this->selectedCall) {
            return 'Select Call';
        }
        
        $call = Call::find($this->selectedCall);
        return $call ? $call->title : 'Select Call';
    }

    /**
     * Get button text based on window status
     */
    public function getWindowButtonTextProperty()
    {
        if (!$this->evaluationWindow) {
            return 'Set Window';
        }
        
        if ($this->evaluationWindow->status === 'active') {
            return 'Edit Window';
        } elseif ($this->evaluationWindow->status === 'draft') {
            return 'Edit Window';
        } elseif ($this->evaluationWindow->status === 'expired') {
            return 'Create New Window';
        }
        
        return 'Set Window';
    }
    
    /**
     * Get button icon based on window status
     */
    public function getWindowButtonIconProperty()
    {
        if (!$this->evaluationWindow) {
            return 'bi-calendar-plus';
        }
        
        if ($this->evaluationWindow->status === 'active') {
            return 'bi-pencil-square';
        } elseif ($this->evaluationWindow->status === 'draft') {
            return 'bi-lock';
        } elseif ($this->evaluationWindow->status === 'expired') {
            return 'bi-plus-circle';
        }
        
        return 'bi-calendar-plus';
    }
    
    /**
     * Get banner class based on window status
     */
    public function getWindowBannerClassProperty()
    {
        if (!$this->evaluationWindow) {
            return 'alert-secondary';
        }
        
        if ($this->evaluationWindow->status === 'active') {
            return 'alert-success';
        } elseif ($this->evaluationWindow->status === 'draft') {
            return 'alert-warning';
        } elseif ($this->evaluationWindow->status === 'expired') {
            return 'alert-danger';
        }
        
        return 'alert-secondary';
    }

    /**
     * Load calls when cohort changes
     */
    public function updatedSelectedCohort()
    {
        $this->selectedCall = null;
        $this->resetPage();
        
        if ($this->calls->isNotEmpty()) {
            $this->selectedCall = $this->calls->first()->id;
            $this->loadEvaluationWindow();
        }
    }

    /**
     * Reset pagination when filters change
     */
    public function updatedSelectedCall()
    {
        $this->resetPage();
        $this->loadEvaluationWindow();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedScoreStatusFilter()
    {
        $this->resetPage();
    }

    /**
     * Set filter from KPI click
     */
    public function setFilter($filter)
    {
        $this->scoreStatusFilter = $filter;
        $this->resetPage();
    }

    public function openAssignEvaluatorModal()
    {
        $this->dispatch('openAssignEvaluatorModal', callId: $this->selectedCall);
    }
}
?>

<div>
    <div class="eval-page container py-3 py-md-4">

        {{-- PAGE HEADER --}}
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 small">
                        <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-muted">Incubation</a></li>
                        <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-muted">Applications</a></li>
                        <li class="breadcrumb-item active">Evaluation &amp; Scoring</li>
                    </ol>
                </nav>
                <h4 class="fw-bold mb-1">
                    <i class="bi bi-clipboard2-data-fill text-primary me-2"></i>Evaluation &amp; Scoring
                </h4>
                <p class="text-muted small mb-0">
                    Score eligible applications · LEHSFF Evaluation Tool
                </p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <button class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1">
                    <i class="bi bi-download me-1"></i>Export Scorings
                </button>
               @can('create Calls for Applications')
                    <button class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1"
                            wire:click="openAssignEvaluatorModal">
                        <i class="bi bi-person-plus me-1"></i>Assign Evaluators
                    </button>

                    <button 
                        type="button"
                        wire:click="lockWindow"
                        class="btn btn-sm btn-warning d-flex align-items-center gap-1">
                        <i class="bi {{ $this->windowButtonIcon }}"></i> 
                        {{ $this->windowButtonText }}
                    </button>
                @endcan
            </div>
        </div>

        {{-- COHORT & CALL FILTERS --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-3">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label small fw-medium mb-1">
                            <i class="bi bi-people me-1"></i>Cohort
                        </label>
                        <select class="form-select form-select-sm" wire:model.live="selectedCohort">
                            <option value="">Select Cohort</option>
                            @foreach($this->cohorts as $cohort)
                                <option value="{{ $cohort->id }}">
                                    {{ $cohort->name }} ({{ $cohort->year }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label small fw-medium mb-1">
                            <i class="bi bi-megaphone me-1"></i>Call for Applications
                        </label>
                        <select class="form-select form-select-sm" wire:model.live="selectedCall" 
                                {{ $this->calls->isEmpty() ? 'disabled' : '' }}>
                            <option value="">Select Call</option>
                            @foreach($this->calls as $call)
                                <option value="{{ $call->id }}">
                                    {{ $call->title }} 
                                    @if($call->status) 
                                        ({{ ucfirst($call->status) }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @if($this->calls->isEmpty() && $selectedCohort)
                            <small class="text-muted">No calls found for this cohort</small>
                        @endif
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <div class="flex-grow-1">
                                <label class="form-label small fw-medium mb-1">
                                    <i class="bi bi-search me-1"></i>Search
                                </label>
                                <input type="text" class="form-control form-control-sm" 
                                       wire:model.live.debounce.300ms="search"
                                       placeholder="Company name, ID...">
                            </div>
                            <div class="mt-auto">
                                <button class="btn btn-sm btn-outline-secondary" wire:click="$set('search', '')">
                                    <i class="bi bi-x-circle"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- EVALUATION WINDOW BANNER --}}
        @if($selectedCall)
            <div class="alert {{ $this->windowBannerClass }} d-flex align-items-center gap-3 mb-4 shadow-sm">
                <i class="bi 
                    @if($this->stats['window_status'] == 'open') bi-unlock-fill 
                    @elseif($this->stats['window_status'] == 'upcoming') bi-clock-history 
                    @elseif($this->stats['window_status'] == 'expired') bi-calendar-x 
                    @else bi-calendar2-week 
                    @endif fs-4">
                </i>
                <div class="flex-grow-1">
                    <div class="fw-semibold">
                        {{ $this->stats['window_message'] }}
                    </div>
                    @if($this->stats['window'] && $this->stats['window_status'] != 'no_window')
                        <small>
                            <i class="bi bi-calendar-range me-1"></i>
                            {{ \Carbon\Carbon::parse($this->stats['window']->open_date)->format('M d, Y') }} - 
                            {{ \Carbon\Carbon::parse($this->stats['window']->close_date)->format('M d, Y') }}
                            @if($this->stats['window']->notes)
                                <span class="ms-2">· {{ $this->stats['window']->notes }}</span>
                            @endif
                        </small>
                    @endif
                </div>
                <span class="badge 
                    @if($this->stats['window_status'] == 'open') bg-success
                    @elseif($this->stats['window_status'] == 'upcoming') bg-warning text-dark
                    @elseif($this->stats['window_status'] == 'expired') bg-secondary
                    @else bg-info
                    @endif">
                    @if($this->stats['window_status'] == 'open') Active
                    @elseif($this->stats['window_status'] == 'upcoming') Upcoming
                    @elseif($this->stats['window_status'] == 'expired') Expired
                    @else Not Set
                    @endif
                </span>
            </div>
        @endif

        {{-- KPI STRIP (Clickable Filters) --}}
        @can('view Analytics & Reporting')
            <div class="row g-3 mb-4">
                <div class="col-6 col-sm-4 col-md-2 col-xl-2">
                    <div class="kpi-mini card border-0 shadow-sm {{ !$scoreStatusFilter || $scoreStatusFilter == 'all' ? 'kpi-active' : '' }}" 
                        wire:click="setFilter('all')">
                        <div class="card-body p-3 text-center">
                            <div class="fw-bold fs-4 lh-1 text-dark">{{ $this->stats['total_applications'] }}</div>
                            <small class="text-muted">All Apps</small>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-sm-4 col-md-2 col-xl-2">
                    <div class="kpi-mini card border-0 shadow-sm {{ $scoreStatusFilter == 'not_scored' ? 'kpi-active' : '' }}" 
                        wire:click="setFilter('not_scored')">
                        <div class="card-body p-3 text-center">
                            <div class="fw-bold fs-4 lh-1 text-warning">{{ $this->stats['not_scored_count'] }}</div>
                            <small class="text-muted">Not Scored</small>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-sm-4 col-md-2 col-xl-2">
                    <div class="kpi-mini card border-0 shadow-sm {{ $scoreStatusFilter == 'partially_scored' ? 'kpi-active' : '' }}" 
                        wire:click="setFilter('partially_scored')">
                        <div class="card-body p-3 text-center">
                            <div class="fw-bold fs-4 lh-1 text-info">{{ $this->stats['partially_scored_count'] }}</div>
                            <small class="text-muted">Partial</small>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-sm-4 col-md-2 col-xl-2">
                    <div class="kpi-mini card border-0 shadow-sm {{ $scoreStatusFilter == 'fully_scored' ? 'kpi-active' : '' }}" 
                        wire:click="setFilter('fully_scored')">
                        <div class="card-body p-3 text-center">
                            <div class="fw-bold fs-4 lh-1 text-success">{{ $this->stats['fully_scored_count'] }}</div>
                            <small class="text-muted">Fully Scored</small>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-sm-4 col-md-2 col-xl-2">
                    <div class="kpi-mini card border-0 shadow-sm">
                        <div class="card-body p-3 text-center">
                            <div class="fw-bold fs-4 lh-1 text-primary">{{ $this->stats['evaluators_count'] }}</div>
                            <small class="text-muted">Evaluators</small>
                        </div>
                    </div>
                </div>
            </div>
        @endcan
        
        {{-- FILTER BAR --}}
        <div class="d-flex align-items-center gap-3 mb-3 flex-wrap">
            <div class="d-flex align-items-center gap-1 text-muted small">
                <i class="bi bi-table me-1"></i>Scoring Queue
                <span class="badge bg-secondary ms-1">{{ method_exists($this->applications, 'total') ? $this->applications->total() : $this->applications->count() }} applications</span>
            </div>
            <div class="ms-auto d-flex gap-2 align-items-center flex-wrap">
                @if($scoreStatusFilter && $scoreStatusFilter != 'all')
                    <button class="btn btn-sm btn-outline-secondary" wire:click="setFilter('all')">
                        <i class="bi bi-x-circle me-1"></i>Clear Filter
                    </button>
                @endif
            </div>
        </div>

        {{-- SCORING QUEUE TABLE --}}
        {{-- SCORING QUEUE TABLE --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3 px-4 d-flex flex-wrap align-items-center justify-content-between gap-2">
                <h6 class="fw-bold mb-0">Scoring Queue</h6>
                <small class="text-muted">{{ $this->stats['total_applications'] }} applications</small>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4 py-3" style="min-width:250px;">Application</th>
                                <th class="py-3 text-center" style="min-width:180px;">Evaluators</th>
                                <th class="py-3 text-center" style="width:130px;">Progress</th>
                                @if(auth()->user()->hasRole('Evaluation Officer'))
                                    <th class="py-3 text-center" style="min-width:100px;">Final Score</th>
                                    <th class="py-3 text-center" style="min-width:100px;">My Score</th>
                                @endif
                                <th class="py-3 text-center" style="min-width:100px;">Status</th>
                                <th class="py-3 text-center" style="min-width:120px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($this->applications as $app)
                            @php
                                $currentEvaluatorId = Auth::id();
                                $userScore = $app->evaluationScores()
                                    ->where('evaluator_id', $currentEvaluatorId)
                                    ->first();
                                $scoreStatus = 'not_scored';
                                $myScore = null;
                                if ($userScore) {
                                    $scoreStatus = $userScore->status;
                                    $myScore = $userScore->total_score;
                                }
                                
                                $assignedEvals = \App\Models\AssignedEvaluator::where('call_id', $selectedCall)->with('evaluator')->get();
                                $totalEvals = $assignedEvals->count();
                                $completedEvals = $app->evaluationScores()
                                    ->where('status', 'submitted')
                                    ->count();
                                $avgScore = $app->evaluationScores()
                                    ->where('status', 'submitted')
                                    ->avg('total_score');
                                $progressPercent = $totalEvals > 0 ? ($completedEvals / $totalEvals) * 100 : 0;
                            @endphp
                            <tr>
                                <!-- Application Column -->
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="app-avatar av-green">
                                            <span>{{ substr($app->company_name, 0, 2) }}</span>
                                        </div>
                                        <div>
                                            <div class="fw-semibold text-dark">{{ $app->company_name }}</div>
                                            <div class="text-muted" style="font-size:.72rem;">
                                                {{ $app->application_number }} · {{ $app->district ?? 'Maseru' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                
                                <!-- Evaluators Column -->
                                <!-- Evaluators Column -->
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        @foreach($assignedEvals as $eval)
                                            @php
                                                $hasScored = $app->evaluationScores()
                                                    ->where('evaluator_id', $eval->user_id)
                                                    ->where('status', 'submitted')
                                                    ->exists();
                                                
                                                // Use the evaluator relationship
                                                $evaluatorUser = $eval->evaluator;
                                                $displayName = $evaluatorUser?->username ?: ($evaluatorUser?->email ? explode('@', $evaluatorUser->email)[0] : 'Eval');
                                                $initials = strtoupper(substr($displayName, 0, 2));
                                            @endphp
                                            <div class="ev-avatar {{ $hasScored ? 'bg-success text-white' : 'bg-light text-muted border' }}" 
                                                style="width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600;"
                                                title="{{ $displayName }} {{ $hasScored ? '- Scored' : '- Not Scored' }}">
                                                {{ $initials }}
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                                
                                <!-- Progress Column -->
                                <td class="text-center">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height:6px;">
                                            <div class="progress-bar bg-primary" style="width: {{ $progressPercent }}%"></div>
                                        </div>
                                        <span class="text-muted small">{{ $completedEvals }}/{{ $totalEvals }}</span>
                                    </div>
                                </td>
                                
                                <!-- Final Score Column (only for Evaluation Officer) -->
                                @if(auth()->user()->hasRole('Evaluation Officer'))
                                    <td class="text-center">
                                        @if($avgScore)
                                            <div class="fw-bold fs-6 lh-1 {{ $avgScore >= 70 ? 'text-success' : ($avgScore >= 50 ? 'text-warning' : 'text-danger') }}">
                                                {{ round($avgScore) }}/100
                                            </div>
                                            <div class="text-muted" style="font-size:.68rem;">avg of {{ $completedEvals }}</div>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                @endif
                                
                                <!-- My Score Column (only for Evaluation Officer) -->
                                @if(auth()->user()->hasRole('Evaluation Officer'))
                                    <td class="text-center">
                                        @if($myScore)
                                            <span class="badge bg-primary bg-opacity-15 text-primary fw-bold px-2 py-1 rounded-pill">{{ round($myScore) }}/100</span>
                                        @else
                                            <span class="badge bg-warning bg-opacity-15 text-warning px-2 py-1 rounded-pill">Pending</span>
                                        @endif
                                    </td>
                                @endif
                                
                                <!-- Status Column -->
                                <td class="text-center">
                                    @if(auth()->user()->hasRole('Evaluation Officer'))
                                        @if($scoreStatus == 'submitted')
                                            <span class="badge rounded-pill bg-success px-2 py-1">Scored</span>
                                        @elseif($scoreStatus == 'draft')
                                            <span class="badge rounded-pill bg-info px-2 py-1">In Progress</span>
                                        @else
                                            <span class="badge rounded-pill bg-warning px-2 py-1">Not Scored</span>
                                        @endif
                                    @else
                                        @php
                                            $allSubmitted = $completedEvals == $totalEvals && $totalEvals > 0;
                                            $someSubmitted = $completedEvals > 0 && $completedEvals < $totalEvals;
                                        @endphp
                                        @if($allSubmitted)
                                            <span class="badge rounded-pill bg-success px-2 py-1">Fully Scored</span>
                                        @elseif($someSubmitted)
                                            <span class="badge rounded-pill bg-info px-2 py-1">Partial</span>
                                        @else
                                            <span class="badge rounded-pill bg-warning px-2 py-1">Pending</span>
                                        @endif
                                    @endif
                                </td>
                                
                                <!-- Action Column -->
                                <td class="text-center">
                                    @php
                                        $userScore = $app->evaluationScores()
                                            ->where('evaluator_id', Auth::id())
                                            ->first();
                                        $isSubmitted = $userScore && $userScore->status === 'submitted';
                                        $hasDraft = $userScore && $userScore->status === 'draft';
                                        $isAssigned = \App\Models\AssignedEvaluator::where('call_id', $selectedCall)
                                            ->where('user_id', Auth::id())
                                            ->exists();
                                    @endphp
                                    
                                    @if(!$isAssigned)
                                        <button class="btn btn-sm btn-outline-secondary py-1 px-3" 
                                                wire:click="openScoreModal({{ $app->id }})">
                                            <i class="bi bi-eye me-1"></i>View Score
                                        </button>
                                    @else
                                        <button class="btn btn-sm {{ $isSubmitted ? 'btn-outline-secondary' : ($hasDraft ? 'btn-warning' : 'btn-primary') }} py-1 px-3" 
                                                wire:click="openScoreModal({{ $app->id }})">
                                            @if($isSubmitted)
                                                <i class="bi bi-eye me-1"></i>View Score
                                            @elseif($hasDraft)
                                                <i class="bi bi-pencil me-1"></i>Continue Scoring
                                            @else
                                                <i class="bi bi-star-fill me-1"></i>Score
                                            @endif
                                        </button>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-2 d-block mb-2 opacity-50"></i>
                                    No eligible applications found for this call.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex flex-wrap align-items-center justify-content-between px-4 py-3 border-top gap-2">
                    <small class="text-muted">
                        @if(method_exists($this->applications, 'total') && $this->applications->total() > 0)
                            Showing {{ $this->applications->firstItem() }}–{{ $this->applications->lastItem() }} 
                            of {{ $this->applications->total() }} applications
                        @else
                            Showing 0 applications
                        @endif
                    </small>
                    @if(method_exists($this->applications, 'links'))
                        {{ $this->applications->links() }}
                    @endif
                </div>
            </div>
        </div>
    </div>
    <livewire:evaluations.modals.evaluation-period/>
</div>