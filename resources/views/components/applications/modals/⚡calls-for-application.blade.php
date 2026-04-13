<?php

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Rules\CallValidationRules;
use Livewire\Attributes\On; 
use App\Models\Cohort;

new class extends Component
{
    // Form properties
    public $title = '';
    public $cohort = '';
    public $target_applications = 200;
    public $description = '';
    public $details = '';
    public $eligibility = '';
    public $sectors = [];
    public $geography = 'All Districts';
    public $publish_date = '';
    public $open_date = '';
    public $close_date = '';
    public $duration_months = '6';
    public $allow_late_submissions = false;
    public $isEditMode = false;
    public $callId = null;
    
    // Cohort validation
    public $cohortExists = false;
    public $cohortSuggestion = null;
    public $latestCohort = null;
    
    // Validation rules from external class
    protected function rules()
    {
        return CallValidationRules::rules();
    }
    
    // Custom validation messages from external class
    protected function messages()
    {
        return CallValidationRules::messages();
    }

    public function mount()
    {
        $this->loadLatestCohort();
    }

    public function loadLatestCohort()
    {
        $this->latestCohort = Cohort::where('status', 'Active')
            ->orderBy('cohort_number', 'desc')
            ->first();
        
        if ($this->latestCohort && empty($this->cohort)) {
            $this->cohort = (string)$this->latestCohort->cohort_number;
            $this->checkCohortExists();
        }
    }

    #[On('reset-call-form')]
    public function resetFormListener()
    {
        $this->resetForm();
    }

    #[On('edit-call')]
    public function edit($id)
    {
        $call = \App\Models\Call::findOrFail($id);

        $this->callId = $call->id;
        $this->title = $call->title;
        $this->cohort = $call->cohort;
        $this->target_applications = $call->target_applications;
        $this->description = $call->description;
        $this->details = $call->details;
        $this->eligibility = $call->eligibility;
        $this->sectors = $call->sectors ?? [];
        $this->geography = $call->geography;
        
        // Format dates to Y-m-d for date input fields
        $this->publish_date = $call->publish_date ? $call->publish_date->format('Y-m-d') : '';
        $this->open_date = $call->open_date ? $call->open_date->format('Y-m-d') : '';
        $this->close_date = $call->close_date ? $call->close_date->format('Y-m-d') : '';
        
        $this->duration_months = (string)$call->duration_months;
        $this->allow_late_submissions = (bool)$call->allow_late_submissions;
        
        // Check if cohort exists
        $this->checkCohortExists();

        $this->isEditMode = true;

        $this->dispatch('show-create-modal');
    }
    
    // Reset form fields
    public function resetForm()
    {
        $this->callId = null;
        $this->isEditMode = false;

        $this->title = '';
        $this->target_applications = 200;
        $this->description = '';
        $this->details = '';
        $this->eligibility = '';
        $this->sectors = [];
        $this->geography = '';
        $this->publish_date = '';
        $this->open_date = '';
        $this->close_date = '';
        $this->duration_months = '6';
        $this->allow_late_submissions = false;
        $this->cohortExists = false;
        $this->cohortSuggestion = null;
        
        // Reset to latest cohort
        $this->loadLatestCohort();

        $this->resetErrorBag();
    }
    
    // Check if cohort exists
    public function checkCohortExists()
    {
        if (empty($this->cohort)) {
            $this->cohortExists = false;
            $this->cohortSuggestion = null;
            return;
        }
        
        // Check ONLY for Active cohorts
        $cohort = Cohort::where('cohort_number', $this->cohort)
            ->where('status', 'Active')
            ->first();
        
        if ($cohort) {
            $this->cohortExists = true;
            $this->cohortSuggestion = null;
        } else {
            $this->cohortExists = false;
            $this->cohortSuggestion = null;
            
            // Check if cohort exists but is not Active
            $inactiveCohort = Cohort::where('cohort_number', $this->cohort)
                ->where('status', '!=', 'Active')
                ->first();
            
            if ($inactiveCohort) {
                $this->dispatch('notify', type: 'warning', message: "Cohort {$this->cohort} exists but is {$inactiveCohort->status}. Please create a new Active cohort.");
            } else {
                $this->dispatch('notify', type: 'info', message: "Cohort {$this->cohort} not found. Please create a new cohort.");
            }
        }
    }
    
    // Get cohort details for display
    public function getCohortDetailsProperty()
    {
        if (empty($this->cohort)) return null;
        return Cohort::where('cohort_number', $this->cohort)->first();
    }
    
    // Open modal and reset form
    public function openCreateModal()
    {
        $this->resetForm();
        $this->isEditMode = false;
        $this->dispatch('show-create-modal');
    }
    
    // Save call to database
    public function saveCall()
    {
        // Custom validation for cohort
        if (empty($this->cohort)) {
            $this->addError('cohort', 'Cohort reference is required.');
            return;
        }
        
        // Check if cohort exists
        $cohortExists = Cohort::where('cohort_number', $this->cohort)->exists();
        
        if (!$cohortExists) {
            $this->addError('cohort', 'Cohort ' . $this->cohort . ' is not registered. Please register this cohort first.');
            return;
        }
        
        $this->validate();

        DB::beginTransaction();

        try {

            if ($this->isEditMode) {
                // UPDATE
                $call = \App\Models\Call::findOrFail($this->callId);

                $call->update([
                    'title' => $this->title,
                    'cohort' => $this->cohort,
                    'target_applications' => $this->target_applications ?? 200,
                    'description' => $this->description,
                    'details' => $this->details,
                    'eligibility' => $this->eligibility,
                    'sectors' => $this->sectors,
                    'geography' => $this->geography,
                    'publish_date' => $this->publish_date,
                    'open_date' => $this->open_date,
                    'close_date' => $this->close_date,
                    'duration_months' => $this->duration_months,
                    'allow_late_submissions' => $this->allow_late_submissions,
                ]);

                $message = 'Call updated successfully!';
            } else {
                // CREATE
                \App\Models\Call::create([
                    'title' => $this->title,
                    'cohort' => $this->cohort,
                    'target_applications' => $this->target_applications ?? 200,
                    'description' => $this->description,
                    'details' => $this->details,
                    'eligibility' => $this->eligibility,
                    'sectors' => $this->sectors,
                    'geography' => $this->geography,
                    'publish_date' => $this->publish_date,
                    'open_date' => $this->open_date,
                    'close_date' => $this->close_date,
                    'duration_months' => $this->duration_months,
                    'allow_late_submissions' => $this->allow_late_submissions,
                    'status' => 'draft',
                ]);

                $message = 'Call created successfully!';
            }

            DB::commit();

            $this->dispatch('close-modal');
            $this->dispatch('notify', type: 'success', message: $message);
            $this->dispatch('refresh-calls');

            $this->resetForm();

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notify', type: 'error', message: 'Something went wrong: ' . $e->getMessage());
        }
    }
}

