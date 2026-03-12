<?php

use Spatie\Permission\Models\Role;
use Livewire\Component;
use Livewire\Attributes\On; 

new class extends Component
{
    public $editName = '';
    public $editDescription = '';
    public $selectedRoleId = null;
    
    protected $rules = [
        'editName' => 'required|min:3|max:255',
        'editDescription' => 'nullable|max:500',
    ];

    
    #[On('openEditRole')] 
    public function openEditRole($roleId)
    {
        $role = Role::findOrFail($roleId);
        $this->editName= $role->name;
        $this->editDescription = $role->description ?? '';
        $this->selectedRoleId = $roleId;
    }

    
    public function editRole($roleId)
    {
        $this->selectedRoleId = $roleId;
        $role = Role::find($roleId);
        
        if ($role) {
            $this->editName = $role->name;
            $this->editDescription = $role->description;
        }
    }
    
    public function updateRole()
    {
        $this->validate();
        $role = Role::find($this->selectedRoleId);
        
        if ($role) {
            $role->update([
                'name' => $this->editName,
                'description' => $this->editDescription,
            ]);
            
           // $this->dispatch('notify', type:'success', message:'Role updated successfully!');
            $this->dispatch('delayedRoleCreated');
      
            // Close modal and reset form
            $this->reset(['editName', 'editDescription', 'selectedRoleId']);
        }
    }
    
    public function resetEditForm()
    {
        $this->reset(['editName', 'editDescription', 'selectedRoleId']);
        $this->resetValidation();
    }
};
?>

<div>
    <div class="modal fade" id="editRoleModal" tabindex="-1" aria-labelledby="editRoleModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-semibold" id="editRoleModalLabel">
                        <i class="bi bi-shield-lock me-2 text-primary"></i>Edit Role
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Role Name</label>
                        <input
                            type="text"
                            class="form-control @error('editName') is-invalid @enderror"
                            wire:model="editName"
                            placeholder="Enter role name">
                        @error('editName')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Description</label>
                        <textarea
                            class="form-control @error('editDescription') is-invalid @enderror"
                            wire:model="editDescription"
                            rows="3"
                            placeholder="Enter role description"></textarea>
                        @error('editDescription')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 px-4">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button
                        type="button"
                        class="btn btn-primary px-4"
                        wire:click="updateRole"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="updateRole">
                            <i class="bi bi-check-lg me-1"></i>Save Changes
                        </span>
                        <span wire:loading wire:target="updateRole">
                            <span class="spinner-border spinner-border-sm me-1"></span>Saving...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>