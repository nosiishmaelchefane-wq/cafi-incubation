<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\IncubationApplication;
use App\Models\Call;
use Illuminate\Support\Facades\Auth;

new class extends Component
{
    use WithPagination;
    
    public $id;
    public $call;
    
    // Filters
    public $search = '';
    public $sectorFilter = '';
    public $districtFilter = '';
    public $stageFilter = '';
    public $statusFilter = '';
    public $perPage = 10;
    
    // Bulk actions
    public $selectedApplications = [];
    public $selectAll = false;
    
    public function mount($id = null)
    {
        if ($id) {
            $this->id = $id;
            $this->call = Call::findOrFail($id);
        }
    }
    
    public function getApplicationsProperty()
    {
        $query = \App\Models\IncubationApplication::query()
            ->with('user');
        
        if (!auth()->user()->hasRole('Super Administrator')) {
            $query->where('user_id', auth()->id());
        } else {
            // For Super Admin, handle draft exclusion based on status filter
            if (empty($this->statusFilter)) {
                // If no status filter is applied (All tab), exclude drafts
                $query->where('status', '!=', 'draft');
            } elseif ($this->statusFilter === 'draft') {
                // If specifically filtering for drafts, show them
                $query->where('status', 'draft');
            } else {
                // For other status filters (submitted, in_review, eligible, shortlisted, rejected), exclude drafts
                $query->where('status', '!=', 'draft')
                    ->where('status', $this->statusFilter);
            }
        }

        if ($this->id) {
            $query->where('call_id', $this->id);
        }

        if ($this->search) {
            $query->where(function($q) {
                $q->where('company_name', 'like', '%' . $this->search . '%')
                    ->orWhere('applicant_name', 'like', '%' . $this->search . '%')
                    ->orWhere('application_number', 'like', '%' . $this->search . '%')
                    ->orWhere('applicant_email', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->sectorFilter) {
            $query->where('sector', $this->sectorFilter);
        }

        if ($this->districtFilter) {
            $query->where('district', $this->districtFilter);
        }

        if ($this->stageFilter) {
            $query->where('company_stage', $this->stageFilter);
        }

        if ($this->statusFilter) {
            // Only apply status filter if it's not already handled above for Super Admin
            if (!auth()->user()->hasRole('Super Administrator')) {
                $query->where('status', $this->statusFilter);
            }
        }

        return $query->orderBy('created_at', 'desc')->paginate($this->perPage);
    }
    
   public function getStatusCountsProperty()
    {
        $query = IncubationApplication::where('call_id', $this->id);
        
        return [
            'all' => (clone $query)->where('status', '!=', 'draft')->count(),
            'submitted' => (clone $query)->where('status', 'submitted')->count(),
            'in_review' => (clone $query)->where('status', 'in_review')->count(),
            'eligible' => (clone $query)->where('status', 'eligible')->count(),
            'shortlisted' => (clone $query)->where('status', 'shortlisted')->count(),
            'rejected' => (clone $query)->where('status', 'rejected')->count(),
            'draft' => (clone $query)->where('status', 'draft')->count(),
        ];
    }
    
    public function updateStatus($applicationId, $status)
    {
        $application = IncubationApplication::findOrFail($applicationId);
        $application->status = $status;
        $application->save();
        
        $this->dispatch('notify', type: 'success', message: 'Application status updated successfully!');
        $this->resetPage();
    }
    
    public function bulkUpdateStatus($status)
    {
        if (empty($this->selectedApplications)) {
            $this->dispatch('notify', type: 'error', message: 'No applications selected');
            return;
        }
        
        IncubationApplication::whereIn('id', $this->selectedApplications)
            ->update(['status' => $status]);
        
        $count = count($this->selectedApplications);
        $this->dispatch('notify', type: 'success', message: "{$count} application(s) updated to " . ucfirst($status));
        
        $this->selectedApplications = [];
        $this->selectAll = false;
        $this->resetPage();
    }
    
    public function toggleSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedApplications = $this->applications->pluck('id')->toArray();
        } else {
            $this->selectedApplications = [];
        }
    }
    
    public function exportApplications()
    {
        $applications = IncubationApplication::where('call_id', $this->id)->get();
        // Add export logic here
        $this->dispatch('notify', type: 'success', message: 'Export started!');
    }


    public function submitDraftApplication($applicationId)
    {
        $application = IncubationApplication::findOrFail($applicationId);
        
        // Only allow if current status is Draft
        if ($application->status !== 'draft') {
            $this->dispatch('notify', type: 'error', message: 'Only draft applications can be submitted.');
            return;
        }
        
        // Update the status to Submitted
        $application->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);
        
        
        $this->dispatch('application-updated');
        $this->dispatch('refresh-applications');
        $this->dispatch('notify', type: 'success', message: 'Application #' . $application->application_number . ' has been submitted successfully!');
    }
    
    public function resetFilters()
    {
        $this->search = '';
        $this->sectorFilter = '';
        $this->districtFilter = '';
        $this->stageFilter = '';
        $this->statusFilter = '';
        $this->resetPage();
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
        return $classes[$status] ?? 'st-pending';
    }
    
    public function updatedSearch()
    {
        $this->resetPage();
    }
    
    public function updatedSectorFilter()
    {
        $this->resetPage();
    }
    
    public function updatedDistrictFilter()
    {
        $this->resetPage();
    }
    
    public function updatedStageFilter()
    {
        $this->resetPage();
    }
    
    public function updatedStatusFilter()
    {
        $this->resetPage();
    }
    
    public function updatedPerPage()
    {
        $this->resetPage();
    }
}
?>

