<?php

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\On;
use Spatie\Permission\Models\Role;

new class extends Component {
    // Change role modal state
    public $showChangeRoleModal = false;
    public $selectedUserId = null;
    public $selectedUserName = null;
    public $selectedRole = null;
    public $currentRole = null;
    
    /**
     * Show change role modal
     */
    #[On('confirmChangeRole')]
    public function confirmChangeRole($userId)
    {
        $this->selectedUserId = $userId;
        $user = User::findOrFail($userId);
        $this->selectedUserName = $user->display_name;
        
        // Get current role
        $this->currentRole = $user->roles->first()?->name ?? 'No Role';
        $this->selectedRole = $this->currentRole !== 'No Role' ? $this->currentRole : null;
        
        $this->showChangeRoleModal = true;
    }
    
    /**
     * Close the change role modal
     */
    public function closeChangeRoleModal()
    {
        $this->reset([
            'selectedUserId', 
            'selectedUserName', 
            'selectedRole', 
            'currentRole', 
            'showChangeRoleModal'
        ]);
        $this->resetErrorBag();
    }
    
    /**
     * Get all available roles
     */
    public function getAvailableRolesProperty()
    {
        return Role::all();
    }
    
    /**
     * Change user role
     */
    public function changeRole()
    {
        $this->validate([
            'selectedRole' => 'required|string|exists:roles,name',
        ]);
        
        try {
            $user = User::findOrFail($this->selectedUserId);
            
            // Remove current role(s)
            $user->syncRoles([]);
            
            // Assign new role
            $user->assignRole($this->selectedRole);
            
            $this->closeChangeRoleModal();
            
            // Dispatch events
            $this->dispatch('roleChanged');
            $this->dispatch('userUpdated');
            $this->dispatch('notify', type: 'success', message: "User role changed to {$this->selectedRole} successfully!");
            
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Failed to change role: ' . $e->getMessage());
        }
    }
};

?>

<div>
    <!-- Change Role Modal -->
    @if($showChangeRoleModal)
    <div class="modal fade show d-block" 
         tabindex="-1" 
         aria-hidden="true"
         style="background: rgba(0,0,0,0.5);"
         wire:key="change-role-modal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-shield-fill text-primary me-2"></i>
                        Change User Role
                    </h5>
                    <button type="button" class="btn-close" 
                            wire:click="closeChangeRoleModal" 
                            aria-label="Close"></button>
                </div>
                
                <form wire:submit.prevent="changeRole">
                    <div class="modal-body p-4">
                        <!-- User info -->
                        <div class="alert alert-info d-flex align-items-center mb-4" role="alert">
                            <i class="bi bi-person-circle me-2 fs-4"></i>
                            <div>
                                <strong>{{ $selectedUserName }}</strong><br>
                                <small>Current role: <span class="badge bg-primary">{{ $currentRole }}</span></small>
                            </div>
                        </div>

                        <!-- Warning message -->
                        <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <div>
                                Changing a user's role will affect their permissions and access levels immediately.
                            </div>
                        </div>

                        <!-- Role selection -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-uppercase text-muted">
                                <i class="bi bi-shield-lock-fill me-1"></i>Select New Role <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('selectedRole') is-invalid @enderror" 
                                    wire:model.live="selectedRole"
                                    required>
                                <option value="">Choose a role...</option>
                                @foreach($this->availableRoles as $role)
                                    <option value="{{ $role->name }}" 
                                            {{ $currentRole === $role->name ? 'disabled' : '' }}>
                                        {{ $role->name }}
                                        @if($currentRole === $role->name)
                                            (Current Role)
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('selectedRole')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text text-muted small">
                                Current role is disabled. Select a different role to change.
                            </div>
                        </div>

                        <!-- Role description (optional) -->
                        @if($selectedRole && $selectedRole !== $currentRole)
                        <div class="alert alert-success d-flex align-items-center mt-3" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            <div>
                                You are about to change <strong>{{ $selectedUserName }}'s</strong> role from 
                                <strong>{{ $currentRole }}</strong> to <strong>{{ $selectedRole }}</strong>.
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-outline-secondary" 
                                wire:click="closeChangeRoleModal">
                            <i class="bi bi-x-lg me-1"></i>
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-primary" 
                                wire:loading.attr="disabled"
                                wire:target="changeRole"
                                @if(!$selectedRole || $selectedRole === $currentRole) disabled @endif>
                            <span wire:loading.remove wire:target="changeRole">
                                <i class="bi bi-shield-fill me-1"></i>
                                Change Role
                            </span>
                            <span wire:loading wire:target="changeRole">
                                <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                Changing...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>