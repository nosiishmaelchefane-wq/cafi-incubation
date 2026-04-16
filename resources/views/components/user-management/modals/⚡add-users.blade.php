<?php

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Rule;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

new class extends Component {
    // Form properties
    public $userId = null;
    
    #[Rule('required|min:3|max:255')]
    public $username = '';

    #[Rule('required|email')]
    public $email = '';

    #[Rule('nullable|string|max:20')]
    public $phone = '';

    #[Rule('required|exists:roles,name')]
    public $role = '';

    #[Rule('nullable|min:8')]
    public $password = '';

    #[Rule('nullable|same:password')]
    public $password_confirmation = '';

    #[Rule('boolean')]
    public $is_active = true;

    public $showModal = false;
    public $isEditing = false;
    public $modalTitle = 'Add New User';
    public $submitButtonText = 'Create User';

    /**
     * Open the add user modal
     */
    #[On('openAddUserModal')]
    public function openAddModal()
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->modalTitle = 'Add New User';
        $this->submitButtonText = 'Create User';
        $this->showModal = true;
        $this->dispatch('open-modal');
    }

    /**
     * Open the edit modal for CAFI Admin users
     * (Super Administrator, Evaluation Officer, Procurement Officer, CAFI Admin)
     */
    #[On('openCafiAdminEditModal')]
    public function openEditModal($userId)
    {
        $this->resetForm();
        $this->isEditing = true;
        $this->userId = $userId;
        $this->modalTitle = 'Edit CAFI Admin User';
        $this->submitButtonText = 'Update User';
        
        // Load user data
        $user = User::with('roles')->findOrFail($userId);
        
        $this->username = $user->username;
        $this->email = $user->email;
        $this->phone = $user->phone;
        $this->is_active = $user->is_active;
        
        // Get the first role (assuming single role per user)
        if ($user->roles->isNotEmpty()) {
            $this->role = $user->roles->first()->name;
        }
        
        $this->showModal = true;
        $this->dispatch('open-modal');
    }

    /**
     * Close the modal
     */
    #[On('closeUserModal')]
    public function closeUserModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    /**
     * Reset form fields
     */
    public function resetForm()
    {
        $this->reset([
            'userId', 'username', 'email', 'phone', 
            'role', 'password', 'password_confirmation', 'is_active',
            'isEditing'
        ]);
        $this->resetErrorBag();
    }

    /**
     * Save or update the user
     */
    public function saveUser()
    {
        // Modify validation rules based on action
        if ($this->isEditing) {
            // For editing
            $this->validate([
                'username' => 'required|min:3|max:255',
                'email' => 'required|email|unique:users,email,' . $this->userId,
                'phone' => 'nullable|string|max:20',
                'role' => 'required|exists:roles,name',
                'password' => 'nullable|min:8',
                'password_confirmation' => 'nullable|same:password',
                'is_active' => 'boolean',
            ]);
        } else {
            // For creating
            $this->validate([
                'username' => 'required|min:3|max:255',
                'email' => 'required|email|unique:users,email',
                'phone' => 'nullable|string|max:20',
                'role' => 'required|exists:roles,name',
                'password' => 'required|min:8',
                'password_confirmation' => 'required|same:password',
                'is_active' => 'boolean',
            ]);
        }

        try {
            if ($this->isEditing) {
                // Update existing user
                $user = User::findOrFail($this->userId);
                
                $userData = [
                    'username' => $this->username,
                    'email' => $this->email,
                    'phone' => $this->phone,
                    'is_active' => $this->is_active,
                ];
                
                // Only update password if provided
                if (!empty($this->password)) {
                    $userData['password'] = Hash::make($this->password);
                }
                
                $user->update($userData);
                
                // Sync role (remove old roles and assign new one)
                $user->syncRoles([$this->role]);
                
                $this->dispatch('notify', type: 'success', message: 'User updated successfully!');
                
            } else {
                // Create new user
                $user = User::create([
                    'name' => $this->username,
                    'email' => $this->email,
                    'phone' => $this->phone,
                    'username' => $this->username,
                    'password' => Hash::make($this->password),
                    'is_active' => $this->is_active,
                    'email_verified_at' => now(),
                ]);

                // Assign role
                $user->assignRole($this->role);
                
                $this->dispatch('notify', type: 'success', message: 'User created successfully!');
            }

            // Dispatch common events
            $this->dispatch('userUpdated');
            $this->dispatch('userCreated');

            // Close modal
            $this->closeUserModal();

        } catch (\Exception $e) {
            // Handle error
            $this->dispatch('notify', type: 'error', message: 'Failed to save user: ' . $e->getMessage());
        }
    }

    /**
     * Get all available roles
     */
    public function with(): array
    {
        return [
            'roles' => Role::all(),
        ];
    }
};
?>

