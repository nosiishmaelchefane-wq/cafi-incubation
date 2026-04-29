<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Call;
use App\Models\Cohort;
use App\Models\IncubationApplication;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;

new class extends Component {
    use WithPagination;

    public $selectedCohort = null;
    public $search = '';
    public $statusFilter = '';
    public $districtFilter = '';
    public $perPage = 10;
    public $selectedApplicationId = null;
    
    protected $queryString = [
        'selectedCohort' => ['except' => null],
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'districtFilter' => ['except' => ''],
    ];

    #[On('applicationUpdated')]
    public function refreshApplications()
    {
        $this->resetPage();
        // Refresh the component to show updated data
        $this->dispatch('$refresh');
    }

    public function mount()
    {
        // Get the latest cohort by default
        $latestCohort = Cohort::orderBy('year', 'desc')->orderBy('cohort_number', 'desc')->first();
        if ($latestCohort) {
            $this->selectedCohort = $latestCohort->id;
        }
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
            ->orderBy('open_date', 'desc')
            ->get();
    }

    /**
     * Get applications query (excluding drafts)
     */
    public function getApplicationsProperty()
    {
        $query = IncubationApplication::query()
            ->with(['user', 'call'])
            ->where('status', '!=', 'draft');
        
        // Filter by selected cohort
        if ($this->selectedCohort) {
            $cohort = Cohort::find($this->selectedCohort);
            if ($cohort) {
                $query->whereHas('call', function($q) use ($cohort) {
                    $q->where('cohort', $cohort->cohort_number);
                });
            }
        }
        
        // Search filter
        if ($this->search) {
            $query->where(function($q) {
                $q->where('company_name', 'like', '%' . $this->search . '%')
                    ->orWhere('application_number', 'like', '%' . $this->search . '%')
                    ->orWhere('applicant_name', 'like', '%' . $this->search . '%')
                    ->orWhere('applicant_email', 'like', '%' . $this->search . '%');
            });
        }
        
        // Status filter
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }
        
        // District filter
        if ($this->districtFilter) {
            $query->where('district', $this->districtFilter);
        }
        
        return $query->orderBy('created_at', 'desc')->paginate($this->perPage);
    }

    /**
     * Get counts for KPI cards
     */
    public function getCountsProperty()
    {
        $query = IncubationApplication::query()
            ->where('status', '!=', 'draft');
        
        if ($this->selectedCohort) {
            $cohort = Cohort::find($this->selectedCohort);
            if ($cohort) {
                $query->whereHas('call', function($q) use ($cohort) {
                    $q->where('cohort', $cohort->cohort_number);
                });
            }
        }
        
        return [
            'total' => $query->count(),
            'submitted' => (clone $query)->where('status', 'submitted')->count(),
            'inReview' => (clone $query)->where('status', 'in_review')->count(),
            'eligible' => (clone $query)->where('status', 'eligible')->count(),
            'rejected' => (clone $query)->where('status', 'rejected')->count(),
            'screened' => (clone $query)->whereIn('status', ['eligible', 'rejected'])->count(),
            'completionPct' => $query->count() > 0 
                ? round((clone $query)->whereIn('status', ['eligible', 'rejected'])->count() / $query->count() * 100) 
                : 0,
        ];
    }

    /**
     * Get unique districts for filter dropdown
     */
    public function getDistrictsProperty()
    {
        $query = IncubationApplication::query()
            ->where('status', '!=', 'draft')
            ->whereNotNull('district');
        
        if ($this->selectedCohort) {
            $cohort = Cohort::find($this->selectedCohort);
            if ($cohort) {
                $query->whereHas('call', function($q) use ($cohort) {
                    $q->where('cohort', $cohort->cohort_number);
                });
            }
        }
        
        return $query->distinct()->pluck('district');
    }

    public function updatedSelectedCohort()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedDistrictFilter()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->districtFilter = '';
        
        if (!$this->selectedCohort) {
            $latestCohort = Cohort::orderBy('year', 'desc')->orderBy('cohort_number', 'desc')->first();
            if ($latestCohort) {
                $this->selectedCohort = $latestCohort->id;
            }
        }
        
        $this->resetPage();
    }

    public function getSelectedCohortNameProperty()
    {
        if (!$this->selectedCohort) {
            return 'All';
        }
        
        $cohort = Cohort::find($this->selectedCohort);
        return $cohort ? $cohort->name . ' (' . $cohort->year . ')' : 'All';
    }
    
    public function markEligible($applicationId)
    {
        $app = IncubationApplication::findOrFail($applicationId);
        $app->status = 'eligible';
        $app->save();
        
        $this->dispatch('notify', type: 'success', message: 'Application marked as eligible');
    }
    
    public function markRejected($applicationId)
    {
        $app = IncubationApplication::findOrFail($applicationId);
        $app->status = 'rejected';
        $app->save();
        
        $this->dispatch('notify', type: 'success', message: 'Application marked as rejected');
    }
    
    public function moveToReview($applicationId)
    {
        $app = IncubationApplication::findOrFail($applicationId);
        $app->status = 'in_review';
        $app->save();
        
        $this->dispatch('notify', type: 'success', message: 'Application moved to review');
    }
    
    public function openReview($applicationId)
    {
        $this->selectedApplicationId = $applicationId;
        $this->dispatch('openReviewPanel', applicationId: $applicationId);
    }
};
?>
<div x-data="{ 
    cohortDropdownOpen: false, 
    callDropdownOpen: false, 
    bulkDropdownOpen: false 
}">
    <div class="screening-page p-4">

    <!-- PAGE HEADER -->
    <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="#" class="text-decoration-none">Incubation</a></li>
                    <li class="breadcrumb-item"><a href="#" class="text-decoration-none">Applications</a></li>
                    <li class="breadcrumb-item active">Screening &amp; Eligibility</li>
                </ol>
            </nav>
            <h4 class="fw-bold mb-1">
                <i class="bi bi-funnel-fill text-primary me-2"></i>Screening &amp; Eligibility
            </h4>
            <p class="text-muted small mb-0">Review submitted applications · Mark eligible or rejected</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <!-- Cohort Filter Dropdown -->
            <div class="position-relative" x-on:click.away="cohortDropdownOpen = false">
                <button @click="cohortDropdownOpen = !cohortDropdownOpen" type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle">
                    <i class="bi bi-funnel me-1"></i> Cohort: {{ $this->selectedCohortName }}
                </button>
                <div x-show="cohortDropdownOpen" x-cloak class="position-absolute top-100 end-0 mt-1 bg-white border rounded shadow-sm" style="min-width: 200px; z-index: 1000;">
                    @foreach($this->cohorts as $cohort)
                        <a href="#" class="dropdown-item {{ $selectedCohort == $cohort->id ? 'active' : '' }}" 
                           style="display: block; padding: 0.5rem 1rem; text-decoration: none; color: #212529;"
                           @click.prevent="cohortDropdownOpen = false; $wire.set('selectedCohort', {{ $cohort->id }})">
                            {{ $cohort->name }} ({{ $cohort->year }})
                        </a>
                    @endforeach
                </div>
            </div>
            
            <!-- Call Dropdown - Shows only calls linked to selected cohort -->
            @if($selectedCohort && $this->calls->count() > 0)
            <div class="position-relative" x-on:click.away="callDropdownOpen = false">
                <button @click="callDropdownOpen = !callDropdownOpen" type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle">
                    <i class="bi bi-megaphone me-1"></i> View Call
                </button>
                <div x-show="callDropdownOpen" x-cloak class="position-absolute top-100 end-0 mt-1 bg-white border rounded shadow-sm" style="min-width: 250px; z-index: 1000;">
                    @foreach($this->calls as $call)
                        <a href="{{ route('call.show', $call->id) }}" 
                           class="dropdown-item d-flex justify-content-between align-items-center"
                           style="display: block; padding: 0.5rem 1rem; text-decoration: none; color: #212529;">
                            <span>{{ $call->title }}</span>
                            <i class="bi bi-box-arrow-up-right text-muted" style="font-size: 0.7rem;"></i>
                        </a>
                    @endforeach
                </div>
            </div>
            @endif
            
            <button class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1">
                <i class="bi bi-download"></i> Export List
            </button>
        </div>
    </div>

    <!-- KPI STRIP -->
    @can('view Analytics & Reporting')
        <div class="row g-3 mb-4">
            <div class="col-6 col-sm-4 col-md-2">
                <div class="kpi-mini card border-0 shadow-sm {{ !$statusFilter ? 'kpi-active' : '' }}" wire:click="$set('statusFilter', '')">
                    <div class="card-body p-3 text-center">
                        <div class="fw-bold fs-4 lh-1 text-dark">{{ $this->counts['total'] }}</div>
                        <small class="text-muted">Total</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-sm-4 col-md-2">
                <div class="kpi-mini card border-0 shadow-sm {{ $statusFilter == 'submitted' ? 'kpi-active' : '' }}" wire:click="$set('statusFilter', 'submitted')">
                    <div class="card-body p-3 text-center">
                        <div class="fw-bold fs-4 lh-1 text-warning">{{ $this->counts['submitted'] }}</div>
                        <small class="text-muted">Submitted</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-sm-4 col-md-2">
                <div class="kpi-mini card border-0 shadow-sm {{ $statusFilter == 'in_review' ? 'kpi-active' : '' }}" wire:click="$set('statusFilter', 'in_review')">
                    <div class="card-body p-3 text-center">
                        <div class="fw-bold fs-4 lh-1 text-info">{{ $this->counts['inReview'] }}</div>
                        <small class="text-muted">In Review</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-sm-4 col-md-2">
                <div class="kpi-mini card border-0 shadow-sm {{ $statusFilter == 'eligible' ? 'kpi-active' : '' }}" wire:click="$set('statusFilter', 'eligible')">
                    <div class="card-body p-3 text-center">
                        <div class="fw-bold fs-4 lh-1 text-success">{{ $this->counts['eligible'] }}</div>
                        <small class="text-muted">Eligible</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-sm-4 col-md-2">
                <div class="kpi-mini card border-0 shadow-sm {{ $statusFilter == 'rejected' ? 'kpi-active' : '' }}" wire:click="$set('statusFilter', 'rejected')">
                    <div class="card-body p-3 text-center">
                        <div class="fw-bold fs-4 lh-1 text-danger">{{ $this->counts['rejected'] }}</div>
                        <small class="text-muted">Rejected</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-sm-4 col-md-2">
                <div class="kpi-mini card border-0 shadow-sm">
                    <div class="card-body p-3 text-center">
                        <div class="fw-bold fs-4 lh-1 text-primary">{{ $this->counts['completionPct'] }}%</div>
                        <small class="text-muted">Screened</small>
                    </div>
                </div>
            </div>
        </div>
        @endcan

    <!-- Progress Bar -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <div class="d-flex justify-content-between small mb-2">
                <span class="fw-medium">Screening Progress</span>
                <span class="text-muted">{{ $this->counts['screened'] }} of {{ $this->counts['total'] }} applications screened</span>
            </div>
            <div class="progress" style="height:10px; border-radius:8px;">
                <div class="progress-bar bg-success" style="border-radius:8px 0 0 8px; width:{{ $this->counts['total'] ? round($this->counts['eligible'] / $this->counts['total'] * 100) : 0 }}%"></div>
                <div class="progress-bar bg-danger" style="width:{{ $this->counts['total'] ? round($this->counts['rejected'] / $this->counts['total'] * 100) : 0 }}%"></div>
                <div class="progress-bar bg-info" style="width:{{ $this->counts['total'] ? round($this->counts['inReview'] / $this->counts['total'] * 100) : 0 }}%"></div>
            </div>
            <div class="d-flex gap-3 mt-2 small text-muted flex-wrap">
                <span><span class="badge bg-success me-1">&nbsp;</span>Eligible</span>
                <span><span class="badge bg-danger me-1">&nbsp;</span>Rejected</span>
                <span><span class="badge bg-info me-1">&nbsp;</span>In Review</span>
                <span><span class="badge bg-warning me-1">&nbsp;</span>Submitted</span>
            </div>
        </div>
    </div>

    <!-- FILTERS + SEARCH -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-6">
                    <label class="form-label small fw-medium mb-1">Search</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control border-start-0" placeholder="Enterprise name, ID, owner…" wire:model.live.debounce.300ms="search">
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small fw-medium mb-1">District</label>
                    <select class="form-select form-select-sm" wire:model.live="districtFilter">
                        <option value="">All Districts</option>
                        @foreach($this->districts as $district)
                            <option value="{{ $district }}">{{ $district }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-3 d-flex gap-2">
                    <button class="btn btn-sm btn-outline-secondary w-100" wire:click="resetFilters">
                        <i class="bi bi-x-circle me-1"></i>Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- APPLICATIONS TABLE -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div class="d-flex align-items-center gap-3">
                <div class="form-check mb-0">
                    <input class="form-check-input" type="checkbox" id="selectAll">
                    <label class="form-check-label small fw-medium" for="selectAll">Select All</label>
                </div>
                <h6 class="fw-bold mb-0">Screening Queue</h6>
                <span class="badge bg-primary rounded-pill">{{ $this->applications->total() }} applications</span>
            </div>
            <div class="d-flex align-items-center gap-2 small text-muted">
                <span>Show:</span>
                <select class="form-select form-select-sm" style="width:auto;" wire:model.live="perPage">
                    <option value="10">10 / page</option>
                    <option value="20">20 / page</option>
                    <option value="50">50 / page</option>
                </select>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th class="px-3 py-3" style="width:40px;"></th>
                            <th class="py-3">Application</th>
                            <th class="py-3">District</th>
                            <th class="py-3">Stage</th>
                            <th class="py-3">Submitted</th>
                            <th class="py-3">Status</th>
                            <th class="py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($this->applications as $app)
                        <tr>
                            <td class="px-3"><input class="form-check-input" type="checkbox" value="{{ $app->id }}"></td>
                            <td class="py-3">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="app-avatar av-green"><span>{{ substr($app->company_name, 0, 2) }}</span></div>
                                    <div>
                                        <div class="fw-semibold text-dark">{{ $app->company_name }}</div>
                                        <div class="text-muted" style="font-size:0.72rem;">{{ $app->application_number }} · {{ $app->applicant_name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $app->district ?? 'N/A' }}</td>
                            <td><span class="badge bg-secondary bg-opacity-10 text-primary">{{ $app->company_stage ?? 'N/A' }}</span></td>
                            <td>{{ $app->submitted_at ? $app->submitted_at->format('d M Y') : 'N/A' }}</td>
                            <td>
                                @php
                                    $statusColors = [
                                        'submitted' => 'bg-warning text-dark',
                                        'in_review' => 'bg-info',
                                        'eligible' => 'bg-success',
                                        'rejected' => 'bg-danger',
                                    ];
                                @endphp
                                <span class="badge rounded-pill {{ $statusColors[$app->status] ?? 'bg-secondary' }}">
                                    {{ ucfirst(str_replace('_', ' ', $app->status ?? 'submitted')) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <!-- Only show action buttons when status is 'in_review' -->
                                    @if($app->status == 'in_review' || $app->status == 'eligible' || $app->status == 'rejected')
                                        <button class="btn btn-sm btn-outline-primary py-1 px-2" 
                                                wire:click="$dispatch('openReviewPanel', { applicationId: {{ $app->id }} })" 
                                                title="Review Application">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    @endif
                                    
                                    <!-- Move to Review button - only for submitted applications -->
                                   @if($app->status == 'submitted')
                                    <button class="btn btn-sm btn-outline-info py-1 px-2" 
                                            wire:click="moveToReview({{ $app->id }})" 
                                            wire:confirm="Are you sure you want to move this application to review? Once moved, you will be able to mark it as Eligible or Rejected."
                                            title="Move to Review">
                                        <i class="bi bi-hourglass-split"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-2 opacity-50"></i>
                                No applications found matching your criteria.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex flex-wrap align-items-center justify-content-between px-4 py-3 border-top gap-2">
                <small class="text-muted">
                    Showing {{ $this->applications->firstItem() ?? 0 }}–{{ $this->applications->lastItem() ?? 0 }} 
                    of {{ $this->applications->total() }} applications
                </small>
                {{ $this->applications->links() }}
            </div>
        </div>
    </div>
</div>
</div>

<style>
    [x-cloak] { display: none !important; }
    
    /* Dropdown item hover - primary background with white text */
    .dropdown-item:hover {
        background-color: #0f172a !important;
        color: white !important;
    }
    
    .dropdown-item:hover i,
    .dropdown-item:hover .bi-box-arrow-up-right {
        color: white !important;
    }
    
    /* Active state - primary background with white text */
    .dropdown-item.active {
        background-color: #0f172a;
        color: white !important;
    }
    
    /* Dropdown item base styling */
    .dropdown-item {
        transition: all 0.2s ease;
        cursor: pointer;
    }
    
    .dropdown-item i {
        transition: color 0.2s ease;
    }
</style>