<?php

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;

new class extends Component {
    use WithPagination;

    // Search and filters with URL persistence
    #[Url(as: 'q', history: true)]
    public $search = '';
    
    #[Url(as: 'role', history: true)]
    public $selectedRole = '';
    
    #[Url(as: 'status', history: true)]
    public $selectedStatus = '';
    
    #[Url(as: 'tab', history: true)]
    public $selectedTab = 'all'; // all, pending, active, suspended
 
    
    
    // Pending approval role assignments
    public $pendingRoleAssignment = [];

    /**
     * Get all roles for dropdowns
     */
    public function with(): array
    {
        return [
            'roles' => \Spatie\Permission\Models\Role::all(),
            'stats' => [
                'total' => User::count(),
                'active' => User::active()->count(),
                'pending' => User::whereNull('email_verified_at')->count(),
                'suspended' => User::suspended()->count(),
                'byRole' => User::getCountByRole(),
            ]
        ];
    }

    /**
     * Get users query with filters
     */
    public function users()
    {
        $query = User::query()
            ->with('roles')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('username', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->selectedRole, function ($query) {
                $query->withRole($this->selectedRole);
            })
            ->when($this->selectedStatus, function ($query) {
                switch ($this->selectedStatus) {
                    case 'active':
                        $query->active();
                        break;
                    case 'suspended':
                        $query->suspended();
                        break;
                    case 'pending':
                        $query->whereNull('email_verified_at');
                        break;
                }
            });

        // Apply tab filtering
        switch ($this->selectedTab) {
            case 'pending':
                $query->whereNull('email_verified_at');
                break;
            case 'active':
                $query->active();
                break;
            case 'suspended':
                $query->suspended();
                break;
        }

        return $query->latest()->paginate(10);
    }

    /**
     * Get pending users for approval cards
     */
    public function pendingUsers()
    {
        return User::whereNull('email_verified_at')
            ->latest()
            ->take(5)
            ->get();
    }

    #[On('userCreated')] 
    #[On('userUpdated')] 
    public function userCreated()
    {
        $this->pendingUsers();
        $this->users();
    }

    /**
     * Reset page when search changes
     */
    public function updatedSearch()
    {
        $this->resetPage();
    }

    /**
     * Reset page when filters change
     */
    public function updatedSelectedRole()
    {
        $this->resetPage();
    }

    public function updatedSelectedStatus()
    {
        $this->resetPage();
    }

    /**
     * Approve a pending user
     */
    public function approveUser($userId)
    {
        $user = User::findOrFail($userId);
        
        // Get the role before assigning
        $assignedRole = null;
        if (isset($this->pendingRoleAssignment[$userId])) {
            $assignedRole = $this->pendingRoleAssignment[$userId];
            $user->assignRole($assignedRole);
        }
        
        // Mark email as verified and activate
        $user->email_verified_at = now();
        $user->is_active = true;
        $user->save();

        // Send approval email
        \Mail::to($user->email)->send(new \App\Mail\UserApproved($user, $assignedRole));

        // Clear the pending assignment
        unset($this->pendingRoleAssignment[$userId]);
        $this->dispatch('userApproved');

        $this->dispatch('notify', type: 'success', message: 'User Approved successfully! A confirmation email has been sent to the user.');
    }

    /**
     * Reject a pending user
     */
    public function rejectUser($userId)
    {
        $user = User::findOrFail($userId);
        
        // Send rejection email before deleting
        \Mail::to($user->email)->send(new \App\Mail\UserRejected($user));
        
        // Delete the user (or mark as rejected)
        $user->delete();
        $this->dispatch('userRejected');
        $this->dispatch('notify', type: 'warning', message: 'User rejected! A notification email has been sent to the user.');
    }
 

    /**
     * Assign role to user
     */
    public function assignRole($userId, $roleName)
    {
        $user = User::findOrFail($userId);
        $user->syncRoles([$roleName]);

        $this->dispatch('notify', type: 'success', message: 'Role assigned successfully!');
    }

    /**
     * Delete a user
     */
    public function deleteUser($userId)
    {
        $user = User::findOrFail($userId);
        $user->delete();

        $this->dispatch('notify', type: 'success', message: 'User deleted successfully!');
    }

    /**
     * Listen for modal close events
     */
    #[On('closeModal')]
    public function closeModal()
    {
        $this->showSuspendModal = false;
        $this->reset(['selectedUserId', 'suspensionReason', 'suspensionUntil']);
    }

        /**
     * Confirm and delete a user
     */
    public function confirmDelete($userId)
    {
        
        try {
            $user = User::findOrFail($userId);
            $userName = $user->username;
            $user->delete();
            
            $this->dispatch('notify', type: 'success', message: "User {$userName} deleted successfully!");
            $this->dispatch('userUpdated');
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Failed to delete user: ' . $e->getMessage());
        }
    }
};
?>