<div>
    <!-- Add/Edit User Modal for CAFI Admin Users -->
    @if($showModal)
    <div class="modal fade show d-block" 
         id="userModal" 
         tabindex="-1" 
         aria-labelledby="userModalLabel" 
         aria-hidden="true"
         style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold" id="userModalLabel">
                        <i class="bi {{ $isEditing ? 'bi-pencil-fill' : 'bi-person-plus-fill' }} text-primary me-2"></i>
                        {{ $modalTitle }}
                    </h5>
                    <button type="button" class="btn-close" 
                            wire:click="$dispatch('closeUserModal')" 
                            aria-label="Close"></button>
                </div>
                
                <div class="modal-body p-4">
                    <form wire:submit.prevent="saveUser">
                        <!-- Name Field -->
                        <div class="mb-3">
                            <label for="username" class="form-label fw-semibold small text-uppercase text-muted">
                                <i class="bi bi-person me-1"></i>Full Name
                            </label>
                            <input type="text" 
                                   class="form-control @error('username') is-invalid @enderror" 
                                   id="username" 
                                   wire:model="username"
                                   placeholder="Enter full name">
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text text-muted small">First and last name</div>
                        </div>

                        <!-- Email Field -->
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold small text-uppercase text-muted">
                                <i class="bi bi-envelope me-1"></i>Email Address
                            </label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   wire:model="email"
                                   placeholder="user@example.com">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Phone Field (Optional) -->
                        <div class="mb-3">
                            <label for="phone" class="form-label fw-semibold small text-uppercase text-muted">
                                <i class="bi bi-telephone me-1"></i>Phone Number <span class="text-muted">(Optional)</span>
                            </label>
                            <input type="tel" 
                                   class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" 
                                   wire:model="phone"
                                   placeholder="+27 12 345 6789">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Role Selection -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold small text-uppercase text-muted">
                                <i class="bi bi-shield me-1"></i>Assign Role
                            </label>
                            <select class="form-select @error('role') is-invalid @enderror" 
                                    wire:model="role">
                                <option value="" selected disabled>Select a role...</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text text-muted small">Role determines user permissions</div>
                        </div>

                        <!-- Password Fields -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label fw-semibold small text-uppercase text-muted">
                                    <i class="bi bi-lock me-1"></i>{{ $isEditing ? 'New Password' : 'Password' }}
                                </label>
                                <input type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       id="password" 
                                       wire:model="password"
                                       placeholder="{{ $isEditing ? 'Leave blank to keep current' : '••••••••' }}">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if($isEditing)
                                    <div class="form-text text-muted small">Leave blank to keep current password</div>
                                @endif
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label fw-semibold small text-uppercase text-muted">
                                    <i class="bi bi-check-circle me-1"></i>Confirm Password
                                </label>
                                <input type="password" 
                                       class="form-control @error('password_confirmation') is-invalid @enderror" 
                                       id="password_confirmation" 
                                       wire:model="password_confirmation"
                                       placeholder="••••••••">
                                @error('password_confirmation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Account Status -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-uppercase text-muted">
                                <i class="bi bi-toggle-on me-1"></i>Account Status
                            </label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" 
                                           type="radio" 
                                           name="accountStatus" 
                                           id="statusActive" 
                                           value="1" 
                                           wire:model="is_active">
                                    <label class="form-check-label small" for="statusActive">Active</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" 
                                           type="radio" 
                                           name="accountStatus" 
                                           id="statusInactive" 
                                           value="0" 
                                           wire:model="is_active">
                                    <label class="form-check-label small" for="statusInactive">Inactive</label>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer bg-light px-0 pb-0">
                            <button type="button" class="btn btn-outline-secondary" 
                                    wire:click="$dispatch('closeUserModal')">
                                <i class="bi bi-x-lg me-1"></i>
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-primary" 
                                    wire:loading.attr="disabled"
                                    wire:target="saveUser">
                                <span wire:loading.remove wire:target="saveUser">
                                    <i class="bi {{ $isEditing ? 'bi-check-lg' : 'bi-person-plus-fill' }} me-1"></i>
                                    {{ $submitButtonText }}
                                </span>
                                <span wire:loading wire:target="saveUser">
                                    <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                    {{ $isEditing ? 'Updating...' : 'Creating...' }}
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>