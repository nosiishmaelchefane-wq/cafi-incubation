<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Cohort;
use Livewire\Attributes\On;

new class extends Component
{
    use WithPagination;
    
    // Filter properties
    public $search = '';
    public $filterStatus = '';
    public $filterYear = '';
    public $deleteId = null;
    
    // Modal states
    public $showDeleteModal = false;
    public $cohortToDelete = null;
    
    // Reset pagination when filters change
    public function updatingSearch()
    {
        $this->resetPage();
    }
    
    public function updatingFilterStatus()
    {
        $this->resetPage();
    }
    
    public function updatingFilterYear()
    {
        $this->resetPage();
    }
    
    // Reset all filters
    public function resetFilters()
    {
        $this->search = '';
        $this->filterStatus = '';
        $this->filterYear = '';
        $this->resetPage();
    }
    
    // Get cohorts with filters
    public function getCohortsProperty()
    {
        return Cohort::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('cohort_number', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterStatus, function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->when($this->filterYear, function ($query) {
                $query->where('year', $this->filterYear);
            })
            ->orderBy('cohort_number', 'desc')
            ->paginate(10);
    }
    
    // Get statistics
    public function getTotalCohortsProperty()
    {
        return Cohort::count();
    }
    
    public function getActiveCohortsProperty()
    {
        return Cohort::where('status', 'Active')->count();
    }
    
    public function getDraftCohortsProperty()
    {
        return Cohort::where('status', 'Draft')->count();
    }
    
    public function getCompletedCohortsProperty()
    {
        return Cohort::where('status', 'Completed')->count();
    }
    
    // Delete confirmation
    public function confirmDelete($id)
    {
        $this->cohortToDelete = Cohort::findOrFail($id);
        $this->showDeleteModal = true;
    }
    
    // Close delete modal
    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->cohortToDelete = null;
    }
    
    // Delete cohort
    public function deleteCohort()
    {
        try {
            $cohortName = $this->cohortToDelete->name;
            $this->cohortToDelete->delete();
            
            $this->dispatch('notify', type: 'success', message: "Cohort '{$cohortName}' deleted successfully!");
            $this->closeDeleteModal();
            $this->resetPage();
            
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Failed to delete cohort: ' . $e->getMessage());
        }
    }
    
    // Edit cohort - dispatch event to open edit modal
    public function editCohort($id)
    {
        $this->dispatch('edit-cohort', cohortId: $id);
    }
    
    // Refresh the list when cohort is created/updated
    #[On('cohort-created')]
    #[On('cohort-updated')]
    public function refreshCohorts()
    {
        $this->resetPage();
    }
};

?>

