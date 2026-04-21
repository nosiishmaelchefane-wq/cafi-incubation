<?php
// app/Livewire/AssignedEvaluatorsModal.php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use App\Models\Call;
use App\Models\User;
use App\Models\AssignedEvaluator;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;


new class extends Component
{
    public $callId = null;
    public $call = null;
    public $showAssignModal = false;
    public $evaluationDeadline = '';
    public $selectAll = false;
    public $allEvaluators = [];
    public $isSaving = false;
    
    public function mount($callId = null)
    {
        $this->callId = $callId;
        if ($callId) {
            $this->call = Call::with('assignedEvaluators')->find($callId);
            $this->loadEvaluators();
        }
    }
    
    #[On('openAssignEvaluatorModal')]
    public function openModal($callId = null)
    {
        if ($callId) {
            $this->callId = $callId;
            $this->call = Call::with('assignedEvaluators')->find($callId);
        }
        $this->loadEvaluators();
        $this->showAssignModal = true;
    }
    
    public function loadEvaluators()
    {
        // Check if Evaluator role exists
        $roleExists = Role::where('name', 'Evaluation Officer')->exists();
        
        if (!$roleExists) {
            $this->allEvaluators = [];
            $this->dispatch('notify', type: 'warning', message: 'Evaluator role not found.');
            return;
        }
        
        // Get users with Evaluator role
        $evaluatorUsers = User::role('Evaluation Officer')->get();
        
        if ($evaluatorUsers->isEmpty()) {
            $this->allEvaluators = [];
            $this->dispatch('notify', type: 'info', message: 'No users have the Evaluator role. Assign the role to users first.');
            return;
        }
        
        // Get existing assignments for this call
        $existingAssignments = AssignedEvaluator::where('call_id', $this->callId)
            ->get()
            ->keyBy('user_id');
        
        // Get eligible applications count for this call
        $eligibleApplicationsCount = $this->call ? $this->call->getEligibleApplicationsForEvaluation()->count() : 0;
        
        $this->allEvaluators = $evaluatorUsers->map(function($user) use ($existingAssignments, $eligibleApplicationsCount) {
            $assignment = $existingAssignments->get($user->id);
            
            return [
                'id' => $user->id,
                'initials' => $this->getInitials($user->name ?? $user->username),
                'name' => $user->name ?? $user->username,
                'email' => $user->email,
                'role' => $user->roles->first()->name ?? 'Evaluator',
                'assigned' => $assignment ? true : false,
                'assignedApps' => $assignment ? $assignment->assigned_applications_count : 0,
                'scoredApps' => $assignment ? $assignment->scored_applications_count : 0,
                'status' => $assignment ? $assignment->status : 'pending',
                'deadline' => $assignment ? $assignment->evaluation_deadline?->format('Y-m-d') : null,
                'active' => $user->is_active ?? true,
                'eligibleAppsCount' => $eligibleApplicationsCount,
            ];
        })->toArray();
        
        // Set default deadline if not set
        if (empty($this->evaluationDeadline) && $this->call && $this->call->close_date) {
            $this->evaluationDeadline = $this->call->close_date->format('Y-m-d');
        } elseif (empty($this->evaluationDeadline)) {
            $this->evaluationDeadline = now()->addDays(30)->format('Y-m-d');
        }
    }
    
    private function getInitials($name)
    {
        $words = explode(' ', $name);
        $initials = '';
        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= strtoupper($word[0]);
            }
        }
        return substr($initials, 0, 2);
    }
    
    public function updatedSelectAll($value)
    {
        foreach ($this->allEvaluators as $index => $ev) {
            if ($ev['scoredApps'] == 0) {
                $this->allEvaluators[$index]['assigned'] = $value;
            }
        }
    }
    
    public function closeModal()
    {
        $this->showAssignModal = false;
        $this->reset(['selectAll', 'isSaving']);
    }
    
    public function saveAssignments()
    {
        if ($this->isSaving) {
            return;
        }
        
        $this->isSaving = true;
        
        if (!$this->callId) {
            $this->dispatch('notify', type: 'error', message: 'No call selected.');
            $this->isSaving = false;
            return;
        }
        
        if (empty($this->evaluationDeadline)) {
            $this->dispatch('notify', type: 'error', message: 'Evaluation deadline is required.');
            $this->isSaving = false;
            return;
        }
        
        if ($this->evaluationDeadline < now()->format('Y-m-d')) {
            $this->dispatch('notify', type: 'error', message: 'Evaluation deadline cannot be in the past.');
            $this->isSaving = false;
            return;
        }
        
        $assignedCount = 0;
        $removedCount = 0;
        
        $eligibleApplicationsCount = $this->call->getEligibleApplicationsForEvaluation()->count();
        
        foreach ($this->allEvaluators as $evaluator) {
            $existingAssignment = AssignedEvaluator::where('call_id', $this->callId)
                ->where('user_id', $evaluator['id'])
                ->first();
            
            if ($evaluator['assigned']) {
                if ($existingAssignment) {
                    $existingAssignment->update([
                        'evaluation_deadline' => $this->evaluationDeadline,
                        'assigned_applications_count' => $eligibleApplicationsCount,
                    ]);
                } else {
                    AssignedEvaluator::create([
                        'call_id' => $this->callId,
                        'user_id' => $evaluator['id'],
                        'assigned_by' => Auth::id(),
                        'evaluation_deadline' => $this->evaluationDeadline,
                        'status' => 'pending',
                        'assigned_applications_count' => $eligibleApplicationsCount,
                        'assigned_at' => now(),
                    ]);
                    $assignedCount++;
                }
            } else {
                if ($existingAssignment) {
                    if ($existingAssignment->scored_applications_count > 0) {
                        $this->dispatch('notify', 
                            type: 'warning', 
                            message: "Cannot remove {$evaluator['name']} - they have already scored {$existingAssignment->scored_applications_count} application(s)."
                        );
                        continue;
                    }
                    $existingAssignment->delete();
                    $removedCount++;
                }
            }
        }
        
        $message = [];
        if ($assignedCount > 0) $message[] = $assignedCount . ' evaluator(s) assigned';
        if ($removedCount > 0) $message[] = $removedCount . ' evaluator(s) removed';
        if (empty($message)) $message[] = 'No changes made';
        
        $this->dispatch('notify', type: 'success', message: implode(' and ', $message) . ' successfully.');
        $this->dispatch('evaluatorsUpdated', callId: $this->callId);
        $this->closeModal();
    }
    
    #[Computed]
    public function assignedCount()
    {
        return collect($this->allEvaluators)->where('assigned', true)->count();
    }
    
    #[Computed]
    public function totalEvaluators()
    {
        return count($this->allEvaluators);
    }
}
?>
<div 
    x-data="{ open: @entangle('showAssignModal') }"
    x-cloak