?>

<div class="calls-page p-4">

    {{-- ═══════════════════════════════════════
         CREATE MODAL (Bootstrap)
    ═══════════════════════════════════════ --}}
    <div class="modal fade" id="createCallModal" tabindex="-1" aria-labelledby="createCallModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg">
            <div class="modal-content p-3">
                <div class="modal-header">
                    <div>
                        <h5 class="fw-bold mb-0">
                            {{ $isEditMode ? 'Edit Call' : 'Create New Call' }}
                        </h5>
                        <small class="text-muted">Fill in the details to launch a new incubation call</small>
                    </div>
                    <button type="button" class="btn-close" wire:click="resetForm" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                    <!-- Basic Info -->
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3">Basic Information</h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-medium small">Call Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       wire:model="title" placeholder="e.g. LEHSFF Cohort 3 – Incubation Call 2025">
                                @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-medium small">
                                    Cohort Reference <span class="text-danger">*</span>
                                </label>
                                
                                @if($latestCohort)
                                    <div class="mb-2">
                                        <small class="text-muted">
                                            <i class="bi bi-info-circle me-1"></i>
                                            Latest cohort: <strong>Cohort {{ $latestCohort->cohort_number }}</strong> 
                                            ({{ $latestCohort->name }}, {{ $latestCohort->year }})
                                        </small>
                                    </div>
                                @endif
                                
                                <input type="text" 
                                    class="form-control @error('cohort') is-invalid @enderror"
                                    wire:model="cohort"
                                    wire:keyup="checkCohortExists"
                                    placeholder="Enter Cohort Number (e.g., 4)">
                                
                                @error('cohort') 
                                    <div class="invalid-feedback">{{ $message }}</div> 
                                @enderror
                                
                                <!-- Cohort validation feedback -->
                                @if(!empty($cohort) && $cohortExists && $this->cohortDetails)
                                    <div class="mt-2 p-2 bg-success bg-opacity-10 rounded text-white small">
                                        <i class="bi bi-check-circle-fill me-1"></i>
                                        ✅ Cohort {{ $cohort }} is registered: 
                                        <strong>{{ $this->cohortDetails->name }}</strong> 
                                        ({{ $this->cohortDetails->year }})
                                    </div>
                                @elseif(!empty($cohort) && !$cohortExists && !$isEditMode)
                                    <div class="mt-2 p-2 bg-danger bg-opacity-10 rounded text-danger small">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                        ❌ Cohort "{{ $cohort }}" is not registered.
                                        
                                        @if($cohortSuggestion)
                                            <div class="mt-1">
                                                <i class="bi bi-lightbulb me-1"></i>
                                                Did you mean: <strong>Cohort {{ $cohortSuggestion }}</strong>?
                                            </div>
                                        @endif
                                        
                                        <div class="mt-2">
                                            <a href="#" wire:click.prevent="$dispatch('open-create-cohort-modal')" class="text-primary text-decoration-none">
                                                <i class="bi bi-plus-circle me-1"></i>
                                                + Register Cohort {{ $cohort }} now
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label fw-medium small">Short Description <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('description') is-invalid @enderror" rows="2" 
                                          wire:model="description" placeholder="Brief public-facing summary of this call…"></textarea>
                                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-medium small">Full Programme Details / Guidelines</label>
                                <textarea class="form-control" rows="4" wire:model="details" 
                                          placeholder="Detailed overview, objectives, programme structure, and what applicants can expect…"></textarea>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Eligibility -->
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3">Eligibility Criteria</h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-medium small">Eligibility Criteria <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('eligibility') is-invalid @enderror" rows="3" 
                                          wire:model="eligibility" placeholder="e.g. Must be a formally registered business, operating for at least 6 months…"></textarea>
                                @error('eligibility') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-medium small">Target Sectors</label>
                                <select class="form-select" multiple size="5" wire:model="sectors">
                                    <option value="Agriculture">Agriculture</option>
                                    <option value="Technology">Technology</option>
                                    <option value="Manufacturing">Manufacturing</option>
                                    <option value="Retail">Retail &amp; Trade</option>
                                    <option value="Textile">Textile &amp; Garments</option>
                                    <option value="Food">Food &amp; Beverage</option>
                                    <option value="Health">Health &amp; Wellness</option>
                                    <option value="Education">Education</option>
                                    <option value="Finance">Finance &amp; Fintech</option>
                                </select>
                                <small class="text-muted">Hold Ctrl / Cmd to select multiple</small>
                                @error('sectors') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-medium small">Geographic Focus</label>
                                <select class="form-select @error('geography') is-invalid @enderror" wire:model="geography">
                                    <option value="All Districts">All Districts</option>
                                    <option value="Maseru">Maseru</option>
                                    <option value="Leribe">Leribe</option>
                                    <option value="Berea">Berea</option>
                                    <option value="Mafeteng">Mafeteng</option>
                                    <option value="Mohales Hoek">Mohale's Hoek</option>
                                    <option value="Quthing">Quthing</option>
                                    <option value="Qacha">Qacha's Nek</option>
                                    <option value="Mokhotlong">Mokhotlong</option>
                                    <option value="Butha Buthe">Butha-Buthe</option>
                                    <option value="Thaba Tseka">Thaba-Tseka</option>
                                </select>
                                @error('geography') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Dates & Schedule -->
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3">Dates & Schedule</h6>
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-medium small">Publication Date</label>
                                <input type="date" class="form-control @error('publish_date') is-invalid @enderror" 
                                       wire:model="publish_date">
                                @error('publish_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-medium small">Application Window Opens <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('open_date') is-invalid @enderror" 
                                       wire:model="open_date">
                                @error('open_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-medium small">Application Window Closes <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('close_date') is-invalid @enderror" 
                                       wire:model="close_date">
                                @error('close_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-medium small">Incubation Duration (months)</label>
                                <select class="form-select @error('duration_months') is-invalid @enderror" wire:model="duration_months">
                                    <option value="6">6 months</option>
                                    <option value="9">9 months</option>
                                    <option value="12">12 months</option>
                                </select>
                                @error('duration_months') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-medium small">Allow Late Submissions?</label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" value="1" wire:model="allow_late_submissions" id="lateYes">
                                        <label class="form-check-label small" for="lateYes">Yes</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" value="0" wire:model="allow_late_submissions" id="lateNo">
                                        <label class="form-check-label small" for="lateNo">No</label>
                                    </div>
                                </div>
                                @error('allow_late_submissions') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal"
                            wire:click="resetForm">
                        Cancel
                    </button>
                    <button type="button" class="btn btn-primary" wire:click="saveCall" wire:loading.attr="disabled"
                            @if(!empty($cohort) && !$cohortExists) disabled @endif>
                        <span wire:loading.remove>{{ $isEditMode ? 'Update Call' : 'Create Call' }}</span>
                        <span wire:loading>
                            <span class="spinner-border spinner-border-sm me-1"></span> Saving...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    document.addEventListener('livewire:initialized', () => {
        // Listen for show modal event
        Livewire.on('show-create-modal', () => {
            var modal = new bootstrap.Modal(document.getElementById('createCallModal'));
            modal.show();
        });
        
        // Listen for close modal event
        Livewire.on('close-modal', () => {
            var modalElement = document.getElementById('createCallModal');
            var modal = bootstrap.Modal.getInstance(modalElement);
            if (modal) {
                modal.hide();
            }
        });
        
        // Listen for cohort created event to refresh latest cohort
        Livewire.on('cohort-created', () => {
            @this.call('loadLatestCohort');
        });
    });
    </script>
</div>