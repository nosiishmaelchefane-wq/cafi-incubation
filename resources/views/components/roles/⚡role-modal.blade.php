<?php

use Livewire\Component;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Rules\RoleValidation;

new class extends Component
{
    public $show = false;
    public $roleId = null;
    public $name = '';
    public $description = '';
    public $color = 'primary';
    public $baseTemplate = '';


    public $colors = ['primary', 'success', 'danger', 'warning', 'info', 'secondary'];
    public $existingRoles = [];


    public function mount()
    {
        $this->existingRoles = Role::pluck('name')->toArray();
    }

    public function open()
    {
        $this->reset();
        $this->show = true;
    }

    public function close()
    {
        $this->show = false;
        $this->resetValidation();
    }


    public function save()
    {
        $this->validate([
            'name' => ['required', new RoleValidation],
            'description' => ['nullable', 'string'],
            'color' => ['required', 'in:primary,success,danger,warning,info,secondary'],
        ]);

        $role = Role::create([
            'name' => $this->name,
            'guard_name' => 'web',
            'description' => $this->description, 
        ]);

        if ($this->baseTemplate) {
            $templateRole = Role::where('name', $this->baseTemplate)->first();
            if ($templateRole) {
                $role->syncPermissions($templateRole->permissions);
            }
        }

       $this->dispatch('notify', type:'success', message:'Role created successfully!');
        $this->dispatch('delayedRoleCreated');
        $this->close();

    }
};
?>

<div>
      
    {{-- resources/views/livewire/roles/role-modal.blade.php --}}
   <div class="modal fade" id="roleModal" tabindex="-1" aria-labelledby="roleModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="roleModalLabel">
                        <i class="bi bi-shield-plus text-primary me-2"></i>{{ $roleId ? 'Edit Role' : 'Add New Role' }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <div class="mb-3">
                        <label class="form-label fw-medium small">Role Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               wire:model="name" placeholder="e.g. Evaluation Officer">
                        @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-medium small">Description</label>
                        <textarea class="form-control" wire:model="description" 
                                  rows="2" placeholder="Brief description of this role's responsibilities"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-medium small">Role Colour</label>
                        <div class="d-flex gap-2 flex-wrap">
                            @foreach($colors as $colorOption)
                                <input type="radio" class="btn-check" 
                                       wire:model="color" 
                                       name="roleColor" 
                                       id="color_{{ $colorOption }}" 
                                       value="{{ $colorOption }}">
                                <label class="btn btn-sm btn-{{ $colorOption }} rounded-circle color-pick" 
                                       for="color_{{ $colorOption }}" 
                                       style="width:32px;height:32px; {{ $color == $colorOption ? 'outline: 3px solid #0d6efd; outline-offset: 2px;' : '' }}">
                                </label>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="mb-1">
                        <label class="form-label fw-medium small">Base Permission Template</label>
                        <select class="form-select" wire:model="baseTemplate">
                            <option value="">— No template, configure manually —</option>
                            @foreach($existingRoles as $roleName)
                                <option value="{{ $roleName }}">Copy from: {{ $roleName }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 px-4 pb-4">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary px-4" wire:click="save" wire:loading.attr="disabled">
                        <span wire:loading.remove>
                            <i class="bi bi-{{ $roleId ? 'pencil' : 'plus-circle' }} me-1"></i>
                            {{ $roleId ? 'Update Role' : 'Create Role' }}
                        </span>
                        <span wire:loading>
                            <span class="spinner-border spinner-border-sm me-1"></span>
                            {{ $roleId ? 'Updating...' : 'Creating...' }}
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    
</div>