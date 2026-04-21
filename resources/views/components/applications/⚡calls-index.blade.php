<?php

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

new class extends Component
{
    use WithPagination;
    
    // Filter properties
    public $search = '';
    public $filterStatus = '';
    public $filterCohort = '';
    public $filterYear = '';
    public $view = 'table';
    public $deleteId = null;
    
    // Reset pagination when filters change
    public function updatingSearch()
    {
        $this->resetPage();
    }
    
    public function updatingFilterStatus()
    {
        $this->resetPage();
    }
    
    public function updatingFilterCohort()
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
        $this->filterCohort = '';
        $this->filterYear = '';
        $this->resetPage();
    }


    #[On('refresh-calls')]
    public function refreshData()
    {
        $this->resetPage();
    }
    

        // Publish call
    public function publishCall($id)
    {
        DB::beginTransaction();
        
        try {
            $call = \App\Models\Call::findOrFail($id);
            
            $call->update([
                'status' => 'published',
                'published_by' => auth()->id(),
                'published_at' => now(),
            ]);
            
            // Auto-open if publish date is today or in the past
            if ($call->publish_date && $call->publish_date <= now()) {
                $call->update(['status' => 'open']);
            }
            
            DB::commit();
            
            $this->dispatch('notify', type: 'success', message: 'Call published successfully!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notify', type: 'error', message: 'Failed to publish call: ' . $e->getMessage());
        }
    }

    // Delete the call
    public function deleteCall($id)
    {
        $this->deleteId = $id;
        DB::beginTransaction();
        
        try {
            $call = \App\Models\Call::findOrFail($this->deleteId);
            
            // Optional: Check if call has applications before deleting
            if ($call->applications_count > 0) {
                $this->dispatch('notify', type: 'warning', message: 'Cannot delete call with existing applications.');
                return;
            }
            
            $callTitle = $call->title;
            $call->delete();
            
            DB::commit();
            
            $this->dispatch('notify', type: 'success', message: "Call '{$callTitle}' deleted successfully!");
            
            // Reset delete ID
            $this->deleteId = null;
            
            // Refresh the calls list (if you have a refresh listener)
            $this->dispatch('refresh-calls');
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notify', type: 'error', message: 'Failed to delete call: ' . $e->getMessage());
        }
    }

    
    // Unpublish call
    public function unpublishCall($id)
    {
        DB::beginTransaction();
        
        try {
            $call = \App\Models\Call::findOrFail($id);
            
            $call->update([
                'status' => 'draft',
                'published_by' => null,
                'published_at' => null,
            ]);
            
            DB::commit();
            
            $this->dispatch('notify', type: 'success', message: 'Call unpublished successfully!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notify', type: 'error', message: 'Failed to unpublish call: ' . $e->getMessage());
        }
    }
    
    // Get calls with filters
    public function getCallsProperty()
    {
        $query = \App\Models\Call::withCount('applications');
        if (!auth()->check() || !auth()->user()->hasRole('Super Administrator')) {
            $query->where('status', 'open');
        }
        
        return $query
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterStatus, function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->when($this->filterCohort, function ($query) {
                $query->where('cohort', $this->filterCohort);
            })
            ->when($this->filterYear, function ($query) {
                $query->whereYear('open_date', $this->filterYear);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }
    
    // Get statistics - hide draft counts from non-admins
    public function getTotalCallsProperty()
    {
        if (!auth()->check() || !auth()->user()->hasRole('Super Administrator')) {
            return \App\Models\Call::where('status', 'open')->count();
        }
        return \App\Models\Call::count();
    }

    public function getOpenCallsProperty()
    {
        return \App\Models\Call::where('status', 'open')->count();
    }

    public function getDraftCallsProperty()
    {
        // Only Super Admin can see draft count
        if (!auth()->check() || !auth()->user()->hasRole('Super Administrator')) {
            return 0;
        }
        return \App\Models\Call::where('status', 'draft')->count();
    }

    public function getTotalApplicationsProperty()
    {
        // Only Super Admin can see total applications
        if (!auth()->check() || !auth()->user()->hasRole('Super Administrator')) {
            return 0;
        }
        return \App\Models\Call::sum('applications_count');
    }
}

?>

<div>
    {{-- ═══════════════════════════════════════
         KPI STRIP
    ═══════════════════════════════════════ --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card kpi-card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3 p-3">
                    <div class="kpi-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-megaphone-fill"></i></div>
                    <div><div class="fw-bold fs-4 lh-1">{{ $this->totalCalls }}</div><small class="text-muted">Total Calls</small></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card kpi-card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3 p-3">
                    <div class="kpi-icon bg-success bg-opacity-10 text-white"><i class="bi bi-broadcast"></i></div>
                    <div><div class="fw-bold fs-4 lh-1">{{ $this->openCalls }}</div><small class="text-muted">Open Now</small></div>
                </div>
            </div>
        </div>
       @can('view Analytics & Reporting')

            <div class="col-6 col-md-3">
                <div class="card kpi-card border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3 p-3">
                        <div class="kpi-icon bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                        <div>
                            <div class="fw-bold fs-4 lh-1">{{ $this->draftCalls }}</div>
                            <small class="text-muted">Draft</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card kpi-card border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3 p-3">
                        <div class="kpi-icon bg-info bg-opacity-10 text-info">
                            <i class="bi bi-file-earmark-text-fill"></i>
                        </div>
                        <div>
                            <div class="fw-bold fs-4 lh-1">{{ $this->totalApplications }}</div>
                            <small class="text-muted">Total Applications</small>
                        </div>
                    </div>
                </div>
            </div>

        @endcan
    </div>

    {{-- ═══════════════════════════════════════
         FILTERS + SEARCH
    ═══════════════════════════════════════ --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-4">
                    <label class="form-label small fw-medium mb-1">Search</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" class="form-control border-start-0" placeholder="Search by title, cohort…" wire:model.live="search">
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-medium mb-1">Status</label>
                    <select class="form-select form-select-sm" wire:model.live="filterStatus">
                        <option value="">All Statuses</option>
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                        <option value="open">Open</option>
                        <option value="closed">Closed</option>
                        <option value="archived">Archived</option>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-medium mb-1">Year</label>
                    <select class="form-select form-select-sm" wire:model.live="filterYear">
                        <option value="">All Years</option>
                        <option value="2023">2023</option>
                        <option value="2024">2024</option>
                        <option value="2025">2025</option>
                    </select>
                </div>
                <div class="col-6 col-md-2 d-flex gap-2">
                    <button class="btn btn-sm btn-outline-secondary w-100" wire:click="resetFilters">
                        <i class="bi bi-x-circle me-1"></i>Reset
                    </button>
                    <button class="btn btn-sm btn-outline-primary w-100">
                        <i class="bi bi-download me-1"></i>Export
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════
         VIEW TOGGLE BUTTONS
    ═══════════════════════════════════════ --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
            <h6 class="fw-bold mb-0">All Calls</h6>
            <div class="d-flex align-items-center gap-2">
                <small class="text-muted">Showing {{ $this->calls->total() }} of {{ $this->totalCalls }} calls</small>
                <div class="btn-group btn-group-sm ms-2" role="group">
                   
                        <i class="bi bi-table"></i>
                    
                </div>
            </div>
        </div>

        {{-- TABLE VIEW --}}
        {{-- TABLE VIEW --}}
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3">Call Title</th>
                            <th class="py-3">Cohort</th>
                            <th class="py-3">Open Date</th>
                            <th class="py-3">Close Date</th>
                            @can('view Analytics & Reporting')
                                <th class="py-3 text-center">Applications</th>
                            @endcan
                            <th class="py-3">Status</th>
                            <th class="py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($this->calls as $call)
                        <tr wire:key="call-{{ $call->id }}">
                            <td class="px-4 py-3">
                                <div class="fw-semibold text-dark">{{ $call->title }}</div>
                                <div class="text-muted" style="font-size:0.75rem;">{{ Str::limit($call->description, 60) }}</div>
                            </td>
                            <td>
                                <span class="badge bg-primary bg-opacity-10 text-primary">Cohort {{ $call->cohort }}</span>
                            </td>
                            <td>{{ $call->open_date ? $call->open_date->format('d M Y') : '—' }}</td>
                            <td>{{ $call->close_date ? $call->close_date->format('d M Y') : '—' }}</td>
                            @can('view Analytics & Reporting')
                                <td class="text-center">
                                    <span class="fw-semibold">{{ $call->applications_count }}</span>
                                </td>
                            @endcan
                            <td>
                                @php
                                    $statusColors = [
                                        'draft' => 'bg-warning text-dark',
                                        'published' => 'bg-primary text-white',
                                        'open' => 'bg-success text-white',
                                        'closed' => 'bg-secondary text-white',
                                        'archived' => 'bg-dark text-white',
                                    ];
                                @endphp
                                <span class="badge rounded-pill {{ $statusColors[$call->status] ?? 'bg-light text-muted' }}">
                                    {{ ucfirst($call->status) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    
                                    <!-- View (visible to everyone) -->
                                    <a href="{{ route('call.show', $call->id) }}">
                                        <button class="btn btn-sm btn-outline-primary py-1 px-2" title="View details">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </a>

                                    <!-- Only Super Admin -->
                            

                                        @can('edit Calls for Applications')
                                            <a href="#"
                                            class="btn btn-sm btn-outline-secondary py-1 px-2"
                                            title="Edit"
                                            wire:click="$dispatch('edit-call', { id: {{ $call->id }} })">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        @endcan

                                        @can('approve Calls for Applications')

                                            @if($call->status === 'draft')
                                                <button class="btn btn-sm btn-outline-success py-1 px-2" 
                                                        wire:click="publishCall({{ $call->id }})" 
                                                        title="Publish"
                                                        wire:confirm="Are you sure you want to publish this call? It will become visible to applicants.">
                                                    <i class="bi bi-broadcast"></i>
                                                </button>

                                            @elseif($call->status !== 'archived' && $call->status !== 'closed')
                                                <button class="btn btn-sm btn-outline-warning py-1 px-2" 
                                                        title="Unpublish"
                                                        wire:click="unpublishCall({{ $call->id }})"
                                                        wire:confirm="Are you sure you want to unpublish this call? It will be moved back to draft.">
                                                    <i class="bi bi-pause-circle"></i>
                                                </button>
                                            @endif

                                        @endcan

                                        @can('delete Calls for Applications')
                                            <button class="btn btn-sm btn-outline-danger py-1 px-2" 
                                                    title="Delete"
                                                    wire:click="deleteCall({{ $call->id }})"
                                                    wire:confirm="Are you sure you want to delete this call? This action cannot be undone.">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        @endcan

                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ auth()->check() && auth()->user()->hasRole('Super Administrator') ? '7' : '6' }}" class="text-center text-muted py-5">
                                <i class="bi bi-inbox fs-3 d-block mb-2 opacity-50"></i>
                                No calls found matching your filters.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white border-top py-3">
                {{ $this->calls->links() }}
            </div>
        </div>
            
    </div>
</div>

<style>

</style>