<div>
    <div class="cds-card">
        <div class="cds-card-header px-4 pt-4 pb-0 flex-column align-items-stretch gap-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <div class="cds-icon-sm"><i class="bi bi-file-earmark-person-fill"></i></div>
                        <h5 class="fw-bold mb-0">Applications</h5>
                    </div>
                    <p class="small text-muted mb-0">All submissions received for this call</p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <button class="btn cds-btn-ghost btn-sm" wire:click="exportApplications">
                        <i class="bi bi-download me-1"></i>Export
                    </button>
                    @if(count($selectedApplications) > 0)
                        <div class="dropdown">
                            <button class="btn cds-btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="bi bi-check2-square me-1"></i>
                                Bulk Action ({{ count($selectedApplications) }})
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" wire:click.prevent="bulkUpdateStatus('eligible')">Mark Eligible</a></li>
                                <li><a class="dropdown-item" href="#" wire:click.prevent="bulkUpdateStatus('in_review')">Move to Review</a></li>
                                <li><a class="dropdown-item" href="#" wire:click.prevent="bulkUpdateStatus('shortlisted')">Shortlist</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="#" wire:click.prevent="bulkUpdateStatus('rejected')">Reject</a></li>
                            </ul>
                        </div>
                    @else
                        <button class="btn cds-btn-primary btn-sm opacity-40 pe-none" disabled>
                            <i class="bi bi-check2-square me-1"></i>Bulk Action
                        </button>
                    @endif
                </div>
            </div>
            
            <!-- Status Tabs -->
            <!-- Status Tabs -->
            @if(auth()->check() && auth()->user()->hasRole('Super Administrator'))
                <div class="cds-tab-row">
                    <button class="cds-tab-btn {{ $statusFilter === '' ? 'cds-tab-active' : '' }}" wire:click="$set('statusFilter', '')">
                        All <span class="cds-tab-count">{{ $this->statusCounts['all'] }}</span>
                    </button>
                    <button class="cds-tab-btn {{ $statusFilter === 'submitted' ? 'cds-tab-active' : '' }}" wire:click="$set('statusFilter', 'submitted')">
                        Submitted <span class="cds-tab-count">{{ $this->statusCounts['submitted'] }}</span>
                    </button>
                    <button class="cds-tab-btn {{ $statusFilter === 'in_review' ? 'cds-tab-active' : '' }}" wire:click="$set('statusFilter', 'in_review')">
                        In Review <span class="cds-tab-count">{{ $this->statusCounts['in_review'] }}</span>
                    </button>
                    <button class="cds-tab-btn {{ $statusFilter === 'eligible' ? 'cds-tab-active' : '' }}" wire:click="$set('statusFilter', 'eligible')">
                        Eligible <span class="cds-tab-count">{{ $this->statusCounts['eligible'] }}</span>
                    </button>
                    <button class="cds-tab-btn {{ $statusFilter === 'shortlisted' ? 'cds-tab-active' : '' }}" wire:click="$set('statusFilter', 'shortlisted')">
                        Shortlisted <span class="cds-tab-count">{{ $this->statusCounts['shortlisted'] }}</span>
                    </button>
                    <button class="cds-tab-btn {{ $statusFilter === 'rejected' ? 'cds-tab-active' : '' }}" wire:click="$set('statusFilter', 'rejected')">
                        Rejected <span class="cds-tab-count">{{ $this->statusCounts['rejected'] }}</span>
                    </button>
                    <button class="cds-tab-btn {{ $statusFilter === 'draft' ? 'cds-tab-active' : '' }}" wire:click="$set('statusFilter', 'draft')">
                        Draft <span class="cds-tab-count">{{ $this->statusCounts['draft'] }}</span>
                    </button>
                </div>
            @endif
        </div>
        
        <!-- Filter Bar -->
        <div class="cds-filter-bar">
            <div class="row g-2 align-items-center">
                <div class="col-12 col-md-4">
                    <div class="cds-search-wrap">
                        <i class="bi bi-search cds-search-icon"></i>
                        <input type="text" class="cds-search-input" placeholder="Search enterprise, owner, ID…" wire:model.live.debounce.300ms="search">
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <select class="cds-select" wire:model.live="sectorFilter">
                        <option value="">All Sectors</option>
                        <option>Primary</option>
                        <option>Secondary</option>
                        <option>Tertiary</option>
                        <option>Quaternary</option>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <select class="cds-select" wire:model.live="districtFilter">
                        <option value="">All Districts</option>
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
                </div>
                <div class="col-6 col-md-2">
                    <select class="cds-select" wire:model.live="stageFilter">
                        <option value="">All Stages</option>
                        <option>Startup</option>
                        <option>Growth</option>
                        <option>Expansion</option>
                        <option>Mature</option>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <button class="btn cds-btn-ghost btn-sm w-100" wire:click="resetFilters">
                        <i class="bi bi-x-circle me-1"></i>Reset
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Table -->
        <div class="table-responsive">
            <table class="table cds-table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="px-4" style="width:42px;">
                            <input class="cds-check" type="checkbox" wire:model.live="selectAll" wire:change="toggleSelectAll">
                        </th>
                        <th style="min-width:210px;">Application #</th>
                        <th>Enterprise</th>
                        <th>Applicant</th>
                        <th>Sector</th>
                        <th>District</th>
                        <th>Submitted</th>
                        <th>Status</th>
                        <th class="text-center pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($this->applications as $app)
                        <tr @class(['cds-row-selected' => in_array($app->id, $selectedApplications)])>
                            <td class="px-4">
                                <input class="cds-check" type="checkbox" value="{{ $app->id }}" wire:model.live="selectedApplications">
                            </td>
                            <td class="py-3">
                                <div class="fw-semibold text-dark small">{{ $app->application_number }}</div>
                                <div class="cds-sub-text">{{ $app->created_at->format('d M Y, H:i') }}</div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="cds-av {{ $this->getAvColor($app->sector) }}">
                                        <span>{{ $this->getInitials($app->company_name) }}</span>
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-dark small">{{ $app->company_name }}</div>
                                        @if($app->registered_company_name)
                                            <div class="cds-sub-text">{{ $app->registered_company_name }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="small">{{ $app->applicant_name }}</div>
                                <div class="cds-sub-text">{{ $app->applicant_email }}</div>
                            </td>
                            <td>
                                <span class="cds-sector-tag cds-sector-tag-sm">{{ $app->sector ?? 'N/A' }}</span>
                            </td>
                            <td class="small">{{ $app->district ?? 'N/A' }}</td>
                            <td class="small">{{ $app->submitted_at ? $app->submitted_at->format('d M Y') : 'N/A' }}</td>
                            <td>
                                <span class="cds-app-status {{ $this->getAppStatusClass($app->status) }}">
                                    {{ ucfirst(str_replace('_', ' ', $app->status)) }}
                                </span>
                            </td>
                           <td class="text-center pe-4">
                                <div class="d-flex justify-content-center gap-1">
                                    <!-- View button: visible to all -->
                                    <a href="{{ route('incubation.show', $app->id) }}" class="cds-action-btn" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                        @if($app->status === 'draft' && auth()->id() === $app->user_id)
                                            <button class="btn btn-sm btn-outline-success py-1 px-2" 
                                                    title="Submit Application"
                                                    wire:click="submitDraftApplication({{ $app->id }})"
                                                    wire:confirm="Are you sure you want to submit this application? Once submitted, you will not be able to make any further changes.">
                                                <i class="bi bi-send-check-fill"></i>
                                            </button>
                                        @endif
                                    <!-- Dropdown: only for Super Administrator -->
                                    @role('Super Administrator')
                                        @if($app->status !== 'draft')
                                            <div class="cds-dropdown-container">
                                                <button class="cds-action-btn" type="button" title="Change Status">
                                                    <i class="bi bi-three-dots-vertical"></i>
                                                </button>
                                                <ul class="cds-dropdown-menu">
                                                    <li>
                                                        <a class="cds-dropdown-item" href="#" wire:click.prevent="updateStatus({{ $app->id }}, 'eligible')">
                                                            <i class="bi bi-check-circle-fill text-success"></i> Mark Eligible
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="cds-dropdown-item" href="#" wire:click.prevent="updateStatus({{ $app->id }}, 'in_review')">
                                                            <i class="bi bi-hourglass-split text-info"></i> Move to Review
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="cds-dropdown-item" href="#" wire:click.prevent="updateStatus({{ $app->id }}, 'shortlisted')">
                                                            <i class="bi bi-star-fill text-warning"></i> Shortlist
                                                        </a>
                                                    </li>
                                                    <li class="cds-dropdown-divider"></li>
                                                    <li>
                                                        <a class="cds-dropdown-item text-danger" href="#" wire:click.prevent="updateStatus({{ $app->id }}, 'rejected')">
                                                            <i class="bi bi-x-circle-fill"></i> Reject
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        @endif
                                    @endrole
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="cds-empty-state">
                                    <i class="bi bi-inbox"></i>
                                    <p>No applications found for this call.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($this->applications->hasPages())
            <div class="cds-pagination-bar">
                <small class="text-muted">
                    Showing {{ $this->applications->firstItem() ?? 0 }}–{{ $this->applications->lastItem() ?? 0 }} of {{ $this->applications->total() }}
                </small>
                <div class="d-flex align-items-center gap-2">
                    <select class="cds-select" style="width:auto;" wire:model.live="perPage">
                        <option value="10">10 / page</option>
                        <option value="25">25 / page</option>
                        <option value="50">50 / page</option>
                        <option value="100">100 / page</option>
                    </select>
                    {{ $this->applications->links() }}
                </div>
            </div>
        @endif
    </div>
</div>