<?php

use Livewire\Component;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

new class extends Component
{
    public $roles = [];
    public $selectedRole = null;
    public $selectedRoleId = null;
    public $totalUsers = 0;
    public $totalPermissions = 0;
    public $suspendedUsers = 0;

    protected $listeners = ['roleUpdated' => 'loadRoles', 'refreshPermissions' => '$refresh'];

    public function mount()
    {
        $this->loadRoles();
        $this->loadStatistics();
    }

    public function loadRoles()
    {
        $this->roles = Role::with('permissions')->get();
    }

      public function loadStatistics()
    {
        $this->totalUsers = User::count();
        $this->totalPermissions = Permission::count();
        $this->suspendedUsers = User::where('is_suspended', true)->count(); // You'll need to add this field
    }

     public function selectRole($roleId)
    {
        $this->selectedRoleId = $roleId;
        $this->selectedRole = Role::with('permissions')->find($roleId);
        $this->dispatch('roleSelected', roleId: $roleId)->to('roles.permission-matrix');
    }

     public function deleteRole($roleId)
    {
        $role = Role::find($roleId);
        
        // Don't allow deletion of system roles or roles with users
        if ($role->name === 'Super Administrator' || $role->users->count() > 0) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Cannot delete this role'
            ]);
            return;
        }

        $role->delete();
        $this->loadRoles();
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Role deleted successfully'
        ]);
    }


};
?>

<div>
   {{-- resources/views/livewire/roles/role-manager.blade.php --}}
<div class="container roles-page p-4">
    {{-- Page Header --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="bi bi-shield-lock-fill text-primary me-2"></i>Roles &amp; Permissions
            </h4>
            <p class="text-muted mb-0 small">Manage system roles and control what each role can access.</p>
        </div>
        <button 
                class="btn btn-primary d-flex align-items-center gap-2 px-4"
                data-bs-toggle="modal"
                data-bs-target="#roleModal"
            >
                <i class="bi bi-plus-circle-fill"></i>
                <span>Add New Role</span>
        </button>
    </div>

    {{-- Summary Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100 stat-card">
                <div class="card-body d-flex align-items-center gap-3 p-3">
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary rounded-3 p-3">
                        <i class="bi bi-shield-fill fs-4"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-4 lh-1">{{ count($roles) }}</div>
                        <small class="text-muted">Total Roles</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100 stat-card">
                <div class="card-body d-flex align-items-center gap-3 p-3">
                    <div class="stat-icon bg-opacity-10 text-success rounded-3 p-3">
                        <i class="bi bi-people-fill fs-4"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-4 lh-1">{{ $totalUsers }}</div>
                        <small class="text-muted">Total Users</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100 stat-card">
                <div class="card-body d-flex align-items-center gap-3 p-3">
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning rounded-3 p-3">
                        <i class="bi bi-key-fill fs-4"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-4 lh-1">{{ $totalPermissions }}</div>
                        <small class="text-muted">Permissions</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100 stat-card">
                <div class="card-body d-flex align-items-center gap-3 p-3">
                    <div class="stat-icon bg-danger bg-opacity-10 text-danger rounded-3 p-3">
                        <i class="bi bi-person-x-fill fs-4"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-4 lh-1">{{ $suspendedUsers }}</div>
                        <small class="text-muted">Suspended</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Roles List --}}
        <div class="col-12 col-xl-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-3 px-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <h6 class="fw-bold mb-0">System Roles</h6>
                        <span class="badge bg-primary rounded-pill">{{ count($roles) }} Roles</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush" id="roleList">
                        @foreach($roles as $role)
                            @php
                                $colors = ['danger', 'primary', 'success', 'info', 'warning', 'secondary'];
                                $color = $colors[$loop->index % count($colors)];
                                $initials = collect(explode(' ', $role->name))->map(fn($word) => strtoupper(substr($word, 0, 1)))->join('');
                                $userCount = $role->users->count();
                            @endphp
                            
                            <a href="#" class="list-group-item list-group-item-action role-item {{ $selectedRoleId == $role->id ? 'active-role' : '' }} px-4 py-3" 
                               wire:click.prevent="selectRole({{ $role->id }})">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="role-avatar bg-{{ $color }} text-white rounded-circle d-flex align-items-center justify-content-center" 
                                             style="width:38px;height:38px;font-size:0.85rem;font-weight:700;flex-shrink:0;">
                                            {{ $initials }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold text-dark small">{{ $role->name }}</div>
                                            <div class="text-muted" style="font-size:0.75rem;">
                                                {{ $role->description ?? 'No description' }}
                                            </div>
                                        </div>
                                    </div>
                                    <span class="badge bg-{{ $color }} bg-opacity-10 text-{{ $color }}">
                                        {{ $userCount }} {{ Str::plural('user', $userCount) }}
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Permissions Matrix --}}
        <div class="col-12 col-xl-8">
            <livewire:roles.permission-matrix />
        </div>
    </div>

    {{-- Role Modal --}}
    <livewire:roles.role-modal />
</div>
</div>