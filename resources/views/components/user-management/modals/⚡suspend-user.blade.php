<?php

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\On;

new class extends Component {
    // User management state
    public $showSuspendModal = false;
    public $selectedUserId = null;
    
    // Suspension form data
    public $suspensionReason = '';
    public $suspensionUntil = null;

    /**
     * Show suspend confirmation modal
     */
    #[On('confirmSuspend')]
    public function confirmSuspend($userId)
    {
        $this->selectedUserId = $userId;
        $this->showSuspendModal = true;
    }

    /**
     * Close the suspend modal
     */
    public function closeSuspendModal()
    {
        $this->reset(['selectedUserId', 'suspensionReason', 'suspensionUntil', 'showSuspendModal']);
        $this->resetErrorBag();
    }

    /**
     * Suspend a user
     */
    public function suspendUser()
    {
        $this->validate([
            'suspensionReason' => 'required|string|max:255',
            'suspensionUntil' => 'nullable|date|after:today',
        ]);

        try {
            $user = User::findOrFail($this->selectedUserId);
            $user->suspend($this->suspensionReason, $this->suspensionUntil);

            $this->closeSuspendModal();
            
            // Dispatch events
            $this->dispatch('userSuspended');
            $this->dispatch('userCreated');
            $this->dispatch('notify', type: 'success', message: 'User suspended successfully!');
            
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Failed to suspend user: ' . $e->getMessage());
        }
    }

    /**
     * Unsuspend a user
     */
    #[On('unsuspendUser')]
    public function unsuspendUser($userId)
    {
        try {
            $user = User::findOrFail($userId);
            $user->unsuspend();

            $this->dispatch('userUnsuspended');
            $this->dispatch('userCreated');
            $this->dispatch('notify', type: 'success', message: 'User reinstated successfully!');
            
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Failed to reinstate user: ' . $e->getMessage());
        }
    }
};
?>

<div>
    <!-- Suspend Modal -->
    @if($showSuspendModal)
    <div class="modal fade show d-block" 
         tabindex="-1" 
         aria-hidden="true"
         style="background: rgba(0,0,0,0.5);"
         wire:key="suspend-modal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-slash-circle-fill text-danger me-2"></i>
                        Suspend User
                    </h5>
                    <button type="button" class="btn-close" 
                            wire:click="closeSuspendModal" 
                            aria-label="Close"></button>
                </div>
                
                <form wire:submit.prevent="suspendUser">
                    <div class="modal-body p-4">
                        <!-- Warning message -->
                        <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <div>
                                This user will lose access to the system while suspended.
                            </div>
                        </div>

                        <!-- Reason field -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-uppercase text-muted">
                                <i class="bi bi-chat-text me-1"></i>Reason for suspension <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control @error('suspensionReason') is-invalid @enderror" 
                                      wire:model="suspensionReason" 
                                      rows="3" 
                                      placeholder="Explain why this user is being suspended..."
                                      required></textarea>
                            @error('suspensionReason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Suspension until field -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-uppercase text-muted">
                                <i class="bi bi-calendar-event me-1"></i>Suspended until (optional)
                            </label>
                            <input type="datetime-local" 
                                   class="form-control @error('suspensionUntil') is-invalid @enderror" 
                                   wire:model="suspensionUntil"
                                   min="{{ now()->addDay()->format('Y-m-d\TH:i') }}">
                            @error('suspensionUntil')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text text-muted small">
                                Leave empty for permanent suspension
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-outline-secondary" 
                                wire:click="closeSuspendModal">
                            <i class="bi bi-x-lg me-1"></i>
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-danger" 
                                wire:loading.attr="disabled"
                                wire:target="suspendUser">
                            <span wire:loading.remove wire:target="suspendUser">
                                <i class="bi bi-slash-circle-fill me-1"></i>
                                Suspend User
                            </span>
                            <span wire:loading wire:target="suspendUser">
                                <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                Suspending...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>