<div>
    <div class="container-fluid py-4 px-3 px-md-4">

        {{-- BREADCRUMB + PAGE HEADER --}}
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb small mb-0">
                <li class="breadcrumb-item">
                    <a href="#" class="text-decoration-none text-muted">
                        <i class="bi bi-house-fill"></i> Home
                    </a>
                </li>
                <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-muted">Incubation</a></li>
                <li class="breadcrumb-item active fw-semibold">Cohort Management</li>
            </ol>
        </nav>

        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
            <div>
                <h4 class="fw-bold mb-1">
                    <i class="bi bi-layers-fill text-primary me-2"></i>Cohort Management
                </h4>
                <p class="text-muted small mb-0">
                    Manage incubation cohorts · Track progress · Monitor compliance
                </p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <button class="btn btn-outline-secondary btn-sm" wire:click="$dispatch('export-cohorts')">
                    <i class="bi bi-download me-1"></i>Export All
                </button>
                <button class="btn btn-primary btn-sm" wire:click="$dispatch('open-create-cohort-modal')">
                    <i class="bi bi-plus-circle-fill me-1"></i>New Cohort
                </button>
            </div>
        </div>

        {{-- KPI STRIP --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center gap-3 p-3">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2">
                            <i class="bi bi-layers-fill fs-4"></i>
                        </div>
                        <div>
                            <div class="fw-bold fs-4 lh-1 text-primary">{{ $this->totalCohorts }}</div>
                            <small class="text-muted">Total Cohorts</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center gap-3 p-3">
                        <div class="bg-success bg-opacity-10 text-white rounded-3 p-2">
                            <i class="bi bi-play-circle-fill fs-4"></i>
                        </div>
                        <div>
                            <div class="fw-bold fs-4 lh-1 text-success">{{ $this->activeCohorts }}</div>
                            <small class="text-muted">Active</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center gap-3 p-3">
                        <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-2">
                            <i class="bi bi-hourglass-split fs-4"></i>
                        </div>
                        <div>
                            <div class="fw-bold fs-4 lh-1 text-warning">{{ $this->draftCohorts }}</div>
                            <small class="text-muted">Draft</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center gap-3 p-3">
                        <div class="bg-info bg-opacity-10 text-info rounded-3 p-2">
                            <i class="bi bi-check-circle-fill fs-4"></i>
                        </div>
                        <div>
                            <div class="fw-bold fs-4 lh-1 text-info">{{ $this->completedCohorts }}</div>
                            <small class="text-muted">Completed</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- FILTER BAR --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-3">
                <div class="row g-2 align-items-end">
                    <div class="col-12 col-md-4">
                        <label class="form-label small fw-medium mb-1">Search</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" class="form-control border-start-0" placeholder="Search cohort name, number…" wire:model.live="search">
                        </div>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label small fw-medium mb-1">Status</label>
                        <select class="form-select form-select-sm" wire:model.live="filterStatus">
                            <option value="">All Statuses</option>
                            <option value="Draft">Draft</option>
                            <option value="Active">Active</option>
                            <option value="Completed">Completed</option>
                            <option value="Archived">Archived</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label small fw-medium mb-1">Year</label>
                        <select class="form-select form-select-sm" wire:model.live="filterYear">
                            <option value="">All Years</option>
                            <option value="2023">2023</option>
                            <option value="2024">2024</option>
                            <option value="2025">2025</option>
                            <option value="2026">2026</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <button class="btn btn-outline-secondary btn-sm w-100" wire:click="resetFilters">
                            <i class="bi bi-x-circle me-1"></i>Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- TABLE VIEW --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                <h6 class="fw-bold mb-0">All Cohorts</h6>
                <small class="text-muted">{{ $this->cohorts->total() }} cohorts total</small>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4 py-3">Cohort</th>
                                <th class="py-3">Cycle</th>
                                <th class="py-3">Status</th>
                                <th class="py-3 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($this->cohorts as $cohort)
                            <tr wire:key="cohort-{{ $cohort->id }}">
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-3 px-3 py-2">
                                            C{{ $cohort->cohort_number }}
                                        </span>
                                        <div>
                                            <div class="fw-semibold small">{{ $cohort->name }}</div>
                                            <div class="text-muted" style="font-size:0.7rem;">Cohort {{ $cohort->cohort_number }} · {{ $cohort->year }}</div>
                                        </div>
                                    </div>
                                 </td>
                                <td class="py-3">
                                    <div>{{ $cohort->start_date ? $cohort->start_date->format('M Y') : 'N/A' }}</div>
                                    <div class="text-muted" style="font-size:0.7rem;">to {{ $cohort->end_date ? $cohort->end_date->format('M Y') : 'N/A' }}</div>
                                 </td>
                                <td class="py-3">
                                    @php
                                        $statusColors = [
                                            'Draft' => 'warning',
                                            'Active' => 'success',
                                            'Completed' => 'info',
                                            'Archived' => 'secondary',
                                        ];
                                        
                                        $statusTextColors = [
                                            'Draft' => 'dark',
                                            'Active' => 'white',
                                            'Completed' => 'dark',
                                            'Archived' => 'white',
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $statusColors[$cohort->status] ?? 'secondary' }} bg-opacity-10 text-{{ $statusTextColors[$cohort->status] ?? 'dark' }} rounded-pill px-2 py-1">
                                        {{ $cohort->status }}
                                    </span>
                                </td>
                                    <td class="py-3 text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            
                                            <!-- View button: visible to all -->
                                            <a href="#" class="cds-action-btn" title="View">
                                                <i class="bi bi-eye"></i>
                                            </a>

                                            <!-- Dropdown: only for Super Administrator -->
                                            <div class="cds-dropdown-container">
                                                <a class="cds-action-btn" href="{{ route('cohorts.show', $cohort->id) }}" title="More">
                                                    <i class="bi bi-three-dots-vertical"></i>
                                                </a>
                                                <ul class="cds-dropdown-menu">
                                                    <li>
                                                        <a class="cds-dropdown-item" href="#" wire:click.prevent="$dispatch('open-edit-cohort-modal', { cohortId: {{ $cohort->id }} })">
                                                            <i class="bi bi-pencil text-primary"></i> Edit Cohort
                                                        </a>
                                                    </li>
                                                    <li class="cds-dropdown-divider"></li>
                                                    <li>
                                                        <a class="cds-dropdown-item text-danger" href="#" wire:click.prevent="confirmDelete({{ $cohort->id }})">
                                                            <i class="bi bi-trash3-fill"></i> Delete Cohort
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                             </tr>
                            @empty
                             <tr>
                                <td colspan="4" class="text-center py-5">
                                    <i class="bi bi-inbox fs-1 text-muted opacity-50 d-block mb-2"></i>
                                    <p class="text-muted mb-2">No cohorts found.</p>
                                    <button class="btn btn-primary btn-sm" wire:click="$dispatch('open-create-cohort-modal')">
                                        <i class="bi bi-plus-circle me-1"></i>Create your first cohort
                                    </button>
                                 </td>
                             </tr>
                            @endforelse
                        </tbody>
                     </table>
                </div>
                @if($this->cohorts->hasPages())
                <div class="card-footer bg-white border-top py-3">
                    {{ $this->cohorts->links() }}
                </div>
                @endif
            </div>
        </div>

        {{-- DELETE CONFIRMATION MODAL --}}
        @if($showDeleteModal)
        <div class="modal fade show d-block" tabindex="-1" aria-hidden="true" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold text-danger">Delete Cohort</h5>
                        <button type="button" class="btn-close" wire:click="closeDeleteModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center py-3">
                            <i class="bi bi-exclamation-triangle-fill text-danger fs-1 mb-3 d-block"></i>
                            <h6 class="fw-bold">Are you absolutely sure?</h6>
                            <p class="text-muted small">
                                You are about to delete <strong>{{ $cohortToDelete?->name }}</strong>.
                                This action <strong class="text-danger">cannot be undone</strong>.
                            </p>
                            <p class="text-muted small mb-0">
                                All data associated with this cohort will be permanently removed.
                            </p>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary" wire:click="closeDeleteModal">Cancel</button>
                        <button type="button" class="btn btn-danger" wire:click="deleteCohort" wire:loading.attr="disabled">
                            <span wire:loading.remove><i class="bi bi-trash-fill me-1"></i>Yes, Delete Cohort</span>
                            <span wire:loading>Deleting...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif

    </div>
</div>