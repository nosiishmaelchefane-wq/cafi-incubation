<?php

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\On;

new class extends Component
{
    public $pendingCount = 0;

    /**
     * Mount the component and load pending count
     */
    public function mount(): void
    {
        $this->loadPendingCount();
    }

    /**
     * Load pending users count
     */
    public function loadPendingCount(): void
    {
        $this->pendingCount = User::whereNull('email_verified_at')->count();
    }

    /**
     * Refresh pending count when users are approved/rejected
     */
    #[On('userApproved')]
    #[On('userRejected')]
    #[On('userCreated')]
    public function refreshPendingCount(): void
    {
        $this->loadPendingCount();
    }

    

    /**
     * Navigate to pending tab
     */
    public function goToPendingTab(): void
    {
        $this->dispatch('setActiveTab', 'pending');
    }
};
?>

<div>
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="bi bi-people-fill text-primary me-2"></i>User Management
            </h4>
            <p class="text-muted mb-0 small">Manage users, approvals, and role assignments.</p>
        </div>
        <button class="btn btn-primary d-flex align-items-center gap-2 px-4"
            wire:click="$dispatch('openAddUserModal')">
            <i class="bi bi-person-plus-fill"></i>
            <span>Add User</span>
        </button>
    </div>

    <!-- ── Pending Approval Banner (only shows if there are pending users) ── -->
    @if($pendingCount > 0)
    <div class="pending-banner rounded-3 p-3 mb-4 d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-hourglass-split text-warning fs-5"></i>
            <div>
                <span class="fw-semibold text-dark small">
                    {{ $pendingCount }} {{ Str::plural('user', $pendingCount) }} awaiting approval
                </span>
                <span class="text-muted small d-block d-sm-inline ms-sm-2">
                    New registrations require admin approval before they can access the system.
                </span>
            </div>
        </div>
        <a href="#pendingTab" 
           class="btn btn-sm btn-warning d-flex align-items-center gap-1"
           wire:click.prevent="goToPendingTab">
            <i class="bi bi-eye-fill"></i>
            <span class="small">Review Now</span>
        </a>
    </div>
    @endif
      <livewire:user-management.modals.add-users />
</div>