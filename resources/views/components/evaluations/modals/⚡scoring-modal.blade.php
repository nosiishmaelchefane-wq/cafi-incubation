<?php

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\IncubationApplication;
use App\Models\EvaluationScore;
use App\Models\AssignedEvaluator;
use Illuminate\Support\Facades\Auth;

new class extends Component
{
    public $showModal = false;
    public $applicationId = null;
    public $application = null;
    public $scoreId = null;
    public $activeTab = 'score';
    public $isLocked = false;
    public $isAssigned = false;
    public $existingScore = null;
    
    // Scoring data
    public $innovation_uniqueness = 0;
    public $innovation_development = 0;
    public $commercial_vision = 0;
    public $commercial_disruption = 0;
    public $commercial_market_size = 0;
    public $team_experience = 0;
    public $team_diversity = 0;
    public $team_size = 0;
    public $team_women_shareholders = false;
    public $team_youth_shareholders = false;
    public $operation_sustainability = 0;
    public $social_safeguards = 0;
    public $social_risk_mitigation = 0;
    public $totalScore = 0;
    public $evaluator_comments = '';
    
    #[On('openScoreModal')]
    public function openModal($applicationId = null)
    {
        $this->applicationId = $applicationId;
        $this->application = IncubationApplication::with(['user', 'call'])->find($applicationId);
        $this->activeTab = 'score';
        
        $this->checkAssignment();
        $this->loadExistingScore();
        $this->checkLocked();
        
        $this->showModal = true;
    }
    
    #[On('closeScoreModal')]
    public function closeModal()
    {
        $this->reset();
        $this->showModal = false;
    }
    
    public function checkAssignment()
    {
        if (!$this->application) {
            $this->isAssigned = false;
            return;
        }
        
        $this->isAssigned = AssignedEvaluator::where('call_id', $this->application->call_id)
            ->where('user_id', Auth::id())
            ->exists();
    }
    
    public function checkLocked()
    {
        $this->isLocked = $this->existingScore && $this->existingScore->status === 'submitted';
    }
    
    public function loadExistingScore()
    {
        $this->existingScore = EvaluationScore::where('application_id', $this->applicationId)
            ->where('evaluator_id', Auth::id())
            ->first();
        
        if ($this->existingScore) {
            $this->scoreId = $this->existingScore->id;
            $this->innovation_uniqueness = $this->existingScore->innovation_uniqueness;
            $this->innovation_development = $this->existingScore->innovation_development;
            $this->commercial_vision = $this->existingScore->commercial_vision;
            $this->commercial_disruption = $this->existingScore->commercial_disruption;
            $this->commercial_market_size = $this->existingScore->commercial_market_size;
            $this->team_experience = $this->existingScore->team_experience;
            $this->team_diversity = $this->existingScore->team_diversity;
            $this->team_size = $this->existingScore->team_size;
            $this->team_women_shareholders = $this->existingScore->team_women_shareholders;
            $this->team_youth_shareholders = $this->existingScore->team_youth_shareholders;
            $this->operation_sustainability = $this->existingScore->operation_sustainability;
            $this->social_safeguards = $this->existingScore->social_safeguards;
            $this->social_risk_mitigation = $this->existingScore->social_risk_mitigation;
            $this->totalScore = $this->existingScore->total_score;
            $this->evaluator_comments = $this->existingScore->evaluator_comments ?? '';
        } else {
            $this->evaluator_name = Auth::user()->name;
            $this->evaluation_date = now()->format('Y-m-d');
            $this->evaluation_location = 'Maseru, Lesotho';
        }
        
        $this->calculateTotalScore();
    }
    
    public function updated($property)
    {
        if ($property !== 'activeTab' && !$this->isLocked) {
            $this->calculateTotalScore();
        }
    }
    
    public function calculateTotalScore()
    {
        $total = 0;
        
        $total += min($this->innovation_uniqueness, 10);
        $total += min($this->innovation_development, 5);
        $total += min($this->commercial_vision, 10);
        $total += min($this->commercial_disruption, 10);
        $total += min($this->commercial_market_size, 5);
        $total += min($this->team_experience, 6);
        $total += min($this->team_diversity, 6);
        $total += min($this->team_size, 10);
        $total += $this->team_women_shareholders ? 4 : 0;
        $total += $this->team_youth_shareholders ? 4 : 0;
        $total += min($this->operation_sustainability, 20);
        $total += min($this->social_safeguards, 5);
        $total += min($this->social_risk_mitigation, 5);
        
        $this->totalScore = min($total, 100);
    }
    
    public function getSection1TotalProperty()
    {
        $total = min($this->innovation_uniqueness, 10) + min($this->innovation_development, 5);
        return ['total' => $total, 'max' => 15];
    }
    
    public function getSection2TotalProperty()
    {
        $total = min($this->commercial_vision, 10) + min($this->commercial_disruption, 10) + min($this->commercial_market_size, 5);
        return ['total' => $total, 'max' => 25];
    }
    
    public function getSection3TotalProperty()
    {
        $total = min($this->team_experience, 6) + min($this->team_diversity, 6) + min($this->team_size, 10);
        $total += $this->team_women_shareholders ? 4 : 0;
        $total += $this->team_youth_shareholders ? 4 : 0;
        return ['total' => $total, 'max' => 30];
    }
    
    public function getSection4TotalProperty()
    {
        return ['total' => min($this->operation_sustainability, 20), 'max' => 20];
    }
    
    public function getSection5TotalProperty()
    {
        $total = min($this->social_safeguards, 5) + min($this->social_risk_mitigation, 5);
        return ['total' => $total, 'max' => 10];
    }
    
    public function saveScore()
    {
        if ($this->isLocked) {
            $this->dispatch('notify', type: 'error', message: 'This score has already been submitted and cannot be modified.');
            return;
        }
        
        if (!$this->isAssigned) {
            $this->dispatch('notify', type: 'error', message: 'You are not assigned to evaluate this application.');
            return;
        }
        
        $this->calculateTotalScore();
        
        EvaluationScore::updateOrCreate(
            [
                'application_id' => $this->applicationId,
                'evaluator_id' => Auth::id(),
            ],
            [
                'call_id' => $this->application->call_id,
                'innovation_uniqueness' => $this->innovation_uniqueness,
                'innovation_development' => $this->innovation_development,
                'commercial_vision' => $this->commercial_vision,
                'commercial_disruption' => $this->commercial_disruption,
                'commercial_market_size' => $this->commercial_market_size,
                'team_experience' => $this->team_experience,
                'team_diversity' => $this->team_diversity,
                'team_size' => $this->team_size,
                'team_women_shareholders' => $this->team_women_shareholders,
                'team_youth_shareholders' => $this->team_youth_shareholders,
                'operation_sustainability' => $this->operation_sustainability,
                'social_safeguards' => $this->social_safeguards,
                'social_risk_mitigation' => $this->social_risk_mitigation,
                'total_score' => $this->totalScore,
                'evaluator_comments' => $this->evaluator_comments,
                'status' => 'submitted',
                'submitted_at' => now(),
                'submitted_by' => Auth::id(),
            ]
        );
        
        $assignedEvaluator = AssignedEvaluator::where('call_id', $this->application->call_id)
            ->where('user_id', Auth::id())
            ->first();
        
        if ($assignedEvaluator) {
            $scoredCount = EvaluationScore::where('call_id', $this->application->call_id)
                ->where('evaluator_id', Auth::id())
                ->where('status', 'submitted')
                ->count();
            $assignedEvaluator->update(['scored_applications_count' => $scoredCount]);
        }
        
        $this->dispatch('notify', type: 'success', message: 'Score saved successfully! Total: ' . $this->totalScore . '/100');
        $this->dispatch('scoreUpdated');
        $this->closeModal();
    }
}
?>

