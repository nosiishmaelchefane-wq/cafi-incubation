<?php

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Cohort;

new class extends Component
{
    public $showCreateCohortModal = false;
    public $editMode = false;
    public $cohortId = null;
    
    // Form fields - set default without function calls
    public $cohort_number = '';
    public $name = '';
    public $year = '';
    public $duration_months = '6';
    public $start_date = '';
    public $end_date = '';
    public $target_enterprises = '';
    public $status = 'Draft';
    public $description = '';

    protected $rules = [
        'cohort_number' => 'required|integer|min:1',
        'name' => 'required|string|max:255',
        'year' => 'required|integer|min:2020|max:2030',
        'duration_months' => 'required|in:6,9,12',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after:start_date',
        'target_enterprises' => 'nullable|integer|min:0',
        'status' => 'required|in:Draft,Active,Completed,Archived',
        'description' => 'nullable|string|max:1000',
    ];

    public function mount()
    {
        $this->year = date('Y');
    }

    #[On('open-create-cohort-modal')]
    public function openModal()
    {
        $this->resetForm();
        $this->editMode = false;
        $this->cohortId = null;
        $this->showCreateCohortModal = true;
    }

    #[On('open-edit-cohort-modal')]
    public function openEditModal($cohortId)
    {
        $this->editMode = true;
        $this->cohortId = $cohortId;
        $this->loadCohortData($cohortId);
        $this->showCreateCohortModal = true;
    }

    public function loadCohortData($cohortId)
    {
        $cohort = Cohort::findOrFail($cohortId);
        
        $this->cohort_number = $cohort->cohort_number;
        $this->name = $cohort->name;
        $this->year = $cohort->year;
        $this->duration_months = $cohort->duration_months;
        $this->start_date = $cohort->start_date->format('Y-m-d');
        $this->end_date = $cohort->end_date->format('Y-m-d');
        $this->target_enterprises = $cohort->target_enterprises;
        $this->status = $cohort->status;
        $this->description = $cohort->description;
    }

    public function closeModal()
    {
        $this->showCreateCohortModal = false;
        $this->resetForm();
        $this->resetErrorBag();
        $this->editMode = false;
        $this->cohortId = null;
    }

    public function resetForm()
    {
        $this->cohort_number = '';
        $this->name = '';
        $this->year = date('Y');
        $this->duration_months = '6';
        $this->start_date = '';
        $this->end_date = '';
        $this->target_enterprises = '';
        $this->status = 'Draft';
        $this->description = '';
    }

    public function save()
    {
        // Dynamic validation rules for unique check in edit mode
        if (!$this->editMode) {
            $this->rules['cohort_number'] = 'required|integer|min:1|unique:cohorts,cohort_number';
        } else {
            $this->rules['cohort_number'] = 'required|integer|min:1|unique:cohorts,cohort_number,' . $this->cohortId;
        }
        
        $this->validate();

        try {
            if ($this->editMode) {
                $cohort = Cohort::findOrFail($this->cohortId);
                $cohort->update([
                    'cohort_number' => $this->cohort_number,
                    'name' => $this->name,
                    'year' => $this->year,
                    'duration_months' => $this->duration_months,
                    'start_date' => $this->start_date,
                    'end_date' => $this->end_date,
                    'target_enterprises' => $this->target_enterprises ?: null,
                    'status' => $this->status,
                    'description' => $this->description,
                ]);

                $this->dispatch('notify', type: 'success', message: "Cohort {$this->cohort_number} - {$this->name} updated successfully!");
                $this->dispatch('cohort-updated', cohortId: $cohort->id);
            } else {
                $cohort = Cohort::create([
                    'cohort_number' => $this->cohort_number,
                    'name' => $this->name,
                    'year' => $this->year,
                    'duration_months' => $this->duration_months,
                    'start_date' => $this->start_date,
                    'end_date' => $this->end_date,
                    'target_enterprises' => $this->target_enterprises ?: null,
                    'status' => $this->status,
                    'description' => $this->description,
                    'created_by' => auth()->id(),
                ]);

                $this->dispatch('notify', type: 'success', message: "Cohort {$this->cohort_number} - {$this->name} created successfully!");
                $this->dispatch('cohort-created', cohortId: $cohort->id);
            }
            
            $this->closeModal();

        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Failed to ' . ($this->editMode ? 'update' : 'create') . ' cohort: ' . $e->getMessage());
        }
    }
};
?>

