<?php

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Call;
use App\Models\EvaluationWindow;
use Carbon\Carbon;

new class extends Component
{
    public $showModal = false;
    public $callId;
    public $call;
    public $evaluationWindow;
    public $isSaving = false;
    
    // Form fields
    public $open_date;
    public $close_date;
    public $notes;
    
    protected $rules = [
        'open_date' => 'required|date|after_or_equal:today',
        'close_date' => 'required|date|after:open_date',
        'notes' => 'nullable|string|max:500',
    ];
    
    protected $messages = [
        'open_date.required' => 'Please select an evaluation open date.',
        'open_date.after_or_equal' => 'Evaluation open date must be today or a future date.',
        'close_date.required' => 'Please select an evaluation close date.',
        'close_date.after' => 'Evaluation close date must be after the open date.',
        'notes.max' => 'Notes cannot exceed 500 characters.',
    ];
    
    #[On('refresh-evaluation-window')]
    public function refreshData()
    {
        $this->loadCallData();
    }
    
    #[On('open-evaluation-window-modal')]
    public function openModal($callId = null)
    {
        // Receive the callId from the dispatched event
        if ($callId) {
            $this->callId = $callId;
        }
        
        $this->resetValidation();
        $this->isSaving = false;
        $this->loadCallData(); // Refresh data before opening
        $this->showModal = true;
    }
    
    public function loadCallData()
    {
        if ($this->callId) {
            $this->call = Call::find($this->callId);
            if ($this->call) {
                // Get the latest evaluation window if exists
                $this->evaluationWindow = $this->call->latestEvaluationWindow;
                if ($this->evaluationWindow) {
                    $this->open_date = $this->evaluationWindow->open_date->format('Y-m-d');
                    $this->close_date = $this->evaluationWindow->close_date->format('Y-m-d');
                    $this->notes = $this->evaluationWindow->notes;
                } else {
                    // Reset form fields if no existing window
                    $this->open_date = null;
                    $this->close_date = null;
                    $this->notes = null;
                }
            } else {
                $this->dispatch('notify', type: 'error', message: 'Call not found.');
            }
        } else {
            $this->dispatch('notify', type: 'error', message: 'No call ID provided.');
        }
    }
    
    public function closeModal()
    {
        $this->showModal = false;
        $this->isSaving = false;
        $this->reset(['open_date', 'close_date', 'notes']);
    }
    
    public function saveEvaluationWindow()
    {
        if ($this->isSaving) {
            return;
        }
        
        $this->isSaving = true;
        
        $this->validate();
        
        try {
            // Additional validation for dates
            $openDate = Carbon::parse($this->open_date);
            $closeDate = Carbon::parse($this->close_date);
            $now = Carbon::now();
            
            // Check if close date is in the past
            if ($closeDate->lt($now)) {
                $this->dispatch('notify', type: 'error', message: 'Evaluation close date cannot be in the past.');
                $this->isSaving = false;
                return;
            }
            
            // Check if open date is in the past (but not today)
            if ($openDate->lt($now->startOfDay())) {
                $this->dispatch('notify', type: 'error', message: 'Evaluation open date cannot be in the past.');
                $this->isSaving = false;
                return;
            }
            
            // Create or update evaluation window
            $evaluationWindow = $this->evaluationWindow ?? new EvaluationWindow();
            $evaluationWindow->call_id = $this->call->id;
            $evaluationWindow->open_date = $openDate;
            $evaluationWindow->close_date = $closeDate;
            $evaluationWindow->notes = $this->notes;
            
            // Set status based on dates
            if ($closeDate < $now) {
                $evaluationWindow->status = 'expired';
                $this->dispatch('notify', type: 'warning', message: 'Evaluation window is already expired.');
            } elseif ($openDate <= $now && $closeDate >= $now) {
                $evaluationWindow->status = 'active';
                $this->dispatch('notify', type: 'success', message: 'Evaluation window is now active!');
            } else {
                $evaluationWindow->status = 'draft';
                $this->dispatch('notify', type: 'info', message: 'Evaluation window scheduled successfully.');
            }
            
            $evaluationWindow->save();
            
            // Update call status based on evaluation window
            if ($evaluationWindow->status === 'active') {
                $this->call->status = 'open';
            } elseif ($evaluationWindow->status === 'draft' && $this->call->status === 'open') {
                $this->call->status = 'published';
            }
            
            $this->call->save();
            
            // Dispatch events to notify other components
            $this->dispatch('evaluation-window-saved', evaluationWindowId: $evaluationWindow->id);
            $this->dispatch('refresh-evaluation-display'); 
            
            $this->closeModal();
            
            // Success notification
            $this->dispatch('notify', type: 'success', message: 'Evaluation window dates have been set successfully!');
            
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Error saving evaluation window: ' . $e->getMessage());
        } finally {
            $this->isSaving = false;
        }
    }
    
    public function updatedOpenDate()
    {
        // Validate open date when changed
        if ($this->open_date && Carbon::parse($this->open_date)->lt(Carbon::now()->startOfDay())) {
            $this->dispatch('notify', type: 'error', message: 'Evaluation open date cannot be in the past.');
            $this->open_date = null;
        }
    }
    
    public function updatedCloseDate()
    {
        // Validate close date when changed
        if ($this->close_date && Carbon::parse($this->close_date)->lt(Carbon::now())) {
            $this->dispatch('notify', type: 'error', message: 'Evaluation close date cannot be in the past.');
            $this->close_date = null;
        }
        
        // Check if close date is after open date
        if ($this->open_date && $this->close_date && Carbon::parse($this->close_date)->lte(Carbon::parse($this->open_date))) {
            $this->dispatch('notify', type: 'error', message: 'Evaluation close date must be after the open date.');
            $this->close_date = null;
        }
    }
    
};
?>