>

    <!-- Modal Backdrop -->
    <div 
        x-show="open"
        x-transition.opacity
        class="fixed inset-0 bg-black bg-opacity-50 z-40"
        @click="open = false"
    ></div>

    <!-- Modal -->
    <div 
        x-show="open"
        x-transition
        @keydown.escape.window="open = false"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
    >
        <div 
            class="bg-white rounded-3 shadow-lg w-100"
            style="max-width: 1000px; max-height: 90vh; display: flex; flex-direction: column;"
            @click.stop
        >

            <!-- Header -->
            <div class="px-4 pt-4 pb-2 d-flex justify-content-between align-items-center border-bottom">
                <div>
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-person-plus-fill text-primary me-2"></i>
                        Assign Evaluators
                    </h5>
                    @if($call)
                    <small class="text-muted">
                        Call: {{ $call->title }} (Cohort {{ $call->cohort }})
                    </small>
                    @endif
                </div>
                <button class="btn-close" @click="open = false"></button>
            </div>

            <!-- Body -->
            <div class="px-4 py-3" style="flex: 1; overflow-y: auto;">
                <div class="alert alert-info small">
                    <i class="bi bi-info-circle me-2"></i>
                    Each evaluator can be assigned only once per call. Once assigned, they will be able to evaluate all eligible applications for this call.
                    @if($call)
                    <strong>{{ $this->assignedCount }}</strong> of <strong>{{ $this->totalEvaluators }}</strong> evaluators currently assigned.
                    @endif
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="small text-muted">
                        <i class="bi bi-people me-1"></i>
                        <span class="fw-semibold">{{ $this->assignedCount }}</span> evaluator(s) selected
                    </div>
                    
                    <div class="d-flex align-items-center gap-3">
                        <div class="small">
                            <label class="form-label mb-0 me-2">Evaluation Deadline:</label>
                            <input type="date"
                                   class="form-control form-control-sm d-inline-block"
                                   style="width: auto;"
                                   wire:model="evaluationDeadline">
                        </div>
                    </div>
                </div>

                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-hover align-middle small mb-0">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th style="width: 40px;">
                                    <input type="checkbox" wire:model="selectAll">
                                </th>
                                <th>Evaluator</th>
                                <th>Role</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Assigned Apps</th>
                                <th class="text-center">Scored</th>
                                <th class="text-center">Progress</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($allEvaluators as $index => $ev)
                            <tr class="{{ $ev['assigned'] ? 'table-light' : '' }}">
                                <td>
                                    <input type="checkbox"
                                           wire:model="allEvaluators.{{ $index }}.assigned"
                                           {{ $ev['scoredApps'] > 0 ? 'disabled' : '' }}>
                                    @if($ev['scoredApps'] > 0)
                                    <div class="small text-muted mt-1">
                                        <i class="bi bi-lock-fill"></i>
                                    </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="ev-lg-avatar bg-primary text-white">
                                            {{ $ev['initials'] }}
                                        </div>
                                        <div>
                                            <div class="fw-medium">{{ $ev['name'] }}</div>
                                            <div class="text-muted small">{{ $ev['email'] }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $ev['role'] }}</td>
                                <td class="text-center">
                                    @if($ev['assigned'])
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-warning text-dark',
                                                'in_progress' => 'bg-info text-white',
                                                'completed' => 'bg-success text-white',
                                            ];
                                            $statusText = [
                                                'pending' => 'Pending',
                                                'in_progress' => 'In Progress',
                                                'completed' => 'Completed',
                                            ];
                                        @endphp
                                        <span class="badge {{ $statusColors[$ev['status']] ?? 'bg-secondary' }}">
                                            {{ $statusText[$ev['status']] ?? ucfirst($ev['status']) }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-25 text-muted">
                                            Not Assigned
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">{{ $ev['assignedApps'] }}</td>
                                <td class="text-center">{{ $ev['scoredApps'] }}</td>
                                <td class="text-center">
                                    @if($ev['assignedApps'] > 0)
                                        <div class="d-flex align-items-center gap-1">
                                            <div class="progress flex-grow-1" style="height: 5px; width: 80px;">
                                                <div class="progress-bar bg-success" 
                                                     style="width: {{ ($ev['scoredApps'] / max($ev['assignedApps'], 1)) * 100 }}%">
                                                </div>
                                            </div>
                                            <small class="text-muted">
                                                {{ $ev['scoredApps'] }}/{{ $ev['assignedApps'] }}
                                            </small>
                                        </div>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-people fs-1 d-block mb-2 opacity-50"></i>
                                    No evaluator users found. Please create users with the "Evaluator" role first.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($call && $call->getEligibleApplicationsForEvaluation()->count() > 0)
                <div class="mt-3 small text-muted">
                    <i class="bi bi-info-circle me-1"></i>
                    <strong>{{ $call->getEligibleApplicationsForEvaluation()->count() }}</strong> eligible application(s) will be assigned to each selected evaluator.
                </div>
                @endif
            </div>

            <!-- Footer -->
            <div class="px-4 pb-4 pt-2 d-flex justify-content-end gap-2 border-top">
                <button class="btn btn-light px-4" @click="open = false">
                    Cancel
                </button>
                <button class="btn btn-primary px-4" wire:click="saveAssignments" wire:loading.attr="disabled">
                    <span wire:loading.remove>
                        <i class="bi bi-save me-1"></i>Save Assignments
                    </span>
                    <span wire:loading>
                        <span class="spinner-border spinner-border-sm me-1"></span>
                        Saving...
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>