<div>
    @if($showCreateCohortModal)
    <div class="modal fade show d-block" tabindex="-1" aria-hidden="true" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <div>
                        <div class="text-success fw-semibold small mb-1">Incubation Management</div>
                        <h5 class="modal-title fw-bold">{{ $editMode ? 'Edit Cohort' : 'Create New Cohort' }}</h5>
                        <p class="text-muted small mb-0">
                            {{ $editMode ? 'Update the cohort information.' : 'Define a new incubation cohort. This will be used to group calls for applications.' }}
                        </p>
                    </div>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>

                <form wire:submit.prevent="save">
                    <div class="modal-body px-4">
                        <div class="row g-3">
                            {{-- Cohort Number --}}
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Cohort Number <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('cohort_number') is-invalid @enderror" 
                                       wire:model="cohort_number" min="1" placeholder="e.g. 4" {{ $editMode ? 'disabled' : '' }}>
                                @error('cohort_number') <span class="text-danger small">{{ $message }}</span> @enderror
                                <small class="text-muted">Unique cohort identifier</small>
                                @if($editMode)
                                    <input type="hidden" wire:model="cohort_number">
                                @endif
                            </div>

                            {{-- Cohort Name --}}
                            <div class="col-md-8">
                                <label class="form-label fw-semibold small">Cohort Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       wire:model="name" placeholder="e.g. LEHSFF Cohort 4 – Incubation Programme">
                                @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            {{-- Year + Duration --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Year <span class="text-danger">*</span></label>
                                <select class="form-select @error('year') is-invalid @enderror" wire:model="year">
                                    <option value="2023">2023</option>
                                    <option value="2024">2024</option>
                                    <option value="2025">2025</option>
                                    <option value="2026">2026</option>
                                </select>
                                @error('year') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Duration (months) <span class="text-danger">*</span></label>
                                <select class="form-select @error('duration_months') is-invalid @enderror" wire:model="duration_months">
                                    <option value="6">6 months</option>
                                    <option value="9">9 months</option>
                                    <option value="12">12 months</option>
                                </select>
                                @error('duration_months') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            {{-- Dates --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Start Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror" wire:model="start_date">
                                @error('start_date') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">End Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror" wire:model="end_date">
                                @error('end_date') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            {{-- Target Enterprises --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Target Enterprises</label>
                                <input type="number" class="form-control @error('target_enterprises') is-invalid @enderror" 
                                       wire:model="target_enterprises" placeholder="e.g. 50">
                                @error('target_enterprises') <span class="text-danger small">{{ $message }}</span> @enderror
                                <small class="text-muted">Number of enterprises to target in this cohort</small>
                            </div>

                            {{-- Status --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Status <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" wire:model="status">
                                    <option value="Draft">Draft</option>
                                    <option value="Active">Active</option>
                                    <option value="Completed">Completed</option>
                                </select>
                                @error('status') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            {{-- Description --}}
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Description / Notes</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          wire:model="description" rows="3" 
                                          placeholder="Brief notes about this cohort's focus, sector, or special conditions…"></textarea>
                                @error('description') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0 pb-4 px-4">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">Cancel</button>
                        <button type="submit" class="btn btn-{{ $editMode ? 'primary' : 'success' }}" wire:loading.attr="disabled">
                            <span wire:loading.remove>
                                <i class="bi bi-{{ $editMode ? 'check-circle' : 'plus-circle' }}-fill me-1"></i>
                                {{ $editMode ? 'Update Cohort' : 'Create Cohort' }}
                            </span>
                            <span wire:loading>
                                <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                                {{ $editMode ? 'Updating...' : 'Creating...' }}
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>