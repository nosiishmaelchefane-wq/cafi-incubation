<?php

use Livewire\Component;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Livewire\Attributes\On; 

new class extends Component
{
    public ?int $selectedRoleId = null;
    public $role = null;
    public $permissions = [];
    public $modules = [];
    public $permissionStates = [];

    protected $listeners = ['roleSelected' => 'loadPermissions'];

    public function mount()
    {
        $this->loadModulesAndPermissions();
    }

       public function loadModulesAndPermissions()
    {
        $this->modules = [
            'Dashboard' => ['view'],
            'Calls for Applications' => ['view', 'create', 'edit', 'delete', 'approve'],
            'Applications' => ['view', 'create', 'edit', 'delete', 'approve'],
            'Screening & Eligibility' => ['view', 'create', 'edit', 'delete', 'approve'],
            'Evaluation & Scoring' => ['view', 'create', 'edit', 'delete', 'approve'],
            'Cohort Management' => ['view', 'create', 'edit', 'delete', 'approve'],
            'Enterprise Reports' => ['view', 'create', 'edit', 'delete', 'approve'],
            'ESO Reports' => ['view', 'create', 'edit', 'delete', 'approve'],
            'User Management' => ['view', 'create', 'edit', 'delete', 'approve'],
            'Knowledge Hub' => ['view', 'create', 'edit', 'delete'],
            'Analytics & Reporting' => ['view'],
        ];

        // Initialize all permissions
        foreach ($this->modules as $module => $actions) {
            foreach ($actions as $action) {
                $permName = $action . ' ' . $module;
                $this->permissionStates[$permName] = false;
            }
        }
    }



    public function loadPermissions($roleId)
    {
        $this->selectedRoleId = $roleId;
        $this->role = Role::with('permissions')->find($roleId);

        // Reset states
        $this->loadModulesAndPermissions();

        // Set states based on role's permissions
        if ($this->role) {
            foreach ($this->role->permissions as $permission) {
                if (isset($this->permissionStates[$permission->name])) {
                    $this->permissionStates[$permission->name] = true;
                }
            }
        }
    }

   public function grantAll()
    {
        if (!$this->selectedRoleId) {
            $this->dispatch('notify', type: 'warning', message: 'Please select a role first.');
            return;
        }

        $role = Role::findOrFail($this->selectedRoleId);
        $role->syncPermissions(Permission::all());

        $this->loadPermissions($this->selectedRoleId); // ← pass the id
        $this->dispatch('notify', type: 'success', message: "All permissions granted to \"{$role->name}\".");
    }

    public function revokeAll()
    {
        if (!$this->selectedRoleId) {
            $this->dispatch('notify', type: 'warning', message: 'Please select a role first.');
            return;
        }

        $role = Role::findOrFail($this->selectedRoleId);

        if ($role->name === 'Super Administrator') {
            $this->dispatch('notify', type: 'danger', message: 'Cannot revoke permissions from Super Administrator.');
            return;
        }

        $role->syncPermissions([]);

        $this->loadPermissions($this->selectedRoleId); // ← pass the id
        $this->dispatch('notify', type: 'success', message: "All permissions revoked from \"{$role->name}\".");
    }

    public function savePermissions()
    {
        if (!$this->role) {
            $this->dispatch('notify', type: 'error', message: 'No role selected' );
            return;
        }

        // Get all permission names that are true
        $selectedPermissions = collect($this->permissionStates)
            ->filter(fn($state) => $state)
            ->keys()
            ->toArray();

        // Sync permissions
        $permissions = Permission::whereIn('name', $selectedPermissions)->get();
        $this->role->syncPermissions($permissions);

        $this->dispatch('roleUpdated');
        $this->dispatch('notify', type: 'success',
            message:'Permissions saved successfully'
        );
    }
};
?>

<div>
   {{-- resources/views/livewire/roles/permission-matrix.blade.php --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3 px-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h6 class="fw-bold mb-0" id="permTitle">
                        {{ $role ? $role->name : 'Select a Role' }} — Permissions
                    </h6>
                    @if($role)
                        <small class="text-muted" id="permSubtitle">
                            {{ $role->permissions->count() }} of {{ count($permissionStates) }} permissions granted
                        </small>
                    @endif
                </div>
                @if($role)
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-secondary" wire:click="revokeAll">
                            <i class="bi bi-x-circle me-1"></i>Revoke All
                        </button>
                        <button class="btn btn-sm btn-outline-primary" wire:click="grantAll">
                            <i class="bi bi-check-circle me-1"></i>Grant All
                        </button>
                        <button class="btn btn-sm btn-primary" wire:click="savePermissions" wire:loading.attr="disabled">
                            <span wire:loading.remove><i class="bi bi-floppy-fill me-1"></i>Save</span>
                            <span wire:loading><span class="spinner-border spinner-border-sm me-1"></span>Saving...</span>
                        </button>
                    </div>
                @endif
            </div>
        </div>
        
        @if($role)
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 perm-table">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4 py-3" style="width:35%">Module</th>
                                <th class="text-center py-3">View</th>
                                <th class="text-center py-3">Create</th>
                                <th class="text-center py-3">Edit</th>
                                <th class="text-center py-3">Delete</th>
                                <th class="text-center py-3">Approve</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($modules as $module => $actions)
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="d-flex align-items-center gap-2">
                                            @php
                                                $icons = [
                                                    'Dashboard' => 'speedometer2',
                                                    'Calls for Applications' => 'megaphone-fill',
                                                    'Applications' => 'file-earmark-text-fill',
                                                    'Screening & Eligibility' => 'funnel-fill',
                                                    'Evaluation & Scoring' => 'clipboard2-data-fill',
                                                    'Cohort Management' => 'people-fill',
                                                    'Enterprise Reports' => 'bar-chart-fill',
                                                    'ESO Reports' => 'building',
                                                    'User Management' => 'person-lines-fill',
                                                    'Knowledge Hub' => 'book-fill',
                                                    'Analytics & Reporting' => 'graph-up-arrow',
                                                ];
                                                $icon = $icons[$module] ?? 'gear-fill';
                                            @endphp
                                            <i class="bi bi-{{ $icon }} text-primary"></i>
                                            <span class="fw-medium small">{{ $module }}</span>
                                        </div>
                                    </td>
                                    
                                    @foreach($actions as $action)
                                        @php
                                            $permName = $action . ' ' . $module;
                                            $isChecked = $permissionStates[$permName] ?? false;
                                        @endphp
                                        <td class="text-center">
                                            <input class="form-check-input perm-check" 
                                                type="checkbox" 
                                                wire:model.live="permissionStates.{{ $permName }}"
                                                {{ in_array($action, ['view', 'create', 'edit', 'delete', 'approve']) ? '' : 'disabled' }}>
                                        </td>
                                    @endforeach
                                    
                                    {{-- Fill empty cells if module has fewer actions --}}
                                    @for($i = count($actions); $i < 5; $i++)
                                        <td class="text-center">
                                            <input class="form-check-input perm-check" type="checkbox" disabled>
                                        </td>
                                    @endfor
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="card-body p-5 text-center text-muted">
                <i class="bi bi-arrow-left-circle fs-1 d-block mb-3"></i>
                <p>Select a role from the left to manage its permissions</p>
            </div>
        @endif
    </div>
 
</div>