<div 
    x-data="{ open: @entangle('showModal') }"
    x-cloak
>
    <!-- Modal Backdrop -->
    <div 
        x-show="open"
        x-transition.opacity
        class="fixed inset-0 bg-black bg-opacity-50 z-50"
        @click="open = false; $wire.closeModal()"
    ></div>

    <!-- Modal -->
    <div 
    x-show="open"
    x-transition
    @keydown.escape.window="open = false; $wire.closeModal()"
    class="fixed inset-0 z-50 flex items-start justify-center"
    style="padding: 50px 0 0 0; margin: 0; overflow-y: auto;"
>
       <div 
        class="bg-white shadow-lg w-100"
        style="width: 100vw; height: 100vh; max-width: 100vw; max-height: 100vh; display: flex; flex-direction: column; border-radius: 0;"
        @click.stop
    >
       

            <!-- Header -->
            <div class="px-5 pt-4 pb-3 d-flex justify-content-between align-items-center border-bottom">
                <div>
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-star-fill text-warning me-2"></i>
                        Score Application
                    </h5>
                    @if($application)
                    <small class="text-muted">
                        {{ $application->company_name }} · {{ $application->application_number }}
                    </small>
                    @endif
                </div>
                <button class="btn-close" @click="open = false; $wire.closeModal()"></button>
            </div>

            <!-- Locked/Unauthorized Banner -->
            @if(!$isAssigned && $application)
            <div class="alert alert-danger mx-3 mt-3 mb-0">
                <i class="bi bi-lock-fill me-2"></i>
                You are not assigned to evaluate this application. Only assigned evaluators can score applications.
            </div>
            @elseif($isLocked)
            <div class="alert alert-secondary mx-3 mt-3 mb-0">
                <i class="bi bi-lock-fill me-2"></i>
                This score has already been submitted and is locked. No further changes can be made.
            </div>
            @endif

            <!-- Score Summary Strip -->
            <div class="row g-2 text-center small bg-light p-3 rounded-3 mx-3 mt-3 mb-0">
                <div class="col-4">
                    <div class="text-muted">My Score</div>
                    <div class="fw-bold fs-5 text-success">{{ $totalScore }}/100</div>
                </div>
                <div class="col-4">
                    <div class="text-muted">Max Points</div>
                    <div class="fw-bold fs-5 text-primary">100</div>
                </div>
                <div class="col-4">
                    <div class="text-muted">Status</div>
                    <div class="fw-bold fs-5">
                        <span class="badge {{ $isLocked ? 'bg-secondary' : ($totalScore > 0 ? 'bg-success' : 'bg-warning') }}">
                            {{ $isLocked ? 'Locked' : ($totalScore > 0 ? 'In Progress' : 'Not Started') }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <div class="px-4 pt-3">
                <ul class="nav nav-tabs border-0 gap-2">
                    <li class="nav-item">
                        <button class="nav-link px-3 py-2 small fw-medium {{ $activeTab == 'score' ? 'active text-primary border-bottom border-primary border-2' : 'text-muted border-0' }}" 
                                wire:click="$set('activeTab', 'score')">
                            <i class="bi bi-star-fill me-1"></i>Score
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link px-3 py-2 small fw-medium {{ $activeTab == 'application' ? 'active text-primary border-bottom border-primary border-2' : 'text-muted border-0' }}" 
                                wire:click="$set('activeTab', 'application')">
                            <i class="bi bi-file-text-fill me-1"></i>Application Info
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link px-3 py-2 small fw-medium {{ $activeTab == 'evaluators' ? 'active text-primary border-bottom border-primary border-2' : 'text-muted border-0' }}" 
                                wire:click="$set('activeTab', 'evaluators')">
                            <i class="bi bi-people-fill me-1"></i>Evaluators
                        </button>
                    </li>
                </ul>
            </div>

            <!-- Body - Scrollable -->
            <div class="px-5 py-4" style="flex: 1; overflow-y: auto;">
                
                <!-- ==================== SCORE TAB ==================== -->
                <div style="{{ $activeTab != 'score' ? 'display: none;' : '' }}">
                    @if(!$isAssigned)
                    <div class="text-center py-5">
                        <i class="bi bi-shield-lock fs-1 d-block mb-3 text-muted"></i>
                        <h6 class="text-muted">Access Restricted</h6>
                        <p class="small text-muted">You are not assigned to evaluate this application.</p>
                    </div>
                    @else
                    <div class="eval-tool-header mb-3 p-3 rounded-3 bg-warning bg-opacity-10 border border-warning">
                        <div class="fw-bold small text-dark">LEHSFF Evaluation Tool — Enterprise Selection (Top 20)</div>
                        <div class="text-muted" style="font-size:.72rem;">Score out of 100 points</div>
                    </div>

                    <!-- Section 1: Innovation and Creativity -->
                    <div class="eval-section mb-4">
                        <div class="eval-section-header d-flex align-items-center justify-content-between mb-2">
                            <div class="d-flex gap-2">
                                <div class="eval-section-num">1</div>
                                <span class="fw-bold small">Innovation and Creativity</span>
                            </div>
                            <div class="eval-section-total-badge">{{ $this->section1Total['total'] }}/{{ $this->section1Total['max'] }} pts</div>
                        </div>
                        <div class="progress mb-3" style="height:4px;">
                            <div class="progress-bar bg-success" style="width: {{ ($this->section1Total['total'] / $this->section1Total['max']) * 100 }}%"></div>
                        </div>
                        
                        <div class="p-3 rounded-3 border bg-light mb-2">
                            <div class="fw-semibold small mb-2">Uniqueness of Solution (Competitive advantage)</div>
                            <div class="text-muted mb-2" style="font-size:.72rem;">Is the service or product solving the identified problem or need in a unique way?</div>
                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                <input type="range" class="form-range flex-grow-1" min="0" max="10" step="1" wire:model.live="innovation_uniqueness" {{ $isLocked ? 'disabled' : '' }}>
                                <div class="score-input-box {{ $innovation_uniqueness >= 7 ? 'sib-high' : ($innovation_uniqueness >= 4 ? 'sib-mid' : 'sib-low') }}">
                                    {{ $innovation_uniqueness }}/10
                                </div>
                            </div>
                        </div>
                        
                        <div class="p-3 rounded-3 border bg-light">
                            <div class="fw-semibold small mb-2">Product development stage</div>
                            <div class="text-muted mb-2" style="font-size:.72rem;">At what stage is the enterprise product/service development?</div>
                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                <input type="range" class="form-range flex-grow-1" min="0" max="5" step="1" wire:model.live="innovation_development" {{ $isLocked ? 'disabled' : '' }}>
                                <div class="score-input-box {{ $innovation_development >= 4 ? 'sib-high' : ($innovation_development >= 2 ? 'sib-mid' : 'sib-low') }}">
                                    {{ $innovation_development }}/5
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Commercial Feasibility -->
                    <div class="eval-section mb-4">
                        <div class="eval-section-header d-flex justify-content-between mb-2">
                            <div class="d-flex gap-2">
                                <div class="eval-section-num">2</div>
                                <span class="fw-bold small">Commercial Feasibility</span>
                            </div>
                            <div class="eval-section-total-badge">{{ $this->section2Total['total'] }}/{{ $this->section2Total['max'] }} pts</div>
                        </div>
                        <div class="progress mb-3" style="height:4px;">
                            <div class="progress-bar bg-success" style="width: {{ ($this->section2Total['total'] / $this->section2Total['max']) * 100 }}%"></div>
                        </div>
                        
                        <div class="p-3 border rounded-3 bg-light mb-2">
                            <div class="fw-semibold small mb-2">Vision and Growth Potential</div>
                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                <input type="range" class="form-range flex-grow-1" min="0" max="10" step="1" wire:model.live="commercial_vision" {{ $isLocked ? 'disabled' : '' }}>
                                <div class="score-input-box {{ $commercial_vision >= 7 ? 'sib-high' : ($commercial_vision >= 4 ? 'sib-mid' : 'sib-low') }}">
                                    {{ $commercial_vision }}/10
                                </div>
                            </div>
                        </div>
                        
                        <div class="p-3 border rounded-3 bg-light mb-2">
                            <div class="fw-semibold small mb-2">Potential Market Disruption</div>
                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                <input type="range" class="form-range flex-grow-1" min="0" max="10" step="1" wire:model.live="commercial_disruption" {{ $isLocked ? 'disabled' : '' }}>
                                <div class="score-input-box {{ $commercial_disruption >= 7 ? 'sib-high' : ($commercial_disruption >= 4 ? 'sib-mid' : 'sib-low') }}">
                                    {{ $commercial_disruption }}/10
                                </div>
                            </div>
                        </div>
                        
                        <div class="p-3 border rounded-3 bg-light">
                            <div class="fw-semibold small mb-2">Market Size</div>
                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                <input type="range" class="form-range flex-grow-1" min="0" max="5" step="1" wire:model.live="commercial_market_size" {{ $isLocked ? 'disabled' : '' }}>
                                <div class="score-input-box {{ $commercial_market_size >= 4 ? 'sib-high' : ($commercial_market_size >= 2 ? 'sib-mid' : 'sib-low') }}">
                                    {{ $commercial_market_size }}/5
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Team Capability -->
                    <div class="eval-section mb-4">
                        <div class="eval-section-header d-flex justify-content-between mb-2">
                            <div class="d-flex gap-2">
                                <div class="eval-section-num">3</div>
                                <span class="fw-bold small">Team Capability</span>
                            </div>
                            <div class="eval-section-total-badge">{{ $this->section3Total['total'] }}/{{ $this->section3Total['max'] }} pts</div>
                        </div>
                        <div class="progress mb-3" style="height:4px;">
                            <div class="progress-bar bg-success" style="width: {{ ($this->section3Total['total'] / $this->section3Total['max']) * 100 }}%"></div>
                        </div>
                        
                        <div class="p-3 border rounded-3 bg-light mb-2">
                            <div class="fw-semibold small mb-2">Team / Founder(s) Experience</div>
                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                <input type="range" class="form-range flex-grow-1" min="0" max="6" step="1" wire:model.live="team_experience" {{ $isLocked ? 'disabled' : '' }}>
                                <div class="score-input-box {{ $team_experience >= 5 ? 'sib-high' : ($team_experience >= 3 ? 'sib-mid' : 'sib-low') }}">
                                    {{ $team_experience }}/6
                                </div>
                            </div>
                        </div>
                        
                        <div class="p-3 border rounded-3 bg-light mb-2">
                            <div class="fw-semibold small mb-2">Skill Diversity</div>
                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                <input type="range" class="form-range flex-grow-1" min="0" max="6" step="1" wire:model.live="team_diversity" {{ $isLocked ? 'disabled' : '' }}>
                                <div class="score-input-box {{ $team_diversity >= 5 ? 'sib-high' : ($team_diversity >= 3 ? 'sib-mid' : 'sib-low') }}">
                                    {{ $team_diversity }}/6
                                </div>
                            </div>
                        </div>
                        
                        <div class="p-3 border rounded-3 bg-light mb-2">
                            <div class="fw-semibold small mb-2">Team Size</div>
                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                <input type="range" class="form-range flex-grow-1" min="0" max="10" step="1" wire:model.live="team_size" {{ $isLocked ? 'disabled' : '' }}>
                                <div class="score-input-box {{ $team_size >= 7 ? 'sib-high' : ($team_size >= 4 ? 'sib-mid' : 'sib-low') }}">
                                    {{ $team_size }}/10
                                </div>
                            </div>
                        </div>
                        
                        <div class="p-3 border rounded-3 d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <div class="fw-semibold small">Women Shareholders</div>
                                <div class="text-muted small">Do you have women shareholders? (4 points)</div>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm {{ $team_women_shareholders ? 'btn-success' : 'btn-outline-success' }}" wire:click="$set('team_women_shareholders', true)" {{ $isLocked ? 'disabled' : '' }}>Yes</button>
                                <button type="button" class="btn btn-sm {{ !$team_women_shareholders ? 'btn-danger' : 'btn-outline-danger' }}" wire:click="$set('team_women_shareholders', false)" {{ $isLocked ? 'disabled' : '' }}>No</button>
                            </div>
                        </div>
                        
                        <div class="p-3 border rounded-3 d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-semibold small">Youth Shareholders</div>
                                <div class="text-muted small">Do you have youth shareholders? (4 points)</div>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm {{ $team_youth_shareholders ? 'btn-success' : 'btn-outline-success' }}" wire:click="$set('team_youth_shareholders', true)" {{ $isLocked ? 'disabled' : '' }}>Yes</button>
                                <button type="button" class="btn btn-sm {{ !$team_youth_shareholders ? 'btn-danger' : 'btn-outline-danger' }}" wire:click="$set('team_youth_shareholders', false)" {{ $isLocked ? 'disabled' : '' }}>No</button>
                            </div>
                        </div>
                    </div>

                    <!-- Section 4: Operation Survival -->
                    <div class="eval-section mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <div class="d-flex gap-2">
                                <div class="eval-section-num">4</div>
                                <span class="fw-bold small">Operation Survival</span>
                            </div>
                            <div class="eval-section-total-badge">{{ $this->section4Total['total'] }}/{{ $this->section4Total['max'] }} pts</div>
                        </div>
                        <div class="progress mb-3" style="height:4px;">
                            <div class="progress-bar bg-warning" style="width: {{ ($this->section4Total['total'] / $this->section4Total['max']) * 100 }}%"></div>
                        </div>
                        
                        <div class="p-3 rounded-3 border">
                            <div class="fw-semibold small mb-2">Business Sustainability</div>
                            <div class="text-muted small mb-2">How long has the enterprise been operational?</div>
                            <div class="d-flex flex-column gap-2">
                                <label class="d-flex justify-content-between align-items-center p-2 rounded cursor-pointer {{ $operation_sustainability == 5 ? 'bg-light border' : '' }}">
                                    <span>Less than 12 months operational</span>
                                    <span class="fw-bold text-muted">5 pts</span>
                                    <input type="radio" name="sustainability" value="5" wire:model="operation_sustainability" class="ms-2" {{ $isLocked ? 'disabled' : '' }}>
                                </label>
                                <label class="d-flex justify-content-between align-items-center p-2 rounded cursor-pointer {{ $operation_sustainability == 10 ? 'bg-light border' : '' }}">
                                    <span>12 – 24 months operational</span>
                                    <span class="fw-bold text-muted">10 pts</span>
                                    <input type="radio" name="sustainability" value="10" wire:model="operation_sustainability" class="ms-2" {{ $isLocked ? 'disabled' : '' }}>
                                </label>
                                <label class="d-flex justify-content-between align-items-center p-2 rounded cursor-pointer {{ $operation_sustainability == 15 ? 'bg-light border' : '' }}">
                                    <span>24 – 36 months operational</span>
                                    <span class="fw-bold text-muted">15 pts</span>
                                    <input type="radio" name="sustainability" value="15" wire:model="operation_sustainability" class="ms-2" {{ $isLocked ? 'disabled' : '' }}>
                                </label>
                                <label class="d-flex justify-content-between align-items-center p-2 rounded cursor-pointer {{ $operation_sustainability == 20 ? 'bg-light border' : '' }}">
                                    <span>Over 36 months operational</span>
                                    <span class="fw-bold text-muted">20 pts</span>
                                    <input type="radio" name="sustainability" value="20" wire:model="operation_sustainability" class="ms-2" {{ $isLocked ? 'disabled' : '' }}>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Section 5: Social and Environmental Safeguards -->
                    <div class="eval-section mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <div class="d-flex gap-2">
                                <div class="eval-section-num">5</div>
                                <span class="fw-bold small">Social & Environmental Safeguards</span>
                            </div>
                            <div class="eval-section-total-badge">{{ $this->section5Total['total'] }}/{{ $this->section5Total['max'] }} pts</div>
                        </div>
                        <div class="progress mb-3" style="height:4px;">
                            <div class="progress-bar bg-success" style="width: {{ ($this->section5Total['total'] / $this->section5Total['max']) * 100 }}%"></div>
                        </div>
                        
                        <div class="p-3 border rounded-3 bg-light mb-2">
                            <div class="fw-semibold small mb-2">Social / Environmental Safeguards</div>
                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                <input type="range" class="form-range flex-grow-1" min="0" max="5" step="1" wire:model.live="social_safeguards" {{ $isLocked ? 'disabled' : '' }}>
                                <div class="score-input-box {{ $social_safeguards >= 4 ? 'sib-high' : ($social_safeguards >= 2 ? 'sib-mid' : 'sib-low') }}">
                                    {{ $social_safeguards }}/5
                                </div>
                            </div>
                        </div>
                        
                        <div class="p-3 border rounded-3 bg-light">
                            <div class="fw-semibold small mb-2">Risk Mitigation</div>
                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                <input type="range" class="form-range flex-grow-1" min="0" max="5" step="1" wire:model.live="social_risk_mitigation" {{ $isLocked ? 'disabled' : '' }}>
                                <div class="score-input-box {{ $social_risk_mitigation >= 4 ? 'sib-high' : ($social_risk_mitigation >= 2 ? 'sib-mid' : 'sib-low') }}">
                                    {{ $social_risk_mitigation }}/5
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Grand Total -->
                    <div class="eval-grand-total p-4 mt-3" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 12px;">
                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                            <div>
                                <div class="fw-bold">GRAND TOTAL SCORE</div>
                                <div class="text-muted small">{{ $totalScore }} raw points out of 100</div>
                            </div>
                            <div class="eval-grand-badge" style="background: {{ $totalScore >= 70 ? '#198754' : ($totalScore >= 50 ? '#fd7e14' : '#dc3545') }}; color: white; padding: 8px 20px; border-radius: 40px; font-size: 24px; font-weight: bold;">
                                {{ $totalScore }}/100
                            </div>
                        </div>
                        <div class="progress mb-2" style="height: 10px;">
                            <div class="progress-bar bg-success" style="width: {{ $totalScore }}%"></div>
                        </div>
                        <div class="d-flex justify-content-between small">
                            <span>0</span>
                            <span class="fw-semibold {{ $totalScore >= 70 ? 'text-success' : ($totalScore >= 50 ? 'text-warning' : 'text-danger') }}">
                                {{ $totalScore >= 70 ? 'Strong Candidate' : ($totalScore >= 50 ? 'Potential Candidate' : 'Needs Improvement') }}
                            </span>
                            <span>100</span>
                        </div>
                    </div>

                    <!-- Comments and Evaluator Info -->
                    <div class="mt-4">
                        <label class="form-label fw-medium small">Evaluator Comments</label>
                        <textarea class="form-control small" rows="3" placeholder="Add your evaluation comments here..." wire:model="evaluator_comments" {{ $isLocked ? 'disabled' : '' }}></textarea>
                    </div>
                    @endif
                </div>

                <!-- ==================== APPLICATION INFO TAB ==================== -->
                <div style="{{ $activeTab != 'application' ? 'display: none;' : '' }}">
                    @if($application)
                    <!-- Business Information -->
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3 pb-2 border-bottom">
                            <i class="bi bi-building me-2"></i>Business Information
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6"><div class="info-card p-3 bg-light rounded"><div class="text-muted small mb-1">Company Name</div><div class="fw-semibold">{{ $application->company_name }}</div></div></div>
                            <div class="col-md-6"><div class="info-card p-3 bg-light rounded"><div class="text-muted small mb-1">Registered Company Name</div><div class="fw-semibold">{{ $application->registered_company_name ?? 'N/A' }}</div></div></div>
                            <div class="col-12"><div class="info-card p-3 bg-light rounded"><div class="text-muted small mb-1">Business Description</div><div class="fw-semibold">{{ $application->business_description ?? 'N/A' }}</div></div></div>
                            <div class="col-md-6"><div class="info-card p-3 bg-light rounded"><div class="text-muted small mb-1">Business Model</div><div class="fw-semibold">{{ $application->business_model ?? 'N/A' }}</div></div></div>
                            <div class="col-md-6"><div class="info-card p-3 bg-light rounded"><div class="text-muted small mb-1">Products/Services Offering</div><div class="fw-semibold">{{ $application->offering ?? 'N/A' }}</div></div></div>
                            <div class="col-md-6"><div class="info-card p-3 bg-light rounded"><div class="text-muted small mb-1">Revenue Model</div><div class="fw-semibold">{{ $application->revenue_model ?? 'N/A' }}</div></div></div>
                            <div class="col-md-6"><div class="info-card p-3 bg-light rounded"><div class="text-muted small mb-1">Website</div><div class="fw-semibold">@if($application->website)<a href="{{ $application->website }}" target="_blank">{{ $application->website }}</a>@else N/A @endif</div></div></div>
                            <div class="col-md-6"><div class="info-card p-3 bg-light rounded"><div class="text-muted small mb-1">Company Type</div><div class="fw-semibold">{{ ucfirst(str_replace('_', ' ', $application->company_type ?? 'N/A')) }}</div></div></div>
                            <div class="col-md-6"><div class="info-card p-3 bg-light rounded"><div class="text-muted small mb-1">Industry/Sector</div><div class="fw-semibold">{{ $application->industry ?? $application->sector ?? 'N/A' }}</div></div></div>
                        </div>
                    </div>

                    <!-- Location & Operations -->
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3 pb-2 border-bottom"><i class="bi bi-geo-alt me-2"></i>Location & Operations</h6>
                        <div class="row g-3">
                            <div class="col-md-6"><div class="info-card p-3 bg-light rounded"><div class="text-muted small mb-1">Country</div><div class="fw-semibold">{{ $application->country ?? 'Lesotho' }}</div></div></div>
                            <div class="col-md-6"><div class="info-card p-3 bg-light rounded"><div class="text-muted small mb-1">District</div><div class="fw-semibold">{{ $application->district ?? 'N/A' }}</div></div></div>
                            <div class="col-md-6"><div class="info-card p-3 bg-light rounded"><div class="text-muted small mb-1">Year of Establishment</div><div class="fw-semibold">{{ $application->year_of_establishment ?? 'N/A' }}</div></div></div>
                            <div class="col-md-6"><div class="info-card p-3 bg-light rounded"><div class="text-muted small mb-1">Years in Operation</div><div class="fw-semibold">{{ $application->year_of_establishment ? date('Y') - $application->year_of_establishment : 'N/A' }} years</div></div></div>
                            <div class="col-md-6"><div class="info-card p-3 bg-light rounded"><div class="text-muted small mb-1">Business Stage</div><div class="fw-semibold">{{ ucfirst(str_replace('_', ' ', $application->company_stage ?? 'N/A')) }}</div></div></div>
                            <div class="col-md-6"><div class="info-card p-3 bg-light rounded"><div class="text-muted small mb-1">Company Size</div><div class="fw-semibold">{{ $application->company_size ?? 'N/A' }} employees</div></div></div>
                        </div>
                    </div>

                    <!-- Financial & Employment -->
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3 pb-2 border-bottom"><i class="bi bi-graph-up me-2"></i>Financial & Employment</h6>
                        <div class="row g-3">
                            <div class="col-md-6"><div class="info-card p-3 bg-light rounded"><div class="text-muted small mb-1">Number of Shareholders</div><div class="fw-semibold">{{ $application->number_of_shareholders ?? '0' }}</div></div></div>
                            <div class="col-md-6"><div class="info-card p-3 bg-light rounded"><div class="text-muted small mb-1">Number of Women Shareholders</div><div class="fw-semibold">{{ $application->number_women_shareholders ?? '0' }}</div></div></div>
                            <div class="col-md-6"><div class="info-card p-3 bg-light rounded"><div class="text-muted small mb-1">Number of Youth Shareholders</div><div class="fw-semibold">{{ $application->number_youth_shareholders ?? '0' }}</div></div></div>
                            <div class="col-md-6"><div class="info-card p-3 bg-light rounded"><div class="text-muted small mb-1">Number of Customers</div><div class="fw-semibold">{{ number_format($application->number_of_customers ?? 0) }}</div></div></div>
                            <div class="col-md-6"><div class="info-card p-3 bg-light rounded"><div class="text-muted small mb-1">Average Monthly Sales</div><div class="fw-semibold">M {{ number_format($application->average_monthly_sales ?? 0, 2) }}</div></div></div>
                            <div class="col-md-6"><div class="info-card p-3 bg-light rounded"><div class="text-muted small mb-1">Jobs to Create (12 months)</div><div class="fw-semibold">{{ $application->jobs_to_create_12_months ?? '0' }}</div></div></div>
                        </div>
                    </div>

                    <!-- Applicant Information -->
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3 pb-2 border-bottom"><i class="bi bi-person-badge me-2"></i>Applicant Information</h6>
                        <div class="row g-3">
                            <div class="col-md-6"><div class="info-card p-3 bg-light rounded"><div class="text-muted small mb-1">Full Name</div><div class="fw-semibold">{{ $application->applicant_name ?? 'N/A' }}</div></div></div>
                            <div class="col-md-6"><div class="info-card p-3 bg-light rounded"><div class="text-muted small mb-1">Email Address</div><div class="fw-semibold">{{ $application->applicant_email ?? 'N/A' }}</div></div></div>
                            <div class="col-md-6"><div class="info-card p-3 bg-light rounded"><div class="text-muted small mb-1">Title/Position</div><div class="fw-semibold">{{ $application->applicant_title ?? 'N/A' }}</div></div></div>
                            <div class="col-md-6"><div class="info-card p-3 bg-light rounded"><div class="text-muted small mb-1">Gender</div><div class="fw-semibold">{{ ucfirst($application->applicant_gender ?? 'N/A') }}</div></div></div>
                            <div class="col-md-6"><div class="info-card p-3 bg-light rounded"><div class="text-muted small mb-1">Nationality</div><div class="fw-semibold">{{ $application->applicant_nationality ?? 'N/A' }}</div></div></div>
                            <div class="col-md-6"><div class="info-card p-3 bg-light rounded"><div class="text-muted small mb-1">Contact Number</div><div class="fw-semibold">{{ $application->applicant_contact_number ?? 'N/A' }}</div></div></div>
                            <div class="col-12"><div class="info-card p-3 bg-light rounded"><div class="text-muted small mb-1">About Applicant</div><div class="fw-semibold">{{ $application->applicant_about ?? 'N/A' }}</div></div></div>
                        </div>
                    </div>

                    <!-- PDO Target Group Flags -->
                    @if($application->applicant_gender == 'female' || ($application->year_of_establishment && (date('Y') - $application->year_of_establishment) <= 5) || ($application->number_women_shareholders ?? 0) > 0 || ($application->number_youth_shareholders ?? 0) > 0)
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3 pb-2 border-bottom"><i class="bi bi-flag me-2"></i>PDO Target Group Flags</h6>
                        <div class="d-flex flex-wrap gap-2">
                            @if($application->applicant_gender == 'female')<span class="badge px-3 py-2" style="background-color: #ec4899; color: white;"><i class="bi bi-gender-female me-1"></i>Women-owned</span>@endif
                            @if($application->year_of_establishment && (date('Y') - $application->year_of_establishment) <= 5)<span class="badge px-3 py-2" style="background-color: #0d6efd; color: white;"><i class="bi bi-rocket me-1"></i>Startup (≤5 years)</span>@endif
                            @if(($application->number_women_shareholders ?? 0) > 0)<span class="badge px-3 py-2" style="background-color: #d63384; color: white;"><i class="bi bi-people-fill me-1"></i>Women Shareholders ({{ $application->number_women_shareholders }})</span>@endif
                            @if(($application->number_youth_shareholders ?? 0) > 0)<span class="badge px-3 py-2" style="background-color: #198754; color: white;"><i class="bi bi-person-heart me-1"></i>Youth Shareholders ({{ $application->number_youth_shareholders }})</span>@endif
                        </div>
                    </div>
                    @endif
                    @else
                    <div class="text-center py-5 text-muted">Loading application data...</div>
                    @endif
                </div>

                <!-- ==================== EVALUATORS TAB ==================== -->
                <div style="{{ $activeTab != 'evaluators' ? 'display: none;' : '' }}">
                    @php
                        $assignedEvaluators = \App\Models\AssignedEvaluator::where('call_id', $application->call_id ?? 0)
                            ->with('evaluator')
                            ->get();
                        $totalEvaluators = $assignedEvaluators->count();
                        $completedEvaluators = 0;
                        foreach ($assignedEvaluators as $eval) {
                            $hasScored = \App\Models\EvaluationScore::where('call_id', $application->call_id ?? 0)
                                ->where('evaluator_id', $eval->user_id)
                                ->where('status', 'submitted')
                                ->exists();
                            if ($hasScored) $completedEvaluators++;
                        }
                    @endphp
                    
                    <div class="alert alert-info small">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>{{ $completedEvaluators }} of {{ $totalEvaluators }}</strong> evaluators have completed scoring this application.
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover align-middle small">
                            <thead class="table-light">
                                <tr><th>Evaluator</th><th>Role</th><th class="text-center">Status</th><th class="text-center">Score</th><th class="text-center">Date</th></tr>
                            </thead>
                            <tbody>
                                @forelse($assignedEvaluators as $evaluator)
                                @php
                                    $score = \App\Models\EvaluationScore::where('call_id', $application->call_id ?? 0)
                                        ->where('evaluator_id', $evaluator->user_id)
                                        ->where('application_id', $application->id)
                                        ->first();
                                    $hasScored = $score && $score->status == 'submitted';
                                    $isCurrentUser = $evaluator->user_id == Auth::id();
                                @endphp
                                <tr class="{{ $isCurrentUser ? 'table-primary' : '' }}">
                                    <td><div class="d-flex align-items-center gap-2"><div class="ev-lg-avatar bg-primary text-white">{{ substr($evaluator->evaluator->username ?? 'E', 0, 2) }}</div><div><div class="fw-medium">{{ $evaluator->evaluator->username ?? 'Unknown' }}</div><div class="text-muted small">{{ $evaluator->evaluator->email ?? '' }}</div></div></div>@if($isCurrentUser)<span class="badge bg-primary mt-1">You</span>@endif</td>
                                    <td>{{ $evaluator->evaluator->roles->first()->name ?? 'Evaluator' }}</td>
                                    <td class="text-center">@if($hasScored)<span class="badge bg-success">Completed</span>@elseif($score && $score->status == 'draft')<span class="badge bg-warning">In Progress</span>@else<span class="badge bg-secondary">Pending</span>@endif</td>
                                    <td class="text-center">@if($hasScored)<span class="fw-bold text-success">{{ $score->total_score }}/100</span>@else<span class="text-muted">—</span>@endif</td>
                                    <td class="text-center">@if($score && $score->submitted_at){{ $score->submitted_at->format('d M Y') }}@else<span class="text-muted">—</span>@endif</td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center py-4 text-muted"><i class="bi bi-people fs-4 d-block mb-2 opacity-50"></i>No evaluators assigned to this call yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-5 pb-4 pt-3 d-flex justify-content-end gap-3 border-top">
                <button class="btn btn-light px-4" @click="open = false; $wire.closeModal()">Cancel</button>
                @if($isAssigned && !$isLocked)
                <button class="btn btn-primary px-4" 
                        wire:click="saveScore" 
                        wire:loading.attr="disabled"
                        wire:confirm="⚠️ IMPORTANT DECISION: Once you submit this score, you will NOT be able to make any further changes to this evaluation.\n\n• The score will be final and locked\n• No further edits will be allowed\n• This action will be recorded in the system\n\nAre you absolutely sure you want to submit this score?">
                    <span wire:loading.remove><i class="bi bi-save me-1"></i>Submit Score</span>
                    <span wire:loading><span class="spinner-border spinner-border-sm me-1"></span>Saving...</span>
                </button>
                @elseif($isLocked)
                <button class="btn btn-secondary px-4" disabled><i class="bi bi-lock me-1"></i>Score Locked</button>
                @endif
            </div>

        </div>
    </div>
</div>