<div>
    {{-- Display current evaluation window info if exists --}}
    @if($evaluationWindow && $evaluationWindow->exists)
        <div class="mt-2 small text-muted">
            <i class="bi bi-calendar-range"></i> 
            <strong>Evaluation Window:</strong> 
            {{ $evaluationWindow->open_date->format('M d, Y') }} - {{ $evaluationWindow->close_date->format('M d, Y') }}
            @if($evaluationWindow->status === 'active')
                <span class="badge bg-success">Active</span>
            @elseif($evaluationWindow->status === 'expired')
                <span class="badge bg-secondary">Expired</span>
            @else
                <span class="badge bg-warning">Upcoming</span>
            @endif
            @if(isset($evaluationWindow->locked_at) && $evaluationWindow->locked_at)
                <span class="badge bg-danger">Locked</span>
            @endif
        </div>
    @endif

    {{-- Modal --}}
    @if($showModal)
        <div class="modal fade show d-block" tabindex="-1" role="dialog" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title">
                            <i class="bi bi-calendar-range"></i> Set Evaluation Window Dates
                            @if($call)
                                <small class="text-muted"> - {{ $call->title }}</small>
                            @endif
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModal" aria-label="Close"></button>
                    </div>
                    
                    <form wire:submit.prevent="saveEvaluationWindow">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="open_date" class="form-label">
                                    <i class="bi bi-unlock"></i> Evaluation Open Date <span class="text-danger">*</span>
                                </label>
                                <input 
                                    type="date" 
                                    id="open_date"
                                    wire:model.live="open_date"
                                    class="form-control @error('open_date') is-invalid @enderror"
                                    min="{{ date('Y-m-d') }}">
                                @error('open_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Evaluation period will start on this date.
                                </small>
                            </div>
                            
                            <div class="mb-3">
                                <label for="close_date" class="form-label">
                                    <i class="bi bi-lock"></i> Evaluation Close Date <span class="text-danger">*</span>
                                </label>
                                <input 
                                    type="date" 
                                    id="close_date"
                                    wire:model.live="close_date"
                                    class="form-control @error('close_date') is-invalid @enderror"
                                    min="{{ $open_date ?? date('Y-m-d', strtotime('+1 day')) }}">
                                @error('close_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Evaluation period will end on this date.
                                </small>
                            </div>
                            
                            <div class="mb-3">
                                <label for="notes" class="form-label">
                                    <i class="bi bi-pencil"></i> Notes (Optional)
                                </label>
                                <textarea 
                                    id="notes"
                                    wire:model="notes"
                                    class="form-control @error('notes') is-invalid @enderror"
                                    rows="3"
                                    placeholder="Add any notes about this evaluation window..."></textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i>
                                <strong>Evaluation Window Information:</strong>
                                <ul class="mb-0 mt-2">
                                    <li>Evaluators can only assess applications during the evaluation window.</li>
                                    <li>The call will automatically open for evaluation on the selected open date.</li>
                                    <li>The evaluation period will automatically close on the selected close date.</li>
                                    <li>You can create multiple evaluation windows for different evaluation cycles.</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary px-4" wire:click="closeModal">
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-warning px-4" wire:loading.attr="disabled">
                                <span wire:loading.remove>
                                    <i class="bi bi-save me-1"></i> Save Window
                                </span>
                                <span wire:loading>
                                    <span class="spinner-border spinner-border-sm me-1"></span>
                                    Saving...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>