<div>
    <!-- ── Main Card ── -->
    <div class="card border-0 shadow-sm">

        <!-- Tabs Navigation -->
        <div class="card-header bg-white border-bottom px-4 py-3">
            <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item">
                    <a class="nav-link {{ $selectedTab === 'all' ? 'active' : '' }}" 
                       wire:click="$set('selectedTab', 'all')" 
                       href="#">
                       All Users ({{ $stats['total'] }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $selectedTab === 'pending' ? 'active' : '' }}" 
                       wire:click="$set('selectedTab', 'pending')" 
                       href="#">
                       Pending ({{ $stats['pending'] }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $selectedTab === 'active' ? 'active' : '' }}" 
                       wire:click="$set('selectedTab', 'active')" 
                       href="#">
                       Active ({{ $stats['active'] }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $selectedTab === 'suspended' ? 'active' : '' }}" 
                       wire:click="$set('selectedTab', 'suspended')" 
                       href="#">
                       Suspended ({{ $stats['suspended'] }})
                    </a>
                </li>
            </ul>
        </div>

        <!-- Filter / Search Bar -->
        <div class="card-body border-bottom py-3 px-4 filter-bar">
            <div class="row g-2 align-items-center">
                <div class="col-12 col-sm-5 col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted" style="font-size:.75rem;"></i>
                        </span>
                        <input type="text" 
                               class="form-control border-start-0 ps-0" 
                               placeholder="Search by name or email…"
                               wire:model.live.debounce.300ms="search">
                    </div>
                </div>
                <div class="col-6 col-sm-3 col-md-2">
                    <select class="form-select form-select-sm" wire:model.live="selectedRole">
                        <option value="">All Roles</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-sm-3 col-md-2 hide-xs">
                    <select class="form-select form-select-sm" wire:model.live="selectedStatus">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="pending">Pending</option>
                        <option value="suspended">Suspended</option>
                    </select>
                </div>
                <div class="col-12 col-md-4 d-flex justify-content-md-end gap-2 mt-1 mt-md-0">
                    <button class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1 hide-sm">
                        <i class="bi bi-download" style="font-size:.75rem;"></i>
                        <span class="small">Export</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- ── Pending Approval Section (shown when Pending tab active) ── -->
        @if($selectedTab === 'pending' && $this->pendingUsers()->count() > 0)
        <div class="px-4 pt-3 pb-0">
            <p class="small fw-semibold text-warning mb-2">
                <i class="bi bi-exclamation-triangle-fill me-1"></i>Awaiting Approval
            </p>
        </div>

        <div class="px-4 pb-3">
            <div class="row g-3">
                @foreach($this->pendingUsers() as $user)
                <div class="col-12 col-md-6 col-xl-4" wire:key="pending-{{ $user->id }}">
                    <div class="card border shadow-sm approval-card h-100">
                        <div class="card-header d-flex align-items-center justify-content-between py-2 px-3">
                            <span class="badge badge-pending rounded-pill px-2 py-1 small">
                                <i class="bi bi-hourglass-split me-1"></i>Pending Approval
                            </span>
                            <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                        </div>
                        <div class="card-body px-3 py-3">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="avatar-circle bg-info text-white rounded-circle d-flex align-items-center justify-content-center fw-bold">
                                    {{ $user->profile_photo_url ?: strtoupper(substr($user->username, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold small">{{ $user->username }}</div>
                                    <div class="text-muted" style="font-size:.75rem;">{{ $user->email }}</div>
                                </div>
                            </div>
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <span class="d-flex align-items-center gap-1 text-muted" style="font-size:.75rem;">
                                    <i class="bi bi-calendar3"></i> Registered {{ $user->created_at->format('d M Y') }}
                                </span>
                            </div>
                            <!-- Role Assignment -->
                            <div class="mb-3">
                                <label class="form-label small fw-semibold mb-1">Assign Role</label>
                                <select class="form-select form-select-sm" 
                                        wire:model="pendingRoleAssignment.{{ $user->id }}">
                                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-top px-3 py-2 d-flex gap-2">
                            <button class="btn btn-sm btn-success flex-fill d-flex align-items-center justify-content-center gap-1"
                                    wire:click="approveUser({{ $user->id }})"
                                    wire:loading.attr="disabled"
                                    wire:target="approveUser({{ $user->id }})">
                                <i class="bi bi-check-lg"></i> Approve
                                <span wire:loading wire:target="approveUser({{ $user->id }})" class="spinner-border spinner-border-sm"></span>
                            </button>
                            <button class="btn btn-sm btn-outline-danger flex-fill d-flex align-items-center justify-content-center gap-1"
                                    wire:click="rejectUser({{ $user->id }})"
                                    wire:confirm="Are you sure you want to reject this user?"
                                    wire:loading.attr="disabled"
                                    wire:target="rejectUser({{ $user->id }})">
                                <i class="bi bi-x-lg"></i> Reject
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- ── Divider ── -->
        <div class="px-4 pt-2 pb-0">
            <hr class="my-2">
            <p class="small fw-semibold text-muted mb-2">
                <i class="bi bi-people me-1"></i>All Users
            </p>
        </div>
        @endif

        <!-- ── Users Table ── -->
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 users-table">
                <thead class="table-light">
                    <tr>
                        <th class="px-4 py-3" style="width:5%;">
                            <input class="form-check-input" type="checkbox">
                        </th>
                        <th class="py-3">User</th>
                        <th class="py-3 hide-sm">Role</th>
                        <th class="py-3 hide-sm">Status</th>
                        <th class="py-3 hide-xs">Joined</th>
                        <th class="py-3 hide-xs">Last Active</th>
                        <th class="py-3 text-end px-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($this->users() as $user)
                    <tr wire:key="user-{{ $user->id }}" 
                        @class([
                            'bg-danger bg-opacity-10' => $user->is_suspended,
                            'bg-warning bg-opacity-10' => is_null($user->email_verified_at)
                        ])>
                        <td class="px-4"><input class="form-check-input" type="checkbox"></td>
                        <td x-data="{ hover: false }" 
                            @mouseenter="hover = true" 
                            @mouseleave="hover = false">
                            <a href="{{ route('users.show', $user->id) }}" 
                            class="text-decoration-none d-block"
                            :class="{ 'bg-light rounded': hover }"
                            style="transition: all 0.2s; padding: 0.25rem 0.5rem; margin: -0.25rem -0.5rem;">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-circle rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                        style="background-color: {{ '#' . substr(md5($user->username), 0, 6) }}; color: white;">
                                        {{ strtoupper(substr($user->username, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold small @if($user->is_suspended) text-muted @endif">
                                            {{ $user->username }}
                                        </div>
                                        <div class="text-muted" style="font-size:.72rem;">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </a>
                        </td>
                        <td class="hide-sm">
                            @if($user->roles->isNotEmpty())
                                @foreach($user->roles as $role)
                                    <span class="role-pill d-inline-block mb-1"
                                          style="background-color: {{ '#' . substr(md5($role->name), 0, 6) }}20; color: {{ '#' . substr(md5($role->name), 0, 6) }};">
                                        {{ $role->name }}
                                    </span>
                                @endforeach
                            @else
                                <span class="text-muted small fst-italic">— Unassigned —</span>
                            @endif
                        </td>
                        <td class="hide-sm">
                            @if(is_null($user->email_verified_at))
                                <span class="badge badge-pending rounded-pill px-2 py-1 small">
                                    <i class="bi bi-hourglass-split me-1" style="font-size:.65rem;"></i>Pending
                                </span>
                            @elseif($user->is_suspended)
                                <span class="badge badge-suspended rounded-pill px-2 py-1 small">
                                    <i class="bi bi-circle-fill me-1" style="font-size:.45rem;"></i>Suspended
                                    @if($user->suspended_until)
                                        <span class="d-block small" style="font-size:.6rem;">until {{ $user->suspended_until->format('M d, Y') }}</span>
                                    @endif
                                </span>
                            @else
                                <span class="badge badge-active rounded-pill px-2 py-1 small">
                                    <i class="bi bi-circle-fill me-1" style="font-size:.45rem; color: #28a745;"></i>Active
                                    @if($user->is_online)
                                        <span class="d-block small text-success" style="font-size:.6rem;">Online now</span>
                                    @endif
                                </span>
                            @endif
                        </td>
                        <td class="hide-xs text-muted small">{{ $user->created_at->format('M d, Y') }}</td>
                        <td class="hide-xs text-muted small">
                            @if($user->last_login_at)
                                {{ $user->last_login_at->diffForHumans() }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="text-end px-4">
                            <div class="row-actions d-flex justify-content-end gap-1">
                                @if(is_null($user->email_verified_at))
                                    <button class="btn btn-sm btn-success" 
                                            wire:click="approveUser({{ $user->id }})"
                                            title="Approve">
                                        <i class="bi bi-check-lg" style="font-size:.7rem;"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" 
                                            wire:click="rejectUser({{ $user->id }})"
                                            wire:confirm="Are you sure you want to reject this user?"
                                            title="Reject">
                                        <i class="bi bi-x-lg" style="font-size:.7rem;"></i>
                                    </button>
                                @elseif($user->is_suspended)
                                    <button class="btn btn-sm btn-outline-success" 
                                            wire:click="$dispatch('unsuspendUser', { userId: {{ $user->id }} })"
                                            wire:confirm="Are you sure you want to reinstate this user?"
                                            title="Reinstate">
                                        <i class="bi bi-check-circle-fill" style="font-size:.7rem;"></i>
                                    </button>
                                @else
                                    <button wire:click="$dispatch('openEditRegistrationModal', { userId: {{ $user->id }} })" 
                                        class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil-fill" style="font-size:.7rem;"></i>
                                    </button>
                                
                                    <button wire:click="confirmDelete({{ $user->id }})" 
                                            class="btn btn-sm btn-outline-danger" 
                                            title="Delete"
                                            wire:confirm="Are you sure you want to delete this user?">
                                        <i class="bi bi-trash-fill" style="font-size:.7rem;"></i>
                                    </button>
                                    @if($user->is_suspended)
                                        <button class="btn btn-sm btn-outline-success" 
                                            wire:click="$dispatch('unsuspendUser', { userId: {{ $user->id }} })"
                                            wire:confirm="Are you sure you want to reinstate this user?"
                                            title="Reinstate">
                                        <i class="bi bi-check-circle-fill" style="font-size:.7rem;"></i>
                                    </button>
                                    @else
                                        <button class="btn btn-sm btn-outline-danger" 
                                            wire:click="$dispatch('confirmSuspend', { userId: {{ $user->id }} })"
                                            title="Suspend"
                                            @if($user->is_suspended) disabled @endif>
                                        <i class="bi bi-slash-circle-fill" style="font-size:.7rem;"></i>
                                    </button>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            No users found matching your criteria.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- ── Pagination ── -->
        <div class="card-footer bg-white border-top px-4 py-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
            <small class="text-muted">
                Showing {{ $this->users()->firstItem() ?? 0 }}–{{ $this->users()->lastItem() ?? 0 }} 
                of {{ $this->users()->total() }} users
            </small>
            {{ $this->users()->links() }}
        </div>

    </div><!-- /card -->

    <!-- Suspend Modal -->
    
    <livewire:user-management.modals.suspend-user/>
  
    <livewire:roles.role-modal />
</div>