<?php

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\On;

new class extends Component
{
    /**
     * Get statistics data for the dashboard
     */
    public function with(): array
    {
        return [
            'stats' => [
                'total' => User::count(),
                'active' => User::active()->count(),
                'pending' => User::whereNull('email_verified_at')->count(),
                'suspended' => User::suspended()->count(),
                'byRole' => User::getCountByRole(),
            ]
        ];
    }

    #[On('userCreated')] 
    #[On('userRejected')]
    public function userCreated()
    {
        $this->with();
    }

};


?>

<div>
    <div class="row g-3 mb-4">
        <!-- Total Users Card -->
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100 stat-card">
                <div class="card-body d-flex align-items-center gap-3 p-3">
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary rounded-3">
                        <i class="bi bi-people-fill fs-4"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-4 lh-1">{{ $stats['total'] }}</div>
                        <small class="text-muted">Total Users</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Users Card -->
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100 stat-card">
                <div class="card-body d-flex align-items-center gap-3 p-3">
                    <div class="stat-icon bg-success bg-opacity-10 text-white rounded-3">
                        <i class="bi bi-person-check-fill fs-4"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-4 lh-1">{{ $stats['active'] }}</div>
                        <small class="text-muted">Active</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Users Card -->
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100 stat-card">
                <div class="card-body d-flex align-items-center gap-3 p-3">
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning rounded-3">
                        <i class="bi bi-hourglass-split fs-4"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-4 lh-1">{{ $stats['pending'] }}</div>
                        <small class="text-muted">Pending</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Suspended Users Card -->
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100 stat-card">
                <div class="card-body d-flex align-items-center gap-3 p-3">
                    <div class="stat-icon bg-danger bg-opacity-10 text-danger rounded-3">
                        <i class="bi bi-person-x-fill fs-4"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-4 lh-1">{{ $stats['suspended'] }}</div>
                        <small class="text-muted">Suspended</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Optional: Add role-based statistics -->
    @if(!empty($stats['byRole']))
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0 fw-semibold">Users by Role</h6>
                </div>
                <div class="card-body pt-0">
                    <div class="row g-2">
                        @foreach($stats['byRole'] as $role => $count)
                        <div class="col-6 col-md-3">
                            <div class="d-flex align-items-center justify-content-between p-2 rounded-3" 
                                 style="background-color: {{ '#' . substr(md5($role), 0, 6) }}10;">
                                <span class="small fw-semibold">{{ $role }}</span>
                                <span class="badge" style="background-color: {{ '#' . substr(md5($role), 0, 6) }};">
                                    {{ $count }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>