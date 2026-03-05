<?php

use Livewire\Component;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Livewire\Attributes\On; 
use App\Models\User;

new class extends Component
{
    public $roles = [];
    public $selectedRole = null;
    public ?int $selectedRoleId = null;
    public $totalUsers = 0;
    public $totalPermissions = 0;
    public $suspendedUsers = 0;
    public $searchRole = '';

    protected $listeners = ['roleUpdated' => 'loadRoles', 'refreshPermissions' => '$refresh'];

    public function mount()
    {
        $this->loadRoles();
        $this->loadStatistics();
    }

      #[On('roleCreated')] 
    public function roleCreated()
    {
        $this->loadRoles();
    }

    public function loadRoles()
    {
        $query = Role::with('permissions');

        if (!empty($this->searchRole)) {
            $query->where('name', 'like', '%' . $this->searchRole . '%');
        }

        $this->roles = $query->get();

        if ($this->selectedRoleId && !$this->roles->contains('id', $this->selectedRoleId)) {
            $this->selectedRoleId = null;
            $this->dispatch('roleDeselected')->to('roles.permission-matrix');
        }
    }

    public function loadStatistics()
    {
        $this->totalUsers = User::count();
        $this->totalPermissions = Permission::count();
        $this->suspendedUsers = User::where('is_suspended', true)->count();
    }

    public function editRole($roleId)
    {
        if ($roleId && Role::find($roleId)) {
            $this->dispatch('openEditRole', roleId: $roleId)->to('roles.manage-role');
        } else {
            $this->dispatch('notify', type: 'warning', message: 'Please select a valid role first');
        }
    }

    public function selectRole($roleId)
    {
        $role = Role::with('permissions')->find($roleId);

        if (!$role) {
            $this->clearSelectedRole();
            return;
        }

        $this->selectedRoleId = $roleId;
        $this->selectedRole = $role;
        $this->dispatch('roleSelected', roleId: $roleId)->to('roles.permission-matrix');
    }

    public function deleteRole($roleId)
    {
        $role = Role::find($roleId);

        if (!$role) {
            $this->dispatch('notify', type: 'danger', message: 'Role not found');
            return;
        }

        if ($role->name === 'Super Administrator' || $role->users->count() > 0) {
            $this->dispatch('notify', type: 'danger', message: 'Cannot delete this role');
            return;
        }

        if ($this->selectedRoleId === $roleId) {
            $this->selectedRoleId = null;
            $this->dispatch('roleDeselected')->to('roles.permission-matrix');
        }

        $role->delete();
        $this->loadRoles();
        $this->redirect(request()->header('Referer'), navigate: true);

    }

    
    public function clearSelectedRole()
    {
        $this->selectedRoleId = null;
        $this->selectedRole = null;
        
        // Also dispatch an event to clear the permission matrix
        $this->dispatch('roleSelected', roleId: null)->to('roles.permission-matrix');
    }

    public function updatedSearchRole()
    {
       
        $this->loadRoles();
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
                        <div class="d-flex flex-column gap-2">
                            {{-- Top Row: Title + Badge --}}
                            <div class="d-flex align-items-center gap-2">
                                <h6 class="fw-bold mb-0">System Roles</h6>
                                <span class="badge bg-primary rounded-pill">{{ count($roles) }} Roles</span>
                            </div>

                            {{-- Bottom Row: Search + Action Buttons --}}
                            <div class="d-flex align-items-center gap-2 mt-2">
                                {{-- Search Bar --}}
                                <div class="input-group input-group-sm flex-grow-1">
                                    <span class="input-group-text bg-white border-end-0">
                                        <i class="bi bi-search text-muted" style="font-size:0.75rem;"></i>
                                    </span>
                                    <input
                                        type="text"
                                        class="form-control border-start-0 ps-0"
                                        placeholder="Search roles..."
                                        wire:model.live.debounce.300ms="searchRole">
                                </div>

                                {{-- Edit Button --}}
                                <button
                                    class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1 flex-shrink-0"
                                    wire:click="editRole({{ $selectedRoleId }})"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editRoleModal"
                                    {{ !$selectedRoleId ? 'disabled' : '' }}
                                    title="Edit selected role">
                                    <i class="bi bi-pencil-fill" style="font-size:0.75rem;"></i>
                                    <span class="small">Edit</span>
                                </button>

                                {{-- Delete Button --}}
                                <button
                                    class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1 flex-shrink-0"
                                    wire:click="deleteRole({{ $selectedRoleId }})"
                                    onclick="return confirm('Are you sure you want to delete this role?')"
                                    {{ !$selectedRoleId ? 'disabled' : '' }}
                                    title="Delete selected role">
                                    <i class="bi bi-trash-fill" style="font-size:0.75rem;"></i>
                                    <span class="small">Delete</span>
                                </button>
                            </div>
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
            
            <a href="#" 
            class="list-group-item list-group-item-action role-item {{ $selectedRoleId == $role->id ? 'active-role' : '' }} px-4 py-3" 
            wire:click.prevent="selectRole({{ $role->id }})"
            wire:key="role-{{ $role->id }}-{{ $loop->index }}">  {{-- Added wire:key --}}
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
                    <span class="badge bg-{{ $color }} bg-opacity-10 text-{{ $color === 'success' ? 'white' : $color }}">
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
        <livewire:roles.manage-role />
        <livewire:notify />
    </div>
    @push('scripts')
    <script>
        Livewire.on('delayedRoleCreated', () => {
            console.log ("yes");
            setTimeout(() => {
                Livewire.dispatch('roleCreated');
            }, 1500);
        });
    </script>
    @endpush
</div>